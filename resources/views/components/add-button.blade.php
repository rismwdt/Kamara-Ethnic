@props(['href' => '#', 'label' => 'Tambah'])

<a href="{{ $href }}"
    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring focus:ring-blue-300 active:bg-blue-800 transition">
    <i class="fas fa-plus mr-2"></i> {{ $label }}
</a>
