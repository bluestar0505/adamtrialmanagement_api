<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quote extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'request_id',
        'supplier_id',
        'products',
        'unit_price',
        'quantity',
        'total_amount',
        'delivery_date',
        'is_sent',
        'is_accepted',
        'order_file',
        'order_file_org',
        'drawing_file',
        'drawing_file_org',
    ];

    protected $dates = [
        'created_at', 'updated_at', 'deleted_at'
    ];

    protected $casts = [
        'array' => 'products',
    ];

    public function obj_supplier(){
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }

    public function obj_product(){
        return $this->belongsTo(Product::class, 'request_id', 'id');
    }

}
