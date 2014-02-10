<?php
// update.php updates data from WIOS and Politechnika Warszawska websites, calculates CAQI, AQI, percent of limit and writes results to data.db database. It shoult be run every 10 or 15 minutes.
// Version: 1.3, 2014-02-10
// Created by Piotr Lyczko, http://google.com/+PiotrLyczko

// Config
$htmlurl = '';
$xmlurl = '';
$csvurl = '';
$alerturl = '';
$province = 'malopolskie';
$provincedesc = 'województwo małopolskie';

$stations = array(
'005'=>array(
'county'=>'krakow',
'countydesc'=>'Kraków',
'city'=>'krakow',
'citydesc'=>'Kraków',
'location'=>'krasinskiego',
'locationdesc'=>'Al. Krasińskiego',
'lat'=>50.057678,
'long'=>19.926189,
'locationtype'=>'stacja komunikacyjna',
'locationparameters'=>'pm10,pm2.5,no2,nox,no,so2,co,caqi,aqi'), 

'006'=>array(
'county'=>'krakow',
'countydesc'=>'Kraków',
'city'=>'krakow',
'citydesc'=>'Kraków',
'location'=>'bulwarowa',
'locationdesc'=>'ul. Bulwarowa',
'lat'=>50.069308,
'long'=>20.053492,
'locationtype'=>'stacja w strefie oddziaływania przemysłu',
'locationparameters'=>'pm10,pm2.5,no2,nox,no,so2,co,c6h6,caqi,aqi,temperature,pressure'), 

'015'=>array(
'county'=>'krakow',
'countydesc'=>'Kraków',
'city'=>'krakow',
'citydesc'=>'Kraków',
'location'=>'bujaka',
'locationdesc'=>'ul. Bujaka',
'lat'=>50.010575,
'long'=>19.949189,
'locationtype'=>'stacja tła miejskiego',
'locationparameters'=>'pm10,pm2.5,no2,nox,no,so2,o3,caqi,aqi'),

'003'=>array(
'county'=>'tarnow',
'countydesc'=>'Tarnów',
'city'=>'tarnow',
'citydesc'=>'Tarnów',
'location'=>'bitwypodstudziankami',
'locationdesc'=>'ul. Bitwy pod Studziankami',
'lat'=>50.020169,
'long'=>21.004167,
'locationtype'=>'stacja tła miejskiego',
'locationparameters'=>'pm10,no2,nox,no,so2,co,o3,caqi,aqi'),

'007'=>array(
'county'=>'nowysacz',
'countydesc'=>'Nowy Sącz',
'city'=>'nowysacz',
'citydesc'=>'Nowy Sącz',
'location'=>'nadbrzezna',
'locationdesc'=>'ul. Nadbrzeżna',
'lat'=>49.619281,
'long'=>20.714403,
'locationtype'=>'stacja tła miejskiego',
'locationparameters'=>'pm10,no2,nox,no,so2,caqi,aqi'),

'009'=>array(
'county'=>'olkuski',
'countydesc'=>'powiat olkuski',
'city'=>'olkusz',
'citydesc'=>'Olkusz',
'location'=>'nullo',
'locationdesc'=>'ul. Nullo',
'lat'=>50.277569,
'long'=>19.569869,
'locationtype'=>'stacja tła miejskiego',
'locationparameters'=>'pm10,no2,nox,no,so2,co,caqi,aqi'),

'004'=>array(
'county'=>'krakowski',
'countydesc'=>'powiat krakowski',
'city'=>'skawina',
'citydesc'=>'Skawina',
'location'=>'ogrody',
'locationdesc'=>'os. Ogrody',
'lat'=>49.971047,
'long'=>19.830422,
'locationtype'=>'stacja tła miejskiego',
'locationparameters'=>'pm10,no2,nox,no,so2,caqi,aqi'),

'013'=>array(
'county'=>'suski',
'countydesc'=>'powiat suski',
'city'=>'suchabeskidzka',
'citydesc'=>'Sucha Beskidzka',
'location'=>'handlowa',
'locationdesc'=>'ul. Handlowa',
'lat'=>49.740644,
'long'=>19.588325,
'locationtype'=>'stacja tła miejskiego',
'locationparameters'=>'pm10,no2,nox,no,so2,c6h6,caqi,aqi'),

'014'=>array(
'county'=>'wielicki',
'countydesc'=>'powiat wielicki',
'city'=>'szarow',
'citydesc'=>'Szarów',
'location'=>'szarow',
'locationdesc'=>'Szarów',
'lat'=>50.007014,
'long'=>20.258475,
'locationtype'=>'stacja tła podmiejskiego',
'locationparameters'=>'no2,nox,no,o3,caqi,aqi'),

'011'=>array(
'county'=>'gorlicki',
'countydesc'=>'powiat gorlicki',
'city'=>'szymbark',
'citydesc'=>'Szymbark',
'location'=>'szymbark',
'locationdesc'=>'Szymbark',
'lat'=>49.633714,
'long'=>21.116833,
'locationtype'=>'stacja tła regionalnego',
'locationparameters'=>'no2,nox,no,so2,o3,caqi,aqi'),

'010'=>array(
'county'=>'chrzanowski',
'countydesc'=>'powiat chrzanowski',
'city'=>'trzebinia',
'citydesc'=>'Trzebinia',
'location'=>'zwm',
'locationdesc'=>'os. Związku Walki Młodych',
'lat'=>50.159406,
'long'=>19.477464,
'locationtype'=>'stacja tła miejskiego',
'locationparameters'=>'pm10,no2,nox,no,so2,co,o3,caqi,aqi,temperature,pressure'),

'008'=>array(
'county'=>'tatrzanski',
'countydesc'=>'powiat tatrzański',
'city'=>'zakopane',
'citydesc'=>'Zakopane',
'location'=>'sienkiewicza',
'locationdesc'=>'ul. Sienkiewicza',
'lat'=>49.293564,
'long'=>19.960083,
'locationtype'=>'stacja tła miejskiego',
'locationparameters'=>'pm10,no2,nox,no,so2,co,o3,caqi,aqi')
);

