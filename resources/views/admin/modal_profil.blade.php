{{--
    MODAL PROFIL UNIVERSAL — Untuk Admin, Kasir, Operator
    
    Tempat: resources/views/admin/partials/_modal_profil.blade.php
    
    Cara pakai:
    Di dashboard masing-masing, SEBELUM @endsection tambahkan:
    @include('admin.partials._modal_profil')
--}}

{{-- ===== SCRIPT BUKA/TUTUP MODAL ===== --}}
<script>
function bukaModalProfil(tab = 'nama') {
    const modal = document.getElementById('modal-profil');
    if (modal) {
        modal.classList.remove('hidden');
        switchTabProfil(tab || '{{ session("modal_tab", "nama") }}');
    }
}

function tutupModalProfil() {
    const modal = document.getElementById('modal-profil');
    if (modal) {
        modal.classList.add('hidden');
    }
}

function switchTabProfil(tab) {
    const formNama = document.getElementById('form-nama');
    const formPassword = document.getElementById('form-password');
    const tabNama = document.getElementById('tab-nama');
    const tabPassword = document.getElementById('tab-password');

    if (tab === 'nama') {
        formNama.classList.remove('hidden');
        formPassword.classList.add('hidden');
        tabNama.classList.add('tab-aktif');
        tabNama.classList.remove('tab-nonaktif');
        tabPassword.classList.remove('tab-aktif');
        tabPassword.classList.add('tab-nonaktif');
    } else {
        formNama.classList.add('hidden');
        formPassword.classList.remove('hidden');
        tabNama.classList.remove('tab-aktif');
        tabNama.classList.add('tab-nonaktif');
        tabPassword.classList.add('tab-aktif');
        tabPassword.classList.remove('tab-nonaktif');
    }
}

// ✅ Buka modal otomatis jika ada error atau success
@if(session('profil_sukses') || $errors->any())
    document.addEventListener('DOMContentLoaded', function () {
        bukaModalProfil('{{ session("modal_tab", "nama") }}');
    });
@endif
</script>

{{-- ===== STYLES ===== --}}
<style>
    .tab-aktif {
        background: #fff !important;
        color: #111 !important;
        border: 1px solid #e5e7eb !important;
    }
    .tab-nonaktif {
        background: transparent !important;
        color: #6b7280 !important;
        border: 1px solid transparent !important;
    }
    .hidden { 
        display: none !important; 
    }
    .field-error { 
        font-size: 11px; 
        color: #dc2626; 
        margin-top: 3px; 
    }
</style>

