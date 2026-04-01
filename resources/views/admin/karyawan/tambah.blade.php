{{-- resources/views/admin/karyawan/tambah.blade.php --}}
@extends('layouts.app')

@section('title', 'Tambah Karyawan')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4 gap-2">
        <a href="{{ route('admin.karyawan.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h4 class="fw-bold mb-0">Tambah Karyawan</h4>
    </div>

    <div class="card shadow-sm" style="max-width:640px">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.karyawan.store') }}" enctype="multipart/form-data">
                @csrf

                {{-- Nama --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                           value="{{ old('nama') }}" required maxlength="100">
                    @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Username --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
                    <input type="text" name="username" class="form-control @error('username') is-invalid @enderror"
                           value="{{ old('username') }}" required maxlength="50">
                    @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Email --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}" maxlength="150">
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- No HP --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">No. HP</label>
                    <input type="text" name="no_hp" class="form-control @error('no_hp') is-invalid @enderror"
                           value="{{ old('no_hp') }}" maxlength="20">
                    @error('no_hp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Peran --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Peran <span class="text-danger">*</span></label>
                    <select name="peran" class="form-select @error('peran') is-invalid @enderror" required id="selectPeran">
                        <option value="">-- Pilih Peran --</option>
                        <option value="admin"    {{ old('peran') == 'admin'    ? 'selected' : '' }}>Admin (Super User)</option>
                        <option value="operator" {{ old('peran') == 'operator' ? 'selected' : '' }}>Operator (Back Office)</option>
                        <option value="kasir"    {{ old('peran') == 'kasir'    ? 'selected' : '' }}>Kasir (Front Liner)</option>
                    </select>
                    @error('peran') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Shift (muncul bila kasir / operator) --}}
                <div class="mb-3" id="fieldShift" style="display:none">
                    <label class="form-label fw-semibold">Shift</label>
                    <select name="shift" class="form-select">
                        <option value="">-- Pilih Shift --</option>
                        <option value="pagi"  {{ old('shift') == 'pagi'  ? 'selected' : '' }}>Pagi</option>
                        <option value="siang" {{ old('shift') == 'siang' ? 'selected' : '' }}>Siang</option>
                        <option value="malam" {{ old('shift') == 'malam' ? 'selected' : '' }}>Malam</option>
                    </select>
                </div>

                {{-- Password --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                           required minlength="8">
                    <div class="form-text">Minimal 8 karakter.</div>
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Konfirmasi Password --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Konfirmasi Password <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirmation" class="form-control" required minlength="8">
                </div>

                {{-- Foto --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Foto Profil</label>
                    <input type="file" name="foto" class="form-control @error('foto') is-invalid @enderror"
                           accept="image/png,image/jpeg,image/jpg">
                    <div class="form-text">Format: JPG/PNG, maks 1 MB.</div>
                    @error('foto') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Status --}}
                <div class="mb-4 form-check">
                    <input class="form-check-input" type="checkbox" name="is_aktif" id="isAktif"
                           value="1" {{ old('is_aktif', '1') ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="isAktif">Akun Aktif</label>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Simpan
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
// Tampilkan shift jika old value ada
if (['operator','kasir'].includes(selectPeran.value)) fieldShift.style.display = 'block';
</script>
@endpush
@endsection