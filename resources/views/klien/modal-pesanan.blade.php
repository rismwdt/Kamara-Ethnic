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
            <h2 class="text-md font-semibold text-gray-800 dark:text-gray-100">Pemesanan Acara</h2>
            <button @click="$dispatch('close-modal', 'modal-pesanan')"
                class="text-gray-500 hover:text-red-600 text-3xl leading-none">&times;</button>
        </div>

        @if ($errors->any())
        <div class="mb-3 rounded-md border border-red-300 bg-red-50 p-3 text-sm text-red-700">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="mb-4">
            <div class="relative px-2">
                <span
                    class="pointer-events-none absolute left-0 top-0 h-full w-4 bg-gradient-to-r from-white to-transparent dark:from-gray-800"></span>
                <span
                    class="pointer-events-none absolute right-0 top-0 h-full w-4 bg-gradient-to-l from-white to-transparent dark:from-gray-800"></span>

                <div class="tabs-scroll flex flex-nowrap gap-2 overflow-x-auto px-1 -mx-1" x-data>
                    <template x-for="opt in types" :key="opt.value">
                        <button type="button"
                            class="shrink-0 whitespace-nowrap px-3 py-1.5 rounded-full text-xs border transition"
                            :class="type === opt.value
                    ? 'bg-white text-primary border-gray-900'
                    : 'bg-white hover:bg-gray-50 border-gray-300'" x-text="opt.label" @click="type = opt.value">
                        </button>
                    </template>
                </div>
            </div>

            <style>
                .tabs-scroll {
                    scrollbar-width: none;
                    -ms-overflow-style: none;
                }

                .tabs-scroll::-webkit-scrollbar {
                    display: none;
                }
            </style>
        </div>


        <form id="formPesanan" action="{{ route('booking.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="event_id" id="event_id_pesanan">
            <input type="hidden" id="event_price" name="price">
            <input type="hidden" name="dp">
            <input type="hidden" name="event_type" x-model="type">

            {{-- PERNIKAHAN --}}
            <div class="mb-3" x-show="type==='pernikahan'" x-cloak>
                <x-text-input name="client_name" placeholder="Nama Pengantin (nama & nama)" class="w-full"
                    x-bind:required="type==='pernikahan'" x-bind:disabled="type!=='pernikahan'" />
            </div>
            <div class="mb-3 grid grid-cols-1 gap-2 md:grid-cols-2" x-show="type==='pernikahan'" x-cloak>
                <x-text-input name="female_parents" placeholder="Nama Orang Tua Wanita"
                    x-bind:required="type==='pernikahan'" x-bind:disabled="type!=='pernikahan'" />
                <x-text-input name="male_parents" placeholder="Nama Orang Tua Pria"
                    x-bind:required="type==='pernikahan'" x-bind:disabled="type!=='pernikahan'" />
            </div>

            {{-- KHITAN --}}
            <div class="mb-3" x-show="type==='khitan'" x-cloak>
                <x-text-input name="client_name" placeholder="Nama Penanggung Jawab" class="w-full"
                    x-bind:required="type==='khitan'" x-bind:disabled="type!=='khitan'" />
            </div>
            <div class="mb-3" x-show="type==='khitan'" x-cloak>
                <x-text-input name="event_name" placeholder="Nama Anak" class="w-full" x-bind:required="type==='khitan'"
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
                    x-bind:required="type==='lainnya'" x-bind:disabled="type!=='lainnya'" />
            </div>
            <div class="mb-3" x-show="type==='lainnya'" x-cloak>
                <x-text-input name="event_name" placeholder="Nama Acara" class="w-full"
                    x-bind:required="type==='lainnya'" x-bind:disabled="type!=='lainnya'" />
            </div>
            <div class="mb-3" x-show="type==='lainnya'" x-cloak>
                <textarea name="description" rows="2" placeholder="Deskripsi singkat acara (tujuan/konsep ringkas)"
                    class="w-full border px-3 py-2 rounded-md text-sm resize-none" x-bind:required="type==='lainnya'"
                    x-bind:disabled="type!=='lainnya'"></textarea>
            </div>

            <div class="mb-3 grid grid-cols-1 gap-2 md:grid-cols-2">
                <x-text-input name="phone" placeholder="No HP" required />
                <x-text-input name="email" type="email" placeholder="Email"
                    value="{{ Auth::check() ? Auth::user()->email : '' }}" required />
            </div>

            <div class="mb-4 space-y-4">

                <div>
                    <x-input-label for="date" value="Tanggal" />
                    <x-text-input id="date" name="date" type="date" class="mt-1 block w-full" required />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="min-w-0">
                        <x-input-label for="start_time" value="Jam Mulai" />
                        <x-text-input id="start_time" name="start_time" type="time" class="mt-1 block w-full"
                            required />
                    </div>
                    <div class="min-w-0">
                        <x-input-label for="end_time" value="Jam Selesai" />
                        <x-text-input id="end_time" name="end_time" type="time" class="mt-1 block w-full" required />
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <x-text-input name="nuance" placeholder="Nuansa/Tema Acara" class="w-full" required />
            </div>

            <div class="mb-4" style="position: relative;">
                <x-input-label for="location_detail" value="Alamat Acara" />
                <textarea id="location_detail" name="location_detail" rows="3"
                    class="w-full border px-3 py-2 rounded-md text-sm resize-none focus:ring-1 focus:ring-primary"
                    placeholder="Ketik alamat atau pilih di peta" required></textarea>

                <ul id="suggestions"
                    style="position:absolute; background:white; list-style:none; padding:5px; margin:0; border:1px solid #ccc; width:100%; max-height:150px; overflow-y:auto; z-index:999;">
                </ul>
            </div>

            <div class="mb-3">
                <label class="block text-sm mb-1 font-medium">Upload Foto Lokasi Acara (Opsional)</label>
                <input type="file" name="location_photo"
                    class="w-full text-sm border border-gray-300 rounded-md file:bg-primary file:text-white file:px-4 file:py-1 file:rounded-md file:border-none file:cursor-pointer">
            </div>

            <div class="mb-3">
                <label class="block text-sm mb-1 font-medium">Upload Bukti Transfer DP</label>
                <input type="file" name="image"
                    class="w-full text-sm border border-gray-300 rounded-md file:bg-primary file:text-white file:px-4 file:py-1 file:rounded-md file:border-none file:cursor-pointer"
                    required>
                <label class="block text-sm mt-2 mb-1 font-medium">
                    DP yang harus dibayarkan:
                    <span id="dp_amount" class="font-semibold">Rp 0</span><br>
                    NoRek BCA: <span class="font-semibold">3762172183</span> a/n Fitri Fitria
                </label>
            </div>

            <div class="mb-4">
                <textarea name="notes" rows="2" placeholder="Catatan"
                    class="w-full border px-3 py-2 rounded-md text-sm resize-none"></textarea>
            </div>

            <div class="text-right">
                <x-primary-button type="submit">Kirim Pesanan</x-primary-button>
            </div>
        </form>
    </div>
</x-modal>
