<?php
/*

Rutina para hacer llamadas a la API de MarketSync

*/

function callAPI($url, $method="GET", $public=FALSE, $private=FALSE, $parameter=[], $data=[], $headers=[], $json=TRUE) {
        $PRIVATE_KEY = $private;
        $TOKEN = $public;

        $parameters = [];
        if ($public!==FALSE && $private!==FALSE) {
            # Set initial parameters
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
        } else {
            if ($parameter) {
                // URL encode the parameters.
                $encoded = array();
                foreach ($parameter as $name => $value) {
                    $encoded[] = rawurlencode($name) . '=' . rawurlencode($value);
                }
                // Concatenate the sorted and URL encoded parameters into a string.
                $concatenated = implode('&', $encoded);
                if (strpos($url, '?')===FALSE) {
                    $url .= '?' . $concatenated;
                } else {
                    $url .= '&' . $concatenated;
                }
            }
        }
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        if ($data) {
            if ($json) {
                curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($data));
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
            }
        }

        if ($headers) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);    
        } else {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));    
        }
        //log_message('error',$url);
        $response = curl_exec($ch);
        //var_dump($ch);
        //var_dump($response);
        
        if (!$response) {
            //echo curl_error ( $ch );
            error_log(curl_error ( $ch ));
            curl_close($ch);
            return FALSE;
        }
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        //var_dump($response);
        try {
            $res = json_decode($response);
            if (is_object($res)) {
                $res->http_code = $http_code;
            } else {
                $res['http_code'] = $http_code;
            }
            return $res;
        } catch (\Throwable $th) {
            return $response;
        }
        
    }
?>