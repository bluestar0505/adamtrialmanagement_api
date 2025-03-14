<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Buyer;
use App\Models\Product;
use App\Models\User;
use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Auth;
use Hash;
use Illuminate\Validation\Rule;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $auth_user = $request->user();
        if($auth_user && ($auth_user->user_type == config('const.user_type.system_admin'))) {
            $total_cnt = 0;
            $current_page = 1;
            $users = [];

            $per_page = $request->input('perPage', 10);
            $sortColumn = $request->query('sort', 'request_date');
            $sortOrder = $request->query('direction', 'desc');

            $condition = [
                'name' => $request->input('name'),
                'email' => $request->input('email'),
            ];

            $obj_users = UserService::doSearch($condition)
                ->where(function ($q) {
                    $q->where('user_type', config('const.user_type.system_admin'))
                        ->orWhere('user_type', config('const.user_type.buyer'));
                })
                ->orderBy($sortColumn, $sortOrder)->paginate($per_page);

            if($obj_users->count() > 0) {
                $current_page = $obj_users->currentPage();
                $total_cnt = $obj_users->total();
                foreach ($obj_users as $obj_user) {
                    $user = [];
                    $user['id'] = $obj_user->id;
                    $user['management_no'] = $obj_user->buyer ? $obj_user->buyer->management_no : '';
                    $user['name'] = $obj_user->name;
                    $user['email'] = $obj_user->email;
                    $user['role'] = config('const.user_type_code.'.$obj_user->user_type) ;
                    $users[] = $user;
                }
            }
            return response()->json([
                'code' => 'success',
                'total_cnt' => $total_cnt,
                'current_page' => $current_page,
                'per_page' => $per_page,
                'users' => $users,
            ]);
        } else {
            return response()->json([
                'code' => 'failed',
                'error' => 'auth_error',
                'message' => 'このページにアクセスする権限がありません。',
            ]);
        }
    }

    public function store(Request $request){
        $request_data = $request->all();
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|'. Rule::unique('users')->whereNull('deleted_at'),
            'password' => 'required|confirmed|min:8',
            'user_type' => 'required',
        ], [], [
            'name' => '担当者名',
            'email' => '担当者メールアドレス',
            'user_type' => 'ロール',
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
                $user->user_type = $request_data['user_type'];
                $user->buyer_id = $buyer->id;
                $user->save();
            }

            return response()->json([
                'code' => 'success',
            ]);
        }
    }

    public function detail(Request $request, User $user)
    {
        if($user) {
            $arr_users = [
                'management_no' => $user->buyer->management_no,
                'name' => $user->name,
                'email' => $user->email,
                'user_type'=> $user->user_type,
            ];
            return response()->json([
                'code' => 'success',
                'users' => $arr_users,
            ]);
        } else {
            return response()->json([
                'code' => 'failed',
                'message' => "存在しないデータです。",
            ]);
        }
    }
    public function update(Request $request, User $user)
    {
        $request_data = $request->all();
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|'. Rule::unique('users')->whereNull('deleted_at')->ignore($user->id),
            'password' => 'nullable|confirmed|min:8',
            'user_type' => 'required',
        ], [], [
            'name' => '担当者名',
            'email' => '担当者メールアドレス',
            'user_type' => 'ロール',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 'failed',
                'errors' => $validator->errors()
            ], 200);
        } else {
            $buyer = $user->buyer;
            if($buyer) {
                $buyer->fill($request_data);
                if($buyer->save()) {
                    $user->name = $buyer->name;
                    $user->email = $buyer->email;
                    if(isset($request_data['password']) && $request_data['password']) {
                        $user->password = Hash::make($request_data['password']);;
                    }
                    $user->user_type = $request_data['user_type'];
                    $user->buyer_id = $buyer->id;
                    $user->save();
                }

            } else {
                return response()->json([
                    'code' => 'failed',
                    'message' => "存在しないバイヤーです。",
                ], 200);
            }

            return response()->json([
                'code' => 'success',
            ]);
        }
    }

    public function delete(Request $request, User $user)
    {
        if($user->buyer) {
            $user->buyer->delete();
        }
        $user->delete();
        return response()->json([
            'code' => 'success',
        ]);
    }

}
