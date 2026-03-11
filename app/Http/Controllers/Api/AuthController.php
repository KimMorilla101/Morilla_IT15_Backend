<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();
        $user = User::query()->where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials.',
            ], 401);
        }

        $token = $user->createToken('react-app')->plainTextToken;

        return response()->json([
            'message' => 'Login successful.',
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
            'profile' => $this->buildProfilePayload($user),
        ]);
    }

    public function profile(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return response()->json([
            'user' => $user,
            'profile' => $this->buildProfilePayload($user),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        Auth::guard('web')->logout();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return response()->json([
            'message' => 'Logout successful.',
        ]);
    }

    /**
     * Build a frontend-ready profile object for sidebar/user menu components.
     *
     * @return array<string, mixed>
     */
    private function buildProfilePayload(User $user): array
    {
        $name = trim((string) $user->name);
        $nameParts = collect(preg_split('/\s+/', $name) ?: [])->filter()->values();
        $initials = $this->buildInitials($nameParts);

        return [
            'id' => $user->id,
            'name' => $name,
            'email' => $user->email,
            'role' => 'School Admin',
            'avatar' => [
                'initials' => $initials,
                'background_color' => '#A0000F',
                'text_color' => '#FFFFFF',
            ],
            'sidebar_card' => [
                'title' => $name,
                'subtitle' => $user->email,
            ],
        ];
    }

    private function buildInitials(Collection $nameParts): string
    {
        $initials = $nameParts
            ->take(2)
            ->map(fn (string $part): string => strtoupper(substr($part, 0, 1)))
            ->implode('');

        return $initials !== '' ? $initials : 'U';
    }
}



