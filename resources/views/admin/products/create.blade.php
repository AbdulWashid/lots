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

                {{-- In create.blade.php and edit.blade.php --}}

                <div class="mb-3">
                    <label for="imageUpload" class="form-label">Product Image</label>
                    {{-- This input is just for selecting the file --}}
                    <input type="file" class="form-control @error('image') is-invalid @enderror" id="imageUpload"
                        accept="image/*">

                    {{-- This hidden input will hold the cropped Base64 data and will be submitted --}}
                    <input type="hidden" name="image" id="croppedImageData">

                    @error('image')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror

                    {{-- A preview container for the cropped image --}}
                    <div class="mt-3">
                        <img id="imagePreview" src="#" alt="Image Preview" class="img-thumbnail"
                            style="display: none; max-width: 200px;">
                    </div>
                </div>


                <div class="modal fade" id="cropperModal" tabindex="-1" aria-labelledby="cropperModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="cropperModalLabel">Crop Image</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
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
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
@endsection
@push('scripts')
    {{-- 1. Include the CKEditor script from the CDN --}}
    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
    <script>
        $(document).ready(function() {
            let lotIndex = 0;
            // Object to hold all editor instances, so we can manage them
            let lotEditors = {};

            $("#add-lot-btn").click(function() {
                // Give the textarea a unique ID using the lotIndex
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

                // 2. Initialize CKEditor on the new textarea
                ClassicEditor
                    .create(document.querySelector(`#${uniqueId}`))
                    .then(editor => {
                        // Save the editor instance so we can destroy it later if needed
                        lotEditors[lotIndex] = editor;
                    })
                    .catch(error => {
                        console.error(error);
                    });

                lotIndex++;
            });

            // Remove lot entry
            $("#lots-container").on("click", ".remove-lot-btn", function() {
                let lotEntry = $(this).closest(".lot-entry");
                let indexToRemove = lotEntry.data('index');

                // 3. Destroy the CKEditor instance before removing the element
                if (lotEditors[indexToRemove]) {
                    lotEditors[indexToRemove].destroy()
                        .then(() => {
                            delete lotEditors[indexToRemove]; // Remove from our object
                            lotEntry.remove(); // Remove the HTML
                        })
                        .catch(error => {
                            console.error(error);
                        });
                } else {
                    lotEntry.remove(); // Fallback for safety
                }
            });
        });

        $(document).ready(function() {
            const imageUpload = document.getElementById('imageUpload');
            const imageToCrop = document.getElementById('imageToCrop');
            const cropButton = document.getElementById('cropButton');
            const croppedImageDataInput = document.getElementById('croppedImageData');
            const imagePreview = document.getElementById('imagePreview');
            const cropperModal = new bootstrap.Modal(document.getElementById('cropperModal'));
            let cropper;

            // 1. When a file is selected
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

            // 2. When the modal is shown, initialize the cropper
            document.getElementById('cropperModal').addEventListener('shown.bs.modal', function() {
                if (cropper) {
                    cropper.destroy();
                }
                cropper = new Cropper(imageToCrop, {
                    aspectRatio: 1 / 1, // You can change this (e.g., 16 / 9)
                    viewMode: 1,
                    dragMode: 'move',
                    background: false,
                });
            });

            // 3. When the "Crop" button is clicked
            cropButton.addEventListener('click', function() {
                const canvas = cropper.getCroppedCanvas({
                    width: 500, // Desired output width
                    height: 500, // Desired output height
                });

                canvas.toBlob(function(blob) {
                    const reader = new FileReader();
                    reader.readAsDataURL(blob);
                    reader.onloadend = function() {
                        const base64data = reader.result;

                        // Set the value of the hidden input
                        croppedImageDataInput.value = base64data;

                        // Show a preview of the cropped image
                        imagePreview.src = base64data;
                        imagePreview.style.display = 'block';

                        // Hide the modal
                        cropperModal.hide();
                    };
                }, 'image/jpeg'); // You can change the format to 'image/png'
            });

            // 4. Clean up when the modal is hidden
            document.getElementById('cropperModal').addEventListener('hidden.bs.modal', function() {
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }
                // Clear the file input so the user can select the same file again if they cancel
                imageUpload.value = '';
            });
        });
    </script>
@endpush
