<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Pesanan') }}
        </h2>
    </x-slot>

    <main class="flex-1 mb-auto bg-white min-h-screen p-6 text-gray-900 flex flex-col">
        <div class="flex justify-between items-center mb-4">
            <a href="{{ route('pesanan.index') }}">
                <x-secondary-button>
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </x-secondary-button>
            </a>
        </div>

        {{-- Info ringkas --}}
        <div class="bg-white shadow rounded-lg p-6 text-gray-800 mb-6">
            <h3 class="text-lg font-semibold mb-4">Informasi Pesanan</h3>
            <table class="table-auto w-full text-sm text-left text-gray-700">
                <tbody class="divide-y divide-gray-200">
                    <tr><th class="py-2 pr-4 font-medium w-1/3">Paket Acara</th><td>: {{ $booking->event->name }}</td></tr>
                    <tr><th class="py-2 pr-4 font-medium">Nama Klien</th><td>: {{ $booking->client_name }}</td></tr>
                    <tr><th class="py-2 pr-4 font-medium">Tanggal</th><td>: {{ \Carbon\Carbon::parse($booking->date)->format('d-m-Y') }}</td></tr>
                    <tr>
                        <th class="py-2 pr-4 font-medium">Waktu</th>
                        <td>: {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <form method="POST" action="{{ route('pesanan.update', $booking->id) }}">
            @csrf
            @method('PUT')

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <strong>Terjadi kesalahan:</strong>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white shadow rounded-lg p-6 text-gray-800">
                <h3 class="text-lg font-semibold mb-4">Ubah Status & Prioritas</h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 max-w-3xl">
                    {{-- Status --}}
                    {{-- <div>
                        <x-input-label for="status" value="Status" />
                        @php $statusOld = old('status', $booking->status); @endphp
                        <select id="status" name="status" class="mt-1 block w-full rounded border-gray-300 shadow-sm">
                            <option value="">-- Biarkan tanpa perubahan --</option>
                            <option value="tertunda" {{ $statusOld==='tertunda' ? 'selected' : '' }}>Tertunda</option>
                            <option value="diterima" {{ $statusOld==='diterima' ? 'selected' : '' }}>Diterima</option>
                            <option value="ditolak"  {{ $statusOld==='ditolak'  ? 'selected' : '' }}>Ditolak</option>
                            <option value="selesai"  {{ $statusOld==='selesai'  ? 'selected' : '' }}>Selesai</option>
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                    </div> --}}

                    {{-- Prioritas --}}
                    <div>
                        <x-input-label for="priority" value="Prioritas" />
                        @php $pOld = old('priority', $booking->priority ?? 'normal'); @endphp
                        <select id="priority" name="priority" class="mt-1 block w-full rounded border-gray-300 shadow-sm">
                            <option value="">-- Biarkan tanpa perubahan --</option>
                            <option value="normal"  {{ $pOld==='normal'  ? 'selected':'' }}>Normal</option>
                            <option value="darurat" {{ $pOld==='darurat' ? 'selected':'' }}>Darurat</option>
                        </select>
                        <x-input-error :messages="$errors->get('priority')" class="mt-2" />
                    </div>

                    {{-- Keluarga / Relasi --}}
                    <div class="flex items-end">
                        <div>
                            <input type="hidden" name="is_family" value="0">
                            <label class="inline-flex items-center gap-2">
                                <input type="checkbox" name="is_family" value="1"
                                       {{ old('is_family', $booking->is_family ? 1 : 0) ? 'checked' : '' }}>
                                Dari keluarga / relasi
                            </label>
                            <x-input-error :messages="$errors->get('is_family')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <x-primary-button class="mt-6">Simpan</x-primary-button>
            </div>
        </form>
    </main>
</x-app-layout>
