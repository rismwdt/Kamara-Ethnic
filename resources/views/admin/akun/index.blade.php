<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manajemen Akun') }}
        </h2>
    </x-slot>

    <main class="flex-1 mb-auto bg-white min-h-screen p-6 text-gray-900 flex flex-col"
        x-data="{ selectedId: null, destroyRoute: '{{ route('akun.destroy', '__ID__') }}' }">

        @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded">
            {{ session('success') }}
        </div>
        @endif

        <div class="flex justify-between items-center mb-4">
            <x-add-button href="{{ route('akun.create') }}" label="Tambah Akun" />
        </div>

        <x-table>
            <x-slot name="thead">
                <tr>
                    <th class="px-4 py-2">No.</th>
                    <th class="px-4 py-2">Nama</th>
                    <th class="px-4 py-2">Email</th>
                    <th class="px-4 py-2">Aksi</th>
                </tr>
            </x-slot>

            @forelse ($performers as $index => $p)
            <tr class="whitespace-nowrap">
                <td class="px-4 py-2">
                    {{ $performers->firstItem() + $index }}
                </td>

                <td class="px-4 py-2">
                    <div class="font-medium text-gray-900 dark:text-gray-100">
                        {{ $p->name }}
                    </div>
                </td>

                <td class="px-4 py-2">
                    <div class="max-w-xs md:max-w-sm overflow-hidden text-ellipsis">
                        <span class="text-gray-700 dark:text-gray-300 break-all">{{ $p->email }}</span>
                    </div>
                </td>

                <td class="px-4 py-2">
                    <div class="flex justify-end items-center space-x-2">
                        <a href="{{ route('akun.edit', $p->id) }}" title="Edit">
                            <x-primary-button class="text-xs px-2 py-1">
                                <i class="fas fa-edit"></i>
                            </x-primary-button>
                        </a>

                        <x-danger-button type="button" class="text-xs px-2 py-1" title="Hapus"
                            @click.prevent="selectedId = {{ $p->id }}; $dispatch('open-modal', 'confirm-delete-akun')">
                            <i class="fas fa-trash"></i>
                        </x-danger-button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-4 py-6 text-center text-gray-500">
                    Belum ada data akun.
                </td>
            </tr>
            @endforelse
        </x-table>

        <div class="mt-8 flex justify-center">
            {{ $performers->links() }}
        </div>

        <x-modal name="confirm-delete-akun" focusable>
            <form :action="destroyRoute.replace('__ID__', selectedId)" method="POST" class="p-6">
                @csrf
                @method('DELETE')

                <h2 class="text-lg font-medium text-gray-900">
                    Konfirmasi Hapus Akun
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    Apakah Anda yakin ingin menghapus akun ini? Tindakan ini tidak dapat dibatalkan.
                </p>

                <div class="mt-6 flex justify-end gap-3">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        Batal
                    </x-secondary-button>
                    <x-danger-button type="submit">
                        Hapus
                    </x-danger-button>
                </div>
            </form>
        </x-modal>
    </main>
</x-app-layout>
