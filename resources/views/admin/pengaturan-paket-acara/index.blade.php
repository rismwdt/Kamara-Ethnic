<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Pengaturan Pengisi Acara') }}
        </h2>
    </x-slot>

    <main class="flex-1 mb-auto bg-white min-h-screen p-6 text-gray-900 flex flex-col">
        @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded">
            {{ session('success') }}
        </div>
        @endif

        <div class="flex justify-between items-center mb-4">
            <x-add-button href="{{ route('pengaturan-paket-acara.create') }}" label="Tambah Pengaturan" />
        </div>

        <div class="overflow-x-auto w-full">
            <x-table>
                <x-slot name="thead">
                    <tr>
                        <th class="px-4 py-2">No.</th>
                        <th class="px-4 py-2">Event</th>
                        <th class="px-4 py-2">Daftar Pengisi Acara</th>
                        <th class="px-4 py-2">Catatan</th>
                        <th class="px-4 py-2">Aksi</th>
                    </tr>
                </x-slot>

                @php
                    $no = ($grouped->currentPage()-1) * $grouped->perPage() + 1;
                @endphp

                @foreach ($grouped as $group)
                    @php
                        $event = $group->first()->event;
                        // gabungkan catatan yang terisi (jika ada)
                        $notes = $group->pluck('notes')->filter()->unique()->values()->all();
                    @endphp
                    <tr>
                        <td class="px-4 py-2">{{ $no++ }}</td>
                        <td class="px-4 py-2">{{ $event->name ?? '-' }}</td>
                        <td class="px-4 py-2">
                            <ul class="list-disc list-inside">
                                @foreach ($group as $req)
                                    <li>{{ $req->performerRole->name ?? '-' }} ({{ $req->quantity }} orang)</li>
                                @endforeach
                            </ul>
                        </td>
                        <td class="px-4 py-2">
                            {{ count($notes) ? implode('; ', $notes) : '—' }}
                        </td>
                        <td class="px-4 py-2">
                            <div class="flex space-x-2">
                                <a href="{{ route('pengaturan-paket-acara.edit', $event->id) }}">
    <x-primary-button class="text-xs px-2 py-1">
        <i class="fas fa-edit"></i>
    </x-primary-button>
</a>

<x-danger-button type="button"
    onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'delete-{{ $event->id }}' }))">
    <i class="fas fa-trash"></i>
</x-danger-button>
<x-modal-delete name="delete-{{ $event->id }}"
    :itemId="$event->id"
    :itemName="'Pengaturan ' . ($event->name ?? 'Data')"
    route="pengaturan-paket-acara.destroy" />


                            </div>
                        </td>
                    </tr>
                @endforeach
            </x-table>

            <div class="mt-8 flex justify-center">
                {{ $grouped->links() }}
            </div>
        </div>
    </main>
</x-app-layout>
