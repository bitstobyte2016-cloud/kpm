<?php

class ShipAPI extends CI_model{
    
    private static $base_url = "https://apiv2.shiprocket.in";
	
    //to get token    
    public function getAuthentication()
    {
        $email ="support@kpopmerch.in";
        $password = "Bhakti@1993";

        if (empty($email) || empty($password)) {
            throw new \RuntimeException(
                'Shiprocket credentials are not configured.'
            );
        }

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => self::$base_url . '/v1/external/auth/login',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 25,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode([
                'email' => $email,
                'password' => $password
            ]),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json'
            ]
        ]);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        if ($result === false) {
            throw new \RuntimeException(
                'Shiprocket authentication request failed: ' . $error
            );
        }

        $response = json_decode($result);

        if ($httpCode < 200 || $httpCode >= 300 || empty($response->token)) {
            throw new \RuntimeException(
                'Shiprocket authentication failed.'
            );
        }

        return $response;
    }

    //to calculate shipping cost
    public function getServiceAbility($code, $token, $wt, $total = null)
    {
        $params = [
            'pickup_postcode' => '313003',
            'delivery_postcode' => $code,
            'weight' => $wt,
            'cod' => 0
        ];

        if ($total !== null) {
            $params['declared_value'] = $total;
        }

        $url = self::$base_url .
            '/v1/external/courier/serviceability/?' .
            http_build_query($params);

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 25,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json'
            ]
        ]);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        if ($result === false) {
            throw new \RuntimeException(
                'Shiprocket rate request failed: ' . $error
            );
        }

        $response = json_decode($result);

        if ($httpCode < 200 || $httpCode >= 300) {
            throw new \RuntimeException(
                'Shiprocket could not return shipping rates.'
            );
        }

        return $response;
    }
        
        
        
	
	public function getCountries() {
		$response = file_get_contents(self::$base_url."/v1/external/countries");
        return json_decode($response);
	}
	
	public function getZone($id) {
		$response = file_get_contents(self::$base_url."/v1/external/countries/show/".$id);
        return json_decode($response);
	}
	
	
	
	public function getOrder($token){
		$curl = curl_init();
		$url = self::$base_url."/v1/external/orders/?filter_by=status&filter=7&per_page=500";
		// OPTIONS:
	   curl_setopt($curl, CURLOPT_URL, $url);
	   curl_setopt($curl, CURLOPT_HTTPHEADER, array(
		  'Authorization: Bearer '.$token,
		  'Content-Type: application/json',
	   ));
	   curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	   curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

	   // EXECUTE:
	   $result = curl_exec($curl);
	   curl_close($curl);
	   return json_decode($result);
	}
	
	public function getOrderStatus($token,$awb){
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => self::$base_url."/v1/external/courier/track/awb/".$awb,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'GET',
		  CURLOPT_HTTPHEADER => array(
			'Content-Type: application/json',
			'Authorization: Bearer '.$token
		  ),
		));

		$url = self::$base_url."/v1/external/courier/track/awb/".$awb;
		// OPTIONS:
	    curl_setopt($curl, CURLOPT_URL, $url);
	    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
		   'Authorization: Bearer '.$token,
		   'Content-Type: application/json',
	    ));
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

	    // EXECUTE:
	    $result = curl_exec($curl);
	    curl_close($curl);
		return json_decode($result);
	}
	
	public function getOrderDetails($token,$awb){
		$curl = curl_init();
		$url = self::$base_url."/v1/external/orders/?search=".$awb;
		// OPTIONS:
	    curl_setopt($curl, CURLOPT_URL, $url);
	    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
		   'Authorization: Bearer '.$token,
		   'Content-Type: application/json',
	    ));
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

	    // EXECUTE:
	    $result = curl_exec($curl);
	    curl_close($curl);
		return json_decode($result);
	}
	
	public function schedulePickup($token,$awb){
		$fields = array(
			"shipment_id" => array($awb)
		);
        // open connection 
        $ch = curl_init();    
        // Set the url, number of POST vars, POST data  set the url
        curl_setopt($ch, CURLOPT_URL, self::$base_url."/v1/external/courier/generate/pickup"); 
        curl_setopt($ch, CURLOPT_POST, true);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		   'Authorization: Bearer '.$token,
		   'Content-Type: application/json',
	    ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        // Execute post   
        $result = curl_exec($ch); 
        // Close connection      
        curl_close($ch);
        return json_decode($result);
	}
	
	public function generateManifest($token,$awb){
		$fields = array(
			"shipment_id" => array($awb)
		);
		$ch = curl_init();    
        // Set the url, number of POST vars, POST data  set the url
        curl_setopt($ch, CURLOPT_URL, self::$base_url."/v1/external/manifests/generate"); 
        curl_setopt($ch, CURLOPT_POST, true);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		   'Authorization: Bearer '.$token,
		   'Content-Type: application/json',
	    ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        // Execute post   
        $result = curl_exec($ch); 
        // Close connection      
        curl_close($ch);
        return json_decode($result);
	}
	
	public function createOrder($token,$order){
		$ch = curl_init();    
        // Set the url, number of POST vars, POST data  set the url
        curl_setopt($ch, CURLOPT_URL, self::$base_url."/v1/external/orders/create/adhoc"); 
        curl_setopt($ch, CURLOPT_POST, true);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		   'Authorization: Bearer '.$token,
		   'Content-Type: application/json',
	    ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($order));
        // Execute post   
        $result = curl_exec($ch); 
        // Close connection      
        curl_close($ch);
        return json_decode($result);
		
	}

}

?>
