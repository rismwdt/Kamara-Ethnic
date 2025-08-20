{{-- Modal Jadwal --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<x-modal name="modal-jadwal" focusable>
    <div class="p-6 relative">
        <div class="flex justify-between items-center mb-2">
            <h2 class="text-md font-semibold text-gray-800 dark:text-gray-100">Masukkan Jadwal Acara</h2>
            <button @click="$dispatch('close-modal', 'modal-jadwal')" class="text-gray-500 hover:text-red-600 text-3xl leading-none">&times;</button>
        </div>
        <p class="mb-4 text-sm text-gray-600">
            Pemesanan disarankan minimal <span class="font-semibold">H-3</span> sebelum tanggal acara.
        </p>

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
                <textarea id="location_detail" name="location_detail" rows="3" class="w-full border px-3 py-2 rounded-md text-sm resize-none focus:ring-1 focus:ring-primary" placeholder="Ketik alamat atau pilih di peta" required></textarea>

                <ul id="suggestions" style="position:absolute; background:white; list-style:none; padding:5px; margin:0; border:1px solid #ccc; width:100%; max-height:150px; overflow-y:auto; z-index:999;"></ul>

                <input type="hidden" id="latitude" name="latitude" />
                <input type="hidden" id="longitude" name="longitude" />

                <div id="map" style="height: 300px; margin-top: 10px;"></div>
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
            <button @click="$dispatch('close-modal', 'modal-jadwal-bentrok')" class="text-gray-500 hover:text-red-600 text-3xl leading-none">&times;</button>
        </div>
        <p class="text-gray-700 mb-4">
            Mohon maaf, tanggal dan waktu yang Anda pilih sudah tidak tersedia atau penuh.<br>
            Silakan pilih hari/tanggal/waktu lain yang tersedia, minimal 3 hari dari hari ini.
        </p>
        <p id="jadwal-bentrok-reason" class="text-sm text-red-600 mt-2"></p>
        <div class="text-right">
            <a href="https://wa.me/6283149299817" target="_blank" class="inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary/90 focus:outline-none focus:ring focus:ring-primary/50 active:bg-primary/80 transition">
                Konsultasi Jadwal
            </a>
        </div>
    </div>
</x-modal>

{{-- Modal Pesanan --}}
<x-modal name="modal-pesanan" focusable>
    <div class="p-6 relative" x-data="{
        type: 'pernikahan',
        types: [
            {value: 'pernikahan', label: 'Pernikahan'},
            {value: 'khitan', label: 'Khitan'},
            {value: 'gathering', label: 'Gathering'},
            {value: 'grand_opening', label: 'Grand Opening'},
            {value: 'lainnya', label: 'Lainnya'},
        ]
    }">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-md font-semibold text-gray-800 dark:text-gray-100">Konfirmasi Pemesanan Acara</h2>
            <button @click="$dispatch('close-modal', 'modal-pesanan')" class="text-gray-500 hover:text-red-600 text-3xl leading-none">&times;</button>
        </div>

        {{-- ERROR VALIDASI --}}
        @if ($errors->any())
            <div class="mb-3 rounded-md border border-red-300 bg-red-50 p-3 text-sm text-red-700">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- TAB JENIS ACARA --}}
        <div class="mb-4">
            <div class="flex flex-wrap gap-2">
                <template x-for="opt in types" :key="opt.value">
                    <button type="button"
                            class="px-3 py-1.5 rounded-full text-xs border"
                            :class="type === opt.value ? 'bg-gray-900 text-primary border-gray-900' : 'bg-white hover:bg-gray-50 border-gray-300'"
                            x-text="opt.label"
                            @click="type = opt.value">
                    </button>
                </template>
            </div>
        </div>

        <form id="formPesanan" action="{{ route('booking.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="event_id" id="event_id_pesanan">
            <input type="hidden" name="latitude" id="latitude_pesanan">
            <input type="hidden" name="longitude" id="longitude_pesanan">
            <input type="hidden" id="event_price" name="price">
            <input type="hidden" name="dp">

            {{-- kirim jenis acara yang dipilih (benar-benar dinamis) --}}
            <input type="hidden" name="event_type" x-model="type">

            {{-- ========== BIODATA ========== --}}
            {{-- PERNIKAHAN --}}
            <div class="mb-3" x-show="type==='pernikahan'" x-cloak>
                <x-text-input name="client_name"
                    placeholder="Nama Pengantin (nama & nama)" class="w-full"
                    x-bind:required="type==='pernikahan'"
                    x-bind:disabled="type!=='pernikahan'" />
            </div>
            <div class="mb-3 grid grid-cols-1 gap-2 md:grid-cols-2" x-show="type==='pernikahan'" x-cloak>
                <x-text-input name="female_parents" placeholder="Nama Orang Tua Wanita"
                    x-bind:required="type==='pernikahan'"
                    x-bind:disabled="type!=='pernikahan'" />
                <x-text-input name="male_parents" placeholder="Nama Orang Tua Pria"
                    x-bind:required="type==='pernikahan'"
                    x-bind:disabled="type!=='pernikahan'" />
            </div>

            {{-- KHITAN --}}
            <div class="mb-3" x-show="type==='khitan'" x-cloak>
                <x-text-input name="client_name" placeholder="Nama Penanggung Jawab" class="w-full"
                    x-bind:required="type==='khitan'"
                    x-bind:disabled="type!=='khitan'" />
            </div>
            <div class="mb-3" x-show="type==='khitan'" x-cloak>
                <x-text-input name="event_name" placeholder="Nama Anak" class="w-full"
                    x-bind:required="type==='khitan'"
                    x-bind:disabled="type!=='khitan'" />
            </div>

            {{-- GATHERING / GRAND OPENING --}}
            <div class="mb-3" x-show="type==='gathering' || type==='grand_opening'" x-cloak>
                <x-text-input name="client_name" placeholder="Nama Penanggung Jawab" class="w-full"
                    x-bind:required="type==='gathering' || type==='grand_opening'"
                    x-bind:disabled="!(type==='gathering' || type==='grand_opening')" />
            </div>
            <div class="mb-3" x-show="type==='gathering' || type==='grand_opening'" x-cloak>
                <x-text-input name="event_name" placeholder="Nama Acara" class="w-full"
                    x-bind:required="type==='gathering' || type==='grand_opening'"
                    x-bind:disabled="!(type==='gathering' || type==='grand_opening')" />
            </div>

            {{-- LAINNYA --}}
            <div class="mb-3" x-show="type==='lainnya'" x-cloak>
                <x-text-input name="client_name" placeholder="Nama Penanggung Jawab" class="w-full"
                    x-bind:required="type==='lainnya'"
                    x-bind:disabled="type!=='lainnya'" />
            </div>
            <div class="mb-3" x-show="type==='lainnya'" x-cloak>
                <x-text-input name="event_name" placeholder="Nama Acara" class="w-full"
                    x-bind:required="type==='lainnya'"
                    x-bind:disabled="type!=='lainnya'" />
            </div>
            <div class="mb-3" x-show="type==='lainnya'" x-cloak>
                <textarea name="description" rows="2" placeholder="Deskripsi singkat acara (tujuan/konsep ringkas)"
                    class="w-full border px-3 py-2 rounded-md text-sm resize-none"
                    x-bind:required="type==='lainnya'"
                    x-bind:disabled="type!=='lainnya'"></textarea>
            </div>
            {{-- ========== /BIODATA ========== --}}

            {{-- Kontak --}}
            <div class="mb-3 grid grid-cols-1 gap-2 md:grid-cols-2">
                <x-text-input name="phone" placeholder="No HP" required />
                <x-text-input name="email" type="email" placeholder="Email" value="{{ Auth::check() ? Auth::user()->email : '' }}" required />
            </div>

            {{-- Waktu --}}
            <div class="mb-3 grid grid-cols-1 gap-2 md:grid-cols-3">
                <x-text-input id="date_pesanan" name="date" type="date" class="w-full" readonly required />
                <x-text-input id="start_time_pesanan" name="start_time" type="time" class="w-full" readonly required />
                <x-text-input id="end_time_pesanan" name="end_time" type="time" class="w-full" readonly required />
            </div>

            <div class="mb-3">
                <x-text-input name="nuance" placeholder="Nuansa/Tema Acara" class="w-full" required />
            </div>

            {{-- Lokasi --}}
            <div class="mb-3">
                <textarea id="location_detail_pesanan" name="location_detail" rows="2" placeholder="Alamat Lengkap Acara" class="w-full border px-3 py-2 rounded-md text-sm resize-none" required></textarea>
            </div>
            <div class="mb-3">
                <label class="block text-sm mb-1 font-medium">Upload Foto Lokasi Acara (Opsional)</label>
                <input type="file" name="location_photo" class="w-full text-sm border border-gray-300 rounded-md file:bg-primary file:text-white file:px-4 file:py-1 file:rounded-md file:border-none file:cursor-pointer">
            </div>

            {{-- DP --}}
            <div class="mb-3">
                <label class="block text-sm mb-1 font-medium">Upload Bukti Transfer DP</label>
                <input type="file" name="image" class="w-full text-sm border border-gray-300 rounded-md file:bg-primary file:text-white file:px-4 file:py-1 file:rounded-md file:border-none file:cursor-pointer" required>
                <label class="block text-sm mt-2 mb-1 font-medium">
                    DP yang harus dibayarkan:
                    <span id="dp_amount" class="font-semibold">Rp 0</span><br>
                    NoRek BCA: <span class="font-semibold">7751463093</span> a/n Fitri Fitria
                </label>
            </div>

            {{-- Catatan --}}
            <div class="mb-4">
                <textarea name="notes" rows="2" placeholder="Catatan" class="w-full border px-3 py-2 rounded-md text-sm resize-none"></textarea>
            </div>

            <div class="text-right">
                <x-primary-button type="submit">Konfirmasi Pesanan</x-primary-button>
            </div>
        </form>
    </div>
</x-modal>

<script>
document.getElementById('formPesanan')?.addEventListener('submit', (e) => {
  const fd = new FormData(e.target);
  console.log('[formPesanan submit]', Object.fromEntries(fd.entries()));
});
</script>

{{-- Auto-buka modal pesanan jika ada error validasi --}}
@if ($errors->any())
<script>
  window.dispatchEvent(new CustomEvent('open-modal', { detail: 'modal-pesanan' }));
</script>
@endif

{{-- Modal Pesanan Berhasil --}}
@if(session('pesanan_berhasil'))
<x-modal name="modal-berhasil" focusable>
    <div class="p-6 relative">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-green-600 mb-4 dark:text-gray-100">Pesanan Berhasil!</h2>
            <button @click="$dispatch('close-modal', 'modal-berhasil')" class="text-gray-500 hover:text-red-600 text-3xl leading-none">&times;</button>
        </div>
        <p class="text-gray-700 dark:text-gray-300 mb-4">
            Terima kasih, pesanan Anda telah kami terima dan akan segera kami proses dalam waktu 1x24 jam.<br>
            Jika ada pertanyaan, silakan hubungi kami melalui WhatsApp.
        </p>
        <div class="flex justify-end">
            <a href="https://wa.me/6283149299817" target="_blank" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50">
                Hubungi via WhatsApp
            </a>
        </div>
    </div>
</x-modal>

<script>
  window.addEventListener('DOMContentLoaded', () => {
    window.dispatchEvent(new CustomEvent('open-modal', { detail: 'modal-berhasil' }));
  });
</script>
@endif
