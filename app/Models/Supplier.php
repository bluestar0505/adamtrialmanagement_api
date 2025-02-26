<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'management_no',
        'company_name',
        'postal_code',
        'address',
        'phone_number',
        'fax_number',
        'contact_name',
        'contact_email',
    ];

    protected $dates = [
        'created_at', 'updated_at', 'deleted_at'
    ];

}
