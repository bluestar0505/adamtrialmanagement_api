<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\RequestQuoteSupplierMail;
use App\Mail\ResponseToQuoteMail;
use App\Models\Product;
use App\Models\ProductSupplier;
use App\Models\Quote;
use App\Models\Supplier;
use App\Services\ProductService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Auth;
use Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ProductsController extends Controller
{


    public function index(Request $request)
    {
        $auth_user = $request->user();
        if($auth_user && ($auth_user->user_type != config('const.user_type.supplier'))) {
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

            $obj_products = ProductService::doSearch($condition)->orderBy($sortColumn, $sortOrder)->paginate($per_page);
            if($obj_products->count() > 0) {
                $current_page = $obj_products->currentPage();
                $total_cnt = $obj_products->total();
                foreach ($obj_products as $obj_product) {
                    $product = [];
                    $product['id'] = $obj_product->id;
                    $product['management_no'] = $obj_product->management_no;
                    $product['important'] = $obj_product->important;
                    $product['product_name'] = $obj_product->product_name;
                    $product['request_date'] = Carbon::parse($obj_product->request_date)->format('Y-m-d H:i:s');
                    $product['reply_due_date'] = Carbon::parse($obj_product->reply_due_date)->format('Y-m-d');
                    $product['request_status'] = $obj_product->request_status;
                    $product['supplier'] = $obj_product->selected_supplier_name;
                    $product['unit_price'] = $obj_product->selected_unit_price;
                    $product['quantity'] = $obj_product->selected_quantity;
                    $product['total_amount'] = $obj_product->selected_total_amount;
                    $product['delivery_date'] = $obj_product->selected_delivery_date;
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

    public function store(Request $request){
        $request_data = $request->all();
        $validator = Validator::make($request->all(), [
            'product_name' => 'required|string|max:255',
            'desired_delivery_date' => 'required|date',
            'reply_due_date' => 'required|date',
            'comment' => 'nullable|string:1000',
            'memo' => 'nullable|string:1000',
            'important' => 'required|numeric',
            'd2_file' => 'required|file|max:102400', //100M
            'd3_file' => 'required|file|max:102400', //100M
        ], [
            'important.numeric' => "優先フラグを選択してください。"
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 'failed',
                'errors' => $validator->errors()
            ], 200);
        } else {
            if ($request->file('d2_file')) {
                $file = $request->file('d2_file');
                $original_filename = $file->getClientOriginalName();
                $new_filename = '2d_' . date("YmdHis") . uniqid(). '.'. $file->getClientOriginalExtension();
                $file->storeAs('uploads', $new_filename, 'public');
                $request_data['data_2d'] = $new_filename;
                $request_data['data_2d_org'] = $original_filename;
            }
            if ($request->file('d3_file')) {
                $file = $request->file('d3_file');
                $original_filename = $file->getClientOriginalName();
                $new_filename = '3d_' . date("YmdHis") . uniqid(). '.'. $file->getClientOriginalExtension();
                $file->storeAs('uploads', $new_filename, 'public');
                $request_data['data_3d'] = $new_filename;
                $request_data['data_3d_org'] = $original_filename;
            }
            $request_data['buyer_id'] = $request->user()->id;
            $request_data['management_no'] = ProductService::generateProductNo();
            $request_data['request_date'] = Carbon::now()->format('Y-m-d H:i:s');
            $request_data['desired_delivery_date'] = Carbon::parse($request_data['desired_delivery_date'])->format('Y-m-d');
            $request_data['reply_due_date'] = Carbon::parse($request_data['reply_due_date'])->format('Y-m-d');

            $product = new Product();
            $product->fill($request_data);
            $product->save();

            return response()->json([
                'code' => 'success',
            ]);
        }
    }

    public function detail(Request $request, Product $product)
    {
        if($product) {
            return response()->json([
                'code' => 'success',
                'requests' => $product->attributesToArray(),
            ]);
        } else {
            return response()->json([
                'code' => 'failed',
                'message' => "存在しないデータです。",
            ]);
        }
    }
    public function update(Request $request, Product $product)
    {
        $request_data = $request->all();
        $validator = Validator::make($request->all(), [
            'product_name' => 'required|string|max:255',
            'desired_delivery_date' => 'required|date',
            'reply_due_date' => 'required|date',
            'comment' => 'nullable|string:1000',
            'memo' => 'nullable|string:1000',
            'd2_file' => 'nullable|file|max:102400', //100M
            'd3_file' => 'nullable|file|max:102400', //100M
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 'failed',
                'errors' => $validator->errors()
            ], 200);
        } else {
            if ($request->file('d2_file')) {
                $file = $request->file('d2_file');
                $original_filename = $file->getClientOriginalName();
                $new_filename = '2d_' . date("YmdHis") . uniqid(). '.'. $file->getClientOriginalExtension();
                $file->storeAs('uploads', $new_filename, 'public');
                $request_data['data_2d'] = $new_filename;
                $request_data['data_2d_org'] = $original_filename;
            }
            if ($request->file('d3_file')) {
                $file = $request->file('d3_file');
                $original_filename = $file->getClientOriginalName();
                $new_filename = '3d_' . date("YmdHis") . uniqid(). '.'. $file->getClientOriginalExtension();
                $file->storeAs('uploads', $new_filename, 'public');
                $request_data['data_3d'] = $new_filename;
                $request_data['data_3d_org'] = $original_filename;
            }
            $request_data['desired_delivery_date'] = Carbon::parse($request_data['desired_delivery_date'])->format('Y-m-d');
            $request_data['reply_due_date'] = Carbon::parse($request_data['reply_due_date'])->format('Y-m-d');

            $product->fill($request_data);
            $product->save();

            return response()->json([
                'code' => 'success',
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
            foreach ($arr_ids as $supplier_id) {
                $obj_supplier = Supplier::where('id', $supplier_id)->first();
                if($obj_supplier) {
                    try {
                        Mail::to($obj_supplier->contact_email)->send(new RequestQuoteSupplierMail($obj_supplier, $product));
                    } catch (\Exception $exception) {
                        Log::error("EMail Sending Failed: {$obj_supplier->id}-{$obj_supplier->contact_email} ");
                        Log::error($exception->getMessage());
                    }
                }
            }
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

    public function getQuotes(Request $request, Product $product=null)
    {
        $per_page = $request->input('perPage', 10);
        $sortColumn = $request->query('sort', 'quotes.id');
        $sortOrder = $request->query('direction', 'desc');
        $condition = [
            'management_no' => $request->input('management_no'),
            'product_name' => $request->input('product_name'),
            'request_date' => $request->input('request_date'),
        ];

        $total_cnt = 0;
        $current_page = 1;
        $quotes = [];

        $obj_quotes = ProductSupplier::orderByDesc('quotes.created_at')
            ->orderBy($sortColumn, $sortOrder)
            ->leftJoin('requests', function ($join) {
                $join->on('requests.id', 'request_suppliers.request_id');
            })
            ->leftJoin('suppliers', function ($join) {
                $join->on('suppliers.id', 'request_suppliers.supplier_id');
            })
            ->leftJoin('quotes', function ($join) {
                $join->on('request_suppliers.request_id', '=', 'quotes.request_id')
                    ->on('request_suppliers.supplier_id', '=', 'quotes.supplier_id');
            });

        if(isset($condition['management_no'])) {
            $obj_quotes->where('requests.management_no', 'like', "%{$condition['management_no']}%");
        }
        if(isset($condition['product_name'])) {
            $obj_quotes->where('requests.product_name', 'like', "%{$condition['product_name']}%");
        }
        if(isset($condition['request_date'])) {
            $obj_quotes->whereDate('requests.request_date', $condition['request_date']);
        }

        if(is_object($product)) {
            $obj_quotes->where('request_suppliers.request_id', $product->id);
        }

        $obj_quotes = $obj_quotes->select([
            'quotes.id as id',
            'requests.id as request_id',
            'requests.management_no as management_no',
            'requests.product_name as product_name',
            'suppliers.company_name as company_name',
            'quotes.created_at as answered_at',
            'quotes.total_amount as total_amount',
            'quotes.is_accepted as is_accepted',
            'quotes.is_sent as is_sent'
        ])
        ->paginate($per_page);

        if($obj_quotes->count() > 0) {
            $current_page = $obj_quotes->currentPage();
            $total_cnt = $obj_quotes->total();
            foreach ($obj_quotes as $obj_quote) {
                $quote = [];
                $quote['quote_id'] = $obj_quote->id;
                $quote['request_id'] = $obj_quote->request_id;
                $quote['management_no'] = $obj_quote->management_no;
                $quote['product_name'] = $obj_quote->product_name;
                $quote['company_name'] = $obj_quote->company_name;
                $quote['answered_at'] = $obj_quote->answered_at? Carbon::parse($obj_quote->answered_at)->format('Y-m-d'):'';
                $quote['quote_status'] = ProductService::getAdminQuoteStatus($obj_quote);
                $quote['total_amount'] = $obj_quote->total_amount ? "¥".number_format($obj_quote->total_amount) : '';
                $quotes[] = $quote;
            }
        }
        return response()->json([
            'code' => 'success',
            'total_cnt' => $total_cnt,
            'current_page' => $current_page,
            'per_page' => $per_page,
            'quotes' =>$quotes,
        ]);
    }

    public function quoteDetail(Request $request, Quote $quote)
    {
        $arr_quote = ProductService::getArrQuote($quote);
        $products = $quote->products ? json_decode($quote->products, true) : [];
        return response()->json([
            'code' => 'success',
            'quote' =>$arr_quote,
            'products' => $products
        ]);
    }

    public function changeQuoteStatus(Request $request, Quote $quote)
    {
        $status = $request->input('status');
        if(is_object($quote)) {
            $quote->is_accepted = config('const.accept_status.' . $status);
            $quote->save();

            $arr_quote = ProductService::getArrQuote($quote);
            $products = $quote->products ? json_decode($quote->products, true) : [];

            if($quote->obj_supplier && $quote->obj_product) {
                try {
                    Mail::to($quote->obj_supplier->contact_email)->send(new ResponseToQuoteMail($quote->obj_supplier, $quote->obj_product, $quote));
                } catch (\Exception $exception) {
                    Log::error("EMail Sending Failed: {$quote->obj_supplier->id}-{$quote->obj_supplier->contact_email} ");
                    Log::error($exception->getMessage());
                }
            }

            return response()->json([
                'code' => 'success',
                'quote' =>$arr_quote,
                'products' => $products
            ]);
        } else {
            return response()->json([
                'code' => 'failed',
                'message' => "存在しないデータです。",
            ]);
        }
    }
    public function fileUpload(Request $request, Quote $quote){
        $request_data = $request->all();
        $validator = Validator::make($request->all(), [
            'order_file' => 'nullable|file|max:102400', //100M
            'drawing_file' => 'nullable|file|max:102400', //100M
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 'failed',
                'errors' => $validator->errors()
            ], 200);
        } else {
            if ($request->file('order_file')) {
                $file = $request->file('order_file');
                $original_filename = $file->getClientOriginalName();
                $new_filename = 'order_' . date("YmdHis") . uniqid(). '.'. $file->getClientOriginalExtension();
                $file->storeAs('quotes', $new_filename, 'public');
                $request_data['order_file'] = $new_filename;
                $request_data['order_file_org'] = $original_filename;
            }
            if ($request->file('drawing_file')) {
                $file = $request->file('drawing_file');
                $original_filename = $file->getClientOriginalName();
                $new_filename = 'drawing_' . date("YmdHis") . uniqid(). '.'. $file->getClientOriginalExtension();
                $file->storeAs('quotes', $new_filename, 'public');
                $request_data['drawing_file'] = $new_filename;
                $request_data['drawing_file_org'] = $original_filename;
            }
            $quote->fill($request_data);
            $quote->save();

            return response()->json([
                'code' => 'success',
            ]);
        }
    }

}
