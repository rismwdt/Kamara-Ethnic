{{-- resources/views/admin/kebutuhan-event/edit.blade.php --}}
<x-app-layout>
  <x-slot name="header"><h2 class="font-semibold text-xl">Kebutuhan: {{ $event->name }}</h2></x-slot>

  <main class="p-6">
    @if(session('success'))
      <div class="mb-4 p-3 rounded bg-green-100 text-green-800">{{ session('success') }}</div>
    @endif

    <form class="mb-6 flex gap-3" method="POST" action="{{ route('admin.kebutuhan-event.store',$event) }}">
      @csrf
      <select name="performer_role_id" class="border rounded px-3 py-2" required>
        <option value="" disabled selected>Pilih Peran</option>
        @foreach($roles as $r)
          <option value="{{ $r->id }}">{{ $r->name }}</option>
        @endforeach
      </select>
      <input type="number" name="quantity" min="1" class="border rounded px-3 py-2 w-24" placeholder="Qty" required>
      <x-primary-button>Tambah/Update</x-primary-button>
    </form>

    <x-table>
      <x-slot name="thead">
        <tr>
          <th class="px-4 py-2">Peran</th>
          <th class="px-4 py-2">Jumlah</th>
          <th class="px-4 py-2">Aksi</th>
        </tr>
      </x-slot>

      @forelse($requirements as $req)
        <tr>
          <td class="px-4 py-2">{{ $req->role?->name ?? '-' }}</td>
          <td class="px-4 py-2">
            <form method="POST" action="{{ route('admin.kebutuhan-event.update', [$event,$req]) }}" class="flex gap-2">
              @csrf @method('PUT')
              <input type="number" name="quantity" min="1" value="{{ $req->quantity }}"
                     class="border rounded px-2 py-1 w-24">
              <x-primary-button class="text-xs">Simpan</x-primary-button>
            </form>
          </td>
          <td class="px-4 py-2">
            <form method="POST" action="{{ route('admin.kebutuhan-event.destroy', [$event,$req]) }}"
                  onsubmit="return confirm('Hapus kebutuhan ini?')">
              @csrf @method('DELETE')
              <x-danger-button class="text-xs px-2 py-1"><i class="fas fa-trash"></i></x-danger-button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td class="px-4 py-4 text-gray-500 italic" colspan="3">Belum ada kebutuhan.</td></tr>
      @endforelse
    </x-table>
  </main>
</x-app-layout>
