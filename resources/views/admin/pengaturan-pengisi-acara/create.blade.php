<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Pengaturan Pengisi Acara') }}
        </h2>
    </x-slot>

    <main class="flex-1 mb-auto bg-white min-h-screen p-6 text-gray-900 flex flex-col">
        <div class="flex justify-between items-center mb-4">
            <a href="{{ route('pengaturan-pengisi-acara.index') }}">
                <x-secondary-button>
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </x-secondary-button>
            </a>
        </div>

        <form method="POST" action="{{ route('pengaturan-pengisi-acara.store') }}">
            @csrf

            {{-- Pilih Acara --}}
            <div class="mb-4">
                <x-input-label for="event_id" value="Acara" />
                <select id="event_id" name="event_id" class="mt-1 block w-full rounded border-gray-300 shadow-sm" required>
                    <option value="">Pilih Acara</option>
                    @foreach($events as $event)
                        <option value="{{ $event->id }}">{{ $event->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('event_id')" class="mt-2" />
            </div>

            {{-- Table Dynamic Rows --}}
            <table class="w-full border mb-4" id="roles-table">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border p-2">Peran</th>
                        <th class="border p-2">Jumlah</th>
                        <th class="border p-2">Unik?</th>
                        <th class="border p-2">Catatan</th>
                        <th class="border p-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="border p-2">
                            <select name="performer_role_id[]" class="w-full rounded border-gray-300 shadow-sm" required>
                                <option value="">Pilih Peran</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="border p-2">
                            <input type="number" name="quantity[]" min="1" value="1" class="w-full rounded border-gray-300 shadow-sm" required>
                        </td>
                        <td class="border p-2 text-center">
                            <input type="checkbox" name="is_unique[]" value="1">
                        </td>
                        <td class="border p-2">
                            <input type="text" name="notes[]" class="w-full rounded border-gray-300 shadow-sm">
                        </td>
                        <td class="border p-2 text-center">
                            <button type="button" class="bg-red-500 text-white px-2 py-1 rounded remove-row">Hapus</button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <button type="button" id="add-row" class="bg-blue-500 text-white px-4 py-2 rounded mb-4">Tambah Baris</button>

            <x-primary-button>Simpan Semua</x-primary-button>
        </form>
    </main>

    {{-- Script Tambah / Hapus Baris --}}
    <script>
        document.getElementById('add-row').addEventListener('click', function () {
            let tableBody = document.querySelector('#roles-table tbody');
            let newRow = tableBody.rows[0].cloneNode(true);

            // Reset value pada baris baru
            newRow.querySelectorAll('select, input').forEach(el => {
                if (el.type === 'checkbox') {
                    el.checked = false;
                } else {
                    el.value = el.defaultValue;
                }
            });

            tableBody.appendChild(newRow);
        });

        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-row')) {
                let rowCount = document.querySelectorAll('#roles-table tbody tr').length;
                if (rowCount > 1) {
                    e.target.closest('tr').remove();
                } else {
                    alert('Minimal satu baris diperlukan.');
                }
            }
        });
    </script>
</x-app-layout>
