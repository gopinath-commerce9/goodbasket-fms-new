<?php

namespace Modules\Sales\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Sales\Entities\SalesServiceHelper;
use Modules\Sales\Entities\SaleOrder;
use Modules\Sales\Entities\SaleOrderProcessHistory;
use Modules\UserRole\Entities\UserRole;

class SalesController extends Controller
{

    public function index()
    {
        return redirect()->route('sales.ordersList');
    }

    public function ordersList(Request $request) {

        $pageTitle = 'Fulfillment Center';
        $pageSubTitle = 'Dashboard';

        $emirates = config('goodbasket.emirates');
        $serviceHelper = new SalesServiceHelper();

        $selectedEmirate = (
            $request->has('emirate')
            && (trim($request->input('emirate')) != '')
            && array_key_exists(trim($request->input('emirate')), $emirates)
        ) ? trim($request->input('emirate')) : 'DXB';

        $todayDate = date('Y-m-d');

        $availableApiChannels = $serviceHelper->getAllAvailableChannels();
        $availableStatuses = $serviceHelper->getAvailableStatuses();
        $deliveryTimeSlots = $serviceHelper->getDeliveryTimeSlots();

        return view('sales::list', compact(
            'pageTitle',
            'pageSubTitle',
            'emirates',
            'selectedEmirate',
            'todayDate',
            'availableApiChannels',
            'availableStatuses',
            'deliveryTimeSlots',
            'serviceHelper'
        ));

    }

    public function searchOrderByIncrementId(Request $request) {

        $incrementId = (
            $request->has('order_number')
            && (trim($request->input('order_number')) != '')
        ) ? trim($request->input('order_number')) : '';

        if ($incrementId == '') {
            return back()
                ->with('error', "Requested Order Number value is invalid!");
        }

        $serviceHelper = new SalesServiceHelper();
        $availableStatuses = $serviceHelper->getAvailableStatuses();
        $currentChannel = $serviceHelper->getApiChannel();
        $currentEnv = $serviceHelper->getApiEnvironment();

        $targetOrder = SaleOrder::firstWhere('increment_id', $incrementId)
            ->where('env', $currentEnv)
            ->where('channel', $currentChannel)
            ->whereIn('order_status', array_keys($availableStatuses));
        if ($targetOrder) {
            $currentRole = null;
            if (session()->has('authUserData')) {
                $sessionUser = session('authUserData');
                $currentRole = $sessionUser['roleCode'];
            }
            if (!is_null($currentRole)) {
                return redirect('/' . $currentRole . '/order-view/' . $targetOrder->id);
            } else {
                return back()
                    ->with('error', "Sale Order #" . $incrementId . " not found!");
            }
        } else {
            return back()
                ->with('error', "Sale Order #" . $incrementId . " not found!");
        }

    }

