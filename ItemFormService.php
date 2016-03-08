<!DOCTYPE html>
<html>
<head>
	<title>Item submission page</title>
	<meta charset="UTF-8">
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
$kind=getPost("kind");
$location=getPost("location");
$size=getPost("size");
$cost=getPost("cost");
$starttime=getPost("starttime");
$endtime=getPost("endtime");
$info=getPost("info");
$INS=getPost("INS");
$DEL=getPost("DEL");

 echo "<form method='post' action='ItemFormService.php' id='items'>";
 echo "Userid: <input type='text' name='userid' placeholder='a12marbr' required value='".$userid."'><br>";
 echo "Itemname: <input type='text' name='itemname' placeholder='Labrador' value='".$itemname."'><br>";
 echo "Kind: <input type='text' name='kind' placeholder='Hund' value='".$kind."'><br>";
 echo "Location: <input type='text' name='location' placeholder='G&ouml;tene' value='".$location."'><br>";
 echo "Size: <input type='text' name='size' placeholder='25' value='".$size."'><br>";
 echo "Cost: <input type='text' name='cost' placeholder='1000' value='".$cost."'><br>";
 echo "Starttime: <input type='text' name='starttime' placeholder='1000' value='".$starttime."'><br>";
 echo "Endtime: <input type='text' name='endtime' placeholder='1000' value='".$endtime."'><br>";
 echo "<input type='submit' value='Submit'>";
 echo "<input type='hidden' name='INS' value='OK'>";
 echo "Info: <textarea rows='4' cols='50' name='information' form='items' placeholder='{\"Stamtavla\":[{\"far\":\"Kjell\"},{\"mor\":\"Yvonne\"}],\"alder\":12}'>".$info."</textarea>";
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
if($INS!=null){
		$sql = "INSERT INTO items (userid,itemname,kind,location,size,cost,starttime,endtime,info) VALUES (:userid,:itemname,:kind,:location,:size,:cost,:starttime,:endtime,:info);";
		$query = $pdo->prepare($sql);
		
		$query->bindParam(":userid", $userid);
		$query->bindParam(":itemname", $itemname);
		$query->bindParam(":kind", $kind);
		$query->bindParam(":location", $location);
		$query->bindParam(":size", $size);
		$query->bindParam(":cost", $cost);
		$query->bindParam(":starttime", $starttime);
		$query->bindParam(":endtime", $endtime);
		$query->bindParam(":info", $info);
		
		if(!$query->execute()){
			$error=$query->errorInfo();
			$debug="Error reading items entries ".$error[2];
		}
}

// If a delete command do the following
if($DEL!=null){
		$sql = "DELETE FROM items where itemid=:itemid;";
		$query = $pdo->prepare($sql);
		
		$query->bindParam(":itemid", $DEL);
		
		if(!$query->execute()){
			$error=$query->errorInfo();
			$debug="Error deleting items".$error[2];
		}
}

// Irrespective of whether it is a delete or an insert we show the table!
$query = $pdo->prepare("SELECT itemid,userid,itemname,kind,location,size,cost,starttime,endtime,info FROM items where Userid=:userid;");
$query->bindParam(':userid', $userid);
if(!$query->execute()){
	$error=$query->errorInfo();
	$debug="Error reading items entries ".$error[2];
}

echo "<table>";
echo "<tr><th>itemid</th><th>itemname</th><th>kind</th><th>location</th><th>size</th><th>cost</th><th>starttime</th><th>endtime</th><th>info</th></tr>";
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
		echo "<td>".$row["info"]."</td>";

		echo "<td><form method='post' action='ItemFormService.php'><input name='DEL' type='hidden' value='".$row["itemid"]."' /><input type='submit' value='DEL!' />";
		 echo "<input type='hidden' name='userid' placeholder='a12marbr' required value='".$userid."'>";
		 echo "<input type='hidden' name='itemname' placeholder='Labrador' value='".$itemname."'>";
		 echo "<input type='hidden' name='kind' placeholder='Hund' value='".$kind."'>";
		 echo "<input type='hidden' name='location' placeholder='G&ouml;tene' value='".$location."'>";
		 echo "<input type='hidden' name='size' placeholder='25' value='".$size."'>";
		 echo "<input type='hidden' name='cost' placeholder='1000' value='".$cost."'>";
		 echo "<input type='hidden' name='starttime' placeholder='1000' value='".$starttime."'>";
		 echo "<input type='hidden' name='endtime' placeholder='1000' value='".$endtime."'>";
	echo "</form></td>";

}

echo "<table>";

if($error!=null){
		echo "<pre>";
		print_r($error);
		echo "</pre>";	
}

?>

</body>
</html>