<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->get('q', '');
        $products = collect();
        $count = 0;

        if (strlen($query) >= 2) {
            $products = Product::visible()
                ->search($query)
                ->with(['images' => fn ($q) => $q->orderBy('sort_order')->limit(1), 'categories', 'fabric'])
                ->latest()
                ->paginate(20);
            $count = $products->total();
        }

        $breadcrumbs = [
            ['label' => 'HOME', 'url' => route('home')],
            ['label' => 'SEARCH RESULTS', 'url' => null],
        ];

        return view('pages.search', compact('products', 'query', 'count', 'breadcrumbs'));
    }
}
