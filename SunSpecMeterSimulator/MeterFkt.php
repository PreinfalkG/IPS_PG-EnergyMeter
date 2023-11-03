<?php 

declare(strict_types=1);

trait METER_FUNCTIONS {



    private function ProcessData(string $receivedData): string {
        
        $responceData = "";
        $meterValueSource = $this->ReadPropertyInteger("selMeterDataSource");           // 0 = all Values '0.0' | 1 = link to Variables | 2 = Update Function | 3 = Sample Values | -1 = nod defined
        $len = strlen($receivedData);
    
        if($len < 12 ) {
            $this->IncreaseCnt("Cnt_NoRequestData");
        } else {
    
            $modbusTCP_Part1_TIPI   = substr($receivedData, 0, 4);      // Transaction Identifier (2 byte) | Protocol Identifier (2 byte) 
            $modbusTCP_Part2_Len    = substr($receivedData, 4, 2);      // Length (2 byte)
            $modbusTCP_Part3_UI     = substr($receivedData, 6, 1);      // Unit Identifier (1 byte) = Modbus Adress
            $modbusData = substr($receivedData, -5);                    // Function Code (1 byte) | Reference/Register Number (2 byte) | Word Count (2 byte)

            $modbusAdress = ord($modbusTCP_Part3_UI);
            if($modbusAdress != $this->ReadPropertyInteger("MeterModbusAddress")) {
                if($this->logLevel >= LogLevel::DEBUG) { $this->AddLog(__FUNCTION__, sprintf("Modbus Adress does not match [%d <> %d]", $modbusAdress, $this->ReadPropertyInteger("MeterModbusAddress"))); }
            } else {
   
                if($this->logLevel >= LogLevel::TRACE) { $this->AddLog(__FUNCTION__, sprintf("ReceivedData Len:  %d", $len)); }
                if($this->logLevel >= LogLevel::TRACE) { $this->AddLog(__FUNCTION__, sprintf("ModbusTCP_Part1_TIPI: %s", $this->String2Hex($modbusTCP_Part1_TIPI))); }
                if($this->logLevel >= LogLevel::TRACE) { $this->AddLog(__FUNCTION__, sprintf("ModbusTCP_Part2_Len: %s", $this->String2Hex($modbusTCP_Part2_Len))); }
                if($this->logLevel >= LogLevel::TRACE) { $this->AddLog(__FUNCTION__, sprintf("ModbusTCP_Part3_UI: %s", $this->String2Hex($modbusTCP_Part3_UI))); }
                if($this->logLevel >= LogLevel::TRACE) { $this->AddLog(__FUNCTION__, sprintf("Part_modbusData: %s", $this->String2Hex($modbusData))); }

                switch($modbusData) {
        
                    case "\x03\x9C\x88\x00\x3A":                                                                                //  40072 :: Word Count 58    
                        //$recordedResponse = "\x03\x74\x3c\x13\x74\xbc\x3f\x21\xd3\x75\x3d\xdc\xbc\x6a\x3e\x3f\xe3\x54\x43\x65\x23\x6d\x43\x64\xd7\xbd\x43\x65\x4a\xb3\x43\x65\x47\xd7\x43\xc6\x70\x83\x43\xc6\x93\xe4\x43\xc6\x77\xb2\x43\xc6\x45\xf3\x42\x48\x0a\x3d\x41\xfe\x20\x00\x41\x95\x50\x00\x41\x15\x10\x00\x40\x72\x80\x00\x43\x54\x68\x00\x43\x10\xb6\x00\x41\xc5\xc8\x00\x42\x2b\xec\x00\xc3\x47\x78\x00\xc3\x0a\x6b\x00\xc1\x9e\x30\x00\xc2\x25\x28\x00\x3e\x18\x93\x75\x3e\x04\x18\x93\x3e\xc1\x06\x25\x3d\xb6\x45\xa2";
                        $responceData = $modbusTCP_Part1_TIPI . "\x00\x77". $modbusTCP_Part3_UI;
                        $responceData .= "\x03\x74";                                                                            // Function Code: 3 |  Byte Count: 116 (0x74)  
                        $dataArr = $this->GetRegisterData_40072_40129($meterValueSource);                                 // Register Values 40072 bis 40129 (A, V, Hz, W, VA, VAr, cos) 
                        $responceData .= pack("G*", ...$dataArr);                                                               // Pack as float32
                        $this->IncreaseCnt("Cnt_40072");
                        break;
        
                    case "\x03\x9C\xC2\x00\x20":                                                                                //  40130 : Word Count 32       
                        //$recordedResponse = "\x03\x40\x48\xc2\x85\x2b\x48\x2e\x94\xa4\x47\xea\x33\x54\x47\xd5\x11\xcc\x4a\x0f\x02\x8c\x49\x32\x18\xce\x48\x5b\x9f\xfe\x49\x9e\x72\x1a\x48\xdf\xa2\xff\x48\x3c\x5e\xfa\x47\xf0\xe1\xec\x47\xf5\x02\x84\x4a\xb6\xf2\x2a\x49\xd7\x62\xa7\x49\x19\xbd\x98\x4a\x5b\xc3\x8d";
                        $responceData = $modbusTCP_Part1_TIPI . "\x00\x43". $modbusTCP_Part3_UI;                                    
                        $responceData .= "\x03\x40";                                                                            // Function Code: 3 |  Byte Count: 64 (0x40)  
                        $dataArr = $this->GetRegisterData_40130_40161($meterValueSource);                                 // Register Values 40130 bis 40161 (Energy Data Wh, VAh)
                        $responceData .= pack("G*", ...$dataArr);                                                               // Pack as float32
                        $this->IncreaseCnt("Cnt_40130");
                        break;
        
                    case "\x03\x9D\x02\x00\x02":                                                                                //  40194 : Word Count 2       
                        $responceData = $modbusTCP_Part1_TIPI . "\x00\x07". $modbusTCP_Part3_UI . "\x03\x04\x00\x00\x00\x00";   // return Registers 40194=0 | 40195=0 Events bitfield32
                        $this->IncreaseCnt("Cnt_40194");
                        break;
        
        
                    case "\x03\x03\x00\x00\x01":        //  768 : Word Count 1       
                        $responceData = $modbusTCP_Part1_TIPI . "\x00\x03" . $modbusTCP_Part3_UI . "\x83\x02";                  //return 'Illegal data address'
                        $this->IncreaseCnt("Cnt_768");
                        break;
        
                    case "\x03\x06\xAA\x00\x01":        //  1076 : Word Count 1               
                        $responceData = $modbusTCP_Part1_TIPI . "\x00\x03" . $modbusTCP_Part3_UI . "\x83\x02";                  //return 'Illegal data address'
                        $this->IncreaseCnt("Cnt_1076");
                        break;
        
                    case "\x03\x9C\x40\x00\x02":                                                                                //  40000 : Word Count 2              
                        $responceData = $modbusTCP_Part1_TIPI . "\x00\x07". $modbusTCP_Part3_UI;
                        $responceData .= "\x03\x04";                                                                            // Function Code: 3 / Byte Count 4
                        $responceData .= "\x53\x75\x6e\x53";                                                                    // 0x53756e53 = SunS - Uniquely identifies this as a SunSpec Modbus Map
                        $this->IncreaseCnt("Cnt_40000");
                        break;
        
                    case "\x03\xC3\x50\x00\x02":                                                                                //  50.000 : Word Count 2              
                        $responceData = $modbusTCP_Part1_TIPI . "\x00\x03" . $modbusTCP_Part3_UI . "\x83\x02";                  //return 'Illegal data address'
                        $this->IncreaseCnt("Cnt_50000");
                        break;
        
                    case "\x03\x9C\x42\x00\x02":                                                                                //  40002 : Word Count 2       
                        $responceData = $modbusTCP_Part1_TIPI . "\x00\x07". $modbusTCP_Part3_UI;
                        $responceData .= "\x03\x04";                                                                            // Function Code: 3 / Byte Count 4
                        $responceData .= "\x00\x01";                                                                            // Register 40003 = 1  = 0x01   - known value. Uniquely identifies this as a SunSpec Common Model block
                        $responceData .= "\x00\x42";                                                                            // Register 40004 = 66 = 0x42   - Length of Common Model block
                        $this->IncreaseCnt("Cnt_40002");
                        break;
        
                    case "\x03\x9C\x86\x00\x02":                                                                                //  40070 : Word Count 2       
                        $responceData = $modbusTCP_Part1_TIPI . "\x00\x07". $modbusTCP_Part3_UI;
                        $responceData .= "\x03\x04";                                                                            // Function Code: 3 / Byte Count 4

                        //$responceData .= "\x00\xd5";                                                                          // Register 40070=213           - SunSpec Meter Modbus Map (float); 211: single phase, 212: split phase, 213: three phase
                        $meterSunSpecModel = $this->ReadPropertyInteger("MeterSunSpecModel");
                        if($meterSunSpecModel == 211) {
                            $responceData .= "\x00\xd3";                                                                        // Register 40070=211: single phase
                            if($this->logLevel >= LogLevel::INFO) { $this->AddLog(__FUNCTION__, sprintf("Simulate 'single phase' meter [meter model: %d]", $meterSunSpecModel)); }
                        } else if ($meterSunSpecModel == 212) {
                            $responceData .= "\x00\xd4";                                                                        // Register 40070=212: split phase
                            if($this->logLevel >= LogLevel::INFO) { $this->AddLog(__FUNCTION__, sprintf("Simulate 'split phase' meter [meter model: %d]", $meterSunSpecModel)); }
                        } else {
                            $responceData .= "\x00\xd5";                                                                        // Register 40070=213: three phase
                            if($this->logLevel >= LogLevel::INFO) { $this->AddLog(__FUNCTION__, sprintf("Simulate 'three phase' meter [meter model: %d]", $meterSunSpecModel)); }
                        }
                                                
                        $responceData .= "\x00\x7c";                                                                            // Register 40071=124           - Length of inverter model block
                        $this->IncreaseCnt("Cnt_40070");
                        break;
        
                    case "\x03\x9D\x04\x00\x02":                                                                                //  40196 : Word Count 2       
                        $responceData = $modbusTCP_Part1_TIPI . "\x00\x07". $modbusTCP_Part3_UI;
                        $responceData .= "\x03\x04";
                        $responceData .= "\xFF\xFF";                                                                            // Register 40196 = 65535 = 0xFF 0xFF   - Identifies this as End block
                        $responceData .= "\x00\x00";                                                                            // Register 40197 = 0 = 0x00            - Length of model block
                        $this->IncreaseCnt("Cnt_40196");
                        break;
        
                    case "\x03\x9C\x44\x00\x42":                                                                                //  40004 : Word Count 66 (Registers 40004 bis 40069 Device Infos)      
                        //$recordedResponse = "\x03\x84\x56\x45\x52\x42\x55\x4e\x44\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x50\x6f\x77\x65\x72\x2d\x4d\x65\x74\x65\x72\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x45\x55\x4d\x45\x4c\x20\x31\x2e\x30\x00\x00\x00\x00\x00\x00\x00\x32\x2e\x33\x2e\x30\x2d\x46\x6c\x65\x78\x00\x00\x00\x00\x00\x00\x41\x38\x34\x30\x34\x31\x31\x38\x44\x44\x44\x44\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x6b\x80\x00";

                        $responceData = $modbusTCP_Part1_TIPI . "\x00\x87". $modbusTCP_Part3_UI;
                        $responceData .= "\x03\x84";                                                                            // Function Code: 3 |  Byte Count: 132 (0x84) 
                        //$responceData .= str_pad("VERBUND",      32, chr(0x00));                                              // Manufacturer
                        //$responceData .= str_pad("Power-Meter",  32, chr(0x00));                                              // Device model
                        //$responceData .= str_pad("EUMEL 1.0",    16, chr(0x00));                                              // Options
                        //$responceData .= str_pad("2.3.0-Flex",   16, chr(0x00));                                              // SW version of meter
                        //$responceData .= str_pad("A8404118AD9E", 32, chr(0x00));                                              // Serialnumber of the meter

                        $responceData .= str_pad(substr($this->ReadPropertyString("MeterManufacturer"), 0, 31),     32, chr(0x00));     // Manufacturer
                        $responceData .= str_pad(substr($this->ReadPropertyString("MeterDeviceModel"), 0, 31),      32, chr(0x00));     // Device model
                        $responceData .= str_pad(substr($this->ReadPropertyString("MeterOptions"), 0, 15),          16, chr(0x00));     // Options
                        $responceData .= str_pad(substr($this->ReadPropertyString("MeterSoftware"), 0, 15),         16, chr(0x00));     // SW version of meter
                        $responceData .= str_pad(substr($this->ReadPropertyString("MeterSerialnumber"), 0, 31),     32, chr(0x00));     // Serialnumber of the meter

                        $responceData .= "\x00" . $modbusTCP_Part3_UI;                                                          // Modbus Device Address 'mit 2 Byte' länge
                        $responceData .= "\x80\x00";                                                                            // ???        
                        $this->IncreaseCnt("Cnt_40004");
                        break;
                    default:
                        $this->IncreaseCnt("Cnt_untreated");
                        if($this->logLevel >= LogLevel::WARN) { $this->AddLog(__FUNCTION__,  sprintf("WARN: untreated Modbus Request [%s]", $this->String2Hex($receivedData))); }
                        break;
                }
            }
        }
        return $responceData;
    }


