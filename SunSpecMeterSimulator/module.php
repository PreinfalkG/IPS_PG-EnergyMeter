<?php

declare(strict_types=1);

require_once __DIR__ . '/MeterFkt.php'; 
require_once __DIR__ . '/../libs/COMMON.php'; 


	class SunSpecMeterSimulator extends IPSModule
	{

		use METER_FUNCTIONS;
		use ENERGYMETER_COMMON;

		//const PROF_NAMES = ["FetchLogInForm", "submitEmailAddressForm", "submitPasswordForm", "fetchInitialAccessTokens", "fetchRefreshedAccessTokens", "FetchUserInfo", "FetchVehiclesAndEnrollmentStatus", "FetchVehicleData"];

		private $logLevel = 3;
		private $enableIPSLogOutput = false;
		private $rootId;
		private $parentRootId;
		private $archivInstanzID;


		public function __construct($InstanceID) {
		
			parent::__construct($InstanceID);		// Diese Zeile nicht löschen
		
			if(IPS_InstanceExists($InstanceID)) {

				$this->rootId = $InstanceID;
				$this->parentRootId = IPS_GetParent($InstanceID);
				$this->archivInstanzID = IPS_GetInstanceListByModuleID("{43192F0B-135B-4CE7-A0A7-1475603F3060}")[0];

				$currentStatus = $this->GetStatus();
				if($currentStatus == 102) {				//Instanz ist aktiv
					$this->logLevel = $this->ReadPropertyInteger("LogLevel");
					if($this->logLevel >= LogLevel::TRACE) { $this->AddLog(__FUNCTION__, sprintf("Log-Level is %d", $this->logLevel), 0); }
				} else {
					if($this->logLevel >= LogLevel::WARN) { $this->AddLog(__FUNCTION__, sprintf("Current Status is '%s'", $currentStatus), 0); }	
				}

			} else {
				IPS_LogMessage("[" . __CLASS__ . "] - " . __FUNCTION__, sprintf("INFO: Instance '%s' not exists", $InstanceID));
			}
		}


		public function Create() {

			parent::Create();				//Never delete this line!

			$this->RequireParent('{8062CF2B-600E-41D6-AD4B-1BA66C32D6ED}');

			IPS_LogMessage("[" . __CLASS__ . "] - " . __FUNCTION__, sprintf("Create Modul '%s' ...", $this->InstanceID));
			if($this->logLevel >= LogLevel::INFO) { $this->AddLog(__FUNCTION__, sprintf("Create Modul '%s [%s']...", IPS_GetName($this->InstanceID), $this->InstanceID), 0); }


			$this->RegisterPropertyBoolean("EnableSSMS", 0);
			$this->RegisterPropertyInteger("LogLevel", 9);
			
			//$this->RegisterPropertyString("MeterName", "Virtual Smart Meter IP");
			//$this->RegisterPropertyString("MeterManufacturer", "VERBUND");
			//$this->RegisterPropertyString("MeterDeviceModel", "Power-Meter");
			//$this->RegisterPropertyString("MeterOptions", "EUMEL 1.0");
			//$this->RegisterPropertyString("MeterSoftware", "2.3.0-Flex");
			//$this->RegisterPropertyString("MeterSerialnumber", "A8404118ABCD");

			$this->RegisterPropertyString("MeterName", "Virtual Smart Meter IP");
			$this->RegisterPropertyString("MeterManufacturer", "IPS_PG");
			$this->RegisterPropertyString("MeterDeviceModel", "Meter-Simulator");
			$this->RegisterPropertyString("MeterOptions", "SSMS 0.1");
			$this->RegisterPropertyString("MeterSoftware", "0.1-IPS");
			$this->RegisterPropertyString("MeterSerialnumber", "AABBCCDDEE11");

			$this->RegisterPropertyInteger("MeterModbusPort", 502);	
			$this->RegisterPropertyInteger("MeterModbusAddress", 107);	
			$this->RegisterPropertyInteger("MeterSunSpecModel", 213);	


			$this->RegisterPropertyInteger('40098_W', 0);
			$this->RegisterPropertyInteger('40100_WphA', 0);
			$this->RegisterPropertyInteger('40102_WphB', 0);
			$this->RegisterPropertyInteger('40104_WphC', 0);


			//Register Attributes for simple profiling
			// ToDo

			$runlevel = IPS_GetKernelRunlevel();
			if($this->logLevel >= LogLevel::TRACE) { $this->AddLog(__FUNCTION__, sprintf("KernelRunlevel '%s'", $runlevel), 0); }	
			if ( $runlevel == KR_READY ) {
				//$this->RegisterHook(self::WEB_HOOK);
			} else {
				$this->RegisterMessage(0, IPS_KERNELMESSAGE);
			}


		}

		public function Destroy() {
			
			IPS_LogMessage("[" . __CLASS__ . "] - " . __FUNCTION__, sprintf("Destroy Modul '%s' ...", $this->InstanceID));
			parent::Destroy();						//Never delete this line!
		}

		public function ApplyChanges() {

			parent::ApplyChanges();					//Never delete this line!

			$this->logLevel = $this->ReadPropertyInteger("LogLevel");
			if($this->logLevel >= LogLevel::INFO) { $this->AddLog(__FUNCTION__, sprintf("Set Log-Level to %d", $this->logLevel), 0); }
			
			if (IPS_GetKernelRunlevel() != KR_READY) {
				if($this->logLevel >= LogLevel::INFO) { $this->AddLog(__FUNCTION__, sprintf("GetKernelRunlevel is '%s'", IPS_GetKernelRunlevel()), 0); }
				//return;
			}

			$this->RegisterProfiles();
			$this->RegisterVariables();  

			$meterModbusPort = $this->ReadPropertyInteger("MeterModbusPort");	
			$meterModbusAddress = $this->ReadPropertyInteger("MeterModbusAddress");	
			$meterSunSpecModel = $this->ReadPropertyInteger("MeterSunSpecModel");	


			//IPS_SETPROPERTY


			$connectionState = -1;
			$conID = IPS_GetInstance($this->InstanceID)['ConnectionID'];
			if($conID > 0) {
				$connectionState = IPS_GetInstance($conID)['InstanceStatus'];
				//$instanzArr = IPS_GetInstance($conID);
				//$this->AddLog(__FUNCTION__, print_r($instanzArr, true), 0);
				if($this->logLevel >= LogLevel::INFO) { $this->AddLog(__FUNCTION__, sprintf("Instanz '%s [%s]' has I/O Gateway '%s [%s]' in State %s", $this->InstanceID, IPS_GetName($this->InstanceID), $conID, IPS_GetName($conID),  $connectionState), 0); }

			} else {
				$connectionState = 0;
				if($this->logLevel >= LogLevel::WARN) { $this->AddLog(__FUNCTION__, sprintf("Instanz '%s [%s]' has NO Gateway/Connection [ConnectionID=%s]", $this->InstanceID, IPS_GetName($this->InstanceID), $conID), 0); }
			}


		}

		public function MessageSink($TimeStamp, $SenderID, $Message, $Data)	{

			$logMsg = sprintf("TimeStamp: %s | SenderID: %s | Message: %s | Data: %s", $TimeStamp, $SenderID, $Message, print_r($Data,true));
			IPS_LogMessage("[" . __CLASS__ . "] - " . __FUNCTION__, $logMsg);
			if($this->logLevel >= LogLevel::TRACE) { $this->AddLog(__FUNCTION__, $logMsg, 0); }

			parent::MessageSink($TimeStamp, $SenderID, $Message, $Data);
			//if ($Message == IPS_KERNELMESSAGE && $Data[0] == KR_READY) 	{
			//		....
			//}
		}


		public function GetConnectionState() {
			$connectionState = -1;
			$conID = IPS_GetInstance($this->InstanceID)['ConnectionID'];
			if($conID > 0) {
				$connectionState = IPS_GetInstance($conID)['InstanceStatus'];
			} else {
				$connectionState = 0;
				if($this->logLevel >= LogLevel::WARN) { $this->AddLog(__FUNCTION__, sprintf("Instanz '%s [%s]' has NO Gateway/Connection [ConnectionID=%s]", $this->InstanceID, IPS_GetName($this->InstanceID), $conID), 0); }
			}
			SetValue($this->GetIDForIdent("connectionState"), $connectionState);
			return $connectionState;
		}


		public function ResetCounterVariables(string $caller='?') {
			if($this->logLevel >= LogLevel::INFO) { $this->AddLog(__FUNCTION__, sprintf("RESET Counter Variables [Trigger > %s] ...", $caller), 0); }
			SetValue($this->GetIDForIdent("modbusReceiveCnt"), 0); 
			SetValue($this->GetIDForIdent("modbusReceiveLast"), 0); 	
			SetValue($this->GetIDForIdent("modbusTransmitCnt"), 0); 

			$childrenIds = IPS_GetChildrenIDs($this->rootId);
			foreach($childrenIds as $childId) {
				$objIdent = IPS_GetObject($childId)["ObjectIdent"];
				if(str_starts_with($objIdent, "Cnt_")) {
					SetValueInteger($childId, 0);
				}

			}
		}



		public function Send(string $Text, string $ClientIP, int $ClientPort) {
			SetValue($this->GetIDForIdent("modbusTransmitCnt"), GetValue($this->GetIDForIdent("modbusTransmitCnt")) + 1); 

			$Text = utf8_encode($Text);
			if($this->logLevel >= LogLevel::COMMUNICATION) { $this->AddLog(__FUNCTION__, sprintf("Transmit '%s' to %s:%s", $this->String2Hex($Text), $ClientIP, $ClientPort), 0); }
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

				if($this->logLevel >= LogLevel::COMMUNICATION) { $this->AddLog(__FUNCTION__, sprintf("Received '%s' from %s:%s", $this->String2Hex($dataReceived), $dataClientIP, $dataClientPort), 0); }

				$responseData = $this->ProcessData($dataReceived);
				if($responseData != "") {
					$this->Send($responseData, $dataClientIP, $dataClientPort);
				} else {
					if($this->logLevel >= LogLevel::TRACE) { $this->AddLog(__FUNCTION__, sprintf("ResponseData empty > transmit nothing to Client '%s:%s'", $dataClientIP, $dataClientPort), 0); } 										
				}

			} else {
				if($this->logLevel >= LogLevel::COMMUNICATION) { $this->AddLog(__FUNCTION__, "SunSpec Smart Meter Simulator DISABLED", 0); }
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

			if($this->logLevel >= LogLevel::TRACE) { $this->AddLog(__FUNCTION__, "Profiles registered", 0); }
		}

		protected function RegisterVariables() {
			
			$this->RegisterVariableInteger("modbusReceiveCnt", "Modbus Receive Cnt", "", 900);
			$this->RegisterVariableInteger("modbusReceiveLast", "Modbus Last Receive", "~UnixTimestamp", 901);
			$this->RegisterVariableInteger("modbusTransmitCnt", "Modbus Transmit Cnt", "", 910);

			IPS_ApplyChanges($this->archivInstanzID);
			if($this->logLevel >= LogLevel::TRACE) { $this->AddLog(__FUNCTION__, "Variables registered", 0); }

		}


		protected function AddLog($name, $daten, $format) {
			$this->SendDebug("[" . __CLASS__ . "] - " . $name, $daten, $format); 	
	
			if($this->enableIPSLogOutput) {
				if($format == 0) {
					IPS_LogMessage("[" . __CLASS__ . "] - " . $name, $daten);	
				} else {
					IPS_LogMessage("[" . __CLASS__ . "] - " . $name, $this->String2Hex($daten));			
				}
			}
		}



	}