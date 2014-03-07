<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/**
 * Helper to manage country codes
 * 
 * @package Framework.Helpers
 */
class CountryHelper extends AppHelper {
	
	/**
	 * Associative array of isocode2 and names of countries
	 * 
	 * @var array
	 * @since 1.0.0
	 */
	protected $_iso_to_name = array(
		"AF" => "Afghanistan",
		"AL" => "Albania",
		"DZ" => "Algeria",
		"AS" => "American Samoa",
		"AD" => "Andorra",
		"AO" => "Angola",
		"AI" => "Anguilla",
		"AQ" => "Antarctica",
		"AG" => "Antigua and Barbuda",
		"AR" => "Argentina",
		"AM" => "Armenia",
		"AW" => "Aruba",
		"AU" => "Australia",
		"AT" => "Austria",
		"AZ" => "Azerbaijan",
		"BS" => "Bahamas",
		"BH" => "Bahrain",
		"BD" => "Bangladesh",
		"BB" => "Barbados",
		"BY" => "Belarus",
		"BE" => "Belgium",
		"BZ" => "Belize",
		"BJ" => "Benin",
		"BM" => "Bermuda",
		"BT" => "Bhutan",
		"BO" => "Bolivia",
		"BA" => "Bosnia and Herzegovina",
		"BW" => "Botswana",
		"BV" => "Bouvet Island",
		"BR" => "Brazil",
		"IO" => "British Indian Ocean Territory",
		"BN" => "Brunei Darussalam",
		"BG" => "Bulgaria",
		"BF" => "Burkina Faso",
		"BI" => "Burundi",
		"KH" => "Cambodia",
		"CM" => "Cameroon",
		"CA" => "Canada",
		"CV" => "Cape Verde",
		"KY" => "Cayman Islands",
		"CF" => "Central African Republic",
		"TD" => "Chad",
		"CL" => "Chile",
		"CN" => "China",
		"CX" => "Christmas Island",
		"CC" => "Cocos Keeling Islands",
		"CO" => "Colombia",
		"KM" => "Comoros",
		"CG" => "Congo",
		"CD" => "Congo, the Democratic Republic of the",
		"CK" => "Cook Islands",
		"CR" => "Costa Rica",
		"CI" => "Cote D'Ivoire",
		"HR" => "Croatia",
		"CU" => "Cuba",
		"CY" => "Cyprus",
		"CZ" => "Czech Republic",
		"DK" => "Denmark",
		"DJ" => "Djibouti",
		"DM" => "Dominica",
		"DO" => "Dominican Republic",
		"EC" => "Ecuador",
		"EG" => "Egypt",
		"SV" => "El Salvador",
		"GQ" => "Equatorial Guinea",
		"ER" => "Eritrea",
		"EE" => "Estonia",
		"ET" => "Ethiopia",
		"FK" => "Falkland Islands Malvinas",
		"FO" => "Faroe Islands",
		"FJ" => "Fiji",
		"FI" => "Finland",
		"FR" => "France",
		"GF" => "French Guiana",
		"PF" => "French Polynesia",
		"TF" => "French Southern Territories",
		"GA" => "Gabon",
		"GM" => "Gambia",
		"GE" => "Georgia",
		"DE" => "Germany",
		"GH" => "Ghana",
		"GI" => "Gibraltar",
		"GR" => "Greece",
		"GL" => "Greenland",
		"GD" => "Grenada",
		"GP" => "Guadeloupe",
		"GU" => "Guam",
		"GT" => "Guatemala",
		"GN" => "Guinea",
		"GW" => "Guinea-Bissau",
		"GY" => "Guyana",
		"HT" => "Haiti",
		"HM" => "Heard Island and Mcdonald Islands",
		"VA" => "Holy See Vatican City State",
		"HN" => "Honduras",
		"HK" => "Hong Kong",
		"HU" => "Hungary",
		"IS" => "Iceland",
		"IN" => "India",
		"ID" => "Indonesia",
		"IR" => "Iran, Islamic Republic of",
		"IQ" => "Iraq",
		"IE" => "Ireland",
		"IL" => "Israel",
		"IT" => "Italy",
		"JM" => "Jamaica",
		"JP" => "Japan",
		"JO" => "Jordan",
		"KZ" => "Kazakhstan",
		"KE" => "Kenya",
		"KI" => "Kiribati",
		"KP" => "Korea, Democratic People's Republic of",
		"KR" => "Korea, Republic of",
		"KW" => "Kuwait",
		"KG" => "Kyrgyzstan",
		"LA" => "Lao People's Democratic Republic",
		"LV" => "Latvia",
		"LB" => "Lebanon",
		"LS" => "Lesotho",
		"LR" => "Liberia",
		"LY" => "Libyan Arab Jamahiriya",
		"LI" => "Liechtenstein",
		"LT" => "Lithuania",
		"LU" => "Luxembourg",
		"MO" => "Macao",
		"MK" => "Macedonia, the Former Yugoslav Republic of",
		"MG" => "Madagascar",
		"MW" => "Malawi",
		"MY" => "Malaysia",
		"MV" => "Maldives",
		"ML" => "Mali",
		"MT" => "Malta",
		"MH" => "Marshall Islands",
		"MQ" => "Martinique",
		"MR" => "Mauritania",
		"MU" => "Mauritius",
		"YT" => "Mayotte",
		"MX" => "Mexico",
		"FM" => "Micronesia, Federated States of",
		"MD" => "Moldova, Republic of",
		"MC" => "Monaco",
		"MN" => "Mongolia",
		"ME" => "Montenegro",
		"MS" => "Montserrat",
		"MA" => "Morocco",
		"MZ" => "Mozambique",
		"MM" => "Myanmar",
		"NA" => "Namibia",
		"NR" => "Nauru",
		"NP" => "Nepal",
		"NL" => "Netherlands",
		"AN" => "Netherlands Antilles",
		"NC" => "New Caledonia",
		"NZ" => "New Zealand",
		"NI" => "Nicaragua",
		"NE" => "Niger",
		"NG" => "Nigeria",
		"NU" => "Niue",
		"NF" => "Norfolk Island",
		"MP" => "Northern Mariana Islands",
		"NO" => "Norway",
		"OM" => "Oman",
		"PK" => "Pakistan",
		"PW" => "Palau",
		"PS" => "Palestinian Territory, Occupied",
		"PA" => "Panama",
		"PG" => "Papua New Guinea",
		"PY" => "Paraguay",
		"PE" => "Peru",
		"PH" => "Philippines",
		"PN" => "Pitcairn",
		"PL" => "Poland",
		"PT" => "Portugal",
		"PR" => "Puerto Rico",
		"QA" => "Qatar",
		"RE" => "Reunion",
		"RO" => "Romania",
		"RU" => "Russian Federation",
		"RW" => "Rwanda",
		"SH" => "Saint Helena",
		"KN" => "Saint Kitts and Nevis",
		"LC" => "Saint Lucia",
		"PM" => "Saint Pierre and Miquelon",
		"VC" => "Saint Vincent and the Grenadines",
		"WS" => "Samoa",
		"SM" => "San Marino",
		"ST" => "Sao Tome and Principe",
		"SA" => "Saudi Arabia",
		"SN" => "Senegal",
		"RS" => "Serbia",
		"SC" => "Seychelles",
		"SL" => "Sierra Leone",
		"SG" => "Singapore",
		"SK" => "Slovakia",
		"SI" => "Slovenia",
		"SB" => "Solomon Islands",
		"SO" => "Somalia",
		"ZA" => "South Africa",
		"GS" => "South Georgia and the South Sandwich Islands",
		"ES" => "Spain",
		"LK" => "Sri Lanka",
		"SD" => "Sudan",
		"SR" => "Suriname",
		"SJ" => "Svalbard and Jan Mayen",
		"SZ" => "Swaziland",
		"SE" => "Sweden",
		"CH" => "Switzerland",
		"SY" => "Syrian Arab Republic",
		"TW" => "Taiwan, Province of China",
		"TJ" => "Tajikistan",
		"TZ" => "Tanzania, United Republic of",
		"TH" => "Thailand",
		"TL" => "Timor-Leste",
		"TG" => "Togo",
		"TK" => "Tokelau",
		"TO" => "Tonga",
		"TT" => "Trinidad and Tobago",
		"TN" => "Tunisia",
		"TR" => "Turkey",
		"TM" => "Turkmenistan",
		"TC" => "Turks and Caicos Islands",
		"TV" => "Tuvalu",
		"UG" => "Uganda",
		"UA" => "Ukraine",
		"AE" => "United Arab Emirates",
		"GB" => "United Kingdom",
		"US" => "United States",
		"UM" => "United States Minor Outlying Islands",
		"UY" => "Uruguay",
		"UZ" => "Uzbekistan",
		"VU" => "Vanuatu",
		"VE" => "Venezuela",
		"VN" => "Viet Nam",
		"VG" => "Virgin Islands, British",
		"VI" => "Virgin Islands, U.s.",
		"WF" => "Wallis and Futuna",
		"EH" => "Western Sahara",
		"YE" => "Yemen",
		"ZM" => "Zambia",
		"ZW" => "Zimbabwe");

		
	/**
	 * Associative array of isocode2 to isocode3 country codes
	 * 
	 * @var array
	 * @since 1.0.0
	 */
	protected $_iso_to_iso3 = array(
		"AF" => "AFG",
		"AL" => "ALB",
		"DZ" => "DZA",
		"AS" => "ASM",
		"AD" => "AND",
		"AO" => "AGO",
		"AI" => "AIA",
		"AQ" => "",
		"AG" => "ATG",
		"AR" => "ARG",
		"AM" => "ARM",
		"AW" => "ABW",
		"AU" => "AUS",
		"AT" => "AUT",
		"AZ" => "AZE",
		"BS" => "BHS",
		"BH" => "BHR",
		"BD" => "BGD",
		"BB" => "BRB",
		"BY" => "BLR",
		"BE" => "BEL",
		"BZ" => "BLZ",
		"BJ" => "BEN",
		"BM" => "BMU",
		"BT" => "BTN",
		"BO" => "BOL",
		"BA" => "BIH",
		"BW" => "BWA",
		"BV" => "",
		"BR" => "BRA",
		"IO" => "",
		"BN" => "BRN",
		"BG" => "BGR",
		"BF" => "BFA",
		"BI" => "BDI",
		"KH" => "KHM",
		"CM" => "CMR",
		"CA" => "CAN",
		"CV" => "CPV",
		"KY" => "CYM",
		"CF" => "CAF",
		"TD" => "TCD",
		"CL" => "CHL",
		"CN" => "CHN",
		"CX" => "",
		"CC" => "",
		"CO" => "COL",
		"KM" => "COM",
		"CG" => "COG",
		"CD" => "COD",
		"CK" => "COK",
		"CR" => "CRI",
		"CI" => "CIV",
		"HR" => "HRV",
		"CU" => "CUB",
		"CY" => "CYP",
		"CZ" => "CZE",
		"DK" => "DNK",
		"DJ" => "DJI",
		"DM" => "DMA",
		"DO" => "DOM",
		"EC" => "ECU",
		"EG" => "EGY",
		"SV" => "SLV",
		"GQ" => "GNQ",
		"ER" => "ERI",
		"EE" => "EST",
		"ET" => "ETH",
		"FK" => "FLK",
		"FO" => "FRO",
		"FJ" => "FJI",
		"FI" => "FIN",
		"FR" => "FRA",
		"GF" => "GUF",
		"PF" => "PYF",
		"TF" => "",
		"GA" => "GAB",
		"GM" => "GMB",
		"GE" => "GEO",
		"DE" => "DEU",
		"GH" => "GHA",
		"GI" => "GIB",
		"GR" => "GRC",
		"GL" => "GRL",
		"GD" => "GRD",
		"GP" => "GLP",
		"GU" => "GUM",
		"GT" => "GTM",
		"GN" => "GIN",
		"GW" => "GNB",
		"GY" => "GUY",
		"HT" => "HTI",
		"HM" => "",
		"VA" => "VAT",
		"HN" => "HND",
		"HK" => "HKG",
		"HU" => "HUN",
		"IS" => "ISL",
		"IN" => "IND",
		"ID" => "IDN",
		"IR" => "IRN",
		"IQ" => "IRQ",
		"IE" => "IRL",
		"IL" => "ISR",
		"IT" => "ITA",
		"JM" => "JAM",
		"JP" => "JPN",
		"JO" => "JOR",
		"KZ" => "KAZ",
		"KE" => "KEN",
		"KI" => "KIR",
		"KP" => "PRK",
		"KR" => "KOR",
		"KW" => "KWT",
		"KG" => "KGZ",
		"LA" => "LAO",
		"LV" => "LVA",
		"LB" => "LBN",
		"LS" => "LSO",
		"LR" => "LBR",
		"LY" => "LBY",
		"LI" => "LIE",
		"LT" => "LTU",
		"LU" => "LUX",
		"MO" => "MAC",
		"MK" => "MKD",
		"MG" => "MDG",
		"MW" => "MWI",
		"MY" => "MYS",
		"MV" => "MDV",
		"ML" => "MLI",
		"MT" => "MLT",
		"MH" => "MHL",
		"MQ" => "MTQ",
		"MR" => "MRT",
		"MU" => "MUS",
		"YT" => "",
		"MX" => "MEX",
		"FM" => "FSM",
		"MD" => "MDA",
		"MC" => "MCO",
		"MN" => "MNG",
		"MS" => "MSR",
		"ME" => "MNE",
		"MA" => "MAR",
		"MZ" => "MOZ",
		"MM" => "MMR",
		"NA" => "NAM",
		"NR" => "NRU",
		"NP" => "NPL",
		"NL" => "NLD",
		"AN" => "ANT",
		"NC" => "NCL",
		"NZ" => "NZL",
		"NI" => "NIC",
		"NE" => "NER",
		"NG" => "NGA",
		"NU" => "NIU",
		"NF" => "NFK",
		"MP" => "MNP",
		"NO" => "NOR",
		"OM" => "OMN",
		"PK" => "PAK",
		"PW" => "PLW",
		"PS" => "",
		"PA" => "PAN",
		"PG" => "PNG",
		"PY" => "PRY",
		"PE" => "PER",
		"PH" => "PHL",
		"PN" => "PCN",
		"PL" => "POL",
		"PT" => "PRT",
		"PR" => "PRI",
		"QA" => "QAT",
		"RE" => "REU",
		"RO" => "ROU",
		"RU" => "RUS",
		"RW" => "RWA",
		"SH" => "SHN",
		"KN" => "KNA",
		"LC" => "LCA",
		"PM" => "SPM",
		"VC" => "VCT",
		"WS" => "WSM",
		"SM" => "SMR",
		"ST" => "STP",
		"SA" => "SAU",
		"SN" => "SEN",
		"RS" => "SRB",
		"SC" => "SYC",
		"SL" => "SLE",
		"SG" => "SGP",
		"SK" => "SVK",
		"SI" => "SVN",
		"SB" => "SLB",
		"SO" => "SOM",
		"ZA" => "ZAF",
		"GS" => "",
		"ES" => "ESP",
		"LK" => "LKA",
		"SD" => "SDN",
		"SR" => "SUR",
		"SJ" => "SJM",
		"SZ" => "SWZ",
		"SE" => "SWE",
		"CH" => "CHE",
		"SY" => "SYR",
		"TW" => "TWN",
		"TJ" => "TJK",
		"TZ" => "TZA",
		"TH" => "THA",
		"TL" => "",
		"TG" => "TGO",
		"TK" => "TKL",
		"TO" => "TON",
		"TT" => "TTO",
		"TN" => "TUN",
		"TR" => "TUR",
		"TM" => "TKM",
		"TC" => "TCA",
		"TV" => "TUV",
		"UG" => "UGA",
		"UA" => "UKR",
		"AE" => "ARE",
		"GB" => "GBR",
		"US" => "USA",
		"UM" => "",
		"UY" => "URY",
		"UZ" => "UZB",
		"VU" => "VUT",
		"VE" => "VEN",
		"VN" => "VNM",
		"VG" => "VGB",
		"VI" => "VIR",
		"WF" => "WLF",
		"EH" => "ESH",
		"YE" => "YEM",
		"ZM" => "ZMB",
		"ZW" => "ZWE");

