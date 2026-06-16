<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetOtpMail;
use App\Models\PasswordResetOtp;
use App\Models\User;
use App\Traits\Api\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class PasswordRecoveryController extends Controller
{
    use ApiResponse;

    public function requestReset(Request $request): JsonResponse
    {
        try {
            $request->validate(['email' => 'required|email']);

            $user = User::where('email', $request->email)->first();

            /**
             * Respuesta genérica de error
             * (no se expone si el correo existe o no)
             */
            if (!$user) {
                return $this->errorResponse(
                    'No fue posible procesar la solicitud. Intenta nuevamente más tarde.'
                );
            }

            /**
             * Verificar si ya existe un OTP activo
             */
            $activeOtp = $user->activePasswordResetOtp;

            if ($activeOtp) {
                return $this->errorResponse(
                    'Ya se ha enviado un código de recuperación. Intenta nuevamente más tarde.'
                );
            }

            /**
             * Generar OTP
             */
            $otp = random_int(100000, 999999);

            /**
             * Guardar OTP
             */
            PasswordResetOtp::create([
                'user_id'   => $user->id,
                'otp_hash'  => Hash::make($otp),
                'expires_at' => now()->addMinutes(
                    (int) config('auth.otp_expiration_minutes')
                ),
            ]);

            /**
             * Enviar correo
             */
            Mail::to($user->email)->queue(new PasswordResetOtpMail($otp));

            return $this->successResponse(
                [], 'Se ha enviado un código de recuperación a tu correo electrónico.'
            );
        }
        catch (\Throwable $e) {
            return $this->errorResponse(
                'Error al procesar la solicitud de recuperación.',
                500,
                $e
            );
        }
    }

    public function resetPassword(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'email'    => 'required|email',
                'otp'      => 'required|string',
                'password' => 'required|string|min:8',
            ]);

            /**
             * Buscar usuario
             */
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return $this->errorResponse(
                    'Usuario no encontrado.'
                );
            }

            /**
             * Obtener OTP activo
             */
            $otpRecord = $user->passwordResetOtps()
                ->active()
                ->latest()
                ->first();

            /**
             * Validar OTP
             */
            if (
                !$otpRecord ||
                !Hash::check($request->otp, $otpRecord->otp_hash)
            ) {
                return $this->errorResponse(
                    'Código inválido o expirado.'
                );
            }

            /**
             * Actualizar contraseña
             */
            $user->update([
                'password' => Hash::make($request->password),
            ]);

            /**
             * Marcar OTP como usado
             */
            $otpRecord->markAsUsed();

            /**
             * Revocar tokens activos (Sanctum)
             */
            if (method_exists($user, 'tokens')) {
                $user->tokens()->delete();
            }

            return $this->successResponse(
                [], 'La contraseña ha sido actualizada correctamente.'
            );
        }
        catch (\Throwable $e) {
            return $this->errorResponse(
                'Error al restablecer la contraseña.',
                500,
                $e
            );
        }
    }
}
