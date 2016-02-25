<?php
/**
  * @author Aristide Asprogerakas <aristideau@gmail.com>
  */

$DB_HOST = 'localhost';
$DB_NAME = 'test';
$DB_USER = 'test';
$DB_PASS = 'test';
$DB_PORT = '3306';

try {
	$db = new PDO("mysql:host=$DB_HOST;port=$DB_PORT;dbname=$DB_NAME", $DB_USER, $DB_PASS);
	$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);	
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}

$sql = "SELECT 
	customer.customer_id, 
	customer.first_name, 
	customer.last_name,
	IF(customer_occupation.occupation_name IS NULL, 'un-employed', customer_occupation.occupation_name) AS occupation_name 
FROM customer   
LEFT OUTER JOIN customer_occupation 
ON customer.customer_occupation_id = customer_occupation.customer_occupation_id";

$sqlOccupation = "SELECT customer_occupation.occupation_name
FROM customer_occupation";

?>

<select onchange="self.location='?occupation_name='+this.value">
	<option value="All" <?php echo ($_GET['occupation_name'] == 'All' ? 'selected="selected"' : ""); ?> >All</option>
<?php	foreach($db->query($sqlOccupation) as $row) { ?>	 
	<option value="<?php echo $row["occupation_name"]; ?>" <?php echo ($_GET['occupation_name'] == $row["occupation_name"] ? 'selected="selected"' : ""); ?>><?php echo $row["occupation_name"]; ?></option>
<?php	} ?>
	<option value="un-employed" <?php echo ($_GET['occupation_name'] == 'un-employed' ? 'selected="selected"' : ""); ?> >un-employed</option>
</select> 

<?php	 
	if($_GET['occupation_name'] == 'un-employed'){
		$sql .= " WHERE occupation_name IS NULL";
		$res = $db->prepare($sql);
		$res->execute();
	}elseif(($_GET['occupation_name'] == 'All')||(!isset($_GET['occupation_name']))){
		$res = $db->prepare($sql);
		$res->execute();
	}else{
		$sql .= " WHERE occupation_name = :occupation_name";
		$res = $db->prepare($sql);
		$res->execute(array('occupation_name' => $_GET['occupation_name']));
	}	
?>	

<h2>Customer List</h2>
<table>
	<tr>
		<th>Customer ID</th>
		<th>First Name</th>
		<th>Last Name</th>
		<th>Occupation</th>
	</tr>
<?php	foreach($res as $row) { ?>
	<tr>
		<td><?php echo $row["customer_id"]; ?></td>
		<td><?php echo $row["first_name"]; ?></td>
		<td><?php echo $row["last_name"]; ?></td>
		<td><?php echo $row["occupation_name"]; ?></td>
	</tr>
<?php	} ?>
</table>
