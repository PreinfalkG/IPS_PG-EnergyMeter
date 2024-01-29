<?php

declare(strict_types=1);


require_once __DIR__ . '/../libs/COMMON.php';
require_once __DIR__ . '/MecMeter.php';
require_once __DIR__ . '/MecMeterHelper.php';


class MECMeter extends IPSModule {

	use ENERGYMETER_COMMON;
	use MECMETER_FUNCTIONS;
	use MECMETER_HELPER;

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


	public function Create() {
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
		$this->RegisterPropertyString('MecMeter_IP', "10.0.11.122");
		$this->RegisterPropertyString('MecMeter_User', "admin");
		$this->RegisterPropertyString('MecMeter_PW', "");
		$this->RegisterPropertyInteger('LogLevel', 3);

		$this->RegisterPropertyBoolean('cb_Basic', false);
		$this->RegisterPropertyBoolean('cb_Vx', true);				// AC-Spannung
		$this->RegisterPropertyBoolean('cb_Ix', false);				// AC-Strom
		$this->RegisterPropertyBoolean('cb_DcI', false);			// DC-Strom
		$this->RegisterPropertyBoolean('cb_Px', true);				// Wirkleistung
		$this->RegisterPropertyBoolean('cb_EFAx', false);			// Wirkenergie Bezug
		$this->RegisterPropertyBoolean('cb_ERAx', false);			// Wirkenergie Einspeisung
		$this->RegisterPropertyBoolean('cb_Qx', false);				// Blindleistung Bezug
		$this->RegisterPropertyBoolean('cb_Sx', false);				// Scheinleistung Bezug
		$this->RegisterPropertyBoolean('cb_EFRx', false);			// Blindenergie Bezug
		$this->RegisterPropertyBoolean('cb_ESx', false);			// Scheinenergie Bezug

		$this->RegisterPropertyBoolean('cb_ERRx', false);			// Blindenergie Einspeisung
		$this->RegisterPropertyBoolean('cb_PFx', false);			// Leistungsfaktor
		$this->RegisterPropertyBoolean('cb_xAx', false);			// Phasenwinkel
		$this->RegisterPropertyBoolean('cb_F_H', false);			// Fundamental&Harmonic
		$this->RegisterPropertyBoolean('cb_Adv', false);			// Advanced
		$this->RegisterPropertyBoolean('cb_other', false);			// Sonstige

		$this->RegisterTimer('TimerUpdate_MECM', 0, 'MECM_TimerUpdate_MECM(' . $this->InstanceID . ');');
		$this->RegisterMessage(0, IPS_KERNELMESSAGE);
	}

	public function Destroy() {
		IPS_LogMessage(__CLASS__ . "_" . __FUNCTION__, sprintf("Destroy Modul '%s' ...", $this->InstanceID));
		parent::Destroy();						//Never delete this line!
	}

	public function MessageSink($TimeStamp, $SenderID, $Message, $Data) {
		$logMsg = sprintf("TimeStamp: %s | SenderID: %s | Message: %d | Data: %s", $TimeStamp, $SenderID, $Message, json_encode($Data));
		if ($this->logLevel >= LogLevel::INFO) {
			$this->AddLog(__FUNCTION__, $logMsg, 0, true);
		}

		if ($Message == IPS_KERNELMESSAGE) {
			if ($Data[0] == KR_READY) {
				//
			}
		}
	}

	public function ApplyChanges() {
		//Never delete this line!
		parent::ApplyChanges();

		$this->logLevel = $this->ReadPropertyInteger("LogLevel");
		if ($this->logLevel >= LogLevel::INFO) {
			$this->AddLog(__FUNCTION__, sprintf("Set Log-Level to %d", $this->logLevel));
		}

		$this->RegisterProfiles();
		$this->RegisterVariables();

		$enableAutoUpdate = $this->ReadPropertyBoolean("EnableAutoUpdate");
		if ($enableAutoUpdate) {
			$updateInterval = $this->ReadPropertyInteger("AutoUpdateInterval");
		} else {
			$updateInterval = 0;
		}
		$this->SetUpdateInterval($updateInterval);
	}


	public function SetUpdateInterval(int $updateInterval) {
		if ($updateInterval <= 0) {
			if ($this->logLevel >= LogLevel::INFO) {
				$this->AddLog(__FUNCTION__, "Auto-Update stopped [TimerIntervall = 0]");
			}
		} else if ($updateInterval < 5) {
			$updateInterval = 5;
			if ($this->logLevel >= LogLevel::INFO) {
				$this->AddLog(__FUNCTION__, sprintf("Set Auto-Update Timer Intervall to %ss", $updateInterval));
			}
		} else {
			if ($this->logLevel >= LogLevel::INFO) {
				$this->AddLog(__FUNCTION__, sprintf("Set Auto-Update Timer Intervall to %ss", $updateInterval));
			}
		}
		$this->SetTimerInterval("TimerUpdate_MECM", $updateInterval * 1000);
	}


