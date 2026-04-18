@extends('layouts.app')

@section('title', 'Search Results — Hamdha Clothing')

@section('content')
<div class="container-page py-8">
    <h1 class="section-title">SEARCH RESULTS FOR "{{ $query ?? '' }}"</h1>
    <p class="section-subtitle">{{ $count ?? 0 }} PRODUCTS</p>
    <p class="mt-4 text-text-medium">Search results page coming soon...</p>
</div>
@endsection