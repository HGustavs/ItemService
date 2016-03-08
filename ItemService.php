<?php 
include_once("itemspw.php");
$pdo = null;
$debug = "";

if (!empty($_GET["userid"])){
	$userid = $_GET["userid"];
} else {
	$userid ="UNK";
	$debug = "Du har inte anget ett anvandarid, tex a12marbr";
}

try {
	$pdo = new PDO(
		'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8',
		DB_USER,
		DB_PASSWORD
	);
} catch (PDOException $e) {
	echo "Failed to get DB handle: " . $e->getMessage() . "</br>";
	exit;
}

$entries=array();
$query = $pdo->prepare("SELECT Itemid,Userid,Itemname,Sort,Location,Langd,Pris,Starttime,Endtime,Information FROM items where Userid=:userid;");
$query->bindParam(':userid', $userid);
if(!$query->execute()){
	$error=$query->errorInfo();
	$debug="Error reading items entries ".$error[2];
}
foreach($query->fetchAll(PDO::FETCH_ASSOC) as $row){
	// Check if Information is valid JSON
	$info = json_decode($row['Information']);
	if ($info === null){
		$info = $row['Information'];
	}
	$entry = array(
		'itemid' => $row['Itemid'],
		'userid' => $row['Userid'],
		'itemname' => $row['Itemname'],
		'sort' => $row['Sort'],
		'location' => $row['Location'],
		'langd' => $row['Langd'],
		'pris' => $row['Pris'],
		'starttime' => $row['Starttime'],
		'Endtime' => $row['Endtime'],	
		'Information' => $info				
	);
	array_push($entries, $entry);
}
$array = array(
	'entries' => $entries,
	"debug" => $debug,
);
date_default_timezone_set("Europe/Stockholm");
header('Content-Type: application/json; charset=utf-8');

echo json_encode($array, JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE);
?>