    public function searchOrderByFilters(Request $request) {

        $serviceHelper = new SalesServiceHelper();

        $dtDraw = (
            $request->has('draw')
            && (trim($request->input('draw')) != '')
        ) ? (int)trim($request->input('draw')) : 1;

        $dtStart = (
            $request->has('start')
            && (trim($request->input('start')) != '')
        ) ? (int)trim($request->input('start')) : 0;

        $dtPageLength = (
            $request->has('length')
            && (trim($request->input('length')) != '')
        ) ? (int)trim($request->input('length')) : 10;

        $emirates = config('goodbasket.emirates');
        $region = (
            $request->has('emirates_region')
            && (trim($request->input('emirates_region')) != '')
            && array_key_exists(trim($request->input('emirates_region')), $emirates)
        ) ? trim($request->input('emirates_region')) : '';

        $availableApiChannels = $serviceHelper->getAllAvailableChannels();
        $apiChannel = (
            $request->has('channel_filter')
            && (trim($request->input('channel_filter')) != '')
            && array_key_exists(trim($request->input('channel_filter')), $availableApiChannels)
        ) ? trim($request->input('channel_filter')) : '';

        $availableStatuses = $serviceHelper->getAvailableStatuses();
        $orderStatus = (
            $request->has('order_status_filter')
            && (trim($request->input('order_status_filter')) != '')
            && array_key_exists(trim($request->input('order_status_filter')), $availableStatuses)
        ) ? trim($request->input('order_status_filter')) : '';

        $deliveryDate = (
            $request->has('delivery_date_filter')
            && (trim($request->input('delivery_date_filter')) != '')
        ) ? trim($request->input('delivery_date_filter')) : '';

        $deliverySlot = (
            $request->has('delivery_slot_filter')
            && (trim($request->input('delivery_slot_filter')) != '')
        ) ? trim($request->input('delivery_slot_filter')) : '';

        $filteredOrders = $serviceHelper->getSaleOrders($region, $apiChannel, $orderStatus, $deliveryDate, $deliverySlot);
        if (!$filteredOrders) {
            return response()->json([], 200);
        }

        $filteredOrderData = [];
        $totalRec = 0;
        $collectRecStart = $dtStart;
        $collectRecEnd = $collectRecStart + $dtPageLength;
        $currentRec = -1;
        $currentRole = null;
        if (session()->has('authUserData')) {
            $sessionUser = session('authUserData');
            $currentRole = $sessionUser['roleCode'];
        }
        foreach ($filteredOrders as $record) {
            $totalRec++;
            $currentRec++;
            if (($currentRec < $collectRecStart) || ($currentRec >= $collectRecEnd)) {
                continue;
            }
            $tempRecord = [];
            $tempRecord['recordId'] = $record->id;
            $tempRecord['orderId'] = $record->order_id;
            $tempRecord['incrementId'] = $record->increment_id;
            $apiChannelId = $record->channel;
            $tempRecord['channel'] = $availableApiChannels[$apiChannelId]['name'];
            $emirateId = $record->region_code;
            $tempRecord['region'] = $emirates[$emirateId];
            $shipAddress = $record->shippingAddress;
            $tempRecord['customerName'] = $shipAddress->first_name . ' ' . $shipAddress->last_name;
            $tempRecord['deliveryDate'] = $record->delivery_date;
            $tempRecord['deliveryTimeSlot'] = $record->delivery_time_slot;
            $tempRecord['deliveryPicker'] = '';
            $tempRecord['deliveryPickerTime'] = '';
            $tempRecord['deliveryDriver'] = '';
            $tempRecord['deliveryDriverTime'] = '';
            $orderStatusId = $record->order_status;
            $tempRecord['orderStatus'] = $availableStatuses[$orderStatusId];
            $deliveryPickerData = $record->pickupData;
            $deliveryDriverData = $record->deliveryData;
            $tempRecord['actions'] = (!is_null($currentRole)) ? url('/' . $currentRole . '/order-view/' . $record->id) : 'javascript:void(0);';
            if ($deliveryPickerData && (count($deliveryPickerData) > 0)) {
                $pickerDetail = $deliveryPickerData[0];
                $tempRecord['deliveryPickerTime'] = $serviceHelper->getFormattedTime($pickerDetail->done_at, 'F d, Y, h:i:s A');
                if ($pickerDetail->actionDoer) {
                    $tempRecord['deliveryPicker'] = $pickerDetail->actionDoer->name;
                }
            }
            if ($deliveryDriverData && (count($deliveryDriverData) > 0)) {
                $driverDetail = $deliveryDriverData[0];
                $tempRecord['deliveryDriverTime'] = $serviceHelper->getFormattedTime($driverDetail->done_at, 'F d, Y, h:i:s A');
                if ($driverDetail->actionDoer) {
                    $tempRecord['deliveryDriver'] = $driverDetail->actionDoer->name;
                }
            }
            $filteredOrderData[] = $tempRecord;
        }

        $returnData = [
            'draw' => $dtDraw,
            'recordsTotal' => $totalRec,
            'recordsFiltered' => $totalRec,
            'data' => $filteredOrderData
        ];

        return response()->json($returnData, 200);

    }

}
