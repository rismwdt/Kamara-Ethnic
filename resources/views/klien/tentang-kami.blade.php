<section id="about" class="pt-32 pb-20 bg-gray-50">
    <div class="container mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <div class="lg:col-span-4 space-y-6 flex flex-col">
            <div class="relative rounded-3xl overflow-hidden shadow-lg flex flex-col justify-end p-6"
                style="min-height: 528px; background-image: url('{{ asset('img/lengkap2.jpg') }}'); background-size: cover; background-position: center;">
                <div class="absolute inset-x-0 top-0 h-1/2 bg-gradient-to-b from-gray-700/60 to-transparent "></div>
                {{-- <div
        class="absolute top-6 right-6 bg-blue-600 text-white text-xs rounded-xl px-3 py-1 max-w-[180px] font-semibold">
        I installed it, but the panel shows a red flashlight. How do I fix this?
    </div> --}}
                <div class="absolute top-6  px-3 py-3">
                    <h2 class="font-bold text-white text-3xl sm:text-4xl lg:text-5xl">Kamara Ethnic</h2>
                    <h4 class="font-bold uppercase text-slate-200 text-md">Since 2022</h4>
                </div>
                {{-- <div class="relative z-10 max-w-[280px] mt-auto mb-2">
        <h2 class="text-2xl font-semibold leading-tight text-black">Collaborative<br />intelligence</h2>
        <p class="mt-2 text-sm font-normal text-black/80 leading-snug">Sub-agents work together seamlessly
            to handle complex scenarios, achieving superior results through coordinated effort.
        </p>
    </div> --}}
            </div>
        </div>
        <div class="lg:col-span-5 flex flex-col gap-8">
            <div class="bg-white rounded-3xl p-6 shadow-lg flex flex-col justify-between" style="min-height: 240px">
                {{-- <div class="flex flex-wrap items-center gap-4">
                    <button
                        class="w-10 h-10 rounded-lg border border-gray-300 text-gray-700 font-semibold text-sm flex items-center justify-center">EN</button>
                    <button
                        class="w-10 h-10 rounded-full border border-yellow-400 text-yellow-400 font-semibold text-sm flex items-center justify-center">UK</button>
                    <button
                        class="w-10 h-10 rounded-lg border border-gray-300 text-gray-700 font-semibold text-sm flex items-center justify-center">FR</button>
                </div> --}}
                <div>
                    <h4 class="font-bold uppercase text-primary text-lg mb-3">Tentang Kami</h4>
                    <p class="font-medium text-base text-secondary max-w-xl lg:text-lg">Kamara Ethnic adalah penyedia
                        layanan acara adat Sunda yang berkomitmen melestarikan budaya
                        melalui penampilan tradisional yang elegan dan profesional. Kami menawarkan paket lengkap
                        seperti upacara adat, sisingaan, siraman, dangdut, dan bajidoran untuk menjadikan momen Anda
                        lebih berkesan dan bermakna.</p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-6" style="min-height: 240px">
                <div class="bg-white rounded-3xl p-6 shadow-lg flex flex-col justify-between"
                    style="background-image: url('{{ asset('img/merak3.jpg') }}'); background-size: cover; background-position: center;">
                    <div class="text-white">
                        {{-- <p class="text-xs mb-1">Automatically detect</p>
        <h3 class="text-xl font-bold">All major languages</h3> --}}
                    </div>
                </div>
                <div class="rounded-3xl p-6 shadow-lg flex flex-col justify-end text-white relative"
                    style="background-image: url('{{ asset('img/tehmpit.png') }}'); background-size: cover; background-position: center; min-height: 240px;">
                    <!-- Overlay gradasi -->
                    <div
                        class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t  from-gray-900/60 to-transparent z-0 rounded-3xl">
                    </div>
                    <div class="relative z-10 mt-5">
                        <p class="text-xs mb-1">Owner</p>
                        <h3 class="text-xl font-bold">Fitri fitria</h3>
                    </div>
                    {{-- <div class="relative w-full h-[100px]">
        <div
            class="absolute top-0 left-0 w-10 h-10 rounded-full bg-white/80 border border-gray-300 flex items-center justify-center shadow">
            <img src="https://storage.googleapis.com/a1aa/image/5d2c1398-a14c-46f7-c5e8-463c9d71af4f.jpg"
                alt="Envelope" class="w-5 h-5" />
        </div>
        <div
            class="absolute top-0 left-1/2 w-10 h-10 rounded-full bg-white/80 flex items-center justify-center shadow"
            style="transform: translate(-50%, 0)">
            <img src="https://storage.googleapis.com/a1aa/image/4e0e4a54-382b-42d8-c353-65aa9f0c8fab.jpg"
                alt="Mic" class="w-5 h-5" />
        </div>
        <div
            class="absolute top-0 right-0 w-10 h-10 rounded-full bg-white/80 border border-gray-300 flex items-center justify-center shadow">
            <img src="https://storage.googleapis.com/a1aa/image/6fe1a8f7-5893-43f2-ca66-a42186d6ba04.jpg"
                alt="Globe" class="w-5 h-5" />
        </div>
        <div
            class="absolute bottom-0 left-1/3 w-8 h-8 rounded-full bg-white/80 border border-gray-300 flex items-center justify-center shadow">
            <img src="https://storage.googleapis.com/a1aa/image/576d3401-d92a-4bcd-8161-518cb3316a91.jpg"
                alt="Calendar" class="w-4 h-4" />
        </div>
    </div> --}}
                </div>
            </div>
        </div>
        <div class="lg:col-span-3 flex flex-col gap-6">
            <div class="border rounded-lg shadow-lg bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center overflow-hidden"
                style="min-height: 528px">
                <video class="w-full h-[480 px] rounded-lg" autoplay muted loop controls>
                    <source src="{{ asset('video/khitan.mp4') }}" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
        </div>
    </div>
</section>
