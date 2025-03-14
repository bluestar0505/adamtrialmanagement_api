<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\RegisterQuoteMail;
use App\Mail\ResponseToQuoteMail;
use App\Models\Product;
use App\Models\ProductSupplier;
use App\Models\Quote;
use App\Services\ProductService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Auth;
use Hash;

class SupplierRequestController extends Controller
{
    public function index(Request $request)
    {
        $auth_user = $request->user();
        if($auth_user && ($auth_user->user_type == config('const.user_type.supplier'))) {
            $total_cnt = 0;
            $current_page = 1;
            $products = [];

            $per_page = $request->input('perPage', 10);
            $sortColumn = $request->query('sort', 'request_date');
            $sortOrder = $request->query('direction', 'desc');

            $condition = [
                'management_no' => $request->input('management_no'),
                'product_name' => $request->input('product_name'),
                'request_date' => $request->input('request_date'),
            ];

            $obj_product_suppliers = ProductService::doRequestQuotesSearch($condition)
                ->where('request_suppliers.supplier_id', $auth_user->supplier_id)
                ->orderBy($sortColumn, $sortOrder)
                ->paginate($per_page);

            if($obj_product_suppliers->count() > 0) {
                $current_page = $obj_product_suppliers->currentPage();
                $total_cnt = $obj_product_suppliers->total();

                foreach ($obj_product_suppliers as $obj_request) {
                    $product = [];
                    $product['id'] = $obj_request->id;
                    $product['management_no'] = $obj_request->management_no;
                    $product['important'] = $obj_request->important;
                    $product['product_name'] = $obj_request->product_name;
                    $product['request_date'] = Carbon::parse($obj_request->request_date)->format('Y-m-d H:i:s');
                    $product['reply_due_date'] = Carbon::parse($obj_request->reply_due_date)->format('Y-m-d');
                    $product['quote_status'] = ProductService::getHPQuoteStatus($obj_request);
                    $product['total_amount'] = $obj_request->total_amount ? "¥".number_format($obj_request->total_amount) : '';
                    $product['delivery_date'] = $obj_request->delivery_date;
                    $product['order_file'] = $obj_request->order_file;
                    $product['drawing_file'] = $obj_request->drawing_file;
                    $products[] = $product;
                }
            }
            return response()->json([
                'code' => 'success',
                'total_cnt' => $total_cnt,
                'current_page' => $current_page,
                'per_page' => $per_page,
                'requests' => $products,
            ]);
        } else {
            return response()->json([
                'code' => 'failed',
                'message' => 'このページにアクセスする権限がありません。',
            ]);
        }
    }
    public function detail(Request $request, Product $product)
    {
        $auth_user = $request->user();
        if($auth_user && ($auth_user->user_type == config('const.user_type.supplier'))) {
            if($product) {
                $arr_product = [];
                $arr_quote = [];
                $obj_quote = Quote::where('supplier_id', $auth_user->supplier_id)
                    ->leftJoin('requests', function ($join) {
                        $join->on('requests.id', 'quotes.request_id');
                    })
                    ->where('request_id', $product->id)
                    ->select([
                        'requests.id as id',
                        'quotes.products as products',
                        'requests.management_no as management_no',
                        'requests.important as important',
                        'requests.product_name as product_name',
                        'requests.reply_due_date as reply_due_date',
                        'requests.request_date as request_date',
                        'quotes.id as quote_id',
                        'quotes.total_amount as total_amount',
                        'quotes.delivery_date as delivery_date',
                        'quotes.is_sent as is_sent',
                        'quotes.is_accepted as is_accepted'
                    ])
                    ->first();
                $arr_quote = [];
                if($obj_quote) {
                    $arr_quote = $obj_quote->attributesToArray();
                    unset($arr_quote['products']);
                    $arr_product = $obj_quote->products ? json_decode($obj_quote->products, true) : [];
                }
                if($obj_quote) {
                    $quote_status = ProductService::getHPQuoteStatus($obj_quote);
                } else {
                    $quote_status = ProductService::getHPQuoteStatus($product);
                }

                return response()->json([
                    'code' => 'success',
                    'request' => $product->attributesToArray(),
                    'quote' =>$arr_quote,
                    'products' =>$arr_product,
                    'quote_status' => $quote_status
                ]);
            } else {
                return response()->json([
                    'code' => 'failed',
                    'message' => "存在しないデータです。",
                ]);
            }
        } else {
            return response()->json([
                'code' => 'failed',
                'message' => 'このページにアクセスする権限がありません。',
            ]);
        }
    }
    public function update(Request $request, Product $product)
    {
        $auth_user = $request->user();
        if($auth_user && ($auth_user->user_type == config('const.user_type.supplier'))) {
            $request_data = $request->all();
            $validator = Validator::make($request->all(), [
                'total_amount' => 'required|string|max:255',
                'delivery_date' => 'required|date',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'code' => 'failed',
                    'errors' => $validator->errors()
                ], 200);
            } else {
                $quote = Quote::where('supplier_id', $auth_user->supplier_id)
                    ->where('request_id', $product->id)
                    ->first();
                if(!$quote) {
                    $quote = new Quote();
                }
                $quote->fill($request_data);
                $quote->request_id = $product->id;
                $quote->supplier_id = $auth_user->supplier_id;
                $quote->products = json_encode($request_data['products'], true);
                $quote->save();

                return response()->json([
                    'code' => 'success',
                ]);
            }
        } else {
            return response()->json([
                'code' => 'failed',
                'message' => 'このページにアクセスする権限がありません。',
            ]);
        }
    }

    public function sendQuote(Request $request, Product $product)
    {
        $auth_user = $request->user();
        if($auth_user && ($auth_user->user_type == config('const.user_type.supplier'))) {
            $quote = Quote::where('supplier_id', $auth_user->supplier_id)
                ->where('request_id', $product->id)
                ->first();
            $quote->is_sent = config('const.quote_sent');
            $quote->is_accepted = config('const.accept_status.default');
            /*メール送信*/
            if($quote->save()){
                if($quote->obj_supplier && $quote->obj_product && $quote->obj_product->obj_buyer) {
                    try {
                        Mail::to($quote->obj_supplier->contact_email)->send(new RegisterQuoteMail($quote->obj_supplier, $quote->obj_product, $quote, $quote->obj_product->obj_buyer));
                    } catch (\Exception $exception) {
                        Log::error("EMail Sending Failed: {$quote->obj_supplier->id}-{$quote->obj_supplier->contact_email} ");
                        Log::error($exception->getMessage());
                    }
                }
            }
            return response()->json([
                'code' => 'success',
            ]);
        } else {
            return response()->json([
                'code' => 'failed',
                'message' => 'このページにアクセスする権限がありません。',
            ]);
        }
    }

    public function delete(Request $request, Product $product)
    {
        $product->delete();
        return response()->json([
            'code' => 'success',
        ]);
    }


    public function selectSuppliers(Request $request, Product $product)
    {
        $supplier_ids = $request->input('supplier_ids');
        if(is_object($product) && $supplier_ids) {
            $arr_ids = [];
            if(is_array($supplier_ids)) {
                $arr_ids = $supplier_ids;
            } else {
                $arr_ids[] = $supplier_ids;
            }
            $records = [];
            foreach ($arr_ids as $id) {
                $record = [
                    'request_id' => $product->id,
                    'supplier_id' => $id,
                ];
                $records[] = $record;
            }
            $product->product_suppliers()->createMany($records);
            return response()->json([
                'ids' => $arr_ids,
                'code' => 'success',
            ]);
        } else {
            return response()->json([
                'code' => 'failed',
            ]);
        }

    }

    public function getSelectedSuppliers(Request $request, Product $product)
    {
        if(is_object($product)) {
            $arr_ids = ProductSupplier::where('request_id', $product->id)
                ->pluck('supplier_id')->toArray();
            return response()->json([
                'ids' => $arr_ids,
                'code' => 'success',
            ]);
        } else {
            return response()->json([
                'code' => 'failed',
            ]);
        }
    }

    public function getQuotes(Request $request, Product $product)
    {
        if(is_object($product)) {
            $arr_ids = ProductSupplier::where('request_id', $product->id)
                ->pluck('supplier_id')->toArray();
            return response()->json([
                'ids' => $arr_ids,
                'code' => 'success',
            ]);
        } else {
            return response()->json([
                'code' => 'failed',
            ]);
        }
    }
}
