@extends('base::layouts.mt-main')

@section('page-title') <?= $pageTitle; ?> @endsection
@section('page-sub-title') <?= $pageSubTitle; ?> @endsection

@section('content')

    <div class="row">
        <div class="col-md-12">

            <div class="card card-custom overflow-hidden">
                <form action="{{ url('/picker/sale_order_status_change') }}" method="POST" id="order_view_status_change_form">
                    @csrf

                    <div class="card-body p-0">

                        <!-- begin: Invoice-->

                        <!-- begin: Invoice header-->
                        <div class="row justify-content-center py-8 px-8 py-md-27 px-md-0">
                            <div class="col-md-11">
                                <div class="d-flex justify-content-between pb-10 pb-md-20 flex-column flex-md-row">
                                    <h1 class="display-2 font-weight-boldest mb-10"><?php echo ($saleOrderData['zone_id']) ?? ''; ?></h1>
                                    <div class="d-flex flex-column align-items-md-end px-0">

                                        <span class="d-flex flex-column align-items-md-end opacity-70">
                                            <span class="font-weight-bolder mb-2">Shipping Information</span>
                                            <span><?php echo $saleOrderData['shipping_address']['first_name'];?> <?php echo $saleOrderData['shipping_address']['last_name']; ?></span>
                                             <?php if(isset($saleOrderData['shipping_address']['company'])){ ?>
                                            <span><?php echo $saleOrderData['shipping_address']['company'];?></span>
                                            <?php } ?>
                                            <span>
                                                <?php echo $saleOrderData['shipping_address']['address_1']; ?>
                                                <?php echo ($saleOrderData['shipping_address']['address_2'] != null) ?  ', ' . $saleOrderData['shipping_address']['address_2'] : ''; ?>
                                                <?php echo ($saleOrderData['shipping_address']['address_3'] != null) ?  ', ' . $saleOrderData['shipping_address']['address_3'] : ''; ?>
                                            </span>
                                            <span><?php echo $saleOrderData['shipping_address']['city'];?>,
                                            <?php if(isset($saleOrderData['shipping_address']['region'])) { ?>
                                                  <?php echo $saleOrderData['shipping_address']['region'].', '; ?>
                                            <?php } ?>
                                                <?php echo $saleOrderData['shipping_address']['post_code']; ?>
                                            </span>
                                            <span><?php echo $saleOrderData['shipping_address']['contact_number']; ?></span>
                                        </span>
                                    </div>
                                </div>
                                <div class="border-bottom w-100"></div>
                                <div class="d-flex justify-content-between pt-4 pb-4">
                                    <div class="d-flex flex-column flex-root">
                                        <span class="font-weight-bolder mb-2">Order Date</span>
                                        <span class="opacity-70">
                                            <?php echo $serviceHelper->getFormattedTime($saleOrderData['order_created_at'], 'F d, Y, h:i:s A'); ?>
                                        </span>
                                    </div>
                                    <div class="d-flex flex-column flex-root">
                                        <span class="font-weight-bolder mb-2">Order ID</span>
                                        <span class="opacity-70"># <?php echo $saleOrderData['increment_id'];?></span>
                                    </div>
                                    <div class="d-flex flex-column flex-root">
                                        <span class="font-weight-bolder mb-2">Payment Method</span>
                                        <span class="opacity-70">
                                            <?php
                                            if(isset($saleOrderData['payment_data'][0]['cc_last4']) && !empty($saleOrderData['payment_data'][0]['cc_last4'])){ ?>
                                                Credit Card ending **** <?php echo $saleOrderData['payment_data'][0]['cc_last4']; ?><br>
                                            <?php } ?>
                                            <?php
                                            $paymentMethodTitle = '';
                                            $payInfoLoopTargetLabel = 'method_title';
                                            if (isset($saleOrderData['payment_data'][0]['extra_info'])) {
                                                $paymentAddInfo = json5_decode($saleOrderData['payment_data'][0]['extra_info'], true);
                                                if (is_array($paymentAddInfo) && (count($paymentAddInfo) > 0)) {
                                                    foreach ($paymentAddInfo as $paymentInfoEl) {
                                                        if ($paymentInfoEl['key'] == $payInfoLoopTargetLabel) {
                                                            $paymentMethodTitle = $paymentInfoEl['value'];
                                                        }
                                                    }
                                                }
                                            }
                                            ?>
                                            <?= $paymentMethodTitle ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="border-bottom w-100"></div>
                                <div class="d-flex justify-content-between pt-4 pb-4">
                                    <div class="d-flex flex-column flex-root">
                                        <span class="font-weight-bolder mb-2">Order Status</span>
                                        <span class="opacity-70">
                                            <?php
                                            $status = $saleOrderData['order_status'];
                                            if(array_key_exists($status, $orderStatuses)) {
                                                $status = $orderStatuses[$status];
                                            }
                                            ?>
                                            <?php echo $status;?>
                                        </span>
                                    </div>
                                    <div class="d-flex flex-column flex-root">
                                        <span class="font-weight-bolder mb-2">Delivery Info.</span>
                                        <span class="opacity-70">
                                            Delivery Date :<?php if(isset($saleOrderData['delivery_date'])){ echo $saleOrderData['delivery_date']; } ?><br>
                                            Delivery Time Slot :<?php if(isset($saleOrderData['delivery_time_slot'])){ echo $saleOrderData['delivery_time_slot']; }?>
                                        </span>
                                    </div>
                                    <div class="d-flex flex-column flex-root">
                                        <span class="font-weight-bolder mb-2">Order Comment</span>
                                        <span class="opacity-70"><?php if(isset($saleOrderData['customer_order_comment'])){ echo $saleOrderData['customer_order_comment']; } ?></span>
                                    </div>
                                </div>
                                <div class="border-bottom w-100"></div>
                                <div class="d-flex justify-content-between pt-4 pb-4">
                                    <div class="d-flex flex-column flex-root">
                                        <span class="font-weight-bolder mb-2">Order Info.</span>
                                        <span class="opacity-70">
                                            <?php echo $customerGroups[$saleOrderData['sale_customer']['customer_group_id']] ?>
                                        </span>
                                    </div>
                                    <div class="d-flex flex-column flex-root">
                                        <span class="font-weight-bolder mb-2">Vendor Status.</span>
                                        <span class="opacity-70 vendor_status" >

                                         </span>
                                    </div>
                                    <div class="d-flex flex-column flex-root">
                                        <span class="font-weight-bolder mb-2">Order Status Histories</span>
                                        <span class="opacity-70"><?php
                                            foreach ($saleOrderData['status_history'] as $orderhistory) {
                                                echo "<b>Comment :</b>";
                                                echo $orderhistory['comments'];
                                                echo "<br/>";
                                                echo "<b>Date :</b>";
                                                echo $serviceHelper->getFormattedTime($orderhistory['status_created_at'], 'F d, Y, h:i:s A');
                                                echo "<br/>";
                                            }
                                            ?>
                                        </span>
                                    </div>

                                    <div class="d-flex flex-column flex-root">

                                    </div>

                                </div>
                            </div>
                        </div>
                        <!-- end: Invoice header-->

                        <div class="row justify-content-center py-8 px-8 py-md-10 px-md-0">
                            <div class="col-md-11">
                                <div class="d-flex justify-content-between">
                                    <?php if($saleOrderData['order_status'] == "pending" || $saleOrderData['order_status'] == "processing") {?>
                                    <a href="{{ url('/admin/being-prepared/' . $saleOrderData['id']) }}" class="btn btn-primary font-weight-bold">Being Prepared</a>

                                    <?php } ?>
                                    <?php if($saleOrderData['order_status'] == "being_prepared") {?>

                                    <input type="hidden" name="action" value="print_awd">
                                    <input type="hidden" name="orderid" value="{{ $saleOrderData['id'] }}">
                                    <input type="hidden" name="approvalcode" id="approvalcode" value="<?= $saleOrderData['approval_code'] ? $saleOrderData['approval_code'] : '';?>">

                                    <table class="block_table">
                                        <tr>

                                            <td>
                                                <input type="button" name="btnscan" onclick="return scanProduct()" class="btn btn-primary font-weight-bold" value="Scan Product">
                                            </td>
                                            <td>
                                                <input type="button" name="btnenableentry" onclick="return enableEntry()" class="btn btn-primary font-weight-bold" value="Enable Manual Entry in Actual Qty">
                                                <input type="button" name="btndisableentry" onclick="return disableEntry()" class="btn btn-primary font-weight-bold" value="Disable Manual Entry in Actual Qty">
                                            </td>

                                        </tr>
                                        <tr>
                                            <td>
                                                Number of Boxes : <input type="text"  min="1"  class="box_qty"  style="text-align:center" size="5" name="box_qty" id="box_qty_1"  required="">
                                            </td>
                                            <td>


                                                <?php /* ?>
																		<input type="button" name="btnsubmit" onclick="return submitORder(this.form)" class="btn btn-primary font-weight-bold" value="Print Shipping Label">
                                                                                   <?php */ ?>
                                                <input type="button" name="btnsubmit" onclick="return submitORder(this.form)" class="btn btn-primary font-weight-bold" value="Ready To Dispatch">
                                            </td>

                                            <?php /* ?>
                                                                               <td>
                                                                                   <a href="orderstatuschange.php?orderid=<?php echo $_GET['orderid'];?>&&orderstatus=ready_to_dispatch" class="btn btn-primary font-weight-bold">Ready To Dispatch</a>
                                                                               </td>
                                                                               <?php */ ?>

                                        </tr>



                                    </table>

                                    <?php  } ?>

                                    <?php if($saleOrderData['order_status'] == 'ready_to_dispatch') {
                                    ?>
                                    <?php /* ?>
																	 <input type="hidden" name="action" value="print_awd">
																	<input type="hidden"  min="1"   value="1" style="text-align:center" size="5" name="box_qty"  required="">
                                                                    <?php */ ?>

                                    <input type="hidden" name="action" value="change_delivery_status">
                                    <input type="hidden" name="orderid" value="{{ $saleOrderData['id'] }}">

                                    <table class="block_table">
                                        <tr>
                                            <td>
                                                <select class="form-control" name="orderstatuschanger"
                                                        id="order-status-changer">
                                                    <option value="">Select Status</option>

                                                    <option value="out_for_delivery">Out For Delivery</option>
                                                    <option value="delivered">Delivered</option>


                                                </select>
                                            </td>
                                            <td>
                                                <input type="submit" name="btnsubmit" class="btn btn-primary font-weight-bold" value="Change Order Status">
                                            </td>
                                            <td>

                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <a href="{{ url('/admin/print-order-shipping-label' . $saleOrderData['id']) }}" class="btn btn-primary font-weight-bold">Print Shipping Label</a>
                                            </td>
                                            <td>
                                                <?php /* ?>
                                                                                <a href="downloaddeliverynote.php?orderid=<?php echo $_GET['orderid'];?>" target="_blank" class="btn btn-light-primary font-weight-bold">Download Delivery Note</a>
                                                                                <?php */ ?>
                                            </td>
                                            <td>

                                            </td>
                                        </tr>
                                    </table>

                                    <?php
                                    }?>

                                    <?php if($saleOrderData['order_status'] == 'out_for_delivery') {
                                    ?>

                                    <input type="hidden" name="orderid" value="<?php echo $saleOrderData['id'] ?>">

                                    <a href="{{ url('/admin/order-status-change?orderid=' . $saleOrderData['id'] . '&orderstatus=delivered') }}" class="btn btn-primary font-weight-bold">Set As Delivered</a>

                                    <?php
                                    }?>
                                </div>
                            </div>
                        </div>

                        <!-- begin: Invoice body-->

                        <div class="row justify-content-center py-8 px-8 py-md-27 px-md-0">
                            <div class="col-md-11">
                                <div class="table-responsive">
                                    <div class="border-bottom w-100"></div>
                                    <div>
                                        <table class="table" id="item-list-table">
                                            <thead>
                                                <tr>
                                                    <th class="pl-0 font-weight-bold text-muted text-uppercase">Actual Quantity</th>
                                                    <th class="text-right font-weight-bold text-muted text-uppercase">Quantity</th>
                                                    <th class="pl-0 font-weight-bold text-muted text-uppercase">Item</th>
                                                    <th class="pl-0 font-weight-bold text-muted text-uppercase">Country</th>
                                                    <th class="pl-0 font-weight-bold text-muted text-uppercase">Sku</th>
                                                    <th class="pl-0 font-weight-bold text-muted text-uppercase">Shelf Number</th>
                                                    <th class="pl-0 font-weight-bold text-muted text-uppercase">Scale Number</th>
                                                    <th class="text-right font-weight-bold text-muted text-uppercase">Price</th>
                                                    <th class="text-right font-weight-bold text-muted text-uppercase">Totals</th>
                                                    <th class="text-right font-weight-bold text-muted text-uppercase">Vendor</th>
                                                    <th class="pl-0 font-weight-bold text-muted text-uppercase">Vendor Availability</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <!-- foreach ($order->lineItems as $line) or some such thing here -->
                                            <?php
                                            $i = 0;

                                            foreach ($saleOrderData['order_items'] as $item) {
                                            $itemInputId = $item['item_sku'];
                                            // echo "<pre>";
                                            //print_r($item);

                                            if($item['qty_ordered'] >1 ) {
                                                $row_subtotal = $item['row_grand_total'];
                                            }
                                            else {
                                                $row_subtotal = $item['row_grand_total'];
                                            }
                                            if(!empty($item['actual_qty']) && $item['actual_qty']>0) {
                                                $actualQty = $item['actual_qty'];
                                            } else {
                                                $actualQty = "";
                                            }
                                            if(!empty($item['selling_unit'])){
                                                $sellingFormat = $item['selling_unit'];
                                            } else {
                                                $sellingFormat = "";
                                            }

                                            if(!empty($item['item_barcode'])){
                                                $barcode = $item['item_barcode'];
                                                if(substr($barcode,7)!=000000) {
                                                    $itemInputId = $barcode;

                                                } else {
                                                    $barcode = "";
                                                }

                                            } else {
                                                $barCode = "";
                                            }


                                            if(!empty($item['item_info'])){
                                                $weightInfo = $item['item_info'];
                                            } else {
                                                $weightInfo = "";
                                            }

                                            if(!empty($item['country_label'])){
                                                $countryLabel = $item['country_label'];
                                            } else {
                                                $countryLabel = "";
                                            }

                                            if(!empty($item['item_name'])){
                                                $productName = $item['item_name'];
                                            } else {
                                                $productName = "";
                                            }

                                            ?>
                                            <tr>
                                                <td><input type="text" class="actual_qty"  name="actual_qty[<?php echo $itemInputId;?>]" id="actual_qty_<?php echo $itemInputId;?>"  value="<?php echo $actualQty; ?>" style="width: 80px;">
                                                    <input type="hidden"   class="actual_qty_tmp" name="tmpactual_qty[<?php echo $itemInputId;?>]" id="tmpactual_qty_<?php echo $itemInputId;?>" value="<?php echo $actualQty; ?>"  style="width: 80px;">

                                                    <input type="hidden" class="ordered_qty" name="ordered_qty[<?php echo $itemInputId;?>]" id="ordered_qty_<?php echo $itemInputId;?>" value="<?php echo $item['qty_ordered']; ?>" >
                                                    <input type="hidden" class="selling_format" name="selling_format[<?php echo $itemInputId;?>]" id="selling_format_<?php echo $itemInputId;?>" value="<?php echo $sellingFormat; ?>"  >
                                                    <?php echo $sellingFormat;?>


                                                    <span id="tick_mark_<?php echo $item['item_sku'];?>"  style="font-size: 20px; font-weight: bold; color: green"></span>

                                                    <span>
                                                        <a href="javascript:;" onclick="clearValue('<?php echo $itemInputId;?>')">Clear </a>
                                                    </span>

                                                </td>

                                                <td class="border-top-0 pl-0 py-4"><?php echo $item['qty_ordered']." ".$sellingFormat;?></td>
                                                <td class="border-top-0 pl-0 py-4"><?php echo $productName;?> <br> <b>Pack & Weight Info :</b> <?php echo $weightInfo;?>

                                                    <br>

                                                    <?php if(!empty($item['gift_message'])) { ?>
                                                    <p><b>Gift Message</b><br>
                                                        From : <?= $item['gift_message']['sender'] ? $item['gift_message']['sender'] : '';?><br>
                                                        To : <?= $item['gift_message']['recipient'] ? $item['gift_message']['recipient'] : '';?> <br>
                                                        Message : <?= $item['gift_message']['message'] ? $item['gift_message']['message'] : '';?> <br>
                                                    </p>
                                                    <?php } ?>

                                                </td>
                                                <td class="border-top-0 pl-0 py-4"><?php echo $countryLabel;?></td>
                                                <td class="border-top-0 pl-0 py-4"><?php echo $item['item_sku']?></td>
                                                <td class="border-top-0 pl-0 py-4"><?php echo isset($item['shelf_number']) ? $item['shelf_number'] : '';?></td>
                                                <td class="border-top-0 pl-0 py-4"><?php echo $item['scale_number'] ? $item['scale_number'] : '';?></td>
                                                <td class="border-top-0 text-right py-4"><?php echo $saleOrderData['order_currency'] . " " . $item['price'];?></td>

                                                <!--  <td class="text-center"><?php echo $item['discount_amount']?></td>
                                                <td class="text-center"><?php echo $item['tax_amount']?></td> -->
                                                <td class="text-danger border-top-0 pr-0 py-4 text-right"><?php echo $saleOrderData['order_currency'] . " " . $row_subtotal;?></td>
                                                <td class="border-top-0 text-center py-4"><?php
                                                    if(!empty($item['vendor_id'])) {
                                                        echo $vendorList[$item['vendor_id']] ? $vendors[$item['vendor_id']] : '';

                                                    }
                                                    ?>
                                                </td>

                                                <td class="border-top-0 text-center py-4" id="availability_<?php echo $i?>"><?php if($item['vendor_availability']==1){ ?><i class="la la-check text-success mr-5 icon-xl"></i> <?php } ?>
                                                    <?php if($item['vendor_availability']==2){ ?><i class="la la-remove text-danger mr-5 icon-xl"></i> <?php } ?>
                                                </td>
                                            </tr>
                                            <?php $i++;} ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="border-bottom w-100 my-13 opacity-15"></div>
                                <!--begin::Invoice total-->


                                <div class="table-responsive">
                                    <table class="table text-md-right font-weight-boldest">
                                        <tbody>
                                        <tr>
                                            <td class="align-middle title-color font-size-lg border-0 pt-0 pl-0 w-50">SUBTOTAL</td>
                                            <td class="align-middle font-size-h3 border-0 pt-0"><?php echo $saleOrderData['order_currency']." ".$saleOrderData['order_subtotal'];?></td>
                                        </tr>
                                        <tr>
                                            <td class="align-middle title-color font-size-h4 border-0 py-7 pl-0 w-50">Shipping (<?php echo $saleOrderData['shipping_method'];?>)</td>
                                            <td class="align-middle font-size-h3 border-0 py-7"><?php echo $saleOrderData['order_currency']." ".$saleOrderData['shipping_total'];?></td>
                                        </tr>
                                        <?php if( !empty($saleOrderData['discount_amount']) ) {?>
                                        <tr>

                                            <td class="align-middle title-color font-size-h4 border-0 py-7 pl-0 w-50">Discount (<?php if(isset($saleOrderData['coupon_code']) && !empty($saleOrderData['coupon_code'])) { echo $saleOrderData['coupon_code']; } ?>)</td>
                                            <td class="no-line text-align-middle font-size-h3 border-0 py-7"><?php echo $saleOrderData['order_currency']." ".$saleOrderData['discount_amount'];?></td>

                                        </tr>
                                        <?php } ?>
                                        <tr>
                                            <td class="align-middle title-color font-size-h4 border-0 pl-0 w-50">GRAND TOTAL</td>
                                            <td class="text-danger font-size-h3 font-weight-boldest"><?php echo $saleOrderData['order_currency']." ".$saleOrderData['order_total'];?></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>


                                <!--end::Invoice total-->
                            </div>
                        </div>

                        <!-- end: Invoice body-->

                        <!-- end: Invoice -->

                    </div>

                </form>
            </div>

        </div>
    </div>


@endsection

@section('custom-js-section')

    <script src="{{ asset('js/picker.js') }}"></script>
    <script>
        jQuery(document).ready(function() {
            PickerCustomJsBlocks.orderViewPage('{{ url('/') }}');
        });
    </script>

@endsection
