@extends('layouts.app')

@section('title', $product->name . ' — Hamdha Clothing')
@section('meta_description', Str::limit(strip_tags($product->description), 160))

@section('content')
@section('json_ld')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Product",
    "name": "{{ $product->name }}",
    "image": "{{ $product->images->first() ? asset('storage/' . $product->images->first()->image_path) : '' }}",
    "description": "{{ Str::limit(strip_tags($product->description), 200) }}",
    "sku": "{{ $product->model_number }}",
    "offers": {
        "@type": "Offer",
        "price": "{{ $product->discount_price ?? $product->price }}",
        "priceCurrency": "LKR",
        "availability": "https://schema.org/InStock"
    }
}
</script>
@overwrite

<div class="max-w-7xl mx-auto px-4 lg:px-16 py-6">
  <nav class="mb-6">
    <ol class="flex items-center text-xs text-text-light uppercase tracking-wider">
      @foreach($breadcrumbs as $i => $crumb)
        @if($i > 0)
          <li class="mx-2">/</li>
        @endif
        @if($crumb['url'])
          <li><a href="{{ $crumb['url'] }}" class="hover:text-text-dark">{{ $crumb['label'] }}</a></li>
        @else
          <li class="text-text-dark font-medium">{{ $crumb['label'] }}</li>
        @endif
      @endforeach
    </ol>
  </nav>

  <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 lg:gap-10">
    <div class="lg:col-span-3" x-data="{ 
      activeImage: 0,
      images: {{ $product->images->map(fn($i) => asset('storage/' . $i->image_path))->toJson() }}
    }">
      <div class="grid grid-cols-[70px_1fr] gap-3 lg:gap-4">
        <div class="flex lg:flex-col gap-2 overflow-x-auto lg:overflow-visible">
          @foreach($product->images as $i => $image)
          <button
            @click="activeImage = {{ $i }}"
            class="flex-shrink-0 w-[70px] aspect-[4/5] overflow-hidden border-2 transition"
            :class="activeImage === {{ $i }} ? 'border-primary' : 'border-transparent'"
          >
            <img src="{{ asset('storage/' . $image->thumbnail_path) }}" class="w-full h-full object-cover" loading="lazy" alt="{{ $product->name }} thumbnail">
          </button>
          @endforeach
        </div>
        <div class="aspect-[4/5] overflow-hidden bg-gray-100">
          <img :src="images[activeImage]" class="w-full h-full object-cover" alt="{{ $product->name }}">
        </div>
      </div>
    </div>

    <div class="lg:col-span-2 lg:pl-4" x-data="{ showSizeGuide: false }">
      <div class="flex flex-wrap gap-2 mb-4">
        @foreach($product->categories as $cat)
          <span class="inline-block bg-primary text-white text-[11px] font-medium tracking-wide px-3 py-1.5 rounded-sm">
            {{ strtoupper($cat->name) }}
          </span>
        @endforeach
        <span class="inline-block border border-primary text-primary text-[11px] font-medium tracking-wide px-3 py-1.5 rounded-sm">
          {{ $product->model_number }}
        </span>
      </div>

      <h1 class="text-lg lg:text-xl font-semibold text-text-dark leading-tight">
        {{ $product->name }}
      </h1>

      <div class="mt-3">
        @if($product->has_discount)
          <span class="text-xl font-bold text-sale">Rs. {{ number_format($product->discount_price) }}</span>
          <span class="text-sm text-text-light line-through ml-2">Rs. {{ number_format($product->price) }}</span>
        @else
          <span class="text-xl font-bold text-text-dark">Rs. {{ number_format($product->price) }}</span>
        @endif
      </div>

      <hr class="my-4 border-border">

      @if($product->description)
      <p class="text-sm text-text-medium leading-relaxed mb-6">{!! $product->description !!}</p>
      @endif

      <div class="space-y-2">
        <div class="flex">
          <span class="text-xs font-semibold tracking-widest uppercase text-text-dark w-24">FABRIC</span>
          <span class="text-sm text-text-medium">{{ $product->fabric->name }}</span>
        </div>
        <div class="flex">
          <span class="text-xs font-semibold tracking-widest uppercase text-text-dark w-24">COLORS</span>
          <span class="text-sm text-text-medium">{{ $product->colors ?? 'N/A' }}</span>
        </div>
        <div class="flex">
          <span class="text-xs font-semibold tracking-widest uppercase text-text-dark w-24">SIZE</span>
          @if($product->sizeCharts->count())
            <button @click="showSizeGuide = true" class="text-sm text-primary underline font-medium hover:text-primary-hover">
              SIZE GUIDE
            </button>
          @else
            <span class="text-sm text-text-medium">Standard</span>
          @endif
        </div>
      </div>

      <a href="{{ $whatsappUrl }}" target="_blank"
        class="flex items-center justify-center gap-3 w-full bg-primary text-white font-semibold text-sm tracking-wide py-4 rounded-sm mt-8 hover:bg-primary-hover transition">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.861-2.03-.961-.273-.096-.474-.048-.675.149-.2.2-.849.861-1.041 1.041-.189.2-.381.2-.678.069-.297-.149-1.255-.461-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
        PLACE ORDER VIA WHATSAPP
      </a>

      @if($product->sizeCharts->count())
      <div x-show="showSizeGuide" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4"
           @click.self="showSizeGuide = false">
        <div class="fixed inset-0 bg-black/50"></div>
        <div class="relative bg-white rounded-lg max-w-lg w-full max-h-[90vh] overflow-y-auto p-6 z-10">
          <button @click="showSizeGuide = false" class="absolute top-3 right-3 text-text-light hover:text-text-dark">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
          <h3 class="text-lg font-semibold text-text-dark mb-4">Size Guide</h3>
          <div class="space-y-4">
            @foreach($product->sizeCharts as $chart)
              <div>
                <p class="text-sm font-medium text-text-dark mb-2">{{ $chart->name }}</p>
                <img src="{{ asset('storage/' . $chart->image_path) }}" class="w-full rounded" alt="{{ $chart->name }}">
              </div>
            @endforeach
          </div>
        </div>
      </div>
      @endif
    </div>
  </div>

  @if($relatedProducts->count())
  <section class="py-16 border-t border-border mt-12">
    <div class="max-w-7xl mx-auto px-4 lg:px-16 text-center">
      <h2 class="font-heading text-2xl font-semibold tracking-[0.12em]">FEATURED DESIGNS</h2>
      <p class="text-xs tracking-[0.1em] text-text-light mt-2 uppercase">Latest Additions</p>
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-6 mt-10">
        @foreach($relatedProducts as $rp)
          <x-product-card :product="$rp" />
        @endforeach
      </div>
    </div>
  </section>
  @endif
</div>

@endsection