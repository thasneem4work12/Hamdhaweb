@props(['items' => [], 'phone' => ''])

<div class="bg-primary text-white text-[11px] lg:text-xs tracking-wide">
    <div class="max-w-7xl mx-auto flex items-center justify-between px-4 py-2">
        @foreach($items as $item)
            <span class="hidden sm:inline">{{ $item }}</span>
        @endforeach
        <span>{{ $phone }}</span>
        <span class="hidden lg:inline">LK</span>
    </div>
</div>