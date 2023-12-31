<?php

declare(strict_types=1);

require_once __DIR__ . '/MeterFkt.php'; 
require_once __DIR__ . '/SunSpec.php'; 
require_once __DIR__ . '/../libs/COMMON.php'; 


class SunSpecMeterSimulator extends IPSModule {

	use SUN_SPEC;
	use METER_FUNCTIONS;
	use ENERGYMETER_COMMON;

	//const PROF_NAMES = ["FetchLogInForm", "submitEmailAddressForm", "submitPasswordForm", "fetchInitialAccessTokens", "fetchRefreshedAccessTokens", "FetchUserInfo", "FetchVehiclesAndEnrollmentStatus", "FetchVehicleData"];

	private $logLevel = LogLevel::WARN;
	private $logCnt = 0;
	private $enableIPSLogOutput = false;
	private $rootId;

	public function __construct($InstanceID) {
	
		parent::__construct($InstanceID);				// Diese Zeile nicht löschen
	
		$this->rootId = $InstanceID;

		$this->logLevel = @$this->ReadPropertyInteger("LogLevel"); 
		if($this->logLevel >= LogLevel::TRACE) { $this->AddLog(__FUNCTION__, sprintf("Log-Level is %d", $this->logLevel)); }
	}


	public function Create() {

		parent::Create();					//Never delete this line!

		$this->RequireParent('{8062CF2B-600E-41D6-AD4B-1BA66C32D6ED}');

		$logMsg = sprintf("Create Modul '%s [%s]'...", IPS_GetName($this->InstanceID), $this->InstanceID);
		if($this->logLevel >= LogLevel::INFO) { $this->AddLog(__FUNCTION__, $logMsg); }
		IPS_LogMessage(__CLASS__."_".__FUNCTION__, $logMsg);

		$logMsg = sprintf("KernelRunlevel '%s'", IPS_GetKernelRunlevel());
		if($this->logLevel >= LogLevel::DEBUG) { $this->AddLog(__FUNCTION__, $logMsg); }	

		$this->RegisterPropertyBoolean("EnableSSMS", 0);
		$this->RegisterPropertyInteger("LogLevel", LogLevel::INFO);	

		$this->RegisterPropertyString("MeterName", "Virtual Smart Meter IP");
		$this->RegisterPropertyString("MeterManufacturer", "IPS-Modul");
		$this->RegisterPropertyString("MeterDeviceModel", "Meter-Simulator");
		$this->RegisterPropertyString("MeterOptions", "SSMS 0.1");
		$this->RegisterPropertyString("MeterSoftware", "0.1-IPS");
		$this->RegisterPropertyString("MeterSerialnumber", "AABBCCDDEE11");

		$this->RegisterPropertyInteger("MeterModbusPort", 502);	
		$this->RegisterPropertyInteger("MeterModbusAddress", 107);	
		$this->RegisterPropertyInteger("MeterSunSpecModel", 213);	

		$this->RegisterPropertyInteger("selMeterDataSource", 0);

		$this->RegisterPropertyInteger("40072", 0);
		$this->RegisterPropertyInteger("40074", 0);
		$this->RegisterPropertyInteger("40076", 0);
		$this->RegisterPropertyInteger("40078", 0);
		$this->RegisterPropertyInteger("40080", 0);
		$this->RegisterPropertyInteger("40082", 0);
		$this->RegisterPropertyInteger("40084", 0);
		$this->RegisterPropertyInteger("40086", 0);
		$this->RegisterPropertyInteger("40088", 0);
		$this->RegisterPropertyInteger("40090", 0);
		$this->RegisterPropertyInteger("40092", 0);
		$this->RegisterPropertyInteger("40094", 0);
		$this->RegisterPropertyInteger("40096", 0);
		$this->RegisterPropertyInteger("40098", 0);
		$this->RegisterPropertyInteger("40100", 0);
		$this->RegisterPropertyInteger("40102", 0);
		$this->RegisterPropertyInteger("40104", 0);
		$this->RegisterPropertyInteger("40106", 0);
		$this->RegisterPropertyInteger("40108", 0);
		$this->RegisterPropertyInteger("40110", 0);
		$this->RegisterPropertyInteger("40112", 0);
		$this->RegisterPropertyInteger("40114", 0);
		$this->RegisterPropertyInteger("40116", 0);
		$this->RegisterPropertyInteger("40118", 0);
		$this->RegisterPropertyInteger("40120", 0);
		$this->RegisterPropertyInteger("40122", 0);
		$this->RegisterPropertyInteger("40124", 0);
		$this->RegisterPropertyInteger("40126", 0);
		$this->RegisterPropertyInteger("40128", 0);
		$this->RegisterPropertyInteger("40130", 0);
		$this->RegisterPropertyInteger("40132", 0);
		$this->RegisterPropertyInteger("40134", 0);
		$this->RegisterPropertyInteger("40136", 0);
		$this->RegisterPropertyInteger("40138", 0);
		$this->RegisterPropertyInteger("40140", 0);
		$this->RegisterPropertyInteger("40142", 0);
		$this->RegisterPropertyInteger("40144", 0);
		$this->RegisterPropertyInteger("40146", 0);
		$this->RegisterPropertyInteger("40148", 0);
		$this->RegisterPropertyInteger("40150", 0);
		$this->RegisterPropertyInteger("40152", 0);
		$this->RegisterPropertyInteger("40154", 0);
		$this->RegisterPropertyInteger("40156", 0);
		$this->RegisterPropertyInteger("40158", 0);
		$this->RegisterPropertyInteger("40160", 0);

		
		$this->RegisterMessage(0, IPS_KERNELMESSAGE);
		$this->RegisterMessage(0, IPS_KERNELSTARTED);
		$this->RegisterMessage(0, IPS_KERNELSHUTDOWN);

		$this->RegisterMessage($this->InstanceID, ML_LOAD);
		$this->RegisterMessage($this->InstanceID, ML_UNLOAD);

		$this->RegisterMessage($this->InstanceID, IM_CREATE);
		$this->RegisterMessage($this->InstanceID, IM_DELETE);
		$this->RegisterMessage($this->InstanceID, IM_CONNECT);
		$this->RegisterMessage($this->InstanceID, IM_DISCONNECT);
		$this->RegisterMessage($this->InstanceID, IM_CHANGESTATUS);
		$this->RegisterMessage($this->InstanceID, IM_CHANGESETTINGS);
		$this->RegisterMessage($this->InstanceID, IM_CHANGEATTRIBUTE);
		$this->RegisterMessage($this->InstanceID, IM_ADDATTRIBUTE);
		$this->RegisterMessage($this->InstanceID, IM_REMOVEATTRIBUTE);

		$this->RegisterMessage($this->InstanceID, FM_CONNECT);
		$this->RegisterMessage($this->InstanceID, FM_DISCONNECT);

	}

