@props(['categories'])

<div x-show="drawerOpen" x-cloak class="fixed inset-0 z-50 lg:hidden">
    <div class="fixed inset-0 bg-black/50" @click="drawerOpen = false"
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    </div>

    <div class="fixed inset-y-0 left-0 w-[280px] bg-white shadow-xl overflow-y-auto"
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full">

        <div class="flex justify-between items-center p-4 border-b border-gray-100">
            <img src="{{ asset('images/logo.svg') }}" alt="Hamdha" class="h-6">
            <button @click="drawerOpen = false" class="text-text-dark">
                <x-heroicon-o-x-mark class="w-6 h-6" />
            </button>
        </div>

        <nav class="px-6 pb-8 pt-2">
            <a href="{{ route('products.new-arrivals') }}"
               class="block py-3 text-sm font-medium tracking-widest uppercase text-text-dark border-b border-gray-100">
                New Arrivals
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('category.show', $cat->slug) }}"
                   class="block py-3 text-sm font-medium tracking-widest uppercase text-text-dark border-b border-gray-100">
                    {{ $cat->name }}
                </a>
                @foreach($cat->children as $child)
                    <a href="{{ route('category.show', $child->slug) }}"
                       class="block py-2 pl-4 text-xs tracking-wide text-text-medium border-b border-gray-50">
                        {{ $child->name }}
                    </a>
                @endforeach
            @endforeach
        </nav>
    </div>
</div>