@include('header')

<div class="container mx-auto py-24 text-center">
    <div class="max-w-md mx-auto bg-white p-12 rounded-[3rem] shadow-2xl border border-stone-100">
        <div class="w-20 h-20 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto mb-6 text-3xl">
            <i class="fas fa-wallet"></i>
        </div>
        <h2 class="text-3xl font-bold text-stone-900 mb-2">Satu Langkah Lagi!</h2>
        <p class="text-stone-500 mb-8">Klik tombol di bawah untuk membayar pesanan kopi Anda menggunakan QRIS atau transfer bank.</p>

        <button id="pay-button" class="w-full bg-amber-600 text-white py-5 rounded-2xl font-bold text-lg hover:bg-amber-700 transition shadow-lg shadow-amber-200">
            BAYAR SEKARANG
        </button>
        
        <p class="mt-6 text-xs text-stone-400">Terproteksi oleh Midtrans Secure Payment</p>
    </div>
</div>

<script type="text/javascript"
        src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('services.midtrans.clientKey') }}"></script>

<script type="text/javascript">
    const payButton = document.getElementById('pay-button');
    payButton.addEventListener('click', function () {
        // Trigger Pop-up Midtrans
        window.snap.pay('{{ $snapToken }}', {
            onSuccess: function(result){
                alert("Pembayaran Berhasil, Kopi segera diproses!");
                console.log(result);
                window.location.href = '/produk'; // Ganti ke dashboard user nanti
            },
            onPending: function(result){
                alert("Menunggu pembayaran Anda...");
            },
            onError: function(result){
                alert("Waduh, pembayaran gagal! Silakan coba lagi.");
            },
            onClose: function(){
                alert('Anda menutup jendela pembayaran sebelum selesai.');
            }
        });
    });
</script>