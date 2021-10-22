<?php


namespace Modules\Dashboard\Entities;

use Modules\Base\Entities\RestApiService;

class DashboardServiceHelper
{

    public function __construct()
    {
    }

    public function getOrdersCountByRegion($region = '') {

        if (is_null($region) || (trim($region) == '')) {
            return [];
        }

        $restApiObj = new RestApiService();
        $uri = $restApiObj->getRestApiUrl() . 'getorderscountbyregion?region=' . urlencode($region);
        $apiResult = $restApiObj->processGetApi($uri);

        return ($apiResult['status']) ? $apiResult['response'] : [];

    }

    public function getDriversByDate($dateString = '') {

        if (is_null($dateString) || (trim($dateString) == '')) {
            return [];
        }

        $restApiObj = new RestApiService();
        $uri = $restApiObj->getRestApiUrl() . 'getdriversbydate?date=' . urlencode($dateString);
        $apiResult = $restApiObj->processGetApi($uri);

        return ($apiResult['status']) ? $apiResult['response'] : [];

    }

}
