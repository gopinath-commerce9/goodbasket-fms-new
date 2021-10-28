<?php

namespace Modules\Sales\Entities;

use Illuminate\Database\Eloquent\Model;

class SaleOrderItem extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sale_order_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'order_id',
        'item_id',
        'sale_order_id',
        'item_created_at',
        'item_updated_at',
        'item_sku',
        'item_barcode',
        'item_name',
        'item_info',
        'item_image',
        'actual_qty',
        'qty_ordered',
        'qty_shipped',
        'qty_invoiced',
        'qty_canceled',
        'qty_returned',
        'qty_refunded',
        'selling_unit',
        'item_weight',
        'price',
        'row_total',
        'tax_amount',
        'tax_percent',
        'discount_amount',
        'discount_percent',
        'row_grand_total',
        'vendor_id',
        'vendor_availability',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Fetches the Sale Order Data of the Order Item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function saleOrder() {
        return $this->belongsTo(SaleOrder::class, 'order_id', 'id');
    }

}