    public function CreateRegisterData_40072_40129(float $initValue=0.0):array {
        $registerDataArr = array_fill(0, 29, $initValue);
        return $registerDataArr;
    }

    public function SetRegisterData_40072_40129(array $meterValueArr) {
        $json = json_encode($meterValueArr);
        $this->SetBuffer("RegisterData_40072_40129", $json);
        $this->SetBuffer("RegisterData_40072_40129_TimeStamp", time());
    }

    private function CreateBufRegisterData_40072_40129() {
        $his->SetRegisterData_40072_40129($this->CreateRegisterData_40072_40129(0.0));
    }

    public function GetRegisterData_40072_40129(int $meterValueSource):array {

        $registerDataArr = [];

        switch ($meterValueSource) {                                      // 0 = all Values '0.0' | 1 = link to Variables | 2 = Update Function | 3 = Sample Values
            case 1:
                $registerDataArr[] = $this->GetMeterValue("40072");            // 40072 - AC Total Current value                       [A | A]
                $registerDataArr[] = $this->GetMeterValue("40074");            // 40074 - AC Phase-A Current value                     [AphA | A]
                $registerDataArr[] = $this->GetMeterValue("40076");            // 40076 - AC Phase-B Current value                     [AphB | A]
                $registerDataArr[] = $this->GetMeterValue("40078");            // 40078 - AC Phase-C Current value                     [AphB | A]
                $registerDataArr[] = $this->GetMeterValue("40080");            // 40080 - AC Voltage Average Phase-to-neutral value    [PhV | V]
                $registerDataArr[] = $this->GetMeterValue("40082");            // 40082 - AC Voltage Phase-A-to-neutral value          [PhVphA | V]
                $registerDataArr[] = $this->GetMeterValue("40084");            // 40084 - AC Voltage Phase-B-to-neutral value          [PhVphB | V]
                $registerDataArr[] = $this->GetMeterValue("40086");            // 40086 - AC Voltage Phase-C-to-neutral value          [PhVphC | V]
                $registerDataArr[] = $this->GetMeterValue("40088");            // 40088 - AC Voltage Average Phase-to-phase value      [PPV | V]
                $registerDataArr[] = $this->GetMeterValue("40090");            // 40090 - AC Voltage Phase-AB value                    [PPVphAB | V]
                $registerDataArr[] = $this->GetMeterValue("40092");            // 40092 - AC Voltage Phase-BC value                    [PPVphBC | V]
                $registerDataArr[] = $this->GetMeterValue("40094");            // 40094 - AC Voltage Phase-CA value                    [PPVphCA | V]
                $registerDataArr[] = $this->GetMeterValue("40096");            // 40096 - AC Frequency value                           [Hz | Hz]
                $registerDataArr[] = $this->GetMeterValue("40098");            // 40098 - AC Power value                               [W | W]  { intval(date('i')) }
                $registerDataArr[] = $this->GetMeterValue("40100");            // 40100 - AC Power Phase A value                       [WphA | W]
                $registerDataArr[] = $this->GetMeterValue("40102");            // 40102 - AC Power Phase B value                       [WphB | W]
                $registerDataArr[] = $this->GetMeterValue("40104");            // 40104 - AC Power Phase C value                       [WphC | W]
                $registerDataArr[] = $this->GetMeterValue("40106");            // 40106 - AC Apparent Power value                      [VA | VA]
                $registerDataArr[] = $this->GetMeterValue("40108");            // 40108 - AC Apparent Power Phase A value              [VAphA | VA]
                $registerDataArr[] = $this->GetMeterValue("40110");            // 40110 - AC Apparent Power Phase B value              [VAphB | VA] 
                $registerDataArr[] = $this->GetMeterValue("40112");            // 40112 - AC Apparent Power Phase C value              [VAphC | VA]
                $registerDataArr[] = $this->GetMeterValue("40114");            // 40114 - AC Reactive Power value                      [VAR | VAr]
                $registerDataArr[] = $this->GetMeterValue("40116");            // 40116 - AC Reactive Power Phase A value              [VARphA | VAr]
                $registerDataArr[] = $this->GetMeterValue("40118");            // 40118 - AC Reactive Power Phase B value              [VARphB | VAr]
                $registerDataArr[] = $this->GetMeterValue("40120");            // 40120 - AC Reactive Power Phase C value              [VARphC | VAr]
                $registerDataArr[] = $this->GetMeterValue("40122");            // 40122 - Power Factor value                           [PF | cos()]
                $registerDataArr[] = $this->GetMeterValue("40124");            // 40124 - Power Factor Phase A value                   [PFphA | cos()]
                $registerDataArr[] = $this->GetMeterValue("40126");            // 40126 - Power Factor Phase B value                   [PFphB | cos()]
                $registerDataArr[] = $this->GetMeterValue("40128");            // 40128 - Power Factor Phase C value                   [PFphC | cos()]
                break;
            case 2:
                $jsonBuf =  $this->GetBuffer("RegisterData_40072_40129");
                if($jsonBuf == "") {
                    $registerDataArr = $this->CreateRegisterData_40072_40129(0);
                } else {
                    $registerDataArr = json_decode($jsonBuf, true);
                }
                break;                
            case 3:
                $dataArr = $this->GetSunSRegisterMap();
                for($i=40072; $i<=40128; $i=$i+2) {
                    $registerDataArr[] = $dataArr[$i][3];
                }               
                break;
            default:
                $registerDataArr = $this->CreateRegisterData_40072_40129(0.0);                             
                break;
        }


        if($this->logLevel >= LogLevel::DEBUG) {
            $dataArr = $this->GetSunSRegisterMap();
            $this->AddLog(__FUNCTION__, sprintf("Used Meter Value SOURCE '%s [%d]'", $this->GetMeterValueSource($meterValueSource), $meterValueSource), 0);
            for($arrIndex=0; $arrIndex<29; $arrIndex++) {
                $modbusRegister = 40072 + $arrIndex*2;
                $this->AddLog(__FUNCTION__, sprintf("%.3f %s - %d %s", $registerDataArr[$arrIndex], $dataArr[$modbusRegister][2], $modbusRegister, $dataArr[$modbusRegister][1] ), 0);
            }  
        }

        if($this->logLevel >= LogLevel::DEBUG) { $this->AddLog(__FUNCTION__,  "RegisterData 40072 - 40129 filled"); }
        return $registerDataArr;  
    }
    

