<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use App\Services\ProductService;
use App\Services\SupplierService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Auth;
use Hash;

class SuppliersController extends Controller
{
    public function index(Request $request)
    {
        $auth_user = $request->user();
        if($auth_user && ($auth_user->user_type != config('const.user_type.supplier'))) {
            $total_cnt = 0;
            $current_page = 1;
            $suppliers = [];

            $per_page = $request->input('perPage', 10);
            $sortColumn = $request->query('sort', 'request_date');
            $sortOrder = $request->query('direction', 'desc');

            $condition = [
                'company_name' => $request->input('company_name'),
                'contact_name' => $request->input('contact_name'),
            ];

            $obj_suppliers = SupplierService::doSearch($condition)->orderBy($sortColumn, $sortOrder)->paginate($per_page);
            if($obj_suppliers->count() > 0) {
                $current_page = $obj_suppliers->currentPage();
                $total_cnt = $obj_suppliers->total();
                foreach ($obj_suppliers as $obj_supplier) {
                    $supplier = [];
                    $supplier['id'] = $obj_supplier->id;
                    $supplier['management_no'] = $obj_supplier->management_no;
                    $supplier['company_name'] = $obj_supplier->company_name;
                    $supplier['address'] = $obj_supplier->address;
                    $supplier['phone_number'] = $obj_supplier->phone_number;
                    $supplier['contact_name'] = $obj_supplier->contact_name;
                    $supplier['contact_email'] = $obj_supplier->contact_email;
                    $suppliers[] = $supplier;
                }
            }
            return response()->json([
                'code' => 'success',
                'total_cnt' => $total_cnt,
                'current_page' => $current_page,
                'per_page' => $per_page,
                'suppliers' => $suppliers,
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
            'company_name' => 'required|string|max:255',
            'postal_code' => 'nullable|string|max:8',
            'address' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:15',
            'fax_number' => 'nullable|string|max:15',
            'contact_name' => 'required|string|max:255',
            'contact_email' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 'failed',
                'errors' => $validator->errors()
            ], 200);
        } else {

            $request_data['management_no'] = SupplierService::generateSupplierNo();

            $supplier = new Supplier();
            $supplier->fill($request_data);
            if($supplier->save()) {
                $user = new User();
                $user->email = $supplier->contact_email;
                $user->name = $supplier->contact_name;
                $user->password = Hash::make('12345678');
                $user->user_type = config('const.user_type.supplier');
                $user->supplier_id = $supplier->id;
                $user->save();
            }

            return response()->json([
                'code' => 'success',
            ]);
        }
    }

    public function detail(Request $request, Supplier $supplier)
    {
        if($supplier) {
            return response()->json([
                'code' => 'success',
                'requests' => $supplier->attributesToArray(),
            ]);
        } else {
            return response()->json([
                'code' => 'failed',
                'message' => "存在しないデータです。",
            ]);
        }
    }
    public function update(Request $request, Supplier $supplier)
    {
        $request_data = $request->all();
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:255',
            'postal_code' => 'nullable|string|max:8',
            'address' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:15',
            'fax_number' => 'nullable|string|max:15',
            'contact_name' => 'required|string|max:255',
            'contact_email' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 'failed',
                'errors' => $validator->errors()
            ], 200);
        } else {
            $supplier->fill($request_data);
            if($supplier->save()) {
                $user = new User();
                $user->email = $supplier->contact_email;
                $user->name = $supplier->contact_name;
                $user->password = Hash::make('12345678');
                $user->user_type = config('const.user_type.supplier');
                $user->supplier_id = $supplier->id;
                $user->save();
            }

            return response()->json([
                'code' => 'success',
            ]);
        }
    }

    public function delete(Request $request, Supplier $supplier)
    {
        $supplier->delete();
        return response()->json([
            'code' => 'success',
        ]);
    }
}
