<?

declare(strict_types=1);

abstract class LogLevel {
    const ALL = 9;
    const TEST = 8;
    const TRACE = 7;
    const COMMUNICATION = 6;
    const DEBUG = 5;
    const INFO = 4;
    const WARN = 3;
    const ERROR = 2;
    const FATAL = 1;
}

abstract class VARIABLE {
    const TYPE_BOOLEAN = 0;
    const TYPE_INTEGER = 1;
    const TYPE_FLOAT = 2;
    const TYPE_STRING = 3;
}


trait ENERGYMETER_COMMON {

    protected function profilingStart($profName) {
        if($this->logLevel >= LogLevel::TEST) { $this->AddLog(__FUNCTION__, $profName . "..."); }
        $profAttrCnt = "prof_" . $profName;
        $profAttrDuration = "prof_" . $profName . "_Duration";
        $this->WriteAttributeInteger($profAttrCnt, $this->ReadAttributeInteger($profAttrCnt)+1);
        $this->WriteAttributeFloat($profAttrDuration, microtime(true));
        if($this->logLevel >= LogLevel::TRACE) { $this->AddLog(__FUNCTION__, sprintf("%s [Cnt: %s]", $profName, $this->ReadAttributeInteger($profAttrCnt))); }
    }

    protected function profilingEnd($profName, $doUpdateCnt=true) {     
        $profAttrCnt = "prof_" . $profName . "_OK";
        $profAttrDuration = "prof_" . $profName . "_Duration";
        $this->WriteAttributeInteger($profAttrCnt, $this->ReadAttributeInteger($profAttrCnt)+1);
        $duration = $this->CalcDuration_ms($this->ReadAttributeFloat($profAttrDuration));
        $this->WriteAttributeFloat($profAttrDuration, $duration);			
        if($doUpdateCnt) { SetValue($this->GetIDForIdent("updateCntOk"), GetValue($this->GetIDForIdent("updateCntOk")) + 1); }
        if($this->logLevel >= LogLevel::TRACE) { $this->AddLog(__FUNCTION__, sprintf("%s [Cnt: %s | Duration: %s ms]", $profName, $this->ReadAttributeInteger($profAttrCnt), $duration)); }
    }	
    
    protected function profilingFault($profName, $msg) {
        $profAttrCnt = "prof_" . $profName  . "_NotOK";
        $profAttrDuration = "prof_" . $profName . "_Duration";
        $this->WriteAttributeInteger($profAttrCnt, $this->ReadAttributeInteger($profAttrCnt)+1);
        $this->WriteAttributeFloat($profAttrDuration, -1);	
        SetValue($this->GetIDForIdent("updateCntError"), GetValue($this->GetIDForIdent("updateCntError")) + 1);  
        SetValue($this->GetIDForIdent("updateLastError"), $msg);			
        if($this->logLevel >= LogLevel::TRACE) { $this->AddLog(__FUNCTION__, sprintf("%s [Cnt: %s | msg: %s ]", $profName, $this->ReadAttributeInteger($profAttrCnt), $msg)); }        
    }	

    protected function GetProfilingData(string $caller='?') {
        if($this->logLevel >= LogLevel::INFO) { $this->AddLog(__FUNCTION__, sprintf("GetProfilingData [%s] ...", $caller)); }
        $profDataArr = [];
        foreach(self::PROF_NAMES as $profName) {
            $arrEntry = array();
            $arrEntry["cntStart"] = $this->ReadAttributeInteger("prof_" . $profName);
            $arrEntry["cntOK"] = $this->ReadAttributeInteger("prof_" . $profName . "_OK");
            $arrEntry["cntNotOk"] = $this->ReadAttributeInteger("prof_" . $profName  . "_NotOK");
            $arrEntry["duration"] = $this->ReadAttributeFloat("prof_" . $profName . "_Duration");
            $profDataArr[$profName] = $arrEntry;
        }
        return $profDataArr;				
    }

