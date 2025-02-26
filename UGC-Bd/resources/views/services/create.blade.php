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
            </div>

            <button type="button" class="btn btn-secondary mt-3" onclick="addService()">إضافة خدمة جديدة</button>

            <div class="form-group">
                <label for="hashtags">الهاشتاجات</label>
                <input type="text" class="form-control" id="hashtags" name="hashtags">
            </div>

            <button type="submit" class="btn btn-primary">إضافة الخدمة</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        let serviceIndex = 0;

        function addService() {
            const serviceHtml = `
                <div class="border p-3 mt-3">
                    <h5>نوع الخدمة ${serviceIndex + 1}</h5>
                    <div class="form-group">
                        <label>اختيار نوع الخدمة</label>
                        <select class="form-control" name="services[${serviceIndex}][type]" required>
                            <option value="reel">ريل</option>
                            <option value="story">ستوري</option>
                            <option value="post">بوست</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>السعر</label>
                        <input type="number" class="form-control" name="services[${serviceIndex}][price]" required>
                    </div>
                    <div class="form-group">
                        <label>الوقت</label>
                        <input type="text" class="form-control" name="services[${serviceIndex}][time]" required>
                    </div>
                    <div class="form-group">
                        <label>سعر قابل للتفاوض</label>
                        <input type="checkbox" name="services[${serviceIndex}][negotiable]" value="1">
                    </div>
                    <button type="button" class="btn btn-danger" onclick="removeService(this)">إزالة الخدمة</button>
                </div>
            `;
            $('#servicesContainer').append(serviceHtml);
            serviceIndex++;
        }

        function removeService(button) {
            $(button).closest('.border').remove();
        }

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
