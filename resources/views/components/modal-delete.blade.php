@props([
'name',
'itemId',
'itemName',
'route'
])

<x-modal :name="$name">
    <div class="p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                Konfirmasi Hapus
            </h2>
            <button @click="$dispatch('close-modal', '{{ $name }}')" class="text-gray-500 hover:text-red-600 text-3xl">
                &times;
            </button>
        </div>
        <p class="text-sm text-gray-600 dark:text-gray-300 mb-6">
            Yakin ingin menghapus data ini?<br> <strong>{{ $itemName }}</strong>
        </p>
        <form method="POST" action="{{ route($route, $itemId) }}">
            @csrf
            @method('DELETE')
            <div class="flex justify-end gap-2">
                <button type="button" @click="$dispatch('close-modal', '{{ $name }}')"
                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">
                    Batal
                </button>
                <x-danger-button>
                    Hapus
                </x-danger-button>
            </div>
        </form>
    </div>
</x-modal>
