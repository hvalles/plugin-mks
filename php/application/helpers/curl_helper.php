<?php
/*

Rutina para hacer llamadas a la API de MarketSync

*/

function callAPI($url, $method="GET", $public, $private, $parameter=[], $data=[]) {
        $PRIVATE_KEY = $private;
        $TOKEN = $public;
       
        # Set initial parameters
        $parameters = [];
        $parameters['token'] = $TOKEN;
        $parameters['timestamp'] = substr(date(DATE_ATOM),0,19); # YYYY-MM-DDTHH:mm:ss
        $parameters['version'] = '1.0';
        
        # You may add others parameters here
        foreach ($parameter as $key => $value) {
            $parameters[$key] = $value;
        }
        
        ksort($parameters);
        // URL encode the parameters.
        $encoded = array();
        foreach ($parameters as $name => $value) {
            $encoded[] = rawurlencode($name) . '=' . rawurlencode($value);
        }
        // Concatenate the sorted and URL encoded parameters into a string.
        $concatenated = implode('&', $encoded);
        
        $sign = rawurlencode(hash_hmac('sha256', $concatenated, $PRIVATE_KEY, false));
        $url = $url . '?' . $concatenated . '&signature=' . $sign;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        if ($data)
            curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($data));
        error_log($url);
        $response = curl_exec($ch);
        
        if (!$response) {
            error_log(str(curl_error ( $ch )));
            curl_close($ch);
            return FALSE;
        }
        curl_close($ch);
        return json_decode($response);
    }
?>