	public function Destroy() {
		IPS_LogMessage(__CLASS__."_".__FUNCTION__, sprintf("Destroy Modul '%s' ...", $this->InstanceID));
		parent::Destroy();						//Never delete this line!
	}

	public function ApplyChanges() {

		parent::ApplyChanges();					//Never delete this line!

		$this->logLevel = $this->ReadPropertyInteger("LogLevel");
		if($this->logLevel >= LogLevel::INFO) { $this->AddLog(__FUNCTION__, sprintf("Set Log-Level to %d", $this->logLevel)); }
		
		$this->RegisterProfiles();
		$this->RegisterVariables();  

		$meterModbusPort = $this->ReadPropertyInteger("MeterModbusPort");	
		$meterModbusAddress = $this->ReadPropertyInteger("MeterModbusAddress");	
		$meterSunSpecModel = $this->ReadPropertyInteger("MeterSunSpecModel");	

		$this->GetConnectionState();
	}

	public function MessageSink($TimeStamp, $SenderID, $Message, $Data)	{
		$logMsg = sprintf("TimeStamp: %s | SenderID: %s | Message: %s | Data: %s", $TimeStamp, $SenderID, $Message, json_encode($Data));
		if($this->logLevel >= LogLevel::DEBUG) { $this->AddLog(__FUNCTION__, $logMsg); }
		//IPS_LogMessage(__CLASS__."_".__FUNCTION__, $logMsg);
	}


