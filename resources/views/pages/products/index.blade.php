@extends('layouts.app')

@section('title', $pageTitle ?? 'All Products — Hamdha Clothing')

@section('content')
<div class="container-page py-8">
    <h1 class="section-title">{{ $pageTitle ?? 'ALL PRODUCTS' }}</h1>
    <p class="section-subtitle">{{ $productCount ?? 0 }} PRODUCTS</p>
    <p class="mt-4 text-text-medium">Product listing page coming soon...</p>
</div>
@endsection