	/**
	 * List of EU countries
	 * 
	 * @var array
	 * @since 1.0.0
	 */
	protected $_eu = array(
		"BE", "BG", "DK", "DE", "EE",
		"FI", "FR", "GR", "IE", "IT",
		"LV", "LT", "LU", "MT", "NL",
		"AT", "PL", "PT", "RO", "SE",
		"SK", "SI", "ES", "CZ", "HU",
		"CY", "GB");

	/**
	 * Convert isocode2 to country name
	 *
	 * @param string $iso_code The isocode2 code
	 * 
	 * @return string The country name
	 * 
	 * @since 1.0.0
	 */
	public function isoToName($iso_code) {
		
		if (array_key_exists($iso_code, $this->_iso_to_name)) {
			return $this->_iso_to_name[$iso_code];
		}

		return false;
	}

	/**
	 * Convert isocode3 to country name
	 *
	 * @param string $iso_code The isocode3 code
	 * 
	 * @return string The county name
	 * 
	 * @since 1.0.0
	 */
	public function iso3ToName($iso_code) {
		return $this->isoToName($this->iso3ToIso($iso_code));
	}

	/**
	 * Convert isocode2 to isocode3
	 *
	 * @param string $iso_code The isocode2 code
	 * 
	 * @return string The isocode3 code
	 * 
	 * @since 1.0.0
	 */
	public function isoToIso3($iso_code) {

		if (array_key_exists($iso_code, $this->_iso_to_iso3)) {
			return $this->_iso_to_iso3[$iso_code];
		}

		return false;
	}

	/**
	 * Convert isocode3 to isocode2
	 *
	 * @param string $iso_code The isocode3 code
	 * 
	 * @return string The isocode2 code
	 * 
	 * @since 1.0.0
	 */
	public function iso3ToIso($iso_code) {
		return array_search($iso_code, $this->_iso_to_iso3);
	}

	/**
	 * Check if a country is in the EU 
	 * 
	 * @param string $iso_code The isocode2 code of the country
	 * 
	 * @return boolean If the country is in the EU
	 * 
	 * @since 1.0.0
	 */
	public function isEU($iso_code) {
		return in_array($iso_code, $this->_eu);
	}

	/**
	 * Get the isocode2 to name associative array
	 * 
	 * @return array The associative array of isocode2 => name
	 * 
	 * @since 1.0.0
	 */
	public function getIsoToNameMapping() {
		return $this->_iso_to_name;
	}

	/**
	 * Get the isocode3 to name associative array
	 * 
	 * @return array The associative array of isocode3 => name
	 * 
	 * @since 1.0.0
	 */
	public function getIsoToIso3Mapping() {
		return $this->_iso_to_iso3;
	}

}