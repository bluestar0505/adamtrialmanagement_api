<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Auth;
use Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $user = User::where('email', $request->email)
            ->where('user_type', $request->user_type)
            ->whereNull('deleted_at')
            ->first();

        if(is_object($user)) {
            if (Hash::check($request->password, $user->password)) {
                Auth::login($user);
                $token = $user->createToken('AccessToken')->plainTextToken;
                return response()->json([
                    'code' => 'success',
                    'authToken' => $token,
                    'userType' => $user->user_type,
                    'userName' => $user->name,
                ], 200);
            } else {
                return response()->json([
                    'code' => 'failed',
                    'error' => 'パスワードが一致しません。'
                ], 200);
            }
        } else {
            return response()->json([
                'code' => 'failed',
                'error' => '一致するユーザーが存在しません。'
            ], 200);
        }
    }

    public function user(Request $request){
        return response()->json(
            [
                $request->user()->name,
                $request->user()->email,
            ]
        );
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'ログアウトしました。'], 200);
    }
}
