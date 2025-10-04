<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Tambah Pesanan') }}
            </h2>

            <div class="flex gap-2 overflow-x-auto max-w-[60vw] sm:max-w-none
                        [-ms-overflow-style:none] [scrollbar-width:none]" x-data x-cloak>
                <style>
                    .tabs-hide-scroll::-webkit-scrollbar {
                        display: none
                    }
                </style>
                <div class="tabs-hide-scroll flex gap-2">
                    <template x-for="opt in $store.bookingForm.types" :key="opt.value">
                        <button type="button" class="px-3 py-1.5 rounded-full text-xs border whitespace-nowrap" :class="$store.bookingForm.type === opt.value
                                    ? 'bg-white text-primary border-gray-900'
                                    : 'bg-white hover:bg-gray-50 border-gray-300'" x-text="opt.label"
                            @click="$store.bookingForm.type = opt.value"></button>
                    </template>
                </div>
            </div>
        </div>
    </x-slot>

    <main class="flex-1 mb-auto bg-white min-h-screen p-4 sm:p-6 text-gray-900 flex flex-col"
        x-data="tambahPesanan({{ json_encode($events->map(fn($e)=>['id'=>$e->id,'name'=>$e->name,'price'=>$e->price??0])) }})">

        <div class="flex justify-between items-center mb-4">
            <a href="{{ route('pesanan.index') }}">
                <x-secondary-button>
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </x-secondary-button>
            </a>
        </div>

        @if ($errors->any())
        <div class="mb-4 rounded-md border border-red-300 bg-red-50 p-3 text-sm text-red-700">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('pesanan.store') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="event_type" :value="$store.bookingForm.type">

            <div class="flex flex-col md:flex-row gap-4">

                <div class="w-full md:w-1/2">
                    <div class="bg-white rounded-2xl shadow-md border border-gray-200 p-4 sm:p-6 space-y-4">
                        <h3 class="text-sm font-semibold text-gray-700">Form Paket Acara</h3>

                        <div>
                            <x-input-label for="event_id" value="Paket Acara" />
                            <select id="event_id" name="event_id"
                                class="mt-1 block w-full rounded border-gray-300 shadow-sm" x-model="eventId"
                                @change="onEventChange()" required>
                                <option value="">-- Pilih Paket --</option>
                                @foreach($events as $ev)
                                <option value="{{ $ev->id }}" data-price="{{ $ev->price ?? 0 }}"
                                    {{ old('event_id') == $ev->id ? 'selected' : '' }}>
                                    {{ $ev->name }} {{ $ev->price ? '— Rp '.number_format($ev->price,0,',','.') : '' }}
                                </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('event_id')" class="mt-2" />
                            <input type="hidden" id="event_price" name="price" x-model="price">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="dp_percent" value="Persentase DP" />
                                <x-text-input id="dp_percent" name="dp_percent" type="number" min="0" max="100" step="1"
                                    class="mt-1 block w-full" x-model.number="dpPercent" @input="calcDp()" />
                                <x-input-error :messages="$errors->get('dp_percent')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label value="Nominal DP (otomatis)" />
                                <div class="mt-1 py-2 px-3 rounded border border-gray-300 bg-gray-50">
                                    <span x-text="formatRupiah(dp)"></span>
                                </div>
                                <input type="hidden" name="dp" :value="dp">
                            </div>
                        </div>

                        <div>
                            <x-input-label for="phone" value="No HP" />
                            <x-text-input id="phone" name="phone" placeholder="08xxxxxxxxxx" value="{{ old('phone') }}"
                                required class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="email" value="Email" />
                            <x-text-input id="email" name="email" type="email" value="{{ old('email') }}" required
                                class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="date" value="Tanggal" />
                            <x-text-input id="date" name="date" type="date" value="{{ old('date') }}" required
                                class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('date')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="start_time" value="Mulai" />
                                <x-text-input id="start_time" name="start_time" type="time"
                                    value="{{ old('start_time') }}"
                                    class="mt-1 block w-full rounded border-gray-300 shadow-sm" required />
                                <x-input-error :messages="$errors->get('start_time')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="end_time" value="Selesai" />
                                <x-text-input id="end_time" name="end_time" type="time" value="{{ old('end_time') }}"
                                    class="mt-1 block w-full rounded border-gray-300 shadow-sm" required />
                                <x-input-error :messages="$errors->get('end_time')" class="mt-2" />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="location_detail" value="Alamat Lengkap Acara" />
                            <textarea id="location_detail" name="location_detail" rows="3"
                                class="mt-1 block w-full rounded border-gray-300 shadow-sm"
                                required>{{ old('location_detail') }}</textarea>
                            <x-input-error :messages="$errors->get('location_detail')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <div class="w-full md:w-1/2">
                    <div class="bg-white rounded-2xl shadow-md border border-gray-200 p-4 sm:p-6 space-y-4">
                        <h3 class="text-sm font-semibold text-gray-700">Form Biodata</h3>

                        {{-- PERNIKAHAN --}}
                        <template x-if="$store.bookingForm.type === 'pernikahan'">
                            <div class="space-y-4">
                                <div>
                                    <x-input-label for="client_name" value="Nama Pengantin (nama & nama)" />
                                    <x-text-input id="client_name" name="client_name"
                                        class="mt-1 block w-full rounded border-gray-300 shadow-sm" />
                                    <x-input-error :messages="$errors->get('client_name')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="female_parents" value="Nama Orang Tua Wanita" />
                                    <x-text-input id="female_parents" name="female_parents"
                                        class="mt-1 block w-full rounded border-gray-300 shadow-sm" />
                                    <x-input-error :messages="$errors->get('female_parents')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="male_parents" value="Nama Orang Tua Pria" />
                                    <x-text-input id="male_parents" name="male_parents"
                                        class="mt-1 block w-full rounded border-gray-300 shadow-sm" />
                                    <x-input-error :messages="$errors->get('male_parents')" class="mt-2" />
                                </div>
                            </div>
                        </template>

                        {{-- KHITAN --}}
                        <template x-if="$store.bookingForm.type === 'khitan'">
                            <div class="space-y-4">
                                <div>
                                    <x-input-label for="client_name" value="Nama Penanggung Jawab" />
                                    <x-text-input id="client_name" name="client_name"
                                        class="mt-1 block w-full rounded border-gray-300 shadow-sm" />
                                    <x-input-error :messages="$errors->get('client_name')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="event_name" value="Nama Anak" />
                                    <x-text-input id="event_name" name="event_name"
                                        class="mt-1 block w-full rounded border-gray-300 shadow-sm" />
                                    <x-input-error :messages="$errors->get('event_name')" class="mt-2" />
                                </div>
                            </div>
                        </template>

                        {{-- GATHERING / GRAND OPENING --}}
                        <template
                            x-if="$store.bookingForm.type === 'gathering' || $store.bookingForm.type === 'grand_opening'">
                            <div class="space-y-4">
                                <div>
                                    <x-input-label for="client_name" value="Nama Penanggung Jawab" />
                                    <x-text-input id="client_name" name="client_name"
                                        class="mt-1 block w-full rounded border-gray-300 shadow-sm" />
                                    <x-input-error :messages="$errors->get('client_name')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="event_name" value="Nama Acara" />
                                    <x-text-input id="event_name" name="event_name"
                                        class="mt-1 block w-full rounded border-gray-300 shadow-sm" />
                                    <x-input-error :messages="$errors->get('event_name')" class="mt-2" />
                                </div>
                            </div>
                        </template>

                        {{-- LAINNYA --}}
                        <template x-if="$store.bookingForm.type === 'lainnya'">
                            <div class="space-y-4">
                                <div>
                                    <x-input-label for="client_name" value="Nama Penanggung Jawab" />
                                    <x-text-input id="client_name" name="client_name"
                                        class="mt-1 block w-full rounded border-gray-300 shadow-sm" />
                                    <x-input-error :messages="$errors->get('client_name')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="event_name" value="Nama Acara" />
                                    <x-text-input id="event_name" name="event_name"
                                        class="mt-1 block w-full rounded border-gray-300 shadow-sm" />
                                    <x-input-error :messages="$errors->get('event_name')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="description" value="Deskripsi Singkat" />
                                    <textarea id="description" name="description" rows="2"
                                        class="mt-1 w-full border px-3 py-2 rounded text-sm resize-none"></textarea>
                                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                </div>
                            </div>
                        </template>

                        <div>
                            <x-input-label for="nuance" value="Nuansa/Tema Acara" />
                            <x-text-input id="nuance" name="nuance"
                                class="mt-1 block w-full rounded border-gray-300 shadow-sm" required />
                            <x-input-error :messages="$errors->get('nuance')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="notes" value="Catatan" />
                            <textarea id="notes" name="notes" rows="3"
                                class="mt-1 block w-full rounded border-gray-300 shadow-sm">{{ old('notes') }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>
                    </div>
                </div>
            </div>
            <x-primary-button class="mt-6">Simpan Pesanan</x-primary-button>
        </form>
    </main>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('bookingForm', {
                type: @json(old('event_type', 'pernikahan')),
                types: [{
                        value: 'pernikahan',
                        label: 'Pernikahan'
                    },
                    {
                        value: 'khitan',
                        label: 'Khitan'
                    },
                    {
                        value: 'gathering',
                        label: 'Gathering'
                    },
                    {
                        value: 'grand_opening',
                        label: 'Grand Opening'
                    },
                    {
                        value: 'lainnya',
                        label: 'Lainnya'
                    },
                ],
            });
        });

        function tambahPesanan(events) {
            return {
                eventId: null,
                price: 0,
                dpPercent: {
                    {
                        old('dp_percent', 50)
                    }
                },
                dp: 0,
                onEventChange() {
                    const ev = events.find(e => e.id == this.eventId);
                    this.price = ev ? ev.price : 0;
                    this.calcDp();
                },
                calcDp() {
                    this.dp = Math.round((this.price || 0) * (this.dpPercent / 100));
                },
                formatRupiah(n) {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR'
                    }).format(n || 0);
                }
            }
        }
    </script>
</x-app-layout>
