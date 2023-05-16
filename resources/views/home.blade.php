@extends('layouts.app')

@section('content')
<div class="container">
    
    <div class="row justify-content-center">
               <h1>Products</h1> 
                @foreach($products as $product)
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">{{$product->name}}</h4>
                            <p class="card-text">{{$product->description}}</p>
                            <h5>₹{{$product->price}}</h5>
                            <a href="{{ route('products.show', $product->id) }}" class="btn btn-primary">Buynow</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
    </div>
</div>
@endsection
