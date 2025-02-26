<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تفاصيل الخدمة</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">{{ $service->title }}</h1>
        <h5 class="text-center text-muted mb-3">{{ $service->user->first_name }} {{ $service->user->last_name }}</h5>
        <p class="text-center">{{ $service->description }}</p>

        <div class="text-center mb-4">
            @if(in_array('instagram', $service->platforms ?? []))
            <i class="fab fa-instagram fa-2x mx-2" style="color: #C13584;"></i>
            @endif
            @if(in_array('facebook', $service->platforms ?? []))
            <i class="fab fa-facebook fa-2x mx-2" style="color: #1877F2;"></i>
            @endif
            @if(in_array('tiktok', $service->platforms ?? []))
            <i class="fab fa-tiktok fa-2x mx-2" style="color: #000;"></i>
            @endif
            @if(in_array('youtube', $service->platforms ?? []))
            <i class="fab fa-youtube fa-2x mx-2" style="color: #FF0000;"></i>
            @endif
        </div>
        <ul class="list-group list-group-flush">
            @foreach ($service->services ?? [] as $serviceDetail)
            <li class="list-group-item">
                <p><i class="fas fa-tags me-2"></i><strong>نوع الخدمة:</strong> {{ $serviceDetail['type'] ?? 'غير محدد' }}</p>
                <p><i class="fas fa-dollar-sign me-2"></i><strong>السعر:</strong> {{ $serviceDetail['price'] ?? 'غير متوفر' }}</p>
                <p><i class="fas fa-clock me-2"></i><strong>الوقت:</strong> {{ $serviceDetail['time'] ?? 'غير متوفر' }}</p>
                <p><i class="fas fa-handshake me-2"></i><strong>قابل للتفاوض:</strong>
                    {{ isset($serviceDetail['negotiable']) && $serviceDetail['negotiable'] ? 'نعم' : 'لا' }}
                </p>
                <p><strong>الهاشتاجات:</strong> {{ is_array($service->hashtags) ? implode(', ', $service->hashtags) : $service->hashtags }}</p>
            </li>
            @endforeach
        </ul>

        <a href="{{ route('orders.create', ['service_id' => $service->id]) }}">إنشاء طلب</a>


        @foreach($messages as $message)
        <div class="{{ $message->sender_id === auth()->id() ? 'text-end' : 'text-start' }}">
            <p><strong>{{ $message->sender->first_name }} {{ $message->sender->last_name }}:</strong> {{ $message->content }}</p>
        </div>
        @endforeach

    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>
