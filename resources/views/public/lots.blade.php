<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lot Details</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}" />

    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .card {
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        .card-header {
            background-color: #007bff;
            color: white;
            border-bottom: none;
            border-radius: 10px 10px 0 0;
        }
        .card-body {
            padding: 2rem;
        }
        .info-label {
            font-weight: bold;
            color: #495057;
        }
        .info-value {
            font-size: 1.1rem;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="col-md-6 offset-md-3">
            <div class="card">
                <div class="card-header text-center">
                    <h4>Lot Details</h4>
                </div>
                <div class="card-body">
                    <p class="info-label">Lot ID:</p>
                    <p class="info-value">{{ $lot->id }}</p>

                    <p class="info-label mt-4">Description:</p>
                    <p>{!! $lot->description ?? 'No description available.' !!}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- JS (optional) -->
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
</body>
</html>
