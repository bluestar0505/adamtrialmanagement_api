<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Buyer;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Auth;
use Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $user = User::where('email', $request->email)
            ->when($request->user_type==config('const.user_type.buyer') ,function ($q) {
                $q->where(function ($q1) {
                    $q1->where('user_type', config('const.user_type.buyer'))
                        ->orWhere('user_type', config('const.user_type.system_admin'));
                });
            })
            ->when($request->user_type==config('const.user_type.supplier') ,function ($q) {
                $q->where('user_type', config('const.user_type.supplier'));
            })
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

    public function register(Request $request){
        $request_data = $request->all();
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|confirmed|email|'. Rule::unique('users')->whereNull('deleted_at'),
            'password' => 'required|confirmed|min:8',
        ], [], [
            'name' => '担当者名',
            'email' => 'メールアドレス',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code' => 'failed',
                'errors' => $validator->errors()
            ], 200);
        } else {
            $request_data['management_no'] = UserService::generateBuyerNo();
            $buyer = new Buyer();
            $buyer->fill($request_data);
            if($buyer->save()) {
                $user = new User();
                $user->name = $buyer->name;
                $user->email = $buyer->email;
                $user->password = Hash::make($request_data['password']);;
                $user->user_type = config('const.user_type.buyer');
                $user->buyer_id = $buyer->id;
                $user->save();
            }
            return response()->json([
                'code' => 'success',
            ]);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'ログアウトしました。'], 200);
    }
}
