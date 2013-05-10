<?php
//Apertura in lettura xml remoto contenente gli hot spot
if (!($fp=@fopen("http://85.18.173.117/mappe/AccessPoint.xml", "r"))) die ("Couldn't open remote XML.");
//Apertura in scrittura xml locale che conterrà i dati sugli hot spot formattati per la scrittura sul db e la presentazione
$write_file = fopen("/var/www/cakephp/app/tmp/AccessPointModified.xml", "w");
//Vettore contenente le regole per la formattazione dei dati
$conversion_chars = array (
		"à" => "a",
		"è" => "e",
		"é" => "e",
		"ì" => "i",
		"ò" => "o",
		"ù" => "u",
		"&apos;" => " ");
//Eliminazione BOM (Byte Order Mark)
$data = fread($fp, 3);
//Formattazione dati
while($data = fread($fp, 4096)) {
	//Preservazione apostrofo e doppie virgolette
	$cerca = array("'", '"');
	$token = array('PIPP0', 'PLUT0');
	$data = utf8_encode(str_replace($cerca, $token, utf8_decode($data)));
	//Sostituzione caratteri nel vettore conversion_chars
	$data = utf8_encode(str_replace (array_keys ($conversion_chars), array_values ($conversion_chars), utf8_decode($data)));
	//Eliminazione caratteri non-ASCII
	$data = preg_replace('/[^(\x20-\x7F)]*/','', $data);
	//Ripristino apostrofo e doppie virgolette
	$data = utf8_encode(str_replace($token, $cerca, $data));
	//Scrittura su file locale
	fwrite($write_file, $data);
}
fclose($fp);
fclose($write_file);
echo('Importazione in locale e formattazione dei dati completata. ');

if (!($fp=@fopen("/var/www/cakephp/app/tmp/AccessPointModified.xml", "r"))) die ("Couldn't open XML.");
$usercount=0;
$userdata=array();
$state='';
if (!($xml_parser = xml_parser_create())) die("Couldn't create parser."); 
  
function startElementHandler ($parser,$name,$attrib){
	global $usercount;
	global $userdata;
	global $state;
	$state = $name;
}

function endElementHandler ($parser,$name){
	global $usercount;
	global $userdata;
	global $state;
	$state='';
	if($name=="ACCESSPOINT") {$usercount++;}
}

function characterDataHandler ($parser, $data) {
	global $usercount;
	global $userdata;
	global $state;
	if (!$state) {return;}
	if ($state=="DENOMINAZIONE") { $userdata[$usercount]["denominazione"] = $data;}
	if ($state=="LATITUDINE") { $userdata[$usercount]["latitudine"] = $data;}
	if ($state=="LONGITUDINE") { $userdata[$usercount]["longitudine"] = $data;}
	if ($state=="INDIRIZZO") { $userdata[$usercount]["indirizzo"] = $data;}
	if ($state=="COMUNE") { $userdata[$usercount]["comune"] = $data;}
	if ($state=="TIPOLOGIA") { $userdata[$usercount]["tipologia"] = $data;}
}

xml_set_element_handler($xml_parser,"startElementHandler","endElementHandler");
xml_set_character_data_handler( $xml_parser, "characterDataHandler");

while($data = fread($fp, 4096)) {
	if(!xml_parse($xml_parser, $data, feof($fp))) {
		break;
	}
}

xml_parser_free($xml_parser);
$query = "DELETE FROM markers WHERE category_id=30";
if (executeQuery($query)) {
	echo('DB Markaspot cancellato. ');}
else {
	echo('Errore cancellazione DB Markaspot. ');
}
for ($i=0;$i<$usercount; $i++) {
	insertContact($i, $userdata[$i]["denominazione"], $userdata[$i]["latitudine"], $userdata[$i]["longitudine"], $userdata[$i]["indirizzo"], $userdata[$i]["comune"]); 
}
echo('DB Markaspot aggiornato. ');

function insertContact($id, $denominazione, $lat, $lon, $ind, $comune) {
    $query = "INSERT INTO markers (id,     gov_id, user_id,     status_id, district_id, source_id, subject,     description,          category_id, street,  zip,    city,       address_id, lat,     lon,     created, modified, rating, votes, voting_pro, voting_con, voting_abs, feedback, media_url, event_start, event_end, spots)
                       	values    (".$id.",0,      '0000000000',1,         NULL,        '',      'deprecated','Hot Spot installato',30,          '".$ind."','','".$comune."',NULL,      ".$lat.",".$lon.", NULL,    NULL,     0.0,    0,     0,          0,          0,          1,       '',          NULL,        NULL, 1) ON DUPLICATE KEY UPDATE spots=spots+1";
	$result = executeQuery($query);
}

function executeQuery($query) {
    $DB_SERVER = "localhost";
    $DB_USERNAME = "root";
    $DB_PASSWORD = "zerodd";
    $DB_NAME = "markaspot";

    $connect = mysql_connect($DB_SERVER, $DB_USERNAME, $DB_PASSWORD);
    $db = mysql_select_db($DB_NAME, $connect);  
    
    $result = mysql_query($query, $connect) or die("Errore esecuzione query su db.");
    mysql_close($connect);
    return $result;
}
?>
