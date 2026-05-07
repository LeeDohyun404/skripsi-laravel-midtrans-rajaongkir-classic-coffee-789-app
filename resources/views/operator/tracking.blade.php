@extends('layouts.karyawan')

@section('title', 'Tracking Pesanan')
@section('page-title', 'Tracking Pesanan')

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
    </a>
@endsection

@push('styles')
<style>
.status-badge{display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;padding:4px 12px;border-radius:99px;}
.badge-dibuat{background:#fef3c7;color:#92400e;}
.badge-dikemas{background:#dbeafe;color:#1e40af;}
.badge-dikirim_ke_pengirim{background:#ede9fe;color:#5b21b6;}
.badge-dikirim{background:#e0e7ff;color:#3730a3;}
.badge-selesai{background:#dcfce7;color:#15803d;}
.badge-batal{background:#fee2e2;color:#b91c1c;}
.hidden{display:none;}
</style>
@endpush

@section('content')

{{-- Filter --}}
<div style="display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap;">
    @foreach(['semua'=>'Semua','dikemas'=>'Perlu Dikemas','dikirim_ke_pengirim'=>'Siap Kirim','dikirim'=>'Sedang Dikirim'] as $key=>$label)
    <a href="?filter={{ $key }}"
        style="padding:8px 16px;border-radius:10px;font-size:12px;font-weight:700;text-decoration:none;transition:all 0.15s;
        {{ (request('filter','semua')===$key) ? 'background:var(--brown-dark);color:var(--gold);' : 'background:var(--cream-dark);color:var(--text-muted);' }}">
        {{ $label }}
    </a>
    @endforeach
</div>

@if($orders->isEmpty())
    <div class="card" style="padding:48px;text-align:center;">
        <div style="font-size:48px;margin-bottom:16px;">🚚</div>
        <p style="color:var(--text-muted);font-size:14px;">Tidak ada pesanan Take Away saat ini.</p>
    </div>
@else
<div style="display:flex;flex-direction:column;gap:16px;">
    @foreach($orders as $order)
    @php
        $ts  = $order->tracking_status ?? 'dibuat';
        $cfg = \App\Models\OrderTracking::getConfig($ts);
        $nextSteps = [];
        if ($ts === 'dibuat')              $nextSteps = ['dikemas'=>'Tandai Dikemas'];
        if ($ts === 'dikemas')             $nextSteps = ['dikirim_ke_pengirim'=>'Serahkan ke Kurir'];
        if ($ts === 'dikirim_ke_pengirim') $nextSteps = ['dikirim'=>'Tandai Dikirim'];
        $canBatal = !in_array($ts, ['selesai','batal']);
    @endphp

    <div class="card">
        {{-- Header --}}
        <div style="padding:20px 24px;display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:12px;border-bottom:1px solid var(--cream-dark);">
            <div>
                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:4px;">
                    <span style="font-family:monospace;font-weight:700;color:var(--text);">{{ $order->number }}</span>
                    <span class="status-badge badge-{{ $ts }}">{{ $cfg['label'] }}</span>
                </div>
                <p style="font-size:12px;color:var(--text-muted);">
                    {{ $order->customer_name }} · {{ $order->courier_name }} {{ $order->courier_service }} ·
                    {{ \Carbon\Carbon::parse($order->created_at)->locale('id')->isoFormat('D MMM YYYY, HH:mm') }}
                </p>
                @if($order->address)
                <p style="font-size:12px;color:var(--text-muted);margin-top:2px;">
                    📍 {{ $order->address }}
                </p>
                @endif
            </div>

            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                @foreach($nextSteps as $nextStatus => $nextLabel)
                <form action="{{ route('operator.tracking.update', $order->id) }}" method="POST"
                      onsubmit="return confirm('Update status ke {{ $nextLabel }}?')">
                    @csrf
                    <input type="hidden" name="status" value="{{ $nextStatus }}">
                    <input type="hidden" name="keterangan" value="">
                    <button type="submit" class="btn btn-primary btn-sm">
                        🚀 {{ $nextLabel }}
                    </button>
                </form>
                @endforeach

                @if($canBatal)
                <button onclick="openBatalModal({{ $order->id }}, '{{ $order->number }}')"
                    class="btn btn-danger btn-sm">
                    ✖ Batalkan
                </button>
                @endif

                <button onclick="toggleDetail('detail-op-{{ $order->id }}')"
                    class="btn btn-outline btn-sm" id="btn-op-{{ $order->id }}">
                    ▼ Detail
                </button>
            </div>
        </div>

        {{-- Items --}}
        <div style="padding:12px 24px;display:flex;flex-wrap:wrap;gap:12px;border-bottom:1px solid var(--cream-dark);">
            @foreach($order->items as $item)
            <span style="font-size:13px;color:var(--text);">{{ $item->product_name }} <span style="color:var(--text-muted);font-size:12px;">×{{ $item->quantity }}</span></span>
            @endforeach
        </div>

        {{-- Detail collapsible --}}
        <div id="detail-op-{{ $order->id }}" class="hidden">
            <div style="padding:20px 24px;display:grid;grid-template-columns:1fr 1fr;gap:24px;">

                {{-- Riwayat --}}
                <div>
                    <p style="font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--text-muted);margin-bottom:12px;">Riwayat Status</p>
                    <div style="display:flex;flex-direction:column;gap:8px;">
                        @foreach($order->trackings->sortByDesc('created_at') as $log)
                        @php $lc = \App\Models\OrderTracking::getConfig($log->status); @endphp
                        <div style="display:flex;gap:8px;align-items:flex-start;">
                            <div style="width:24px;height:24px;border-radius:50%;background:var(--cream-dark);display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:2px;font-size:10px;">
                                ●
                            </div>
                            <div style="flex:1;">
                                <div style="display:flex;justify-content:space-between;align-items:center;">
                                    <p style="font-size:12px;font-weight:700;color:var(--text);">{{ $lc['label'] }}</p>
                                    <p style="font-size:11px;color:var(--text-muted);">{{ \Carbon\Carbon::parse($log->created_at)->locale('id')->isoFormat('D MMM, HH:mm') }}</p>
                                </div>
                                @if($log->keterangan)<p style="font-size:11px;color:var(--text-muted);">{{ $log->keterangan }}</p>@endif
                                @if($log->updated_by)<p style="font-size:11px;color:var(--text-muted);">oleh {{ $log->updated_by }}</p>@endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Update dengan catatan --}}
                @if(!in_array($ts, ['selesai','batal']))
                <div>
                    <p style="font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--text-muted);margin-bottom:12px;">Update dengan Catatan</p>
                    <form action="{{ route('operator.tracking.update', $order->id) }}" method="POST">
                        @csrf
                        <div class="form-group" style="margin-bottom:12px;">
                            <label>Status Baru</label>
                            <select name="status" required>
                                @foreach(\App\Models\OrderTracking::$statusConfig as $sKey => $sVal)
                                    @if(!in_array($sKey, ['dibuat','selesai','batal']))
                                        <option value="{{ $sKey }}" {{ $ts===$sKey?'selected':'' }}>{{ $sVal['label'] }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group" style="margin-bottom:12px;">
                            <label>Catatan (opsional)</label>
                            <textarea name="keterangan" rows="2" placeholder="Contoh: Paket diserahkan ke driver JNE..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary" style="width:100%;">
                            💾 Simpan Update
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>
<div style="margin-top:20px;">{{ $orders->links() }}</div>
@endif

{{-- Modal Batal --}}
<div id="batalModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:50;display:none;align-items:center;justify-content:center;padding:16px;">
    <div style="background:white;border-radius:20px;box-shadow:0 20px 60px rgba(0,0,0,0.2);width:100%;max-width:440px;padding:24px;">
        <h3 style="font-weight:700;color:var(--text);font-size:16px;margin-bottom:4px;">Batalkan Pesanan</h3>
        <p style="color:var(--text-muted);font-size:13px;margin-bottom:16px;" id="batalModalDesc"></p>
        <form id="batalForm" action="" method="POST">
            @csrf
            <input type="hidden" name="status" value="batal">
            <div class="form-group" style="margin-bottom:16px;">
                <label>Alasan Pembatalan *</label>
                <textarea name="keterangan" rows="3" required placeholder="Contoh: Alamat tidak ditemukan setelah konfirmasi ke kurir via WA."></textarea>
            </div>
            <div style="display:flex;gap:12px;">
                <button type="button" onclick="closeBatalModal()" class="btn btn-outline" style="flex:1;">Tidak</button>
                <button type="submit" class="btn btn-danger" style="flex:1;background:#dc2626;color:white;border-color:#dc2626;">
                    ✖ Ya, Batalkan
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function toggleDetail(id) {
    const el = document.getElementById(id);
    const isHidden = el.classList.toggle('hidden');
    const ordId = id.replace('detail-op-','');
    document.getElementById('btn-op-'+ordId).textContent = isHidden ? '▼ Detail' : '▲ Tutup';
}
function openBatalModal(orderId, orderNumber) {
    document.getElementById('batalModalDesc').textContent = 'Pesanan #' + orderNumber;
    document.getElementById('batalForm').action = '/operator/tracking/' + orderId + '/update';
    document.getElementById('batalModal').style.display = 'flex';
}
function closeBatalModal() {
    document.getElementById('batalModal').style.display = 'none';
}
document.getElementById('batalModal').addEventListener('click', function(e){
    if(e.target===this) closeBatalModal();
});
</script>
@endpush