<?php

namespace Modules\Sales\Entities;

use Illuminate\Database\Eloquent\Model;

class SaleOrder extends Model
{

    const AVAILABLE_ORDER_STATUSES = [
        'pending',
        'processing',
        'being_prepared',
        'ready_to_dispatch',
        'out_for_delivery',
        'delivered'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sale_orders';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'env',
        'channel',
        'order_id',
        'increment_id',
        'order_created_at',
        'order_updated_at',
        'customer_id',
        'is_guest',
        'customer_firstname',
        'customer_lastname',
        'region_id',
        'region_code',
        'region',
        'city',
        'zone_id',
        'store',
        'delivery_date',
        'delivery_time_slot',
        'total_item_count',
        'total_qty_ordered',
        'order_weight',
        'box_count',
        'not_require_pack',
        'order_currency',
        'order_subtotal',
        'order_tax',
        'discount_amount',
        'shipping_total',
        'shipping_method',
        'order_total',
        'order_due',
        'order_state',
        'order_status',
        'order_status_label',
        'to_be_synced',
        'is_synced',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Fetches the Customer data of the Sale Order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function saleCustomer() {
        return $this->belongsTo(SaleCustomer::class, 'customer_id', 'id');
    }

    /**
     * Fetches the Order Items Data of the Sale Order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function  orderItems() {
        return $this->hasMany(SaleOrderItem::class, 'order_id', 'id');
    }

    /**
     * Fetches the Billing Address of the Sale Order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function billingAddress() {
        return $this->hasOne(SaleOrderAddress::class, 'order_id', 'id')
            ->where('type', '=', 'billing');
    }

    /**
     * Fetches the Shipping Address of the Sale Order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function shippingAddress() {
        return $this->hasOne(SaleOrderAddress::class, 'order_id', 'id')
            ->where('type', '=', 'shipping');
    }

    /**
     * Fetches the Payment Data of the Sale Order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function  paymentData() {
        return $this->hasMany(SaleOrderPayment::class, 'order_id', 'id');
    }

    /**
     * Fetches the Status History Data of the Sale Order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function  statusHistory() {
        return $this->hasMany(SaleOrderStatusHistory::class, 'order_id', 'id');
    }

    /**
     * Fetches the Process History Data of the Sale Order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function  processHistory() {
        return $this->hasMany(SaleOrderProcessHistory::class, 'order_id', 'id');
    }

}
