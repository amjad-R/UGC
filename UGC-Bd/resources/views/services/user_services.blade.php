<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الخدمات المنشورة</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>الخدمات المنشورة من قبل المستخدم</h1>

        @if($services->isEmpty())
            <p>لا توجد خدمات منشورة لهذا المستخدم.</p>
        @else
            <div class="row">
                @foreach($services as $service)
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title">{{ $service->title }}</h5>
                                <p class="card-text">{{ $service->description }}</p>
                                <p><strong>المنصات:</strong> {{ implode(', ', $service->platforms) }}</p>
                                <p><strong>الهاشتاجات:</strong> {{ $service->hashtags }}</p>

                                <h6>أنواع الخدمات:</h6>
                                <ul>
                                    @foreach($service->services as $type => $details)
                                        <li>
                                            نوع: {{ $details['type'] }},
                                            السعر: {{ $details['price'] }},
                                            الوقت: {{ $details['time'] }},
                                            قابل للتفاوض: {{ isset($details['negotiable']) && $details['negotiable'] == '1' ? 'نعم' : 'لا' }}
                                        </li>
                                    @endforeach
                                </ul>

                                <a href="{{ route('services.edit', $service->id) }}" class="btn btn-primary">تعديل</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</body>
</html>
