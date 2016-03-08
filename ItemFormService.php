<!DOCTYPE html>
<html>
<head>
	<title>Item submission page</title>
	<meta charset="UTF-8">
	<style>
	label {
		display:inline-block;
		width:80px;
		text-align: right;
	}
	table {
    border-collapse: collapse;
	}

	table, th, td {
	    border: 1px solid black;
	}
	tr:nth-child(even) {
    	background: #fed;
	} 

	thead tr:nth-child(1) {
    	background: #def;
	} 

	</style>
</head>
<body>

<?php 
include_once("../itemspw.php");
$pdo = null;

function getpost($str)
{
	if (!empty($_POST[$str])){
		$ut = $_POST[$str];
	} else {
		$ut = null;
	}		
	return $ut;
}

if (!empty($_GET["userid"])){
	$userid = $_GET["userid"];
} else {
	$userid ="UNK";
	$debug = "Du har inte anget ett anvandarid, tex a12marbr";
}

$us=getPost("userid");
if($us!=null) $userid=$us;

$itemname=getPost("itemname");
$itemid=getPost("itemid");
$kind=getPost("kind");
$location=getPost("location");
$size=getPost("size");
$cost=getPost("cost");
$starttime=getPost("starttime");
$endtime=getPost("endtime");
$information=getPost("information");
$action=getPost("action");

echo "<h1>Item Submission page</h1>";
echo "<form method='post' action='ItemFormService.php' id='items'>";
echo "<label>Userid:</label><input type='text' name='userid' placeholder='a12marbr' required value='".$userid."'><input type='submit' value='view'><br>";
echo "<label>Itemname:</label><input type='text' name='itemname' placeholder='Labrador' value='".$itemname."'><br>";
echo "<label>Kind:</label><input type='text' name='kind' placeholder='Hund' value='".$kind."'><br>";
echo "<label>Location:</label><input type='text' name='location' placeholder='G&ouml;tene' value='".$location."'><br>";
echo "<label>Size:</label><input type='text' name='size' placeholder='25' value='".$size."'><br>";
echo "<label>Cost:</label><input type='text' name='cost' placeholder='999.99' value='".$cost."'><br>";
echo "<label>Starttime:</label><input type='text' name='starttime' placeholder='2016-01-01 17:00:00' value='".$starttime."'><br>";
echo "<label>Endtime:</label><input type='text' name='endtime' placeholder='2016-01-02 07:59:59' value='".$endtime."'><br>";
echo "<label>Information:</label><textarea rows='4' cols='50' name='information' form='items' placeholder='{\"Stamtavla\":[{\"far\":\"Kjell\"},{\"mor\":\"Yvonne\"}],\"alder\":12}'>".$information."</textarea><br>";
echo "<input type='submit' value='Submit'>";
echo "<input type='hidden' name='action' value='INS'>";
echo "</form>";

$error=null;
$debug = "";
$queryFields="Userid";
$setFields=":userid";

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

// If an insert command do the following!
if($action === "INS"){
		$sql = "INSERT INTO items (userid,itemname,kind,location,size,cost,starttime,endtime,information) VALUES (:userid,:itemname,:kind,:location,:size,:cost,:starttime,:endtime,:information);";
		$query = $pdo->prepare($sql);

		$info = json_decode($information);
		if ($info === null){
			$info = $information;
		}
		
		$query->bindParam(":userid", $userid);
		$query->bindParam(":itemname", $itemname);
		$query->bindParam(":kind", $kind);
		$query->bindParam(":location", $location);
		$query->bindParam(":size", $size);
		$query->bindParam(":cost", $cost);
		$query->bindParam(":starttime", $starttime);
		$query->bindParam(":endtime", $endtime);
		$query->bindParam(":information", json_encode($info));
		
		if(!$query->execute()){
			$error=$query->errorInfo();
			$debug="Error reading items entries ".$error[2];
		}
}

// If a delete command do the following
if($action === "DEL"){
		$sql = "DELETE FROM items where itemid=:itemid;";
		$query = $pdo->prepare($sql);

		$query->bindParam(":itemid", $itemid);
		
		if(!$query->execute()){
			$error=$query->errorInfo();
			$debug="Error deleting items".$error[2];
		}
}

// Irrespective of whether it is a delete or an insert we show the table!
$query = $pdo->prepare("SELECT itemid,userid,itemname,kind,location,size,cost,starttime,endtime,information FROM items where Userid=:userid;");
$query->bindParam(':userid', $userid);
if(!$query->execute()){
	$error=$query->errorInfo();
	$debug="Error reading items entries ".$error[2];
}

echo "<table><caption>Item data stored for user <strong>".$userid."</strong></caption><thead>";
echo "<tr><th>itemid</th><th>itemname</th><th>kind</th><th>location</th><th>size</th><th>cost</th><th>starttime</th><th>endtime</th><th>information</th><th></th></tr></thead><tbody>";
foreach($query->fetchAll(PDO::FETCH_ASSOC) as $row){
		echo "<tr>";

		echo "<td>".$row["itemid"]."</td>";
		echo "<td>".$row["itemname"]."</td>";
		echo "<td>".$row["kind"]."</td>";
		echo "<td>".$row["location"]."</td>";
		echo "<td>".$row["size"]."</td>";
		echo "<td>".$row["cost"]."</td>";
		echo "<td>".$row["starttime"]."</td>";
		echo "<td>".$row["endtime"]."</td>";
		echo "<td>".$row["information"]."</td>";

		echo "<td><form method='post' action='ItemFormService.php'>";
		echo "<input type='hidden' name='action' value='DEL' />";
		echo "<input type='hidden' name='itemid' value='".$row["itemid"]."' />";
		echo "<input type='hidden' name='userid' placeholder='a12marbr' required value='".$userid."' />";
		echo "<input type='hidden' name='itemname' placeholder='Labrador' value='".$itemname."' />";
		echo "<input type='hidden' name='kind' placeholder='Hund' value='".$kind."' />";
		echo "<input type='hidden' name='location' placeholder='G&ouml;tene' value='".$location."' />";
		echo "<input type='hidden' name='size' placeholder='25' value='".$size."' />";
		echo "<input type='hidden' name='cost' placeholder='999.99' value='".$cost."' />";
		echo "<input type='hidden' name='starttime' placeholder='2016-01-01 17:00:00' value='".$starttime."' />";
		echo "<input type='hidden' name='endtime' placeholder='2016-01-02 07:59:59' value='".$endtime."' />";
		echo "<input type='submit' value='Delete'>";
		echo "</form></td>";

}

echo "</tbody><table>";

if($error!=null){
		echo "<pre>";
		print_r($error);
		echo "</pre>";	
}

?>

</body>
</html>