	public function TimerUpdate_MECM() {
		if ($this->logLevel >= LogLevel::INFO) {
			$this->AddLog(__FUNCTION__, "TimerUpdate_MECM called ...", 0, true);
		}

		$result = $this->Update();
	}


	public function Update() {

		$meterDataArr = $this->RequestMeterData();
		if ($meterDataArr !== false) {

			$cnt = 0;
			foreach (SELF::MECM_CONIG_ARR as $key => $configArr) {
				if ($configArr[CONFIG::ENABLED]) {
					$groupIdent = $configArr[CONFIG::GROUPIDENT];
					$groupName = $configArr[CONFIG::GROUPNAME];
					$groupPos = $configArr[CONFIG::GROUPPOS];

					$groupEnabled = $this->ReadPropertyBoolean("cb_" . $groupIdent);
					if ($groupEnabled) {
						$dummyID = $this->CreateDummyInstanceByIdent($groupIdent, $groupName, $this->InstanceID, $groupPos, "");
						if ($dummyID !== false) {
							if (array_key_exists($key, $meterDataArr)) {
								$varValue = $meterDataArr[$key];
								$this->SetVariableByIdent($varValue, $key, $configArr[CONFIG::NAME], $dummyID, $configArr[CONFIG::VARTYPE], $configArr[CONFIG::POSITION], $configArr[CONFIG::VARPROFILE], "", false, $configArr[CONFIG::ROUND], $configArr[CONFIG::FACTOR]);
							} else {
								if ($this->logLevel >= LogLevel::WARN) {
									$this->AddLog(__FUNCTION__, sprintf("Key '%s' not found in MeterData Array", $key));
								}
							}
						} else {
							if ($this->logLevel >= LogLevel::WARN) {
								$this->AddLog(__FUNCTION__, sprintf("WARN: Dummy '%s' not created/found", $groupName));
							}
						}
					}
				}
			}
			$this->Increase_CounterVariable($this->GetIDForIdent("updateCntOk"));
		} else {
			if ($this->logLevel >= LogLevel::WARN) {
				$this->AddLog(__FUNCTION__, "WARN: keine aktullen SmartMeter Messdaten vorhanden !");
			}
		}
	}


	public function CheckConfig($trigger) {

		$meterDataArr = $this->RequestMeterData();
		if ($meterDataArr !== false) {
			$this->Increase_CounterVariable($this->GetIDForIdent("updateCntOk"));

			$cntFound = 0;
			$cntNotFound = 0;
			foreach ($meterDataArr as $key => $value) {

				if (array_key_exists($key, SELF::MECM_CONIG_ARR)) {
					$cntFound++;
				} else {
					$cntNotFound++;
					if ($this->logLevel >= LogLevel::WARN) {
						$this->AddLog(__FUNCTION__, sprintf("WARN: Messpunkt '%s' not found in Config!", $key));
					}
				}
			}
			if ($this->logLevel >= LogLevel::INFO) {
				$this->AddLog(__FUNCTION__, sprintf("INFO SmartMeter Datenpunkte: %d NICHT vorhadnen | %d vorhanden", $cntNotFound, $cntFound));
			}
		} else {
			if ($this->logLevel >= LogLevel::WARN) {
				$this->AddLog(__FUNCTION__, "WARN: keine aktullen SmartMeter Messdaten vorhanden !");
			}
		}
	}

	public function ResetCounterVariables() {
		if ($this->logLevel >= LogLevel::INFO) {
			$this->AddLog(__FUNCTION__, 'RESET Counter Variables', 0);
		}
		SetValue($this->GetIDForIdent("updateCntOk"), 0);
		SetValue($this->GetIDForIdent("updateCntError"), 0);
		SetValue($this->GetIDForIdent("updateLastError"), "");
		SetValue($this->GetIDForIdent("updateLastDuration"), 0);
	}

