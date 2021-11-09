<?php

namespace Modules\Supervisor\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Supervisor\Entities\SupervisorServiceHelper;
use Modules\Sales\Entities\SaleOrder;
use Modules\Sales\Entities\SaleOrderProcessHistory;
use Modules\UserRole\Entities\UserRole;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Html2Pdf;

class SupervisorController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return redirect()->route('supervisor.dashboard');
    }

    public function dashboard(Request $request)
    {

        $pageTitle = 'Fulfillment Center';
        $pageSubTitle = 'Dashboard';

        $emirates = config('goodbasket.emirates');
        $serviceHelper = new SupervisorServiceHelper();

        $selectedEmirate = (
            $request->has('emirate')
            && (trim($request->input('emirate')) != '')
            && array_key_exists(trim($request->input('emirate')), $emirates)
        ) ? trim($request->input('emirate')) : 'DXB';

        $todayDate = date('Y-m-d');

        $availableApiChannels = $serviceHelper->getAllAvailableChannels();
        $availableStatuses = $serviceHelper->getSupervisorsAllowedStatuses();
        $deliveryTimeSlots = $serviceHelper->getDeliveryTimeSlots();

        $supervisorOrders = $serviceHelper->getSupervisorOrders();
        $regionOrderCount = (!is_null($supervisorOrders)) ? count($supervisorOrders) : 0;

        $userRoleObj = new UserRole();
        $pickers = $userRoleObj->allPickers();
        $drivers = $userRoleObj->allDrivers();

        return view('supervisor::dashboard', compact(
            'pageTitle',
            'pageSubTitle',
            'emirates',
            'selectedEmirate',
            'regionOrderCount',
            'todayDate',
            'supervisorOrders',
            'availableApiChannels',
            'availableStatuses',
            'deliveryTimeSlots',
            'serviceHelper',
            'pickers',
            'drivers'
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

        $serviceHelper = new SupervisorServiceHelper();
        $availableStatuses = $serviceHelper->getSupervisorsAllowedStatuses();
        $currentChannel = $serviceHelper->getApiChannel();
        $currentEnv = $serviceHelper->getApiEnvironment();

        $targetOrder = SaleOrder::firstWhere('increment_id', $incrementId)
            ->where('env', $currentEnv)
            ->where('channel', $currentChannel)
            ->whereIn('order_status', array_keys($availableStatuses));
        if ($targetOrder) {
            return redirect('/supervisor/order-view/' . $targetOrder->id);
        } else {
            return back()
                ->with('error', "Sale Order #" . $incrementId . " not found!");
        }

    }

    public function searchOrderByFilters(Request $request) {

        $serviceHelper = new SupervisorServiceHelper();

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

        $availableStatuses = $serviceHelper->getSupervisorsAllowedStatuses();
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

        $filteredOrders = $serviceHelper->getSupervisorOrders($region, $apiChannel, $orderStatus, $deliveryDate, $deliverySlot);
        if (!$filteredOrders) {
            return response()->json([], 200);
        }

        $filteredOrderData = [];
        $totalRec = 0;
        $collectRecStart = $dtStart;
        $collectRecEnd = $collectRecStart + $dtPageLength;
        $currentRec = -1;
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
            $tempRecord['actions'] = url('/supervisor/order-view/' . $record->id);
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

    public function viewPicker($pickerId) {

        if (is_null($pickerId) || !is_numeric($pickerId) || ((int)$pickerId <= 0)) {
            return back()
                ->with('error', 'The Picker Id input is invalid!');
        }

        $pickerObject = User::find($pickerId);
        if (!$pickerObject) {
            return back()
                ->with('error', 'The Picker does not exist!');
        }

        if (is_null($pickerObject->mappedRole) || (count($pickerObject->mappedRole) == 0)) {
            return back()
                ->with('error', 'The given User is not a Picker!');
        }

        $mappedRole = $pickerObject->mappedRole[0];
        if (!$mappedRole->isPicker()) {
            return back()
                ->with('error', 'The given User is not a Picker!');
        }

        $pageTitle = 'Fulfillment Center';
        $pageSubTitle = 'Picker: ' . $pickerObject->name;
        $givenUserData = $pickerObject;
        $serviceHelper = new SupervisorServiceHelper();
        $emirates = config('goodbasket.emirates');
        $availableApiChannels = $serviceHelper->getAllAvailableChannels();
        $availableStatuses = $serviceHelper->getSupervisorsAllowedStatuses();

        return view('supervisor::picker-view', compact(
            'pageTitle',
            'pageSubTitle',
            'givenUserData',
            'serviceHelper',
            'emirates',
            'availableApiChannels',
            'availableStatuses'
        ));

    }

    public function viewDriver($driverId) {

        if (is_null($driverId) || !is_numeric($driverId) || ((int)$driverId <= 0)) {
            return back()
                ->with('error', 'The Driver Id input is invalid!');
        }

        $driverObject = User::find($driverId);
        if (!$driverObject) {
            return back()
                ->with('error', 'The Driver does not exist!');
        }

        if (is_null($driverObject->mappedRole) || (count($driverObject->mappedRole) == 0)) {
            return back()
                ->with('error', 'The given User is not a Driver!');
        }

        $mappedRole = $driverObject->mappedRole[0];
        if (!$mappedRole->isDriver()) {
            return back()
                ->with('error', 'The given User is not a Driver!');
        }

        $pageTitle = 'Fulfillment Center';
        $pageSubTitle = 'Driver: ' . $driverObject->name;
        $givenUserData = $driverObject;
        $serviceHelper = new SupervisorServiceHelper();
        $emirates = config('goodbasket.emirates');
        $availableApiChannels = $serviceHelper->getAllAvailableChannels();
        $availableStatuses = $serviceHelper->getSupervisorsAllowedStatuses();

        return view('supervisor::driver-view', compact(
            'pageTitle',
            'pageSubTitle',
            'givenUserData',
            'serviceHelper',
            'emirates',
            'availableApiChannels',
            'availableStatuses'
        ));

    }

    public function viewOrder($orderId) {

        if (is_null($orderId) || !is_numeric($orderId) || ((int)$orderId <= 0)) {
            return back()
                ->with('error', 'The Sale Order Id input is invalid!');
        }

        $saleOrderObj = SaleOrder::find($orderId);
        if(!$saleOrderObj) {
            return back()
                ->with('error', 'The Sale Order does not exist!');
        }

        $pageTitle = 'Fulfillment Center';
        $pageSubTitle = 'Sale Order #' . $saleOrderObj->increment_id;

        $orderStatuses = config('goodbasket.order_statuses');
        $serviceHelper = new SupervisorServiceHelper();
        $availableStatuses = $serviceHelper->getSupervisorsAllowedStatuses();
        $statusKeys = array_keys($availableStatuses);
        if(!in_array($saleOrderObj->order_status, $statusKeys)) {
            return back()
                ->with('error', 'The Sale Order not accessible!');
        }

        $customerGroups = [];
        $customerGroupData = $serviceHelper->getCustomerGroups();
        if (array_key_exists('items', $customerGroupData)) {
            foreach($customerGroupData['items'] as $group) {
                $customerGroups[$group['id']] = $group['code'];
            }
        }

        $vendorList = [];
        if (session()->has('salesOrderVendorList')) {
            $vendorList = session()->get('salesOrderVendorList');
        } else {
            $vendorResponse = $serviceHelper->getVendorsList();
            foreach($vendorResponse as $vendor)
            {
                $vendorList[$vendor['vendor_id']] = $vendor['vendor_name'];
            }
            session()->put('salesOrderVendorList', $vendorList);
        }

        $saleOrderObj->saleCustomer;
        $saleOrderObj->orderItems;
        $saleOrderObj->billingAddress;
        $saleOrderObj->shippingAddress;
        $saleOrderObj->paymentData;
        $saleOrderObj->statusHistory;
        $saleOrderObj->processHistory;
        if ($saleOrderObj->processHistory && (count($saleOrderObj->processHistory) > 0)) {
            foreach($saleOrderObj->processHistory as $processHistory) {
                $processHistory->actionDoer;
            }
        }
        $saleOrderData = $saleOrderObj->toArray();

        $userRoleObj = new UserRole();
        $pickers = $userRoleObj->allPickers();
        $drivers = $userRoleObj->allDrivers();

        return view('supervisor::order-view', compact(
            'pageTitle',
            'pageSubTitle',
            'saleOrderObj',
            'saleOrderData',
            'customerGroups',
            'vendorList',
            'serviceHelper',
            'orderStatuses',
            'pickers',
            'drivers'
        ));

    }

    public function orderStatusChange(Request $request, $orderId) {

        if (is_null($orderId) || !is_numeric($orderId) || ((int)$orderId <= 0)) {
            return back()
                ->with('error', 'The Sale Order Id input is invalid!');
        }

        $saleOrderObj = SaleOrder::find($orderId);
        if(!$saleOrderObj) {
            return back()
                ->with('error', 'The Sale Order does not exist!');
        }

        $allowedStatuses = [
            SaleOrder::SALE_ORDER_STATUS_PENDING,
            SaleOrder::SALE_ORDER_STATUS_PROCESSING,
            SaleOrder::SALE_ORDER_STATUS_ON_HOLD,
            SaleOrder::SALE_ORDER_STATUS_READY_TO_DISPATCH
        ];
        if (!in_array($saleOrderObj->order_status, $allowedStatuses)) {
            return back()
                ->with('error', 'The Sale Order Status cannot be changed!');
        }

        $userRoleObj = new UserRole();
        $pickers = $userRoleObj->allPickers();
        $drivers = $userRoleObj->allDrivers();

        $pickerIds = [];
        $driverIds = [];

        $serviceHelper = new SupervisorServiceHelper();
        if(count($pickers->mappedUsers) > 0) {
            foreach($pickers->mappedUsers as $userEl) {
                /*if(is_null($serviceHelper->isPickerAssigned($userEl))) {
                    $pickerIds[] = $userEl->id;
                }*/
                $pickerIds[] = $userEl->id;
            }
        }
        if(count($drivers->mappedUsers) > 0) {
            foreach($drivers->mappedUsers as $userEl) {
                /*if(is_null($serviceHelper->isDriverAssigned($userEl))) {
                    $driverIds[] = $userEl->id;
                }*/
                $driverIds[] = $userEl->id;
            }
        }

        $validator = Validator::make($request->all() , [
            'assign_pickup_to' => [
                Rule::requiredIf(function () use ($request, $saleOrderObj) {
                    return (
                        ($saleOrderObj->order_status === SaleOrder::SALE_ORDER_STATUS_PENDING)
                        || ($saleOrderObj->order_status === SaleOrder::SALE_ORDER_STATUS_PROCESSING)
                        || ($saleOrderObj->order_status === SaleOrder::SALE_ORDER_STATUS_ON_HOLD)
                    );
                }),
                Rule::in($pickerIds)
            ],
            'assign_delivery_to' => [
                Rule::requiredIf(function () use ($request, $saleOrderObj) {
                    return $saleOrderObj->order_status === SaleOrder::SALE_ORDER_STATUS_READY_TO_DISPATCH;
                }),
                Rule::in($driverIds)
            ],
        ], [
            'assign_pickup_to.requiredIf' => 'The Picker is not selected.',
            'assign_pickup_to.in' => 'The selected Picker does not exist (or) is not available .',
            'assign_delivery_to.requiredIf' => 'The Driver is not selected.',
            'assign_delivery_to.in' => 'The selected Driver does not exist (or) is not available .',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator);
        }

        $processUserId = 0;
        if (session()->has('authUserData')) {
            $sessionUser = session('authUserData');
            $processUserId = $sessionUser['id'];
        }

        $postData = $validator->validated();
        $assignedPickerId = (array_key_exists('assign_pickup_to', $postData)) ? $postData['assign_pickup_to'] : null;
        $assignedDriverId = (array_key_exists('assign_delivery_to', $postData)) ? $postData['assign_delivery_to'] : null;

        if ($assignedPickerId) {
            $assignedPicker = (!is_null($assignedPickerId)) ? User::find($assignedPickerId) : null;
            $returnResult = $serviceHelper->setOrderAsBeingPrepared($saleOrderObj, $assignedPicker->id, $processUserId);
            if ($returnResult) {
                return back()->with('success', 'The Sale Order is assigned to the Picker successfully!');
            } else {
                return back()->with('error', $returnResult['message']);
            }
        } elseif ($assignedDriverId) {
            $assignedDriver = (!is_null($assignedDriverId)) ? User::find($assignedDriverId) : null;
            $returnResult = $serviceHelper->assignOrderToDriver($saleOrderObj, $assignedDriver->id, $processUserId);
            if ($returnResult) {
                return back()->with('success', 'The Sale Order is assigned to the Driver successfully!');
            } else {
                return back()->with('error', $returnResult['message']);
            }
        } else {
            return back()->with('error', 'No process happened!');
        }

    }

    public function printShippingLabel($orderId) {

        if (is_null($orderId) || !is_numeric($orderId) || ((int)$orderId <= 0)) {
            return back()
                ->with('error', 'The Sale Order Id input is invalid!');
        }

        $saleOrderObj = SaleOrder::find($orderId);
        if(!$saleOrderObj) {
            return back()
                ->with('error', 'The Sale Order does not exist!');
        }

        if($saleOrderObj->order_status !== SaleOrder::SALE_ORDER_STATUS_READY_TO_DISPATCH) {
            return back()
                ->with('error', 'Cannot print the Shipping Label of the Sale Order.!');
        }

        try {

            $pdfOrientation = 'P';
            $pdfPaperSize = 'A5';
            $pdfUseLang = 'en';
            $pdfDefaultFont = 'Arial';

            $saleOrderObj->saleCustomer;
            $saleOrderObj->orderItems;
            $saleOrderObj->billingAddress;
            $saleOrderObj->shippingAddress;
            $saleOrderObj->paymentData;
            $saleOrderObj->statusHistory;
            $saleOrderObj->processHistory;
            if ($saleOrderObj->processHistory && (count($saleOrderObj->processHistory) > 0)) {
                foreach($saleOrderObj->processHistory as $processHistory) {
                    $processHistory->actionDoer;
                }
            }
            $orderData = $saleOrderObj->toArray();

            $path = public_path('ktmt/media/logos/logo_goodbasket.png');
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $logoEncoded = 'data:image/' . $type . ';base64,' . base64_encode($data);

            $pdfContent = view('supervisor::print-label', compact('orderData', 'logoEncoded'))->render();

            $pdfName = "print-label-order-" . $saleOrderObj->increment_id . ".pdf";
            $outputMode = 'D';

            $html2pdf = new Html2Pdf($pdfOrientation, $pdfPaperSize, $pdfUseLang);
            $html2pdf->setDefaultFont($pdfDefaultFont);
            $html2pdf->writeHTML($pdfContent);

            $pdfOutput = $html2pdf->output($pdfName, $outputMode);

        } catch (Html2PdfException $e) {
            $html2pdf->clean();
            $formatter = new ExceptionFormatter($e);
            return back()
                ->with('error', $formatter->getMessage());
        }

    }

}
