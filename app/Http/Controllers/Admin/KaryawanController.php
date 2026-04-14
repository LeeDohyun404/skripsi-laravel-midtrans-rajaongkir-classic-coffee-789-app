<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Config;

class KaryawanController extends Controller
{
    // Tampilkan daftar karyawan dengan filter & pagination
    public function index(Request $request)
    {
        $query = Karyawan::query();

        // Filter search nama / email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }

        // Filter peran
        if ($request->filled('peran')) {
            $query->where('peran', $request->peran);
        }

        // Filter status aktivasi — gunakan kolom is_aktif yang ada di tabel
        if ($request->filled('status')) {
            if ($request->status === 'aktif') {
                $query->where('is_aktif', true)->whereNotNull('password');
            } elseif ($request->status === 'belum_aktif') {
                $query->where(function ($q) {
                    $q->where('is_aktif', false)->orWhereNull('password');
                });
            }
        }

        $karyawan = $query->latest()->paginate(10);

        return view('admin.karyawan.index', compact('karyawan'));
    }

    // Form tambah karyawan
    public function create()
    {
        return view('admin.karyawan.tambah');
    }

    // Simpan karyawan baru
    public function store(Request $request)
    {
        $request->validate([
            'nama'  => 'required|string|max:100',
            'email' => 'required|email|unique:karyawan,email,NULL,id,deleted_at,NULL', // abaikan data soft delete
            'peran' => 'required|in:admin,operator,kasir',
        ]);

        $karyawan = Karyawan::create([
            'nama'     => $request->nama,
            'username' => Str::slug($request->nama) . Str::lower(Str::random(4)),
            'email'    => $request->email,
            'password' => null,
            'peran'    => $request->peran,
            'is_aktif' => false,
        ]);

        $hasilPengiriman = $this->kirimUndanganAktivasi($karyawan);

        if ($hasilPengiriman['success']) {
            return redirect()->route('admin.karyawan.index')
                             ->with('success', 'Karyawan berhasil ditambahkan dan undangan aktivasi berhasil dikirim!');
        }

        return redirect()->route('admin.karyawan.index')
                         ->with('warning', 'Karyawan berhasil ditambahkan, tetapi email aktivasi gagal dikirim.')
                         ->with('manual_activation_url', $hasilPengiriman['url']);
    }

    // Form edit karyawan
    public function edit($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        return view('admin.karyawan.edit', compact('karyawan'));
    }

    // Update data karyawan
    public function update(Request $request, $id)
    {
        $karyawan = Karyawan::findOrFail($id);

        $request->validate([
            'nama'  => 'required|string|max:100',
            'email' => 'required|email|unique:karyawan,email,' . $id . ',id,deleted_at,NULL', // abaikan data soft delete
            'peran' => 'required|in:admin,operator,kasir',
        ]);

        $karyawan->update([
            'nama'  => $request->nama,
            'email' => $request->email,
            'peran' => $request->peran,
        ]);

        return redirect()->route('admin.karyawan.index')
                         ->with('success', 'Data karyawan berhasil diperbarui!');
    }

    // Hapus karyawan
    public function destroy($id)
    {
        $karyawan = Karyawan::withTrashed()->findOrFail($id);

        DB::transaction(function () use ($karyawan) {
            $karyawan->forceDelete();
        });

        return redirect()->route('admin.karyawan.index')
                         ->with('success', 'Karyawan berhasil dihapus permanen dari database!');
    }

    // Kirim ulang undangan aktivasi
    public function resendUndangan($id)
    {
        $karyawan = Karyawan::findOrFail($id);

        if ($karyawan->is_aktif) {
            return redirect()->route('admin.karyawan.index')
                             ->with('error', 'Akun karyawan ini sudah aktif.');
        }

        $hasilPengiriman = $this->kirimUndanganAktivasi($karyawan);

        if ($hasilPengiriman['success']) {
            return redirect()->route('admin.karyawan.index')
                             ->with('success', 'Undangan berhasil dikirim ulang!');
        }

        return redirect()->route('admin.karyawan.index')
                         ->with('warning', 'Gagal mengirim email aktivasi otomatis.')
                         ->with('manual_activation_url', $hasilPengiriman['url']);
    }

    private function kirimUndanganAktivasi(Karyawan $karyawan): array
    {
        $urlAktivasi = URL::temporarySignedRoute(
            'aktivasi.form',
            now()->addHours(24),
            ['token' => $karyawan->id]
        );

        $validator = Validator::make(
            ['email' => $karyawan->email],
            ['email' => ['required', 'email']]
        );

        if ($validator->fails()) {
            Log::warning('Email aktivasi karyawan dibatalkan karena email tidak valid.', [
                'karyawan_id' => $karyawan->id,
                'email' => $karyawan->email,
                'errors' => $validator->errors()->all(),
                'url_aktivasi' => $urlAktivasi,
            ]);

            return [
                'success' => false,
                'url' => $urlAktivasi,
            ];
        }

        try {
            $mailer = Config::get('mail.default', 'smtp');
            $emailTujuan = trim($karyawan->email);

            Mail::mailer($mailer)->send(
                'emails.undangan-karyawan',
                [
                    'karyawan' => $karyawan,
                    'urlAktivasi' => $urlAktivasi,
                ],
                function ($message) use ($karyawan, $emailTujuan) {
                    $message->to($emailTujuan, $karyawan->nama)
                            ->subject('Undangan Aktivasi Akun Karyawan');
                }
            );

            $failures = method_exists(Mail::getFacadeRoot(), 'failures') ? Mail::failures() : [];

            if (!empty($failures)) {
                Log::warning('Mailer tidak melempar exception, tetapi melaporkan kegagalan pengiriman email aktivasi.', [
                    'karyawan_id' => $karyawan->id,
                    'email' => $emailTujuan,
                    'mailer' => $mailer,
                    'failures' => $failures,
                    'url_aktivasi' => $urlAktivasi,
                ]);

                return [
                    'success' => false,
                    'url' => $urlAktivasi,
                ];
            }

            Log::info('Email aktivasi karyawan berhasil dikirim.', [
                'karyawan_id' => $karyawan->id,
                'email' => $emailTujuan,
                'mailer' => $mailer,
                'mail_host' => config('mail.mailers.smtp.host'),
                'mail_port' => config('mail.mailers.smtp.port'),
                'mail_encryption' => config('mail.mailers.smtp.encryption'),
                'from_address' => config('mail.from.address'),
                'url_aktivasi' => $urlAktivasi,
            ]);

            return [
                'success' => true,
                'url' => $urlAktivasi,
            ];
        } catch (\Throwable $e) {
            Log::error('Gagal mengirim email aktivasi karyawan', [
                'karyawan_id' => $karyawan->id,
                'email' => $karyawan->email,
                'mailer' => config('mail.default'),
                'mail_host' => config('mail.mailers.smtp.host'),
                'url_aktivasi' => $urlAktivasi,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'url' => $urlAktivasi,
            ];
        }
    }
}
