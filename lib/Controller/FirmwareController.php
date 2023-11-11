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

     // Get status Api Endpoint
     public function getStatus() {
        $responseData = $this->makeApiCall('https://34.145.217.103/api/core/firmware/status');
        if ($responseData['status'] === 'ok') {
            return new DataResponse([
                'upgradeAvailable' => true,
                'downloadSize' => $responseData['download_size'],
                'numberOfPackages' => $responseData['updates'],
                'rebootRequired' => $responseData['upgrade_needs_reboot'] === '0' ? false : true
            ]);
        } else if (isset($responseData['status_msg'])) {
            return new DataResponse(['errorMessage' => $responseData['status_msg']]);   
        }
        return new Dataresponse(['error' => 'Error making OPNsense API call'], 500);
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

     // Post Alias API endpoint
     //@author: Jharby
    public function addAlias() {
        // Get the JSON POST data
          // decode to associative array
    
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
                    
                } elseif ($responseData['result'] === 'success') {
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
     * @param array $categoryData The data for the new category from the client side.
     * @return DataResponse
     * @author Diego Munoz
     */
     // Post Categories API endpoint
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
                    
                } elseif ($responseData['result'] === 'success') {
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
                $errorDetails = 'Failed to retrieve aliases';
            }
            // This handles a case where 'rows' does not appear and gives error message to the user 
            return new DataResponse([
                'error' => 'Failed to retrieve aliases',
                'details' => $responseData
            ], 500); //HTTP status code 500 for internal server error
        }
        
    }
    public function deleteCategories(string $uuid) {
        $url = 'https://34.145.217.103/api/firewall/category/delItem/'.urlencode($uuid);
        try {
            $responseData = $this->makePostApiCall($url);
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
                    
                } elseif ($responseData['result'] === 'success') {
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