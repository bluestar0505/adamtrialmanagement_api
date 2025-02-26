<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ProductService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Auth;
use Hash;

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
                    $product['quote_status'] = $obj_product->quote_status;
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
            'material' => 'required|string|max:255',
            'quantity' => 'required|numeric|max:10000',
            'desired_delivery_date' => 'required|date',
            'reply_due_date' => 'required|date',
            'comment' => 'required|string:1000',
            'memo' => 'required|string:1000',
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
            'material' => 'required|string|max:255',
            'quantity' => 'required|numeric|max:10000',
            'desired_delivery_date' => 'required|date',
            'reply_due_date' => 'required|date',
            'comment' => 'required|string:1000',
            'memo' => 'required|string:1000',
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
}
