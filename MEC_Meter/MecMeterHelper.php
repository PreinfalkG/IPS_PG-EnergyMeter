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

        'F' => ['Mains frequency', VARIABLETYPE_FLOAT, 'SM.Hz.2', 1, 2, 'Basic', 'Basic', 10, 1, true],
        'T' => ['Internal Temperature', VARIABLETYPE_FLOAT, '~Temperature', 1, 2, 'Basic', 'Basic', 10, 2, true],
        'VA' => ['AC-Spannung L1', VARIABLETYPE_FLOAT, 'SM.Volt.2', 1, 2, 'Vx', 'AC-Spannung', 20, 3, true],
        'VB' => ['AC-Spannung L2', VARIABLETYPE_FLOAT, 'SM.Volt.2', 1, 2, 'Vx', 'AC-Spannung', 20, 4, true],
        'VC' => ['AC-Spannung L3', VARIABLETYPE_FLOAT, 'SM.Volt.2', 1, 2, 'Vx', 'AC-Spannung', 20, 5, true],
        'VAB' => ['AC-Spannung L1 - L2', VARIABLETYPE_FLOAT, 'SM.Volt.2', 1, 2, 'Vx', 'AC-Spannung', 20, 6, true],
        'VBC' => ['AC-Spannung L2 - L3', VARIABLETYPE_FLOAT, 'SM.Volt.2', 1, 2, 'Vx', 'AC-Spannung', 20, 7, true],
        'VCA' => ['AC-Spannung L3 - L1', VARIABLETYPE_FLOAT, 'SM.Volt.2', 1, 2, 'Vx', 'AC-Spannung', 20, 8, true],
        'VPT' => ['Durchschnittliche Phase-Phase-Spannung ', VARIABLETYPE_FLOAT, 'SM.Volt.2', 1, 2, 'Vx', 'AC-Spannung', 20, 9, true],
        'VT' => ['Durchschnittliche N-Phasen-Spannung ', VARIABLETYPE_FLOAT, 'SM.Volt.2', 1, 2, 'Vx', 'AC-Spannung', 20, 10, true],
        'IA' => ['AC-Strom L1', VARIABLETYPE_FLOAT, 'SM.Current.3', 1, 3, 'Ix', 'AC-Strom', 30, 11, true],
        'IB' => ['AC-Strom L2', VARIABLETYPE_FLOAT, 'SM.Current.3', 1, 3, 'Ix', 'AC-Strom', 30, 12, true],
        'IC' => ['AC-Strom L3', VARIABLETYPE_FLOAT, 'SM.Current.3', 1, 3, 'Ix', 'AC-Strom', 30, 13, true],
        'IN' => ['AC-Strom N', VARIABLETYPE_FLOAT, 'SM.Current.3', 1, 3, 'Ix', 'AC-Strom', 30, 14, true],
        'IADC' => ['DC-Strom L1', VARIABLETYPE_FLOAT, 'DC.mA.3', 1000, 3, 'DcI', 'DC-Strom', 31, 15, true],
        'IBDC' => ['DC-Strom L2', VARIABLETYPE_FLOAT, 'DC.mA.3', 1000, 3, 'DcI', 'DC-Strom', 31, 16, true],
        'ICDC' => ['DC-Strom L3', VARIABLETYPE_FLOAT, 'DC.mA.3', 1000, 3, 'DcI', 'DC-Strom', 31, 17, true],
        'PA' => ['Leistung L1', VARIABLETYPE_FLOAT, 'SM.Watt.2', 1, 2, 'Px', 'Wirkleistung', 40, 18, true],
        'PB' => ['Leistung L2', VARIABLETYPE_FLOAT, 'SM.Watt.2', 1, 2, 'Px', 'Wirkleistung', 40, 19, true],
        'PC' => ['Leistung L3', VARIABLETYPE_FLOAT, 'SM.Watt.2', 1, 2, 'Px', 'Wirkleistung', 40, 20, true],
        'PT' => ['Leistung Gesamt', VARIABLETYPE_FLOAT, 'SM.Watt.2', 1, 2, 'Px', 'Wirkleistung', 40, 21, true],
        'EFAA' => ['Wirkenergie Bezug L1', VARIABLETYPE_FLOAT, 'SM.kWh.3', 0.001, 3, 'EFAx', 'Wirkenergie Bezug', 50, 22, true],
        'EFAB' => ['Wirkenergie Bezug L2', VARIABLETYPE_FLOAT, 'SM.kWh.3', 0.001, 3, 'EFAx', 'Wirkenergie Bezug', 50, 23, true],
        'EFAC' => ['Wirkenergie Bezug L3', VARIABLETYPE_FLOAT, 'SM.kWh.3', 0.001, 3, 'EFAx', 'Wirkenergie Bezug', 50, 24, true],
        'EFAT' => ['Wirkenergie Bezug Gesamt', VARIABLETYPE_FLOAT, 'SM.kWh.3', 0.001, 3, 'EFAx', 'Wirkenergie Bezug', 50, 25, true],
        'ERAA' => ['Wirkenergie Einspeisung L1', VARIABLETYPE_FLOAT, 'SM.kWh.3', 0.001, 3, 'ERAx', 'Wirkenergie Einspeisung', 51, 26, true],
        'ERAB' => ['Wirkenergie Einspeisung L2', VARIABLETYPE_FLOAT, 'SM.kWh.3', 0.001, 3, 'ERAx', 'Wirkenergie Einspeisung', 51, 27, true],
        'ERAC' => ['Wirkenergie Einspeisung L3', VARIABLETYPE_FLOAT, 'SM.kWh.3', 0.001, 3, 'ERAx', 'Wirkenergie Einspeisung', 51, 28, true],
        'ERAT' => ['Wirkenergie Einspeisung Gesamt', VARIABLETYPE_FLOAT, 'SM.kWh.3', 0.001, 3, 'ERAx', 'Wirkenergie Einspeisung', 51, 29, true],
        'QA' => ['Blindleistung L1', VARIABLETYPE_FLOAT, 'SM.Var.2', 1, 2, 'Qx', 'Blindleistung Bezug', 60, 30, true],
        'QB' => ['Blindleistung L2', VARIABLETYPE_FLOAT, 'SM.Var.2', 1, 2, 'Qx', 'Blindleistung Bezug', 60, 31, true],
        'QC' => ['Blindleistung L3', VARIABLETYPE_FLOAT, 'SM.Var.2', 1, 2, 'Qx', 'Blindleistung Bezug', 60, 32, true],
        'QT' => ['Blindleistung Gesamt', VARIABLETYPE_FLOAT, 'SM.Var.2', 1, 2, 'Qx', 'Blindleistung Bezug', 60, 33, true],
        'SA' => ['Scheinleistung L1', VARIABLETYPE_FLOAT, 'SM.VA.2', 1, 2, 'Sx', 'Scheinleistung Bezug', 61, 34, true],
        'SB' => ['Scheinleistung L2', VARIABLETYPE_FLOAT, 'SM.VA.2', 1, 2, 'Sx', 'Scheinleistung Bezug', 61, 35, true],
        'SC' => ['Scheinleistung L3', VARIABLETYPE_FLOAT, 'SM.VA.2', 1, 2, 'Sx', 'Scheinleistung Bezug', 61, 36, true],
        'ST' => ['Scheinleistung Gesamt', VARIABLETYPE_FLOAT, 'SM.VA.2', 1, 2, 'Sx', 'Scheinleistung Bezug', 61, 37, true],
        'EFRA' => ['Blindenergie L1', VARIABLETYPE_FLOAT, 'SM.kVA.3', 0.001, 3, 'EFRx', 'Blindenergie Bezug', 65, 38, true],
        'EFRB' => ['Blindenergie L2', VARIABLETYPE_FLOAT, 'SM.kVA.3', 0.001, 3, 'EFRx', 'Blindenergie Bezug', 65, 39, true],
        'EFRC' => ['Blindenergie L3', VARIABLETYPE_FLOAT, 'SM.kVA.3', 0.001, 3, 'EFRx', 'Blindenergie Bezug', 65, 40, true],
        'EFRT' => ['Blindenergie Gesamt', VARIABLETYPE_FLOAT, 'SM.kVA.3', 0.001, 3, 'EFRx', 'Blindenergie Bezug', 65, 41, true],
        'ESA' => ['Apparent Energy Consumption - Phase A', VARIABLETYPE_FLOAT, 'SM.kVA.3', 0.001, 3, 'ESx', 'Scheinenergie Bezug', 66, 42, true],
        'ESB' => ['Apparent Energy Consumption - Phase B', VARIABLETYPE_FLOAT, 'SM.kVA.3', 0.001, 3, 'ESx', 'Scheinenergie Bezug', 66, 43, true],
        'ESC' => ['Apparent Energy Consumption - Phase C', VARIABLETYPE_FLOAT, 'SM.kVA.3', 0.001, 3, 'ESx', 'Scheinenergie Bezug', 66, 44, true],
        'EST' => ['Apparent Energy Consumption - Total', VARIABLETYPE_FLOAT, 'SM.kVA.3', 0.001, 3, 'ESx', 'Scheinenergie Bezug', 66, 45, true],
        'ERRA' => ['Reverse Reactive Energy - Phase A', VARIABLETYPE_FLOAT, 'SM.kVA.3', 0.001, 3, 'ERRx', 'Blindenergie Einspeisung', 68, 46, true],
        'ERRB' => ['Reverse Reactive Energy - Phase B', VARIABLETYPE_FLOAT, 'SM.kVA.3', 0.001, 3, 'ERRx', 'Blindenergie Einspeisung', 68, 47, true],
        'ERRC' => ['Reverse Reactive Energy - Phase C', VARIABLETYPE_FLOAT, 'SM.kVA.3', 0.001, 3, 'ERRx', 'Blindenergie Einspeisung', 68, 48, true],
        'ERRT' => ['Reverse Reactive Energy - Total', VARIABLETYPE_FLOAT, 'SM.kVA.3', 0.001, 3, 'ERRx', 'Blindenergie Einspeisung', 68, 49, true],
        'PFA' => ['Leistungsfaktor L1', VARIABLETYPE_FLOAT, '', 1, 2, 'PFx', 'Leistungsfaktor', 70, 50, true],
        'PFB' => ['Leistungsfaktor L2', VARIABLETYPE_FLOAT, '', 1, 2, 'PFx', 'Leistungsfaktor', 70, 51, true],
        'PFC' => ['Leistungsfaktor L3', VARIABLETYPE_FLOAT, '', 1, 2, 'PFx', 'Leistungsfaktor', 70, 52, true],
        'PFT' => ['Leistungsfaktor Gesamt', VARIABLETYPE_FLOAT, '', 1, 2, 'PFx', 'Leistungsfaktor', 70, 53, true],
        'IAA' => ['Phasenwinkel Strom L1', VARIABLETYPE_FLOAT, 'SM.PhaseAngel.2', 1, 2, 'xAx', 'Phasenwinkel', 75, 54, true],
        'IAB' => ['Phasenwinkel Strom L2', VARIABLETYPE_FLOAT, 'SM.PhaseAngel.2', 1, 2, 'xAx', 'Phasenwinkel', 75, 55, true],
        'IAC' => ['Phasenwinkel Strom L3', VARIABLETYPE_FLOAT, 'SM.PhaseAngel.2', 1, 2, 'xAx', 'Phasenwinkel', 75, 56, true],
        'UAA' => ['Phasenwinkel Spannung L1-L3', VARIABLETYPE_FLOAT, 'SM.PhaseAngel.2', 1, 2, 'xAx', 'Phasenwinkel', 75, 57, true],
        'UAB' => ['Phasenwinkel Spannung L2-L3', VARIABLETYPE_FLOAT, 'SM.PhaseAngel.2', 1, 2, 'xAx', 'Phasenwinkel', 75, 58, true],
        'UAC' => ['Phasenwinkel Spannung L3-L3', VARIABLETYPE_FLOAT, 'SM.PhaseAngel.2', 1, 2, 'xAx', 'Phasenwinkel', 75, 59, true],
        'THUA' => ['Phase A voltage THD+N ', VARIABLETYPE_FLOAT, '', 1, 2, 'F_H', 'Fundamental&Harmonic', 80, 60, true],
        'THUB' => ['Phase B voltage THD+N ', VARIABLETYPE_FLOAT, '', 1, 2, 'F_H', 'Fundamental&Harmonic', 80, 61, true],
        'THUC' => ['Phase C voltage THD+N ', VARIABLETYPE_FLOAT, '', 1, 2, 'F_H', 'Fundamental&Harmonic', 80, 62, true],
        'THIA' => ['Phase A current THD+N ', VARIABLETYPE_FLOAT, '', 1, 2, 'F_H', 'Fundamental&Harmonic', 80, 63, true],
        'THIB' => ['Phase B current THD+N ', VARIABLETYPE_FLOAT, '', 1, 2, 'F_H', 'Fundamental&Harmonic', 80, 64, true],
        'THIC' => ['Phase C current THD+N ', VARIABLETYPE_FLOAT, '', 1, 2, 'F_H', 'Fundamental&Harmonic', 80, 65, true],
        'PAF' => ['Active Fundamental Power phase A ', VARIABLETYPE_FLOAT, 'SM.Watt.2', 1, 2, 'F_H', 'Fundamental&Harmonic', 80, 66, true],
        'PBF' => ['Active Fundamental Power phase B ', VARIABLETYPE_FLOAT, 'SM.Watt.2', 1, 2, 'F_H', 'Fundamental&Harmonic', 80, 67, true],
        'PCF' => ['Active Fundamental Power phase C ', VARIABLETYPE_FLOAT, 'SM.Watt.2', 1, 2, 'F_H', 'Fundamental&Harmonic', 80, 68, true],
        'PTF' => ['Active Fundamental Power all phase ', VARIABLETYPE_FLOAT, 'SM.Watt.2', 1, 2, 'F_H', 'Fundamental&Harmonic', 80, 69, true],
        'PAH' => ['Active Harmonic Power phase A ', VARIABLETYPE_FLOAT, 'SM.Watt.2', 1, 2, 'F_H', 'Fundamental&Harmonic', 80, 70, true],
        'PBH' => ['Active Harmonic Power phase B ', VARIABLETYPE_FLOAT, 'SM.Watt.2', 1, 2, 'F_H', 'Fundamental&Harmonic', 80, 71, true],
        'PCH' => ['Active Harmonic Power phase C ', VARIABLETYPE_FLOAT, 'SM.Watt.2', 1, 2, 'F_H', 'Fundamental&Harmonic', 80, 72, true],
        'PTH' => ['Active Harmonic Power all phase ', VARIABLETYPE_FLOAT, 'SM.Watt.2', 1, 2, 'F_H', 'Fundamental&Harmonic', 80, 73, true],
        'EFAF' => ['Forward Active Fundamental Energy phase A ', VARIABLETYPE_FLOAT, 'SM.kWh.3', 0.001, 3, 'F_H', 'Fundamental&Harmonic', 80, 74, true],
        'EFBF' => ['Forward Active Fundamental Energy phase B ', VARIABLETYPE_FLOAT, 'SM.kWh.3', 0.001, 3, 'F_H', 'Fundamental&Harmonic', 80, 75, true],
        'EFCF' => ['Forward Active Fundamental Energy phase C ', VARIABLETYPE_FLOAT, 'SM.kWh.3', 0.001, 3, 'F_H', 'Fundamental&Harmonic', 80, 76, true],
        'EFTF' => ['Forward Active Fundamental Energyt all phase', VARIABLETYPE_FLOAT, 'SM.kWh.3', 0.001, 3, 'F_H', 'Fundamental&Harmonic', 80, 77, true],
        'EFAH' => ['Forward Active Harmonic Energy phase A ', VARIABLETYPE_FLOAT, 'SM.kWh.3', 0.001, 3, 'F_H', 'Fundamental&Harmonic', 80, 78, true],
        'EFBH' => ['Forward Active Harmonic Energy phase B ', VARIABLETYPE_FLOAT, 'SM.kWh.3', 0.001, 3, 'F_H', 'Fundamental&Harmonic', 80, 79, true],
        'EFCH' => ['Forward Active Harmonic Energy phase C ', VARIABLETYPE_FLOAT, 'SM.kWh.3', 0.001, 3, 'F_H', 'Fundamental&Harmonic', 80, 80, true],
        'EFTH' => ['Forward Active Harmonic Energy all phase ', VARIABLETYPE_FLOAT, 'SM.kWh.3', 0.001, 3, 'F_H', 'Fundamental&Harmonic', 80, 81, true],
        'ERAF' => ['Reverse Active Fundamental Energy - Phase A', VARIABLETYPE_FLOAT, 'SM.kWh.3', 0.001, 3, 'F_H', 'Fundamental&Harmonic', 80, 82, true],
        'ERBF' => ['Reverse Active Fundamental Energy - Phase B', VARIABLETYPE_FLOAT, 'SM.kWh.3', 0.001, 3, 'F_H', 'Fundamental&Harmonic', 80, 83, true],
        'ERCF' => ['Reverse Active Fundamental Energy - Phase C', VARIABLETYPE_FLOAT, 'SM.kWh.3', 0.001, 3, 'F_H', 'Fundamental&Harmonic', 80, 84, true],
        'ERTF' => ['Reverse Active Fundamental Energy - Total', VARIABLETYPE_FLOAT, 'SM.kWh.3', 0.001, 3, 'F_H', 'Fundamental&Harmonic', 80, 85, true],
        'ERAH' => ['Reverse Active Harmonic Energy - Phase A', VARIABLETYPE_FLOAT, 'SM.kWh.3', 0.001, 3, 'F_H', 'Fundamental&Harmonic', 80, 86, true],
        'ERBH' => ['Reverse Active Harmonic Energy - Phase B', VARIABLETYPE_FLOAT, 'SM.kWh.3', 0.001, 3, 'F_H', 'Fundamental&Harmonic', 80, 87, true],
        'ERCH' => ['Reverse Active Harmonic Energy - Phase C', VARIABLETYPE_FLOAT, 'SM.kWh.3', 0.001, 3, 'F_H', 'Fundamental&Harmonic', 80, 88, true],
        'ERTH' => ['Reverse Active Harmonic Energy - Total', VARIABLETYPE_FLOAT, 'SM.kWh.3', 0.001, 3, 'F_H', 'Fundamental&Harmonic', 80, 89, true],
        'Stat ' => ['Status of Metering Chip', VARIABLETYPE_INTEGER, '', 1, 2, 'Adv', 'Advanced', 90, 90, true],
        'IN1' => ['N Line Sampled current RMS', VARIABLETYPE_FLOAT, '', 1, 2, 'Adv', 'Advanced', 90, 91, true],
        'IN0' => ['N Line calculated current RMS', VARIABLETYPE_FLOAT, '', 1, 2, 'Adv', 'Advanced', 90, 92, true],
        'TIME' => ['Operating Time', VARIABLETYPE_INTEGER, '', 1, 0, 'Adv', 'Advanced', 90, 93, true],
        'STATUS' => ['Status of the VPM ', VARIABLETYPE_INTEGER, '', 1, 0, 'Adv', 'Advanced', 90, 94, true],
        'SAMPLES' => ['Samples', VARIABLETYPE_INTEGER, '', 1, 0, 'other', 'Sonstige', 100, 95, true],
        'EVT' => ['EVT', VARIABLETYPE_FLOAT, '', 1, 3, 'other', 'Sonstige', 100, 96, true],
        'EMT' => ['EMT', VARIABLETYPE_FLOAT, '', 1, 3, 'other', 'Sonstige', 100, 97, true],
        'ERSA' => ['ERSA', VARIABLETYPE_FLOAT, '', 1, 3, 'other', 'Sonstige', 100, 98, true],
        'ERSB' => ['ERSB', VARIABLETYPE_FLOAT, '', 1, 3, 'other', 'Sonstige', 100, 99, true],
        'ERSC' => ['ERSC', VARIABLETYPE_FLOAT, '', 1, 3, 'other', 'Sonstige', 100, 100, true],
        'ERST' => ['ERST', VARIABLETYPE_FLOAT, '', 1, 3, 'other', 'Sonstige', 100, 101, true],
        'EFSA' => ['EFSA', VARIABLETYPE_FLOAT, '', 1, 3, 'other', 'Sonstige', 100, 102, true],
        'EFSB' => ['EFSB', VARIABLETYPE_FLOAT, '', 1, 3, 'other', 'Sonstige', 100, 103, true],
        'EFSC' => ['EFSC', VARIABLETYPE_FLOAT, '', 1, 3, 'other', 'Sonstige', 100, 104, true],
        'EFST' => ['EFST', VARIABLETYPE_FLOAT, '', 1, 3, 'other', 'Sonstige', 100, 105, true],
        'ERT1' => ['ERT1', VARIABLETYPE_FLOAT, '', 1, 3, 'other', 'Sonstige', 100, 106, true],
        'ERT2' => ['ERT2', VARIABLETYPE_FLOAT, '', 1, 3, 'other', 'Sonstige', 100, 107, true],
        'ERT3' => ['ERT3', VARIABLETYPE_FLOAT, '', 1, 3, 'other', 'Sonstige', 100, 108, true],
        'ERT4' => ['ERT4', VARIABLETYPE_FLOAT, '', 1, 3, 'other', 'Sonstige', 100, 109, true],
        'ERA1' => ['ERA1', VARIABLETYPE_FLOAT, '', 1, 3, 'other', 'Sonstige', 100, 110, true],
        'ERA2' => ['ERA2', VARIABLETYPE_FLOAT, '', 1, 3, 'other', 'Sonstige', 100, 111, true],
        'ERA3' => ['ERA3', VARIABLETYPE_FLOAT, '', 1, 3, 'other', 'Sonstige', 100, 112, true],
        'ERA4' => ['ERA4', VARIABLETYPE_FLOAT, '', 1, 3, 'other', 'Sonstige', 100, 113, true],
        'ERB1' => ['ERB1', VARIABLETYPE_FLOAT, '', 1, 3, 'other', 'Sonstige', 100, 114, true],
        'ERB2' => ['ERB2', VARIABLETYPE_FLOAT, '', 1, 3, 'other', 'Sonstige', 100, 115, true],
        'ERB3' => ['ERB3', VARIABLETYPE_FLOAT, '', 1, 3, 'other', 'Sonstige', 100, 116, true],
        'ERB4' => ['ERB4', VARIABLETYPE_FLOAT, '', 1, 3, 'other', 'Sonstige', 100, 117, true],
        'ERC1' => ['ERC1', VARIABLETYPE_FLOAT, '', 1, 3, 'other', 'Sonstige', 100, 118, true],
        'ERC2' => ['ERC2', VARIABLETYPE_FLOAT, '', 1, 3, 'other', 'Sonstige', 100, 119, true],
        'ERC3' => ['ERC3', VARIABLETYPE_FLOAT, '', 1, 3, 'other', 'Sonstige', 100, 120, true],
        'ERC4' => ['ERC4', VARIABLETYPE_FLOAT, '', 1, 3, 'other', 'Sonstige', 100, 121, true]

    );
}
