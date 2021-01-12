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
        // For debug only
        //curl_setopt($ch, CURLOPT_VERBOSE, TRUE);
        //curl_setopt($ch, CURLOPT_HEADER, TRUE);
        //curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        // DO NOT VERIFY
        #curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        #curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);                
        
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
        $response = curl_exec($ch);
        if (!$response) {
            //log_message('error',curl_error ( $ch ));
            error_log(curl_error ( $ch ));
            curl_close($ch);
            return FALSE;
        }
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        try {
            $res = json_decode($response);
            if (is_object($res) && !is_array($res)) {
                $res->http_code = $http_code;
            } else {
                $res = (array)$res;
                $res['http_code'] = $http_code;
            }
            return $res;
        } catch (\Throwable $th) {
            //var_dump($th);
            return $response;
        }
    }


function shellAPI($url, $method="GET", $parameter=[], $data=[], $headers=[], $raw='') {
    if ($parameter) {
        $encoded = array();
        foreach ($parameter as $name => $value) {
            $encoded[] = rawurlencode($name) . '=' . rawurlencode($value);
        }
        // Concatenate the sorted and URL encoded parameters into a string.
        $concatenated = implode('&', $encoded);
        $url = $url . (strpos($url, '?') === FALSE ? '?':'&') . $concatenated ;
    }
    //if (isset($headers['Content-Type']) && $raw)  $headers['Content-Type'] = 'multipart/magic';

    $cmd = 'curl -X '.$method.' "'.$url.'"';
    foreach ($headers as $key => $value) {
        $cmd .= ' -H "'.$key.':'.$value.'"';
    }
    if ($raw) {
        $cmd .= " --data \"@$raw\"";
    } else {
        foreach ($data as $key => $value) {
            $cmd .= ' -d "'.$key.'='.$value.'"';
        }
    }

    print "\n$cmd\n";
    return shell_exec($cmd);
}

function Xml2Json($xml, $ns='') {
    $res = str_replace($ns,'', $xml);
    return simplexml_load_string($res);
}

function downloadAPI($formato, $server, $archivo, $puerto='', $user='', $password='') {
    $response = null;
    $error = null;
    $path = sys_get_temp_dir(). DIRECTORY_SEPARATOR . time(). basename($archivo);
    
    if ($formato == 'http' || $formato == 'https') {
        $url = "$formato://$server";
        if ($puerto) $url .= ":$puerto";
        $url .= "/$archivo";
        
        $curl = curl_init();
        // Define which url you want to access
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        
        // Add authorization header
        if ($user) {
            curl_setopt($curl, CURLOPT_USERPWD, $user . ':' . $password);
            // Allow curl to negotiate auth method (may be required, depending on server)
            curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        }
        // Get response and possible errors
        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);       
        $file = fopen($path, "w+");
        fputs($file, $response);
        fclose($file);
    }

    if ($formato == 'sftp') {
        $current = getcwd();
        $folder = DIR_MARKET.'Shared'.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR;
        chdir($folder);
        if (ENVIRONMENT=='production') {
            $cmd = "/usr/bin/python secure_ftp.py --path=$path --server=$server --user=$user --password='$password' --file='$archivo'";
        } else {
            $cmd = "python secure_ftp.py --path=$path --server=$server --user=$user --password='$password' --file='$archivo'";
        }

        $data = '';
        log_message('error', $cmd);
        try {
            exec($cmd, $data, $res);
            sleep(2);
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
        }
    }

    if ($formato == 'ftp') {
        $conn = null;
        if ($puerto) {
            $conn = ftp_connect($server, (int)$puerto) or die("No se pudo conectar a $server"); 
        } else {
            $conn = ftp_connect($server) or die("No se pudo conectar a $server");
        }
        $login_result = ftp_login($conn, $user, $password);
        $res = ftp_get($conn, $path, $archivo, FTP_TEXT);
        ftp_close($conn);
    }

    return $path;
}
