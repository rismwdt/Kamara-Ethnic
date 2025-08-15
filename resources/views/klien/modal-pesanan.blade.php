{{-- Modal Jadwal --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

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
                    <x-input-label for="date" value="Tanggal" />
                    <x-text-input id="date" name="date" type="date" class="mt-1 block w-full" required />
                </div>
                <div class="w-full lg:w-1/3">
                    <x-input-label for="start_time" value="Jam Mulai" />
                    <x-text-input id="start_time" name="start_time" type="time" class="mt-1 block w-full" required />
                </div>
                <div class="w-full lg:w-1/3">
                    <x-input-label for="end_time" value="Jam Selesai" />
                    <x-text-input id="end_time" name="end_time" type="time" class="mt-1 block w-full" required />
                </div>
            </div>

            <div class="mb-4" style="position: relative;">
                <x-input-label for="location_detail" value="Alamat Acara" />
                <textarea id="location_detail" name="location_detail" rows="3"
                    class="w-full border px-3 py-2 rounded-md text-sm resize-none focus:ring-1 focus:ring-primary"
                    placeholder="Ketik alamat atau pilih di peta" required></textarea>

                <!-- Dropdown suggestion -->
                <ul id="suggestions"
                    style="position:absolute; background:white; list-style:none; padding:5px; margin:0; border:1px solid #ccc; width:100%; max-height:150px; overflow-y:auto; z-index:999;">
                </ul>

                <!-- Hidden field untuk koordinat -->
                <input type="hidden" id="latitude" name="latitude" />
                <input type="hidden" id="longitude" name="longitude" />

                <!-- Peta -->
                <div id="map" style="height: 300px; margin-top: 10px;"></div>
            </div>

            {{-- <div class="flex gap-4 mb-4">
                <div class="w-1/2">
                    <x-input-label for="latitude" value="Latitude" />
                    <x-text-input id="latitude" name="latitude" type="text" class="mt-1 block w-full" required readonly />
                </div>
                <div class="w-1/2">
                    <x-input-label for="longitude" value="Longitude" />
                    <x-text-input id="longitude" name="longitude" type="text" class="mt-1 block w-full" required readonly />
                </div>
            </div> --}}

            {{-- <div class="mb-4">
                <x-input-label for="priority" value="Prioritas" />
                <select id="priority" name="priority" class="mt-1 block w-full rounded border-gray-300 shadow-sm" required>
                    <option value="0">Normal</option>
                    <option value="1">VIP</option>
                    <option value="2">Keluarga</option>
                </select>
            </div> --}}

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
            Silakan pilih hari/tanggal/waktu lain yang tersedia, minimal 3 hari dari hari ini.
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
            <input type="hidden" name="latitude">
            <input type="hidden" name="longitude">

            <input type="hidden" id="event_price">
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
                <label class="block text-sm mb-1 font-medium">Upload Foto Lokasi Acara (Opsional)</label>
                <input type="file" name="location_photo"
                    class="w-full text-sm border border-gray-300 rounded-md file:bg-primary file:text-white file:px-4 file:py-1 file:rounded-md file:border-none file:cursor-pointer">
            </div>
            <div class="mb-3">
                <label class="block text-sm mb-1 font-medium">Upload Bukti Transfer DP</label>
                <input type="file" name="image"
                    class="w-full text-sm border border-gray-300 rounded-md file:bg-primary file:text-white file:px-4 file:py-1 file:rounded-md file:border-none file:cursor-pointer" required>
                <label class="block text-sm mt-2 mb-1 font-medium">
                    DP yang harus dibayarkan:
                    <span id="dp_amount" class="font-semibold">Rp 0</span><br>
                    NoRek BCA: <span class="font-semibold">7751463093</span> a/n Fitri Fitria
                </label>
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
            <button
                @click="$dispatch('close-modal', 'modal-berhasil')"
                class="text-gray-500 hover:text-red-600 text-3xl leading-none">
                &times;
            </button>
        </div>
        <p class="text-gray-700 dark:text-gray-300 mb-4">
            Terima kasih, pesanan Anda telah kami terima dan akan segera kami proses dalam waktu 1x24 jam.<br>
            Jika ada pertanyaan, silakan hubungi kami melalui WhatsApp.
        </p>
        <div class="flex justify-end">
            <a href="https://wa.me/6283149299817" target="_blank"
                class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50">
                Hubungi via WhatsApp
            </a>
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


