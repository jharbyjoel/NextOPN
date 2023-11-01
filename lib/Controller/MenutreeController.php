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

class MenutreeController extends Controller {
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
        $responseData = $this->makeApiCall('https://35.199.16.187/api/core/menu/search');
        if ($responseData['status'] === 'ok') {
            return new DataResponse([
                    'menuID' => array_map(function($item) {
                        return $item['Id'];
                    }, $responseData),
                    'menuOrder' => array_map(function($item) {
                        return $item['Order'];
                    }, $responseData),
                    'ChildrenId' => array_map(function($item) {
                        return array_map(function($child) {
                            return $child['Id'];
                        }, $item['Children']);
                    }, $responseData)
            ]);
        } else if (isset($responseData['status_msg'])) {
            return new DataResponse(['errorMessage' => $responseData['status_msg']]);   
        }
        return new Dataresponse(['error' => 'Error making OPNsense API call'], 500);
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
    
    

}