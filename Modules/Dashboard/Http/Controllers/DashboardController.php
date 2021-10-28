<?php

namespace Modules\Dashboard\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Input;
use Modules\Dashboard\Entities\DashboardServiceHelper;
use Modules\Sales\Jobs\SaleOrderChannelImport;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return Renderable
     */
    public function index(Request $request)
    {
        $pageTitle = 'Fulfillment Center';
        $pageSubTitle = 'Dashboard';

        $emirates = config('goodbasket.emirates');
        $serviceHelper = new DashboardServiceHelper();

        $selectedEmirate = (
            $request->has('emirate')
            && (trim($request->input('emirate')) != '')
            && array_key_exists(trim($request->input('emirate')), $emirates)
        ) ? trim($request->input('emirate')) : 'DXB';

        $regionOrderCount = $serviceHelper->getOrdersCountByRegion($selectedEmirate);
        $todayDate = date('Y-m-d');
        $driverData = $serviceHelper->getDriversByDate($todayDate);

        $availableApiChannels = $serviceHelper->getAllAvailableChannels();

        return view('dashboard::index', compact(
            'pageTitle',
            'pageSubTitle',
            'emirates',
            'selectedEmirate',
            'regionOrderCount',
            'todayDate',
            'driverData',
            'availableApiChannels'
        ));
    }

    public function deliveryDetails(Request $request) {

        $region = (
            $request->has('region')
            && (trim($request->input('region')) != '')
        ) ? urldecode(trim($request->input('region'))) : null;

        $interval = (
            $request->has('interval')
            && (trim($request->input('interval')) != '')
        ) ? urldecode(trim($request->input('interval'))) : null;

        $date = (
            $request->has('date')
            && (trim($request->input('date')) != '')
        ) ? urldecode(trim($request->input('date'))) : null;

        $pageNo = (
            $request->has('pageno')
            && (trim($request->input('pageno')) != '')
        ) ? urldecode(trim($request->input('pageno'))) : 1;

        if (is_null($region) || is_null($interval) || is_null($date)) {
            return back()
                ->with('error', "Requested Parameters are Empty!!");
        }

        $pageTitle = 'Fulfillment Center';
        $pageSubTitle = 'Order List';

        $orderStatuses = config('goodbasket.order_statuses');
        $serviceHelper = new DashboardServiceHelper();

        $customerGroups = [];
        $customerGroupData = $serviceHelper->getCustomerGroups();
        if (array_key_exists('items', $customerGroupData)) {
            foreach($customerGroupData['items'] as $group) {
                $customerGroups[$group['id']] = $group['code'];
            }
        }

        $startPageLink = $pageNo;
        $endPageLink = $pageNo + 3;
        $pageSize = 20;
        $offset = ($pageNo - 1) * $pageSize;

        $orderData = $serviceHelper->getOrdersByRegion($region, $interval, $date);
        $totalRows = count($orderData);

        $totalPages = ceil($totalRows / $pageSize);
        if($endPageLink > $totalPages) {
            $endPageLink = $totalPages;
        }

        $orderData = $serviceHelper->getOrdersByRegion($region, $interval, $date, $pageSize, $pageNo);
        $orderIds = [];


        return view('dashboard::delivery-details', compact(
            'pageTitle',
            'pageSubTitle',
            'region',
            'interval',
            'date',
            'customerGroups',
            'orderData',
            'totalRows',
            'startPageLink',
            'endPageLink',
            'totalPages',
            'pageNo',
            'orderIds',
            'orderStatuses',
            'serviceHelper'
        ));

    }

    public function getVendorStatus(Request $request) {

        $orderIds = (
            $request->has('orderids')
            && (trim($request->input('orderids')) != '')
        ) ? trim($request->input('orderids')) : '';

        if ($orderIds == '') {
            return response()->json([], 200);
        }

        $serviceHelper = new DashboardServiceHelper();

        $orderIdArray = explode(',', $orderIds);
        $orderIdsClean = array_map('trim', $orderIdArray);
        $vendorResponse = $serviceHelper->getOrderVendorStatus($orderIdsClean);

        $vendorStatusList = [];
        foreach($vendorResponse as $vendor) {
            $vendorStatusList[$vendor['main_order_id']] = $vendor['status'];
        }

        return response()->json($vendorStatusList, 200);

    }

    public function fetchChannelOrders(Request $request) {

        $serviceHelper = new DashboardServiceHelper();

        $apiChannel = (
            $request->has('api_channel')
            && (trim($request->input('api_channel')) != '')
        ) ? trim($request->input('api_channel')) : $serviceHelper->getApiChannel();

        $startDate = (
            $request->has('api_channel_date_start')
            && (trim($request->input('api_channel_date_start')) != '')
        ) ? trim($request->input('api_channel_date_start')) : date('Y-m-d', strtotime('-3 days'));

        $endDate = (
            $request->has('api_channel_date_end')
            && (trim($request->input('api_channel_date_end')) != '')
        ) ? trim($request->input('api_channel_date_end')) : date('Y-m-d', strtotime('+10 days'));

        $sessionUser = session('authUserData');
        SaleOrderChannelImport::dispatch($apiChannel, $startDate, $endDate, $sessionUser['id']);

        return response()->json([ 'message' => 'The sale orders will be fetched in the background' ], 200);

    }

}
