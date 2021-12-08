<?php

namespace Modules\Driver\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\API\Entities\MobileAppUser;
use Modules\API\Http\Controllers\BaseController;
use Validator;
use Hash;
use Modules\Driver\Entities\DriverApiServiceHelper;
use Modules\Sales\Entities\SaleOrder;
use Modules\Sales\Entities\SaleOrderProcessHistory;
use Modules\UserRole\Entities\UserRole;
use Modules\UserRole\Entities\UserRoleMap;
use Modules\API\Entities\ApiServiceHelper;

class ApiController extends BaseController
{

    public function generateDriverToken(Request $request) {

        $validator = Validator::make($request->all() , [
            'username'   => ['required', 'email'],
            'password' => ['required', 'min:6'],
            'deviceName' => ['required'],
        ], [
            'username.required' => 'EMail Address should be provided.',
            'username.email' => 'EMail Address should be valid.',
            'password.required' => 'Password should be provided.',
            'password.min' => 'Password should be minimum :min characters.',
            'deviceName.required' => 'Device Name should be minimum :min characters.',
        ]);
        if ($validator->fails()) {
            $errMessage = implode(" | ", $validator->errors());
            return $this->sendError($errMessage, ['error' => $errMessage], ApiServiceHelper::HTTP_STATUS_CODE_BAD_REQUEST);
        }

        $user = User::where('email', $request->username)->first();
        if (! $user || ! Hash::check($request->password, $user->password)) {
            $errMessage = 'User Authentication failed!';
            return $this->sendError($errMessage, ['error' => $errMessage], ApiServiceHelper::HTTP_STATUS_CODE_UNAUTHORIZED);
        }

        $roleMapData = UserRoleMap::firstWhere('user_id', $user->id);
        if (!$roleMapData) {
            $errMessage = 'The User not assigned to any role!';
            return $this->sendError($errMessage, ['error' => $errMessage], ApiServiceHelper::HTTP_STATUS_CODE_UNAUTHORIZED);
        }

        $mappedRoleId = $roleMapData->role_id;
        $roleData = UserRole::find($mappedRoleId);
        if (!$roleData) {
            $errMessage = 'The User not assigned to any role!';
            return $this->sendError($errMessage, ['error' => $errMessage], ApiServiceHelper::HTTP_STATUS_CODE_UNAUTHORIZED);
        }

        if (!$roleData->isDriver()) {
            $errMessage = 'The User is not a Driver!';
            return $this->sendError($errMessage, ['error' => $errMessage], ApiServiceHelper::HTTP_STATUS_CODE_UNAUTHORIZED);
        }

        $token = $user->createToken($request->deviceName)->plainTextToken;

        $mobileAppUser = MobileAppUser::updateOrCreate([
            'user_id' => $user->id
        ], [
            'role_id' => $roleData->id,
            'access_token' => $token,
            'device_id' => $request->deviceName,
            'logged_in' => 1,
        ]);

        $returnData = [
            'token' => $token,
            'token_type' => 'Bearer',
            'name' => $user->name,
        ];
        return $this->sendResponse($returnData, 'Hi '.$user->name.', welcome to home');

    }

