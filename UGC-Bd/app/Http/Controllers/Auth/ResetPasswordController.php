<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ResetPasswordController extends Controller
{
    public function reset(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string|min:8|confirmed',
                'token' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {
                    $user->forceFill([
                        'password' => bcrypt($password),
                    ])->save();
                }
            );

            if ($status === Password::PASSWORD_RESET) {
                return response()->json(['message' => __('كلمة المرور أعيد تعيينها بنجاح.')], 200);
            }

            return response()->json(['error' => __('فشل في إعادة تعيين كلمة المرور، تأكد من صحة البريد الإلكتروني أو الرمز.')], 400);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->validator->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => __('حدث خطأ غير متوقع، يرجى المحاولة لاحقًا.')], 500);
        }
    }
}
