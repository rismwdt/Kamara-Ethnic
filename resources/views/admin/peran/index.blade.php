<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Peran Pengisi') }}
        </h2>
    </x-slot>

    <main class="p-6" x-data="roleManager()">
        @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded">
            {{ session('success') }}
        </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-2">
                <x-table>
                    <x-slot name="thead">
                        <tr>
                            <th class="px-4 py-2 w-16">No</th>
                            <th class="px-4 py-2">Nama Peran</th>
                            <th class="px-4 py-2 w-24 text-center">Aksi</th>
                        </tr>
                    </x-slot>

                    @foreach ($roles as $i => $role)
                    <tr>
                        <td class="px-4 py-2">{{ $roles->firstItem() + $i }}</td>

                        <td class="px-4 py-2">
                            <button type="button" class="text-black"
                                @click="select({ id: {{ $role->id }}, name: @js($role->name) })">
                                {{ $role->name }}
                            </button>
                        </td>

                        <td class="px-4 py-2">
                            <div class="flex justify-center items-center space-x-2">
                                <x-danger-button type="button"
                                    onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'delete-{{ $role->id }}' }))">
                                    <i class="fas fa-trash"></i>
                                </x-danger-button>
                                <x-modal-delete name="delete-{{ $role->id }}" :itemId="$role->id"
                                    :itemName="$role->name" route="peran.destroy" />
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </x-table>

                <div class="mt-8 flex justify-center">
                    {{ $roles->links() }}
                </div>
            </div>

            <div class="md:col-span-1 space-y-6">
                <div class="border rounded-lg p-4">
                    <h3 class="font-semibold mb-3">Tambah Peran</h3>
                    <form method="POST" action="{{ route('peran.store') }}" class="space-y-3">
                        @csrf
                        <input name="name" type="text" placeholder="Nama peran (mis. Sinden)"
                            class="w-full border rounded px-3 py-2" required>
                        <x-primary-button class="w-full justify-center">Tambah</x-primary-button>
                    </form>
                </div>

                <div class="border rounded-lg p-4" x-show="selected" x-cloak>
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-semibold">Edit Peran</h3>
                        <button type="button" class="text-sm text-gray-500" @click="clear()">Batal</button>
                    </div>

                    <form method="POST" :action="updateUrl()" class="space-y-3">
                        @csrf
                        @method('PUT')
                        <input name="name" type="text" class="w-full border rounded px-3 py-2" x-model="selected.name"
                            required>
                        <x-primary-button class="w-full justify-center">Perbarui</x-primary-button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script>
        function roleManager() {
            return {
                selected: null,
                updateTemplate: "{{ route('peran.update', 0) }}",
                select(r) {
                    this.selected = {
                        ...r
                    };
                },
                clear() {
                    this.selected = null;
                },
                updateUrl() {
                    if (!this.selected) return '#';
                    return this.updateTemplate.replace(/0(?=\/?$)/, this.selected.id);
                }
            }
        }
    </script>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</x-app-layout>
