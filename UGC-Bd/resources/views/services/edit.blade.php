<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل الخدمة</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h1>تعديل الخدمة</h1>
        <form action="{{ route('services.update', $service->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="title">العنوان</label>
                <input type="text" name="title" id="title" class="form-control" value="{{ $service->title }}" required>
            </div>

            <div class="form-group">
                <label for="description">الوصف</label>
                <textarea name="description" id="description" class="form-control" rows="4">{{ $service->description }}</textarea>
            </div>

            <div class="form-group">
                <label for="platforms">المنصات</label><br>
                <input type="checkbox" name="platforms[]" value="facebook" {{ in_array('facebook', $service->platforms) ? 'checked' : '' }}> فيسبوك
                <input type="checkbox" name="platforms[]" value="instagram" {{ in_array('instagram', $service->platforms) ? 'checked' : '' }}> إنستجرام
                <input type="checkbox" name="platforms[]" value="tiktok" {{ in_array('tiktok', $service->platforms) ? 'checked' : '' }}> تيك توك
                <input type="checkbox" name="platforms[]" value="youtube" {{ in_array('youtube', $service->platforms) ? 'checked' : '' }}> يوتيوب
            </div>

            <div class="form-group">
                <label for="hashtags">الهاشتاجات</label>
                <input type="text" name="hashtags" id="hashtags" class="form-control" value="{{ $service->hashtags }}">
            </div>

            <div id="servicesContainer">
                <h4>الخدمات</h4>
                @foreach($service->services as $index => $serviceItem)
                <div class="border p-3 mt-3">
                    <h5>نوع الخدمة {{ $index + 1 }}</h5>
                    <div class="form-group">
                        <label>اختيار نوع الخدمة</label>
                        <select class="form-control" name="services[{{ $index }}][type]" required>
                            <option value="reel" {{ $serviceItem['type'] === 'reel' ? 'selected' : '' }}>ريل</option>
                            <option value="story" {{ $serviceItem['type'] === 'story' ? 'selected' : '' }}>ستوري</option>
                            <option value="post" {{ $serviceItem['type'] === 'post' ? 'selected' : '' }}>بوست</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>السعر</label>
                        <input type="number" class="form-control" name="services[{{ $index }}][price]" value="{{ $serviceItem['price'] }}" required>
                    </div>
                    <div class="form-group">
                        <label>الوقت</label>
                        <input type="text" class="form-control" name="services[{{ $index }}][time]" value="{{ $serviceItem['time'] }}" required>
                    </div>
                    <div class="form-group">
                        <label>سعر قابل للتفاوض</label>
                        <input type="checkbox" name="services[{{ $index }}][negotiable]" value="1"
                            {{ isset($serviceItem['negotiable']) && $serviceItem['negotiable'] == '1' ? 'checked' : '' }}>
                    </div>
                    <button type="button" class="btn btn-danger" onclick="removeService(this)">إزالة الخدمة</button>
                </div>
                @endforeach
            </div>

            <button type="button" class="btn btn-secondary mt-3" onclick="addService()">إضافة خدمة جديدة</button>

            <button type="submit" class="btn btn-primary mt-4">تحديث الخدمة</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        let serviceIndex = {
            {
                count($service - > services)
            }
        };

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
    </script>
</body>

</html>