    public function getRecentAssignedOrders(Request $request) {

        $serviceHelper = new DriverApiServiceHelper();

        $user = auth()->user();
        $userId = $user->id;
        $validStatus = $serviceHelper->isValidApiUser($userId);
        if ($validStatus['success'] === false) {
            return $this->sendError($validStatus['message'], ['error' => $validStatus['message']], $validStatus['httpStatus']);
        }

        $pageStart = (
            $request->has('page')
            && (trim($request->input('page')) != '')
        ) ? (int)trim($request->input('page')) : 0;

        $pageLength = (
            $request->has('limit')
            && (trim($request->input('limit')) != '')
        ) ? (int)trim($request->input('limit')) : 10;

        $filterStatus = [
            SaleOrder::SALE_ORDER_STATUS_READY_TO_DISPATCH
        ];
        $filteredOrders = $serviceHelper->getDriverOrders('', '', $filterStatus, '', '');
        if (!$filteredOrders) {
            return $this->sendResponse([], 'No Orders Found!');
        }

        $emirates = config('goodbasket.emirates');
        $availableApiChannels = $serviceHelper->getAllAvailableChannels();
        $availableStatuses = $serviceHelper->getDriversAllowedStatuses();
        $filteredOrderData = [];
        $totalRec = 0;
        $collectRecStart = $pageStart;
        $collectRecEnd = $collectRecStart + $pageLength;
        $currentRec = -1;
        foreach ($filteredOrders as $record) {
            $deliveryDriverData = $record->currentDriver;
            $canProceed = false;
            $driverDetail = null;
            if ($deliveryDriverData && (count($deliveryDriverData) > 0)) {
                foreach ($deliveryDriverData as $dDeliver) {
                    if (($userId > 0) && !is_null($dDeliver->done_by) && ((int)$dDeliver->done_by == $userId)) {
                        $canProceed = true;
                        $driverDetail = $dDeliver;
                    }
                }
            }
            if ($canProceed) {
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
                $tempRecord['city'] = $record->city;
                $tempRecord['zoneId'] = $record->zone_id;
                $shipAddress = $record->shippingAddress;
                $tempRecord['customerName'] = $shipAddress->first_name . ' ' . $shipAddress->last_name;
                $tempRecord['deliveryDate'] = $record->delivery_date;
                $tempRecord['deliveryTimeSlot'] = $record->delivery_time_slot;
                $tempRecord['deliveryPickerTime'] = '';
                $tempRecord['deliveryDriverTime'] = '';
                $orderStatusId = $record->order_status;
                $tempRecord['orderStatus'] = $availableStatuses[$orderStatusId];
                $deliveryPickerData = $record->pickedData;
                if ($deliveryPickerData) {
                    $tempRecord['deliveryPickerTime'] = $deliveryPickerData->done_at;
                }
                if (!is_null($driverDetail)) {
                    $tempRecord['deliveryDriverTime'] = $driverDetail->done_at;
                }
                $filteredOrderData[] = $tempRecord;
            }
        }

        return $this->sendResponse($filteredOrderData, count($filteredOrderData) . ' Order(s) Found!');

    }

    public function getDeliveryOrders(Request $request) {

        $serviceHelper = new DriverApiServiceHelper();

        $user = auth()->user();
        $userId = $user->id;
        $validStatus = $serviceHelper->isValidApiUser($userId);
        if ($validStatus['success'] === false) {
            return $this->sendError($validStatus['message'], ['error' => $validStatus['message']], $validStatus['httpStatus']);
        }

        $pageStart = (
            $request->has('page')
            && (trim($request->input('page')) != '')
        ) ? (int)trim($request->input('page')) : 0;

        $pageLength = (
            $request->has('limit')
            && (trim($request->input('limit')) != '')
        ) ? (int)trim($request->input('limit')) : 10;

        $filterStatus = [
            SaleOrder::SALE_ORDER_STATUS_OUT_FOR_DELIVERY
        ];
        $filteredOrders = $serviceHelper->getDriverOrders('', '', $filterStatus, '', '');
        if (!$filteredOrders) {
            return $this->sendResponse([], 'No Orders Found!');
        }

        $emirates = config('goodbasket.emirates');
        $availableApiChannels = $serviceHelper->getAllAvailableChannels();
        $availableStatuses = $serviceHelper->getDriversAllowedStatuses();
        $filteredOrderData = [];
        $totalRec = 0;
        $collectRecStart = $pageStart;
        $collectRecEnd = $collectRecStart + $pageLength;
        $currentRec = -1;
        foreach ($filteredOrders as $record) {
            $deliveryDriverData = $record->currentDriver;
            $canProceed = false;
            $driverDetail = null;
            if ($deliveryDriverData && (count($deliveryDriverData) > 0)) {
                foreach ($deliveryDriverData as $dDeliver) {
                    if (($userId > 0) && !is_null($dDeliver->done_by) && ((int)$dDeliver->done_by == $userId)) {
                        $canProceed = true;
                        $driverDetail = $dDeliver;
                    }
                }
            }
            if ($canProceed) {
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
                $tempRecord['city'] = $record->city;
                $tempRecord['zoneId'] = $record->zone_id;
                $shipAddress = $record->shippingAddress;
                $tempRecord['customerName'] = $shipAddress->first_name . ' ' . $shipAddress->last_name;
                $tempRecord['deliveryDate'] = $record->delivery_date;
                $tempRecord['deliveryTimeSlot'] = $record->delivery_time_slot;
                $tempRecord['deliveryPickerTime'] = '';
                $tempRecord['deliveryDriverTime'] = '';
                $orderStatusId = $record->order_status;
                $tempRecord['orderStatus'] = $availableStatuses[$orderStatusId];
                $deliveryPickerData = $record->pickedData;
                if ($deliveryPickerData) {
                    $tempRecord['deliveryPickerTime'] = $deliveryPickerData->done_at;
                }
                if (!is_null($driverDetail)) {
                    $tempRecord['deliveryDriverTime'] = $driverDetail->done_at;
                }
                $filteredOrderData[] = $tempRecord;
            }
        }

        return $this->sendResponse($filteredOrderData, count($filteredOrderData) . ' Order(s) Found!');

    }

