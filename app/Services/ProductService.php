<?php
namespace App\Services;

use App\Models\Product;
use App\Models\ProductSupplier;
use App\Models\Quote;
use Carbon\Carbon;
use Storage;
use Hash;
use Auth;
class ProductService
{
    public static function doSearch($condition=[], $sort='created_at', $direction='desc') {
        $products = Product::orderBy($sort, $direction);
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

        list($prefix, $day, $seq) = explode('-', $latest_product_no);
        $current_day = Carbon::now()->format('ymd');
        if($prefix && $day && $seq) {
            if($day == $current_day) {
                $new_seq =  (int)$seq + 1;
                $number_length = strlen($new_seq) > 3 ? strlen($new_seq) : 3;
                $new_seq = str_pad($new_seq, $number_length, "0", STR_PAD_LEFT);
                return "RFQ-{$current_day}-{$new_seq}";
            }
        }
        return "RFQ-{$current_day}-001";
    }


    public static function doRequestQuotesSearch($condition=[]) {
        $products = ProductSupplier::orderByDesc('requests.created_at')
            ->leftJoin('requests', function ($join) {
                $join->on('requests.id', 'request_suppliers.request_id');
            })
            ->leftJoin('quotes', function ($join) {
                $join->on('request_suppliers.request_id', '=', 'quotes.request_id')
                    ->on('request_suppliers.supplier_id', '=', 'quotes.supplier_id');
            });

        if(isset($condition['management_no'])) {
            $products->where('requests.management_no', 'like', "%{$condition['management_no']}%");
        }
        if(isset($condition['product_name'])) {
            $products->where('requests.product_name', 'like', "%{$condition['product_name']}%");
        }
        if(isset($condition['request_date'])) {
            $products->whereDate('requests.request_date', $condition['request_date']);
        }

        $products->select([
            'requests.id as id',
            'requests.management_no as management_no',
            'requests.important as important',
            'requests.product_name as product_name',
            'requests.reply_due_date as reply_due_date',
            'requests.request_date as request_date',
            'quotes.id as quote_id',
            'quotes.total_amount as total_amount',
            'quotes.delivery_date as delivery_date',
            'quotes.is_accepted as is_accepted',
            'quotes.is_sent as is_sent',
            'quotes.order_file as order_file',
            'quotes.drawing_file as drawing_file'
        ]);
        return $products;
    }

    public static function getHPQuoteStatus($quote) {
        $limited_date = Carbon::parse($quote->reply_due_date);
        $today = Carbon::today();
        if($quote->quote_id) {
            if($quote->is_accepted == config('const.accept_status.accepted')) {
                return config('const.quote_status.accepted');
            } else if($quote->is_accepted == config('const.accept_status.rejected')){
                return config('const.quote_status.rejected');
            } else {
                if ($today->isBefore($limited_date)) {
                    if($quote->is_accepted == config('const.accept_status.returned')){
                        return config('const.quote_status.returned');
                    }
                    if($quote->is_sent) {
                        return config('const.quote_status.quoted');
                    } else {
                        return config('const.quote_status.waiting');
                    }
                } else {
                    return config('const.quote_status.limited');
                }
            }
        } else {
            if ($today->isBefore($limited_date)) {
                return config('const.quote_status.waiting');
            } else {
                return config('const.quote_status.limited');
            }
        }
    }

    public static function getAdminQuoteStatus($quote) {
        if($quote->id) {
            if($quote->is_accepted == config('const.accept_status.accepted')) {
                return config('const.admin_quote_status.accepted');
            } else if($quote->is_accepted == config('const.accept_status.rejected')){
                return config('const.admin_quote_status.rejected');
            } else if($quote->is_accepted == config('const.accept_status.returned')){
                return config('const.admin_quote_status.returned');
            } else {
                if($quote->is_sent) {
                    return config('const.admin_quote_status.quoted');
                } else {
                    return config('const.admin_quote_status.waiting');
                }
            }
        } else {
            return config('const.admin_quote_status.waiting');
        }
    }

    public static function getArrQuote(Quote $quote) {
        $arr_quote = [];
        $arr_quote['quote_id'] = $quote->id;
        $arr_quote['management_no'] = $quote->obj_product ? $quote->obj_product->management_no : '';
        $arr_quote['company_name'] = $quote->obj_supplier ? $quote->obj_supplier->company_name : '';
        $arr_quote['total_amount'] = $quote->total_amount ? $quote->total_amount : '';
        $arr_quote['delivery_date'] = $quote->delivery_date? Carbon::parse($quote->delivery_date)->format('Y-m-d'):'';
        $arr_quote['quote_status'] = ProductService::getAdminQuoteStatus($quote);
        $arr_quote['is_accepted'] = $quote->is_accepted;
        $arr_quote['order_file'] = $quote->is_accepted == config('const.accept_status.accepted') ? $quote->order_file:'';
        $arr_quote['order_file_org'] = $quote->is_accepted == config('const.accept_status.accepted') ? $quote->order_file_org:'';
        $arr_quote['drawing_file'] = $quote->is_accepted == config('const.accept_status.accepted') ? $quote->drawing_file:'';
        $arr_quote['drawing_file_org'] = $quote->is_accepted == config('const.accept_status.accepted') ? $quote->drawing_file_org:'';
        return $arr_quote;
    }
}
