    <x-app-layout>
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Notes') }}
            </h2>
        </x-slot>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                    <div x-data="{ open: false }">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-medium">Your Active Notes</h3>
                            <button @click="open = !open"
                                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition shadow-sm">
                                <span x-text="open ? '- Cancel' : '+ New Note'"></span>
                            </button>
                        </div>
                        <div class="flex gap-4 mb-6 border-b">
                            <a href="{{ route('dashboard', ['archived' => 0]) }}"
                                class="pb-2 px-1 {{ !request('archived') ? 'border-b-2 border-blue-500 font-bold' : '' }}">
                                Active
                            </a>
                            <a href="{{ route('dashboard', ['archived' => 1]) }}"
                                class="pb-2 px-1 {{ request('archived') ? 'border-b-2 border-blue-500 font-bold' : '' }}">
                                Archived
                            </a>
                        </div>

                        <div x-show="open" x-cloak x-transition
                            class="mb-8 bg-gray-50 p-6 rounded-xl border border-gray-200 shadow-inner">
                            <form action="{{ route('notes.store') }}" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <label class="block text-sm font-bold mb-2 text-gray-700">Title</label>
                                    <input type="text" name="title"
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                        required>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-bold mb-2 text-gray-700">Body</label>
                                    <textarea name="body" rows="4"
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required></textarea>
                                </div>
                                <button type="submit"
                                    class="bg-green-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-green-700 transition">
                                    Save Note
                                </button>
                            </form>
                        </div>
                    </div>
                    <form action="{{ route('dashboard') }}" method="GET" class="mb-6">
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Search titles or content..."
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 pl-10">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                    </form>
                    @forelse ($notes as $note)
                        <div class="border-b mb-6 pb-6 last:border-b-0" x-data="{
                            editing: false,
                            loading: false,
                            expanded: false,
                            isOverflowing: false,
                        
                            body: @js($note->body),
                            tldr: @js($note->tldr),
                        
                            get preview() {
                                return this.tldr ?? this.body;
                            },
                        
                            originalBody: @js($note->body),
                        
                            checkOverflow() {
                                this.$nextTick(() => {
                                    this.isOverflowing = this.$refs.bodyText.scrollHeight > this.$refs.bodyText.clientHeight;
                                });
                            }
                        }">

                            <h4 class="font-bold text-blue-600 text-lg mb-1">{{ $note->title }}</h4>

                            <div x-show="!editing">
                                <div x-ref="bodyText" :class="expanded ? '' : 'line-clamp-3'"
                                    class="text-gray-600 my-2 whitespace-pre-line cursor-pointer"
                                    x-text="expanded ? body : preview" @click="expanded = !expanded">
                                </div>

                                <div x-show="isOverflowing">
                                    <button @click="expanded = !expanded"
                                        class="text-xs font-bold text-gray-400 hover:text-gray-600 uppercase tracking-wide mb-2">
                                        <span x-text="expanded ? '↑ Show Less' : '↓ Show More'"></span>
                                    </button>
                                </div>

                                <div class="flex items-center justify-between mt-4">
                                    <div class="flex items-center gap-4">
                                        <button @click="editing = true"
                                            class="text-sm font-semibold text-blue-500 hover:text-blue-700">
                                            Edit
                                        </button>

                                        <button
                                            @click="
                                            loading = true;
                                            fetch('{{ route('notes.summarize', $note) }}', {
                                                method: 'POST',
                                                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                                            })
                                            .then(res => res.ok ? res.json() : res.json().then(e => { throw e }))
                                            .then(data => { 
                                            tldr = data.note.tldr;
                                            loading = false;
                                            checkOverflow();
                                            })
                                            .catch(err => { alert('AI is busy'); loading = false; })
                                        "
                                            :disabled="loading"
                                            class="text-sm font-semibold text-purple-600 hover:text-purple-800 disabled:opacity-50">
                                            <span x-text="loading ? '✨ Thinking...' : '✨ Summarize'"></span>
                                        </button>
                                    </div>

                                    <form action="{{ route('notes.archive', $note) }}" method="POST"
                                        onsubmit="return confirm('Archive this note?')">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                            class="text-sm font-semibold text-red-400 hover:text-red-600">
                                            Archive
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div x-show="editing" x-cloak x-transition>
                                <textarea x-model="body"
                                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 mb-2" rows="4"></textarea>
                                <div class="flex gap-2">
                                    <button
                                        @click="
                                        fetch('{{ route('notes.update', $note) }}', {
                                            method: 'PATCH',
                                            headers: { 
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}', 
                                                'Content-Type': 'application/json', 
                                                'Accept': 'application/json' 
                                            },
                                            body: JSON.stringify({ body: body, title: '{{ addslashes($note->title) }}' })
                                        })
                                        .then(res => {
                                            if (res.ok) {
                                                editing = false;
                                                originalBody = body;
                                                checkOverflow(); // Recalculate button visibility
                                            } else { alert('Save failed'); }
                                        })
                                    "
                                        class="bg-blue-600 text-white px-4 py-1.5 rounded-md text-sm font-medium hover:bg-blue-700">
                                        Save
                                    </button>
                                    <button @click="editing = false; body = originalBody"
                                        class="text-gray-500 text-sm hover:underline">
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 italic">No active notes. Time to write something!</p>
                    @endforelse

                    <div class="mt-6">
                        {{ $notes->links() }}
                    </div>
                </div>
            </div>
        </div>
    </x-app-layout>
