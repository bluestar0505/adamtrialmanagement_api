<?php
namespace App\Services;

use App\Models\Product;
use App\Models\Supplier;
use Carbon\Carbon;
use Storage;
use Hash;
use Auth;
class SupplierService
{
    public static function doSearch($condition=[]) {
        $suppliers = Supplier::orderByDesc('created_at');
        if(isset($condition['company_name'])) {
            $suppliers->where('company_name', 'like', "%{$condition['company_name']}%");
        }

        if(isset($condition['contact_name'])) {
            $suppliers->where('contact_name', 'like', "%{$condition['contact_name']}%");
        }
        return $suppliers;
    }

    public static function generateSupplierNo($latest_supplier_no=''){
        if(!$latest_supplier_no) {
            $latest_supplier_obj = Supplier::orderByDesc('id')->withTrashed()->first();
            if (is_object($latest_supplier_obj)) {
                $latest_supplier_no = $latest_supplier_obj->management_no;
            }
        }

        $supplier_no = ltrim(str_replace('S', '', $latest_supplier_no), '0');

        if($supplier_no) {
            $new_number =  (int)$supplier_no + 1;
            $number_length = strlen($new_number) > 4 ? strlen($new_number) : 4;
            $new_supplier_no = str_pad($new_number, $number_length, "0", STR_PAD_LEFT);
            $new_supplier_no = "S".$new_supplier_no;
        } else {
            $new_supplier_no = 'S0001';

        }
        return $new_supplier_no;
    }

}
