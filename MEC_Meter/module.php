<?php

declare(strict_types=1);


require_once __DIR__ . '/../libs/COMMON.php';
require_once __DIR__ . '/MecMeter.php';


	class MECMeter extends IPSModule
	{

		use ENERGYMETER_COMMON;
		use MECMETER_FUNCTIONS;

		private $logLevel = 4;		// WARN = 3 | INFO = 4
		private $logCnt = 0;
		private $enableIPSLogOutput = false;


		public function __construct($InstanceID) {

			parent::__construct($InstanceID);		// Diese Zeile nicht löschen
	
			$this->logLevel = @$this->ReadPropertyInteger("LogLevel");
			if ($this->logLevel >= LogLevel::TRACE) {
				$this->AddLog(__FUNCTION__, sprintf("Log-Level is %d", $this->logLevel));
			}
		}


		public function Create()
		{
			//Never delete this line!
			parent::Create();

			$logMsg = sprintf("Create Modul '%s [%s]'...", IPS_GetName($this->InstanceID), $this->InstanceID);
			if ($this->logLevel >= LogLevel::INFO) {
				$this->AddLog(__FUNCTION__, $logMsg);
			}
			IPS_LogMessage(__CLASS__ . "_" . __FUNCTION__, $logMsg);
	
			$logMsg = sprintf("KernelRunlevel '%s'", IPS_GetKernelRunlevel());
			if ($this->logLevel >= LogLevel::INFO) {
				$this->AddLog(__FUNCTION__, $logMsg);
			}
			IPS_LogMessage(__CLASS__ . "_" . __FUNCTION__, $logMsg);

			
			$this->RegisterPropertyBoolean('EnableAutoUpdate', false);
			$this->RegisterPropertyInteger('AutoUpdateInterval', 15);	
			$this->RegisterPropertyString('MecMeter_IP', "10.0.11.160");
			$this->RegisterPropertyInteger('LogLevel', 3);
	
			$this->RegisterPropertyBoolean('cb_Data1', false);
			$this->RegisterPropertyBoolean('cb_Data2', false);
	
			$this->RegisterTimer('TimerUpdate_MECM', 0, 'MECM_TimerUpdate_MECM('.$this->InstanceID.');');
			$this->RegisterMessage(0, IPS_KERNELMESSAGE);


		}

		public function Destroy() {
			IPS_LogMessage(__CLASS__."_".__FUNCTION__, sprintf("Destroy Modul '%s' ...", $this->InstanceID));
			parent::Destroy();						//Never delete this line!
		}

		public function MessageSink($TimeStamp, $SenderID, $Message, $Data) {
			$logMsg = sprintf("TimeStamp: %s | SenderID: %s | Message: %d | Data: %s", $TimeStamp, $SenderID, $Message, json_encode($Data));
			if($this->logLevel >= LogLevel::INFO) { $this->AddLog(__FUNCTION__, $logMsg, 0, true); }
	
			if($Message == IPS_KERNELMESSAGE) {
				if ($Data[0] == KR_READY ) {
					//
				}
			}
		}

		public function ApplyChanges() {
			//Never delete this line!
			parent::ApplyChanges();

			$this->logLevel = $this->ReadPropertyInteger("LogLevel");
			if($this->logLevel >= LogLevel::INFO) { $this->AddLog(__FUNCTION__, sprintf("Set Log-Level to %d", $this->logLevel)); }
	
			$this->RegisterProfiles();
			$this->RegisterVariables();  
	
			$enableAutoUpdate = $this->ReadPropertyBoolean("EnableAutoUpdate");		
			if($enableAutoUpdate) {
				$updateInterval = $this->ReadPropertyInteger("AutoUpdateInterval");
			} else {
				$updateInterval = 0;
			}
			$this->SetUpdateInterval($updateInterval);
		}
	
	
		public function SetUpdateInterval(int $updateInterval) {
			if ($updateInterval <= 0) {  
				if($this->logLevel >= LogLevel::INFO) { $this->AddLog(__FUNCTION__, "Auto-Update stopped [TimerIntervall = 0]"); }	
			}else if ($updateInterval < 5) { 
				$updateInterval = 5; 
				if($this->logLevel >= LogLevel::INFO) { $this->AddLog(__FUNCTION__, sprintf("Set Auto-Update Timer Intervall to %ss", $updateInterval)); }	
			} else {
				if($this->logLevel >= LogLevel::INFO) { $this->AddLog(__FUNCTION__, sprintf("Set Auto-Update Timer Intervall to %ss", $updateInterval)); }
			}
			$this->SetTimerInterval("TimerUpdate_MECM", $updateInterval * 1000);	
		}
	
	
		public function TimerUpdate_MECM() {
			if ($this->logLevel >= LogLevel::INFO) {
				$this->AddLog(__FUNCTION__, "TimerUpdate_MECM called ...", 0 , true);
			}
			
			$result = $this->Update();

			if($result === false) {
			}

		}
	
		public function Update() {		
			$this->AddLog(__FUNCTION__, "xxXXxx");
		}


		public function ResetCounterVariables() {
			if($this->logLevel >= LogLevel::INFO) { $this->AddLog(__FUNCTION__, 'RESET Counter Variables', 0); }
			
			SetValue($this->GetIDForIdent("updateCntOk"), 0);
			SetValue($this->GetIDForIdent("updateCntError"), 0);
			SetValue($this->GetIDForIdent("updateLastError"), "");
			SetValue($this->GetIDForIdent("updateLastDuration"), 0); 
		}

		protected function RegisterProfiles() {

			if($this->logLevel >= LogLevel::DEBUG) { $this->AddLog(__FUNCTION__, "Variable Profiles registered"); }
		}

		protected function RegisterVariables() {

			$parentRootId = IPS_GetParent($this->InstanceID);

			$this->RegisterVariableInteger("updateCntOk", "Update Cnt OK", "", 900);
			$this->RegisterVariableInteger("updateCntError", "Update Cnt ERROR", "", 910);
			$this->RegisterVariableString("updateLastError", "Update Last Error", "", 920);
			$this->RegisterVariableInteger("lastProcessingTotalDuration", "Last API Request Duration [ms]", "", 930);	

			if($this->logLevel >= LogLevel::DEBUG) { $this->AddLog(__FUNCTION__, "Variables registered"); }
		}


		public function GetClassInfo() {
			return print_r($this, true);
		}


		protected function AddLog($name, $daten, $format=0, $ipsLogOutput=false) {
			$this->logCnt++;
			$logSender = "[".__CLASS__."] - " . $name;
			if($this->logLevel >= LogLevel::DEBUG) {
				$logSender = sprintf("%02d-T%2d [%s] - %s", $this->logCnt, $_IPS['THREAD'], __CLASS__, $name);
			} 
			$this->SendDebug($logSender, $daten, $format); 	
		
			if($ipsLogOutput or $this->enableIPSLogOutput) {
				if($format == 0) {
					IPS_LogMessage($logSender, $daten);	
				} else {
					IPS_LogMessage($logSender, $this->String2Hex($daten));			
				}
			}
		}

	}