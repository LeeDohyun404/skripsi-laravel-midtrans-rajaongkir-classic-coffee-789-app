{{-- resources/views/admin/history.blade.php --}}
@extends('layouts.app')
@section('title', 'History Pesanan')
@section('content')

{{-- Navbar Staff --}}
<nav class="mb-4 py-3 px-4 d-flex align-items-center justify-content-between" style="background:#1c1917;">
    <span class="fw-black text-white fs-5">
        <i class="bi bi-cup-hot-fill text-warning me-2"></i>
        CLASSIC<span class="text-warning">789</span> Staff
    </span>
    <div class="d-flex align-items-center gap-2">
        <a class="px-3 py-2 rounded-3 text-decoration-none fw-semibold small {{ request()->is('admin/karyawan*') ? 'bg-warning text-dark' : 'text-white' }}"
           href="/admin/karyawan"><i class="bi bi-people-fill me-1"></i> Kelola Karyawan</a>
        <a class="px-3 py-2 rounded-3 text-decoration-none fw-semibold small {{ request()->is('admin/pelanggan') ? 'bg-warning text-dark' : 'text-white' }}"
           href="/admin/pelanggan"><i class="bi bi-person-lines-fill me-1"></i> Kelola Pelanggan</a>
        <a class="px-3 py-2 rounded-3 text-decoration-none fw-semibold small {{ request()->is('admin/history') ? 'bg-warning text-dark' : 'text-white' }}"
           href="/admin/history"><i class="bi bi-clock-history me-1"></i> History Pesanan</a>
        <a class="px-3 py-2 rounded-3 text-decoration-none fw-semibold small {{ request()->is('admin/stok') ? 'bg-warning text-dark' : 'text-white' }}"
           href="/admin/stok"><i class="bi bi-box-seam me-1"></i> Lihat Stok Produk</a>
        <form method="POST" action="{{ route('staff.logout') }}" class="d-inline ms-2">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-warning">
                <i class="bi bi-box-arrow-right me-1"></i> Logout
            </button>
        </form>
    </div>
</nav>


<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>History Pesanan</h4>
        <span class="text-muted small">Total: {{ count($orders) }} Transaksi</span>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Order ID & Waktu</th>
                            <th>Pelanggan</th>
                            <th>Tipe</th>
                            <th>Alamat / Catatan</th>
                            <th>Total</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $o)
                        <tr>
                            <td>
                                <span class="fw-bold text-warning d-block">#{{ substr($o->number, -8) }}</span>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($o->created_at)->format('d M Y, H:i') }}</small>
                            </td>
                            <td>
                                <span class="fw-bold d-block">{{ $o->customer_name }}</span>
                                <small class="text-success"><i class="bi bi-whatsapp me-1"></i>{{ $o->customer_phone }}</small><br>
                                <small class="text-muted">{{ $o->customer_email }}</small>
                            </td>
                            <td>
                                @if(($o->order_type ?? '') == 'dine-in')
                                    <span class="badge bg-primary">Makan di Sini</span>
                                @else
                                    <span class="badge bg-warning text-dark">Bawa Pulang</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ ($o->order_type ?? '') == 'dine-in' ? 'Pesanan di Tempat' : ($o->address ?? '-') }}</small>
                                @if($o->notes)
                                    <br><small class="text-warning fst-italic">"{{ $o->notes }}"</small>
                                @endif
                            </td>
                            <td class="fw-bold">Rp {{ number_format($o->total_price) }}</td>
                            <td class="text-center">
                                <span class="badge {{ in_array($o->status, ['success','settlement','capture','completed']) ? 'bg-success' : 'bg-warning text-dark' }}">
                                    {{ $o->status }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection