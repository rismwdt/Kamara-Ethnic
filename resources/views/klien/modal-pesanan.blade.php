{{-- Modal Jadwal --}}
<x-modal name="modal-jadwal" focusable>
    <div class="p-6 relative">
        <div class="flex justify-between items-center mb-4">
        <h2 class="text-md font-semibold text-gray-800 dark:text-gray-100">Masukkan Jadwal Acara</h2>
        <button
            @click="$dispatch('close-modal', 'modal-jadwal')"
            class="text-gray-500 hover:text-red-600 text-3xl leading-none">
            &times;
        </button>
    </div>
        <form id="formJadwal" action="{{ route('cek-jadwal') }}" method="POST">
            @csrf
            <input type="hidden" name="event_id" id="event_id_input">
            <div class="flex gap-2 mb-4">
                <div class="w-1/3">
                    <x-input-label for="tanggal" value="Tanggal" />
                    <x-text-input id="tanggal" name="date" type="date" class="mt-1 block w-full" required />
                </div>
                <div class="w-1/3">
                    <x-input-label for="mulai" value="Jam Mulai" />
                    <x-text-input id="mulai" name="start_time" type="time" class="mt-1 block w-full" required />
                </div>
                <div class="w-1/3">
                    <x-input-label for="selesai" value="Jam Selesai" />
                    <x-text-input id="selesai" name="end_time" type="time" class="mt-1 block w-full" required />
                </div>
            </div>
            <div class="mb-4">
                <x-input-label for="alamat" value="Alamat Acara" />
                <textarea id="alamat" name="location" rows="3"
                    class="w-full border px-3 py-2 rounded-md text-sm resize-none focus:ring-1 focus:ring-primary"
                    placeholder="Alamat Lengkap Acara" required></textarea>
            </div>
            <div class="mt-6 text-right">
                <x-primary-button type="submit">Cek Jadwal</x-primary-button>
            </div>
        </form>
    </div>
</x-modal>

{{-- Modal Jadwal Bentrok --}}
<x-modal name="modal-jadwal-bentrok" focusable>
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold text-red-600">Jadwal Tidak Tersedia</h2>
        <button
            @click="$dispatch('close-modal', 'modal-jadwal-bentrok')"
            class="text-gray-500 hover:text-red-600 text-xl font-bold leading-none">
            &times;
        </button>
    </div>
    <p class="text-gray-700 mb-4">
        Mohon maaf, tanggal dan waktu yang Anda pilih sudah penuh.<br>
        Silakan pilih hari/tanggal/waktu lain yang tersedia, minimal 7 hari dari hari ini.
    </p>
    <div class="text-right">
        <button
            onclick="tutupModalBentrokDanBukaJadwal()"
            class="inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary/90 focus:outline-none focus:ring focus:ring-primary/50 active:bg-primary/80 transition">
            Pilih Jadwal Lain
        </button>
    </div>
</x-modal>

{{-- Modal Pesanan --}}
<x-modal name="modal-pesanan" focusable>
    <div class="w-full max-w-lg">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Konfirmasi Pemesanan Acara</h2>
            <button
                @click="$dispatch('close-modal', 'modal-pesanan')"
                class="text-gray-500 hover:text-red-600 text-xl font-bold leading-none">
                &times;
            </button>
        </div>
        <form action="{{ route('booking.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="event_id" id="event_id_pesanan">
            <div class="mb-3">
                <x-text-input name="nama_pengantin" placeholder="Nama Pengantin (nama & nama)" class="w-full" required />
            </div>
            <div class="mb-3 grid grid-cols-1 md:grid-cols-2 gap-2">
                <x-text-input name="ortu_wanita" placeholder="Nama Orang Tua Wanita" required />
                <x-text-input name="ortu_pria" placeholder="Nama Orang Tua Pria" required />
            </div>
            <div class="mb-3 grid grid-cols-1 md:grid-cols-2 gap-2">
                <x-text-input name="no_hp" placeholder="No HP" required />
                <x-text-input name="email" type="email" placeholder="Email" value="{{ Auth::check() ? Auth::user()->email : '' }}" required />
            </div>
            <div class="mb-3 grid grid-cols-1 md:grid-cols-3 gap-2">
                <x-text-input id="tanggal" name="tanggal" type="date" class="w-full" readonly required />
                <x-text-input id="mulai" name="start_time" type="time" class="w-full" readonly required />
                <x-text-input id="selesai" name="end_time" type="time" class="w-full" readonly required />
            </div>
            <div class="mb-3">
                <x-text-input name="tema" placeholder="Nuansa/Tema Acara" class="w-full" />
            </div>
            <div class="mb-3">
                <textarea name="alamat" rows="2" placeholder="Alamat Lengkap Acara" class="w-full border px-3 py-2 rounded-md text-sm resize-none" required></textarea>
            </div>
            <div class="mb-3">
                <label class="block text-sm mb-1 font-medium">Upload Bukti Transfer DP</label>
                <input type="file" name="bukti_tf" class="w-full text-sm file:bg-primary file:text-white file:px-4 file:py-1 file:rounded-md file:border-none file:cursor-pointer">
            </div>
            <div class="mb-4">
                <textarea name="catatan" rows="2" placeholder="Catatan" class="w-full border px-3 py-2 rounded-md text-sm resize-none"></textarea>
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
        <div class="max-w-md w-full bg-white p-6 rounded-lg shadow-md relative">
            {{-- Tombol Close --}}
            <button
                @click="$dispatch('close-modal', 'modal-berhasil')"
                class="absolute top-3 right-4 text-gray-500 hover:text-red-600 text-2xl font-bold leading-none">
                &times;
            </button>
            <h2 class="text-xl font-semibold text-green-600 mb-4">Pesanan Berhasil!</h2>
            <p class="text-gray-700">
                Terima kasih, pesananmu sudah kami terima dan akan segera kami proses.
            </p>
        </div>
    </x-modal>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'modal-berhasil' }));
        });
    </script>
@endif

