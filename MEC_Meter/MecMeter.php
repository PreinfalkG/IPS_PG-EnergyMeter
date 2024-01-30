<?php

declare(strict_types=1);

trait MECMETER_FUNCTIONS {

    const JSON_API_URL = "http://%%IP-ADDRESS%%/wizard/public/api/measurements";
    const JSON_DEVICEINFO_URL = "http://%%IP-ADDRESS%%/wizard/public/api/hardware";

    public function RequestMeterData() {

        $returnData = "{}";

        $mecMeterApiUrl = str_replace("%%IP-ADDRESS%%", $this->ReadPropertyString("MecMeter_IP"), SELF::JSON_API_URL);
        $mecMeterUser = $this->ReadPropertyString("MecMeter_User");
        $mecMeterPW = $this->ReadPropertyString("MecMeter_PW");

        $start = microtime(true);
        if ($this->logLevel >= LogLevel::COMMUNICATION) {
            $this->AddLog(__FUNCTION__, sprintf("Request JSON Data from '%s' [@%s]", $mecMeterApiUrl, $start));
        }

        $ch = curl_init();
        try {
            curl_setopt($ch, CURLOPT_URL, $mecMeterApiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERPWD, "$mecMeterUser:$mecMeterPW");

            // disabled on 02.11.2023
            //curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
            //curl_setopt($ch, CURLOPT_TIMEOUT_MS, 1200);

            // added on 02.11.2023
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);            // 03.11.2023 changed from '2' to '0'
            curl_setopt($ch, CURLOPT_TIMEOUT, 4);
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

            //curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: close'));

            $result =  curl_exec($ch);
            $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if ($result === false) {

                $returnData = false;

                //throw new Exception(curl_error($ch), curl_errno($ch));
                $errorMsg = sprintf("ERROR: '%s' [%s]", curl_error($ch), curl_errno($ch));
                if ($this->logLevel >= LogLevel::ERROR) {
                    $this->AddLog(__FUNCTION__, $errorMsg);
                }
                $this->Increase_CounterVariable($this->GetIDForIdent("updateCntError"));
                SetValue($this->GetIDForIdent("updateLastError"), $errorMsg);
            } else if ($httpStatusCode != 200) {

                $returnData = false;

                $errorMsg = sprintf("WARN: httpStatusCode '%s' received from '%s'", $httpStatusCode, $mecMeterApiUrl);
                if ($this->logLevel >= LogLevel::ERROR) {
                    $this->AddLog(__FUNCTION__, $errorMsg);
                }
                $this->Increase_CounterVariable($this->GetIDForIdent("updateCntError"));
                SetValue($this->GetIDForIdent("updateLastError"), $errorMsg);
            } else {
                $returnData = json_decode($result, true);
                if ($this->logLevel >= LogLevel::COMMUNICATION) {
                    $this->AddLog(__FUNCTION__, $result);
                }
            }
        } catch (Exception $e) {
            $errCode = $e->getCode();
            switch ($errCode) {
                case 28:
                    // Increase Error Counter ..
                    break;
            }
            $errorMsg = sprintf("Exception '%s . %s' requesting '%s'", $errCode, $e->getMessage(), $mecMeterApiUrl);
            if ($this->logLevel >= LogLevel::ERROR) {
                $this->AddLog(__FUNCTION__, $errorMsg);
            }
            $this->Increase_CounterVariable($this->GetIDForIdent("updateCntError"));
            SetValue($this->GetIDForIdent("updateLastError"), $errorMsg);
        } finally {
            curl_close($ch);
        }

        SetValueInteger($this->GetIDForIdent("updateLastApiDuration"), ceil((microtime(true) - $start) * 1000));
        return $returnData;
    }


    public function RequestDeviceInfo() {

        $mecMeterDeviceInfoUrl = str_replace("%%IP-ADDRESS%%", $this->ReadPropertyString("MecMeter_IP"), SELF::JSON_DEVICEINFO_URL);
        $response = @file_get_contents($mecMeterDeviceInfoUrl);
        if($response === false) {
            $error = error_get_last();
            if ($this->logLevel >= LogLevel::ERROR) {
                $this->AddLog(__FUNCTION__, sprintf("ERROR: %s", print_r($error, true)), 0, true);
            }
        } 
        return $response;
    }

}