    public function getDeliveredOrders(Request $request) {

        $serviceHelper = new DriverApiServiceHelper();

        $user = auth()->user();
        $userId = $user->id;
        $validStatus = $serviceHelper->isValidApiUser($userId);
        if ($validStatus['success'] === false) {
            return $this->sendError($validStatus['message'], ['error' => $validStatus['message']], $validStatus['httpStatus']);
        }

        $pageStart = (
            $request->has('page')
            && (trim($request->input('page')) != '')
        ) ? (int)trim($request->input('page')) : 0;

        $pageLength = (
            $request->has('limit')
            && (trim($request->input('limit')) != '')
        ) ? (int)trim($request->input('limit')) : 10;

        $filterStatus = [
            SaleOrder::SALE_ORDER_STATUS_DELIVERED,
            SaleOrder::SALE_ORDER_STATUS_CANCELED,
        ];
        $filteredOrders = $serviceHelper->getDriverOrders('', '', $filterStatus, '', '');
        if (!$filteredOrders) {
            return $this->sendResponse([], 'No Orders Found!');
        }

        $emirates = config('goodbasket.emirates');
        $availableApiChannels = $serviceHelper->getAllAvailableChannels();
        $availableStatuses = $serviceHelper->getDriversAllowedStatuses();
        $statusList = config('goodbasket.order_statuses');
        $filteredOrderData = [];
        $totalRec = 0;
        $collectRecStart = $pageStart;
        $collectRecEnd = $collectRecStart + $pageLength;
        $currentRec = -1;
        foreach ($filteredOrders as $record) {
            $deliveryDriverData = null;
            if ($record->order_status == SaleOrder::SALE_ORDER_STATUS_DELIVERED) {
                $deliveryDriverData = $record->deliveredData;
            } elseif ($record->order_status == SaleOrder::SALE_ORDER_STATUS_CANCELED) {
                $deliveryDriverData = $record->canceledData;
            }
            $canProceed = false;
            $driverDetail = null;
            if ($deliveryDriverData) {
                if (($userId > 0) && !is_null($deliveryDriverData->done_by) && ((int)$deliveryDriverData->done_by == $userId)) {
                    $canProceed = true;
                    $driverDetail = $deliveryDriverData;
                }
            }
            if ($canProceed) {
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
                $tempRecord['city'] = $record->city;
                $tempRecord['zoneId'] = $record->zone_id;
                $shipAddress = $record->shippingAddress;
                $tempRecord['customerName'] = $shipAddress->first_name . ' ' . $shipAddress->last_name;
                $tempRecord['deliveryDate'] = $record->delivery_date;
                $tempRecord['deliveryTimeSlot'] = $record->delivery_time_slot;
                $tempRecord['deliveryPickerTime'] = '';
                $tempRecord['deliveryDriverTime'] = '';
                $orderStatusId = $record->order_status;
                $tempRecord['orderStatus'] = $statusList[$orderStatusId];
                $deliveryPickerData = $record->pickedData;
                if ($deliveryPickerData) {
                    $tempRecord['deliveryPickerTime'] = $deliveryPickerData->done_at;
                }
                if (!is_null($driverDetail)) {
                    $tempRecord['deliveryDriverTime'] = $driverDetail->done_at;
                }
                $filteredOrderData[] = $tempRecord;
            }
        }

        return $this->sendResponse($filteredOrderData, count($filteredOrderData) . ' Order(s) Found!');

    }

