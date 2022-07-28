<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Certificate extends Model
{
    use HasFactory;

    protected $dates = [
        'activation_at'
    ];

    protected $fillable = [
        'identity',
        'user_id',
        'status',
        'total_price',
        'currency_code',
        'product_id',
        'product_count',
        'activation_at'
    ];

    public function scopeActivatedWithUserAndProduct($query, $status = 'active') {

        $query
            ->select('certificates.id', 'certificates.identity', 'certificates.status', 'certificates.total_price', 'certificates.currency_code', 'certificates.product_count', 'products.name as product_name', 'products.place', 'products.implementation_time', 'users.name as user_name', 'users.last_name' )
            ->leftJoin('products', 'certificates.product_id', '=', 'products.id')
            ->leftJoin('users', 'certificates.user_id', '=', 'users.id')
            ->where('certificates.status', '=', $status)
            ->latest('certificates.created_at');
    }

    public function scopeActivated($query) {

        $query->where('status', '=', 'active');
    }

    public function product() {

        return $this->belongsTo('App\Models\Product');
    }

    public function user() {

        return $this->belongsTo('App\Models\User');
    }
}
