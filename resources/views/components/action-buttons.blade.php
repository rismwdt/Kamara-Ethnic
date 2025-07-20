<div class="space-x-2">
    <a href="{{ route("$route.show", $id) }}" class="text-blue-500 hover:underline text-sm">Detail</a>
    <a href="{{ route("$route.edit", $id) }}" class="text-yellow-500 hover:underline text-sm">Edit</a>
    <form action="{{ route("$route.destroy", $id) }}" method="POST" class="inline">
        @csrf
        @method('DELETE')
        <button onclick="return confirm('Hapus?')" class="text-red-500 hover:underline text-sm">Hapus</button>
    </form>
</div>