    public  function getOrderDetails(Request $request) {

        $serviceHelper = new DriverApiServiceHelper();

        $user = auth()->user();
        $userId = $user->id;
        $validStatus = $serviceHelper->isValidApiUser($userId);
        if ($validStatus['success'] === false) {
            return $this->sendError($validStatus['message'], ['error' => $validStatus['message']], $validStatus['httpStatus']);
        }

        $givenOrderId = (
            $request->has('orderId')
            && (trim($request->input('orderId')) != '')
            && is_numeric($request->input('orderId'))
            && ((int)trim($request->input('orderId')) > 0)
        ) ? (int)trim($request->input('orderId')) : null;
        if (is_null($givenOrderId)) {
            $errMessage = 'Sale Order Not found!';
            return $this->sendError($errMessage, ['error' => $errMessage], ApiServiceHelper::HTTP_STATUS_CODE_NOT_FOUND);
        }

        $allowedReqStatuses = [
            'pickup' => SaleOrder::SALE_ORDER_STATUS_READY_TO_DISPATCH,
            'delivery' => SaleOrder::SALE_ORDER_STATUS_OUT_FOR_DELIVERY,
            'delivered' => SaleOrder::SALE_ORDER_STATUS_DELIVERED,
            'canceled' => SaleOrder::SALE_ORDER_STATUS_CANCELED,
        ];
        $givenAction = (
            $request->has('orderState')
            && (trim($request->input('orderState')) != '')
        ) ? trim($request->input('orderState')) : null;
        if (!is_null($givenAction) && !in_array($givenAction, array_keys($allowedReqStatuses))) {
            $errMessage = 'Sale Order not accessible!';
            return $this->sendError($errMessage, ['error' => $errMessage], ApiServiceHelper::HTTP_STATUS_CODE_NOT_FOUND);
        }

        $saleOrderObj = SaleOrder::find($givenOrderId);
        if (!$saleOrderObj) {
            $errMessage = 'Sale Order Not found!';
            return $this->sendError($errMessage, ['error' => $errMessage], ApiServiceHelper::HTTP_STATUS_CODE_NOT_FOUND);
        }

        if (
            (
                is_null($givenAction)
                && !in_array($saleOrderObj->order_status, array_values($allowedReqStatuses))
            )
            || (
                !is_null($givenAction)
                && ($saleOrderObj->order_status !== $allowedReqStatuses[$givenAction])
            )
        ) {
            $errMessage = 'The Sale Order not accessible!';
            return $this->sendError($errMessage, ['error' => $errMessage], ApiServiceHelper::HTTP_STATUS_CODE_UNAUTHORIZED);
        }

        $canProceed = false;
        $driverDetail = null;
        $isOrderCanceled = false;
        if (
            ($saleOrderObj->order_status === SaleOrder::SALE_ORDER_STATUS_READY_TO_DISPATCH)
            || ($saleOrderObj->order_status === SaleOrder::SALE_ORDER_STATUS_OUT_FOR_DELIVERY)
        ) {
            $deliveryDriverData = $saleOrderObj->currentDriver;
            if ($deliveryDriverData && (count($deliveryDriverData) > 0)) {
                foreach ($deliveryDriverData as $dDeliver) {
                    if (($userId > 0) && !is_null($dDeliver->done_by) && ((int)$dDeliver->done_by == $userId)) {
                        $canProceed = true;
                        $driverDetail = $dDeliver;
                    }
                }
            }
            if (!$canProceed) {
                $errMessage = 'The Sale Order is not assigned to the user!';
                return $this->sendError($errMessage, ['error' => $errMessage], ApiServiceHelper::HTTP_STATUS_CODE_UNAUTHORIZED);
            }
        } elseif ($saleOrderObj->order_status === SaleOrder::SALE_ORDER_STATUS_DELIVERED) {
            $deliveryDriverData = $saleOrderObj->deliveredData;
            if ($deliveryDriverData) {
                if (($userId > 0) && !is_null($deliveryDriverData->done_by) && ((int)$deliveryDriverData->done_by == $userId)) {
                    $canProceed = true;
                    $driverDetail = $deliveryDriverData;
                }
            }
            if (!$canProceed) {
                $errMessage = 'The Sale Order is not delivered by the user!';
                return $this->sendError($errMessage, ['error' => $errMessage], ApiServiceHelper::HTTP_STATUS_CODE_UNAUTHORIZED);
            }
        } elseif ($saleOrderObj->order_status === SaleOrder::SALE_ORDER_STATUS_CANCELED) {
            $deliveryDriverData = $saleOrderObj->canceledData;
            if ($deliveryDriverData) {
                if (($userId > 0) && !is_null($deliveryDriverData->done_by) && ((int)$deliveryDriverData->done_by == $userId)) {
                    $canProceed = true;
                    $isOrderCanceled = true;
                    $driverDetail = $deliveryDriverData;
                }
            }
            if (!$canProceed) {
                $errMessage = 'The Sale Order is not canceled by the user!';
                return $this->sendError($errMessage, ['error' => $errMessage], ApiServiceHelper::HTTP_STATUS_CODE_UNAUTHORIZED);
            }
        }

        $emirates = config('goodbasket.emirates');
        $availableApiChannels = $serviceHelper->getAllAvailableChannels();
        $statusList = config('goodbasket.order_statuses');

        $saleOrderObj->saleCustomer;
        $saleOrderObj->orderItems;
        $saleOrderObj->billingAddress;
        $saleOrderObj->shippingAddress;
        $saleOrderObj->paymentData;
        $saleOrderObj->statusHistory;
        $saleOrderData = $saleOrderObj->toArray();

        $returnData = [
            'recordId' => $saleOrderData['id'],
            'orderId' => $saleOrderData['order_id'],
            'incrementId' => $saleOrderData['increment_id'],
            'channel' => (!is_null($saleOrderData['channel']) && array_key_exists($saleOrderData['channel'], $availableApiChannels)) ? $availableApiChannels[$saleOrderData['channel']]['name'] : $saleOrderData['channel'],
            'region' => (!is_null($saleOrderData['region_code']) && array_key_exists($saleOrderData['region_code'], $emirates)) ? $emirates[$saleOrderData['region_code']] : $saleOrderData['region_code'],
            'city' => $saleOrderData['city'],
            'zoneId' => $saleOrderData['zone_id'],
            'orderCreatedAt' => (!is_null($saleOrderData['order_created_at']) && strtotime($saleOrderData['order_created_at'])) ? date('Y-m-d H:i:s', strtotime($saleOrderData['order_created_at'])) : $saleOrderData['order_created_at'],
            'deliveryDate' => $saleOrderData['delivery_date'],
            'deliveryTimeSlot' => $saleOrderData['delivery_time_slot'],
            'totalQtyOrdered' => $saleOrderData['total_qty_ordered'],
            'orderWeight' => $saleOrderData['order_weight'],
            'boxCount' => $saleOrderData['box_count'],
            'orderCurrency' => $saleOrderData['order_currency'],
            'orderSubtotal' => $saleOrderData['order_subtotal'],
            'orderTax' => $saleOrderData['order_tax'],
            'discountAmount' => $saleOrderData['discount_amount'],
            'shippingTotal' => $saleOrderData['shipping_total'],
            'shippingMethod' => $saleOrderData['shipping_method'],
            'orderTotal' => $saleOrderData['order_total'],
            'orderDue' => $saleOrderData['order_due'],
            'orderStatus' => (!is_null($saleOrderData['order_status']) && array_key_exists($saleOrderData['order_status'], $statusList)) ? $statusList[$saleOrderData['order_status']] : $saleOrderData['order_status'],
            'orderItems' => [],
            'shippingAddress' => [],
            'deliveryPickerTime' => '',
            'orderDeliveredTime' => '',
            'orderCanceledTime' => '',
        ];

        if (!is_null($saleOrderData['order_items']) && is_array($saleOrderData['order_items']) && (count($saleOrderData['order_items']) > 0)) {
            $orderItems = $saleOrderData['order_items'];
            foreach ($orderItems as $orderItemEl) {
                $returnData['orderItems'][] = [
                    'itemId' => $orderItemEl['item_id'],
                    'productId' => $orderItemEl['product_id'],
                    'productType' => $orderItemEl['product_type'],
                    'itemSku' => $orderItemEl['item_sku'],
                    'itemBarcode' => $orderItemEl['item_barcode'],
                    'itemName' => $orderItemEl['item_name'],
                    'itemInfo' => $orderItemEl['item_info'],
                    'itemImage' => $orderItemEl['item_image'],
                    'qtyOrdered' => $orderItemEl['qty_ordered'],
                    'sellingUnit' => $orderItemEl['selling_unit'],
                    'sellingUnitLabel' => $orderItemEl['selling_unit_label'],
                    'price' => $orderItemEl['price'],
                    'rowTotal' => $orderItemEl['row_total'],
                    'taxAmount' => $orderItemEl['tax_amount'],
                    'discountAmount' => $orderItemEl['discount_amount'],
                    'rowGrandTotal' => $orderItemEl['row_grand_total'],
                ];
            }
        }

        if (!is_null($saleOrderData['shipping_address']) && is_array($saleOrderData['shipping_address']) && (count($saleOrderData['shipping_address']) > 0)) {
            $shippingAddress = $saleOrderData['shipping_address'];
            $returnData['shippingAddress'] = [
                'firstName' => $shippingAddress['first_name'],
                'lastName' => $shippingAddress['last_name'],
                'address1' => $shippingAddress['address_1'],
                'address2' => $shippingAddress['address_2'],
                'address3' => $shippingAddress['address_3'],
                'city' => $shippingAddress['city'],
                'region' => (!is_null($shippingAddress['region_code']) && array_key_exists($shippingAddress['region_code'], $emirates)) ? $emirates[$shippingAddress['region_code']] : $shippingAddress['region_code'],
                'countryId' => $shippingAddress['country_id'],
                'postCode' => $shippingAddress['post_code'],
                'contactNumber' => $shippingAddress['contact_number'],
            ];
        }

        $deliveryPickerData = $saleOrderObj->pickedData;
        if ($deliveryPickerData) {
            $returnData['deliveryPickerTime'] = $deliveryPickerData->done_at;
        }
        if (!is_null($driverDetail) && !$isOrderCanceled) {
            $returnData['orderDeliveredTime'] = $driverDetail->done_at;
        }
        if (!is_null($driverDetail) && $isOrderCanceled) {
            $returnData['orderCanceledTime'] = $driverDetail->done_at;
        }

        return $this->sendResponse($returnData, 'The Sale Order fetched successfully!');

    }

