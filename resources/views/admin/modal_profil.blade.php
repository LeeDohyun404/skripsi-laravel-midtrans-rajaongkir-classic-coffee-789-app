{{--
    MODAL PROFIL UNIVERSAL — Untuk Admin, Kasir, Operator
    Theme: Modern Slate Tech Enterprise
--}}

@php
    $tabAwal = session("modal_tab")
        ?? ($errors->has("password_lama") || $errors->has("password_baru") ? "password" : "nama");
@endphp

<script>
function bukaModalProfil(tab = 'nama') {
    const modal = document.getElementById('modal-profil');
    if (modal) {
        modal.classList.remove('hidden');
        switchTabProfil(tab || '{{ $tabAwal }}');
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
        if (formNama) formNama.classList.remove('hidden');
        if (formPassword) formPassword.classList.add('hidden');
        if (tabNama) {
            tabNama.classList.add('tab-aktif');
            tabNama.classList.remove('tab-nonaktif');
        }
        if (tabPassword) {
            tabPassword.classList.remove('tab-aktif');
            tabPassword.classList.add('tab-nonaktif');
        }
    } else {
        if (formNama) formNama.classList.add('hidden');
        if (formPassword) formPassword.classList.remove('hidden');
        if (tabNama) {
            tabNama.classList.remove('tab-aktif');
            tabNama.classList.add('tab-nonaktif');
        }
        if (tabPassword) {
            tabPassword.classList.add('tab-aktif');
            tabPassword.classList.remove('tab-nonaktif');
        }
    }
}

// Buka modal otomatis jika ada error atau success
@if(session('profil_sukses') || $errors->has('nama') || $errors->has('password') || $errors->has('password_lama') || $errors->has('password_baru'))
    document.addEventListener('DOMContentLoaded', function () {
        bukaModalProfil('{{ $tabAwal }}');
    });
@endif
</script>

<style>
    .tab-aktif {
        background: #ffffff !important;
        color: #0f172a !important;
        font-weight: 700 !important;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08) !important;
        border: 1px solid #e2e8f0 !important;
    }
    .tab-nonaktif {
        background: transparent !important;
        color: #64748b !important;
        font-weight: 500 !important;
        border: 1px solid transparent !important;
    }
    .hidden { 
        display: none !important; 
    }
    .field-error { 
        font-size: 11.5px; 
        color: #e11d48; 
        margin-top: 4px; 
        font-weight: 500;
    }
</style>

