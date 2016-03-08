<!DOCTYPE html>
<html>
<head>
	<title>Item submission page</title>
	<meta charset="UTF-8">
</head>
<body>
 <form method="post" action="ItemFormService.php" id="items">
  Userid: <input type="text" name="userid" placeholder="a12marbr" required><br>
  Itemname: <input type="text" name="itemname" placeholder="Labrador"><br>
  Sort: <input type="text" name="sort" placeholder="Hund"><br>
  Location: <input type="text" name="location" placeholder="Götene"><br>
  Langd: <input type="text" name="langd" placeholder="Sverige"><br>
  Pris: <input type="text" name="pris" placeholder="999.90"><br>
  Starttime: <input type="text" name="starttime" placeholder="2016-01-01 17:00:00"><br>
  Endtime: <input type="text" name="endtime" placeholder="2016-01-02 08:00:00"><br>
  <input type='submit' value='Submit'>
  <input type="hidden" name="INS" value="OK">
  Info: <textarea rows="4" cols="50" name="information" form="items" placeholder='{"Stamtavla":[{"far":"Kjell"},{"mor":"Yvonne"}],"ålder":12}'></textarea>
</form>


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

if (!empty($_GET["userid"])){
	$userid = $_GET["userid"];
} else {
	$userid ="UNK";
	$debug = "Du har inte anget ett anvandarid, tex a12marbr";
}

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
		$sql = "INSERT INTO items (userid,itemname,kind,location,size,cost,starttime,endtime,info) VALUES (:userid,:itemname,:kind,:length,:cost,:starttime,:endtime,:info);";
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

		echo "<td><form method='post' action='ItemFormService.php'><input name='DEL' type='hidden' value='".$row["itemid"]."' /></form></td>";

}

echo "<table>";

?>

</body>
</html>