@extends('admin.layouts.master')
@section('title', 'Dashboard')
@section('content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Edit Product: {{ $product->name }}</h3>
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
            <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="name" class="form-label">Product Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                        name="name" value="{{ old('name', $product->name) }}">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label">Product Image</label>
                    <input type="file" class="form-control @error('image') is-invalid @enderror" id="image"
                        name="image">
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @if ($product->image)
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" width="150">
                            <p><small>Current Image</small></p>
                        </div>
                    @endif
                </div>

                <hr>
                <h4>Lots</h4>
                <div id="lots-container">
                    @foreach ($product->lots as $index => $lot)
                        <div class="lot-entry row mb-3">
                            <div class="col-md-5">
                                <input type="text" name="lots[{{ $index }}][number]" class="form-control"
                                    placeholder="Lot Number" value="{{ $lot->number }}" required>
                            </div>
                            <div class="col-md-5">
                                <textarea name="lots[{{ $index }}][description]" class="form-control" placeholder="Lot Description">{{ $lot->description }}</textarea>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger remove-lot-btn">Remove</button>
                            </div>
                        </div>
                    @endforeach
                </div>
                @error('lots.*.number')
                    <div class="text-danger mb-2">{{ $message }}</div>
                @enderror

                <button type="button" id="add-lot-btn" class="btn btn-secondary mb-3">Add Lot</button>
                <hr>

                <button type="submit" class="btn btn-primary">Update Product</button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-link">Cancel</a>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            let lotIndex = {{ $product->lots->count() }}; // Start index from existing lots

            $("#add-lot-btn").click(function() {
                let lotFields = `
                <div class="lot-entry row mb-3">
                    <div class="col-md-5">
                        <input type="text" name="lots[${lotIndex}][number]" class="form-control" placeholder="Lot Number" required>
                    </div>
                    <div class="col-md-5">
                        <textarea name="lots[${lotIndex}][description]" class="form-control" placeholder="Lot Description"></textarea>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger remove-lot-btn">Remove</button>
                    </div>
                </div>`;
                $("#lots-container").append(lotFields);
                lotIndex++;
            });

            // Remove lot entry
            $("#lots-container").on("click", ".remove-lot-btn", function() {
                $(this).closest(".lot-entry").remove();
            });
        });
    </script>
@endpush
