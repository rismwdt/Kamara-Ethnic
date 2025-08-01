<section id="event" class="pt-32 pb-20 bg-gray-50">
    <div class="container">
        <div class="w-full px-4">
            <div class="max-w-xl mx-auto text-center mb-16">
                <h4 class="font-semibold text-lg text-primary mb-2">Paket Acara</h4>
                <h2 class="font-bold text-dark text-3xl mb-4 md:text-4xl lg:text-5xl">Paket Acara Kamara
                    Ethnic</h2>
                <p class="font-medium text-md text-secondary md:text-lg">
                    Kami menyediakan berbagai paket pertunjukan seni tradisional khas Sunda yang memeriahkan momen
                    spesial Anda.
                </p>
            </div>
            <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                @foreach ($events as $event)
                <div x-data="{ open: false }" class="flex flex-col bg-white rounded-3xl shadow-lg overflow-hidden p-4">
                    <img src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->name }}"
                        class="w-full h-64 rounded-2xl object-cover flex-shrink-0 mb-4">
                    <h2 class="font-bold text-2xl text-dark leading-tight">{{ $event->name }}</h2>
                    <h4 class="font-semibold text-lg text-primary mb-2">
                        Rp{{ number_format($event->price, 0, ',', '.') }}
                    </h4>
                    <button
                        onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'modal-detail-{{ $event->id }}' }))"
                        class="px-2 py-2 text-xs text-dark mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20z" />
                        </svg>
                        Detail Paket
                    </button>
                    @auth
                    <button
    data-event-id="{{ $event->id }}"
    class="btnPesanSekarang bg-primary text-white text-lg font-sans font-semibold rounded-lg px-8 py-2 flex items-center justify-center gap-2 hover:bg-[#5a0c0f] hover:text-white transition">
    Pesan Sekarang
</button>
                    @else
                    <a href="{{ route('login') }}"
                        class="bg-primary text-white text-lg font-sans font-semibold rounded-lg px-8 py-2 flex items-center justify-center gap-2 hover:bg-[#5a0c0f] hover:text-white transition">
                        Pesan Sekarang
                    </a>
                    @endauth
                    <x-modal name="modal-detail-{{ $event->id }}">
                        <div class="p-6 relative">
                            <button
                                onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'modal-detail-{{ $event->id }}' }))"
                                class="absolute top-3 right-3 text-gray-500 hover:text-red-600 text-3xl">
                                &times;
                            </button>
                            <h2 class="text-md font-semibold text-gray-800 mb-4">Detail Paket Acara</h2>
                            <p class="text-sm text-secondary whitespace-pre-line">{{ $event->description }}</p>
                        </div>
                    </x-modal>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

@include('klien.modal-pesanan')