    public function setOrderForDelivery(Request $request) {

        $serviceHelper = new DriverApiServiceHelper();

        $user = auth()->user();
        $userId = $user->id;
        $validStatus = $serviceHelper->isValidApiUser($userId);
        if ($validStatus['success'] === false) {
            return $this->sendError($validStatus['message'], ['error' => $validStatus['message']], $validStatus['httpStatus']);
        }

        $givenOrderId = (
            $request->has('orderId')
            && (trim($request->input('orderId')) != '')
            && is_numeric($request->input('orderId'))
            && ((int)trim($request->input('orderId')) > 0)
        ) ? (int)trim($request->input('orderId')) : null;

        if (is_null($givenOrderId)) {
            $errMessage = 'Sale Order Not found!';
            return $this->sendError($errMessage, ['error' => $errMessage], ApiServiceHelper::HTTP_STATUS_CODE_NOT_FOUND);
        }

        $saleOrderObj = SaleOrder::find($givenOrderId);
        if (!$saleOrderObj) {
            $errMessage = 'Sale Order Not found!';
            return $this->sendError($errMessage, ['error' => $errMessage], ApiServiceHelper::HTTP_STATUS_CODE_NOT_FOUND);
        }

        $allowedStatuses = [
            SaleOrder::SALE_ORDER_STATUS_READY_TO_DISPATCH
        ];
        if (!in_array($saleOrderObj->order_status, $allowedStatuses)) {
            $errMessage = 'The Sale Order Status cannot be changed!';
            return $this->sendError($errMessage, ['error' => $errMessage], ApiServiceHelper::HTTP_STATUS_CODE_NOT_FOUND);
        }

        $canProceed = false;
        if ($saleOrderObj->currentDriver && (count($saleOrderObj->currentDriver) > 0)) {
            $currentHistory = $saleOrderObj->currentDriver[0];
            if ($currentHistory->done_by === $userId) {
                $canProceed = true;
            }
        }
        if (!$canProceed) {
            $errMessage = 'The Sale Order is not assigned to the user!';
            return $this->sendError($errMessage, ['error' => $errMessage], ApiServiceHelper::HTTP_STATUS_CODE_UNAUTHORIZED);
        }

        $newStatus = SaleOrder::SALE_ORDER_STATUS_OUT_FOR_DELIVERY;
        $returnResult = $serviceHelper->changeSaleOrderStatus($saleOrderObj, $newStatus, $userId);
        if (!$returnResult['status']) {
            return $this->sendError($returnResult['message'], ['error' => $returnResult['message']], ApiServiceHelper::HTTP_STATUS_CODE_NOT_FOUND);
        }

        return $this->sendResponse([], 'The Sale Order is now ready for delivery!');

    }

