@extends('layouts.karyawan')

@section('title', 'Kelola Pengaduan')
@section('page-title', 'Kelola Pengaduan')

@section('sidebar-nav')
    <span class="nav-section-label">Menu Utama</span>
    <a href="{{ route('operator.dashboard') }}" class="nav-item {{ request()->routeIs('operator.dashboard') ? 'active' : '' }}">
        <span class="nav-icon">🏠</span> Dashboard
    </a>
    <span class="nav-section-label">Inventori</span>
    <a href="{{ route('operator.stok') }}" class="nav-item {{ request()->routeIs('operator.stok') ? 'active' : '' }}">
        <span class="nav-icon">📦</span> Manajemen Stok
    </a>
    <span class="nav-section-label">Pengiriman</span>
    <a href="{{ route('operator.tracking') }}" class="nav-item {{ request()->routeIs('operator.tracking') ? 'active' : '' }}">
        <span class="nav-icon">🚚</span> Tracking Pesanan
    </a>
    <a href="{{ route('operator.pengaduan') }}" class="nav-item {{ request()->routeIs('operator.pengaduan*') ? 'active' : '' }}">
        <span class="nav-icon">📋</span> Kelola Pengaduan
        @if(isset($totalMenunggu) && $totalMenunggu > 0)
            <span style="margin-left:auto;background:#dc2626;color:white;font-size:10px;font-weight:700;padding:2px 7px;border-radius:99px;">{{ $totalMenunggu }}</span>
        @endif
    </a>
@endsection

@push('styles')
<style>
.complaint-badge { display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:700;padding:3px 10px;border-radius:99px; }
.hidden { display:none; }
</style>
@endpush

@section('content')

{{-- Filter --}}
<div style="display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap;">
    @foreach(['semua'=>'Semua','menunggu'=>'Menunggu','diproses'=>'Diproses','selesai'=>'Selesai','ditolak'=>'Ditolak'] as $key=>$label)
    <a href="?filter={{ $key }}"
        style="padding:8px 16px;border-radius:10px;font-size:12px;font-weight:700;text-decoration:none;transition:all 0.15s;
        {{ (request('filter','semua')===$key) ? 'background:var(--brown-dark);color:var(--gold);' : 'background:var(--cream-dark);color:var(--text-muted);' }}">
        {{ $label }}
        @if($key === 'menunggu' && isset($totalMenunggu) && $totalMenunggu > 0)
            <span style="background:#dc2626;color:white;font-size:10px;padding:1px 6px;border-radius:99px;margin-left:4px;">{{ $totalMenunggu }}</span>
        @endif
    </a>
    @endforeach
</div>

@if($complaints->isEmpty())
    <div class="card" style="padding:48px;text-align:center;">
        <div style="font-size:48px;margin-bottom:16px;">📋</div>
        <p style="color:var(--text-muted);font-size:14px;">Tidak ada pengaduan saat ini.</p>
    </div>
