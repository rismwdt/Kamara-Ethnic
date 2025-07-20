<div class="overflow-x-auto">
    <table {{ $attributes->merge(['class' => 'min-w-full divide-y divide-gray-200']) }}>
        <thead class="bg-gray-50 text-gray-700 text-left text-sm font-semibold">
            {{ $thead }}
        </thead>
        <tbody class="bg-white divide-y divide-gray-200 text-sm text-gray-600">
            {{ $slot }}
        </tbody>
    </table>
</div>
