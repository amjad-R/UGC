<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نموذج تسجيل الدخول</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center">تسجيل الدخول</h2>
        <form id="loginForm" action="{{ route('login') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="email">البريد الإلكتروني:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">كلمة المرور:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">تسجيل الدخول</button>
            <div class="mt-3">
                <a href="{{ route('password.email') }}">نسيت كلمة المرور؟</a>
            </div>
        </form>

        <div class="text-center mt-4">
            <p>أو سجل الدخول باستخدام:</p>
            <a href="{{ route('google.login') }}" class="btn btn-danger">تسجيل الدخول بواسطة جوجل</a>
            <a href="{{ route('facebook.login') }}" class="btn btn-primary">تسجيل الدخول بواسطة فيسبوك</a>
            <a href="{{ route('instagram.login') }}" class="btn btn-info">تسجيل الدخول بواسطة إنستغرام</a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
