{{-- resources/views/admin/karyawan/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Karyawan')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4 gap-2">
        <a href="{{ route('admin.karyawan.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h4 class="fw-bold mb-0">Edit Karyawan</h4>
    </div>

    <div class="card shadow-sm" style="max-width:640px">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.karyawan.update', $karyawan->id) }}" enctype="multipart/form-data">
                @csrf @method('PUT')

                {{-- Nama --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                           value="{{ old('nama', $karyawan->nama) }}" required maxlength="100">
                    @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Username --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
                    <input type="text" name="username" class="form-control @error('username') is-invalid @enderror"
                           value="{{ old('username', $karyawan->username) }}" required maxlength="50">
                    @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Email --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email', $karyawan->email) }}" maxlength="150">
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- No HP --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">No. HP</label>
                    <input type="text" name="no_hp" class="form-control @error('no_hp') is-invalid @enderror"
                           value="{{ old('no_hp', $karyawan->no_hp) }}" maxlength="20">
                    @error('no_hp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Peran --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Peran <span class="text-danger">*</span></label>
                    <select name="peran" class="form-select @error('peran') is-invalid @enderror" required id="selectPeran">
                        <option value="admin"    {{ old('peran', $karyawan->peran) == 'admin'    ? 'selected' : '' }}>Admin (Super User)</option>
                        <option value="operator" {{ old('peran', $karyawan->peran) == 'operator' ? 'selected' : '' }}>Operator (Back Office)</option>
                        <option value="kasir"    {{ old('peran', $karyawan->peran) == 'kasir'    ? 'selected' : '' }}>Kasir (Front Liner)</option>
                    </select>
                    @error('peran') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Shift --}}
                <div class="mb-3" id="fieldShift"
                     style="{{ in_array($karyawan->peran, ['operator','kasir']) ? '' : 'display:none' }}">
                    <label class="form-label fw-semibold">Shift</label>
                    <select name="shift" class="form-select">
                        <option value="">-- Pilih Shift --</option>
                        <option value="pagi"  {{ old('shift', $karyawan->shift) == 'pagi'  ? 'selected' : '' }}>Pagi</option>
                        <option value="siang" {{ old('shift', $karyawan->shift) == 'siang' ? 'selected' : '' }}>Siang</option>
                        <option value="malam" {{ old('shift', $karyawan->shift) == 'malam' ? 'selected' : '' }}>Malam</option>
                    </select>
                </div>

                {{-- Password (opsional saat edit) --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Password Baru</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                           minlength="8" placeholder="Kosongkan jika tidak ingin ubah">
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" class="form-control" minlength="8">
                </div>

                {{-- Foto --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Foto Profil</label>
                    @if($karyawan->foto)
                        <div class="mb-2">
                            <img src="{{ asset('storage/'.$karyawan->foto) }}" width="60" height="60"
                                 class="rounded-circle border" style="object-fit:cover">
                            <small class="text-muted ms-2">Foto saat ini</small>
                        </div>
                    @endif
                    <input type="file" name="foto" class="form-control @error('foto') is-invalid @enderror"
                           accept="image/png,image/jpeg,image/jpg">
                    <div class="form-text">Format: JPG/PNG, maks 1 MB. Kosongkan jika tidak ingin ubah.</div>
                    @error('foto') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Status --}}
                <div class="mb-4 form-check">
                    <input class="form-check-input" type="checkbox" name="is_aktif" id="isAktif"
                           value="1" {{ old('is_aktif', $karyawan->is_aktif) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="isAktif">Akun Aktif</label>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Simpan Perubahan
                    </button>
                    <a href="{{ route('admin.karyawan.index') }}" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
const selectPeran = document.getElementById('selectPeran');
const fieldShift  = document.getElementById('fieldShift');
selectPeran.addEventListener('change', function () {
    fieldShift.style.display = ['operator','kasir'].includes(this.value) ? 'block' : 'none';
});
</script>
@endpush
@endsection