<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSupplier extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'request_suppliers';
    public $timestamps = false;

    protected $fillable = [
        'request_id',
        'supplier_id',
    ];

    protected $dates = [
        'created_at', 'updated_at', 'deleted_at'
    ];

    public function product(){
        return $this->hasMany(Product::class, 'id', 'request_id');
    }

}
