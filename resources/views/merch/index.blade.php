@extends('layouts.app')

@section('content')
<div class="container mx-auto py-10">
    <h1 class="text-4xl font-bold text-center mb-8">Our Merchandise</h1>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($products as $product)
            <div class="bg-white rounded-xl shadow hover:shadow-lg transition duration-300 overflow-hidden">
                <div class="relative">
                    @if($product->variants->first())
                        <img src="{{ $product->variants->first()->image ?? 'https://via.placeholder.com/400x300' }}" alt="{{ $product->name }}" class="w-full h-64 object-cover">
                    @endif
                    @if(!$product->allow_preorder)
                        <span class="absolute top-2 left-2 bg-red-600 text-white text-xs px-2 py-1 rounded">In Stock</span>
                    @else
                        <span class="absolute top-2 left-2 bg-yellow-500 text-white text-xs px-2 py-1 rounded">Preorder</span>
                    @endif
                </div>

                <div class="p-4">
                    <h2 class="text-xl font-semibold">{{ $product->name }}</h2>
                    <p class="text-gray-600 mt-1">{{ Str::limit($product->description, 80) }}</p>
                    <p class="text-indigo-600 font-bold mt-2">Ksh {{ number_format($product->base_price,2) }}</p>

                    <a href="{{ route('merch.show', $product->id) }}" class="mt-4 inline-block w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded text-center font-semibold transition">
                        View Details
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>

@endsection
