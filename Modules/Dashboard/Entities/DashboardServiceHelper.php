<?php


namespace Modules\Dashboard\Entities;

use Modules\Base\Entities\RestApiService;

class DashboardServiceHelper
{

    private $restApiService = null;

    public function __construct($channel = '')
    {
        $this->restApiService = new RestApiService();
        $this->setApiChannel($channel);
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

    public function getOrdersCountByRegion($region = '') {

        if (is_null($region) || (trim($region) == '')) {
            return [];
        }

        $uri = $this->restApiService->getRestApiUrl() . 'getorderscountbyregion';
        $qParams = [
            'region' => trim($region)
        ];
        $apiResult = $this->restApiService->processGetApi($uri, $qParams);

        return ($apiResult['status']) ? $apiResult['response'] : [];

    }

    public function getOrdersByRegion($region = '', $interval = '', $date = '', $pageSize = 0, $currentPage = 0) {

        if (
            (is_null($region) || (trim($region) == ''))
            || (is_null($interval) || (trim($interval) == ''))
            || (is_null($date) || (trim($date) == ''))
        ) {
            return [];
        }

        $pageSizeClean = (is_numeric(trim($pageSize))) ? trim((int)$pageSize) : 0;
        $currentPageClean = (is_numeric(trim($currentPage))) ? trim((int)$currentPage) : 0;

        $uri = $this->restApiService->getRestApiUrl() . 'getordersbyregion';
        $qParams = [
            'region' => trim($region),
            'timeInterval' => trim($interval),
            'date' => trim($date),
            'pageSize' => trim($pageSizeClean),
            'currentPage' => trim($currentPageClean),
        ];
        $apiResult = $this->restApiService->processGetApi($uri, $qParams);

        return ($apiResult['status']) ? $apiResult['response'] : [];

    }

    public function getDriversByDate($dateString = '') {

        if (is_null($dateString) || (trim($dateString) == '')) {
            return [];
        }

        $uri = $this->restApiService->getRestApiUrl() . 'getdriversbydate';
        $qParams = [
            'date' => trim($dateString)
        ];
        $apiResult = $this->restApiService->processGetApi($uri, $qParams);

        return ($apiResult['status']) ? $apiResult['response'] : [];

    }

    public function getCustomerGroups() {

        $uri = $this->restApiService->getRestApiUrl() . 'customerGroups/search';
        $qParams = [
            'searchCriteria' => '?'
        ];
        $apiResult = $this->restApiService->processGetApi($uri, $qParams);

        return ($apiResult['status']) ? $apiResult['response'] : [];

    }

    public function getOrderVendorStatus($orderIds = []) {

        if (!is_array($orderIds) || (is_array($orderIds) && (count($orderIds) == 0))) {
            return [];
        }

        $uri = $this->restApiService->getRestApiUrl() . 'vendors/orderstatus';
        $qParams = [
            'orderId' => implode(',', $orderIds)
        ];
        $apiResult = $this->restApiService->processGetApi($uri, $qParams);

        return ($apiResult['status']) ? $apiResult['response'] : [];

    }

    public function getAvailableRegionsList($countryId = '') {

        if (is_null($countryId) || (is_string($countryId) && (trim($countryId) == ''))) {
            $countryId = $this->restApiService->getApiDefaultCountry();
        }

        $uri = $this->restApiService->getRestApiUrl() . 'directory/countries/' . $countryId;
        $apiResult = $this->restApiService->processGetApi($uri);

        return ($apiResult['status']) ? $apiResult['response'] : [];

    }

    public function getAvailableCityList($countryId = '') {

        if (is_null($countryId) || (is_string($countryId) && (trim($countryId) == ''))) {
            $countryId = $this->restApiService->getApiDefaultCountry();
        }

        $uri = $this->restApiService->getRestApiUrl() . 'directory/areas/' . $countryId;
        $apiResult = $this->restApiService->processGetApi($uri);

        return ($apiResult['status']) ? $apiResult['response'] : [];

    }

}