    protected function GetProfilingDataAsText(string $caller='?') {
        if($this->logLevel >= LogLevel::INFO) { $this->AddLog(__FUNCTION__, sprintf("GetProfilingDataAsText [%s] ...", $caller)); }
        $profilingInfo = "";
        foreach(self::PROF_NAMES as $profName) {
            $profilingInfo .= sprintf("\r\n%s: %s\r\n",  $profName, $this->ReadAttributeInteger("prof_" . $profName));
            $profilingInfo .= sprintf("%s: %s\r\n",  $profName . "_OK", $this->ReadAttributeInteger("prof_" . $profName . "_OK"));
            $profilingInfo .= sprintf("%s: %s\r\n",  $profName  . "_NotOK", $this->ReadAttributeInteger("prof_" . $profName  . "_NotOK"));
            $profilingInfo .= sprintf("%s: %s ms\r\n",  $profName . "_Duration", $this->ReadAttributeFloat("prof_" . $profName . "_Duration")); 
        }
        if($this->logLevel >= LogLevel::INFO) { $this->AddLog(__FUNCTION__, sprintf("ProfilingData: %s", $profilingInfo), 0);  }
        return $profilingInfo;
    }

    protected function Reset_ProfilingData(string $caller='?') {
        if($this->logLevel >= LogLevel::INFO) { $this->AddLog(__FUNCTION__, sprintf("Reset_ProfilingData [%s] ...", $caller)); }
        foreach(self::PROF_NAMES as $profName) {
            $this->WriteAttributeInteger("prof_" . $profName, 0);
            $this->WriteAttributeInteger("prof_" . $profName . "_OK", 0);
            $this->WriteAttributeInteger("prof_" . $profName  . "_NotOK", 0);
            $this->WriteAttributeFloat("prof_" . $profName . "_Duration", 0);
        }
    }


    protected function CreateCategoryByIdent($identName, $categoryName, $parentId, $position = 0, $icon = "") {

        $identName = $this->GetValidIdent($identName);
        $categoryId = @IPS_GetObjectIDByIdent($identName, $parentId);
        if ($categoryId === false) {

            if ($this->logLevel >= LogLevel::TRACE) {
                $this->AddLog(
                    __FUNCTION__,
                    sprintf("Create IPS-Category :: Name: %s | Ident: %s | ParentId: %s", $categoryName, $identName, $parentId)
                );
            }

            $categoryId = IPS_CreateCategory();
            IPS_SetParent($categoryId, $parentId);
            IPS_SetIdent($categoryId, $identName);
            IPS_SetName($categoryId, $categoryName);
            IPS_SetPosition($categoryId, $position);
            if (!empty($icon)) {
                IPS_SetIcon($categoryId, $icon);
            }
        } else {
            if ($this->logLevel >= LogLevel::TRACE) {
                $this->AddLog(__FUNCTION__, sprintf("IPS-Category exists:: Name: %s | Ident: %s | ParentId: %s", $categoryName, $identName, $parentId)
                );
            }
        }
        return $categoryId;
    }

    protected function CreateDummyInstanceByIdent($identName, $instanceName, $parentId, $position = 0, $icon = "") {

        $identName = $this->GetValidIdent($identName);
        $instanceId = @IPS_GetObjectIDByIdent($identName, $parentId);
        if ($instanceId === false) {

            if ($this->logLevel >= LogLevel::TRACE) {
                $this->AddLog(
                    __FUNCTION__,
                    sprintf("Create IPS-DummyInstance :: Name: %s | Ident: %s | ParentId: %s", $instanceName, $identName, $parentId)
                );
            }

            $instanceId = IPS_CreateInstance("{485D0419-BE97-4548-AA9C-C083EB82E61E}");
            IPS_SetParent($instanceId, $parentId);
            IPS_SetIdent($instanceId, $identName);
            IPS_SetName($instanceId, $instanceName);
            IPS_SetPosition($instanceId, $position);
            if (!empty($icon)) {
                IPS_SetIcon($instanceId, $icon);
            }
        } else {
            if ($this->logLevel >= LogLevel::TEST) {
                $this->AddLog(__FUNCTION__, sprintf("IPS-DummyInstance exists:: Name: %s | Ident: %s | ParentId: %s", $instanceName, $identName, $parentId)
                );
            }
        }
        return $instanceId;
    }

