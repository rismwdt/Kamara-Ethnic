<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Pengisi Acara') }}
        </h2>
    </x-slot>

    <main class="flex-1 mb-auto bg-white min-h-screen p-6 text-gray-900 flex flex-col">
        @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded">
            {{ session('success') }}
        </div>
        @endif

        <div class="flex justify-between items-center mb-4">
            <x-add-button href="{{ route('pengisi-acara.create') }}" label="Tambah Pengisi Acara" />
        </div>

        <div class="overflow-x-auto w-full">
            <x-table>
                <x-slot name="thead">
                    <tr>
                        <th class="px-4 py-2">No.</th>
                        <th class="px-4 py-2">Nama</th>
                        <th class="px-4 py-2">Jenis Kelamin</th>
                        <th class="px-4 py-2">Peran</th>
                        <th class="px-4 py-2">No. HP</th>
                        <th class="px-4 py-2">Rekening</th>
                        <th class="px-4 py-2">Ketersediaan</th>
                        <th class="px-4 py-2">Catatan</th>
                        <th class="px-4 py-2">Aksi</th>
                    </tr>
                </x-slot>

                @foreach ($performers as $index => $performer)
                <tr>
                    <td class="px-4 py-2">{{ $performers->firstItem() + $index }}</td>
                    <td class="px-4 py-2">{{ $performer->name }}</td>
                    <td class="px-4 py-2 capitalize">{{ $performer->gender }}</td>

                    {{-- Peran + badge Eksternal --}}
                    <td class="px-4 py-2">
                        <div class="flex items-center gap-2">
                            <span class="capitalize">{{ $performer->role?->name ?? '-' }}</span>
                            @if($performer->is_external)
                              <span class="text-[10px] px-1.5 py-0.5 rounded bg-purple-100 text-purple-800">Eksternal</span>
                            @endif
                        </div>
                    </td>

                    {{-- No. HP dengan formatter WhatsApp (62...) --}}
                    <td class="px-4 py-2">
                        @php
                            $raw = preg_replace('/\D+/', '', (string)$performer->phone); // hanya digit
                            if ($raw && str_starts_with($raw, '0')) {
                                $wa = '62'.substr($raw, 1);
                            } elseif ($raw) {
                                $wa = $raw;
                            } else {
                                $wa = null;
                            }
                        @endphp

                        @if ($wa)
                          <a href="https://wa.me/{{ $wa }}" target="_blank" class="text-blue-600 underline">
                            {{ $performer->phone }}
                          </a>
                        @else
                          -
                        @endif
                    </td>

                    <td class="px-4 py-2">{{ $performer->account_number }} ({{ $performer->bank_name }})</td>

                    <td class="px-4 py-2">
    @if ($performer->is_active)
        <span class="inline-block bg-green-100 text-green-800 text-sm px-2 py-1 rounded font-medium">
            Ya
        </span>
    @else
        <span class="inline-block bg-red-100 text-red-800 text-sm px-2 py-1 rounded font-medium">
            Tidak
        </span>
    @endif
</td>




                    <td class="px-4 py-2">{{ $performer->notes ?? '-' }}</td>

                    <td class="px-4 py-2">
                        <div class="flex justify-center items-center space-x-2">
                            <a href="{{ route('pengisi-acara.edit', $performer->id) }}">
                                <x-primary-button class="text-xs px-2 py-1">
                                    <i class="fas fa-edit"></i>
                                </x-primary-button>
                            </a>
                            <x-danger-button type="button"
                                onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'delete-{{ $performer->id }}' }))">
                                <i class="fas fa-trash"></i>
                            </x-danger-button>
                            <x-modal-delete name="delete-{{ $performer->id }}" :itemId="$performer->id"
                                :itemName="$performer->name" route="pengisi-acara.destroy" />
                        </div>
                    </td>
                </tr>
                @endforeach
            </x-table>

            <div class="mt-8 flex justify-center">
                {{ $performers->links() }}
            </div>
        </div>
    </main>
</x-app-layout>
