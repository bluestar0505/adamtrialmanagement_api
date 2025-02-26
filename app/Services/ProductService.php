<?php
namespace App\Services;

use App\Models\Product;
use Carbon\Carbon;
use Storage;
use Hash;
use Auth;
class ProductService
{
    public static function doSearch($condition=[]) {
        $products = Product::orderByDesc('created_at');
        if(isset($condition['management_no'])) {
            $products->where('management_no', 'like', "%{$condition['management_no']}%");
        }

        if(isset($condition['product_name'])) {
            $products->where('product_name', 'like', "%{$condition['product_name']}%");
        }

        if(isset($condition['request_date'])) {
            $products->whereDate('request_date', $condition['request_date']);
        }
        return $products;
    }

    public static function generateProductNo($latest_product_no=''){
        if(!$latest_product_no) {
            $latest_product_obj = Product::orderByDesc('id')->first();
            if (is_object($latest_product_obj)) {
                $latest_product_no = $latest_product_obj->management_no;
            }
        }

        list($prefix, $year, $seq) = explode('-', $latest_product_no);
        $current_year = Carbon::now()->format('Y');
        if($prefix && $year && $seq) {
            if($year == $current_year) {
                $new_seq =  (int)$seq + 1;
                $number_length = strlen($new_seq) > 3 ? strlen($new_seq) : 3;
                $new_seq = str_pad($new_seq, $number_length, "0", STR_PAD_LEFT);
                return "RFQ-{$current_year}-{$new_seq}";
            }
        }
        return "RFQ-{$current_year}-001";
    }

}
