@extends('layouts.karyawan')

@section('title', 'Tambah Karyawan')
@section('page-title', 'Tambah Karyawan')

@section('sidebar-nav')
    <span class="nav-section-label">Menu Utama</span>
    <a href="{{ route('admin.dashboard') }}" class="nav-item">
        <span class="nav-icon">🏠</span> Dashboard
    </a>
    <span class="nav-section-label">Manajemen</span>
    <a href="{{ route('admin.karyawan.index') }}" class="nav-item active">
        <span class="nav-icon">👥</span> Karyawan
    </a>
    <a href="{{ route('admin.pelanggan') }}" class="nav-item">
        <span class="nav-icon">🧑‍🤝‍🧑</span> Pelanggan
    </a>
    <a href="{{ route('admin.stok') }}" class="nav-item">
        <span class="nav-icon">📦</span> Stok
    </a>
    <a href="{{ route('admin.history') }}" class="nav-item">
        <span class="nav-icon">📋</span> History Transaksi
    </a>
@endsection

@section('content')

{{-- Info banner --}}
<div style="background:#fff8ee;border:1px solid #e8d5a3;border-left:4px solid #c8a96e;border-radius:10px;padding:16px 20px;margin-bottom:24px;display:flex;gap:14px;align-items:flex-start;">
    <span style="font-size:20px;">📧</span>
    <div>
        <div style="font-size:13px;font-weight:600;color:#3d2410;margin-bottom:4px;">Sistem Undangan via Email</div>
        <div style="font-size:13px;color:#7a5c3a;line-height:1.7;">
            Setelah karyawan ditambahkan, sistem akan otomatis mengirim email undangan ke alamat yang Anda isi.
            Karyawan kemudian membuat passwordnya sendiri lewat link aktivasi di email tersebut.
            <strong>Anda tidak perlu membuat password untuk karyawan.</strong>
        </div>
    </div>
</div>

<div class="card" style="max-width:640px;">
    <div class="card-header">
        <h2>Data Karyawan Baru</h2>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.karyawan.store') }}">
            @csrf

            <div class="form-grid">

                {{-- Nama --}}
                <div class="form-group full">
                    <label for="nama">Nama Lengkap <span style="color:#c0392b;">*</span></label>
                    <input
                        type="text"
                        id="nama"
                        name="nama"
                        value="{{ old('nama') }}"
                        placeholder="Masukkan nama lengkap"
                        class="{{ $errors->has('nama') ? 'is-error' : '' }}"
                        required
                    >
                    @error('nama')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="form-group full">
                    <label for="email">Email Karyawan <span style="color:#c0392b;">*</span></label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="nama@email.com"
                        class="{{ $errors->has('email') ? 'is-error' : '' }}"
                        required
                    >
                    <span style="font-size:12px;color:var(--text-muted);">
                        Link aktivasi akan dikirim ke email ini.
                    </span>
                    @error('email')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- No HP --}}
                <div class="form-group">
                    <label for="no_hp">No. HP</label>
                    <input
                        type="tel"
                        id="no_hp"
                        name="no_hp"
                        value="{{ old('no_hp') }}"
                        placeholder="08xx-xxxx-xxxx"
                    >
                    @error('no_hp')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Peran --}}
                <div class="form-group">
                    <label for="peran">Peran <span style="color:#c0392b;">*</span></label>
                    <select id="peran" name="peran" required>
                        <option value="" disabled {{ !old('peran') ? 'selected' : '' }}>Pilih peran...</option>
                        <option value="admin"    {{ old('peran') === 'admin'    ? 'selected' : '' }}>Admin</option>
                        <option value="operator" {{ old('peran') === 'operator' ? 'selected' : '' }}>Operator</option>
                        <option value="kasir"    {{ old('peran') === 'kasir'    ? 'selected' : '' }}>Kasir</option>
                    </select>
                    @error('peran')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Alamat --}}
                <div class="form-group full">
                    <label for="alamat">Alamat</label>
                    <textarea
                        id="alamat"
                        name="alamat"
                        rows="3"
                        placeholder="Alamat tempat tinggal (opsional)"
                        style="resize:vertical;"
                    >{{ old('alamat') }}</textarea>
                    @error('alamat')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

            </div>

            {{-- Peran info card --}}
            <div id="peranInfo" style="display:none;background:var(--cream-dark);border-radius:10px;padding:14px 18px;margin-top:20px;font-size:13px;color:var(--text-muted);line-height:1.7;">
                <span id="peranInfoText"></span>
            </div>

            {{-- Actions --}}
            <div style="display:flex;gap:12px;margin-top:28px;padding-top:20px;border-top:1px solid var(--cream-dark);">
                <button type="submit" class="btn btn-primary">
                    📧 Simpan & Kirim Undangan
                </button>
                <a href="{{ route('admin.karyawan.index') }}" class="btn btn-outline">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const peranDesc = {
        admin:    '👑 Admin dapat mengakses semua fitur: manajemen karyawan, pelanggan, stok, dan history transaksi.',
        operator: '📦 Operator dapat mengelola stok barang dan inventori.',
        kasir:    '🧾 Kasir dapat memproses transaksi penjualan.',
    };

    document.getElementById('peran').addEventListener('change', function () {
        const info = document.getElementById('peranInfo');
        const text = document.getElementById('peranInfoText');
        if (this.value && peranDesc[this.value]) {
            text.textContent = peranDesc[this.value];
            info.style.display = 'block';
        } else {
            info.style.display = 'none';
        }
    });

    // Trigger on load if old value exists
    const peranEl = document.getElementById('peran');
    if (peranEl.value) peranEl.dispatchEvent(new Event('change'));
</script>
@endpush