$citieslocationtype = 'wartość maksymalna dla obszaru miasta';
$citieslocationparameters = 'pm10,pm2.5,no2,so2,co,o3,caqi,aqi,temperature,pressure,windspeed,winddirection,clouds,precipitation';
$cities = array(
'krakow'=>array(
'county'=>'krakow',
'countydesc'=>'Kraków',
'city'=>'krakow',
'citydesc'=>'Kraków',
'location'=>null,
'locationdesc'=>null,
'lat'=>50.061389,
'long'=>19.938333,
'locationtype'=>$citieslocationtype,
'locationparameters'=>$citieslocationparameters),

'tarnow'=>array(
'county'=>'tarnow',
'countydesc'=>'Tarnów',
'city'=>'tarnow',
'citydesc'=>'Tarnów',
'location'=>null,
'locationdesc'=>null,
'lat'=>50.0125,
'long'=>20.988333,
'locationtype'=>$citieslocationtype,
'locationparameters'=>$citieslocationparameters),

'nowysacz'=>array(
'county'=>'nowysacz',
'countydesc'=>'Nowy Sącz',
'city'=>'nowysacz',
'citydesc'=>'Nowy Sącz',
'location'=>null,
'locationdesc'=>null,
'lat'=>49.616667,
'long'=>20.7,
'locationtype'=>$citieslocationtype,
'locationparameters'=>$citieslocationparameters),

'bochnia'=>array(
'county'=>'bochenski',
'countydesc'=>'powiat bocheński',
'city'=>'bochnia',
'citydesc'=>'Bochnia',
'location'=>null,
'locationdesc'=>null,
'lat'=>49.968889,
'long'=>20.43,
'locationtype'=>$citieslocationtype,
'locationparameters'=>$citieslocationparameters),

'brzesko'=>array(
'county'=>'brzeski',
'countydesc'=>'powiat brzeski',
'city'=>'brzesko',
'citydesc'=>'Brzesko',
'location'=>null,
'locationdesc'=>null,
'lat'=>49.968889,
'long'=>20.606389,
'locationtype'=>$citieslocationtype,
'locationparameters'=>$citieslocationparameters),

'chrzanow'=>array(
'county'=>'chrzanowski',
'countydesc'=>'powiat chrzanowski',
'city'=>'chrzanow',
'citydesc'=>'Chrzanów',
'location'=>null,
'locationdesc'=>null,
'lat'=>50.133333,
'long'=>19.4,
'locationtype'=>$citieslocationtype,
'locationparameters'=>$citieslocationparameters),

'dabrowatarnowska'=>array(
'county'=>'dabrowski',
'countydesc'=>'powiat dąbrowski',
'city'=>'dabrowatarnowska',
'citydesc'=>'Dąbrowa Tarnowska',
'location'=>null,
'locationdesc'=>null,
'lat'=>50.174722,
'long'=>20.986389,
'locationtype'=>$citieslocationtype,
'locationparameters'=>$citieslocationparameters),

'gorlice'=>array(
'county'=>'gorlicki',
'countydesc'=>'powiat gorlicki',
'city'=>'gorlice',
'citydesc'=>'Gorlice',
'location'=>null,
'locationdesc'=>null,
'lat'=>49.654444,
'long'=>21.159167,
'locationtype'=>$citieslocationtype,
'locationparameters'=>$citieslocationparameters),

'krynicazdroj'=>array(
'county'=>'nowosadecki',
'countydesc'=>'powiat nowosądecki',
'city'=>'krynicazdroj',
'citydesc'=>'Krynica Zdrój',
'location'=>null,
'locationdesc'=>null,
'lat'=>49.411667,
'long'=>20.955,
'locationtype'=>$citieslocationtype,
'locationparameters'=>$citieslocationparameters),

'limanowa'=>array(
'county'=>'limanowski',
'countydesc'=>'powiat limanowski',
'city'=>'limanowa',
'citydesc'=>'Limanowa',
'location'=>null,
'locationdesc'=>null,
'lat'=>49.701667,
'long'=>20.425556,
'locationtype'=>$citieslocationtype,
'locationparameters'=>$citieslocationparameters),

'miechow'=>array(
'county'=>'miechowski',
'countydesc'=>'powiat miechowski',
'city'=>'miechow',
'citydesc'=>'Miechów',
'location'=>null,
'locationdesc'=>null,
'lat'=>50.357778,
'long'=>20.0325,
'locationtype'=>$citieslocationtype,
'locationparameters'=>$citieslocationparameters),

'myslenice'=>array(
'county'=>'myslenicki',
'countydesc'=>'powiat myślenicki',
'city'=>'myslenice',
'citydesc'=>'Myślenice',
'location'=>null,
'locationdesc'=>null,
'lat'=>49.833333,
'long'=>19.940556,
'locationtype'=>$citieslocationtype,
'locationparameters'=>$citieslocationparameters),

'nowytarg'=>array(
'county'=>'nowotarski',
'countydesc'=>'powiat nowotarski',
'city'=>'nowytarg',
'citydesc'=>'Nowy Targ',
'location'=>null,
'locationdesc'=>null,
'lat'=>49.477778,
'long'=>20.03,
'locationtype'=>$citieslocationtype,
'locationparameters'=>$citieslocationparameters),

'olkusz'=>array(
'county'=>'olkuski',
'countydesc'=>'powiat olkuski',
'city'=>'olkusz',
'citydesc'=>'Olkusz',
'location'=>null,
'locationdesc'=>null,
'lat'=>50.279167,
'long'=>19.559722,
'locationtype'=>$citieslocationtype,
'locationparameters'=>$citieslocationparameters),

'oswiecim'=>array(
'county'=>'oswiecimski',
'countydesc'=>'powiat oświęcimski',
'city'=>'oswiecim',
'citydesc'=>'Oświęcim',
'location'=>null,
'locationdesc'=>null,
'lat'=>50.039167,
'long'=>19.220833,
'locationtype'=>$citieslocationtype,
'locationparameters'=>$citieslocationparameters),

'proszowice'=>array(
'county'=>'proszowicki',
'countydesc'=>'powiat proszowicki',
'city'=>'proszowice',
'citydesc'=>'Proszowice',
'location'=>null,
'locationdesc'=>null,
'lat'=>50.193139,
'long'=>20.288717,
'locationtype'=>$citieslocationtype,
'locationparameters'=>$citieslocationparameters),

'skawina'=>array(
'county'=>'krakowski',
'countydesc'=>'powiat krakowski',
'city'=>'skawina',
'citydesc'=>'Skawina',
'location'=>null,
'locationdesc'=>null,
'lat'=>49.975,
'long'=>19.828333,
'locationtype'=>$citieslocationtype,
'locationparameters'=>$citieslocationparameters),

'suchabeskidzka'=>array(
'county'=>'suski',
'countydesc'=>'powiat suski',
'city'=>'suchabeskidzka',
'citydesc'=>'Sucha Beskidzka',
'location'=>null,
'locationdesc'=>null,
'lat'=>49.740278,
'long'=>19.588611,
'locationtype'=>$citieslocationtype,
'locationparameters'=>$citieslocationparameters),

'trzebina'=>array(
'county'=>'chrzanowski',
'countydesc'=>'powiat chrzanowski',
'city'=>'trzebina',
'citydesc'=>'Trzebinia',
'location'=>null,
'locationdesc'=>null,
'lat'=>50.159722,
'long'=>19.470556,
'locationtype'=>$citieslocationtype,
'locationparameters'=>$citieslocationparameters),

'tuchow'=>array(
'county'=>'tarnowski',
'countydesc'=>'powiat tarnowski',
'city'=>'tuchow',
'citydesc'=>'Tuchów',
'location'=>null,
'locationdesc'=>null,
'lat'=>49.895,
'long'=>21.054167,
'locationtype'=>$citieslocationtype,
'locationparameters'=>$citieslocationparameters),

'wadowice'=>array(
'county'=>'wadowicki',
'countydesc'=>'powiat wadowicki',
'city'=>'wadowice',
'citydesc'=>'Wadowice',
'location'=>null,
'locationdesc'=>null,
'lat'=>49.883333,
'long'=>19.5,
'locationtype'=>$citieslocationtype,
'locationparameters'=>$citieslocationparameters),

'wieliczka'=>array(
'county'=>'wielicki',
'countydesc'=>'powiat wielicki',
'city'=>'wieliczka',
'citydesc'=>'Wieliczka',
'location'=>null,
'locationdesc'=>null,
'lat'=>49.986111,
'long'=>20.061667,
'locationtype'=>$citieslocationtype,
'locationparameters'=>$citieslocationparameters),

'zakopane'=>array(
'county'=>'tatrzanski',
'countydesc'=>'powiat tatrzański',
'city'=>'zakopane',
'citydesc'=>'Zakopane',
'location'=>null,
'locationdesc'=>null,
'lat'=>49.3,
'long'=>19.95,
'locationtype'=>$citieslocationtype,
'locationparameters'=>$citieslocationparameters),
);

$countieslocationtype = 'wartość maksymalna dla obszaru powiatu';
$countieslocationparameters = 'pm10,pm2.5,no2,so2,co,o3,caqi,aqi,temperature,pressure,windspeed,winddirection,clouds,precipitation';
$counties = array(
'bochenski'=>array(
'county'=>'bochenski',
'countydesc'=>'powiat bocheński',
'city'=>null,
'citydesc'=>null,
'location'=>null,
'locationdesc'=>null,
'lat'=>49.968889,
'long'=>20.43,
'locationtype'=>$countieslocationtype,
'locationparameters'=>$countieslocationparameters),

'brzeski'=>array(
'county'=>'brzeski',
'countydesc'=>'powiat brzeski',
'city'=>null,
'citydesc'=>null,
'location'=>null,
'locationdesc'=>null,
'lat'=>49.968889,
'long'=>20.606389,
'locationtype'=>$countieslocationtype,
'locationparameters'=>$countieslocationparameters),

'chrzanowski'=>array(
'county'=>'chrzanowski',
'countydesc'=>'powiat chrzanowski',
'city'=>null,
'citydesc'=>null,
'location'=>null,
'locationdesc'=>null,
'lat'=>50.133333,
'long'=>19.4,
'locationtype'=>$countieslocationtype,
'locationparameters'=>$countieslocationparameters),

'dabrowski'=>array(
'county'=>'dabrowski',
'countydesc'=>'powiat dąbrowski',
'city'=>null,
'citydesc'=>null,
'location'=>null,
'locationdesc'=>null,
'lat'=>50.174722,
'long'=>20.986389,
'locationtype'=>$countieslocationtype,
'locationparameters'=>$countieslocationparameters),

'gorlicki'=>array(
'county'=>'gorlicki',
'countydesc'=>'powiat gorlicki',
'city'=>null,
'citydesc'=>null,
'location'=>null,
'locationdesc'=>null,
'lat'=>49.654444,
'long'=>21.159167,
'locationtype'=>$countieslocationtype,
'locationparameters'=>$countieslocationparameters),

'krakowski'=>array(
'county'=>'krakowski',
'countydesc'=>'powiat krakowski',
'city'=>null,
'citydesc'=>null,
'location'=>null,
'locationdesc'=>null,
'lat'=>50.174328,
'long'=>19.875638,
'locationtype'=>$countieslocationtype,
'locationparameters'=>$countieslocationparameters),

'limanowski'=>array(
'county'=>'limanowski',
'countydesc'=>'powiat limanowski',
'city'=>null,
'citydesc'=>null,
'location'=>null,
'locationdesc'=>null,
'lat'=>49.701667,
'long'=>20.425556,
'locationtype'=>$countieslocationtype,
'locationparameters'=>$countieslocationparameters),

'miechowski'=>array(
'county'=>'miechowski',
'countydesc'=>'powiat miechowski',
'city'=>null,
'citydesc'=>null,
'location'=>null,
'locationdesc'=>null,
'lat'=>50.357778,
'long'=>20.0325,
'locationtype'=>$countieslocationtype,
'locationparameters'=>$countieslocationparameters),

'myslenicki'=>array(
'county'=>'myslenicki',
'countydesc'=>'powiat myślenicki',
'city'=>null,
'citydesc'=>null,
'location'=>null,
'locationdesc'=>null,
'lat'=>49.833333,
'long'=>19.940556,
'locationtype'=>$countieslocationtype,
'locationparameters'=>$countieslocationparameters),

'nowosadecki'=>array(
'county'=>'nowosadecki',
'countydesc'=>'powiat nowosądecki',
'city'=>null,
'citydesc'=>null,
'location'=>null,
'locationdesc'=>null,
'lat'=>49.570589,
'long'=>20.845111,
'locationtype'=>$countieslocationtype,
'locationparameters'=>$countieslocationparameters),

'nowotarski'=>array(
'county'=>'nowotarski',
'countydesc'=>'powiat nowotarski',
'city'=>null,
'citydesc'=>null,
'location'=>null,
'locationdesc'=>null,
'lat'=>49.477778,
'long'=>20.03,
'locationtype'=>$countieslocationtype,
'locationparameters'=>$countieslocationparameters),

'olkuski'=>array(
'county'=>'olkuski',
'countydesc'=>'powiat olkuski',
'city'=>null,
'citydesc'=>null,
'location'=>null,
'locationdesc'=>null,
'lat'=>50.279167,
'long'=>19.559722,
'locationtype'=>$countieslocationtype,
'locationparameters'=>$countieslocationparameters),

'oswiecimski'=>array(
'county'=>'oswiecimski',
'countydesc'=>'powiat oświęcimski',
'city'=>null,
'citydesc'=>null,
'location'=>null,
'locationdesc'=>null,
'lat'=>50.039167,
'long'=>19.220833,
'locationtype'=>$countieslocationtype,
'locationparameters'=>$countieslocationparameters),

'proszowicki'=>array(
'county'=>'proszowicki',
'countydesc'=>'powiat proszowicki',
'city'=>null,
'citydesc'=>null,
'location'=>null,
'locationdesc'=>null,
'lat'=>50.193139,
'long'=>20.288717,
'locationtype'=>$countieslocationtype,
'locationparameters'=>$countieslocationparameters),

'suski'=>array(
'county'=>'suski',
'countydesc'=>'powiat suski',
'city'=>null,
'citydesc'=>null,
'location'=>null,
'locationdesc'=>null,
'lat'=>49.740278,
'long'=>19.588611,
'locationtype'=>$countieslocationtype,
'locationparameters'=>$countieslocationparameters),

'tarnowski'=>array(
'county'=>'tarnowski',
'countydesc'=>'powiat tarnowski',
'city'=>null,
'citydesc'=>null,
'location'=>null,
'locationdesc'=>null,
'lat'=>49.889632,
'long'=>20.961670,
'locationtype'=>$countieslocationtype,
'locationparameters'=>$countieslocationparameters),

'tatrzanski'=>array(
'county'=>'tatrzanski',
'countydesc'=>'powiat tatrzański',
'city'=>null,
'citydesc'=>null,
'location'=>null,
'locationdesc'=>null,
'lat'=>49.3,
'long'=>19.95,
'locationtype'=>$countieslocationtype,
'locationparameters'=>$countieslocationparameters),

'wadowicki'=>array(
'county'=>'wadowicki',
'countydesc'=>'powiat wadowicki',
'city'=>null,
'citydesc'=>null,
'location'=>null,
'locationdesc'=>null,
'lat'=>49.883333,
'long'=>19.5,
'locationtype'=>$countieslocationtype,
'locationparameters'=>$countieslocationparameters),

'wielicki'=>array(
'county'=>'wielicki',
'countydesc'=>'powiat wielicki',
'city'=>null,
'citydesc'=>null,
'location'=>null,
'locationdesc'=>null,
'lat'=>49.986111,
'long'=>20.061667,
'locationtype'=>$countieslocationtype,
'locationparameters'=>$countieslocationparameters),
);

