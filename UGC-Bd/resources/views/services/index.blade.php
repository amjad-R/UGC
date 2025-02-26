<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>قائمة الخدمات</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        .card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
            transition: transform 0.2s;
        }

        .card:hover {
            transform: scale(1.05);
        }

        .card-title {
            font-weight: bold;
            color: #004085;
        }

        .social-icons i {
            font-size: 1.2rem;
            color: #495057;
            margin-right: 10px;
        }

        .social-icons i.instagram {
            color: #C13584;
        }

        .social-icons i.facebook {
            color: #1877F2;
        }

        .social-icons i.tiktok {
            color: #000;
        }

        .social-icons i.youtube {
            color: #FF0000;
        }

        .btn-primary {
            background-color: #004085;
            border-color: #004085;
            transition: background-color 0.2s;
        }

        .btn-primary:hover {
            background-color: #003066;
            border-color: #003066;
        }
    </style>
</head>

<body>

    <div class="container mt-5">
        <h1 class="text-center mb-4">قائمة الخدمات</h1>
        <div class="row">
            @foreach($listings as $listing)
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">{{ $listing->title }}</h5>

                        <h6 class="card-subtitle mb-2 text-muted">
                            {{ $listing->user->first_name }} {{ $listing->user->last_name }}
                        </h6>

                        <div class="social-icons mb-3">
                            @if(in_array('instagram', $listing->platforms))
                            <i class="fab fa-instagram instagram"></i>
                            @endif
                            @if(in_array('facebook', $listing->platforms))
                            <i class="fab fa-facebook facebook"></i>
                            @endif
                            @if(in_array('tiktok', $listing->platforms))
                            <i class="fab fa-tiktok tiktok"></i>
                            @endif
                            @if(in_array('youtube', $listing->platforms))
                            <i class="fab fa-youtube youtube"></i>
                            @endif
                        </div>

                        <p class="card-text">{{ \Illuminate\Support\Str::limit($listing->description, 100) }}</p>
                        <a href="{{ route('services.show', $listing->id) }}" class="btn btn-primary w-100">عرض المزيد</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>
