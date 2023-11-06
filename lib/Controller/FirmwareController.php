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
        $responseData = $this->makeApiCall('https://35.199.16.187/api/core/firmware/status');
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
     * @param array $aliasData The data for the new alias from the client side.
     * @return DataResponse
     */

     // Post Alias API endpoint
     public function addAlias($aliasData) {
        // Payload Constructor
        $payload = [
            'alias' => [
                'enabled' => $aliasData['enabled'],
                'name' => $aliasData['name'],
                'type' => $aliasData['type'],
                'proto' => $aliasData['proto'],
                'categories' => $aliasData['categories'],
                'updatefreq' => $aliasData['updatefreq'],
                'content' => $aliasData['content'],
            ],
            'authgroup_content' => $aliasData['authgroup_content'],
            'network_content' => $aliasData['nextwork_content'],
        ];

        $url = 'https://35.199.16.187/api/firewall/alias/addItem/';

        try {

            $responseData = $this->makePostApiCall($url, $payload);

            if(isset($responseData['status']) && $responseData['status'] === 'ok'){

                return new DataResponse([
                    'success' => true,
                    'message' => 'Alias added successfully.',
                    'data' => $responseData
                ]);
            } else {
                // Handle case where API response fails
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
        'Content-Type: application/json',
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