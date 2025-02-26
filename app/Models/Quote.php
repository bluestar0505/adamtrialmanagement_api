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
        'unit_price',
        'quantity',
        'total_amount',
        'delivery_date',
        'is_accepted',
    ];

    protected $dates = [
        'created_at', 'updated_at', 'deleted_at'
    ];

    public function obj_supplier(){
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }

}
