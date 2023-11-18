<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: Jharby <jsara030@fiu.edu>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\NextOPN\Controller;

use OCA\NextOPN\AppInfo\Application;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IRequest;
use OCP\Util;

class FirmwareController extends Controller {
    private $apiKey = 'vYHHpQQsII92D60QBehJWzoBkO+kZW5oiyqsTLXOhu+z2BNy8MZ3Ax4nseJbCDF0pW5tQ52bbR+AHPel';
    private $apiSecret = 'wXHy7GQsLwE/Y2fm1qD1CcQ8e3L7leEsxmEDG5fbOez+pu3s9TKwOxWG1Xz8UeAETMfzaZxyd3WnDydD';
    public function __construct($AppName, IRequest $request) {
        parent::__construct($AppName, $request);
    }

     /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */

     public function getStatus() {
        $responseData = $this->makeApiCall('https://34.145.217.103/api/core/firmware/status');
        if ($responseData['status'] === '200 OK') {
            return new DataResponse([
                'downloadSize' => $responseData['download_size'],
                'rebootRequired' => $responseData['needs_reboot'] === '1',
                'statusMessage' => $responseData['status_msg'],
            ]);
        } else if (isset($responseData['status_msg'])) {
            return new DataResponse(['statusMessage' => $responseData['status_msg']]);   
        }
        return new Dataresponse(['error' => 'Error making OPNsense API call'], 500);
     }

     /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */

