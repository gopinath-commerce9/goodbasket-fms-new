<?php

namespace Modules\Sales\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class SaleOrderProcessHistory extends Model
{

    const SALE_ORDER_PROCESS_ACTION_PICKUP = 'pickup';
    const SALE_ORDER_PROCESS_ACTION_PICKED = 'picked';
    const SALE_ORDER_PROCESS_ACTION_DELIVERY = 'delivery';
    const SALE_ORDER_PROCESS_ACTION_DELIVERED = 'delivered';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sale_order_process_histories';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'order_id',
        'action',
        'status',
        'comments',
        'extra_info',
        'done_by',
        'done_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Fetches the Sale Order Data of the Process History.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function saleOrder() {
        return $this->belongsTo(SaleOrder::class, 'order_id', 'id');
    }

    /**
     * Fetches the User data who executed the action.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function actionDoer() {
        return $this->belongsTo(User::class, 'done_by', 'id');
    }

}
