<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Notes') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium mb-4">Your Active Notes</h3>

                @forelse ($notes as $note)
                    <div class="border-b mb-4 pb-2">
                        <h4 class="font-bold text-blue-600">{{ $note->title }}</h4>
                        <p class="text-gray-600">{{ Str::limit($note->body, 100) }}</p>
                    </div>
                @empty
                    <p>No notes found. Create your first one!</p>
                @endforelse

                {{ $notes->links() }} {{-- Pagination Links --}}
            </div>
        </div>
    </div>
</x-app-layout>