$regions = array(
'obszar1'=>array(
'locationdesc'=>'Kraków',
'regioncities'=>'krakow',
'regionstations'=>'krasinskiego,bulwarowa,bujaka'),

'obszar2'=>array(
'locationdesc'=>'Małopolska północno-wschodnia',
'regioncities'=>'tarnow,bochnia,brzesko,dabrowatarnowska,tuchow,bochenski,brzeski,dabrowski,tarnowski',
'regionstations'=>'bitwypodstudziankami'),

'obszar3'=>array(
'locationdesc'=>'Małopolska południowo-wschodnia',
'regioncities'=>'nowysacz,gorlice,krynicazdroj,limanowa,gorlicki,limanowski,nowosadecki',
'regionstations'=>'nadbrzezna'),

'obszar4'=>array(
'locationdesc'=>'Małopolska północna',
'regioncities'=>'miechow,proszowice,skawina,wieliczka,krakowski,miechowski,proszowicki,wielicki',
'regionstations'=>'ogrody'),

'obszar5'=>array(
'locationdesc'=>'Małopolska południowo-zachodnia',
'regioncities'=>'myslenice,suchabeskidzka,wadowice,myslenicki,suski,wadowicki',
'regionstations'=>'handlowa'),

'obszar6'=>array(
'locationdesc'=>'Małopolska zachodnia',
'regioncities'=>'chrzanow,olkusz,oswiecim,trzebina,chrzanowski,olkuski,oswiecimski',
'regionstations'=>'zwm,nullo'),

'obszar7'=>array(
'locationdesc'=>'Małopolska południowa',
'regioncities'=>'nowytarg,zakopane,nowotarski,tatrzanski',
'regionstations'=>'sienkiewicza'));

$parameters = array(
'pm10'=>array(
'desc'=>'pył PM10',
'unit'=>'µg/m³'),

'pm2.5'=>array(
'desc'=>'pył PM2.5',
'unit'=>'µg/m³'),

'no2'=>array(
'desc'=>'dwutlenek azotu',
'unit'=>'µg/m³'),

'nox'=>array(
'desc'=>'tlenki azotu',
'unit'=>'µg/m³'),

'no'=>array(
'desc'=>'tlenek azotu',
'unit'=>'µg/m³'),

'so2'=>array(
'desc'=>'dwutlenek siarki',
'unit'=>'µg/m³'),

'co'=>array(
'desc'=>'tlenek węgla',
'unit'=>'µg/m³'),

'o3'=>array(
'desc'=>'ozon',
'unit'=>'µg/m³'),

'c6h6'=>array(
'desc'=>'benzen',
'unit'=>'µg/m³'),

'caqi'=>array(
'desc'=>'CAQI',
'unit'=>null),

'aqi'=>array(
'desc'=>'AQI',
'unit'=>null),

'temperature'=>array(
'desc'=>'temperatura',
'unit'=>'°C'),

'pressure'=>array(
'desc'=>'ciśnienie',
'unit'=>'hPa'),

'windspeed'=>array(
'desc'=>'prędkość wiatru',
'unit'=>'m/s'),

'winddirection'=>array(
'desc'=>'kierunek wiatru',
'unit'=>'°'),

'clouds'=>array(
'desc'=>'zachmurzenie',
'unit'=>'%'),

'precipitation'=>array(
'desc'=>'opady',
'unit'=>'mm/h'),

'alert'=>array(
'desc'=>'stopień zagrożenia',
'unit'=>null));

ini_set('max_execution_time', 300);
date_default_timezone_set('Europe/Warsaw');

// Functions
function urlOnline($url) {
	$handle = curl_init($url);
	curl_setopt($handle,CURLOPT_CONNECTTIMEOUT,10);
	curl_setopt($handle,CURLOPT_HEADER,true);
	curl_setopt($handle,CURLOPT_NOBODY,true);
	curl_setopt($handle,CURLOPT_RETURNTRANSFER,true);
	$response = curl_exec($handle);
	$httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
	if ($httpCode == 404) $response = false;
	curl_close($handle);
return $response;
}
function getHtmlTd($url) {
	$html = new DOMDocument();
	libxml_use_internal_errors(true);
	$file = @file_get_contents($url);
	if (!empty($file)) {
		$html->loadHTML($file);
		$item = $html->getElementsByTagName('td');
	} else $item = null;
return $item;
}
function openXML($url) {
	$xml = new DOMDocument();
	$xml->load($url);
	$item = $xml->getElementsByTagName('Item');
return $item;
}
function xmlLocations($id) {
	switch ($id) {
		case 'Kraków, Al. Krasińskiego': $id = '005'; break;
		case 'Kraków, ul. Bulwarowa': $id = '006'; break;
		case 'Kraków, ul. Bujaka': $id = '015'; break;
		case 'Tarnów': $id = '003'; break;
		case 'Nowy Sącz': $id = '007'; break;
		case 'Olkusz': $id = '009'; break;
		case 'Skawina': $id = '004'; break;
		case 'Sucha Beskidzka': $id = '013'; break;
		case 'Szarów': $id = '014'; break;
		case 'Szymbark': $id = '011'; break;
		case 'Trzebinia': $id = '010'; break;
		case 'Zakopane': $id = '008'; break;
	}
return $id;
}
function parameterPosition($item) {
	$position = array();
	if ($item) {
		for ($i = 0; $i < $item->length; $i++) {
			switch ($item->item($i)->nodeValue) {
				case 'Dwutlenek siarki (SO2)': $position['so2']=$i; break;
				case 'Tlenek azotu (NO)': $position['no']=$i; break;
				case 'Dwutlenek azotu (NO2)': $position['no2']=$i; break;
				case 'Tlenek węgla (CO)': $position['co']=$i; break;
				case 'Ozon (O3)': $position['o3']=$i; break;
				case 'Tlenki azotu (NOx)': $position['nox']=$i; break;
				case 'Pył zawieszony (PM10)': $position['pm10']=$i; break;
				case 'Pył zawieszony PM 2.5 (PM2.5)': $position['pm2.5']=$i; break;
				case 'Ciśnienie atmosferyczne (PH)': $position['pressure']=$i; break;
				case 'Temperatura (TP)': $position['temperature']=$i; break;
				case 'Benzen (C6H6)': $position['c6h6']=$i; break;
			}
		}
	}
return $position;
}
function repairAverage($average) {
	switch ($average) {
		case 'max': $average = '1hmax'; break;
		case '8_h': $average = '8hmax'; break;
		case '1_h': $average = '1h'; break;
	}
return trim($average);
}
function percentOfLimit($parameter,$average,$value) {
	if ($average === '1hmax') $average = '1h';
	if ($average === '8hmax') $average = '8h';
	$limit = null;
	switch ($parameter) {
		case 'pm10': if ($average === '24h') $limit = 50; break;
		case 'no2': if ($average === '1h') $limit = 200; break;
		case 'so2': if ($average === '1h') $limit = 350; elseif ($average === '24h') $limit = 125; break;
		case 'co': if ($average === '8h') $limit = 10000; break;
		case 'o3': if ($average === '1h') $limit = 180; elseif ($average === '8h') $limit = 120; break;
	}
	if (($limit !== null)&&($value !== 0)) $percentoflimit = ceil (100 * $value / $limit);
	else $percentoflimit = null;
return $percentoflimit;
}
function caqi($parameter,$average,$value) {
	if ($average === '1hmax') $average = '1h';
	if ($average === '8hmax') $average = '8h';
	if ($average === '1h')
		switch ($parameter) {
			case 'pm10': $levels = array(0,25,50,90,180); break;
			case 'pm2.5': $levels = array(0,15,30,55,110); break;
			case 'no2': $levels = array(0,50,100,200,400); break;
			case 'so2': $levels = array(0,50,100,350,500); break;
			case 'o3': $levels = array(0,60,120,180,240); break;
			default: $levels = null;
		}
	elseif ($average === '8h')
		switch ($parameter) {
			case 'co': $levels = array(0,5000,7500,10000,20000); break;
			default: $levels = null;
		}
	elseif ($average === '24h')
		switch ($parameter) {
			case 'pm10': $levels = array(0,15,30,50,100); break;
			case 'pm2.5': $levels = array(0,10,20,30,60); break;
			default: $levels = null;
		}
	else $levels = null;
	$grid = array(0,25,50,75,100);
	$caqi = 0;
	if ($levels != null) {
		if ($value > $levels[4]) $caqi = 101;
		else for ($i = 3; $i >= 0; $i--) {
			if ($value > $levels[$i]) {
				$caqi = ceil($grid[$i] + ($grid[$i+1]-$grid[$i])/($levels[$i+1]-$levels[$i]) * ($value - $levels[$i]));
				break;
			}
		}
	}
	if ($parameter === 'caqi') $caqi = $value;
	if ($caqi > 0)
		switch ($caqi) {
			case ($caqi > 100): $caqiclass = 5; $caqidesc = 'bardzo wysoki'; $caqicolor = '#960018'; break;
			case ($caqi > 75): $caqiclass = 4; $caqidesc = 'wysoki'; $caqicolor = '#f29305'; break;
			case ($caqi > 50): $caqiclass = 3; $caqidesc = 'średni'; $caqicolor = '#eec20b'; break;
			case ($caqi > 25): $caqiclass = 2; $caqidesc = 'niski'; $caqicolor = '#bbcf4c'; break;
			case ($caqi > 0): $caqiclass = 1; $caqidesc = 'bardzo niski'; $caqicolor = '#79bc6a'; break;
		}
	else $caqi = $caqiclass = $caqidesc = $caqicolor = null;
return array($caqi,$caqiclass,$caqidesc,$caqicolor);
}
function aqi($parameter,$average,$value) {
	if ($average === '1hmax') $average = '1h';
	if ($average === '8hmax') $average = '8h';
	if ($average === '1h')
		switch ($parameter) {
			case 'pm10': $levels = array(0,55,155,255,355,425,505,604); break;
			case 'pm2.5': $levels = array(0,15.5,40.5,65.5,150.5,250.5,350.5,500.4); break;
			case 'no2': $levels = array(null,null,null,null,1223,2352,3105,3839); break;
			case 'so2': $levels = array(0,92,380,590,799,1585,2109,2631); break;
			case 'o3': $levels = array(null,null,245,324,402,795,991,1186); break;
			default: $levels = null;
		}
	elseif ($average === '8h')
		switch ($parameter) {
			case 'o3': $levels = array(0,118,149,188,228,736,null,null); break;
			case 'co': $levels = array(0,5155,10883,14320,17757,34941,46397,57738); break;
			default: $levels = null;
		}
	else $levels = null;
	$grid = array(0,51,101,151,201,301,401,500);
	$aqi = 0;
	if (($parameter === 'o3')&&($average === '8h')&&($value >= $levels[5])) {$aqi = 300; }
	elseif (($parameter === 'o3')&&($average === '1h')&&($value < $levels[2])) {$aqi = 0; }
	elseif (($parameter === 'no2')&&($average === '1h')&&($value < $levels[4])) {$aqi = 0; }
	elseif (($value >= $levels[7])&&($levels[7] != null)) {$aqi = 500;}
	else for ($i = 6; $i >= 0; $i--) {
		if (($value >= $levels[$i])&&($levels[$i+1] != 0)) {
			$aqi = floor($grid[$i] + ($grid[$i+1]-$grid[$i])/($levels[$i+1]-$levels[$i]) * ($value - $levels[$i]));
			break;
		}
	}
	if ($parameter === 'aqi') $aqi = $value;
	if ($aqi > 0)
		switch ($aqi) {
			case ($aqi > 300): $aqiclass = 6; $aqidesc = 'niebezpieczny'; $aqicolor = '#7e0023'; break;
			case ($aqi > 200): $aqiclass = 5; $aqidesc = 'bardzo niezdrowy'; $aqicolor = '#99004c'; break;
			case ($aqi > 150): $aqiclass = 4; $aqidesc = 'niezdrowy'; $aqicolor = '#ff0000'; break;
			case ($aqi > 100): $aqiclass = 3; $aqidesc = 'niezdrowy dla grup wrażliwych'; $aqicolor = '#ff7e00'; break;
			case ($aqi > 50): $aqiclass = 2; $aqidesc = 'umiarkowany'; $aqicolor = '#ffff00'; break;
			case ($aqi > 0): $aqiclass = 1; $aqidesc = 'dobry'; $aqicolor = '#00e400'; break;
		}
	else $aqi = $aqiclass = $aqidesc = $aqicolor = null;
return array($aqi,$aqiclass,$aqidesc,$aqicolor);
}
function fetchValue($db,$value,$table,$where=null) {
	try {
		$query = $db->query("SELECT $value FROM $table".$where);
		$results = $query->fetchAll();
		$query->closeCursor();
	} catch (PDOException $e) {
		return FALSE;
	}
	$array = array();
	foreach ($results as $result) {
		array_push($array,$result[$value]);
	}
	if (count($array) == 0) return null;
	elseif (count($array) == 1) return $array[0];
	else return $array;
}
function tableExists($db,$table) {
	try {
		$result = $db->query("SELECT 1 FROM $table LIMIT 1");
	} catch (PDOException $e) {
		return FALSE;
	}
	return $result;
}

