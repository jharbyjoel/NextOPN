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

     public function getStatus(string $id,string $password) {
        $responseData = $this->logon('https://35.199.16.187/api/captiveportal/session/connect',$id,$password);
        if ($responseData['status'] === 'ok') {
            return new DataResponse(['success' => 'Login Sucessfull', 200])
        } else if (isset($responseData['status_msg'])) {
            return new DataResponse(['errorMessage' => $responseData['status_msg']]);   
        }
        return new Dataresponse(['error' => 'Error making OPNsense API call'], 500);
     }
     private function logon($url,string $userId,string $userPassword) {
        $ch = curl_init($url); 
        $params = [
            'zoneid' => 0,
            'userId' => $userId,
            'userPassword' => $userPassword,
        ];
        // Disable SSL Verification (for debugging purposes)
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        // Set authentication headers for Basic Authentication
        $headers = [
            'Authorization: Basic ' . base64_encode($this->apiKey . ':' . $this->apiSecret)
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
        $response = curl_exec($ch);
        
        if(curl_errno($ch)) {
            throw new \Exception('Request error: ' . curl_error($ch));
        }
        
        curl_close($ch);
        
        return json_decode($response, true);
    }
    
    

}