    public function CreateRegisterData_40130_40161(float $initValue=0.0):array {
        $registerDataArr = array_fill(0, 16, $initValue);
        return $registerDataArr;
    }

    public function SetRegisterData_40130_40161(array $meterValueArr) {
        $json = json_encode($meterValueArr);
        $this->SetBuffer("RegisterData_40130_40161", $json);
        $this->SetBuffer("RegisterData_40130_40161_TimeStamp", time());
    }

    private function CreateBufRegisterData_40130_40161() {
        $his->SetRegisterData_40130_40161($this->CreateRegisterData_40130_40161(0.0));
    }

    public function GetRegisterData_40130_40161(int $meterValueSource):array {

        $registerDataArr = [];

        switch ($meterValueSource) {                                            // 0 = all Values '0.0' | 1 = link to Variables | 2 = Update Function | 3 = Sample Values
            case 1:
                $registerDataArr[] = $this->GetMeterValue("40130");             // 40130 - Total Watt-hours Exported                   [TotWhExp | Wh]
                $registerDataArr[] = $this->GetMeterValue("40132");             // 40132 - Total Watt-hours Exported phase A           [TotWhExpPhA | Wh]
                $registerDataArr[] = $this->GetMeterValue("40134");             // 40134 - Total Watt-hours Exported phase B           [TotWhExpPhB | Wh]
                $registerDataArr[] = $this->GetMeterValue("40136");             // 40136 - Total Watt-hours Exported phase C           [TotWhExpPhC | Wh]
                $registerDataArr[] = $this->GetMeterValue("40138");             // 40138 - Total Watt-hours Imported                   [TotWhImpPh | Wh]
                $registerDataArr[] = $this->GetMeterValue("40140");             // 40140 - Total Watt-hours Imported phase A           [TotWhImpPhA | Wh]
                $registerDataArr[] = $this->GetMeterValue("40142");             // 40142 - Total Watt-hours Imported phase B           [TotWhImpPhA | Wh]
                $registerDataArr[] = $this->GetMeterValue("40144");             // 40144 - Total Watt-hours Imported phase C           [TotWhImpPhA | Wh]
                $registerDataArr[] = $this->GetMeterValue("40146");             // 40146 - Total VA-hours Exported                     [TotVAhExp | VAh]
                $registerDataArr[] = $this->GetMeterValue("40148");             // 40148 - Total VA-hours Exported phase A             [TotVAhExpPhA | VAh]
                $registerDataArr[] = $this->GetMeterValue("40150");             // 40150 - Total VA-hours Exported phase B             [TotVAhExpPhB | VAh]
                $registerDataArr[] = $this->GetMeterValue("40152");             // 40152 - Total VA-hours Exported phase C             [TotVAhExpPhC | VAh]
                $registerDataArr[] = $this->GetMeterValue("40154");             // 40154 - Total VA-hours Imported                     [TotVAhImp | VAh]
                $registerDataArr[] = $this->GetMeterValue("40156");             // 40156 - Total VA-hours Imported phase A             [TotVAhImpPhA | VAh]
                $registerDataArr[] = $this->GetMeterValue("40158");             // 40158 - Total VA-hours Imported phase B             [TotVAhImpPhB | VAh]
                $registerDataArr[] = $this->GetMeterValue("40160");             // 40160 - Total VA-hours Imported phase C             [TotVAhImpPhC | VAh]    
                break;
            case 2:
                $jsonBuf =  $this->GetBuffer("RegisterData_40130_40161");
                if($jsonBuf == "") {
                    $registerDataArr = $this->CreateRegisterData_40130_40161(0);
                } else {
                    $registerDataArr = json_decode($jsonBuf, true);
                }
                break;
            case 3:
                $dataArr = $this->GetSunSRegisterMap();
                for($i=40130; $i<=40160; $i=$i+2) {
                    $registerDataArr[] = $dataArr[$i][3];
                }               
                break;
            default:
                $registerDataArr = $this->CreateRegisterData_40130_40161(0.0);         
                break;
        }

        if($this->logLevel >= LogLevel::DEBUG) {
            $dataArr = $this->GetSunSRegisterMap();
            $this->AddLog(__FUNCTION__, sprintf("Used Meter Value SOURCE '%s [%d]'", $this->GetMeterValueSource($meterValueSource), $meterValueSource), 0);
            for($arrIndex=0; $arrIndex<16; $arrIndex++) {
                $modbusRegister = 40130 + $arrIndex*2;
                $this->AddLog(__FUNCTION__, sprintf("%.3f %s - %d %s", $registerDataArr[$arrIndex], $dataArr[$modbusRegister][2], $modbusRegister, $dataArr[$modbusRegister][1] ), 0);
            }  
        }

        if($this->logLevel >= LogLevel::DEBUG) { $this->AddLog(__FUNCTION__,  "RegisterData 40130 - 40161 filled"); }            
        return $registerDataArr;      
    }
    