// Operations on database
try {
	$db = new PDO('sqlite::memory:');
	$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
	$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);
	$db->exec("ATTACH '".dirname(__FILE__).DIRECTORY_SEPARATOR."all.db' as alldb");

// Checking which datasources were changed
	$db->exec("CREATE TABLE IF NOT EXISTS alldb.verify (type text UNIQUE,hash text)");
	$gethtml = $getxml = $getcsv = $getlist = false;
	if (urlOnline($htmlurl)) {
		$hashdbhtml = fetchValue($db,'hash','alldb.verify'," WHERE type = 'html'");
		$dates = array(date('Y-m-d'),date('Y-m-d',strtotime('-1 day')),date('Y-m-d',strtotime('-2 days')));
		$hashhtml = '';
		foreach ($stations as $id=>$station) {
			$hashhtml .= @md5_file($htmlurl.'?stacja='.$id.'&date_input='.$dates[0]);
		}
		$hashhtml = md5($hashhtml);
		if ($hashhtml !== $hashdbhtml) $gethtml = true;
	}
	if (urlOnline($xmlurl)) {
		$hashdbxml = fetchValue($db,'hash','alldb.verify'," WHERE type = 'xml'");
		$hashxml = @md5_file($xmlurl);
		if ($hashxml !== $hashdbxml) $getxml = true;
	}
	if (urlOnline($csvurl)) {
		$hashdbcsv = fetchValue($db,'hash','alldb.verify'," WHERE type = 'csv'");
		$csvurlt = $csvurl.date('Ymd').'/ostrzezenia/'.date('Ymd').'_nowe_poziomy.csv';
		$hashcsv = @md5_file($csvurlt);
		if (!$hashcsv) {
			$csvurly = $csvurl.date('Ymd',strtotime('yesterday')).'/ostrzezenia/'.date('Ymd',strtotime('yesterday')).'_nowe_poziomy.csv';
			$hashcsv = @md5_file($csvurly);
			$csvurl = $csvurly;
		} else $csvurl = $csvurlt;
		if ($hashcsv !== $hashdbcsv) $getcsv = true;
	}
	$getmeasurement = ($gethtml || $getxml);
	$getforecast = $getcsv;
	$getalert = ($getmeasurement || $getforecast);
	$hashdbl = fetchValue($db,'hash','alldb.verify'," WHERE type = 'list'");
	$hashl = md5(json_encode(array_merge($stations,$cities,$counties,$parameters)));
	if ($hashl !== $hashdbl) $getlist = true;
	$update = ($getalert || $getlist);
	
	if ($update) {

// HTML: get measurements data from WIOS website
		if ($gethtml) {
			$db->exec("INSERT OR REPLACE INTO alldb.verify (type,hash) VALUES ('html','$hashhtml')");
			$db->exec("CREATE TABLE main.html(city text, location text, date text, time integer, parameter text, average text, value integer)");
			$stmt = $db->prepare("INSERT INTO main.html (city,location,date,time,parameter,average,value) VALUES (:city,:location,:date,:time,:parameter,:average,:value)");
			$stmt->bindParam(':city', $city);
			$stmt->bindParam(':location', $location);
			$stmt->bindParam(':date', $date);
			$stmt->bindParam(':time', $time);
			$stmt->bindParam(':parameter', $parameter);
			$stmt->bindParam(':average', $average);
			$stmt->bindParam(':value', $value);

			foreach ($stations as $id=>$station) {
				$city = $station['city'];
				$location = $station['location'];
				foreach ($dates as $date) {
					$item = getHtmlTd($htmlurl.'?stacja='.$id.'&date_input='.$date);
					$positions = parameterPosition($item);
					foreach ($positions as $parameter => $position) {
						$average = '1h';
						for ($i = $position+3; $i < $position+28; $i++) {
							$time = $i-$position-2;
							if ($time === 24) {$date = date('Y-m-d',strtotime('+1 day',strtotime($date))); $time = 0;}
							if ($time === 25) {$time = 0; $average = '24h';}
							$value = $item->item($i)->nodeValue + 0;
							if ($parameter === 'co') $value = 1000*$value;
							if (empty($value)) $value = null;
							$stmt->execute();
						}
					}
				}
			}
			$db->exec("DROP TABLE IF EXISTS alldb.html");
			$db->exec("CREATE TABLE alldb.html AS SELECT * FROM main.html");
		}

// XML: get measurements data from UMWM website
		if ($getxml) {
			$db->exec("INSERT OR REPLACE INTO alldb.verify (type,hash) VALUES ('xml','$hashxml')");
			$db->exec("CREATE TABLE main.xml(city text, location text, date text, time integer, parameter text, average text, value integer)");
			$stmt = $db->prepare("INSERT INTO main.xml (city,location,date,time,parameter,average,value) VALUES (:city,:location,:date,:time,:parameter,:average,:value)");
			$stmt->bindParam(':city', $city);
			$stmt->bindParam(':location', $location);
			$stmt->bindParam(':date', $date);
			$stmt->bindParam(':time', $time);
			$stmt->bindParam(':parameter', $parameter);
			$stmt->bindParam(':average', $average);
			$stmt->bindParam(':value', $value);

			$item = openXML($xmlurl);
			foreach($item as $node) {
				$id = xmlLocations($node->getElementsByTagName('City')->item(0)->nodeValue);
				$city = $stations[$id]['city'];
				$location = $stations[$id]['location'];
				$date = substr($node->getElementsByTagName('Date')->item(0)->nodeValue, 0, 10);
				$time = intval(substr($node->getElementsByTagName('Date')->item(0)->nodeValue, 11, 2));
				$parameter = strtolower($node->getElementsByTagName('Pollutant')->item(0)->nodeValue);
				$average = $node->getElementsByTagName('Concentration')->item(0)->nodeValue;
				$value = str_replace(',', '.', $node->getElementsByTagName('Value')->item(0)->nodeValue);
				if (($parameter === 'co')&&($value < 10)) $value = $value * 1000;
				if (empty($value)) $value = null; else $value = ceil($value);
				$stmt->execute();
			}
			$db->exec("DROP TABLE IF EXISTS alldb.xml");
			$db->exec("CREATE TABLE alldb.xml AS SELECT * FROM main.xml");
		}

// CSV: get forecasts data from Politechnika Warszawska website
		if ($getcsv) {
			$db->exec("INSERT OR REPLACE INTO alldb.verify (type,hash) VALUES ('csv','$hashcsv')");
			$db->exec("CREATE TABLE main.csv(city text, location text, date text, time integer, parameter text, average text, value integer)");
			$stmt = $db->prepare("INSERT INTO main.csv (city,location,date,time,parameter,average,value) VALUES (:city,:location,:date,:time,:parameter,:average,:value)");
			$stmt->bindParam(':city', $city);
			$stmt->bindParam(':location', $location);
			$stmt->bindParam(':date', $date);
			$stmt->bindParam(':time', $time);
			$stmt->bindParam(':parameter', $parameter);
			$stmt->bindParam(':average', $average);
			$stmt->bindParam(':value', $value);
			
			$csv = array();
			$csv = @file($csvurl);
			foreach ($csv as $key => $line) {
				$record = explode(';',$line);
				$city = strtolower(str_replace('_','',trim($record[0])));
				if (($key < 700)&&(($city == 'krakow')||($city == 'tarnow')||($city == 'nowysacz'))) continue;
				$location = null;
				if (strlen($record[1]) === 8) {
					$date = date('Y-m-d',strtotime($record[1]));
					$time = 0;
				}
				elseif (strlen($record[1]) === 10) {
					$date = date('Y-m-d',strtotime(substr($record[1],0,8)));
					$time = substr($record[1],8,2);
				}
				$parameter = strtolower(str_replace('-','',trim($record[3])));
				$average = repairAverage($record[2]);
				$value = $record[4];
				if (($parameter == 'caqi')&&($value > 100)) $value = 101;
				if ($parameter == 'clouds') {
					if ($value < 0) $value = 0; else $value *= 100;
				}
				if (($parameter == 'temperature')||($parameter == 'windspeed')||($parameter == 'precipitation')) $value = round($value,1);
				else $value = ceil($value);
				$stmt->execute();
			}
			$db->exec("DROP TABLE IF EXISTS alldb.csv");
			$db->exec("CREATE TABLE alldb.csv AS SELECT * FROM main.csv");
		}

// Measurements: choose value from XML or HTML
	if ($getmeasurement) {
		if (!tableExists($db,'main.html')) {
			if (tableExists($db,'alldb.html')) $db->exec("CREATE TABLE main.html AS SELECT * FROM alldb.html");
			else $db->exec("CREATE TABLE main.html(city text, location text, date text, time integer, parameter text, average text, value integer)");
		}
		if (!tableExists($db,'main.xml')) {
			if (tableExists($db,'alldb.xml')) $db->exec("CREATE TABLE main.xml AS SELECT * FROM alldb.xml");
			else $db->exec("CREATE TABLE main.xml(city text, location text, date text, time integer, parameter text, average text, value integer)");
		}
		$db->exec("CREATE TABLE main.measurement(type text,province text,provincedesc text,county text,countydesc text,city text,citydesc text,location text,locationdesc text,lat float,long float,date text,time integer,timestamp integer,parameter text,parameterdesc text,average text,value integer,unit text,percentoflimit integer,caqi integer,caqiclass integer,caqidesc text,caqicolor text,aqi integer,aqiclass integer,aqidesc text,aqicolor text,message text)");
		$stmt = $db->prepare("INSERT INTO main.measurement (type,province,provincedesc,county,countydesc,city,citydesc,location,locationdesc,lat,long,date,time,timestamp,parameter,parameterdesc,average,value,unit,percentoflimit,caqi,caqiclass,caqidesc,caqicolor,aqi,aqiclass,aqidesc,aqicolor,message) VALUES (:type,:province,:provincedesc,:county,:countydesc,:city,:citydesc,:location,:locationdesc,:lat,:long,:date,:time,:timestamp,:parameter,:parameterdesc,:average,:value,:unit,:percentoflimit,:caqi,:caqiclass,:caqidesc,:caqicolor,:aqi,:aqiclass,:aqidesc,:aqicolor,:message)");
		$stmt->bindParam(':type', $type);
		$stmt->bindParam(':province', $province);
		$stmt->bindParam(':provincedesc', $provincedesc);
		$stmt->bindParam(':county', $county);
		$stmt->bindParam(':countydesc', $countydesc);
		$stmt->bindParam(':city', $city);
		$stmt->bindParam(':citydesc', $citydesc);
		$stmt->bindParam(':location', $location);
		$stmt->bindParam(':locationdesc', $locationdesc);
		$stmt->bindParam(':lat', $lat);
		$stmt->bindParam(':long', $long);
		$stmt->bindParam(':date', $date);
		$stmt->bindParam(':time', $time);
		$stmt->bindParam(':timestamp', $timestamp);
		$stmt->bindParam(':parameter', $parameter);
		$stmt->bindParam(':parameterdesc', $parameterdesc);
		$stmt->bindParam(':average', $average);
		$stmt->bindParam(':value', $value);
		$stmt->bindParam(':unit', $unit);
		$stmt->bindParam(':percentoflimit', $percentoflimit);
		$stmt->bindParam(':caqi', $caqi);
		$stmt->bindParam(':caqiclass', $caqiclass);
		$stmt->bindParam(':caqidesc', $caqidesc);
		$stmt->bindParam(':caqicolor', $caqicolor);
		$stmt->bindParam(':aqi', $aqi);
		$stmt->bindParam(':aqiclass', $aqiclass);
		$stmt->bindParam(':aqidesc', $aqidesc);
		$stmt->bindParam(':aqicolor', $aqicolor);
		$stmt->bindParam(':message', $message);

		$type = 'measurement';
		$message = null;
		$datetime48 = array();
		for ($i = 0; $i<48; $i++) array_push($datetime48, strtotime('-'.$i.' hour',strtotime(date('Y-m-d H:00:00'))));
		foreach ($datetime48 as $timestamp) {
			$date = date('Y-m-d',$timestamp);
			$time = date('G',$timestamp);
			foreach ($stations as $station) {
				list($county,$countydesc,$city,$citydesc,$location,$locationdesc,$lat,$long,,$locationparameters) = array_values($station);
				$parametersarray = explode(',',$locationparameters);
				if(($key = array_search('caqi', $parametersarray)) !== false) unset($parametersarray[$key]);
				if(($key = array_search('aqi', $parametersarray)) !== false) unset($parametersarray[$key]);
				foreach ($parametersarray as $parameter) {
					$parameterdesc = $parameters[$parameter]['desc'];
					$unit = $parameters[$parameter]['unit'];
					$averages = array('1h','24h');
					foreach ($averages as $average) {
						if (($average === '24h')&&($time != 0)) continue;
						$value = fetchValue($db,'value','main.xml'," WHERE date = '$date' AND time = $time AND location = '$location' AND parameter = '$parameter' AND average = '$average'");
						if (empty($value)) {
							$value = fetchValue($db,'value','main.html'," WHERE date = '$date' AND time = $time AND location = '$location' AND parameter = '$parameter' AND average = '$average'");
							if (empty($value)) $value = null;
						}
						$percentoflimit = percentOfLimit($parameter,$average,$value);
						list($caqi,$caqiclass,$caqidesc,$caqicolor) = caqi($parameter,$average,$value);
						list($aqi,$aqiclass,$aqidesc,$aqicolor) = aqi($parameter,$average,$value);
						$stmt->execute();
					}
				}
			}
		}
		$yday = date('Y-m-d',strtotime('yesterday'));
		$valuespm1024hy = fetchValue($db,'value','main.measurement'," WHERE date = '$yday' AND time = 0 AND parameter = 'pm10' AND average = '24h'");
		$hashpm1024hy = md5(implode($valuespm1024hy));

// Measurements: calculate 8h average
		$average = '8h';
		foreach ($stations as $station) {
			list($county,$countydesc,$city,$citydesc,$location,$locationdesc,$lat,$long,,$locationparameters) = array_values($station);
			$parametersarray = explode(',',$locationparameters);
			if(($key = array_search('caqi', $parametersarray)) !== false) unset($parametersarray[$key]);
			if(($key = array_search('aqi', $parametersarray)) !== false) unset($parametersarray[$key]);
			foreach ($parametersarray as $parameter) {
				$parameterdesc = $parameters[$parameter]['desc'];
				$unit = $parameters[$parameter]['unit'];
				foreach ($datetime48 as $timestamp) {
					$date = date('Y-m-d',$timestamp);
					$time = date('G',$timestamp);
					$value = fetchValue($db,'value','main.xml'," WHERE date = '$date' AND time = $time AND location = '$location' AND parameter = '$parameter' AND average = '8h'");
					if (empty($value)) {
						$values = array();
						for ($i = 0; $i<8; $i++) {
							$date8 = date('Y-m-d',strtotime('-'.$i.' hour',$timestamp));
							$time8 = date('G',strtotime('-'.$i.' hour',$timestamp));
							$values[$i] = fetchValue($db,'value','main.xml'," WHERE date = '$date8' AND time = $time8 AND location = '$location' AND parameter = '$parameter' AND average = '1h'");
						}
						if (count(array_filter($values)) > 0) $value = ceil(array_sum($values)/count(array_filter($values)));
						else $value = null;
					}
					$percentoflimit = percentOfLimit($parameter,$average,$value);
					list($caqi,$caqiclass,$caqidesc,$caqicolor) = caqi($parameter,$average,$value);
					list($aqi,$aqiclass,$aqidesc,$aqicolor) = aqi($parameter,$average,$value);
					$stmt->execute();
					}
				}
			}

// Measurements: calculate 1hmax and 8hmax average
		$yesterday = $today = array();
		for ($i = 1; $i<25; $i++) array_push($yesterday, strtotime('+'.$i.' hour',strtotime('yesterday')));
		for ($i = 1; $i<25; $i++) array_push($today, strtotime('+'.$i.' hour',strtotime('today')));
		$datetime24 = array(strtotime('yesterday')=>$yesterday,strtotime('today')=>$today);
		foreach ($stations as $station) {
			list($county,$countydesc,$city,$citydesc,$location,$locationdesc,$lat,$long,,$locationparameters) = array_values($station);
			$parametersarray = explode(',',$locationparameters);
			if(($key = array_search('caqi', $parametersarray)) !== false) unset($parametersarray[$key]);
			if(($key = array_search('aqi', $parametersarray)) !== false) unset($parametersarray[$key]);
			foreach ($parametersarray as $parameter) {
				$parameterdesc = $parameters[$parameter]['desc'];
				$unit = $parameters[$parameter]['unit'];
				if (($parameter == 'no2')||($parameter == 'so2')||($parameter == 'o3')) {
					$average = '1hmax';
					foreach ($datetime24 as $timestamp=>$datetimes) {
						$value1 = array(null);
						foreach ($datetimes as $key=>$datetime) {
							$value1[$key] = fetchValue($db,'value','main.measurement'," WHERE location = '$location' AND timestamp = $datetime AND parameter = '$parameter' AND average = '1h'");
						}
						$date = date('Y-m-d',$timestamp);
						$time = 0;
						if (count($value1)>1) $value = max($value1); else $value = $value1[0];
						$percentoflimit = percentOfLimit($parameter,$average,$value);
						list($caqi,$caqiclass,$caqidesc,$caqicolor) = caqi($parameter,$average,$value);
						list($aqi,$aqiclass,$aqidesc,$aqicolor) = aqi($parameter,$average,$value);
						$stmt->execute();
					}
				}
				if (($parameter == 'co')||($parameter == 'o3')) {
					$average = '8hmax';
					foreach ($datetime24 as $timestamp=>$datetimes) {
						$value8 = array(null);
						foreach ($datetimes as $key=>$datetime) {
							$value8[$key] = fetchValue($db,'value','main.measurement'," WHERE location = '$location' AND timestamp = $datetime AND parameter = '$parameter' AND average = '8h'");
						}
						$date = date('Y-m-d',$timestamp);
						$time = 0;
						if (count($value8)>1) $value = max($value8); else $value = $value8[0];
						$percentoflimit = percentOfLimit($parameter,$average,$value);
						list($caqi,$caqiclass,$caqidesc,$caqicolor) = caqi($parameter,$average,$value);
						list($aqi,$aqiclass,$aqidesc,$aqicolor) = aqi($parameter,$average,$value);
						$stmt->execute();
					}
				}
			}
		}

// Measurements: calculate CAQI and AQI for every hour
		$percentoflimit = $unit = null;
		foreach ($datetime48 as $timestamp) {
			$date = date('Y-m-d',$timestamp);
			$time = date('G',$timestamp);
			foreach ($stations as $station) {
				$parameter = 'caqi';
				$parameterdesc = $parameters[$parameter]['desc'];
				$average = '1h';
				list($county,$countydesc,$city,$citydesc,$location,$locationdesc,$lat,$long) = array_values($station);
				$valuearray = fetchValue($db,'caqi','main.measurement'," WHERE location = '$location' AND timestamp = $timestamp AND (average = '1h' OR average = '8h')");
				if ($valuearray) $value = max($valuearray); else $value = null;
				$percentoflimit = percentOfLimit($parameter,$average,$value);
				list($caqi,$caqiclass,$caqidesc,$caqicolor) = caqi($parameter,$average,$value);
				list($aqi,$aqiclass,$aqidesc,$aqicolor) = aqi($parameter,$average,$value);
				$stmt->execute();
				if ($time == 0) {
					$average = '24h';
					$valuearray = fetchValue($db,'caqi','main.measurement'," WHERE location = '$location' AND timestamp = $timestamp AND (average = '1hmax' OR average = '8hmax' OR average = '24h')");
					if ($valuearray) $value = max($valuearray); else $value = null;
					$percentoflimit = percentOfLimit($parameter,$average,$value);
					list($caqi,$caqiclass,$caqidesc,$caqicolor) = caqi($parameter,$average,$value);
					list($aqi,$aqiclass,$aqidesc,$aqicolor) = aqi($parameter,$average,$value);
					$stmt->execute();
				}
				$parameter = 'aqi';
				$parameterdesc = $parameters[$parameter]['desc'];
				$average = '1h';
				$valuearray = fetchValue($db,'aqi','main.measurement'," WHERE location = '$location' AND timestamp = $timestamp AND (average = '1h' OR average = '8h')");
				if ($valuearray) $value = max($valuearray); else $value = null;
				$percentoflimit = percentOfLimit($parameter,$average,$value);
				list($caqi,$caqiclass,$caqidesc,$caqicolor) = caqi($parameter,$average,$value);
				list($aqi,$aqiclass,$aqidesc,$aqicolor) = aqi($parameter,$average,$value);
				$stmt->execute();
			}
		}

// Last measurements: choose values from measurements
		$db->exec("CREATE TABLE main.lastmeasurement(type text,province text,provincedesc text,county text,countydesc text,city text,citydesc text,location text,locationdesc text,lat float,long float,date text,time integer,timestamp integer,parameter text,parameterdesc text,average text,value integer,unit text,percentoflimit integer,caqi integer,caqiclass integer,caqidesc text,caqicolor text,aqi integer,aqiclass integer,aqidesc text,aqicolor text,message text)");
		$stmt = $db->prepare("INSERT INTO main.lastmeasurement (type,province,provincedesc,county,countydesc,city,citydesc,location,locationdesc,lat,long,date,time,timestamp,parameter,parameterdesc,average,value,unit,percentoflimit,caqi,caqiclass,caqidesc,caqicolor,aqi,aqiclass,aqidesc,aqicolor,message) VALUES (:type,:province,:provincedesc,:county,:countydesc,:city,:citydesc,:location,:locationdesc,:lat,:long,:date,:time,:timestamp,:parameter,:parameterdesc,:average,:value,:unit,:percentoflimit,:caqi,:caqiclass,:caqidesc,:caqicolor,:aqi,:aqiclass,:aqidesc,:aqicolor,:message)");
		$stmt->bindParam(':type', $type);
		$stmt->bindParam(':province', $province);
		$stmt->bindParam(':provincedesc', $provincedesc);
		$stmt->bindParam(':county', $county);
		$stmt->bindParam(':countydesc', $countydesc);
		$stmt->bindParam(':city', $city);
		$stmt->bindParam(':citydesc', $citydesc);
		$stmt->bindParam(':location', $location);
		$stmt->bindParam(':locationdesc', $locationdesc);
		$stmt->bindParam(':lat', $lat);
		$stmt->bindParam(':long', $long);
		$stmt->bindParam(':date', $date);
		$stmt->bindParam(':time', $time);
		$stmt->bindParam(':timestamp', $timestamp);
		$stmt->bindParam(':parameter', $parameter);
		$stmt->bindParam(':parameterdesc', $parameterdesc);
		$stmt->bindParam(':average', $average);
		$stmt->bindParam(':value', $value);
		$stmt->bindParam(':unit', $unit);
		$stmt->bindParam(':percentoflimit', $percentoflimit);
		$stmt->bindParam(':caqi', $caqi);
		$stmt->bindParam(':caqiclass', $caqiclass);
		$stmt->bindParam(':caqidesc', $caqidesc);
		$stmt->bindParam(':caqicolor', $caqicolor);
		$stmt->bindParam(':aqi', $aqi);
		$stmt->bindParam(':aqiclass', $aqiclass);
		$stmt->bindParam(':aqidesc', $aqidesc);
		$stmt->bindParam(':aqicolor', $aqicolor);
		$stmt->bindParam(':message', $message);

		$type = 'lastmeasurement';
		$average = '1h';
		foreach ($stations as $station) {
			list($county,$countydesc,$city,$citydesc,$location,$locationdesc,$lat,$long,,$locationparameters) = array_values($station);
			$parametersarray = explode(',',$locationparameters);
			foreach ($parametersarray as $parameter) {
				$parameterdesc = $parameters[$parameter]['desc'];
				$unit = $parameters[$parameter]['unit'];
				for ($i = 0; $i<48; $i++) {
					$timestamp = $datetime48[$i];
					$date = date('Y-m-d',$timestamp);
					$time = date('G',$timestamp);
					$value = fetchValue($db,'value','main.measurement'," WHERE location = '$location' AND timestamp = $timestamp AND parameter = '$parameter' AND average = '$average'");
					if (!empty($value)) {
						$query = $db->query("SELECT percentoflimit,caqi,caqiclass,caqidesc,caqicolor,aqi,aqiclass,aqidesc,aqicolor,message FROM main.measurement WHERE location = '$location' AND timestamp = $timestamp AND parameter = '$parameter' AND average = '$average'");
						list($percentoflimit,$caqi,$caqiclass,$caqidesc,$caqicolor,$aqi,$aqiclass,$aqidesc,$aqicolor,$message) = array_values($query->fetch());
						$query->closeCursor();
						$stmt->execute();
						break;
					}
				}
			}
		}

// Smart measurements: choose values from measurements
		$db->exec("CREATE TABLE main.smartmeasurement(type text,province text,provincedesc text,county text,countydesc text,city text,citydesc text,location text,locationdesc text,lat float,long float,date text,time integer,timestamp integer,parameter text,parameterdesc text,average text,value integer,unit text,percentoflimit integer,caqi integer,caqiclass integer,caqidesc text,caqicolor text,aqi integer,aqiclass integer,aqidesc text,aqicolor text,message text)");
		$stmt = $db->prepare("INSERT INTO main.smartmeasurement (type,province,provincedesc,county,countydesc,city,citydesc,location,locationdesc,lat,long,date,time,timestamp,parameter,parameterdesc,average,value,unit,percentoflimit,caqi,caqiclass,caqidesc,caqicolor,aqi,aqiclass,aqidesc,aqicolor,message) VALUES (:type,:province,:provincedesc,:county,:countydesc,:city,:citydesc,:location,:locationdesc,:lat,:long,:date,:time,:timestamp,:parameter,:parameterdesc,:average,:value,:unit,:percentoflimit,:caqi,:caqiclass,:caqidesc,:caqicolor,:aqi,:aqiclass,:aqidesc,:aqicolor,:message)");
		$stmt->bindParam(':type', $type);
		$stmt->bindParam(':province', $province);
		$stmt->bindParam(':provincedesc', $provincedesc);
		$stmt->bindParam(':county', $county);
		$stmt->bindParam(':countydesc', $countydesc);
		$stmt->bindParam(':city', $city);
		$stmt->bindParam(':citydesc', $citydesc);
		$stmt->bindParam(':location', $location);
		$stmt->bindParam(':locationdesc', $locationdesc);
		$stmt->bindParam(':lat', $lat);
		$stmt->bindParam(':long', $long);
		$stmt->bindParam(':date', $date);
		$stmt->bindParam(':time', $time);
		$stmt->bindParam(':timestamp', $timestamp);
		$stmt->bindParam(':parameter', $parameter);
		$stmt->bindParam(':parameterdesc', $parameterdesc);
		$stmt->bindParam(':average', $average);
		$stmt->bindParam(':value', $value);
		$stmt->bindParam(':unit', $unit);
		$stmt->bindParam(':percentoflimit', $percentoflimit);
		$stmt->bindParam(':caqi', $caqi);
		$stmt->bindParam(':caqiclass', $caqiclass);
		$stmt->bindParam(':caqidesc', $caqidesc);
		$stmt->bindParam(':caqicolor', $caqicolor);
		$stmt->bindParam(':aqi', $aqi);
		$stmt->bindParam(':aqiclass', $aqiclass);
		$stmt->bindParam(':aqidesc', $aqidesc);
		$stmt->bindParam(':aqicolor', $aqicolor);
		$stmt->bindParam(':message', $message);

		$type = 'smartmeasurement';
		$average = '1h';
		foreach ($stations as $station) {
			list($county,$countydesc,$city,$citydesc,$location,$locationdesc,$lat,$long,,$locationparameters) = array_values($station);
			$parametersarray = explode(',',$locationparameters);
			$smart = 0;
			foreach ($parametersarray as $parameter) {
				for ($i = 0; $i<3; $i++) {
					$timestamp = $datetime48[$i];
					$value = fetchValue($db,'value','main.measurement'," WHERE location = '$location' AND timestamp = $timestamp AND parameter = '$parameter' AND average = '$average'");
					if (!empty($value)) { $smart = $timestamp; break; }
				}
				if ($smart !== 0) break;
			}
			if ($smart === 0) $smart = strtotime(date('Y-m-d H:00:00'));
			foreach ($parametersarray as $parameter) {
				$parameterdesc = $parameters[$parameter]['desc'];
				$unit = $parameters[$parameter]['unit'];
				$date = date('Y-m-d',$smart);
				$time = date('G',$smart);
				$query = $db->query("SELECT value,percentoflimit,caqi,caqiclass,caqidesc,caqicolor,aqi,aqiclass,aqidesc,aqicolor,message FROM main.measurement WHERE location = '$location' AND timestamp = $smart AND parameter = '$parameter' AND average = '$average'");
				list($value,$percentoflimit,$caqi,$caqiclass,$caqidesc,$caqicolor,$aqi,$aqiclass,$aqidesc,$aqicolor,$message) = array_values($query->fetch());
				$query->closeCursor();
				$stmt->execute();
			}
		}

// Measurements: write data from memory to file
		$db->exec("DROP TABLE IF EXISTS alldb.measurement");
		$db->exec("CREATE TABLE alldb.measurement AS SELECT * FROM main.measurement");
		$db->exec("DROP TABLE IF EXISTS alldb.lastmeasurement");
		$db->exec("CREATE TABLE alldb.lastmeasurement AS SELECT * FROM main.lastmeasurement");
		$db->exec("DROP TABLE IF EXISTS alldb.smartmeasurement");
		$db->exec("CREATE TABLE alldb.smartmeasurement AS SELECT * FROM main.smartmeasurement");
	}

// Forecasts: choose value from CSV
	if ($getforecast) {
		if (!tableExists($db,'main.csv')) {
			if (tableExists($db,'alldb.csv')) $db->exec("CREATE TABLE main.csv AS SELECT * FROM alldb.csv");
			else $db->exec("CREATE TABLE main.csv(city text, location text, date text, time integer, parameter text, average text, value integer)");
		}
		$db->exec("CREATE TABLE main.forecast(type text,province text,provincedesc text,county text,countydesc text,city text,citydesc text,location text,locationdesc text,lat float,long float,date text,time integer,timestamp integer,parameter text,parameterdesc text,average text,value integer,unit text,percentoflimit integer,caqi integer,caqiclass integer,caqidesc text,caqicolor text,aqi integer,aqiclass integer,aqidesc text,aqicolor text,message text)");
		$stmt = $db->prepare("INSERT INTO main.forecast (type,province,provincedesc,county,countydesc,city,citydesc,location,locationdesc,lat,long,date,time,timestamp,parameter,parameterdesc,average,value,unit,percentoflimit,caqi,caqiclass,caqidesc,caqicolor,aqi,aqiclass,aqidesc,aqicolor,message) VALUES (:type,:province,:provincedesc,:county,:countydesc,:city,:citydesc,:location,:locationdesc,:lat,:long,:date,:time,:timestamp,:parameter,:parameterdesc,:average,:value,:unit,:percentoflimit,:caqi,:caqiclass,:caqidesc,:caqicolor,:aqi,:aqiclass,:aqidesc,:aqicolor,:message)");
		$stmt->bindParam(':type', $type);
		$stmt->bindParam(':province', $province);
		$stmt->bindParam(':provincedesc', $provincedesc);
		$stmt->bindParam(':county', $county);
		$stmt->bindParam(':countydesc', $countydesc);
		$stmt->bindParam(':city', $city);
		$stmt->bindParam(':citydesc', $citydesc);
		$stmt->bindParam(':location', $location);
		$stmt->bindParam(':locationdesc', $locationdesc);
		$stmt->bindParam(':lat', $lat);
		$stmt->bindParam(':long', $long);
		$stmt->bindParam(':date', $date);
		$stmt->bindParam(':time', $time);
		$stmt->bindParam(':timestamp', $timestamp);
		$stmt->bindParam(':parameter', $parameter);
		$stmt->bindParam(':parameterdesc', $parameterdesc);
		$stmt->bindParam(':average', $average);
		$stmt->bindParam(':value', $value);
		$stmt->bindParam(':unit', $unit);
		$stmt->bindParam(':percentoflimit', $percentoflimit);
		$stmt->bindParam(':caqi', $caqi);
		$stmt->bindParam(':caqiclass', $caqiclass);
		$stmt->bindParam(':caqidesc', $caqidesc);
		$stmt->bindParam(':caqicolor', $caqicolor);
		$stmt->bindParam(':aqi', $aqi);
		$stmt->bindParam(':aqiclass', $aqiclass);
		$stmt->bindParam(':aqidesc', $aqidesc);
		$stmt->bindParam(':aqicolor', $aqicolor);
		$stmt->bindParam(':message', $message);

		$type = 'forecast';
		$message = null;
		$citiescounties = array_merge($cities,$counties);
		
		$query = $db->prepare("SELECT * FROM main.csv");
		$query->execute();
		$forecasts = $query->fetchAll();
		$query->closeCursor();

		for ($i = 0; $i<count($forecasts); $i++) {
			$id = $forecasts[$i]['city'];
			if (!isset($citiescounties[$id])) $citiescounties[$id] = array();
			list($county,$countydesc,$city,$citydesc,$location,$locationdesc,$lat,$long) = array_values($citiescounties[$id]); 
			$date = $forecasts[$i]['date'];
			$time = $forecasts[$i]['time'];
			$timestamp = strtotime($date.' '.$time.':00:00');
			$parameter = $forecasts[$i]['parameter'];
			$parameterdesc = $parameters[$parameter]['desc'];
			$unit = $parameters[$parameter]['unit'];
			$average = $forecasts[$i]['average'];
			$value = $forecasts[$i]['value'];
			$percentoflimit = percentOfLimit($parameter,$average,$value);
			list($caqi,$caqiclass,$caqidesc,$caqicolor) = caqi($parameter,$average,$value);
			list($aqi,$aqiclass,$aqidesc,$aqicolor) = aqi($parameter,$average,$value);
			$stmt->execute();
		}

// Forecasts: calculate AQI for every value
		$parameter = 'aqi';
		$parameterdesc = $parameters[$parameter]['desc'];
		$unit = $parameters[$parameter]['unit'];
		$average = '1h';
		$percentoflimit = null;
		foreach ($cities as $item) {
			list($county,$countydesc,$city,$citydesc,$location,$locationdesc,$lat,$long) = array_values($item);
			$dates = array(date('Y-m-d'),date('Y-m-d',strtotime('+1 day')),date('Y-m-d',strtotime('+2 days')));
			foreach ($dates as $date) {
				for ($time = 0; $time<24; $time++) {
					$timestamp = strtotime($date.' '.$time.':00:00');
					$valuearray = fetchValue($db,'aqi','main.forecast'," WHERE city = '$city' AND timestamp = $timestamp AND (average = '1h' OR average = '8h')");
					if ($valuearray) $value = max($valuearray); else $value = null;
					list($caqi,$caqiclass,$caqidesc,$caqicolor) = caqi($parameter,$average,$value);
					list($aqi,$aqiclass,$aqidesc,$aqicolor) = aqi($parameter,$average,$value);
					$stmt->execute();
				}
			}
		}

// Forecasts: write data from memory to file
		$db->exec("DROP TABLE IF EXISTS alldb.forecast");
		$db->exec("CREATE TABLE alldb.forecast AS SELECT * FROM main.forecast");
	}

// Alert: calculate from measurement and forecast
		$hashdba = fetchValue($db,'hash','alldb.verify'," WHERE type = 'alert'");
		$hasha = @md5_file($alerturl).$hashpm1024hy;
		if (($hasha == $hashdba)&&(!$getforecast)) $getalert = false;
	if ($getalert) {
		$db->exec("INSERT OR REPLACE INTO alldb.verify (type,hash) VALUES ('alert','$hasha')");
		$db->exec("CREATE TABLE main.alert(type text,province text,provincedesc text,county text,countydesc text,city text,citydesc text,location text,locationdesc text,lat float,long float,date text,time integer,timestamp integer,parameter text,parameterdesc text,average text,value integer,unit text,percentoflimit integer,caqi integer,caqiclass integer,caqidesc text,caqicolor text,aqi integer,aqiclass integer,aqidesc text,aqicolor text,message text)");
		$stmt = $db->prepare("INSERT INTO main.alert (type,province,provincedesc,county,countydesc,city,citydesc,location,locationdesc,lat,long,date,time,timestamp,parameter,parameterdesc,average,value,unit,percentoflimit,caqi,caqiclass,caqidesc,caqicolor,aqi,aqiclass,aqidesc,aqicolor,message) VALUES (:type,:province,:provincedesc,:county,:countydesc,:city,:citydesc,:location,:locationdesc,:lat,:long,:date,:time,:timestamp,:parameter,:parameterdesc,:average,:value,:unit,:percentoflimit,:caqi,:caqiclass,:caqidesc,:caqicolor,:aqi,:aqiclass,:aqidesc,:aqicolor,:message)");
		$stmt->bindParam(':type', $type);
		$stmt->bindParam(':province', $province);
		$stmt->bindParam(':provincedesc', $provincedesc);
		$stmt->bindParam(':county', $county);
		$stmt->bindParam(':countydesc', $countydesc);
		$stmt->bindParam(':city', $city);
		$stmt->bindParam(':citydesc', $citydesc);
		$stmt->bindParam(':location', $location);
		$stmt->bindParam(':locationdesc', $locationdesc);
		$stmt->bindParam(':lat', $lat);
		$stmt->bindParam(':long', $long);
		$stmt->bindParam(':date', $date);
		$stmt->bindParam(':time', $time);
		$stmt->bindParam(':timestamp', $timestamp);
		$stmt->bindParam(':parameter', $parameter);
		$stmt->bindParam(':parameterdesc', $parameterdesc);
		$stmt->bindParam(':average', $average);
		$stmt->bindParam(':value', $value);
		$stmt->bindParam(':unit', $unit);
		$stmt->bindParam(':percentoflimit', $percentoflimit);
		$stmt->bindParam(':caqi', $caqi);
		$stmt->bindParam(':caqiclass', $caqiclass);
		$stmt->bindParam(':caqidesc', $caqidesc);
		$stmt->bindParam(':caqicolor', $caqicolor);
		$stmt->bindParam(':aqi', $aqi);
		$stmt->bindParam(':aqiclass', $aqiclass);
		$stmt->bindParam(':aqidesc', $aqidesc);
		$stmt->bindParam(':aqicolor', $aqicolor);
		$stmt->bindParam(':message', $message);
		
		$type = 'alert';
		$citiescounties = array_merge($cities,$counties);
		$timestamp = strtotime(date('Y-m-d 00:00:00'));
		$date = date('Y-m-d');
		$time = 0;
		$yday = strtotime('-1 day',$timestamp);
		$parameter = 'alert';
		$parameterdesc = $parameters[$parameter]['desc'];
		$unit = $parameters[$parameter]['unit'];
		$average = '24h';
		$percentoflimit = $caqi = $caqiclass = $caqidesc = $caqicolor = $aqi = $aqiclass = $aqidesc = $aqicolor = null;
		
		$lvl = $msg = array();
		for ($i = 1; $i<8; $i++) {
			$lvl['obszar'.$i] = 0;
			for ($l = 0; $l<4; $l++) {
				$msg['obszar'.$i][$l] = '';
			}
		}
		
		if (urlOnline($alerturl)) {
			$file = @file($alerturl);
			foreach ($file as $line) {
				$record = explode(';',$line);
				if (($record[2] == 0)||($record[2] == 1)||($record[2] == 2)) {
					$msg[$record[0]][$record[2]] = $record[3];
				}
				elseif (($record[2] == 3)&&($record[1] == date('Y-m-d'))) {
					$lvl[$record[0]] = 3;
					$msg[$record[0]][3] = $record[3];
				}
			}
		}
		foreach ($regions as $location => $regiondata) {
			$liststations = explode(',',$regiondata['regionstations']);
			foreach ($liststations as $key => $station) {
				$ydaylvl[$location][$key] = fetchValue($db,'value','alldb.measurement'," WHERE location = '$station' AND timestamp = $yday AND parameter = 'pm10' AND average = '24h'");
			}
			$ydaylvl[$location] = max($ydaylvl[$location]);
			$listcities = explode(',',$regiondata['regioncities']);
			foreach ($listcities as $key => $city) {
				$tdaylvl[$location][$key] = fetchValue($db,'value','alldb.forecast'," WHERE city = '$city' AND timestamp = $timestamp AND parameter = 'pm10' AND average = '24h'");
			}
			$tdaylvl[$location] = max($tdaylvl[$location]);
			if ($lvl[$location] !== 3) {
				if (($ydaylvl[$location] > 200)||($tdaylvl[$location] > 200)) $lvl[$location] = 2;
				elseif (($ydaylvl[$location] > 50)&&($tdaylvl[$location] > 50)) $lvl[$location] = 1;
			}
			foreach ($listcities as $id) {
				list($county,$countydesc,$city,$citydesc,,,$lat,$long) = array_values($citiescounties[$id]);
				$locationdesc = $regiondata['locationdesc'];
				$value = $lvl[$location];
				$message = $msg[$location][$value];
				$stmt->execute();
			}
		}

// Alert: write data from memory to file
		$db->exec("DROP TABLE IF EXISTS alldb.alert");
		$db->exec("CREATE TABLE alldb.alert AS SELECT * FROM main.alert");
	}

// List: available cities and locations
	if ($getlist) {
		$db->exec("INSERT OR REPLACE INTO alldb.verify (type,hash) VALUES ('list','$hashl')");
		$db->exec("CREATE TABLE main.list(type text,province text,provincedesc text,county text,countydesc text,city text,citydesc text,location text,locationdesc text,locationtype text,lat float,long float,parameterslist text,parametersdesclist text,unitslist text)");
		$stmt = $db->prepare("INSERT INTO main.list (type,province,provincedesc,county,countydesc,city,citydesc,location,locationdesc,locationtype,lat,long,parameterslist,parametersdesclist,unitslist) VALUES (:type,:province,:provincedesc,:county,:countydesc,:city,:citydesc,:location,:locationdesc,:locationtype,:lat,:long,:parameterslist,:parametersdesclist,:unitslist)");
		$stmt->bindParam(':type', $type);
		$stmt->bindParam(':province', $province);
		$stmt->bindParam(':provincedesc', $provincedesc);
		$stmt->bindParam(':county', $county);
		$stmt->bindParam(':countydesc', $countydesc);
		$stmt->bindParam(':city', $city);
		$stmt->bindParam(':citydesc', $citydesc);
		$stmt->bindParam(':location', $location);
		$stmt->bindParam(':locationdesc', $locationdesc);
		$stmt->bindParam(':locationtype', $locationtype);
		$stmt->bindParam(':lat', $lat);
		$stmt->bindParam(':long', $long);
		$stmt->bindParam(':parameterslist', $parameterslist);
		$stmt->bindParam(':parametersdesclist', $parametersdesclist);
		$stmt->bindParam(':unitslist', $unitslist);
		
		$type = 'list';
		$alllocations = array_merge($stations,$cities,$counties);
		foreach ($alllocations as $record) {
			list($county,$countydesc,$city,$citydesc,$location,$locationdesc,$lat,$long,$locationtype,$parameterslist) = array_values($record);
			$parametersarray = explode(',',$parameterslist);
			foreach ($parametersarray as $key => $parameter) {
				$parametersdescarray[$key] = $parameters[$parameter]['desc'];
				$unitsarray[$key] = $parameters[$parameter]['unit'];
			}
			$parametersdesclist = implode(',',$parametersdescarray);
			$unitslist = implode(',',$unitsarray);
			$stmt->execute();
		}

// List: write data from memory to file
		$db->exec("DROP TABLE IF EXISTS alldb.list");
		$db->exec("CREATE TABLE alldb.list AS SELECT * FROM main.list");		
	}

// Write measurements and forecasts data to temporary database
	$db->exec("ATTACH '".dirname(__FILE__).DIRECTORY_SEPARATOR."temp.db' as tempdb");
	$db->exec("CREATE TABLE tempdb.measurement AS SELECT * FROM alldb.measurement");
	$db->exec("CREATE TABLE tempdb.forecast AS SELECT * FROM alldb.forecast");
	$db->exec("CREATE TABLE tempdb.lastmeasurement AS SELECT * FROM alldb.lastmeasurement");
	$db->exec("CREATE TABLE tempdb.smartmeasurement AS SELECT * FROM alldb.smartmeasurement");
	$db->exec("CREATE TABLE tempdb.alert AS SELECT * FROM alldb.alert");
	$db->exec("CREATE TABLE tempdb.list AS SELECT * FROM alldb.list");
	$db->exec("DETACH tempdb");
	$db->exec("DETACH alldb");
	$db = null;

// Copy data to final database
	if (file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.'temp.db')) {
		copy (dirname(__FILE__).DIRECTORY_SEPARATOR.'temp.db',dirname(__FILE__).DIRECTORY_SEPARATOR.'data.db');
		unlink (dirname(__FILE__).DIRECTORY_SEPARATOR.'temp.db');
	}
	}
}
catch(PDOException $e) {
	echo $e->getMessage();
}
?>
