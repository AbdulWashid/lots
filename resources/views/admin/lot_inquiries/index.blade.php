@extends('admin.layouts.master')
@section('title', 'Lot Inquiries')
@section('content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Lot Inquiries</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Product Name</th>
                            <th>Inquirer Name</th>
                            <th>Mobile</th>
                            <th>Address</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($inquiries as $inquiry)
                            <tr>
                                <td>{{ $inquiry->id }}</td>
                                <td>
                                    {{-- Link to the product if it still exists --}}
                                    @if($inquiry->product)
                                        <a href="{{ route('admin.products.show', $inquiry->product->id) }}">{{ $inquiry->product->name }}</a>
                                    @else
                                        <span class="text-muted">Product Deleted</span>
                                    @endif
                                </td>
                                <td>{{ $inquiry->name }}</td>
                                <td>{{ $inquiry->mobile }}</td>
                                <td>{{ $inquiry->address }}</td>
                                <td>{{ $inquiry->created_at->format('d M, Y h:i A') }}</td>
                                <td>
                                    <form action="{{ route('admin.lot-inquiries.destroy', $inquiry->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this inquiry?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No inquiries found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Links -->
            <div class="d-flex justify-content-center">
                {{ $inquiries->links() }}
            </div>
        </div>
    </div>
@endsection
