<?php 

declare(strict_types=1);

trait SUN_SPEC {

    /*  benötigt PHP 8

    const SunS_REGISTER_MAP = array(
        40072 => array("A",             "AC Total Current",                     "A", 0.006),
        40074 => array("AphA",          "AC Phase-A Current",                   "A", 0.001),
        40076 => array("AphB",          "AC Phase-B Current",                   "A", 0.003),
        40078 => array("AphC",          "AC Phase-C Current",                   "A", 0.002),
    
        40080 => array("PhV",           "AC Voltage Phase-to-neutral Average",  "V", 230.0),
        40082 => array("PhVphA",        "AC Voltage Phase-A-to-neutral",        "V", 230.1),
        40084 => array("PhVphB",        "AC Voltage Phase-B-to-neutral",        "V", 229.3),
        40086 => array("PhVphC",        "AC Voltage Phase-C-to-neutral",        "V", 230.4),
    
        40088 => array("PPV",           "AC Voltage Phase-to-phase Average",    "V", 400.0),
        40090 => array("PPVphAB",       "AC Voltage Phase-AB",                  "V", 400.1),
        40092 => array("PPVphBC",       "AC Voltage Phase-BC",                  "V", 399.3),
        40094 => array("PPVphCA",       "AC Voltage Phase-CA",                  "V", 400.4),
    
        40096 => array("Hz",            "AC Frequency",                         "Hz", 50.0),
        
        40098 => array("W",             "AC Power",                             "W", 1.3788),
        40100 => array("WphA",          "AC Power Phase A",                     "W", 0.2301),
        40102 => array("WphB",          "AC Power Phase B",                     "W", 0.6879),
        40104 => array("WphC",          "AC Power Phase C",                     "W", 0.4608),
    
        40106 => array("VA",            "AC Apparent Power",                    "VA", 0.6),
        40108 => array("VAphA",         "AC Apparent Power Phase A",            "VA", 0.1),
        40110 => array("VAphB",         "AC Apparent Power Phase B",            "VA", 0.2),
        40112 => array("VAphC",         "AC Apparent Power Phase C",            "VA", 0.3),
    
        40114 => array("VAR",           "AC Reactive Power",                    "VAr", 0.06),
        40116 => array("VARphA",        "AC Reactive Power Phase A",            "VAr", 0.01),
        40118 => array("VARphB",        "AC Reactive Power Phase B",            "VAr", 0.02),
        40120 => array("VARphC",        "AC Reactive Power Phase C",            "VAr", 0.03),
    
        40122 => array("PF",            "Power Factor",                         "cos", 1.0),
        40124 => array("PFphA",         "Power Factor Phase A",                 "cos", 0.99),
        40126 => array("PFphB",         "Power Factor Phase B",                 "cos", 0.98),
        40128 => array("PFphC",         "Power Factor Phase C",                 "cos", 0.97),    
    
        40130 => array("TotWhExp",      "Total Watt-hours Exported",            "Wh", 0.35),
        40132 => array("TotWhExpPhA",   "Total Watt-hours Exported phase A",    "Wh", 0.11),
        40134 => array("TotWhExpPhB",   "Total Watt-hours Exported phase B",    "Wh", 0.12),
        40136 => array("TotWhExpPhC",   "Total Watt-hours Exported phase C",    "Wh", 0.13),  
    
        40138 => array("TotWhImpPh",    "Total Watt-hours Imported",            "Wh", 0.65),
        40140 => array("TotWhImpPhA",   "Total Watt-hours Imported phase A",    "Wh", 0.21),
        40142 => array("TotWhImpPhB",   "Total Watt-hours Imported phase B",    "Wh", 0.22),
        40144 => array("TotWhImpPhC",   "Total Watt-hours Imported phase C",    "Wh", 0.23),  
    
        40146 => array("TotVAhExp",     "Total VA-hours Exported",              "VAh", 0.35),
        40148 => array("TotVAhExpPhA",  "Total VA-hours Exported phase A",      "VAh", 0.11),
        40150 => array("TotVAhExpPhB",  "Total VA-hours Exported phase B",      "VAh", 0.12),
        40152 => array("TotVAhExpPhC",  "Total VA-hours Exported phase C",      "VAh", 0.13),  
    
        40154 => array("TotVAhImp",     "Total VA-hours Imported",              "VAh", 0.65),
        40156 => array("TotVAhImpPhA",  "Total VA-hours Imported phase A",      "VAh", 0.21),
        40158 => array("TotVAhImpPhB",  "Total VA-hours Imported phase B",      "VAh", 0.22),
        40160 => array("TotVAhImpPhC",  "Total VA-hours Imported phase C",      "VAh", 0.23),  
    
    );

    public function GetSunSRegisterMap():array {
        return self::SunS_REGISTER_MAP;
    }

    */

