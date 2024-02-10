<?php

declare(strict_types=1);


abstract class CONFIG {

    const NAME = 0;
    const VARTYPE = 1;
    const VARPROFILE = 2;
    const FACTOR = 3;
    const ROUND = 4;
    const GROUPIDENT = 5;
    const GROUPNAME = 6;
    const GROUPPOS = 7;
    const POSITION = 8;
    const ENABLED = 9;
}

trait MECMETER_HELPER {



    const MECM_CONIG_ARR = array(

        'F' => ['Frequency', VARIABLETYPE_FLOAT, 'SM.Hz.2', 1, 2, 'Basic', 'Basic', 10, 1, true],
        'T' => ['Internal Temperature', VARIABLETYPE_FLOAT, '~Temperature', 1, 2, 'Basic', 'Basic', 10, 2, true],
        'STATUS' => ['Status of the VPM ', VARIABLETYPE_INTEGER, '', 1, 0, 'Basic', 'Basic', 10, 3, true],
        'TIME' => ['Operating Time (last fctory reset)', VARIABLETYPE_INTEGER, 'Duration.INT.ms', 1, 0, 'Basic', 'Basic', 10, 4, true],
        'SAMPLES' => ['Counter from the Quadrants updated values', VARIABLETYPE_INTEGER, '', 1, 0, 'Basic', 'Basic', 10, 5, true],
        'PA' => ['Leistung L1', VARIABLETYPE_FLOAT, 'SM.Watt.2', 1, 2, 'Px', 'Wirkleistung', 20, 6, true],
        'PB' => ['Leistung L2', VARIABLETYPE_FLOAT, 'SM.Watt.2', 1, 2, 'Px', 'Wirkleistung', 20, 7, true],
        'PC' => ['Leistung L3', VARIABLETYPE_FLOAT, 'SM.Watt.2', 1, 2, 'Px', 'Wirkleistung', 20, 8, true],
        'PT' => ['Leistung Gesamt', VARIABLETYPE_FLOAT, 'SM.Watt.2', 1, 2, 'Px', 'Wirkleistung', 20, 9, true],
        'EFAA' => ['Wirkenergie Bezug L1', VARIABLETYPE_FLOAT, 'SM.kWh.3', 0.001, 3, 'EFAx', 'Wirkenergie Bezug', 25, 10, true],
        'EFAB' => ['Wirkenergie Bezug L2', VARIABLETYPE_FLOAT, 'SM.kWh.3', 0.001, 3, 'EFAx', 'Wirkenergie Bezug', 25, 11, true],
        'EFAC' => ['Wirkenergie Bezug L3', VARIABLETYPE_FLOAT, 'SM.kWh.3', 0.001, 3, 'EFAx', 'Wirkenergie Bezug', 25, 12, true],
        'EFAT' => ['Wirkenergie Bezug Total', VARIABLETYPE_FLOAT, 'SM.kWh.3', 0.001, 3, 'EFAx', 'Wirkenergie Bezug', 25, 13, true],
        'ERAA' => ['Wirkenergie Einspeisung L1', VARIABLETYPE_FLOAT, 'SM.kWh.3', 0.001, 3, 'ERAx', 'Wirkenergie Einspeisung', 26, 16, true],
        'ERAB' => ['Wirkenergie Einspeisung L2', VARIABLETYPE_FLOAT, 'SM.kWh.3', 0.001, 3, 'ERAx', 'Wirkenergie Einspeisung', 26, 17, true],
        'ERAC' => ['Wirkenergie Einspeisung L3', VARIABLETYPE_FLOAT, 'SM.kWh.3', 0.001, 3, 'ERAx', 'Wirkenergie Einspeisung', 26, 18, true],
        'ERAT' => ['Wirkenergie Einspeisung Total', VARIABLETYPE_FLOAT, 'SM.kWh.3', 0.001, 3, 'ERAx', 'Wirkenergie Einspeisung', 26, 19, true],
        'VA' => ['AC-Spannung L1', VARIABLETYPE_FLOAT, 'SM.Volt.2', 1, 2, 'Vx', 'AC-Spannung', 30, 20, true],
        'VB' => ['AC-Spannung L2', VARIABLETYPE_FLOAT, 'SM.Volt.2', 1, 2, 'Vx', 'AC-Spannung', 30, 21, true],
        'VC' => ['AC-Spannung L3', VARIABLETYPE_FLOAT, 'SM.Volt.2', 1, 2, 'Vx', 'AC-Spannung', 30, 22, true],
        'VT' => ['durchschnittliche N-Phasen-Spannung ', VARIABLETYPE_FLOAT, 'SM.Volt.2', 1, 2, 'Vx', 'AC-Spannung', 30, 23, true],
        'VAB' => ['AC-Spannung L1 - L2', VARIABLETYPE_FLOAT, 'SM.Volt.2', 1, 2, 'Vx', 'AC-Spannung', 30, 24, true],
        'VBC' => ['AC-Spannung L2 - L3', VARIABLETYPE_FLOAT, 'SM.Volt.2', 1, 2, 'Vx', 'AC-Spannung', 30, 25, true],
        'VCA' => ['AC-Spannung L3 - L1', VARIABLETYPE_FLOAT, 'SM.Volt.2', 1, 2, 'Vx', 'AC-Spannung', 30, 26, true],
        'VPT' => ['durchschnittliche Phase-Phase-Spannung ', VARIABLETYPE_FLOAT, 'SM.Volt.2', 1, 2, 'Vx', 'AC-Spannung', 30, 27, true],
        'IA' => ['AC-Strom L1', VARIABLETYPE_FLOAT, 'SM.Current.3', 1, 3, 'Ix', 'AC-Strom', 35, 28, true],
        'IB' => ['AC-Strom L2', VARIABLETYPE_FLOAT, 'SM.Current.3', 1, 3, 'Ix', 'AC-Strom', 35, 29, true],
        'IC' => ['AC-Strom L3', VARIABLETYPE_FLOAT, 'SM.Current.3', 1, 3, 'Ix', 'AC-Strom', 35, 30, true],
        'IN' => ['AC-Strom N', VARIABLETYPE_FLOAT, 'SM.Current.3', 1, 3, 'Ix', 'AC-Strom', 35, 31, true],
        'IN0' => ['AC-Strom N (Calculated)', VARIABLETYPE_FLOAT, 'SM.Current.3', 1, 2, 'Ix', 'AC-Strom', 35, 32, true],
        'IADC' => ['Direct Current - Phase A', VARIABLETYPE_FLOAT, 'SM.Current.3', 1, 3, 'IxDC', 'Direct Current', 36, 33, true],
        'IBDC' => ['Direct Current - Phase B', VARIABLETYPE_FLOAT, 'SM.Current.3', 1, 3, 'IxDC', 'Direct Current', 36, 34, true],
        'ICDC' => ['Direct Current - Phase C', VARIABLETYPE_FLOAT, 'SM.Current.3', 1, 3, 'IxDC', 'Direct Current', 36, 35, true],
        'PFA' => ['Leistungsfaktor L1', VARIABLETYPE_FLOAT, '', 1, 3, 'PFx', 'Leistungsfaktor', 50, 36, true],
        'PFB' => ['Leistungsfaktor L2', VARIABLETYPE_FLOAT, '', 1, 3, 'PFx', 'Leistungsfaktor', 50, 37, true],
        'PFC' => ['Leistungsfaktor L3', VARIABLETYPE_FLOAT, '', 1, 3, 'PFx', 'Leistungsfaktor', 50, 38, true],
        'PFT' => ['Leistungsfaktor Total', VARIABLETYPE_FLOAT, '', 1, 3, 'PFx', 'Leistungsfaktor', 50, 39, true],
        'IAA' => ['Phasenwinkel Strom L1', VARIABLETYPE_FLOAT, 'SM.PhaseAngel.2', 1, 2, 'xAx', 'Phasenwinkel', 55, 40, true],
        'IAB' => ['Phasenwinkel Strom L2', VARIABLETYPE_FLOAT, 'SM.PhaseAngel.2', 1, 2, 'xAx', 'Phasenwinkel', 55, 41, true],
        'IAC' => ['Phasenwinkel Strom L3', VARIABLETYPE_FLOAT, 'SM.PhaseAngel.2', 1, 2, 'xAx', 'Phasenwinkel', 55, 42, true],
        'UAA' => ['Phasenwinkel Spannung L1-L3', VARIABLETYPE_FLOAT, 'SM.PhaseAngel.2', 1, 2, 'xAx', 'Phasenwinkel', 55, 43, true],
        'UAB' => ['Phasenwinkel Spannung L2-L3', VARIABLETYPE_FLOAT, 'SM.PhaseAngel.2', 1, 2, 'xAx', 'Phasenwinkel', 55, 44, true],
        'UAC' => ['Phasenwinkel Spannung L3-L3', VARIABLETYPE_FLOAT, 'SM.PhaseAngel.2', 1, 2, 'xAx', 'Phasenwinkel', 55, 45, true],
        'QA' => ['Reactive Power - Phase A', VARIABLETYPE_FLOAT, 'SM.Var.2', 1, 2, 'RPx', 'Blindleistung', 60, 46, true],
        'QB' => ['Reactive Power - Phase B', VARIABLETYPE_FLOAT, 'SM.Var.2', 1, 2, 'RPx', 'Blindleistung', 60, 47, true],
        'QC' => ['Reactive Power - Phase C', VARIABLETYPE_FLOAT, 'SM.Var.2', 1, 2, 'RPx', 'Blindleistung', 60, 48, true],
        'QT' => ['Reactive Power - Total', VARIABLETYPE_FLOAT, 'SM.Var.2', 1, 2, 'RPx', 'Blindleistung', 60, 49, true],
        'EFRA' => ['Forward Reactive Energy - Phase A', VARIABLETYPE_FLOAT, 'SM.kvarh.3', 0.001, 3, 'REx', 'Blindenergie', 65, 50, true],
        'EFRB' => ['Forward Reactive Energy - Phase B', VARIABLETYPE_FLOAT, 'SM.kvarh.3', 0.001, 3, 'REx', 'Blindenergie', 65, 51, true],
        'EFRC' => ['Forward Reactive Energy - Phase C', VARIABLETYPE_FLOAT, 'SM.kvarh.3', 0.001, 3, 'REx', 'Blindenergie', 65, 52, true],
        'EFRT' => ['Forward Reactive Energy - Total', VARIABLETYPE_FLOAT, 'SM.kvarh.3', 0.001, 3, 'REx', 'Blindenergie', 65, 53, true],
        'ERRA' => ['Reverse Reactive Energy - Phase A', VARIABLETYPE_FLOAT, 'SM.kvarh.3', 0.001, 3, 'REx', 'Blindenergie', 65, 54, true],
        'ERRB' => ['Reverse Reactive Energy - Phase B', VARIABLETYPE_FLOAT, 'SM.kvarh.3', 0.001, 3, 'REx', 'Blindenergie', 65, 55, true],
        'ERRC' => ['Reverse Reactive Energy - Phase C', VARIABLETYPE_FLOAT, 'SM.kvarh.3', 0.001, 3, 'REx', 'Blindenergie', 65, 56, true],
        'ERRT' => ['Reverse Reactive Energy - Total', VARIABLETYPE_FLOAT, 'SM.kvarh.3', 0.001, 3, 'REx', 'Blindenergie', 65, 57, true],
        'ERT1' => ['Reactive Energy Quadrant 1 - Total', VARIABLETYPE_FLOAT, 'SM.kvarh.3', 0.001, 3, 'RE1_4', 'Blindenergie Quadrant 1 - 4', 66, 58, true],
        'ERT2' => ['Reactive Energy Quadrant 2 - Total', VARIABLETYPE_FLOAT, 'SM.kvarh.3', 0.001, 3, 'RE1_4', 'Blindenergie Quadrant 1 - 4', 66, 59, true],
        'ERT3' => ['Reactive Energy Quadrant 3 - Total', VARIABLETYPE_FLOAT, 'SM.kvarh.3', 0.001, 3, 'RE1_4', 'Blindenergie Quadrant 1 - 4', 66, 60, true],
        'ERT4' => ['Reactive Energy Quadrant 4 - Total', VARIABLETYPE_FLOAT, 'SM.kvarh.3', 0.001, 3, 'RE1_4', 'Blindenergie Quadrant 1 - 4', 66, 61, true],
        'ERA1' => ['Reactive Energy Quadrant 1 - Phase A', VARIABLETYPE_FLOAT, 'SM.kvarh.3', 0.001, 3, 'RE1_4', 'Blindenergie Quadrant 1 - 4', 66, 62, true],
        'ERA2' => ['Reactive Energy Quadrant 2 - Phase A', VARIABLETYPE_FLOAT, 'SM.kvarh.3', 0.001, 3, 'RE1_4', 'Blindenergie Quadrant 1 - 4', 66, 63, true],
        'ERA3' => ['Reactive Energy Quadrant 3 - Phase A', VARIABLETYPE_FLOAT, 'SM.kvarh.3', 0.001, 3, 'RE1_4', 'Blindenergie Quadrant 1 - 4', 66, 64, true],
        'ERA4' => ['Reactive Energy Quadrant 4 - Phase A', VARIABLETYPE_FLOAT, 'SM.kvarh.3', 0.001, 3, 'RE1_4', 'Blindenergie Quadrant 1 - 4', 66, 65, true],
        'ERB1' => ['Reactive Energy Quadrant 1 - Phase B', VARIABLETYPE_FLOAT, 'SM.kvarh.3', 0.001, 3, 'RE1_4', 'Blindenergie Quadrant 1 - 4', 66, 66, true],
        'ERB2' => ['Reactive Energy Quadrant 2 - Phase B', VARIABLETYPE_FLOAT, 'SM.kvarh.3', 0.001, 3, 'RE1_4', 'Blindenergie Quadrant 1 - 4', 66, 67, true],
        'ERB3' => ['Reactive Energy Quadrant 3 - Phase B', VARIABLETYPE_FLOAT, 'SM.kvarh.3', 0.001, 3, 'RE1_4', 'Blindenergie Quadrant 1 - 4', 66, 68, true],
        'ERB4' => ['Reactive Energy Quadrant 4 - Phase B', VARIABLETYPE_FLOAT, 'SM.kvarh.3', 0.001, 3, 'RE1_4', 'Blindenergie Quadrant 1 - 4', 66, 69, true],
        'ERC1' => ['Reactive Energy Quadrant 1 - Phase C', VARIABLETYPE_FLOAT, 'SM.kvarh.3', 0.001, 3, 'RE1_4', 'Blindenergie Quadrant 1 - 4', 66, 70, true],
        'ERC2' => ['Reactive Energy Quadrant 2 - Phase C', VARIABLETYPE_FLOAT, 'SM.kvarh.3', 0.001, 3, 'RE1_4', 'Blindenergie Quadrant 1 - 4', 66, 71, true],
        'ERC3' => ['Reactive Energy Quadrant 3 - Phase C', VARIABLETYPE_FLOAT, 'SM.kvarh.3', 0.001, 3, 'RE1_4', 'Blindenergie Quadrant 1 - 4', 66, 72, true],
        'ERC4' => ['Reactive Energy Quadrant 4 - Phase C', VARIABLETYPE_FLOAT, 'SM.kvarh.3', 0.001, 3, 'RE1_4', 'Blindenergie Quadrant 1 - 4', 66, 73, true],
        'SA' => ['Apparent Power - Phase A', VARIABLETYPE_FLOAT, 'SM.VA.2', 1, 2, 'APx', 'Scheinleistung', 70, 74, true],
        'SB' => ['Apparent Power - Phase B', VARIABLETYPE_FLOAT, 'SM.VA.2', 1, 2, 'APx', 'Scheinleistung', 70, 75, true],
        'SC' => ['Apparent Power - Phase C', VARIABLETYPE_FLOAT, 'SM.VA.2', 1, 2, 'APx', 'Scheinleistung', 70, 76, true],
        'ST' => ['Apparent Power - Total', VARIABLETYPE_FLOAT, 'SM.VA.2', 1, 2, 'APx', 'Scheinleistung', 70, 77, true],
        'EMT' => ['Apparent Power - Total (Vector Sum)', VARIABLETYPE_FLOAT, 'SM.VA.2', 1, 2, 'APx', 'Scheinleistung', 70, 78, true],
        'ESA' => ['Apparent Energy - Phase A', VARIABLETYPE_FLOAT, 'SM.kVAh.3', 0.001, 3, 'AEx', 'Scheinenergie', 75, 79, true],
        'ESB' => ['Apparent Energy - Phase B', VARIABLETYPE_FLOAT, 'SM.kVAh.3', 0.001, 3, 'AEx', 'Scheinenergie', 75, 80, true],
        'ESC' => ['Apparent Energy - Phase C', VARIABLETYPE_FLOAT, 'SM.kVAh.3', 0.001, 3, 'AEx', 'Scheinenergie', 75, 81, true],
        'EST' => ['Apparent Energy - Total', VARIABLETYPE_FLOAT, 'SM.kVAh.3', 0.001, 3, 'AEx', 'Scheinenergie', 75, 82, true],
        'EVT' => ['Apparent Energy - Total (Vector Sum)', VARIABLETYPE_FLOAT, 'SM.kVAh.3', 0.001, 3, 'AEx', 'Scheinenergie', 75, 83, true],
        'ERSA' => ['Apparent Reverse Energy - Phase A', VARIABLETYPE_FLOAT, 'SM.kVAh.3', 0.001, 3, 'AEx', 'Scheinenergie', 75, 84, true],
        'ERSB' => ['Apparent Reverse Energy - Phase B', VARIABLETYPE_FLOAT, 'SM.kVAh.3', 0.001, 3, 'AEx', 'Scheinenergie', 75, 85, true],
        'ERSC' => ['Apparent Reverse Energy - Phase C', VARIABLETYPE_FLOAT, 'SM.kVAh.3', 0.001, 3, 'AEx', 'Scheinenergie', 75, 86, true],
        'ERST' => ['Apparent Reverse Energy - Total', VARIABLETYPE_FLOAT, 'SM.kVAh.3', 0.001, 3, 'AEx', 'Scheinenergie', 75, 87, true],
        'EFSA' => ['Apparent Forward Energy - Phase A', VARIABLETYPE_FLOAT, 'SM.kWh.3', 0.001, 3, 'AEx', 'Scheinenergie', 75, 88, true],
        'EFSB' => ['Apparent Forward Energy - Phase B', VARIABLETYPE_FLOAT, 'SM.kWh.3', 0.001, 3, 'AEx', 'Scheinenergie', 75, 89, true],
        'EFSC' => ['Apparent Forward Energy - Phase C', VARIABLETYPE_FLOAT, 'SM.kWh.3', 0.001, 3, 'AEx', 'Scheinenergie', 75, 90, true],
        'EFST' => ['Apparent Forward Energy - Total', VARIABLETYPE_FLOAT, 'SM.kWh.3', 0.001, 3, 'AEx', 'Scheinenergie', 75, 91, true],
        'THUA' => ['Phase A voltage THD+N ', VARIABLETYPE_FLOAT, 'Percent.2', 1, 2, 'THD', 'THD+N', 80, 92, true],
        'THUB' => ['Phase B voltage THD+N ', VARIABLETYPE_FLOAT, 'Percent.2', 1, 2, 'THD', 'THD+N', 80, 93, true],
        'THUC' => ['Phase C voltage THD+N ', VARIABLETYPE_FLOAT, 'Percent.2', 1, 2, 'THD', 'THD+N', 80, 94, true],
        'THIA' => ['Phase A current THD+N ', VARIABLETYPE_FLOAT, 'Percent.2', 1, 2, 'THD', 'THD+N', 80, 95, true],
        'THIB' => ['Phase B current THD+N ', VARIABLETYPE_FLOAT, 'Percent.2', 1, 2, 'THD', 'THD+N', 80, 96, true],
        'THIC' => ['Phase C current THD+N ', VARIABLETYPE_FLOAT, 'Percent.2', 1, 2, 'THD', 'THD+N', 80, 97, true],
        'PAF' => ['Active Fundamental Power - Phase A', VARIABLETYPE_FLOAT, 'SM.Watt.2', 1, 2, 'FPx', 'Fundamental Power', 90, 98, true],
        'PBF' => ['Active Fundamental Power - Phase B', VARIABLETYPE_FLOAT, 'SM.Watt.2', 1, 2, 'FPx', 'Fundamental Power', 90, 99, true],
        'PCF' => ['Active Fundamental Power - Phase C', VARIABLETYPE_FLOAT, 'SM.Watt.2', 1, 2, 'FPx', 'Fundamental Power', 90, 100, true],
        'PTF' => ['Active Fundamental Power - Total', VARIABLETYPE_FLOAT, 'SM.Watt.2', 1, 2, 'FPx', 'Fundamental Power', 90, 101, true],
        'EFAF' => ['Forward Active Fundamental Energy - Phase A ', VARIABLETYPE_FLOAT, 'SM.kWh.3', 0.001, 3, 'FEx', 'Fundamental Energy', 91, 102, true],
        'EFBF' => ['Forward Active Fundamental Energy - Phase B ', VARIABLETYPE_FLOAT, 'SM.kWh.3', 0.001, 3, 'FEx', 'Fundamental Energy', 91, 103, true],
        'EFCF' => ['Forward Active Fundamental Energy - Phase C', VARIABLETYPE_FLOAT, 'SM.kWh.3', 0.001, 3, 'FEx', 'Fundamental Energy', 91, 104, true],
        'EFTF' => ['Forward Active Fundamental Energyt - Total', VARIABLETYPE_FLOAT, 'SM.kWh.3', 0.001, 3, 'FEx', 'Fundamental Energy', 91, 105, true],
        'ERAF' => ['Reverse Active Fundamental Energy - Phase A', VARIABLETYPE_FLOAT, 'SM.kWh.3', 0.001, 3, 'FEx', 'Fundamental Energy', 91, 106, true],
        'ERBF' => ['Reverse Active Fundamental Energy - Phase B', VARIABLETYPE_FLOAT, 'SM.kWh.3', 0.001, 3, 'FEx', 'Fundamental Energy', 91, 107, true],
        'ERCF' => ['Reverse Active Fundamental Energy - Phase C', VARIABLETYPE_FLOAT, 'SM.kWh.3', 0.001, 3, 'FEx', 'Fundamental Energy', 91, 108, true],
        'ERTF' => ['Reverse Active Fundamental Energy - Total', VARIABLETYPE_FLOAT, 'SM.kWh.3', 0.001, 3, 'FEx', 'Fundamental Energy', 91, 109, true],
        'PAH' => ['Active Harmonic Power - Phase A', VARIABLETYPE_FLOAT, 'SM.Watt.2', 1, 2, 'HPx', 'Harmonic Power', 95, 110, true],
        'PBH' => ['Active Harmonic Power - Phase B', VARIABLETYPE_FLOAT, 'SM.Watt.2', 1, 2, 'HPx', 'Harmonic Power', 95, 111, true],
        'PCH' => ['Active Harmonic Power - Phase C', VARIABLETYPE_FLOAT, 'SM.Watt.2', 1, 2, 'HPx', 'Harmonic Power', 95, 112, true],
        'PTH' => ['Active Harmonic Power - Total', VARIABLETYPE_FLOAT, 'SM.Watt.2', 1, 2, 'HPx', 'Harmonic Power', 95, 113, true],
        'EFAH' => ['Forward Active Harmonic Energy - Phase A ', VARIABLETYPE_FLOAT, 'SM.kWh.3', 0.001, 3, 'HEx', 'Harmonic Energy', 96, 114, true],
        'EFBH' => ['Forward Active Harmonic Energy - Phase B ', VARIABLETYPE_FLOAT, 'SM.kWh.3', 0.001, 3, 'HEx', 'Harmonic Energy', 96, 115, true],
        'EFCH' => ['Forward Active Harmonic Energy - Phase C ', VARIABLETYPE_FLOAT, 'SM.kWh.3', 0.001, 3, 'HEx', 'Harmonic Energy', 96, 116, true],
        'EFTH' => ['Forward Active Harmonic Energy - Total ', VARIABLETYPE_FLOAT, 'SM.kWh.3', 0.001, 3, 'HEx', 'Harmonic Energy', 96, 117, true],
        'ERAH' => ['Reverse Active Harmonic Energy - Phase A', VARIABLETYPE_FLOAT, 'SM.kWh.3', 0.001, 3, 'HEx', 'Harmonic Energy', 96, 118, true],
        'ERBH' => ['Reverse Active Harmonic Energy - Phase B', VARIABLETYPE_FLOAT, 'SM.kWh.3', 0.001, 3, 'HEx', 'Harmonic Energy', 96, 119, true],
        'ERCH' => ['Reverse Active Harmonic Energy - Phase C', VARIABLETYPE_FLOAT, 'SM.kWh.3', 0.001, 3, 'HEx', 'Harmonic Energy', 96, 120, true],
        'ERTH' => ['Reverse Active Harmonic Energy - Total', VARIABLETYPE_FLOAT, 'SM.kWh.3', 0.001, 3, 'HEx', 'Harmonic Energy', 96, 121, true]

    );
}