{{-- MODAL OVERLAY --}}
<div id="modal-profil" class="hidden" style="position:fixed;inset:0;background:rgba(15,23,42,0.65);backdrop-filter:blur(6px);z-index:9999;display:flex;align-items:center;justify-content:center;padding:16px;" onclick="if(event.target===this)tutupModalProfil()">
    <div style="background:#ffffff;border-radius:20px;width:440px;max-width:100%;overflow:hidden;border:1px solid #e2e8f0;box-shadow:0 25px 50px -12px rgba(15,23,42,0.25);animation:modalPop 0.22s cubic-bezier(0.16,1,0.3,1);">

        <style>
            @keyframes modalPop {
                from { opacity: 0; transform: scale(0.96) translateY(6px); }
                to   { opacity: 1; transform: scale(1) translateY(0); }
            }
        </style>

        {{-- HEADER --}}
        <div style="display:flex;align-items:center;justify-content:space-between;padding:18px 24px;border-bottom:1px solid #f1f5f9;background:#f8fafc;">
            <div style="display:flex;align-items:center;gap:8px;">
                <span style="font-size:16px;">⚙️</span>
                <span style="font-size:14.5px;font-weight:700;color:#0f172a;letter-spacing:-0.2px;">Pengaturan Akun Staff</span>
            </div>
            <button type="button" onclick="tutupModalProfil()" style="background:#f1f5f9;border:none;cursor:pointer;width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:16px;line-height:1;color:#64748b;transition:background 0.15s;" onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">×</button>
        </div>

        {{-- INFO PROFIL --}}
        <div style="margin:20px 24px 16px;display:flex;align-items:center;gap:14px;background:#f8fafc;border-radius:14px;padding:14px 16px;border:1px solid #e2e8f0;">
            <div style="width:46px;height:46px;border-radius:50%;background:linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%);display:flex;align-items:center;justify-content:center;font-size:15px;font-weight:700;color:#ffffff;flex-shrink:0;box-shadow:0 2px 8px rgba(79,70,229,0.3);">
                {{ strtoupper(substr(auth('karyawan')->user()->nama ?? 'U', 0, 2)) }}
            </div>
            <div style="flex:1;min-width:0;">
                <div style="font-size:14px;font-weight:700;color:#0f172a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    {{ auth('karyawan')->user()->nama ?? '' }}
                </div>
                <div style="font-size:11.5px;color:#64748b;text-transform:capitalize;margin-top:2px;display:flex;align-items:center;gap:6px;">
                    <span style="display:inline-block;width:7px;height:7px;border-radius:50%;background:#10b981;"></span>
                    {{ auth('karyawan')->user()->peran ?? 'Staff' }} • {{ auth('karyawan')->user()->email ?? '' }}
                </div>
            </div>
        </div>

        {{-- TABS --}}
        <div style="display:flex;gap:4px;margin:0 24px 18px;background:#f1f5f9;border-radius:10px;padding:4px;">
            <button type="button" id="tab-nama" onclick="switchTabProfil('nama')" class="tab-aktif" 
                style="flex:1;padding:8px 12px;font-size:12.5px;border:none;cursor:pointer;border-radius:8px;transition:all .18s;font-family:inherit;">
                ✏️ Ganti Nama
            </button>
            <button type="button" id="tab-password" onclick="switchTabProfil('password')" class="tab-nonaktif"
                style="flex:1;padding:8px 12px;font-size:12.5px;border:none;cursor:pointer;border-radius:8px;transition:all .18s;font-family:inherit;">
                🔑 Ubah Password
            </button>
        </div>

        {{-- ALERT SUKSES --}}
        @if(session('profil_sukses'))
            <div style="margin:0 24px 16px;padding:12px 16px;background:#ecfdf5;border:1px solid #a7f3d0;border-radius:10px;font-size:12.5px;color:#047857;font-weight:600;display:flex;align-items:center;gap:8px;">
                <span>✓</span> {{ session('profil_sukses') }}
            </div>
        @endif

        {{-- FORM GANTI NAMA --}}
        <div id="form-nama" style="padding:0 24px 24px;">
            @php
                $role = auth('karyawan')->user()->peran ?? 'admin';
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
                    <label style="font-size:12px;font-weight:600;color:#334155;display:block;margin-bottom:6px;">Nama Lengkap Baru</label>
                    <input type="text" name="nama" value="{{ old('nama', auth('karyawan')->user()->nama ?? '') }}"
                        style="width:100%;padding:10px 13px;font-size:13.5px;border-radius:9px;border:1.5px solid {{ $errors->has('nama') ? '#fda4af' : '#e2e8f0' }};outline:none;box-sizing:border-box;font-family:inherit;"
                        placeholder="Masukkan nama baru" required>
                    @error('nama') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div style="margin-bottom:18px;">
                    <label style="font-size:12px;font-weight:600;color:#334155;display:block;margin-bottom:6px;">Konfirmasi Password Saat Ini</label>
                    <input type="password" name="password"
                        style="width:100%;padding:10px 13px;font-size:13.5px;border-radius:9px;border:1.5px solid {{ $errors->has('password') ? '#fda4af' : '#e2e8f0' }};outline:none;box-sizing:border-box;font-family:inherit;"
                        placeholder="Ketik password untuk konfirmasi" required>
                    @error('password') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <button type="submit" style="width:100%;padding:11px;font-size:13px;font-weight:600;border:none;border-radius:9px;background:linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);color:#ffffff;cursor:pointer;transition:all .18s;box-shadow:0 2px 6px rgba(79,70,229,0.25);font-family:inherit;">
                    Simpan Perubahan Nama
                </button>
            </form>
        </div>

        {{-- FORM UBAH PASSWORD --}}
        <div id="form-password" class="hidden" style="padding:0 24px 24px;">
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
                    <label style="font-size:12px;font-weight:600;color:#334155;display:block;margin-bottom:6px;">Password Lama</label>
                    <input type="password" name="password_lama"
                        style="width:100%;padding:10px 13px;font-size:13.5px;border-radius:9px;border:1.5px solid {{ $errors->has('password_lama') ? '#fda4af' : '#e2e8f0' }};outline:none;box-sizing:border-box;font-family:inherit;"
                        placeholder="Masukkan password saat ini" required>
                    @error('password_lama') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div style="margin-bottom:14px;">
                    <label style="font-size:12px;font-weight:600;color:#334155;display:block;margin-bottom:6px;">Password Baru</label>
                    <input type="password" name="password_baru" id="input-pass-baru" oninput="cekKekuatan(this.value)"
                        style="width:100%;padding:10px 13px;font-size:13.5px;border-radius:9px;border:1.5px solid {{ $errors->has('password_baru') ? '#fda4af' : '#e2e8f0' }};outline:none;box-sizing:border-box;font-family:inherit;"
                        placeholder="Min. 8 karakter kombinasi" required>
                    <div style="height:4px;background:#e2e8f0;border-radius:2px;margin-top:7px;overflow:hidden;">
                        <div id="strength-bar" style="height:100%;width:0;transition:width .3s;border-radius:2px;"></div>
                    </div>
                    <span id="strength-label" style="font-size:11px;color:#64748b;display:block;margin-top:4px;font-weight:600;"></span>
                    @error('password_baru') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div style="margin-bottom:18px;">
                    <label style="font-size:12px;font-weight:600;color:#334155;display:block;margin-bottom:6px;">Ulangi Password Baru</label>
                    <input type="password" name="password_baru_confirmation"
                        style="width:100%;padding:10px 13px;font-size:13.5px;border-radius:9px;border:1.5px solid #e2e8f0;outline:none;box-sizing:border-box;font-family:inherit;"
                        placeholder="Ulangi password baru" required>
                </div>
                <button type="submit" style="width:100%;padding:11px;font-size:13px;font-weight:600;border:none;border-radius:9px;background:linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);color:#ffffff;cursor:pointer;transition:all .18s;box-shadow:0 2px 6px rgba(79,70,229,0.25);font-family:inherit;">
                    Perbarui Password
                </button>
            </form>
        </div>

    </div>
</div>

<script>
function cekKekuatan(val) {
    let skor = 0;
    if (val.length >= 8) skor++;
    if (/[A-Z]/.test(val)) skor++;
    if (/[0-9]/.test(val)) skor++;
    if (/[^A-Za-z0-9]/.test(val)) skor++;

    const level = [
        { w: '0%',   c: 'transparent', t: '' },
        { w: '25%',  c: '#f43f5e',     t: 'Lemah' },
        { w: '50%',  c: '#f59e0b',     t: 'Cukup' },
        { w: '75%',  c: '#0284c7',     t: 'Kuat' },
        { w: '100%', c: '#10b981',     t: 'Sangat Kuat' },
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