     public function getInfo() {
        $responseData = $this->makeApiCall('https://34.145.217.103/api/core/firmware/info');
            if (isset($responseData['product_id']) && isset($responseData['product_version'])) {
                return new DataResponse([
                    'productID' => $responseData['product_id'],
                    'productVersion' => $responseData['product_version'],
                ]);
        } else {
            return new DataResponse([
                'error' => 'Failed to get information'
            ],500);
     }
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */

     public function getAlias() {
        $responseData = $this->makeApiCall('https://34.145.217.103/api/firewall/alias/searchItem');
            // if status message is ok and there is a rows set
            if (isset($responseData['rows'])) {
                return new DataResponse($responseData['rows']);
            } else {
                // check to see if the row is not set and print the error message that follwos
                if (isset($responseData['status']) && $responseData['status'] !== 'ok') {
                    $errorDetails = isset($responseData['status_msg']);
                } else {
                    $errorDetails = 'Failed to retrieve aliases';
                }
                // This handles a case where 'rows' does not appear and gives error message to the user 
                return new DataResponse([
                    'error' => 'Failed to retrieve aliases',
                    'details' => $responseData
                ], 500); //HTTP status code 500 for internal server error
            }
     }

     
    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @param array $aliasData The data for the new alias from the client side.
     * @return DataResponse
     */

     // Post Group API Endpoint
     public function delAlias($uuid) {
        $url = 'https://34.145.217.103/api/firewall/alias/delItem/'. $uuid;
        try {
            $responseData = $this->makePostApiCall($url,[]);
            // Check the 'result' field directly for 'success' or 'failed' status
            if(isset($responseData['result'])) {
                if ($responseData['result'] === 'deleted') {

                    $sucessMessage = 'Deleted successfully';

                    return new DataResponse([
                        'success' => true,
                        'message' => $sucessMessage
                    ]);
                    
            } else {
                // If no 'result' field is present, handle as error
                return new DataResponse([
                    'success' => false,
                    'message' => isset($responseData['result']) ? $responseData['result'] : 'Nothing was deleted.'
                ]);
            }
        }
        } catch (\Exception $e) {
            // Handle Exceptions thrown during the API call
            return new DataResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
     }
     /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @param array $aliasData The data for the new alias from the client side.
     * @return DataResponse
     */

     // Post Alias API endpoint
     public function addAlias() {
        // Get the JSON POST data
        $json = file_get_contents('php://input');
        $aliasData = json_decode($json, true); // decode to associative array
    
        // Now use $aliasData to construct your payload
        $payload = [
            'alias' => [
                'enabled' => $aliasData['alias']['enabled'], // multi-dimensional array to retrieve the field from the front end
                'name' => $aliasData['alias']['name'],
                'type' => $aliasData['alias']['type'],
                'proto' => $aliasData['alias']['proto'],
                'categories' => $aliasData['alias']['categories'],
                'updatefreq' => $aliasData['alias']['updatefreq'],
                'content' => $aliasData['alias']['content'],
            ],
            'authgroup_content' => $aliasData['authgroup_content'],
            'network_content' => $aliasData['network_content'],
        ];

        $url = 'https://34.145.217.103/api/firewall/alias/addItem/';

        try {
            $responseData = $this->makePostApiCall($url, $payload);
            
            // Check the 'result' field directly for 'success' or 'failed' status
            if (isset($responseData['result'])) {
                if ($responseData['result'] === 'failed') {

                    $errorMessage = isset($responseData['validations']) ? 
                    "Alias not added. " . implode(' ', $responseData['validations']) :
                    "Alias not added due to an unknown error.";


                    // The operation failed
                    return new DataResponse([
                        'success' => false,
                        'message' => $errorMessage
                    ]);
                    
                } elseif ($responseData['result'] === 'saved') {
                    // The operation was successful
                    return new DataResponse([
                        'success' => true,
                        'message' => 'Alias added successfully.'
                    ]);
                } else {
                    // If result is neither 'failed' nor 'success', handle as error
                    return new DataResponse([
                        'success' => false,
                        'message' => 'Unexpected result status.'
                    ]);
                }
            } else {
                // If no 'result' field is present, handle as error
                return new DataResponse([
                    'success' => false,
                    'message' => isset($responseData['status_msg']) ? $responseData['status_msg'] : 'Unknown error occurred.'
                ]);
            }
        } catch (\Exception $e) {
            // Handle Exceptions thrown during the API call
            return new DataResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
     }
     /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function getlistCategories() {
        $responseData = $this->makeApiCall('https://34.145.217.103/api/firewall/alias/listCategories');
            // if status message is ok and there is a rows set
            if (isset($responseData['rows'])) {
                return new DataResponse($responseData['rows']);
            } else {
                // check to see if the row is not set and print the error message that follwos
                if (isset($responseData['status']) && $responseData['status'] !== 'ok') {
                    $errorDetails = isset($responseData['status_msg']);
                } else {
                    $errorDetails = 'Failed to retrieve category-aliases';
                }
                // This handles a case where 'rows' does not appear and gives error message to the user 
                return new DataResponse([
                    'error' => 'Failed to retrieve category-aliases',
                    'details' => $responseData
                ], 500); //HTTP status code 500 for internal server error
            }
    }
    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function getAliasItem($uuid = null) {
        try {
            $url = 'https://34.145.217.103/api/firewall/alias/getItem/';
            $responseData = $this->makeApiCall($url);
            
            if (is_array($responseData) && isset($responseData['alias'])) {
                return new DataResponse($responseData['alias']);
            } else {
                $errorDetails = 'Invalid or unexpected API response: ' . json_encode($responseData);
    
                return new DataResponse([
                    'error' => 'Failed to retrieve alias-item',
                    'details' => $errorDetails
                ], 500);
            }
        } catch (Exception $e) {
            // Handle exceptions, log or return an error response
            return new DataResponse([
                'error' => 'Exception occurred',
                'details' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function toogleItem($uuid) {
        $url = 'https://34.145.217.103/api/firewall/alias/toggleItem/'. $uuid;
        try {
            $responseData = $this->makePostApiCall($url,[]);
            // Check the 'result' field directly for 'success' or 'failed' status
            if(isset($responseData['result'])) {
                if ($responseData['result'] === 'Disabled'|| 'Enabled') {

                    $sucessMessage = 'Toogled successfully';

                    return new DataResponse([
                        'success' => true,
                        'message' => $sucessMessage
                    ]);
                    
            } else {
                // If no 'result' field is present, handle as error
                return new DataResponse([
                    'success' => false,
                    'message' => isset($responseData['result']) ? $responseData['result'] : 'Nothing was deleted.'
                ]);
            }
        }
        } catch (\Exception $e) {
            // Handle Exceptions thrown during the API call
            return new DataResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
     public function addGroup() {
        $json = file_get_contents('php://input');
        $groupData = json_decode($json, true);

        $payload = [
            'group' => [
                'ifname' => $groupData['group']['ifname'],
                'members' => $groupData['group']['members'],
                'nogroup' => $groupData['group']['nogroup'],
                'sequence' => $groupData['group']['sequence'],
                'descr' => $groupData['group']['descr'],
            ]
            ];
            $url = 'https://34.145.217.103/api/firewall/group/addItem/';

            try {
                $responseData = $this->makePostApiCall($url, $payload);

                if(isset($responseData['result'])) {
                    if($responseData['result'] === 'failed') {

                        $errorMessage = isset($responseData['validations']) ?
                        "Group not added. " . implode(' ', $responseData['validations']) :
                        "Alias not added due to unknown error.";

                        return new DataResponse([
                            'success' => false,
                            'message' => $errorMessage
                        ]);
                    } elseif ($responseData['result'] === 'saved') {

                        return new DataResponse([
                            'success' => true,
                            'message' => 'Group added successfully.'
                        ]);
                    } else {
                        return new DataResponse([
                            'success' => 'false',
                            'message' => 'Unexpected error result, no success or failed recieved from API endpoint'
                        ]);
                    }
                }
            } catch (\Exception $e) {
                return new DataResponse([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }
     }
     /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
     public function delGroup($uid){
        $json = file_get_contents('php://input');
        $delGroupData = json_decode($json, true);

        $url = 'https://34.145.217.103/api/firewall/group/delItem/' . $uid; // the period appends the two strings together

        $data = array(); // Since there is not data that needs to be send we can 

        try {
            $response = $this->makePostApiCall($url, $data);
            return $response;
        } catch (\Exception $e) {
            throw $e;
        }
     }

         /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @param array $categoryData The data for the new category from the client side.
     * @return DataResponse
     */
    public function addCategories() {

        $json = file_get_contents('php://input');
        $categoryData = json_decode($json, true);

        $payload = [
            'category' => [
                'auto' => $categoryData['category']['auto'],
                //user needs to input a Hex value ex afbfcf
                'color' => $categoryData['category']['color'],
                'name' => $categoryData['category']['name'],
            ],
        ];
        $url = 'https://34.145.217.103/api/firewall/category/addItem/';

        try {
            $responseData = $this->makePostApiCall($url, $payload);
            
            // Check the 'result' field directly for 'success' or 'failed' status
            if(isset($responseData['result'])) {
                if ($responseData['result'] === 'failed') {

                    $errorMessage = isset($responseData['validations']) ? 
                    "Category not added. " . implode(' ', $responseData['validations']) :
                    "Category not added due to an unknown error.";


                    // The operation failed
                    return new DataResponse([
                        'success' => false,
                        'message' => $errorMessage
                    ]);
                    
                } elseif ($responseData['result'] === 'saved') {
                    // The operation was successful
                    return new DataResponse([
                        'success' => true,
                        'message' => 'Category added successfully.'
                    ]);
                } else {
                    // If result is neither 'failed' nor 'success', handle as error
                    return new DataResponse([
                        'success' => false,
                        'message' => 'Unexpected result status.'
                    ]);
                }
            } else {
                // If no 'result' field is present, handle as error
                return new DataResponse([
                    'success' => false,
                    'message' => isset($responseData['status_msg']) ? $responseData['status_msg'] : 'Unknown error occurred.'
                ]);
            }
        } catch (\Exception $e) {
            // Handle Exceptions thrown during the API call
            return new DataResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function getCategories() {
        $responseData = $this->makeApiCall('https://34.145.217.103/api/firewall/category/searchItem');

        if(isset($responseData['rows'])) {
            return new DataResponse($responseData['rows']);
        } else {
            if (isset($responseData['status']) && $responseData['status'] !== 'ok') {
                $errorDetails = isset($responseData['status_msg']);
            } else {
                $errorDetails = 'Failed to retrieve category';
            }
            // This handles a case where 'rows' does not appear and gives error message to the user 
            return new DataResponse([
                'error' => 'Failed to retrieve category',
                'details' => $responseData
            ], 500); //HTTP status code 500 for internal server error
        }
        
    }


    private function makeApiCall($url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        // Disable SSL Verification (for debugging purposes)
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    
        // Set authentication headers for Basic Authentication
        $headers = [
            'Authorization: Basic ' . base64_encode($this->apiKey . ':' . $this->apiSecret)
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
        $response = curl_exec($ch);
        
        if(curl_errno($ch)) {
            throw new \Exception('Request error: ' . curl_error($ch));
        }
        
        curl_close($ch);
        
        return json_decode($response, true);
    }

    private function makePostApiCall($url, $data) {
        $ch = curl_init($url);

        $jsonData = json_encode($data);

            // Set the cURL options for a POST request
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Set the content type to application/json for JSON body
        $headers = [
            'Authorization: Basic ' . base64_encode($this->apiKey . ':' . $this->apiSecret),
            'Content-Type: application/json; charset=UTF-8',
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Disable SSL Verification (for debugging purposes)
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        $response = curl_exec($ch);

        if(curl_errno($ch)) {
            throw new \Exception('Request error: ' . curl_error($ch));
        }

        curl_close($ch);

        return json_decode($response, true);
        }
}
