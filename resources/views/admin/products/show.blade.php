@extends('admin.layouts.master')
@section('title', 'Dashboard')
@section('content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Product Details</h3>
                </div>
                <div class="col-sm-6">
                    <div class="btn-group float-end" role="group">
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Back to List</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ $product->name }}</h5> <br>

                    @if ($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid my-3"
                            alt="{{ $product->name }}" style="max-width: 300px;">
                    @endif

                    <h6 class="mt-4">Lots Associated with this Product:</h6>
                    @if ($product->lots->isNotEmpty())
                        <ul class="list-group">
                            @foreach ($product->lots as $lot)
                                <li class="list-group-item">
                                    <strong>Lot Number:</strong> {{ $lot->number }} <br>
                                    <strong>Description:</strong> {{ $lot->description ?? 'N/A' }}
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p>No lots found for this product.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