    protected function SetVariableByIdent($value, $identName, $varName, $parentId, $varType = 3, $position = 0, $varProfile = "", $icon = "", $forceUpdate = false, $round = null, $faktor = 1) {

        $variable_created = false;
        $identName = $this->GetValidIdent($identName);
        $varId = @IPS_GetObjectIDByIdent($identName, $parentId);
        if ($varId === false) {
            if ($varType < 0) {
                $varType = $this->GetTypeFromValue($value);
            }
            $varId = IPS_CreateVariable($varType);
            $variable_created = true;

            if ($this->logLevel >= LogLevel::TRACE) {
                $this->AddLog(__FUNCTION__, sprintf("Createed IPS-Variable :: Type: %d | Ident: %s | Profile: %s | Name: %s | ForceUpdate: %b", $varType, $identName, $varProfile, $varName, $forceUpdate));
            }

        }

        if ($variable_created || $forceUpdate) {
            IPS_SetParent($varId, $parentId);
            IPS_SetName($varId, $varName);
            IPS_SetIdent($varId, $identName);
            IPS_SetPosition($varId, $position);
            IPS_SetVariableCustomProfile($varId, $varProfile);
            if (!empty($icon)) {
                IPS_SetIcon($varId, $icon);
            }
        }

        if ($faktor != 1) {
            $value = $value * $faktor;
        }
        if (!is_null($round)) {
            $value = round($value, $round);
        }
        $result = SetValue($varId, $value);
        if (!$result) {
            if ($this->logLevel >= LogLevel::WARN) {
                $this->AddLog(__FUNCTION__, sprintf("WARN :: Cannot set Value '%s' to Varible '%s' [parentId: %s | identName: %s | varId: %s | type: %s]", print_r($value, true), $varName, $parentId, $identName, $varId, gettype($value)));
            }
        } else {
            if ($this->logLevel >= LogLevel::TRACE) {
                $this->AddLog(__FUNCTION__, sprintf("Set Value '%s' to Varible '%s' [parentId: %s | identName: %s | varId: %s | type: %s]", print_r($value, true), $varName, $parentId, $identName, $varId, gettype($value)));
            }
        }

        return $varId;
    }


    /* START ::::: used in SunSpecMeterSimulator */

    protected function GetCategoryID($identName, $categoryName, $parentId, $position=0) {

        $categoryId = @IPS_GetObjectIDByIdent($identName, $parentId);
        if ($categoryId == false) {

            if($this->logLevel >= LogLevel::TRACE) { $this->AddLog(__FUNCTION__, 
                sprintf("Create IPS-Category :: Name: %s | Ident: %s | ParentId: %s", $categoryName, $identName, $parentId)); }	

            $categoryId = IPS_CreateCategory();
            IPS_SetParent($categoryId, $parentId);
            IPS_SetIdent($categoryId, $identName);
            IPS_SetName($categoryId, $categoryName);
            IPS_SetPosition($categoryId, $position);
        }  
        return $categoryId;

    }


    protected function GetDummyModuleID($identName, $instanceName, $parentId, $position=0) {

        $instanceId = @IPS_GetObjectIDByIdent($identName, $parentId);
        if ($instanceId == false) {

            if($this->logLevel >= LogLevel::TRACE) { $this->AddLog(__FUNCTION__, 
                sprintf("Create Dummy-Module :: Name: %s | Ident: %s | ParentId: %s", $instanceName, $identName, $parentId)); }	

            $instanceId = IPS_CreateInstance("{485D0419-BE97-4548-AA9C-C083EB82E61E}");
            IPS_SetParent($instanceId, $parentId);
            IPS_SetIdent($instanceId, $identName);
            IPS_SetName($instanceId, $instanceName);
            IPS_SetPosition($instanceId, $position);
        }  
        return $instanceId;

    }

