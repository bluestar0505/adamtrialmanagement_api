<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Buyer extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'management_no',
        'name',
        'email',
    ];

    protected $dates = [
        'created_at', 'updated_at', 'deleted_at'
    ];


    public function user(){
        return $this->hasOne(User::class, 'buyer_id', 'id');
    }
}
