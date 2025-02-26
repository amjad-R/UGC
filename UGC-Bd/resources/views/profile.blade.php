<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>صفحة البروفايل</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .profile-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow-lg">
            <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                <h1 class="mb-0">تحديث البروفايل</h1>
                <a href="{{ route('services.create') }}" class="btn btn-light text-primary">إدارة خدماتي</a>
            </div>

            <div class="card-body">
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="text-center mb-3">
                        @if($user->profile && $user->profile->profile_image)
                        <img src="{{ asset('uploads/' . $user->profile->profile_image) }}" alt="Profile Image">
                        @else
                        <img src="{{ asset('default-profile.png') }}" alt="Default Profile Image">
                        @endif
                    </div>



                    <div class="mb-3">
                        <label for="profile_image" class="form-label">تحميل صورة البروفايل:</label>
                        <input type="file" id="profile-image" name="profile_image" onchange="previewProfileImage(event)">
                        <img id="profile-image-preview" src="" alt="Profile Image" width="100" height="100">
                    </div>

                    <div class="mb-3">
                        <label for="first_name" class="form-label">الاسم الأول:</label>
                        <input type="text" id="first_name" name="first_name" class="form-control" required value="{{ old('first_name', $user->first_name ?? '') }}">
                    </div>

                    <div class="mb-3">
                        <label for="last_name" class="form-label">الاسم الأخير:</label>
                        <input type="text" id="last_name" name="last_name" class="form-control" required value="{{ old('last_name', $user->last_name ?? '') }}">
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">البريد الإلكتروني:</label>
                        <input type="email" id="email" name="email" class="form-control" required value="{{ old('email', $user->email ?? '') }}">
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">رقم الهاتف:</label>
                        <input type="text" id="phone" name="phone" class="form-control" required value="{{ old('phone', $user->phone ?? '') }}">
                    </div>

                    <!-- روابط وسائل التواصل الاجتماعي -->
                    <div class="mb-3">
                        <label for="instagram" class="form-label">رابط إنستغرام:</label>
                        <input type="url" id="instagram" name="instagram" class="form-control" value="{{ old('instagram', $user->profile->instagram ?? '') }}">
                    </div>
                    <div class="mb-3">
                        <label for="facebook" class="form-label">رابط فيسبوك:</label>
                        <input type="url" id="facebook" name="facebook" class="form-control" value="{{ old('facebook', $user->profile->facebook ?? '') }}">
                    </div>
                    <div class="mb-3">
                        <label for="tiktok" class="form-label">رابط تيكتوك:</label>
                        <input type="url" id="tiktok" name="tiktok" class="form-control" value="{{ old('tiktok', $user->profile->tiktok ?? '') }}">
                    </div>
                    <div class="mb-3">
                        <label for="youtube" class="form-label">رابط يوتيوب:</label>
                        <input type="url" id="youtube" name="youtube" class="form-control" value="{{ old('youtube', $user->profile->youtube ?? '') }}">
                    </div>

                    <div class="mb-3">
                        <label for="service_description" class="form-label">وصف الخدمة:</label>
                        <textarea id="service_description" name="service_description" class="form-control">{{ old('service_description', $user->profile->service_description ?? '') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="hashtags" class="form-label">هاشتاجات:</label>
                        <input type="text" id="hashtags" name="hashtags" class="form-control" value="{{ old('hashtags', $user->profile->hashtags ?? '') }}">
                    </div>

                    <div class="mb-3">
                        <label for="identity_verification" class="form-label">تحميل صورة الهوية (PDF):</label>
                        <input type="file" id="identity_verification" name="identity_verification" class="form-control" accept=".pdf">
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-block">تحديث المعلومات</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function previewProfileImage(event) {
            const reader = new FileReader();
            const preview = document.getElementById('profile-image-preview');

            if (preview && event.target.files[0]) {
                reader.onload = function() {
                    preview.src = reader.result;
                };
                reader.readAsDataURL(event.target.files[0]);
            }
        }
    </script>
</body>

</html>
