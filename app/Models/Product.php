<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'requests';
    protected $fillable = [
        'buyer_id',
        'request_date',
        'management_no',
        'product_name',
        'material',
        'quantity',
        'data_2d',
        'data_2d_org',
        'data_3d',
        'data_3d_org',
        'desired_delivery_date',
        'reply_due_date',
        'comment',
        'memo',
        'important',
    ];

    protected $dates = [
        'created_at', 'updated_at', 'deleted_at'
    ];

    public function obj_selected_quote(){
        return $this->hasOne(Quote::class, 'id', 'request_id')
            ->where('is_accepted', config('const.accept_status.accepted'));
    }

    public function getQuoteStatusAttribute(){
        $query = Quote::where('request_id', $this->id);
        if($query->count() == 0) {
            return config('const.quote_status.waiting');
        } else {
            $query->where('is_accepted', config('const.accept_status.accepted'));
            if($query->count() > 0) {
                return config('const.quote_status.selected');
            } else {
                return config('const.quote_status.has_quote');
            }
        }
    }

    public function getSelectedSupplierNameAttribute(){
        return $this->obj_selected_quote ? $this->obj_selected_quote->name : '';
    }
    public function getSelectedUnitPriceAttribute(){
        return $this->obj_selected_quote ? $this->obj_selected_quote->unit_price : '';
    }
    public function getSelectedQuantityAttribute(){
        return $this->obj_selected_quote ? $this->obj_selected_quote->quantity : '';
    }
    public function getSelectedTotalAmountAttribute(){
        return $this->obj_selected_quote ? $this->obj_selected_quote->total_amount : '';
    }
    public function getSelectedDeliveryDateAttribute(){
        return $this->obj_selected_quote ? $this->obj_selected_quote->delivery_date : '';
    }
}