	public function GetConnectionState() {
		$connectionState = -1;
		$conID = IPS_GetInstance($this->InstanceID)['ConnectionID'];
		if($conID > 0) {
			$connectionState = IPS_GetInstance($conID)['InstanceStatus'];
			if($this->logLevel >= LogLevel::INFO) { $this->AddLog(__FUNCTION__, sprintf("Instanz '%s [%s]' has I/O Gateway '%s [%s]' in State %s", $this->InstanceID, IPS_GetName($this->InstanceID), $conID, IPS_GetName($conID),  $connectionState)); }
		} else {
			$connectionState = 0;
			if($this->logLevel >= LogLevel::WARN) { $this->AddLog(__FUNCTION__, sprintf("Instanz '%s [%s]' has NO Gateway/Connection [ConnectionID=%s]", $this->InstanceID, IPS_GetName($this->InstanceID), $conID)); }
		}
		return $connectionState;
	}


	public function ResetCounterVariables(string $caller='?') {
		if($this->logLevel >= LogLevel::INFO) { $this->AddLog(__FUNCTION__, sprintf("RESET Counter Variables [Trigger > %s] ...", $caller)); }
		SetValue($this->GetIDForIdent("modbusReceiveCnt"), 0); 
		SetValue($this->GetIDForIdent("modbusReceiveLast"), 0); 	
		SetValue($this->GetIDForIdent("modbusTransmitCnt"), 0); 

		$childrenIds = IPS_GetChildrenIDs($this->rootId);
		foreach($childrenIds as $childId) {
			$objIdent = IPS_GetObject($childId)["ObjectIdent"];
			
			//if(str_starts_with($objIdent, "Cnt_")) {
			//	SetValueInteger($childId, 0);
			//}

			if($this->startsWith($objIdent, "Cnt_")) {
				SetValueInteger($childId, 0);
			}

		}
	}


	public function Send(string $Text, string $ClientIP, int $ClientPort) {
		SetValue($this->GetIDForIdent("modbusTransmitCnt"), GetValue($this->GetIDForIdent("modbusTransmitCnt")) + 1); 

		$Text = utf8_encode($Text);
		if($this->logLevel >= LogLevel::COMMUNICATION) { $this->AddLog(__FUNCTION__, sprintf("Transmit '%s' to %s:%s", $this->String2Hex($Text), $ClientIP, $ClientPort)); }
		//$this->SendDataToParent(json_encode(['DataID' => '{C8792760-65CF-4C53-B5C7-A30FCC84FEFE}', "ClientIP" => $ClientIP, "ClientPort" => $ClientPort, "Buffer" => $Text]));
		$this->SendDataToParent(json_encode(['DataID' => '{C8792760-65CF-4C53-B5C7-A30FCC84FEFE}', 'Buffer' => $Text, 'Type' => 0, 'ClientIP' => $ClientIP, 'ClientPort' => $ClientPort ]));
	}

	public function ReceiveData($JSONString) {

		if($this->ReadPropertyBoolean("EnableSSMS")) {

			SetValue($this->GetIDForIdent("modbusReceiveCnt"), GetValue($this->GetIDForIdent("modbusReceiveCnt")) + 1); 
			SetValue($this->GetIDForIdent("modbusReceiveLast"), time()); 

			$data = json_decode($JSONString);
			$dataReceived = utf8_decode($data->Buffer);
			//$dataReceived = $data->Buffer;
			$dataClientIP = $data->ClientIP;
			$dataClientPort = $data->ClientPort;

			if($this->logLevel >= LogLevel::COMMUNICATION) { $this->AddLog(__FUNCTION__, sprintf("Received '%s' from %s:%s", $this->String2Hex($dataReceived), $dataClientIP, $dataClientPort)); }

			$responseData = $this->ProcessData($dataReceived);
			if($responseData != "") {
				$this->Send($responseData, $dataClientIP, $dataClientPort);
			} else {
				if($this->logLevel >= LogLevel::TRACE) { $this->AddLog(__FUNCTION__, sprintf("ResponseData empty > transmit nothing to Client '%s:%s'", $dataClientIP, $dataClientPort)); } 										
			}

		} else {
			if($this->logLevel >= LogLevel::COMMUNICATION) { $this->AddLog(__FUNCTION__, "SunSpec Smart Meter Simulator DISABLED"); }
		}

	}


