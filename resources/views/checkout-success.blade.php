@extends('layouts.app')
  
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
  
                <div class="card-body">
  
                    <div class="alert alert-success">
                        <h3>Product purchase successfully!</h3>
                        <p>{{$customer->name}}</p>
                    </div>
                    
  
                </div>
            </div>
        </div>
    </div>
</div>
@endsection