<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Jobs\LogsQueue;
use App\Models\DeviceToken;
use App\Models\LoginLogs;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     * @throws ValidationException
     */
    public function store(LoginRequest $request): \Illuminate\Http\JsonResponse
    {
        return $this->requestToken($request);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Logout successful'
        ]);
    }

    /**
     * @throws ValidationException
     */
    private function requestToken($request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'message' => ['The provided credentials are incorrect.'],
            ]);
        }

        $response = $this->authenticated($request, $user);

        $this->saveDeviceToken($response);

        $response = Arr::except($response, ['device_name', 'device_token']);

        return response()->json($response);
    }

    private function saveDeviceToken(array $data): void
    {
        $user = User::with('deviceTokens')->findOrFail($data['user']['id']);
        $user->deviceTokens->filter(function ($item) use ($data) {
            return $item->device_token == $data['device_token'];
        })->map(function ($item) {
            return $item->delete();
        });

        $device_info = DeviceToken::create([
            'device_token' => $data['device_token'],
            'access_token_id' => explode('|', $data['token'])[0],
            'device_name' => $data['device_name'],
            'user_id' => $data['user']['id']
        ]);

        $device_info->save();
    }

    private function authenticated(Request $request, $user): array
    {
        $device = $request->device_name == null ? 'default' : $request->device_name;

        $device_token = $request->device_token == null ? '' : $request->device_token;

        return [
            'user' => $user,
            'token' => $user->createToken($device)->plainTextToken,
            'device_token' => $device_token,
            'device_name' => $device
        ];
    }
}
