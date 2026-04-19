@extends('layouts.karyawan')

@section('title', 'Dashboard Kasir')
@section('page-title', 'Dashboard')

@section('sidebar-nav')
    <span class="nav-section-label">Menu Utama</span>
    <a href="{{ route('kasir.dashboard') }}" class="nav-item {{ request()->routeIs('kasir.dashboard') ? 'active' : '' }}">
        <span class="nav-icon">🏠</span> Dashboard
    </a>
    <span class="nav-section-label">Transaksi</span>
    <a href="{{ route('kasir.transaksi') }}" class="nav-item {{ request()->routeIs('kasir.transaksi') ? 'active' : '' }}">
        <span class="nav-icon">🧾</span> Kelola Transaksi
    </a>
    @include('admin.modal_profil')
@endsection

@push('styles')
<style>
    /* ===== WHATSAPP BUTTON ===== */
    .btn-whatsapp {
        background: #25D366;
        color: white;
        border: none;
        padding: 10px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: background 0.2s, transform 0.1s;
    }
    .btn-whatsapp:hover {
        background: #1fbd50;
        transform: translateY(-1px);
    }
    .btn-whatsapp:active {
        transform: translateY(0);
    }
    /* ===== MODAL OVERLAY ===== */
    .modal-panel {
        position: fixed;
        inset: 0;
        background: rgba(30,17,8,0.5);
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        z-index: 9999;
        overflow-y: auto;
    }
    .modal-panel.show { display: flex; }

    .modal-card {
        width: 100%;
        max-width: 680px;
        background: white;
        border-radius: 18px;
        border: 1px solid var(--cream-dark);
        box-shadow: 0 20px 50px rgba(0,0,0,0.15);
        overflow: visible; /* ✅ FIX: dropdown tidak terpotong */
        margin: auto;
    }
    .modal-card-header {
        padding: 20px 26px;
        border-bottom: 1px solid var(--cream-dark);
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #fdf9f4;
        border-radius: 18px 18px 0 0; /* ✅ FIX: radius tetap terlihat */
    }
    .modal-card-header h3 {
        font-family: 'Playfair Display', serif;
        font-size: 20px;
        font-weight: 400;
        color: var(--brown-mid);
    }
    .modal-card-body { padding: 24px 26px; }
    .modal-card-footer {
        padding: 18px 26px;
        border-top: 1px solid var(--cream-dark);
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        background: #fdf9f4;
        border-radius: 0 0 18px 18px; /* ✅ FIX: radius tetap terlihat */
    }
    .modal-close-btn {
        background: none;
        border: none;
        font-size: 22px;
        cursor: pointer;
        color: var(--text-muted);
        line-height: 1;
        padding: 2px 6px;
        border-radius: 6px;
        transition: background 0.15s;
    }
    .modal-close-btn:hover { background: var(--cream-dark); }

    /* ===== FORM TRANSAKSI ===== */
    .form-section-label {
        font-size: 11px;
        letter-spacing: 2px;
        text-transform: uppercase;
        color: var(--text-muted);
        margin: 20px 0 10px;
        font-weight: 500;
    }
    .form-section-label:first-child { margin-top: 0; }

    .input-field {
        width: 100%;
        padding: 11px 14px;
        border: 1.5px solid var(--cream-dark);
        border-radius: 10px;
        font-size: 14px;
        font-family: 'DM Sans', sans-serif;
        color: var(--text);
        background: white;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .input-field:focus {
        border-color: var(--gold);
        box-shadow: 0 0 0 3px rgba(200,169,110,0.12);
    }

    /* ===== KERANJANG PRODUK ===== */
    .product-search-wrap {
        position: relative;
    }
    .product-dropdown {
        position: absolute;
        top: calc(100% + 4px);
        left: 0;
        right: 0;
        background: white;
        border: 1.5px solid var(--gold);
        border-radius: 10px;
        max-height: 220px;
        overflow-y: auto;
        z-index: 99999;
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        display: none;
    }
    .product-dropdown.show { display: block; }
    .product-option {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 14px;
        cursor: pointer;
        border-bottom: 1px solid var(--cream-dark);
        transition: background 0.12s;
    }
    .product-option:last-child { border-bottom: none; }
    .product-option:hover { background: #fdf9f4; }
    .product-option img {
        width: 36px; height: 36px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid var(--cream-dark);
        flex-shrink: 0;
    }
    .product-option .opt-fallback {
        width: 36px; height: 36px;
        border-radius: 8px;
        background: #fdf1d6;
        border: 1px solid var(--cream-dark);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .product-option .opt-name { font-size: 13.5px; font-weight: 500; color: var(--text); }
    .product-option .opt-price { font-size: 12px; color: var(--text-muted); }

    /* ===== CART ITEMS ===== */
    .cart-list {
        margin-top: 12px;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .cart-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 14px;
        border: 1px solid var(--cream-dark);
        border-radius: 10px;
        background: #fdf9f4;
    }
    .cart-item-img {
        width: 36px; height: 36px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid var(--cream-dark);
        flex-shrink: 0;
    }
    .cart-item-fallback {
        width: 36px; height: 36px;
        border-radius: 8px;
        background: #fdf1d6;
        border: 1px solid var(--cream-dark);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .cart-item-info { flex: 1; min-width: 0; }
    .cart-item-name { font-size: 13.5px; font-weight: 600; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .cart-item-price { font-size: 12px; color: var(--text-muted); }
    .qty-control { display: flex; align-items: center; gap: 6px; }
    .qty-btn {
        width: 28px; height: 28px;
        border: 1.5px solid var(--cream-dark);
        background: white;
        border-radius: 7px;
        font-size: 16px;
        cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        font-family: 'DM Sans', sans-serif;
        transition: all 0.12s;
        color: var(--brown-mid);
        line-height: 1;
    }
    .qty-btn:hover { border-color: var(--gold); background: #fdf9f4; }
    .qty-value { min-width: 28px; text-align: center; font-size: 14px; font-weight: 600; color: var(--text); }
    .cart-remove {
        background: none; border: none; cursor: pointer;
        font-size: 18px; color: #c0392b; padding: 4px;
        opacity: 0.6; transition: opacity 0.15s;
    }
    .cart-remove:hover { opacity: 1; }
    .cart-item-subtotal { font-size: 13px; font-weight: 600; color: var(--brown-mid); white-space: nowrap; min-width: 80px; text-align: right; }

    .empty-cart {
        text-align: center;
        color: var(--text-muted);
        padding: 28px;
        font-size: 13px;
        border: 1.5px dashed var(--cream-dark);
        border-radius: 10px;
        margin-top: 8px;
    }

    .cart-total-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px 16px;
        background: white;
        border: 1.5px solid var(--gold);
        border-radius: 10px;
        margin-top: 14px;
    }
    .cart-total-label { font-size: 13px; color: var(--text-muted); }
    .cart-total-value { font-family: 'Playfair Display', serif; font-size: 22px; color: var(--brown-mid); }

    /* ===== STRUK / RECEIPT ===== */
    #modalStruk .modal-card {
        max-width: 420px;
        font-family: 'DM Sans', sans-serif;
    }
    .struk-wrap {
        background: white;
        padding: 0;
    }
    .struk-header {
        text-align: center;
        padding: 20px 24px 14px;
        border-bottom: 2px dashed var(--cream-dark);
    }
    .struk-logo {
        font-family: 'Playfair Display', serif;
        font-size: 22px;
        color: var(--brown-mid);
        margin-bottom: 2px;
    }
    .struk-sub {
        font-size: 11px;
        letter-spacing: 2px;
        text-transform: uppercase;
        color: var(--text-muted);
    }
    .struk-antrian {
        text-align: center;
        padding: 18px 24px 14px;
        border-bottom: 1px solid var(--cream-dark);
        background: #fdf9f4;
    }
    .struk-antrian-label {
        font-size: 11px;
        letter-spacing: 2px;
        text-transform: uppercase;
        color: var(--text-muted);
        margin-bottom: 4px;
    }
    .struk-antrian-number {
        font-family: 'Playfair Display', serif;
        font-size: 52px;
        line-height: 1;
        color: var(--brown-mid);
        font-weight: 600;
    }
    .struk-info {
        padding: 14px 24px;
        border-bottom: 1px solid var(--cream-dark);
    }
    .struk-row {
        display: flex;
        justify-content: space-between;
        font-size: 13px;
        padding: 4px 0;
    }
    .struk-row .lbl { color: var(--text-muted); }
    .struk-row .val { color: var(--text); font-weight: 500; }
    .struk-items {
        padding: 14px 24px;
        border-bottom: 2px dashed var(--cream-dark);
    }
    .struk-item-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        font-size: 13px;
        padding: 5px 0;
        gap: 8px;
    }
    .struk-item-name { flex: 1; color: var(--text); }
    .struk-item-qty { color: var(--text-muted); font-size: 12px; white-space: nowrap; }
    .struk-item-price { font-weight: 600; color: var(--brown-mid); white-space: nowrap; }
    .struk-total {
        padding: 14px 24px 18px;
        text-align: right;
    }
    .struk-total-label { font-size: 12px; color: var(--text-muted); margin-bottom: 2px; }
    .struk-total-value { font-family: 'Playfair Display', serif; font-size: 26px; color: var(--brown-mid); }
    .struk-footer {
        text-align: center;
        padding: 12px 24px 20px;
        border-top: 1px solid var(--cream-dark);
        font-size: 12px;
        color: var(--text-muted);
    }

    /* Print style */
    @media print {
        body * { visibility: hidden; }
        #strukturCetak, #strukturCetak * { visibility: visible; }
        #strukturCetak { position: fixed; inset: 0; background: white; }
    }
</style>
@endpush

@section('content')

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">🧾</div>
        <div class="stat-value">{{ $transaksiHariIni ?? 0 }}</div>
        <div class="stat-label">Transaksi Hari Ini</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">💰</div>
        <div class="stat-value">Rp {{ number_format($pendapatanHariIni ?? 0, 0, ',', '.') }}</div>
        <div class="stat-label">Pendapatan Hari Ini</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">📅</div>
        <div class="stat-value">{{ $transaksiMingguIni ?? 0 }}</div>
        <div class="stat-label">Transaksi Minggu Ini</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">💵</div>
        <div class="stat-value">Rp {{ number_format($pendapatanMingguIni ?? 0, 0, ',', '.') }}</div>
        <div class="stat-label">Pendapatan Minggu Ini</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Transaksi Terbaru Hari Ini</h2>
        <button type="button" class="btn btn-primary btn-sm" onclick="bukaModalTransaksi()">
            + Transaksi Baru
        </button>
    </div>
    <div style="padding:0;">
        <table>
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>No. Order</th>
                    <th>Pelanggan</th>
                    <th>Total</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transaksiTerbaru ?? [] as $t)
                    <tr>
                        <td style="color:var(--text-muted);font-size:12px;">
                            {{ \Carbon\Carbon::parse($t->created_at)->format('H:i') }}
                        </td>
                        <td><span class="mono-code">{{ $t->number ?? '-' }}</span></td>
                        <td>{{ $t->customer_name ?? 'Umum' }}</td>
                        <td style="font-weight:500;">Rp {{ number_format($t->total_price ?? 0, 0, ',', '.') }}</td>
                        <td>
                            @php
                                $badge = match($t->status ?? '') {
                                    'pending'    => 'badge-gold',
                                    'processing' => 'badge-blue',
                                    'completed', 'success' => 'badge-green',
                                    'cancelled'  => 'badge-red',
                                    default      => 'badge-gray',
                                };
                                $label = match($t->status ?? '') {
                                    'pending'    => '⏳ Pending',
                                    'processing' => '▶ Diproses',
                                    'completed', 'success' => '✓ Selesai',
                                    'cancelled'  => '✕ Batal',
                                    default      => ucfirst($t->status ?? '-'),
                                };
                            @endphp
                            <span class="badge {{ $badge }}">{{ $label }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center;color:var(--text-muted);padding:32px;">
                            Belum ada transaksi hari ini
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- =====================================================
     MODAL: FORM TRANSAKSI BARU
===================================================== --}}
<div class="modal-panel" id="modalTransaksi">
    <div class="modal-card">
        <div class="modal-card-header">
            <h3>🧾 Transaksi Baru</h3>
            <button class="modal-close-btn" onclick="tutupModal('modalTransaksi')">×</button>
        </div>
        <div class="modal-card-body">

            {{-- Nama Pembeli --}}
            <p class="form-section-label">Informasi Pembeli</p>
            <input
                type="text"
                id="tnNamaPembeli"
                class="input-field"
                placeholder="Nama pembeli (opsional — kosong = Umum)"
                autocomplete="off"
            >
            <!-- ===== WHATSAPP NUMBER INPUT ===== -->
            <div class="form-section-label">Nomor WhatsApp (Opsional)</div>
            <input 
                type="text" 
                class="input-field" 
                id="tnWhatsapp" 
                placeholder="Contoh: 081234567890 atau +6281234567890"
            />
            <div style="font-size:11px; color:var(--text-muted); margin-top:6px;">
                💡 Masukkan nomor WhatsApp untuk mengirim pesanan langsung
            </div>
            {{-- Pilih Produk --}}
            <p class="form-section-label" style="margin-top:18px;">Produk yang Dibeli</p>
            <div class="product-search-wrap">
                <input
                    type="text"
                    id="tnSearchProduk"
                    class="input-field"
                    placeholder="🔍 Ketik nama produk untuk mencari..."
                    autocomplete="off"
                    oninput="cariProduk(this.value)"
                    onfocus="cariProduk(this.value)"
                >
                <div class="product-dropdown" id="productDropdown"></div>
            </div>

            {{-- Keranjang --}}
            <div id="cartList" class="cart-list">
                <div class="empty-cart" id="emptyCartMsg">
                    ☕ Belum ada produk dipilih. Ketik di atas untuk mencari produk.
                </div>
            </div>

            {{-- Total --}}
            <div class="cart-total-row" id="cartTotalRow" style="display:none;">
                <span class="cart-total-label">Total Pembayaran</span>
                <span class="cart-total-value" id="cartTotalDisplay">Rp 0</span>
            </div>

        </div>
        <div class="modal-card-footer">
            <button type="button" class="btn btn-outline" onclick="tutupModal('modalTransaksi')">Batal</button>
            <!-- Tombol Kirim WhatsApp -->
            <button class="btn-whatsapp" type="button" onclick="kirimKeWhatsapp()" id="btnKirimWA">
                📱 Kirim ke WhatsApp
            </button>
            <button type="button" class="btn btn-primary" onclick="prosesTransaksi()">
                🧾 Cetak Struk &amp; Simpan
            </button>
        </div>
    </div>
</div>

{{-- =====================================================
     MODAL: STRUK / RECEIPT
===================================================== --}}
<div class="modal-panel" id="modalStruk">
    <div class="modal-card">
        <div id="strukturCetak">
            <div class="struk-wrap">

                <div class="struk-header">
                    <div class="struk-logo">☕ Classic Coffee</div>
                    <div class="struk-sub">Struk Pembayaran</div>
                </div>

                <div class="struk-antrian">
                    <div class="struk-antrian-label">Nomor Antrian</div>
                    <div class="struk-antrian-number" id="strukAntrian">—</div>
                </div>

                <div class="struk-info">
                    <div class="struk-row">
                        <span class="lbl">Pelanggan</span>
                        <span class="val" id="strukNama">—</span>
                    </div>
                    <div class="struk-row">
                        <span class="lbl">Waktu</span>
                        <span class="val" id="strukWaktu">—</span>
                    </div>
                    <div class="struk-row">
                        <span class="lbl">No. Order</span>
                        <span class="val" id="strukNomor">—</span>
                    </div>
                </div>

                <div class="struk-items" id="strukItems">
                    {{-- Diisi JS --}}
                </div>

                <div class="struk-total">
                    <div class="struk-total-label">TOTAL</div>
                    <div class="struk-total-value" id="strukTotal">Rp 0</div>
                </div>

                <div class="struk-footer">
                    Terima kasih telah berkunjung 🙏<br>
                    Classic Coffee — Selalu tersaji dengan cinta
                </div>

            </div>
        </div>

        <div class="modal-card-footer">
            <button type="button" class="btn btn-outline" onclick="tutupModal('modalStruk')">Tutup</button>
            <button type="button" class="btn btn-primary" onclick="window.print()">🖨 Cetak</button>
            <button type="button" class="btn btn-primary" onclick="tutupModal('modalStruk'); location.reload();">✓ Selesai</button>
        </div>
    </div>
</div>

{{-- Data produk dari server (JSON) --}}
<script>
    const PRODUK_DATA = Array.isArray(@json($produkList ?? [])) ? @json($produkList ?? []) : [];
</script>

@endsection

@push('scripts')
<script>
// ===============================================================
// STATE
// ===============================================================
let cart = {};

// ===============================================================
// MODAL HELPERS
// ===============================================================
function bukaModalTransaksi() {
    cart = {};
    document.getElementById('tnNamaPembeli').value = '';
    document.getElementById('tnSearchProduk').value = '';
    document.getElementById('productDropdown').classList.remove('show');
    renderCart();
    document.getElementById('modalTransaksi').classList.add('show');
}

function tutupModal(id) {
    document.getElementById(id).classList.remove('show');
}

// Tutup dropdown jika klik di luar
document.addEventListener('click', function(e) {
    const wrap = document.querySelector('.product-search-wrap');
    if (wrap && !wrap.contains(e.target)) {
        document.getElementById('productDropdown').classList.remove('show');
    }
});

// ===============================================================
// ✅ PENCARIAN PRODUK — pakai addEventListener supaya klik berfungsi
// ===============================================================
function cariProduk(query) {
    const dd     = document.getElementById('productDropdown');
    const q      = (query || '').trim().toLowerCase();
    const produk = Array.isArray(PRODUK_DATA) ? PRODUK_DATA : [];

    const filtered = q === ''
        ? produk.slice(0, 12)
        : produk.filter(p => (p.name || '').toLowerCase().includes(q)).slice(0, 12);

    dd.innerHTML = '';

    if (filtered.length === 0) {
        dd.innerHTML = '<div style="padding:14px;text-align:center;color:var(--text-muted);font-size:13px;">Produk tidak ditemukan</div>';
    } else {
        filtered.forEach(p => {
            const nama  = p.name || 'Tanpa nama';
            const harga = parseInt(p.price || 0);
            const image = p.image_url || '';

            const div = document.createElement('div');
            div.className = 'product-option';

            // Tombol + untuk tambah langsung dari dropdown
            div.innerHTML = `
                ${image
                    ? `<img src="/images/${escapeHtml(image)}" alt="${escapeHtml(nama)}">`
                    : `<div class="opt-fallback">☕</div>`
                }
                <div style="flex:1;">
                    <div class="opt-name">${escapeHtml(nama)}</div>
                    <div class="opt-price">Rp ${harga.toLocaleString('id-ID')}</div>
                </div>
                <div style="
                    width:28px;height:28px;
                    background:var(--brown-mid);
                    color:var(--gold);
                    border-radius:7px;
                    display:flex;align-items:center;justify-content:center;
                    font-size:20px;font-weight:bold;flex-shrink:0;
                    cursor:pointer;
                ">+</div>`;

            // ✅ addEventListener — tidak konflik tanda kutip
            div.addEventListener('click', () => {
                tambahKeCart(p.id, nama, harga, image);
            });

            dd.appendChild(div);
        });
    }

    dd.classList.add('show');
}

function escapeHtml(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

// ===============================================================
// ✅ KERANJANG — tombol + dan - untuk ubah qty
// ===============================================================
function tambahKeCart(id, name, price, image) {
    document.getElementById('productDropdown').classList.remove('show');
    document.getElementById('tnSearchProduk').value = '';

    if (cart[id]) {
        cart[id].qty++;
    } else {
        cart[id] = { id, name, price: parseInt(price), image, qty: 1 };
    }
    renderCart();
}

function ubahQty(id, delta) {
    if (!cart[id]) return;
    cart[id].qty += delta;
    if (cart[id].qty <= 0) delete cart[id];
    renderCart();
}

function hapusDariCart(id) {
    delete cart[id];
    renderCart();
}

function hitungTotal() {
    return Object.values(cart).reduce((s, item) => s + item.price * item.qty, 0);
}

function renderCart() {
    const list     = document.getElementById('cartList');
    const totalRow = document.getElementById('cartTotalRow');
    const totalDisp = document.getElementById('cartTotalDisplay');
    const items    = Object.values(cart);

    if (items.length === 0) {
        list.innerHTML = `<div class="empty-cart">☕ Belum ada produk dipilih. Ketik di atas untuk mencari produk.</div>`;
        totalRow.style.display = 'none';
        return;
    }

    list.innerHTML = items.map(item => {
        const subtotal = item.price * item.qty;
        const imgTag = item.image
            ? `<img src="/images/${item.image}" class="cart-item-img" alt="${item.name}">`
            : `<div class="cart-item-fallback">☕</div>`;
        return `
            <div class="cart-item">
                ${imgTag}
                <div class="cart-item-info">
                    <div class="cart-item-name">${item.name}</div>
                    <div class="cart-item-price">Rp ${item.price.toLocaleString('id-ID')} / item</div>
                </div>
                <div class="qty-control">
                    <button class="qty-btn" onclick="ubahQty(${item.id}, -1)">−</button>
                    <span class="qty-value">${item.qty}</span>
                    <button class="qty-btn" onclick="ubahQty(${item.id}, 1)">+</button>
                </div>
                <span class="cart-item-subtotal">Rp ${subtotal.toLocaleString('id-ID')}</span>
                <button class="cart-remove" onclick="hapusDariCart(${item.id})" title="Hapus">🗑</button>
            </div>`;
    }).join('');

    const total = hitungTotal();
    totalDisp.textContent = 'Rp ' + total.toLocaleString('id-ID');
    totalRow.style.display = 'flex';
}
// ===============================================================
// ✅ KIRIM KE WHATSAPP
// ===============================================================
function kirimKeWhatsapp() {
    const items = Object.values(cart);
    if (items.length === 0) {
        alert('Pilih minimal 1 produk terlebih dahulu.');
        return;
    }

    const namaPembeli = document.getElementById('tnNamaPembeli').value.trim() || 'Pelanggan';
    const waNumber = document.getElementById('tnWhatsapp').value.trim();
    
    if (!waNumber) {
        alert('Masukkan nomor WhatsApp terlebih dahulu.');
        return;
    }

    const total = hitungTotal();

    // Format nomor WhatsApp: hapus karakter non-digit
    let formattedNumber = waNumber.replace(/\D/g, '');
    // Jika dimulai dengan 0, ganti dengan 62
    if (formattedNumber.startsWith('0')) {
        formattedNumber = '62' + formattedNumber.substring(1);
    }
    // Jika belum ada kode negara, tambahkan 62
    if (!formattedNumber.startsWith('62')) {
        formattedNumber = '62' + formattedNumber;
    }

    // Buat pesan WhatsApp
    let pesanwa = `*Pesanan dari Kopi Toko*\n\n`;
    pesanwa += `*Nama:* ${namaPembeli}\n`;
    pesanwa += `*Waktu:* ${new Date().toLocaleString('id-ID')}\n\n`;
    pesanwa += `*Detail Pesanan:*\n`;
    pesanwa += `${'='.repeat(35)}\n`;

    items.forEach(item => {
        const subtotal = item.price * item.qty;
        pesanwa += `${item.name}\n`;
        pesanwa += `  ${item.qty}x Rp ${item.price.toLocaleString('id-ID')} = Rp ${subtotal.toLocaleString('id-ID')}\n`;
    });

    pesanwa += `${'='.repeat(35)}\n`;
    pesanwa += `*TOTAL: Rp ${total.toLocaleString('id-ID')}*\n\n`;
    pesanwa += `Terima kasih telah memesan! ☕`;

    // Encode untuk URL
    const encodedPesan = encodeURIComponent(pesanwa);

    // Buka WhatsApp
    const waUrl = `https://wa.me/${formattedNumber}?text=${encodedPesan}`;
    window.open(waUrl, '_blank');
}

// ===============================================================
// PROSES TRANSAKSI → KIRIM KE SERVER → TAMPILKAN STRUK
// ===============================================================
function prosesTransaksi() {
    const items = Object.values(cart);
    if (items.length === 0) {
        alert('Pilih minimal 1 produk terlebih dahulu.');
        return;
    }

    const namaPembeli = document.getElementById('tnNamaPembeli').value.trim() || 'Umum';
    const waNumber    = document.getElementById('tnWhatsapp').value.trim(); // ✅ AMBIL NOMOR WA
    const total       = hitungTotal();

    const payload = {
        customer_name    : namaPembeli,
        whatsapp_number  : waNumber,   // ✅ KIRIM NOMOR WA KE SERVER
        total_price      : total,
        items         : items.map(i => ({
            product_id   : i.id,
            product_name : i.name,
            product_image: i.image,
            price        : i.price,
            quantity     : i.qty,
            subtotal     : i.price * i.qty,
        })),
        _token: document.querySelector('meta[name="csrf-token"]')?.content
                || '{{ csrf_token() }}',
    };

    const btn = document.querySelector('#modalTransaksi .btn-primary:last-child');
    btn.disabled    = true;
    btn.textContent = 'Menyimpan...';

    fetch('{{ route("kasir.transaksi.store") }}', {
        method : 'POST',
        headers: {
            'Content-Type'    : 'application/json',
            'X-CSRF-TOKEN'    : payload._token,
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify(payload),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            tutupModal('modalTransaksi');
            tampilkanStruk(data.order, items, total);
        } else {
            alert('Gagal menyimpan: ' + (data.message ?? 'Terjadi kesalahan.'));
        }
    })
    .catch(err => {
        alert('Terjadi kesalahan jaringan. Coba lagi.');
        console.error(err);
    })
    .finally(() => {
        btn.disabled    = false;
        btn.textContent = '🧾 Cetak Struk & Simpan';
    });
}

// ===============================================================
// TAMPILKAN STRUK
// ===============================================================
function tampilkanStruk(order, items, total) {
    document.getElementById('strukAntrian').textContent = order.antrian ?? '—';
    document.getElementById('strukNama').textContent    = order.customer_name ?? 'Umum';
    document.getElementById('strukWaktu').textContent   = order.created_at ?? new Date().toLocaleString('id-ID');
    document.getElementById('strukNomor').textContent   = order.number ?? '-';
    document.getElementById('strukTotal').textContent   = 'Rp ' + parseInt(total).toLocaleString('id-ID');

    const itemsHtml = items.map(item => `
        <div class="struk-item-row">
            <span class="struk-item-name">${item.name}</span>
            <span class="struk-item-qty">${item.qty}×</span>
            <span class="struk-item-price">Rp ${(item.price * item.qty).toLocaleString('id-ID')}</span>
        </div>
    `).join('');

    document.getElementById('strukItems').innerHTML = itemsHtml;
    document.getElementById('modalStruk').classList.add('show');
}
</script>
@endpush