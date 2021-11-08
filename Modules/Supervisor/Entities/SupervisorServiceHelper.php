<?php


namespace Modules\Supervisor\Entities;

use Modules\Base\Entities\RestApiService;
use Modules\Sales\Entities\SaleOrder;
use DB;
use Modules\Base\Entities\BaseServiceHelper;
use App\Models\User;
use Modules\Sales\Entities\SaleOrderProcessHistory;

class SupervisorServiceHelper
{

    private $restApiService = null;
    private $baseService = null;

    public function __construct($channel = '')
    {
        $this->baseService = new BaseServiceHelper();
        $this->restApiService = new RestApiService();
        $this->setApiChannel($channel);
    }

    public function getApiEnvironment() {
        return $this->restApiService->getApiEnvironment();
    }

    /**
     * Get the current RESTFul API Channel.
     * @return string
     */
    public function getApiChannel() {
        return $this->restApiService->getCurrentApiChannel();
    }

    /**
     * Switch to the given RESTFul API Channel
     *
     * @param string $channel
     */
    public function setApiChannel($channel = '') {
        if ($this->restApiService->isValidApiChannel($channel)) {
            $this->restApiService->setApiChannel($channel);
        }
    }

    /**
     * Get the list of all the available API Channels.
     *
     * @return array
     */
    public function getAllAvailableChannels() {
        return $this->restApiService->getAllAvailableApiChannels();
    }

    /**
     * Get the given DateTime string in the given DateTime format
     *
     * @param string $dateTimeString
     * @param string $format
     *
     * @return string
     */
    public function getFormattedTime($dateTimeString = '', $format = '') {

        if (is_null($dateTimeString) || (trim($dateTimeString) == '')) {
            return '';
        }

        if (is_null($format) || (trim($format) == '')) {
            $format = \DateTime::ISO8601;
        }

        $appTimeZone = config('app.timezone');
        $channelTimeZone = $this->restApiService->getApiTimezone();
        $zoneList = timezone_identifiers_list();
        $cleanZone = (in_array(trim($channelTimeZone), $zoneList)) ? trim($channelTimeZone) : $appTimeZone;

        try {
            $dtObj = new \DateTime($dateTimeString, new \DateTimeZone($appTimeZone));
            $dtObj->setTimezone(new \DateTimeZone($cleanZone));
            return $dtObj->format($format);
        } catch (\Exception $e) {
            return '';
        }

    }

    public function getSupervisorsAllowedStatuses() {
        $statusList = config('goodbasket.order_statuses');
        $allowedStatusList = config('goodbasket.role_allowed_statuses.supervisor');
        $statusListClean = [];
        if(!is_null($allowedStatusList) && is_array($allowedStatusList) && (count($allowedStatusList) > 0)) {
            foreach ($allowedStatusList as $loopStatus) {
                $statusKey = strtolower(str_replace(' ', '_', trim($loopStatus)));
                $statusValue = ucwords(str_replace('_', ' ', trim($statusKey)));
                $statusListClean[$statusKey] = (array_key_exists($statusKey, $statusList) ? $statusList[$statusKey] : $statusValue);
            }
        }
        return $statusListClean;
    }

    public function getDeliveryTimeSlots() {
        $statusList = $this->getSupervisorsAllowedStatuses();
        $orders = SaleOrder::whereIn('order_status', array_keys($statusList))
            ->groupBy('delivery_time_slot')
            ->select('delivery_time_slot', DB::raw('count(*) as total_orders'))
            ->get();
        $timeSlotArray = [];
        if ($orders && (count($orders) > 0)) {
            foreach ($orders as $orderEl) {
                $timeSlotArray[] = $orderEl->delivery_time_slot;
            }
        }
        return $timeSlotArray;
    }

    public function getSupervisorOrders($region = '', $apiChannel = '', $status = '', $deliveryDate = '', $timeSlot = '') {

        $orderRequest = SaleOrder::select('*');

        $emirates = config('goodbasket.emirates');
        if (!is_null($region) && (trim($region) != '')) {
            $orderRequest->where('region_code', trim($region));
        } else {
            $orderRequest->whereIn('region_code', array_keys($emirates));
        }

        $availableApiChannels = $this->getAllAvailableChannels();
        if (!is_null($apiChannel) && (trim($apiChannel) != '')) {
            $orderRequest->where('channel', trim($apiChannel));
        } else {
            $orderRequest->whereIn('channel', array_keys($availableApiChannels));
        }

        $availableStatuses = $this->getSupervisorsAllowedStatuses();
        if (!is_null($status) && (trim($status) != '')) {
            $orderRequest->where('order_status', trim($status));
        } else {
            $orderRequest->whereIn('order_status', array_keys($availableStatuses));
        }

        if (!is_null($deliveryDate) && (trim($deliveryDate) != '')) {
            $orderRequest->where('delivery_date', trim($deliveryDate));
        }

        if (!is_null($timeSlot) && (trim($timeSlot) != '')) {
            $orderRequest->where('delivery_time_slot', trim($timeSlot));
        }

        $orderRequest->orderBy('delivery_date', 'asc');

        return $orderRequest->get();

    }

    public function getCustomerGroups() {

        $uri = $this->restApiService->getRestApiUrl() . 'customerGroups/search';
        $qParams = [
            'searchCriteria' => '?'
        ];
        $apiResult = $this->restApiService->processGetApi($uri, $qParams, [], true, true);

        return ($apiResult['status']) ? $apiResult['response'] : [];

    }

    public function getVendorsList() {

        $uri = $this->restApiService->getRestApiUrl() . 'vendors';
        $apiResult = $this->restApiService->processGetApi($uri, [], [], true, true);

        return ($apiResult['status']) ? $apiResult['response'] : [];

    }

    public function getFileUrl($path = '') {
        return $this->baseService->getFileUrl($path);
    }

    public function getUserImageUrl($path = '') {
        return $this->baseService->getFileUrl('media/images/users/' . $path);
    }

    public function isPickerAssigned(User $picker = null) {
        $assignmentObj = null;
        if (is_null($picker) && (count($picker->saleOrderProcessHistory) > 0)) {
            foreach ($picker->saleOrderProcessHistory as $processHistory) {
                if ($processHistory->action == SaleOrderProcessHistory::SALE_ORDER_PROCESS_ACTION_PICKUP) {
                    if (
                        ($processHistory->saleOrder)
                        && ($processHistory->saleOrder->order_status == SaleOrder::SALE_ORDER_STATUS_BEING_PREPARED)
                    ) {
                        $assignmentObj = $processHistory;
                    }
                }
            }
        }
        return $assignmentObj;
    }

    public function isDriverAssigned(User $driver = null) {
        $assignmentObj = null;
        if (is_null($driver) && (count($driver->saleOrderProcessHistory) > 0)) {
            foreach ($driver->saleOrderProcessHistory as $processHistory) {
                if ($processHistory->action == SaleOrderProcessHistory::SALE_ORDER_PROCESS_ACTION_DELIVERY) {
                    if (
                        ($processHistory->saleOrder)
                        && (
                            ($processHistory->saleOrder->order_status == SaleOrder::SALE_ORDER_STATUS_READY_TO_DISPATCH)
                            || ($processHistory->saleOrder->order_status == SaleOrder::SALE_ORDER_STATUS_OUT_FOR_DELIVERY)
                        )
                    ) {
                        $assignmentObj = $processHistory;
                    }
                }
            }
        }
        return $assignmentObj;
    }

}