    protected function SaveVariableValue($value, $parentId, $varIdent, $varName, $varType=3, $position=0, $varProfile="", $asMaxValue=false) {
			
        $varId = @IPS_GetObjectIDByIdent($varIdent, $parentId);
        if($varId === false) {

            if($varType < 0) {
                switch(gettype($value)) {
                    case "boolean":
                        $varType = VARIABLE::TYPE_BOOLEAN;
                        break;
                    case "integer":
                        $varType = VARIABLE::TYPE_INTEGER;
                        break;     
                    case "double":
                    case "float":
                        $varType = VARIABLE::TYPE_FLOAT;
                        break;                                                  
                    default:
                        $varType = VARIABLE::TYPE_STRING;
                        break;
                }
            }

            if($this->logLevel >= LogLevel::TRACE) { $this->AddLog(__FUNCTION__, 
                sprintf("Create IPS-Variable :: Type: %d | Ident: %s | Profile: %s | Name: %s", $varType, $varIdent, $varProfile, $varName)); }	

            $varId = IPS_CreateVariable($varType);
            IPS_SetParent($varId, $parentId);
            IPS_SetIdent($varId, $varIdent);
            IPS_SetName($varId, $varName);
            IPS_SetPosition($varId, $position);
            IPS_SetVariableCustomProfile($varId, $varProfile);
            //AC_SetLoggingStatus ($this->archivInstanzID, $varId, true);
            //IPS_ApplyChanges($this->archivInstanzID);

        }			
        
        if($asMaxValue) {
            $valueTemp = GetValue($varId); 
            if($value > $valueTemp) {
                SetValue($varId, $value); 	
            }

        } else {
            if(IPS_GetVariable($varId)["VariableType"]  == VARIABLE::TYPE_FLOAT) {
                $value = round($value, 2);
            }
            $result = SetValue($varId, $value);  
            if(!$result) {
                if($this->logLevel >= LogLevel::WARN) { $this->AddLog(__FUNCTION__, sprintf("WARN :: Cannot save Variable '%s' with value '%s' [parentId: %s | varIdent: %s | varId: %s | type: %s]", $varName, print_r($value, true), $parentId, $varIdent, $varId, gettype($value))); }	
            }
        }
        return $value;
    }

    /* END  ::::: used in SunSpecMeterSimulator */

    protected function GetValidIdent($ident) {

        $ident = strtr($ident, [
            '-' => '_',
            ' ' => '_',
            ':' => '_',
            '%' => 'p'
        ]);

        $ident = preg_replace('/[^A-Za-z0-9\.\_]/', '', $ident);
        return $ident;
    }

    protected function GetTypeFromValue($value) {
        $varType = VARIABLE::TYPE_STRING;
        switch (gettype($value)) {
            case "boolean":
                $varType = VARIABLE::TYPE_BOOLEAN;
                break;
            case "integer":
                $varType = VARIABLE::TYPE_INTEGER;
                break;
            case "double":
            case "float":
                $varType = VARIABLE::TYPE_FLOAT;
                break;
            default:
                $varType = VARIABLE::TYPE_STRING;
                break;
        }
        return $varType;
    }


    protected function implode_recursive(string $separator, array $array): string {
        $string = '';
        foreach ($array as $i => $a) {
            if (is_array($a)) {
                $string .= implode_recursive($separator, $a);
            } else {
                $string .= $a;
                if ($i < count($array) - 1) {
                    $string .= $separator;
                }
            }
        }
        return $string;
    }


    protected function Increase_CounterVariable(int $varId) {
        SetValueInteger($varId, GetValueInteger($varId) + 1);
    }

    protected function UnixTimestamp2String(int $timestamp) {
        return date('d.m.Y H:i:s', $timestamp);
    }

    protected function String2Hex($string) {
        $hex = '';
        for ($i = 0; $i < strlen($string); $i++) {
            $hex .= sprintf("%02X", ord($string[$i])) . " ";
        }
        return trim($hex);
    }

    protected function LoadFileContents($fileName) {
        if($this->logLevel >= LogLevel::DEBUG) { $this->AddLog(__FUNCTION__, sprintf("Load File Content form '%s'", $fileName)); }	
        return file_get_contents($fileName);
    }

    protected function CalcDuration_ms(float $timeStart) {
        $duration =  microtime(true) - $timeStart;
        return round($duration * 1000, 2);
    }

}


?>