	public function GetClassInfo() {
		return print_r($this, true);
	}


	protected function RegisterProfiles() {

		/*
		if ( !IPS_VariableProfileExists('EV.level') ) {
			IPS_CreateVariableProfile('EV.level', VARIABLE::TYPE_INTEGER );
			IPS_SetVariableProfileDigits('EV.level', 0 );
			IPS_SetVariableProfileText('EV.level', "", " %" );
			IPS_SetVariableProfileValues('EV.level', 0, 100, 1);
		} */

		if($this->logLevel >= LogLevel::TRACE) { $this->AddLog(__FUNCTION__, "Profiles registered"); }
	}

	protected function RegisterVariables() {
		
		$this->RegisterVariableInteger("modbusReceiveCnt", "Modbus Receive Cnt", "", 900);
		$this->RegisterVariableInteger("modbusReceiveLast", "Modbus Last Receive", "~UnixTimestamp", 901);
		$this->RegisterVariableInteger("modbusTransmitCnt", "Modbus Transmit Cnt", "", 910);

		$archivInstanzID = IPS_GetInstanceListByModuleID("{43192F0B-135B-4CE7-A0A7-1475603F3060}")[0];
		IPS_ApplyChanges($archivInstanzID);
		if($this->logLevel >= LogLevel::TRACE) { $this->AddLog(__FUNCTION__, "Variables registered"); }

	}


	public function GetConfigurationForm() {
		// wird beim Laden der Instanz-Konfig-Seite aufgerufen
		// Form.json auslesen und CheckBox für Zustand der Instanz setzen
		$Form = json_decode(file_get_contents(__DIR__ . '/form.json'), true);
		if($this->ReadPropertyInteger("selMeterDataSource") == 1) {
			$Form['elements']["5"]['visible'] = true; 
			$Form['elements']["5"]['expanded'] = true;
		} else {
			$Form['elements']["5"]['visible'] = false;
			$Form['elements']["5"]['expanded'] = false;
		}
		return json_encode($Form);
	}

	public function OnChange_selMeterDataSource(string $caller='?') {

		if($this->ReadPropertyInteger("selMeterDataSource") == 1) {
			$this->UpdateFormField("selMeterDataSource", "visible", true);
			if($this->logLevel >= LogLevel::TRACE) { $this->AddLog(__FUNCTION__, "xxx_true"); }
		} else {
			$this->UpdateFormField("selMeterDataSource", "visible", false);
			if($this->logLevel >= LogLevel::TRACE) { $this->AddLog(__FUNCTION__, "xxxx_false"); }
		}

		//Line from form.json { "name": "cbLinkToVariables", "type": "CheckBox", "caption": "Use linked Variables", "enabled": true, "value": false, "onChange": "SSMS_OnChange_cbLinkToVariables($id, 'ModulForm');" },
	}


	protected function startsWith(string $haystack, string $needle) {
		return strpos($haystack, $needle) === 0;
	}

	protected function AddLog($name, $daten, $format=0) {
		$this->logCnt++;
		$logSender = "[".__CLASS__."] - " . $name;
		if($this->logLevel >= LogLevel::DEBUG) {
			$logSender = sprintf("%02d-T%2d [%s] - %s", $this->logCnt, $_IPS['THREAD'], __CLASS__, $name);
		} 
		$this->SendDebug($logSender, $daten, $format); 	
	
		if($this->enableIPSLogOutput) {
			if($format == 0) {
				IPS_LogMessage($logSender, $daten);	
			} else {
				IPS_LogMessage($logSender, $this->String2Hex($daten));			
			}
		}
	}

}