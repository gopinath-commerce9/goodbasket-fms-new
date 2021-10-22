<?php


namespace Modules\Base\Entities;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

class RestApiService
{

    private $apiConfigKey = 'goodbasket.api';
    private $apiEnvironment = 'live';
    private $apiBaseUrl = '';
    private $apiVersion = 1;
    private $apiRole = '';
    private $apiUsername = '';
    private $apiPassword = '';
    private $apiTimeoutSeconds = 30;
    private $apiRetryLoop = 3;
    private $apiRetryLoopInterval = 30;
    private $apiUserTokenSessionKey = 'api_user_token';

    public function __construct() {
        $apiConfigs = config($this->apiConfigKey);
        $this->apiEnvironment = $apiConfigs['env'];
        $this->apiBaseUrl = $apiConfigs[$this->apiEnvironment]['url'];
        $this->apiVersion = $apiConfigs[$this->apiEnvironment]['version'];
        $this->apiRole = $apiConfigs[$this->apiEnvironment]['role'];
        $this->apiUsername = $apiConfigs[$this->apiEnvironment][$this->apiRole]['username'];
        $this->apiPassword = $apiConfigs[$this->apiEnvironment][$this->apiRole]['password'];
        $this->apiTimeoutSeconds = $apiConfigs[$this->apiEnvironment]['timeoutSeconds'];
        $this->apiRetryLoop = $apiConfigs[$this->apiEnvironment]['retryLoop'];
        $this->apiRetryLoopInterval = $apiConfigs[$this->apiEnvironment]['retryLoopInterval'];
    }

    /**
     * Get the Base URL of the API.
     *
     * @return string
     */
    public function getBaseUrl() {
        return $this->apiBaseUrl;
    }

    /**
     * Get the full API URL.
     *
     * @return string
     */
    public function getRestApiUrl() {
        return $this->apiBaseUrl . 'rest/V' . $this->apiVersion . '/';
    }

    /**
     * Get the Authentication Bearer Token for the API Calls.
     *
     * @return mixed|string|null
     */
    public function getApiUserBearerToken() {

        if(session()->has($this->apiUserTokenSessionKey)) {
            $cleanToken = trim(session()->get($this->apiUserTokenSessionKey));
            if (!is_null($cleanToken) && ($cleanToken != '')) {
                return $cleanToken;
            }
        }

        $authCredentials = [
            'username' => $this->apiUsername,
            'password' => $this->apiPassword
        ];
        $authUrl = $this->getRestApiUrl() . 'integration/admin/token';

        try {

            $authResponse = Http::acceptJson()
                ->retry($this->apiRetryLoop, $this->apiRetryLoopInterval)
                ->post($authUrl, $authCredentials);

            if ($authResponse->failed()) {
                return null;
            }

            $responseData = $authResponse->json();
            if (is_array($responseData) && array_key_exists('message', $responseData)) {
                return null;
            }

            session()->put($this->apiUserTokenSessionKey, $responseData);
            return $responseData;

        } catch(\Exception $e) {
            return null;
        }

    }

