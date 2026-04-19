@props(['categories'])

<header class="bg-white border-b border-gray-200 sticky top-0 z-30">
    <div class="max-w-7xl mx-auto px-4 lg:px-16 py-3 flex items-center justify-between">
        <button @click="drawerOpen = true" class="lg:hidden text-text-dark">
            <x-heroicon-o-bars-3 class="w-6 h-6" />
        </button>

        <a href="{{ route('home') }}" class="flex-shrink-0">
            <img src="{{ asset('images/logo-dark.png') }}" alt="Hamdha" class="h-5 lg:h-6 w-auto">
        </a>

        <div class="hidden lg:flex flex-1 max-w-md mx-8">
            <form action="{{ route('search') }}" method="GET" class="relative w-full">
                <input type="text" name="q" placeholder=""
                       value="{{ request('q') }}"
                       class="w-full border border-gray-300 rounded-full py-2.5 px-5 pr-12 text-sm focus:outline-none focus:border-primary">
                <button type="submit"
                        class="absolute right-1 top-1/2 -translate-y-1/2 bg-primary text-white rounded-full w-9 h-9 flex items-center justify-center hover:bg-primary-hover transition">
                    <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                </button>
            </form>
        </div>

        <div class="flex items-center gap-3">
            <button @click="searchOpen = true" class="lg:hidden text-text-dark">
                <x-heroicon-o-magnifying-glass class="w-5 h-5" />
            </button>
        </div>
    </div>

    <nav class="hidden lg:block border-t border-gray-100">
        <div class="max-w-7xl mx-auto px-4 lg:px-16 py-3">
            <div class="flex items-center space-x-8 overflow-x-auto">
                <a href="{{ route('products.new-arrivals') }}" class="nav-link">New Arrivals</a>
                @foreach($categories as $cat)
                    <div class="relative group">
                        <a href="{{ route('category.show', $cat->slug) }}" class="nav-link">
                            {{ $cat->name }}
                        </a>
                        @if($cat->children->count())
                            <div class="absolute top-full left-0 mt-1 bg-white shadow-lg rounded-md border border-gray-100 py-2 px-3 min-w-[200px] opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-20">
                                @foreach($cat->children as $child)
                                    <a href="{{ route('category.show', $child->slug) }}"
                                       class="block py-2 px-2 text-sm text-text-medium hover:text-primary transition">
                                        {{ $child->name }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </nav>
</header>