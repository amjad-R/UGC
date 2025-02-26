<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class ForgotPasswordController extends Controller
{
    public function sendResetLinkEmail(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
            ]);

            $status = Password::sendResetLink($request->only('email'));
            if ($status === Password::RESET_LINK_SENT) {
                return response()->json(['message' => __('رابط إعادة تعيين كلمة المرور تم إرساله بنجاح إلى بريدك الإلكتروني.')], 200);
            }

            return response()->json(['error' => __('فشل في إرسال رابط إعادة تعيين كلمة المرور، تأكد من صحة البريد الإلكتروني.')], 400);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->validator->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => __('حدث خطأ غير متوقع، يرجى المحاولة لاحقًا.')], 500);
        }
    }
}