    /**
     * Execute the GET method RESTFul API Call.
     *
     * @param string $url
     * @param array $params
     * @param array $headers
     * @param bool $authenticate
     *
     * @return array
     */
    public function processGetApi($url = '', $params = [], $headers = [], $authenticate = true) {

        if (is_null($url) || (trim($url) == '')) {
            return [
                'status' => false,
                'message' => 'Invalid API URL!',
                'response' => null
            ];
        }

        if (!is_null($params) && !is_array($params)) {
            return [
                'status' => false,
                'message' => 'Invalid Params input!',
                'response' => null
            ];
        }

        if (!is_null($headers) && !is_array($headers)) {
            return [
                'status' => false,
                'message' => 'Invalid Headers input!',
                'response' => null
            ];
        }

        $apiResponse = null;

        try {

            if ($authenticate) {

                $authToken = $this->getApiUserBearerToken();
                if(!$authToken) {
                    return [
                        'status' => false,
                        'message' => 'The API could not authenticate!',
                        'response' => null
                    ];
                }

                $apiResponse = Http::acceptJson()
                    ->withToken($authToken)
                    ->withHeaders($headers)
                    ->retry($this->apiRetryLoop, $this->apiRetryLoopInterval)
                    ->get($url, $params);

            } else {
                $apiResponse = Http::acceptJson()
                    ->withHeaders($headers)
                    ->retry($this->apiRetryLoop, $this->apiRetryLoopInterval)
                    ->get($url, $params);
            }

            if (!$apiResponse || $apiResponse->failed()) {
                return [
                    'status' => false,
                    'message' => 'The API call failed!',
                    'response' => null
                ];
            }

            return [
                'status' => true,
                'message' => '',
                'response' => $apiResponse->json()
            ];

        } catch(\Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
                'response' => null
            ];
        }

    }

    /**
     * Execute the POST method RESTFul API Call.
     *
     * @param string $url
     * @param array $params
     * @param array $headers
     * @param bool $authenticate
     *
     * @return array
     */
    public function processPostApi($url = '', $params = [], $headers = [], $authenticate = true) {

        if (is_null($url) || (trim($url) == '')) {
            return [
                'status' => false,
                'message' => 'Invalid API URL!',
                'response' => null
            ];
        }

        if (!is_null($params) && !is_array($params)) {
            return [
                'status' => false,
                'message' => 'Invalid Params input!',
                'response' => null
            ];
        }

        if (!is_null($headers) && !is_array($headers)) {
            return [
                'status' => false,
                'message' => 'Invalid Headers input!',
                'response' => null
            ];
        }

        $apiResponse = null;

        try {

            if ($authenticate) {

                $authToken = $this->getApiUserBearerToken();
                if(!$authToken) {
                    return [
                        'status' => false,
                        'message' => 'The API could not authenticate!',
                        'response' => null
                    ];
                }

                $apiResponse = Http::acceptJson()
                    ->withToken($authToken)
                    ->withHeaders($headers)
                    ->retry($this->apiRetryLoop, $this->apiRetryLoopInterval)
                    ->post($url, $params);

            } else {
                $apiResponse = Http::acceptJson()
                    ->withHeaders($headers)
                    ->retry($this->apiRetryLoop, $this->apiRetryLoopInterval)
                    ->post($url, $params);
            }

            if (!$apiResponse || $apiResponse->failed()) {
                return [
                    'status' => false,
                    'message' => 'The API call failed!',
                    'response' => null
                ];
            }

            return [
                'status' => true,
                'message' => '',
                'response' => $apiResponse->json()
            ];

        } catch(\Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
                'response' => null
            ];
        }

    }

    /**
     * Execute the PUT method RESTFul API Call.
     *
     * @param string $url
     * @param array $params
     * @param array $headers
     * @param bool $authenticate
     *
     * @return array
     */
    public function processPutApi($url = '', $params = [], $headers = [], $authenticate = true) {

        if (is_null($url) || (trim($url) == '')) {
            return [
                'status' => false,
                'message' => 'Invalid API URL!',
                'response' => null
            ];
        }

        if (!is_null($params) && !is_array($params)) {
            return [
                'status' => false,
                'message' => 'Invalid Params input!',
                'response' => null
            ];
        }

        if (!is_null($headers) && !is_array($headers)) {
            return [
                'status' => false,
                'message' => 'Invalid Headers input!',
                'response' => null
            ];
        }

        $apiResponse = null;

        try {

            if ($authenticate) {

                $authToken = $this->getApiUserBearerToken();
                if(!$authToken) {
                    return [
                        'status' => false,
                        'message' => 'The API could not authenticate!',
                        'response' => null
                    ];
                }

                $apiResponse = Http::acceptJson()
                    ->withToken($authToken)
                    ->withHeaders($headers)
                    ->retry($this->apiRetryLoop, $this->apiRetryLoopInterval)
                    ->put($url, $params);

            } else {
                $apiResponse = Http::acceptJson()
                    ->withHeaders($headers)
                    ->retry($this->apiRetryLoop, $this->apiRetryLoopInterval)
                    ->put($url, $params);
            }

            if (!$apiResponse || $apiResponse->failed()) {
                return [
                    'status' => false,
                    'message' => 'The API call failed!',
                    'response' => null
                ];
            }

            return [
                'status' => true,
                'message' => '',
                'response' => $apiResponse->json()
            ];

        } catch(\Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
                'response' => null
            ];
        }

    }

    /**
     * Execute the DELETE method RESTFul API Call.
     *
     * @param string $url
     * @param array $params
     * @param array $headers
     * @param bool $authenticate
     *
     * @return array
     */
    public function processDeleteApi($url = '', $params = [], $headers = [], $authenticate = true) {

        if (is_null($url) || (trim($url) == '')) {
            return [
                'status' => false,
                'message' => 'Invalid API URL!',
                'response' => null
            ];
        }

        if (!is_null($params) && !is_array($params)) {
            return [
                'status' => false,
                'message' => 'Invalid Params input!',
                'response' => null
            ];
        }

        if (!is_null($headers) && !is_array($headers)) {
            return [
                'status' => false,
                'message' => 'Invalid Headers input!',
                'response' => null
            ];
        }

        $apiResponse = null;

        try {

            if ($authenticate) {

                $authToken = $this->getApiUserBearerToken();
                if(!$authToken) {
                    return [
                        'status' => false,
                        'message' => 'The API could not authenticate!',
                        'response' => null
                    ];
                }

                $apiResponse = Http::acceptJson()
                    ->withToken($authToken)
                    ->withHeaders($headers)
                    ->retry($this->apiRetryLoop, $this->apiRetryLoopInterval)
                    ->put($url, $params);

            } else {
                $apiResponse = Http::acceptJson()
                    ->withHeaders($headers)
                    ->retry($this->apiRetryLoop, $this->apiRetryLoopInterval)
                    ->put($url, $params);
            }

            if (!$apiResponse || $apiResponse->failed()) {
                return [
                    'status' => false,
                    'message' => 'The API call failed!',
                    'response' => null
                ];
            }

            return [
                'status' => true,
                'message' => '',
                'response' => $apiResponse->json()
            ];

        } catch(\Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
                'response' => null
            ];
        }

    }

}