    public function GetSunSRegisterMap():array {
        return array(
            40072 => array("A",             "AC Total Current",                     "A", 0.006),
            40074 => array("AphA",          "AC Phase-A Current",                   "A", 0.001),
            40076 => array("AphB",          "AC Phase-B Current",                   "A", 0.003),
            40078 => array("AphC",          "AC Phase-C Current",                   "A", 0.002),
        
            40080 => array("PhV",           "AC Voltage Phase-to-neutral Average",  "V", 230.0),
            40082 => array("PhVphA",        "AC Voltage Phase-A-to-neutral",        "V", 230.1),
            40084 => array("PhVphB",        "AC Voltage Phase-B-to-neutral",        "V", 229.3),
            40086 => array("PhVphC",        "AC Voltage Phase-C-to-neutral",        "V", 230.4),
        
            40088 => array("PPV",           "AC Voltage Phase-to-phase Average",    "V", 400.0),
            40090 => array("PPVphAB",       "AC Voltage Phase-AB",                  "V", 400.1),
            40092 => array("PPVphBC",       "AC Voltage Phase-BC",                  "V", 399.3),
            40094 => array("PPVphCA",       "AC Voltage Phase-CA",                  "V", 400.4),
        
            40096 => array("Hz",            "AC Frequency",                         "Hz", 50.0),
            
            40098 => array("W",             "AC Power",                             "W", 1.3788),
            40100 => array("WphA",          "AC Power Phase A",                     "W", 0.2301),
            40102 => array("WphB",          "AC Power Phase B",                     "W", 0.6879),
            40104 => array("WphC",          "AC Power Phase C",                     "W", 0.4608),
        
            40106 => array("VA",            "AC Apparent Power",                    "VA", 0.6),
            40108 => array("VAphA",         "AC Apparent Power Phase A",            "VA", 0.1),
            40110 => array("VAphB",         "AC Apparent Power Phase B",            "VA", 0.2),
            40112 => array("VAphC",         "AC Apparent Power Phase C",            "VA", 0.3),
        
            40114 => array("VAR",           "AC Reactive Power",                    "VAr", 0.06),
            40116 => array("VARphA",        "AC Reactive Power Phase A",            "VAr", 0.01),
            40118 => array("VARphB",        "AC Reactive Power Phase B",            "VAr", 0.02),
            40120 => array("VARphC",        "AC Reactive Power Phase C",            "VAr", 0.03),
        
            40122 => array("PF",            "Power Factor",                         "cos", 1.0),
            40124 => array("PFphA",         "Power Factor Phase A",                 "cos", 0.99),
            40126 => array("PFphB",         "Power Factor Phase B",                 "cos", 0.98),
            40128 => array("PFphC",         "Power Factor Phase C",                 "cos", 0.97),    
        
            40130 => array("TotWhExp",      "Total Watt-hours Exported",            "Wh", 0.35),
            40132 => array("TotWhExpPhA",   "Total Watt-hours Exported phase A",    "Wh", 0.11),
            40134 => array("TotWhExpPhB",   "Total Watt-hours Exported phase B",    "Wh", 0.12),
            40136 => array("TotWhExpPhC",   "Total Watt-hours Exported phase C",    "Wh", 0.13),  
        
            40138 => array("TotWhImpPh",    "Total Watt-hours Imported",            "Wh", 0.65),
            40140 => array("TotWhImpPhA",   "Total Watt-hours Imported phase A",    "Wh", 0.21),
            40142 => array("TotWhImpPhB",   "Total Watt-hours Imported phase B",    "Wh", 0.22),
            40144 => array("TotWhImpPhC",   "Total Watt-hours Imported phase C",    "Wh", 0.23),  
        
            40146 => array("TotVAhExp",     "Total VA-hours Exported",              "VAh", 0.35),
            40148 => array("TotVAhExpPhA",  "Total VA-hours Exported phase A",      "VAh", 0.11),
            40150 => array("TotVAhExpPhB",  "Total VA-hours Exported phase B",      "VAh", 0.12),
            40152 => array("TotVAhExpPhC",  "Total VA-hours Exported phase C",      "VAh", 0.13),  
        
            40154 => array("TotVAhImp",     "Total VA-hours Imported",              "VAh", 0.65),
            40156 => array("TotVAhImpPhA",  "Total VA-hours Imported phase A",      "VAh", 0.21),
            40158 => array("TotVAhImpPhB",  "Total VA-hours Imported phase B",      "VAh", 0.22),
            40160 => array("TotVAhImpPhC",  "Total VA-hours Imported phase C",      "VAh", 0.23),  
        
        );
    }


}

?>