<?php 

trait METER_FUNCTIONS {

    static $my= 'xxXXxx';

    private $myVariable; 


    private function GetRegister(): string {

        return "abc";
    }


    private function ProcessData(string $receivedData): string {
        
        $responceData = "";

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
                if($this->logLevel >= LogLevel::DEBUG) { $this->AddLog(__FUNCTION__, sprintf("Modbus Adress does not match [%d <> %d]", $modbusAdress, $this->ReadPropertyInteger("MeterModbusAddress")), 0); }
            } else {
   
                if($this->logLevel >= LogLevel::TRACE) { $this->AddLog(__FUNCTION__, sprintf("ReceivedData Len:  %d", $len), 0); }
                if($this->logLevel >= LogLevel::TRACE) { $this->AddLog(__FUNCTION__, sprintf("ModbusTCP_Part1_TIPI: %s", $this->String2Hex($modbusTCP_Part1_TIPI)), 0); }
                if($this->logLevel >= LogLevel::TRACE) { $this->AddLog(__FUNCTION__, sprintf("ModbusTCP_Part2_Len: %s", $this->String2Hex($modbusTCP_Part2_Len)), 0); }
                if($this->logLevel >= LogLevel::TRACE) { $this->AddLog(__FUNCTION__, sprintf("ModbusTCP_Part3_UI: %s", $this->String2Hex($modbusTCP_Part3_UI)), 0); }
                if($this->logLevel >= LogLevel::TRACE) { $this->AddLog(__FUNCTION__, sprintf("Part_modbusData: %s", $this->String2Hex($modbusData)), 0); }

                switch($modbusData) {
        
                    case "\x03\x9C\x88\x00\x3A":                                                                                //  40072 :: Word Count 58    
                        //$recordedResponse = "\x03\x74\x3c\x13\x74\xbc\x3f\x21\xd3\x75\x3d\xdc\xbc\x6a\x3e\x3f\xe3\x54\x43\x65\x23\x6d\x43\x64\xd7\xbd\x43\x65\x4a\xb3\x43\x65\x47\xd7\x43\xc6\x70\x83\x43\xc6\x93\xe4\x43\xc6\x77\xb2\x43\xc6\x45\xf3\x42\x48\x0a\x3d\x41\xfe\x20\x00\x41\x95\x50\x00\x41\x15\x10\x00\x40\x72\x80\x00\x43\x54\x68\x00\x43\x10\xb6\x00\x41\xc5\xc8\x00\x42\x2b\xec\x00\xc3\x47\x78\x00\xc3\x0a\x6b\x00\xc1\x9e\x30\x00\xc2\x25\x28\x00\x3e\x18\x93\x75\x3e\x04\x18\x93\x3e\xc1\x06\x25\x3d\xb6\x45\xa2";
                        $responceData = $modbusTCP_Part1_TIPI . "\x00\x77". $modbusTCP_Part3_UI;
                        $responceData .= "\x03\x74";                                                                            // Function Code: 3 |  Byte Count: 116 (0x74)  
                        $dataArr = $this->GetRegisterData40072_40129();                                                        // Register Values 40072 bis 40129 (A, V, Hz, W, VA, VAr, cos) 
                        $responceData .= pack("G*", ...$dataArr);                                                               // Pack as float32
                        $this->IncreaseCnt("Cnt_40072");
                        break;
        
                    case "\x03\x9C\xC2\x00\x20":                                                                                //  40130 : Word Count 32       
                        //$recordedResponse = "\x03\x40\x48\xc2\x85\x2b\x48\x2e\x94\xa4\x47\xea\x33\x54\x47\xd5\x11\xcc\x4a\x0f\x02\x8c\x49\x32\x18\xce\x48\x5b\x9f\xfe\x49\x9e\x72\x1a\x48\xdf\xa2\xff\x48\x3c\x5e\xfa\x47\xf0\xe1\xec\x47\xf5\x02\x84\x4a\xb6\xf2\x2a\x49\xd7\x62\xa7\x49\x19\xbd\x98\x4a\x5b\xc3\x8d";
                        $responceData = $modbusTCP_Part1_TIPI . "\x00\x43". $modbusTCP_Part3_UI;                                    
                        $responceData .= "\x03\x40";                                                                            // Function Code: 3 |  Byte Count: 64 (0x40)  
                        $dataArr = $this->GetRegisterData40130_40161();                                                         // Register Values 40130 bis 40161 (Energy Data Wh, VAh)
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
                            if($this->logLevel >= LogLevel::INFO) { $this->AddLog(__FUNCTION__, sprintf("Simulate 'single phase' meter [meter model: %d]", $meterSunSpecModel), 0); }
                        } else if ($meterSunSpecModel == 212) {
                            $responceData .= "\x00\xd4";                                                                        // Register 40070=212: split phase
                            if($this->logLevel >= LogLevel::INFO) { $this->AddLog(__FUNCTION__, sprintf("Simulate 'split phase' meter [meter model: %d]", $meterSunSpecModel), 0); }
                        } else {
                            $responceData .= "\x00\xd5";                                                                        // Register 40070=213: three phase
                            if($this->logLevel >= LogLevel::INFO) { $this->AddLog(__FUNCTION__, sprintf("Simulate 'three phase' meter [meter model: %d]", $meterSunSpecModel), 0); }
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
                        if($this->logLevel >= LogLevel::WARN) { $this->AddLog(__FUNCTION__,  sprintf("WARN: untreated Modbus Request [%s]", $this->String2Hex($receivedData)), 0); }
                        break;
                }
            }
        }
        return $responceData;
    }



    function GetRegisterData40072_40129():array {
        $registerDataArr = [];
        $registerDataArr[] = 0.6;               // 40072 - AC Total Current value                       [A | A]
        $registerDataArr[] = 0.1;               // 40074 - AC Phase-A Current value                     [AphA | A]
        $registerDataArr[] = 0.2;               // 40076 - AC Phase-B Current value                     [AphB | A]
        $registerDataArr[] = 0.3;               // 40078 - AC Phase-C Current value                     [AphB | A]
        $registerDataArr[] = 230.0;             // 40080 - AC Voltage Average Phase-to-neutral value    [PhV | V]
        $registerDataArr[] = 230.1;             // 40082 - AC Voltage Phase-A-to-neutral value          [PhVphA | V]
        $registerDataArr[] = 230.2;             // 40084 - AC Voltage Phase-B-to-neutral value          [PhVphB | V]
        $registerDataArr[] = 230.3;             // 40086 - AC Voltage Phase-C-to-neutral value          [PhVphC | V]
        $registerDataArr[] = 400.0;             // 40088 - AC Voltage Average Phase-to-phase value      [PPV | V]
        $registerDataArr[] = 400.1;             // 40090 - AC Voltage Phase-AB value                    [PPVphAB | V]
        $registerDataArr[] = 400.2;             // 40092 - AC Voltage Phase-BC value                    [PPVphBC | V]
        $registerDataArr[] = 400.3;             // 40094 - AC Voltage Phase-CA value                    [PPVphCA | V]
        $registerDataArr[] = $this->GetMeterValue("40096_Hz");              // 40096 - AC Frequency value                           [Hz | Hz]
        $registerDataArr[] = $this->GetMeterValue("40098_W");               // 40098 - AC Power value                               [W | W]  { intval(date('i')) }
        $registerDataArr[] = $this->GetMeterValue("40100_WphA");            // 40100 - AC Power Phase A value                       [WphA | W]
        $registerDataArr[] = $this->GetMeterValue("40102_WphB");            // 40102 - AC Power Phase B value                       [WphB | W]
        $registerDataArr[] = $this->GetMeterValue("40104_WphC");            // 40104 - AC Power Phase C value                       [WphC | W]
        $registerDataArr[] = 10.0;              // 40106 - AC Apparent Power value                      [VA | VA]
        $registerDataArr[] = 10.1;              // 40108 - AC Apparent Power Phase A value              [VAphA | VA]
        $registerDataArr[] = 10.2;              // 40110 - AC Apparent Power Phase B value              [VAphB | VA] 
        $registerDataArr[] = 10.3;              // 40112 - AC Apparent Power Phase C value              [VAphC | VA]
        $registerDataArr[] = -11.0;             // 40114 - AC Reactive Power value                      [VAR | VAr]
        $registerDataArr[] = -11.1;             // 40116 - AC Reactive Power Phase A value              [VARphA | VAr]
        $registerDataArr[] = -11.2;             // 40118 - AC Reactive Power Phase B value              [VARphB | VAr]
        $registerDataArr[] = -11.3;             // 40120 - AC Reactive Power Phase C value              [VARphC | VAr]
        $registerDataArr[] = 0.123;             // 40122 - Power Factor value                           [PF | cos()]
        $registerDataArr[] = 0.101;             // 40124 - Power Factor Phase A value                   [PFphA | cos()]
        $registerDataArr[] = 0.102;             // 40126 - Power Factor Phase B value                   [PFphB | cos()]
        $registerDataArr[] = 0.103;             // 40128 - Power Factor Phase C value                   [PFphC | cos()]
        if($this->logLevel >= LogLevel::DEBUG) { $this->AddLog(__FUNCTION__,  "RegisterData 40072 - 40129 created", 0); }
        return $registerDataArr;  
    }
    
    function GetRegisterData40130_40161():array {
        $registerDataArr = [];
        $registerDataArr[] = 30.6;               // 40130 - Total Watt-hours Exported                   [TotWhExp | Wh]
        $registerDataArr[] = 10.1;               // 40132 - Total Watt-hours Exported phase A           [TotWhExpPhA | Wh]
        $registerDataArr[] = 10.2;               // 40134 - Total Watt-hours Exported phase B           [TotWhExpPhB | Wh]
        $registerDataArr[] = 10.3;               // 40136 - Total Watt-hours Exported phase C           [TotWhExpPhC | Wh]
        $registerDataArr[] = 33.6;               // 40138 - Total Watt-hours Imported                   [TotWhImpPh | Wh]
        $registerDataArr[] = 11.1;               // 40140 - Total Watt-hours Imported phase A           [TotWhImpPhA | Wh]
        $registerDataArr[] = 11.2;               // 40142 - Total Watt-hours Imported phase B           [TotWhImpPhA | Wh]
        $registerDataArr[] = 11.3;               // 40144 - Total Watt-hours Imported phase C           [TotWhImpPhA | Wh]
        $registerDataArr[] = 36.6;               // 40146 - Total VA-hours Exported                     [TotVAhExp | VAh]
        $registerDataArr[] = 12.1;               // 40148 - Total VA-hours Exported phase A             [TotVAhExpPhA | VAh]
        $registerDataArr[] = 12.2;               // 40150 - Total VA-hours Exported phase B             [TotVAhExpPhB | VAh]
        $registerDataArr[] = 12.3;               // 40152 - Total VA-hours Exported phase C             [TotVAhExpPhC | VAh]
        $registerDataArr[] = 39.6;               // 40154 - Total VA-hours Imported                     [TotVAhImp | VAh]
        $registerDataArr[] = 13.1;               // 40156 - Total VA-hours Imported phase A             [TotVAhImpPhA | VAh]
        $registerDataArr[] = 13.2;               // 40158 - Total VA-hours Imported phase B             [TotVAhImpPhB | VAh]
        $registerDataArr[] = 13.3;               // 40160 - Total VA-hours Imported phase C             [TotVAhImpPhC | VAh]    
        if($this->logLevel >= LogLevel::DEBUG) { $this->AddLog(__FUNCTION__,  "RegisterData 40130 - 40161 created", 0); }                                 
        return $registerDataArr;      
    }
    

    private function GetMeterValue(string $propertyName):float {
        $meterValue = 0;
        $varIdConfigured = @$this->ReadPropertyInteger($propertyName);
        if($varIdConfigured > 1) {
            $meterValue = GetValue($varIdConfigured);
        }
        if($this->logLevel >= LogLevel::DEBUG) { $this->AddLog(__FUNCTION__,  sprintf(" %s = %f", $propertyName, $meterValue), 0); }  
        return $meterValue;
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