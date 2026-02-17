@include('header')
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.0/jquery.min.js"></script>

<style>
    .autocomplete-dropdown {
        position: absolute;
        z-index: 9999;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        max-height: 220px;
        overflow-y: auto;
        width: 100%;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        top: 100%;
        left: 0;
    }
    .autocomplete-item {
        padding: 10px 16px;
        cursor: pointer;
        font-size: 14px;
        border-bottom: 1px solid #f3f4f6;
    }
    .autocomplete-item:hover { background-color: #fef3c7; }
    .autocomplete-item .label { font-weight: 600; color: #1c1917; }
    .search-wrapper { position: relative; }
    .kurir-card {
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 12px 16px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .kurir-card:hover { border-color: #f59e0b; background: #fffbeb; }
    .kurir-card.selected { border-color: #f59e0b; background: #fffbeb; }
</style>

<div class="container mx-auto py-12 px-4">
    <h2 class="text-3xl font-bold mb-8 text-center">Checkout</h2>

    <div class="max-w-2xl mx-auto space-y-6">

        {{-- FORM DETAIL PEMESAN --}}
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-stone-100">
            <h3 class="text-lg font-bold text-stone-800 mb-4">👤 Detail Pemesan</h3>
            <form id="checkout-form" action="{{ route('payment.process') }}" method="POST">
                @csrf

                {{-- Nama (readonly dari user login) --}}
                <div class="mb-4">
                    <label class="block text-sm font-bold text-stone-700 mb-2">Nama Lengkap</label>
                    <div class="relative">
                        <input
                            type="text"
                            name="customer_name"
                            value="{{ auth()->user()->name }}"
                            readonly
                            class="w-full p-4 border rounded-xl bg-gray-100 text-stone-600 outline-none cursor-not-allowed"
                        >
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs text-gray-400 bg-gray-200 px-2 py-1 rounded-full">
                            🔒 Otomatis
                        </span>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Nama diambil dari akun lo yang sedang login</p>
                </div>

                {{-- No HP --}}
                <div class="mb-4">
                    <label class="block text-sm font-bold text-stone-700 mb-2">No. HP / WhatsApp</label>
                    <div class="flex">
                        <span class="flex items-center px-4 bg-gray-100 border border-r-0 rounded-l-xl text-stone-600 font-bold text-sm">+62</span>
                        <input
                            type="tel"
                            name="phone"
                            id="phone"
                            placeholder="812xxxxxxxx"
                            maxlength="13"
                            class="w-full p-4 border rounded-r-xl bg-gray-50 outline-none focus:border-amber-400"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            required
                        >
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Masukkan angka saja, tanpa awalan 0 atau +62</p>
                </div>

                {{-- Metode Pesanan --}}
                <div class="mb-2">
                    <label class="block text-sm font-bold text-stone-700 mb-2">Metode Pesanan</label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="border-2 border-stone-200 rounded-xl p-3 flex items-center justify-center cursor-pointer has-[:checked]:border-amber-500 has-[:checked]:bg-amber-50">
                            <input type="radio" name="order_type" value="dine-in" class="mr-2" checked onchange="toggleMetode('dine-in')">
                            <span class="font-bold text-stone-700">🍽️ Makan di Sini</span>
                        </label>
                        <label class="border-2 border-stone-200 rounded-xl p-3 flex items-center justify-center cursor-pointer has-[:checked]:border-amber-500 has-[:checked]:bg-amber-50">
                            <input type="radio" name="order_type" value="take-away" class="mr-2" onchange="toggleMetode('take-away')">
                            <span class="font-bold text-stone-700">🛵 Bawa Pulang</span>
                        </label>
                    </div>
                </div>

                {{-- FORM PENGIRIMAN (muncul kalau take-away) --}}
                <div id="pengiriman-container" class="hidden space-y-4 mt-4 pt-4 border-t border-stone-100">

                    {{-- Search Destinasi --}}
                    <div>
                        <label class="block text-sm font-bold mb-2">📍 Kota / Kecamatan Tujuan</label>
                        <div class="search-wrapper">
                            <input
                                type="text"
                                id="destination_search"
                                placeholder="Ketik nama kota atau kecamatan..."
                                class="w-full p-4 border rounded-xl bg-gray-50 outline-none focus:border-amber-400"
                                autocomplete="off"
                            >
                            <div id="destination_dropdown" class="autocomplete-dropdown hidden"></div>
                        </div>
                        <input type="hidden" name="destination_id" id="destination_id">
                        <p id="destination_selected" class="mt-2 text-sm text-amber-600 font-semibold hidden"></p>
                    </div>

                    {{-- Pilih Layanan Kurir (muncul setelah destinasi dipilih) --}}
                    <div id="kurir-container" class="hidden">
                        <label class="block text-sm font-bold mb-2">🚚 Pilih Layanan Pengiriman</label>
                        <div id="list-kurir" class="space-y-2">
                            <p class="text-gray-400 text-sm">Pilih destinasi dulu untuk melihat layanan kurir...</p>
                        </div>
                        <input type="hidden" name="ongkir" id="selected_ongkir" value="0">
                        <input type="hidden" name="courier_service" id="selected_service" value="">
                        <input type="hidden" name="courier_name" id="selected_courier_name" value="">
                    </div>

                    {{-- Alamat Lengkap --}}
                    <div>
                        <label class="block text-sm font-bold mb-2">🏠 Alamat Lengkap</label>
                        <textarea name="address" rows="3"
                            class="w-full p-4 border rounded-xl bg-gray-50 outline-none focus:border-amber-400"
                            placeholder="Contoh: Jl. Merdeka No. 10, RT 01/02, Kelurahan ..."></textarea>
                    </div>

                </div>

                {{-- RINGKASAN PESANAN --}}
                <div class="mt-6 pt-4 border-t border-stone-100">
                    <h3 class="text-lg font-bold text-stone-800 mb-4">🧾 Ringkasan Pesanan</h3>

                    {{-- List Produk dari Cart --}}
                    <div class="space-y-3 mb-4">
                        @php
                            $cart = session()->get('cart', []);
                            $subtotal = 0;
                        @endphp

                        @forelse ($cart as $id => $item)
                            @php
                                $subtotal += $item['price'] * $item['quantity'];
                            @endphp
                            <div class="flex items-center justify-between py-2">
                                <div class="flex items-center gap-3">
                                    @if(isset($item['image']) && $item['image'])
                                       {{-- ✅ BENAR --}}
                                    <img src="{{ asset('images/'.$item['image']) }}" class="w-12 h-12 rounded-xl object-cover" alt="{{ $item['name'] }}">
                                    @else
                                        <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center text-xl">☕</div>
                                    @endif
                                    <div>
                                        <p class="font-bold text-stone-800 text-sm">{{ $item['name'] }}</p>
                                        <p class="text-xs text-gray-500">{{ $item['quantity'] }} x Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                                    </div>
                                </div>
                                <p class="font-bold text-stone-800">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</p>
                            </div>
                        @empty
                            <p class="text-gray-400 text-sm text-center py-4">Keranjang kosong</p>
                        @endforelse
                    </div>

                    {{-- Garis pemisah --}}
                    <div class="border-t border-dashed border-stone-200 my-3"></div>

                    {{-- Subtotal Produk --}}
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-stone-600">Subtotal Produk</span>
                        <span class="font-bold text-stone-800" id="subtotal-display">
                            Rp {{ number_format($subtotal, 0, ',', '.') }}
                        </span>
                    </div>

                    {{-- Ongkir --}}
                    <div class="flex justify-between items-center mb-2" id="ongkir-row">
                        <span class="text-stone-600">Ongkos Kirim</span>
                        <span class="font-bold text-stone-800" id="ongkir-display">—</span>
                    </div>

                    {{-- Garis pemisah --}}
                    <div class="border-t border-stone-200 my-3"></div>

                    {{-- Total --}}
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-bold text-stone-800">Total</span>
                        <span class="text-xl font-bold text-amber-600" id="total-display">
                            Rp {{ number_format($subtotal, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                {{-- Hidden inputs --}}
                <input type="hidden" name="subtotal" value="{{ $subtotal }}">
                <input type="hidden" name="total" id="input-total" value="{{ $subtotal }}">

                {{-- Tombol Bayar --}}
                <button type="submit" id="btn-bayar"
                    class="w-full bg-stone-900 text-white py-4 rounded-2xl font-bold mt-6 hover:bg-amber-600 transition text-lg">
                    💳 LANJUT KE PEMBAYARAN
                </button>

            </form>
        </div>

    </div>
</div>

<script>
    const subtotal = {{ $subtotal }};
    let ongkirValue = 0;

    function toggleMetode(type) {
        if (type === 'take-away') {
            $('#pengiriman-container').removeClass('hidden');
            $('#ongkir-row').show();
        } else {
            $('#pengiriman-container').addClass('hidden');
            ongkirValue = 0;
            updateTotal();
            $('#ongkir-display').text('—');
        }
    }

    function updateTotal() {
        let total = subtotal + ongkirValue;
        $('#ongkir-display').text('Rp ' + ongkirValue.toLocaleString('id-ID'));
        $('#total-display').text('Rp ' + total.toLocaleString('id-ID'));
        $('#input-total').val(total);
        $('#selected_ongkir').val(ongkirValue);
    }

    function pilihKurir(price, service, courierName) {
        ongkirValue = price;
        $('#selected_service').val(service);
        $('#selected_courier_name').val(courierName);
        updateTotal();

        // Highlight kurir terpilih
        $('.kurir-card').removeClass('selected');
        $('#kurir-' + service).addClass('selected');
    }

    function renderKurir(data) {
        $('#list-kurir').html('');

        if (!data || data.length === 0) {
            $('#list-kurir').html('<p class="text-red-400 text-sm">Layanan tidak tersedia untuk rute ini.</p>');
            return;
        }

        $.each(data, function (i, item) {
            let svc       = item.service ?? item.code ?? '';
            let desc      = item.description ?? '';
            let courierName = item.name ?? '';
            let price     = item.cost ?? 0;
            let etd       = item.etd ?? '-';

            $('#list-kurir').append(
                '<div class="kurir-card" id="kurir-' + svc + '" onclick="pilihKurir(' + price + ', \'' + svc + '\', \'' + courierName + '\')">' +
                    '<div class="flex justify-between items-center">' +
                        '<div>' +
                            '<span class="font-bold text-stone-800">' + svc + '</span>' +
                            '<span class="text-sm text-gray-500 ml-2">(' + desc + ')</span>' +
                            '<p class="text-xs text-gray-400 mt-1">⏱️ Estimasi ' + etd + '</p>' +
                        '</div>' +
                        '<span class="font-bold text-amber-600 text-lg">Rp ' + parseInt(price).toLocaleString('id-ID') + '</span>' +
                    '</div>' +
                '</div>'
            );
        });

        $('#kurir-container').removeClass('hidden');
    }

    function loadOngkir(destinationId) {
        let courier = 'jne:jnt:sicepat:anteraja:pos:tiki';

        $('#list-kurir').html('<p class="text-gray-400 text-sm">⏳ Memuat layanan kurir...</p>');
        $('#kurir-container').removeClass('hidden');

        // Load semua kurir populer sekaligus
        let couriers = ['jne', 'jnt', 'sicepat'];
        let allData  = [];
        let done     = 0;

        $.each(couriers, function(i, c) {
            $.ajax({
                url: '/api/ongkir',
                type: 'POST',
                data: {
                    _token:      '{{ csrf_token() }}',
                    origin:      '{{ env("RAJAONGKIR_ORIGIN_ID", "43942") }}',
                    destination: destinationId,
                    weight:      1000,
                    courier:     c
                },
                dataType: 'json',
                success: function (data) {
                    if (data && data.length > 0) {
                        allData = allData.concat(data);
                    }
                },
                complete: function () {
                    done++;
                    if (done === couriers.length) {
                        renderKurir(allData);
                    }
                }
            });
        });
    }

    $(document).ready(function () {
        let searchTimeout = null;

       // Autocomplete search destinasi
        $('#destination_search').on('input', function () {
            let keyword = $(this).val().trim();

            if (keyword.length < 2) {
                $('#destination_dropdown').addClass('hidden').html('');
                $('#destination_id').val('');
                $('#destination_selected').addClass('hidden').text('');
                $('#kurir-container').addClass('hidden'); // ✅ sudah ada
                
                // ✅ TAMBAHKAN INI — reset ongkir kalau user ganti destinasi
                $('#list-kurir').html('<p class="text-gray-400 text-sm">Pilih destinasi dulu untuk melihat layanan kurir...</p>');
                ongkirValue = 0;
                updateTotal();
                
                return;
            }

            // ✅ TAMBAHKAN INI JUGA — reset saat mulai ngetik lagi
            $('#kurir-container').addClass('hidden');
            $('#destination_id').val('');
            $('#destination_selected').addClass('hidden');
            ongkirValue = 0;
            updateTotal();

            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function () {
                $('#destination_dropdown')
                    .removeClass('hidden')
                    .html('<div class="autocomplete-item text-gray-400">Mencari...</div>');

                $.ajax({
                    url: '/api/search-destination',
                    type: 'GET',
                    data: { search: keyword },
                    dataType: 'json',
                    success: function (data) {
                        $('#destination_dropdown').html('');
                        if (data && data.length > 0) {
                            $.each(data, function (i, item) {
                                let id    = item.id;
                                let label = item.label ?? '';
                                $('#destination_dropdown').append(
                                    '<div class="autocomplete-item" data-id="' + id + '" data-label="' + label + '">' +
                                        '<div class="label">' + label + '</div>' +
                                    '</div>'
                                );
                            });
                        } else {
                            $('#destination_dropdown').html(
                                '<div class="autocomplete-item text-gray-400">Lokasi tidak ditemukan</div>'
                            );
                        }
                    },
                    error: function () {
                        $('#destination_dropdown').html(
                            '<div class="autocomplete-item text-red-400">Gagal mencari lokasi</div>'
                        );
                    }
                });
            }, 400);
        });

        // Pilih dari dropdown → otomatis load ongkir
        $(document).on('click', '.autocomplete-item[data-id]', function () {
            let id    = $(this).data('id');
            let label = $(this).data('label');

            $('#destination_id').val(id);
            $('#destination_search').val(label);
            $('#destination_dropdown').addClass('hidden').html('');
            $('#destination_selected').removeClass('hidden').text('✅ Terpilih: ' + label);

            // Reset ongkir & total
            ongkirValue = 0;
            updateTotal();
            $('#ongkir-display').text('Pilih layanan kurir...');

            // Load ongkir otomatis
            loadOngkir(id);
        });

        // Tutup dropdown klik di luar
        $(document).on('click', function (e) {
            if (!$(e.target).closest('.search-wrapper').length) {
                $('#destination_dropdown').addClass('hidden');
            }
        });

        // Validasi form sebelum submit
        $('#checkout-form').on('submit', function (e) {
            let phone       = $('#phone').val();
            let orderType   = $('input[name="order_type"]:checked').val();
            let destId      = $('#destination_id').val();
            let ongkirVal   = $('#selected_ongkir').val();

            if (!phone || phone.length < 8) {
                e.preventDefault();
                alert('Masukkan nomor HP yang valid!');
                $('#phone').focus();
                return false;
            }

            if (orderType === 'take-away') {
                if (!destId) {
                    e.preventDefault();
                    alert('Pilih kota tujuan dulu!');
                    return false;
                }
                if (!ongkirVal || ongkirVal == '0') {
                    e.preventDefault();
                    alert('Pilih layanan kurir dulu!');
                    return false;
                }
            }
        });
    });
</script>