    public function setOrderAsDelivered(Request $request) {

        $serviceHelper = new DriverApiServiceHelper();

        $user = auth()->user();
        $userId = $user->id;
        $validStatus = $serviceHelper->isValidApiUser($userId);
        if ($validStatus['success'] === false) {
            return $this->sendError($validStatus['message'], ['error' => $validStatus['message']], $validStatus['httpStatus']);
        }

        $givenOrderId = (
            $request->has('orderId')
            && (trim($request->input('orderId')) != '')
            && is_numeric($request->input('orderId'))
            && ((int)trim($request->input('orderId')) > 0)
        ) ? (int)trim($request->input('orderId')) : null;

        if (is_null($givenOrderId)) {
            $errMessage = 'Sale Order Not found!';
            return $this->sendError($errMessage, ['error' => $errMessage], ApiServiceHelper::HTTP_STATUS_CODE_NOT_FOUND);
        }

        $saleOrderObj = SaleOrder::find($givenOrderId);
        if (!$saleOrderObj) {
            $errMessage = 'Sale Order Not found!';
            return $this->sendError($errMessage, ['error' => $errMessage], ApiServiceHelper::HTTP_STATUS_CODE_NOT_FOUND);
        }

        $allowedStatuses = [
            SaleOrder::SALE_ORDER_STATUS_OUT_FOR_DELIVERY
        ];
        if (!in_array($saleOrderObj->order_status, $allowedStatuses)) {
            $errMessage = 'The Sale Order Status cannot be changed!';
            return $this->sendError($errMessage, ['error' => $errMessage], ApiServiceHelper::HTTP_STATUS_CODE_NOT_FOUND);
        }

        $canProceed = false;
        if ($saleOrderObj->currentDriver && (count($saleOrderObj->currentDriver) > 0)) {
            $currentHistory = $saleOrderObj->currentDriver[0];
            if ($currentHistory->done_by === $userId) {
                $canProceed = true;
            }
        }
        if (!$canProceed) {
            $errMessage = 'The Sale Order is not assigned to the user!';
            return $this->sendError($errMessage, ['error' => $errMessage], ApiServiceHelper::HTTP_STATUS_CODE_UNAUTHORIZED);
        }

        $newStatus = SaleOrder::SALE_ORDER_STATUS_DELIVERED;
        $returnResult = $serviceHelper->changeSaleOrderStatus($saleOrderObj, $newStatus, $userId);
        if (!$returnResult['status']) {
            return $this->sendError($returnResult['message'], ['error' => $returnResult['message']], ApiServiceHelper::HTTP_STATUS_CODE_NOT_FOUND);
        }

        return $this->sendResponse([], 'The Sale Order is delivered successfully!');

    }

