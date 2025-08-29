@extends('admin.layouts.master')
@section('title', 'Dashboard')
@section('content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Product List</h3>
                </div>
                <div class="col-sm-6">
                    <div class="btn-group float-end" role="group">
                        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">Add Product</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <!-- Search Form -->
            <form action="{{ route('admin.products.index') }}" method="GET" class="mb-3">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search by product name..."
                        value="{{ request('search') }}">
                    <button class="btn btn-outline-secondary" type="submit">Search</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th style="width: 50px;"></th>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr class="product-row">
                                <td>
                                    <button class="btn btn-sm btn-outline-primary toggle-lots-btn" data-id="{{ $product->id }}" data-url="{{ route('admin.products.fetchLots', $product->id) }}">
                                        <i class="bi bi-plus-lg"></i>
                                    </button>
                                </td>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    @if ($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" width="100">
                                    @else
                                        No Image
                                    @endif
                                </td>
                                <td><a href="{{ route('admin.products.show', $product->id) }}">{{ $product->name }}</a></td>
                                <td>
                                    <a href="{{ route('admin.products.qr', $product->id) }}" class="btn btn-sm btn-info" title="Download Main QR"><i class="bi bi-qr-code"></i></a>
                                    <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                    <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            <tr class="lots-container-row" style="display: none;">
                                <td colspan="5">
                                    <div class="p-3" id="lots-for-{{ $product->id }}">
                                        {{-- AJAX content will be loaded here --}}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No products found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $products->links() }}
        </div>
    </div>

    <!-- QR Code Modal -->
    <div class="modal fade" id="qrCodeModal" tabindex="-1" aria-labelledby="qrCodeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="qrCodeModalLabel">QR Code</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div id="qrCodeImageContainer">
                        {{-- QR Code image will be loaded here --}}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="printQrCode()">Print</button>
                    <a href="#" id="downloadQrBtn" class="btn btn-primary" download>Download</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function printQrCode() {
        let qrImage = document.getElementById('qrCodeImageContainer').innerHTML;
        let printWindow = window.open('', '_blank', 'height=500,width=500');
        printWindow.document.write('<html><head><title>Print QR Code</title></head><body style="text-align:center;">');
        printWindow.document.write(qrImage);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.print();
    }

    $(document).ready(function() {
        // Handle clicking the plus/minus button to show/hide lots
        $('.toggle-lots-btn').on('click', function() {
            const button = $(this);
            const productId = button.data('id');
            const url = button.data('url');
            const icon = button.find('i');
            const lotsContainerRow = button.closest('tr').next('.lots-container-row');
            const lotsDiv = $(`#lots-for-${productId}`);

            // If the row is visible, hide it and reset the button
            if (lotsContainerRow.is(':visible')) {
                lotsContainerRow.hide();
                icon.removeClass('bi-dash-lg').addClass('bi-plus-lg');
            } else {
                // Show loading state
                icon.removeClass('bi-plus-lg').addClass('bi-arrow-clockwise anim-spin');
                lotsDiv.html('<p class="text-center">Loading lots...</p>');
                lotsContainerRow.show();

                // Perform AJAX call
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(data) {
                        icon.removeClass('bi-arrow-clockwise anim-spin').addClass('bi-dash-lg');
                        let content = '<h6 class="mb-2">Associated Lots:</h6>';
                        if (data.length > 0) {
                            content += '<ul class="list-group">';
                            $.each(data, function(index, lot) {
                                content += `<li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span><strong>Lot #${lot.number}:</strong> ${lot.description || 'N/A'}</span>
                                                <button class="btn btn-sm btn-outline-secondary show-qr-btn"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#qrCodeModal"
                                                        data-product-name="${button.closest('tr').find('td:nth-child(4)').text()}"
                                                        data-qr-image-url="{{ route('admin.products.qrImage', ['lot' => lot.id]) }}"
                                                        data-qr-download-url="{{ route('admin.products.qrDownload', ['lot' => lot.id]) }}">
                                                    <i class="bi bi-qr-code-scan"></i> Show QR
                                                </button>
                                            </li>`;
                            });
                            content += '</ul>';
                        } else {
                            content += '<p>No lots found for this product.</p>';
                        }
                        lotsDiv.html(content);
                    },
                    error: function() {
                        icon.removeClass('bi-arrow-clockwise anim-spin').addClass('bi-plus-lg');
                        lotsDiv.html('<p class="text-center text-danger">Failed to load lots.</p>');
                    }
                });
            }
        });

        // Handle showing the QR code in the modal
        $(document).on('click', '.show-qr-btn', function() {
            const productName = $(this).data('product-name');
            const imageUrl = $(this).data('qr-image-url');
            const downloadUrl = $(this).data('qr-download-url');

            $('#qrCodeModalLabel').text(`QR Code for ${productName}`);
            $('#qrCodeImageContainer').html(`<img src="${imageUrl}" alt="QR Code" class="img-fluid">`);
            $('#downloadQrBtn').attr('href', downloadUrl);
        });
    });
</script>
<style>
    .anim-spin {
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
@endpush
