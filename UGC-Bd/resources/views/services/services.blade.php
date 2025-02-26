<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة خدمة</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <meta name="csrf-token" content="{{ csrf_token() }}">

</head>

<body>

    <div class="container mt-5">
        <h2>إضافة خدمة</h2>

        <form id="serviceForm" method="POST" action="{{ route('services.store') }}">
            @csrf

            <div class="form-group">
                <label for="title">العنوان</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>

            <div class="form-group">
                <label for="platforms">المنصات</label><br>
                <input type="checkbox" name="platforms[]" value="facebook"> فيسبوك
                <input type="checkbox" name="platforms[]" value="instagram"> إنستجرام
                <input type="checkbox" name="platforms[]" value="tiktok"> تيك توك
                <input type="checkbox" name="platforms[]" value="youtube"> يوتيوب
            </div>

            <div class="form-group">
                <label for="description">الوصف</label>
                <textarea class="form-control" id="description" name="description"></textarea>
            </div>

            <div id="servicesContainer">
                <h4>الخدمات</h4>
                <div class="form-group">
                    <label for="services">اختيار نوع الخدمة</label>
                    <select class="form-control" name="services[0][type]" required>
                        <option value="reel">ريل</option>
                        <option value="story">ستوري</option>
                        <option value="post">بوست</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="price">السعر</label>
                    <input type="number" class="form-control" name="services[0][price]" required>
                </div>
                <div class="form-group">
                    <label for="time">الوقت</label>
                    <input type="text" class="form-control" name="services[0][time]" required>
                </div>
                <div class="form-group">
                    <label for="negotiable">سعر قابل للتفاوض</label>
                    <input type="checkbox" name="services[0][negotiable]" value="1">
                </div>
            </div>

            <div class="form-group">
                <label for="hashtags">الهاشتاجات</label>
                <input type="text" class="form-control" id="hashtags" name="hashtags">
            </div>

            <button type="submit" class="btn btn-primary">إضافة الخدمة</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#serviceForm').on('submit', function(event) {
                event.preventDefault();
                $.ajax({
                    url: $(this).attr('action'),
                    type: $(this).attr('method'),
                    data: $(this).serialize(),
                    success: function(response) {
                        alert(response.message);
                    },
                    error: function(xhr) {
                        alert('حدث خطأ. يرجى التحقق من المدخلات.');
                    }
                });
            });
        });
    </script>
</body>

</html>
