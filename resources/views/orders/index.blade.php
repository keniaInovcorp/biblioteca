<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Encomendas</h2>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <livewire:orders-table />
    </div>
</x-app-layout>