{{-- ===== MODAL OVERLAY ===== --}}
<div id="modal-profil" class="hidden" style="position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:200;display:flex;align-items:center;justify-content:center;" onclick="if(event.target===this)tutupModalProfil()">
    <div style="background:#fff;border-radius:14px;width:420px;max-width:95vw;overflow:hidden;border:1px solid #e5e7eb;box-shadow:0 20px 25px -5px rgba(0,0,0,.1);">

        {{-- ===== HEADER ===== --}}
        <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid #f3f4f6;">
            <span style="font-size:14px;font-weight:600;color:#111;">⚙️ Pengaturan Profil</span>
            <button onclick="tutupModalProfil()" style="background:none;border:none;cursor:pointer;font-size:20px;line-height:1;color:#9ca3af;padding:0;">×</button>
        </div>

        {{-- ===== INFO PROFIL ===== --}}
        <div style="margin:16px 20px;display:flex;align-items:center;gap:12px;background:#f9fafb;border-radius:10px;padding:12px 14px;border:1px solid #e5e7eb;">
            <div style="width:44px;height:44px;border-radius:50%;background:#dbeafe;display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:600;color:#1d4ed8;flex-shrink:0;">
                {{ strtoupper(substr(auth('karyawan')->user()->nama, 0, 2)) }}
            </div>
            <div style="flex:1;">
                <div style="font-size:14px;font-weight:600;color:#111;">{{ auth('karyawan')->user()->nama }}</div>
                <div style="font-size:12px;color:#6b7280;text-transform:capitalize;">{{ auth('karyawan')->user()->peran }}</div>
            </div>
        </div>

        {{-- ===== TABS ===== --}}
        <div style="display:flex;gap:4px;margin:0 20px 16px;background:#f3f4f6;border-radius:8px;padding:3px;">
            <button id="tab-nama" onclick="switchTabProfil('nama')" class="tab-aktif" 
                style="flex:1;padding:8px 12px;font-size:12px;font-weight:600;border:none;cursor:pointer;border-radius:6px;transition:all .2s;">
                Ganti Nama
            </button>
            <button id="tab-password" onclick="switchTabProfil('password')" class="tab-nonaktif"
                style="flex:1;padding:8px 12px;font-size:12px;font-weight:600;border:none;cursor:pointer;border-radius:6px;transition:all .2s;">
                Ubah Password
            </button>
        </div>

        {{-- ===== ALERT SUKSES ===== --}}
        @if(session('profil_sukses'))
            <div style="margin:0 20px 12px;padding:10px 14px;background:#f0fdf4;border:1px solid #86efac;border-radius:8px;font-size:12px;color:#166534;font-weight:500;">
                ✓ {{ session('profil_sukses') }}
            </div>
        @endif

        {{-- ===== FORM GANTI NAMA ===== --}}
        <div id="form-nama" style="padding:0 20px 20px;">
            @php
                // Tentukan route berdasarkan role user
                $role = auth('karyawan')->user()->peran;
                $routeNama = match($role) {
                    'admin' => 'admin.profil.nama',
                    'kasir' => 'kasir.profil.nama',
                    'operator' => 'operator.profil.nama',
                    default => 'admin.profil.nama',
                };
            @endphp

            <form method="POST" action="{{ route($routeNama) }}">
                @csrf
                <div style="margin-bottom:14px;">
                    <label style="font-size:12px;color:#6b7280;display:block;margin-bottom:5px;">Nama baru</label>
                    <input type="text" name="nama" value="{{ old('nama', auth('karyawan')->user()->nama) }}"
                        style="width:100%;padding:8px 11px;font-size:13px;border-radius:8px;border:1px solid {{ $errors->has('nama') ? '#fca5a5' : '#e5e7eb' }};outline:none;box-sizing:border-box;"
                        placeholder="Masukan nama baru">
                    @error('nama') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div style="margin-bottom:16px;">
                    <label style="font-size:12px;color:#6b7280;display:block;margin-bottom:5px;">Konfirmasi dengan password</label>
                    <input type="password" name="password"
                        style="width:100%;padding:8px 11px;font-size:13px;border-radius:8px;border:1px solid {{ $errors->has('password') ? '#fca5a5' : '#e5e7eb' }};outline:none;box-sizing:border-box;"
                        placeholder="Masukan password Anda">
                    @error('password') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <button type="submit" style="width:100%;padding:9px;font-size:13px;font-weight:600;border:none;border-radius:8px;background:#2563eb;color:#fff;cursor:pointer;transition:background .2s;">
                    Simpan Nama
                </button>
            </form>
        </div>

        {{-- ===== FORM UBAH PASSWORD ===== --}}
        <div id="form-password" class="hidden" style="padding:0 20px 20px;">
            @php
                $routePassword = match($role) {
                    'admin' => 'admin.profil.password',
                    'kasir' => 'kasir.profil.password',
                    'operator' => 'operator.profil.password',
                    default => 'admin.profil.password',
                };
            @endphp

            <form method="POST" action="{{ route($routePassword) }}">
                @csrf
                <div style="margin-bottom:14px;">
                    <label style="font-size:12px;color:#6b7280;display:block;margin-bottom:5px;">Password lama</label>
                    <input type="password" name="password_lama"
                        style="width:100%;padding:8px 11px;font-size:13px;border-radius:8px;border:1px solid {{ $errors->has('password_lama') ? '#fca5a5' : '#e5e7eb' }};outline:none;box-sizing:border-box;"
                        placeholder="Masukan password lama">
                    @error('password_lama') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div style="margin-bottom:14px;">
                    <label style="font-size:12px;color:#6b7280;display:block;margin-bottom:5px;">Password baru</label>
                    <input type="password" name="password_baru" id="input-pass-baru" oninput="cekKekuatan(this.value)"
                        style="width:100%;padding:8px 11px;font-size:13px;border-radius:8px;border:1px solid {{ $errors->has('password_baru') ? '#fca5a5' : '#e5e7eb' }};outline:none;box-sizing:border-box;"
                        placeholder="Min. 8 karakter (huruf besar + angka)">
                    <div style="height:3px;background:#e5e7eb;border-radius:2px;margin-top:6px;overflow:hidden;">
                        <div id="strength-bar" style="height:100%;width:0;transition:width .3s;border-radius:2px;"></div>
                    </div>
                    <span id="strength-label" style="font-size:11px;color:#9ca3af;display:block;margin-top:3px;"></span>
                    @error('password_baru') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div style="margin-bottom:16px;">
                    <label style="font-size:12px;color:#6b7280;display:block;margin-bottom:5px;">Konfirmasi password baru</label>
                    <input type="password" name="password_baru_confirmation"
                        style="width:100%;padding:8px 11px;font-size:13px;border-radius:8px;border:1px solid #e5e7eb;outline:none;box-sizing:border-box;"
                        placeholder="Ulangi password baru">
                </div>
                <button type="submit" style="width:100%;padding:9px;font-size:13px;font-weight:600;border:none;border-radius:8px;background:#2563eb;color:#fff;cursor:pointer;transition:background .2s;">
                    Ubah Password
                </button>
            </form>
        </div>

    </div>
</div>

{{-- ===== SCRIPT CEK KEKUATAN PASSWORD ===== --}}
<script>
function cekKekuatan(val) {
    let skor = 0;
    if (val.length >= 8) skor++;
    if (/[A-Z]/.test(val)) skor++;
    if (/[0-9]/.test(val)) skor++;
    if (/[^A-Za-z0-9]/.test(val)) skor++;

    const level = [
        { w: '0%',   c: 'transparent', t: '' },
        { w: '30%',  c: '#ef4444', t: 'Lemah' },
        { w: '55%',  c: '#f59e0b', t: 'Sedang' },
        { w: '80%',  c: '#22c55e', t: 'Kuat' },
        { w: '100%', c: '#10b981', t: 'Sangat kuat' },
    ][skor] || { w: '0%', c: 'transparent', t: '' };

    const bar = document.getElementById('strength-bar');
    const lbl = document.getElementById('strength-label');

    if (bar) {
        bar.style.width = level.w;
        bar.style.background = level.c;
    }

    if (lbl) {
        lbl.textContent = level.t;
        lbl.style.color = level.c;
    }
}
</script>