	protected function RegisterProfiles() {

		if (!IPS_VariableProfileExists('SM.Hz.2')) {
			IPS_CreateVariableProfile('SM.Hz.2', VARIABLE::TYPE_FLOAT);
			IPS_SetVariableProfileDigits('SM.Hz.2', 2);
			IPS_SetVariableProfileText('SM.Hz.2', "", " Hz");
		}

		if (!IPS_VariableProfileExists('SM.Volt.2')) {
			IPS_CreateVariableProfile('SM.Volt.2', VARIABLE::TYPE_FLOAT);
			IPS_SetVariableProfileDigits('SM.Volt.2', 2);
			IPS_SetVariableProfileText('SM.Volt.2', "", " V");
		}

		if (!IPS_VariableProfileExists('SM.Current.3')) {
			IPS_CreateVariableProfile('SM.Current.3', VARIABLE::TYPE_FLOAT);
			IPS_SetVariableProfileDigits('SM.Current.3', 3);
			IPS_SetVariableProfileText('SM.Current.3', "", " A");
		}

		if (!IPS_VariableProfileExists('SM.PhaseAngel.2')) {
			IPS_CreateVariableProfile('SM.PhaseAngel.2', VARIABLE::TYPE_FLOAT);
			IPS_SetVariableProfileDigits('SM.PhaseAngel.2', 2);
			IPS_SetVariableProfileText('SM.PhaseAngel.2', "", " °");
		}

		if (!IPS_VariableProfileExists('SM.Watt.2')) {
			IPS_CreateVariableProfile('SM.Watt.2', VARIABLE::TYPE_FLOAT);
			IPS_SetVariableProfileDigits('SM.Watt.2', 2);
			IPS_SetVariableProfileText('SM.Watt.2', "", " W");
		}

		if (!IPS_VariableProfileExists('SM.Var.2')) {
			IPS_CreateVariableProfile('SM.Var.2', VARIABLE::TYPE_FLOAT);
			IPS_SetVariableProfileDigits('SM.Var.2', 2);
			IPS_SetVariableProfileText('SM.Var.2', "", " Var");
		}

		if (!IPS_VariableProfileExists('SM.VA.2')) {
			IPS_CreateVariableProfile('SM.VA.2', VARIABLE::TYPE_FLOAT);
			IPS_SetVariableProfileDigits('SM.VA.2', 2);
			IPS_SetVariableProfileText('SM.VA.2', "", " VA");
		}

		if (!IPS_VariableProfileExists('SM.kWh.3')) {
			IPS_CreateVariableProfile('SM.kWh.3', VARIABLE::TYPE_FLOAT);
			IPS_SetVariableProfileDigits('SM.kWh.3', 3);
			IPS_SetVariableProfileText('SM.kWh.3', "", " kWh");
		}

		if (!IPS_VariableProfileExists('SM.kVA.3')) {
			IPS_CreateVariableProfile('SM.kVA.3', VARIABLE::TYPE_FLOAT);
			IPS_SetVariableProfileDigits('SM.kVA.3', 3);
			IPS_SetVariableProfileText('SM.kVA.3', "", " kVA");
		}

		if (!IPS_VariableProfileExists('DC.mA.3')) {
			IPS_CreateVariableProfile('DC.mA.3', VARIABLE::TYPE_FLOAT);
			IPS_SetVariableProfileDigits('DC.mA.3', 3);
			IPS_SetVariableProfileText('DC.mA.3', "", " mA");
		}

		if ($this->logLevel >= LogLevel::DEBUG) {
			$this->AddLog(__FUNCTION__, "Variable Profiles registered");
		}
	}

	protected function RegisterVariables() {

		$parentRootId = IPS_GetParent($this->InstanceID);

		$this->RegisterVariableInteger("updateCntOk", "Update Cnt OK", "", 900);
		$this->RegisterVariableInteger("updateCntError", "Update Cnt ERROR", "", 910);
		$this->RegisterVariableString("updateLastError", "Update Last Error", "", 920);
		$this->RegisterVariableInteger("lastProcessingTotalDuration", "Last API Request Duration [ms]", "", 930);

		if ($this->logLevel >= LogLevel::DEBUG) {
			$this->AddLog(__FUNCTION__, "Variables registered");
		}
	}

	public function GetClassInfo() {
		return print_r($this, true);
	}

	protected function AddLog($name, $daten, $format = 0, $ipsLogOutput = false) {
		$this->logCnt++;
		$logSender = "[" . __CLASS__ . "] - " . $name;
		if ($this->logLevel >= LogLevel::DEBUG) {
			$logSender = sprintf("%02d-T%2d [%s] - %s", $this->logCnt, $_IPS['THREAD'], __CLASS__, $name);
		}
		$this->SendDebug($logSender, $daten, $format);

		if ($ipsLogOutput or $this->enableIPSLogOutput) {
			if ($format == 0) {
				IPS_LogMessage($logSender, $daten);
			} else {
				IPS_LogMessage($logSender, $this->String2Hex($daten));
			}
		}
	}
}