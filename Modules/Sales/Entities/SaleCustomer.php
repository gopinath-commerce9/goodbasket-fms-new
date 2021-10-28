<?php

namespace Modules\Sales\Entities;

use Illuminate\Database\Eloquent\Model;

class SaleCustomer extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sale_customers';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'env',
        'channel',
        'first_name',
        'last_name',
        'email_id',
        'contact_number',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Fetches all the Sale Orders placed by the Customer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function saleOrders() {
        return $this->hasMany(SaleOrder::class, 'customer_id', 'id');
    }

}
