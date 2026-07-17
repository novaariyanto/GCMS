<?php

namespace App\Http\Controllers\Api\V1;

use App\Application\Auth\Services\AuthService;
use App\Application\Auth\Services\OtpService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\LoginRequest;
use App\Http\Requests\Api\V1\RegisterRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $auth,
        private readonly OtpService $otps,
    ) {}

    /**
     * @OA\Post(
     *     path="/api/v1/auth/login",
     *     tags={"Auth"},
     *     summary="Masuk ke API GCMS",
     *
     *     @OA\RequestBody(required=true, @OA\JsonContent(required={"email","password"}, @OA\Property(property="email", type="string", format="email"), @OA\Property(property="password", type="string"))),
     *
     *     @OA\Response(response=200, description="Berhasil masuk"),
     *     @OA\Response(response=401, description="Kredensial tidak valid")
     * )
     */
    #[OA\Post(
        path: '/api/v1/auth/login',
        summary: 'Masuk ke API GCMS',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email'),
                    new OA\Property(property: 'password', type: 'string'),
                ],
                type: 'object'
            )
        ),
        tags: ['Auth'],
        responses: [
            new OA\Response(response: 200, description: 'Berhasil masuk'),
            new OA\Response(response: 401, description: 'Kredensial tidak valid'),
        ]
    )]
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();
        $result = $this->auth->login(
            $credentials['email'],
            $credentials['password'],
            $request->ip(),
            $request->userAgent(),
        );

        return $this->respondWithToken($result['token'], $result['user']->load(['opd', 'unit']));
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/register",
     *     tags={"Auth"},
     *     summary="Daftar akun masyarakat",
     *
     *     @OA\Response(response=201, description="Akun berhasil dibuat")
     * )
     */
    #[OA\Post(
        path: '/api/v1/auth/register',
        summary: 'Daftar akun masyarakat',
        tags: ['Auth'],
        responses: [
            new OA\Response(response: 201, description: 'Akun berhasil dibuat'),
        ]
    )]
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->safe()->except('password_confirmation');
        $user = $this->auth->register($data);
        $token = auth('api')->login($user);

        return $this->respondWithToken($token, $user->load(['opd', 'unit']), 201);
    }

    public function logout(): JsonResponse
    {
        $this->auth->logout();

        return response()->json([
            'message' => 'Berhasil keluar.',
        ]);
    }

    public function refresh(): JsonResponse
    {
        $token = auth('api')->refresh();

        return $this->respondWithToken($token, auth('api')->user()?->load(['opd', 'unit']));
    }

    /**
     * @OA\Get(
     *     path="/api/v1/auth/me",
     *     tags={"Auth"},
     *     summary="Profil pengguna aktif",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(response=200, description="Profil pengguna")
     * )
     */
    #[OA\Get(
        path: '/api/v1/auth/me',
        summary: 'Profil pengguna aktif',
        security: [['bearerAuth' => []]],
        tags: ['Auth'],
        responses: [
            new OA\Response(response: 200, description: 'Profil pengguna'),
        ]
    )]
    public function me(): UserResource
    {
        return new UserResource(auth('api')->user()->load(['opd', 'unit']));
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $status = Password::sendResetLink($data);

        return response()->json([
            'message' => __($status),
        ], $status === Password::RESET_LINK_SENT ? 200 : 422);
    }

    public function sendOtp(Request $request): JsonResponse
    {
        $data = $request->validate([
            'destination' => ['required', 'string', 'max:255'],
            'channel' => ['required', 'in:email,whatsapp,sms'],
            'purpose' => ['required', 'in:login,register,reset'],
        ]);

        $user = User::query()
            ->where('email', $data['destination'])
            ->orWhere('phone', $data['destination'])
            ->first();

        $otp = $this->otps->send($data['destination'], $data['channel'], $data['purpose'], $user?->id);

        return response()->json([
            'message' => 'Kode OTP berhasil dibuat.',
            'expires_at' => $otp->expires_at->toISOString(),
            'debug_code' => config('app.debug') ? $otp->code : null,
        ]);
    }

    public function verifyOtp(Request $request): JsonResponse
    {
        $data = $request->validate([
            'destination' => ['required', 'string', 'max:255'],
            'purpose' => ['required', 'in:login,register,reset'],
            'code' => ['required', 'string', 'max:10'],
        ]);

        if (! $this->otps->verify($data['destination'], $data['code'], $data['purpose'])) {
            throw ValidationException::withMessages([
                'code' => ['Kode OTP tidak valid atau sudah kedaluwarsa.'],
            ]);
        }

        return response()->json([
            'message' => 'Kode OTP berhasil diverifikasi.',
            'verified' => true,
        ]);
    }

    private function respondWithToken(string $token, ?User $user, int $status = 200): JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => $user ? new UserResource($user) : null,
        ], $status);
    }
}