    private function GetMeterValue(string $propertyName):float {
        $meterValue = 0;
        $varIdConfigured = @$this->ReadPropertyInteger($propertyName);
        if($varIdConfigured > 1) {
            $meterValue = GetValue($varIdConfigured);
        }
        if($this->logLevel >= LogLevel::DEBUG) { $this->AddLog(__FUNCTION__,  sprintf(" %s = %f", $propertyName, $meterValue)); }  
        return $meterValue;
    }

    private function GetMeterValueSource(int $key) : string {

        $meterValSourceArr = $this->GetMeterValueSources();
        if(array_key_exists($key, $meterValSourceArr)) {
            return $meterValSourceArr [$key];
        } else {
            return "Soruce not exist";
        }      
    }

    private function GetMeterValueSources() : array {
        $meterValSourceArr = [];
        $meterValSourceArr[-1] = "not defined";
        $meterValSourceArr[0] = "all Values '0.0'";
        $meterValSourceArr[1] = "Link to Variables";
        $meterValSourceArr[2] = "Update Function";
        $meterValSourceArr[3] = "use Sample Values";
        return $meterValSourceArr;
    }

    private function IncreaseCnt(string $identName) {

        $varId = @IPS_GetObjectIDByIdent($identName, $this->rootId);
        if ($varId === false) {
            $varId = IPS_CreateVariable(1);     //0 - Boolean | 1-Integer | 2 - Float | 3 - String
            IPS_SetIdent($varId, $identName);
            IPS_SetParent($varId, $this->rootId);
            IPS_SetPosition($varId, 950);
            IPS_SetName($varId, $identName);
        }
        SetValueInteger($varId, GetValueInteger($varId) + 1);
    }


}


?>