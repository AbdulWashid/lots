@extends('admin.layouts.master')
@section('title', 'Dashboard')
@push('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css" rel="stylesheet">
    <style>
        .ck-editor__editable {
            min-height: 150px;
        }
    </style>
@endpush
@section('content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Edit Product: {{ $product->name }}</h3>
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
                    <label for="imageUpload" class="form-label">Product Image</label>
                    <p><small>Upload a new image to replace the current one.</small></p>

                    <input type="file" class="form-control @error('image') is-invalid @enderror" id="imageUpload"
                        accept="image/*">

                    <input type="hidden" name="image" id="croppedImageData">

                    @error('image')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror

                    <div class="mt-3">
                        <img id="imagePreview"
                             src="{{ $product->image ? asset('storage/' . $product->image) : '#' }}"
                             alt="Image Preview" class="img-thumbnail"
                             style="display: {{ $product->image ? 'block' : 'none' }}; max-width: 200px;">
                    </div>
                </div>

                <hr>
                <h4>Lots</h4>
                <div id="lots-container">
                    @foreach ($product->lots as $index => $lot)
                        <div class="lot-entry row mb-3 align-items-center" data-index="{{ $index }}">
                            <div class="col-md-5">
                                <input type="text" name="lots[{{ $index }}][number]" class="form-control"
                                    placeholder="Lot Number" value="{{ $lot->number }}" required>
                            </div>
                            <div class="col-md-5">
                                <textarea id="description-{{$index}}" name="lots[{{ $index }}][description]" class="form-control" placeholder="Lot Description">{{ $lot->description }}</textarea>
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
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>

    <div class="modal fade" id="cropperModal" tabindex="-1" aria-labelledby="cropperModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cropperModalLabel">Crop Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div>
                        <img id="imageToCrop" src="" style="max-width: 100%;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="cropButton">Crop</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
    <script>
        $(document).ready(function() {
            // --- CKEditor and Lots Logic ---
            let lotIndex = {{ $product->lots->count() }};
            let lotEditors = {};

            // Initialize CKEditor for existing textareas
            @foreach($product->lots as $index => $lot)
                ClassicEditor
                    .create(document.querySelector(`#description-{{$index}}`))
                    .then(editor => {
                        lotEditors[{{$index}}] = editor;
                    })
                    .catch(error => {
                        console.error(error);
                    });
            @endforeach

            $("#add-lot-btn").click(function() {
                let uniqueId = `description-${lotIndex}`;
                let lotFields = `
                <div class="lot-entry row mb-3 align-items-center" data-index="${lotIndex}">
                    <div class="col-md-5">
                        <input type="text" name="lots[${lotIndex}][number]" class="form-control" placeholder="Lot Number" required>
                    </div>
                    <div class="col-md-5">
                        <textarea id="${uniqueId}" name="lots[${lotIndex}][description]" class="form-control" placeholder="Lot Description"></textarea>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger remove-lot-btn">Remove</button>
                    </div>
                </div>`;
                $("#lots-container").append(lotFields);

                ClassicEditor
                    .create(document.querySelector(`#${uniqueId}`))
                    .then(editor => {
                        lotEditors[lotIndex] = editor;
                    })
                    .catch(error => {
                        console.error(error);
                    });
                lotIndex++;
            });

            $("#lots-container").on("click", ".remove-lot-btn", function() {
                let lotEntry = $(this).closest(".lot-entry");
                let indexToRemove = lotEntry.data('index');
                if (lotEditors[indexToRemove]) {
                    lotEditors[indexToRemove].destroy()
                        .then(() => {
                            delete lotEditors[indexToRemove];
                            lotEntry.remove();
                        })
                        .catch(error => console.error(error));
                } else {
                    lotEntry.remove();
                }
            });

            // --- Cropper JS Logic ---
            const imageUpload = document.getElementById('imageUpload');
            const imageToCrop = document.getElementById('imageToCrop');
            const cropButton = document.getElementById('cropButton');
            const croppedImageDataInput = document.getElementById('croppedImageData');
            const imagePreview = document.getElementById('imagePreview');
            const cropperModal = new bootstrap.Modal(document.getElementById('cropperModal'));
            let cropper;

            imageUpload.addEventListener('change', function(e) {
                const files = e.target.files;
                if (files && files.length > 0) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        imageToCrop.src = event.target.result;
                        cropperModal.show();
                    };
                    reader.readAsDataURL(files[0]);
                }
            });

            document.getElementById('cropperModal').addEventListener('shown.bs.modal', function() {
                if (cropper) {
                    cropper.destroy();
                }
                cropper = new Cropper(imageToCrop, {
                    aspectRatio: 1 / 1,
                    viewMode: 1,
                });
            });

            cropButton.addEventListener('click', function() {
                const canvas = cropper.getCroppedCanvas({
                    width: 500,
                    height: 500,
                });
                canvas.toBlob(function(blob) {
                    const reader = new FileReader();
                    reader.readAsDataURL(blob);
                    reader.onloadend = function() {
                        const base64data = reader.result;
                        croppedImageDataInput.value = base64data;
                        imagePreview.src = base64data;
                        imagePreview.style.display = 'block';
                        cropperModal.hide();
                    };
                }, 'image/jpeg');
            });

            document.getElementById('cropperModal').addEventListener('hidden.bs.modal', function() {
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }
                imageUpload.value = '';
            });
        });
    </script>
@endpush
