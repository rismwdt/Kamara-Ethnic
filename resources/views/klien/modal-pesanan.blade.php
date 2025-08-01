{{-- Modal Jadwal --}}
<x-modal name="modal-jadwal" focusable>
    <div class="p-6 relative">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-md font-semibold text-gray-800 dark:text-gray-100">Masukkan Jadwal Acara</h2>
            <button @click="$dispatch('close-modal', 'modal-jadwal')"
                class="text-gray-500 hover:text-red-600 text-3xl leading-none">
                &times;
            </button>
        </div>
        <form id="formJadwal" action="{{ route('cek-jadwal') }}" method="POST">
            @csrf
            <input type="hidden" name="event_id" id="event_id_input">
            <div class="flex flex-col lg:flex-row gap-4 mb-4">
                <div class="w-full lg:w-1/3">
                    <x-input-label for="tanggal" value="Tanggal" />
                    <x-text-input id="tanggal" name="date" type="date" class="mt-1 block w-full" required />
                </div>
                <div class="w-full lg:w-1/3">
                    <x-input-label for="mulai" value="Jam Mulai" />
                    <x-text-input id="mulai" name="start_time" type="time" class="mt-1 block w-full" required />
                </div>
                <div class="w-full lg:w-1/3">
                    <x-input-label for="selesai" value="Jam Selesai" />
                    <x-text-input id="selesai" name="end_time" type="time" class="mt-1 block w-full" required />
                </div>
            </div>
            <div class="mb-4">
                <x-input-label for="alamat" value="Alamat Acara" />
                <textarea id="alamat" name="location_detail" rows="3"
                    class="w-full border px-3 py-2 rounded-md text-sm resize-none focus:ring-1 focus:ring-primary"
                    placeholder="Alamat Lengkap Acara" required></textarea>
            </div>
            <div class="mt-6 text-right">
                <x-primary-button type="button" id="cek-jadwal-button">Cek Jadwal</x-primary-button>
            </div>
        </form>
    </div>
</x-modal>

{{-- Modal Jadwal Bentrok --}}
<x-modal name="modal-jadwal-bentrok" focusable>
    <div class="p-6 relative">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-md font-semibold text-gray-800 dark:text-gray-100">Jadwal Tidak Tersedia</h2>
            <button @click="$dispatch('close-modal', 'modal-jadwal-bentrok')"
                class="text-gray-500 hover:text-red-600 text-3xl leading-none">
                &times;
            </button>
        </div>
        <p class="text-gray-700 mb-4">
            Mohon maaf, tanggal dan waktu yang Anda pilih sudah tidak tersedia atau penuh.<br>
            Silakan pilih hari/tanggal/waktu lain yang tersedia, minimal 2 hari dari hari ini.
        </p>
        <p id="jadwal-bentrok-reason" class="text-sm text-red-600 mt-2"></p>
        <div class="text-right">
            <a href="https://wa.me/6283149299817" target="_blank"
    class="inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary/90 focus:outline-none focus:ring focus:ring-primary/50 active:bg-primary/80 transition">
    Konsultasi Jadwal
</a>
        </div>
    </div>
</x-modal>

{{-- Modal Pesanan --}}
<x-modal name="modal-pesanan" focusable>
    <div class="p-6 relative">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-md font-semibold text-gray-800 dark:text-gray-100">Konfirmasi Pemesanan Acara</h2>
            <button @click="$dispatch('close-modal', 'modal-pesanan')"
                class="text-gray-500 hover:text-red-600 text-3xl leading-none">
                &times;
            </button>
        </div>
        <form id="formPesanan" action="{{ route('booking.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="event_id" id="event_id">
            <div class="mb-3">
                <x-text-input name="client_name" placeholder="Nama Pengantin (nama & nama)" class="w-full" required />
            </div>
            <div class="mb-3 grid grid-cols-1 gap-2 md:grid-cols-2">
                <x-text-input name="male_parents" placeholder="Nama Orang Tua Wanita" required />
                <x-text-input name="female_parents" placeholder="Nama Orang Tua Pria" required />
            </div>
            <div class="mb-3 grid grid-cols-1 gap-2 md:grid-cols-2">
                <x-text-input name="phone" placeholder="No HP" required />
                <x-text-input name="email" type="email" placeholder="Email"
                    value="{{ Auth::check() ? Auth::user()->email : '' }}" required />
            </div>
            <div class="mb-3 grid grid-cols-1 gap-2 md:grid-cols-3">
                <x-text-input id="date" name="date" type="date" class="w-full" readonly required />
                <x-text-input id="start_time" name="start_time" type="time" class="w-full" readonly required />
                <x-text-input id="end_time" name="end_time" type="time" class="w-full" readonly required />
            </div>
            <div class="mb-3">
                <x-text-input name="nuance" placeholder="Nuansa/Tema Acara" class="w-full" required />
            </div>
            <div class="mb-3">
                <textarea id="location_detail" name="location_detail" rows="2" placeholder="Alamat Lengkap Acara"
                    class="w-full border px-3 py-2 rounded-md text-sm resize-none" required></textarea>
            </div>
            <div class="mb-3">
                <label class="block text-sm mb-1 font-medium">Upload Bukti Transfer DP</label>
                <input type="file" name="image"
                    class="w-full text-sm file:bg-primary file:text-white file:px-4 file:py-1 file:rounded-md file:border-none file:cursor-pointer" required>
            </div>
            <div class="mb-4">
                <textarea name="notes" rows="2" placeholder="Catatan"
                    class="w-full border px-3 py-2 rounded-md text-sm resize-none"></textarea>
            </div>
            <div class="text-right">
                <x-primary-button type="submit">
                    Konfirmasi Pesanan
                </x-primary-button>
            </div>
        </form>
    </div>
</x-modal>

{{-- Modal Pesanan Berhasil --}}
@if(session('pesanan_berhasil'))
<x-modal name="modal-berhasil" focusable>
    <div class="p-6 relative">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-green-600 mb-4 dark:text-gray-100">Pesanan Berhasil!</h2>
            {{-- <button
            @click="$dispatch('close-modal', 'modal-berhasil')"
            class="text-gray-500 hover:text-red-600 text-3xl leading-none">
            &times;
        </button> --}}
        </div>
        <p class="text-gray-700 dark:text-gray-300 mb-4">
            Terima kasih, pesananmu sudah kami terima dan akan segera kami proses.
            <!-- Pesanan Anda telah berhasil dibuat. Silakan cek email Anda untuk detail pesanan. -->
        </p>
        <div class="flex justify-end">
            <button @click="$dispatch('close-modal', 'modal-berhasil')"
                class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 focus:outline-none hover:focus:ring-2 hover:focus:ring-green-500 hover:focus:ring-opacity-50">
                Tutup
            </button>
        </div>
    </div>
</x-modal>

<script>
    window.addEventListener('DOMContentLoaded', () => {
        window.dispatchEvent(new CustomEvent('open-modal', {
            detail: 'modal-berhasil'
        }));
    });
</script>
@endif