    public function setOrderAsCanceled(Request $request) {

        $serviceHelper = new DriverApiServiceHelper();

        $user = auth()->user();
        $userId = $user->id;
        $validStatus = $serviceHelper->isValidApiUser($userId);
        if ($validStatus['success'] === false) {
            return $this->sendError($validStatus['message'], ['error' => $validStatus['message']], $validStatus['httpStatus']);
        }

        $givenOrderId = (
            $request->has('orderId')
            && (trim($request->input('orderId')) != '')
            && is_numeric($request->input('orderId'))
            && ((int)trim($request->input('orderId')) > 0)
        ) ? (int)trim($request->input('orderId')) : null;

        if (is_null($givenOrderId)) {
            $errMessage = 'Sale Order Not found!';
            return $this->sendError($errMessage, ['error' => $errMessage], ApiServiceHelper::HTTP_STATUS_CODE_NOT_FOUND);
        }

        $saleOrderObj = SaleOrder::find($givenOrderId);
        if (!$saleOrderObj) {
            $errMessage = 'Sale Order Not found!';
            return $this->sendError($errMessage, ['error' => $errMessage], ApiServiceHelper::HTTP_STATUS_CODE_NOT_FOUND);
        }

        $allowedStatuses = [
            SaleOrder::SALE_ORDER_STATUS_OUT_FOR_DELIVERY
        ];
        if (!in_array($saleOrderObj->order_status, $allowedStatuses)) {
            $errMessage = 'The Sale Order Status cannot be changed!';
            return $this->sendError($errMessage, ['error' => $errMessage], ApiServiceHelper::HTTP_STATUS_CODE_NOT_FOUND);
        }

        $canProceed = false;
        if ($saleOrderObj->currentDriver && (count($saleOrderObj->currentDriver) > 0)) {
            $currentHistory = $saleOrderObj->currentDriver[0];
            if ($currentHistory->done_by === $userId) {
                $canProceed = true;
            }
        }
        if (!$canProceed) {
            $errMessage = 'The Sale Order is not assigned to the user!';
            return $this->sendError($errMessage, ['error' => $errMessage], ApiServiceHelper::HTTP_STATUS_CODE_UNAUTHORIZED);
        }

        $newStatus = SaleOrder::SALE_ORDER_STATUS_CANCELED;
        $returnResult = $serviceHelper->changeSaleOrderStatus($saleOrderObj, $newStatus, $userId);
        if (!$returnResult['status']) {
            return $this->sendError($returnResult['message'], ['error' => $returnResult['message']], ApiServiceHelper::HTTP_STATUS_CODE_NOT_FOUND);
        }

        return $this->sendResponse([], 'The Sale Order is canceled successfully!');

    }

}
