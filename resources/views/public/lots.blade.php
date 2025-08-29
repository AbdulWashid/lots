@dd($lot)
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lot Information for: {{ $product->name }}</title>

    <link rel="stylesheet" href="{{ asset('css/fontsource.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/overlayscrollbars.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/adminlte.css') }}" />

    <style>
        body {
            background-color: #f4f6f9;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 1rem;
        }
    </style>
</head>

<body class="hold-transition">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Lot Information for: {{ $product->name }}</h4>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        @if ($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid mb-3"
                                alt="{{ $product->name }}" style="max-width: 250px;">
                        @endif

                        @if ($product->lots->isNotEmpty())
                            <ul class="list-group">
                                @foreach ($product->lots as $lot)
                                    <li class="list-group-item">
                                        <strong>Lot Number:</strong> {{ $lot->number }} <br>
                                        <strong>Description:</strong>
                                        {{ $lot->description ?? 'No description available.' }}
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p>No lot information is available for this product.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/popper.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/overlayscrollbars.min.js') }}"></script>
    <script src="{{ asset('js/adminlte.js') }}"></script>
</body>

</html>