@else
<div style="display:flex;flex-direction:column;gap:16px;">
    @foreach($complaints as $complaint)
    @php
        $jenisCfg  = \App\Models\OrderComplaint::getJenis($complaint->jenis);
        $statusCfg = \App\Models\OrderComplaint::getStatus($complaint->status);
    @endphp

    <div class="card">
        <div style="padding:20px 24px;display:flex;flex-wrap:wrap;align-items:flex-start;justify-content:space-between;gap:12px;border-bottom:1px solid var(--cream-dark);">
            <div>
                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:6px;">
                    <span style="font-size:18px;">{{ $jenisCfg['icon'] }}</span>
                    <span style="font-weight:700;color:var(--text);font-size:14px;">{{ $jenisCfg['label'] }}</span>
                    <span class="complaint-badge" style="background:{{ $statusCfg['bg'] }};color:{{ $statusCfg['color'] }};">
                        {{ $statusCfg['label'] }}
                    </span>
                </div>
                <p style="font-size:12px;color:var(--text-muted);">
                    Pesanan: <strong>{{ $complaint->order->number ?? '-' }}</strong> ·
                    Pelanggan: <strong>{{ $complaint->order->customer_name ?? '-' }}</strong> ·
                    {{ \Carbon\Carbon::parse($complaint->created_at)->locale('id')->isoFormat('D MMM YYYY, HH:mm') }}
                </p>
                <p style="font-size:13px;color:var(--text);margin-top:8px;">{{ $complaint->detail }}</p>
            </div>

            <button onclick="toggleDetail('complaint-{{ $complaint->id }}')"
                class="btn btn-outline btn-sm" id="btn-complaint-{{ $complaint->id }}">
                ▼ Detail & Respon
            </button>
        </div>

        {{-- Detail collapsible --}}
        <div id="complaint-{{ $complaint->id }}" class="hidden">
            <div style="padding:20px 24px;display:grid;grid-template-columns:1fr 1fr;gap:24px;">

                {{-- Foto bukti --}}
                <div>
                    <p style="font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--text-muted);margin-bottom:12px;">Foto Bukti</p>
                    @if($complaint->foto)
                        @php
                            $fotoSrc = $complaint->foto_url ?? asset('storage/' . $complaint->foto);
                        @endphp
                        <div style="position:relative;display:inline-block;max-width:100%;">
                            <img src="{{ $fotoSrc }}"
                                alt="Foto Bukti"
                                style="width:100%;max-width:340px;border-radius:12px;border:1.5px solid var(--slate-200);cursor:pointer;object-fit:cover;box-shadow:var(--shadow-card);"
                                onclick="window.open('{{ $fotoSrc }}', '_blank')"
                                title="Klik untuk memperbesar foto">
                            <span style="display:block;font-size:11px;color:var(--slate-500);margin-top:6px;">🔍 Klik gambar untuk melihat ukuran penuh</span>
                        </div>
                    @else
                        <div style="background:var(--slate-100);border-radius:12px;padding:24px;text-align:center;color:var(--slate-500);font-size:13px;border:1px dashed var(--slate-300);">
                            📷 Tidak ada foto bukti yang diunggah
                        </div>
                    @endif
                </div>

                {{-- Form respon operator --}}
                <div>
                    <p style="font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--text-muted);margin-bottom:12px;">Respon Pengaduan</p>

                    @if($complaint->catatan_operator)
                    <div style="background:var(--cream-dark);border-radius:12px;padding:14px;margin-bottom:16px;font-size:13px;color:var(--text);">
                        <p style="font-size:10px;font-weight:700;color:var(--text-muted);margin-bottom:4px;">CATATAN SEBELUMNYA:</p>
                        {{ $complaint->catatan_operator }}
                    </div>
                    @endif

                    <form action="{{ route('operator.pengaduan.update', $complaint->id) }}" method="POST">
                        @csrf
                        <div class="form-group" style="margin-bottom:12px;">
                            <label>Update Status</label>
                            <select name="status" required>
                                @foreach(\App\Models\OrderComplaint::$statusConfig as $sKey => $sVal)
                                    <option value="{{ $sKey }}" {{ $complaint->status === $sKey ? 'selected' : '' }}>
                                        {{ $sVal['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group" style="margin-bottom:12px;">
                            <label>Catatan untuk Pelanggan</label>
                            <textarea name="catatan_operator" rows="3"
                                placeholder="Contoh: Kami mohon maaf atas ketidaknyamanan ini. Pengembalian dana akan diproses dalam 1-3 hari kerja."
                                style="resize:none;">{{ $complaint->catatan_operator }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary" style="width:100%;">
                            💾 Simpan Respon
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
<div style="margin-top:20px;">{{ $complaints->links() }}</div>
@endif

@endsection

@push('scripts')
<script>
function toggleDetail(id) {
    const el = document.getElementById(id);
    const isHidden = el.classList.toggle('hidden');
    const btnId = id.replace('complaint-', 'btn-complaint-');
    document.getElementById(btnId).textContent = isHidden ? '▼ Detail & Respon' : '▲ Tutup';
}
</script>
@endpush