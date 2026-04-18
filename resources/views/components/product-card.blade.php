@props(['product', 'currentCategory' => null])

<a href="{{ route('products.show', $product->slug) }}" class="group block">
    <div class="aspect-[4/5] overflow-hidden bg-gray-100 relative">
        @if($product->images->first())
            <img src="{{ asset('storage/' . $product->images->first()->thumbnail_path) }}"
                 alt="{{ $product->name }}"
                 class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                 loading="lazy">
        @else
            <div class="w-full h-full flex items-center justify-center text-text-light text-sm">No Image</div>
        @endif

        @if($product->has_discount)
            <span class="absolute top-2 right-2 bg-sale text-white text-[10px] font-semibold px-2 py-1 rounded">SALE</span>
        @endif
    </div>

    <div class="mt-3">
        <h3 class="text-[13px] text-text-dark leading-tight line-clamp-2">{{ $product->name }}</h3>

        <div class="mt-1">
            @if($product->has_discount)
                <span class="text-sm font-semibold text-sale">Rs. {{ number_format($product->discount_price) }}</span>
                <span class="text-xs text-text-light line-through ml-1">Rs. {{ number_format($product->price) }}</span>
            @else
                <span class="text-sm font-semibold text-text-dark">Rs. {{ number_format($product->price) }}</span>
            @endif
        </div>

        <div class="flex flex-wrap gap-1.5 mt-2">
            @foreach($product->categories as $cat)
                @if(!$currentCategory || $cat->id !== $currentCategory->id)
                    <span class="tag-pill">{{ $cat->name }}</span>
                @endif
            @endforeach
        </div>
    </div>
</a>