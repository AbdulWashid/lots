@extends('admin.layouts.master')
@section('title', 'Dashboard')
@section('content')

    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Product Add</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Product Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                        name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label">Product Image</label>
                    <input type="file" class="form-control @error('image') is-invalid @enderror" id="image"
                        name="image" required>
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <hr>
                <h4>Lots</h4>
                <div id="lots-container">
                </div>
                @error('lots.*.number')
                    <div class="text-danger mb-2">{{ $message }}</div>
                @enderror

                <button type="button" id="add-lot-btn" class="btn btn-secondary mb-3">Add Lot</button>
                <hr>

                <button type="submit" class="btn btn-primary">Save Product</button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-link">Cancel</a>
            </form>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            let lotIndex = 0;

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
