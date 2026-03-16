<x-app-layout>
    <div class="bg-gray-100 dark:bg-gray-900 min-h-screen text-gray-900 dark:text-gray-100 font-sans antialiased">

        <main class="max-w-4xl w-full mx-auto px-6 py-8 flex flex-col gap-8">

            <header class="flex justify-between items-end">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">My Notes</h1>
                    <p class="text-gray-400 text-sm">Capture your ideas, tasks, and daily thoughts.</p>
                </div>
            </header>

            <section x-data="{ open: false }"
                class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 flex justify-between items-center border-b dark:border-gray-700">
                    <h2 class="text-lg font-semibold dark:text-white">
                        {{ request('archived') ? 'Archived Notes' : 'Your Active Notes' }}
                    </h2>
                    <button @click="open = !open"
                        class="bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium py-1.5 px-3 rounded-md transition-colors flex items-center gap-2">
                        <span x-text="open ? '- Cancel' : '+ New Note'"></span>
                    </button>
                </div>

                <div class="px-6 border-b dark:border-gray-700 flex gap-6">
                    <a href="{{ route('dashboard', ['archived' => 0]) }}"
                        class="py-3 text-sm font-medium {{ !request('archived') ? 'text-blue-500 border-b-2 border-blue-500' : 'text-gray-400 border-b-2 border-transparent' }}">
                        Active
                    </a>
                    <a href="{{ route('dashboard', ['archived' => 1]) }}"
                        class="py-3 text-sm font-medium {{ request('archived') ? 'text-blue-500 border-b-2 border-blue-500' : 'text-gray-400 border-b-2 border-transparent' }}">
                        Archived
                    </a>
                </div>

                <div x-show="open" x-cloak x-transition class="p-6 dark:bg-gray-850/50">
                    <form action="{{ route('notes.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium dark:text-gray-300 mb-1">Title</label>
                            <input type="text" name="title" required
                                class="w-full bg-white dark:bg-gray-900 border dark:border-gray-700 border-gray-200 rounded-lg py-2 px-3 dark:text-white focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm shadow-sm">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium dark:text-gray-300 mb-1">Body</label>
                            <textarea name="body" rows="4" required
                                class="w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg py-2 px-3 dark:text-white focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm resize-none shadow-sm"></textarea>
                        </div>
                        <button type="submit"
                            class="bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium py-2 px-4 rounded-lg transition-colors shadow-sm">
                            Save Note
                        </button>
                    </form>
                </div>
            </section>

            <form action="{{ route('dashboard') }}" method="GET" class="relative">
                <input type="hidden" name="archived" value="{{ request('archived', 0) }}">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search titles, content, or AI summaries..."
                    class="block w-full pl-11 pr-4 py-3 dark:bg-gray-800 border dark:border-gray-700 border-gray-200 rounded-xl text-gray-200 placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm shadow-sm">
            </form>

            <div class="flex flex-col gap-4">
                @forelse ($notes as $note)
                    <article x-data="{
                        editing: false,
                        expanded: false,
                        loading: false,
                        body: @js($note->body),
                        title: @js($note->title),
                        tldr: @js($note->tldr)
                    }"
                        class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 border-gray-200 shadow-sm overflow-hidden dark:hover:border-gray-600 transition-all duration-200">

                        <div class="p-5">
                            <div x-show="!editing">
                                <div @click="expanded = !expanded" class="cursor-pointer group">
                                    <div class="flex justify-between items-start mb-2">
                                        <h3 class="text-lg font-semibold dark:text-white group-hover:text-blue-400 transition-colors"
                                            x-text="title"></h3>
                                        <i class="fa-solid fa-chevron-down text-[10px] text-gray-500 mt-2 transition-transform duration-300"
                                            :class="expanded ? 'rotate-180' : ''"></i>
                                    </div>

                                    <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed whitespace-pre-line"
                                        :class="expanded ? '' : 'line-clamp-3'" x-text="body"></p>
                                </div>

                                <template x-if="tldr">
                                    <div
                                        class="mt-4 p-3 dark:bg-blue-500/10 bg-blue-500/20 border-l-2 border-blue-500 rounded-r-lg">
                                        <p class="text-[10px] font-bold text-blue-400 uppercase tracking-widest mb-1">AI
                                            TL;DR</p>
                                        <p class="text-gray-500 dark:text-gray-300 text-sm italic leading-relaxed"
                                            x-text="tldr"></p>
                                    </div>
                                </template>
                            </div>

                            <div x-show="editing" x-cloak class="space-y-3">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-xs font-semibold text-blue-500 tracking-wider uppercase">Editing
                                        Note</span>
                                </div>
                                <input x-model="title"
                                    class="w-full dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg py-2 px-3a text-gray-800 dark:text-white focus:ring-1 focus:ring-blue-500 sm:text-sm font-medium shadow-sm">
                                <textarea x-model="body" rows="6"
                                    class="w-full dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg py-2 px-3 text-gray-800 dark:text-gray-300 focus:ring-1 focus:ring-blue-500 sm:text-sm  shadow-sm"></textarea>
                                <div class="flex items-center gap-3 pt-2">
                                    <button
                                        @click="
                            fetch('{{ route('notes.update', $note) }}', {
                                method: 'PATCH',
                                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' },
                                body: JSON.stringify({ body: body, title: title })
                            }).then(res => res.ok ? editing = false : alert('Error saving'))
                            "
                                        class="bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium py-1.5 px-4 rounded-md transition-colors">
                                        Save Changes
                                    </button>
                                    <button @click="editing = false"
                                        class="text-gray-400 hover:text-gray-200 text-sm font-medium">Cancel</button>
                                </div>
                            </div>
                        </div>

                        <div
                            class="px-5 py-3 border-t dark:border-gray-700 dark:bg-gray-800/50 flex items-center gap-6 text-sm dark:text-gray-400 text-gray-600">
                            <button @click="editing = true"
                                class="hover:text-blue-400 flex items-center gap-2 transition-colors">
                                <i class="fa-solid fa-pen text-xs"></i> Edit
                            </button>

                            <button
                                @click="
                    loading = true;
                    fetch('{{ route('notes.summarize', $note) }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                    })
                    .then(res => res.json())
                    .then(data => { tldr = data.note.tldr; loading = false; })
                    .catch(() => { loading = false; })
                    "
                                :disabled="loading || tldr"
                                class="hover:text-blue-400 flex items-center gap-2 transition-colors disabled:opacity-30">
                                <i class="fa-solid fa-wand-magic-sparkles text-xs"
                                    :class="loading ? 'animate-pulse' : ''"></i>
                                <span x-text="loading ? 'Thinking...' : (tldr ? 'Summarized' : 'Summarize')"></span>
                            </button>

                            <div class="flex-1"></div>

                            <form action="{{ route('notes.archive', $note) }}" method="POST">
                                @csrf @method('PATCH')
                                <button {{ request('archived') ? 'disabled' : '' }}
                                    class="flex items-center gap-2 transition-colors {{ request('archived') ? 'text-gray-600 cursor-not-allowed opacity-50' : 'hover:text-red-400' }}">
                                    <i class="fa-solid fa-box-archive text-xs"></i>
                                    {{ request('archived') ? 'Archived' : 'Archive' }}
                                </button>
                            </form>
                        </div>
                    </article>
                @empty
                    <div class="text-center py-12 bg-gray-800 rounded-xl border border-gray-700 border-dashed">
                        <i class="fa-solid fa-note-sticky text-gray-600 text-4xl mb-3"></i>
                        <p class="text-gray-500 italic">No notes found. Create your first one above!</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $notes->links() }}
            </div>

        </main>
    </div>
</x-app-layout>
