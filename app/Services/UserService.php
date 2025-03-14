<?php
namespace App\Services;

use App\Models\Buyer;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Storage;
use Hash;
use Auth;
class UserService
{
    public static function doSearch($condition=[]) {
        $users = User::orderByDesc('created_at');
        if(isset($condition['name'])) {
            $users->where('name', 'like', "%{$condition['name']}%");
        }

        if(isset($condition['email'])) {
            $users->where('email', 'like', "%{$condition['email']}%");
        }
        return $users;
    }

    public static function generateBuyerNo($latest_user_no=''){
        if(!$latest_user_no) {
            $latest_user_obj = Buyer::orderByDesc('id')->withTrashed()->first();
            if (is_object($latest_user_obj)) {
                $latest_user_no = $latest_user_obj->management_no;
            }
        }

        $user_no = ltrim(str_replace('T', '', $latest_user_no), '0');

        if($user_no) {
            $new_number =  (int)$user_no + 1;
            $number_length = strlen($new_number) > 4 ? strlen($new_number) : 4;
            $new_user_no = str_pad($new_number, $number_length, "0", STR_PAD_LEFT);
            $new_user_no = "T".$new_user_no;
        } else {
            $new_user_no = 'T0001';

        }
        return $new_user_no;
    }

}
