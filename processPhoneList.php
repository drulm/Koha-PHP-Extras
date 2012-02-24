<?php
## Process phone list

## Copyright 2008 Darrell Ulm

## This file is part of koha-extras.

## koha-extras is free software; you can redistribute it and/or modify
## it under the terms of the GNU General Public License as published by
## the Free Software Foundation; either version 2 of the License, or
## (at your option) any later version.

## koha-extras is distributed in the hope that it will be useful,
## but WITHOUT ANY WARRANTY; without even the implied warranty of
## MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
## GNU General Public License for more details.

## You should have received a copy of the GNU General Public License
## along with koha-extras; if not, write to the Free Software
## Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

ini_set("memory_limit","256M");
// Set this to your Koha staff intranet base address
require_once("kohafunctions.php");
// Set this to your Koha staff intranet base address
require_once("addrkoha.php");
// Print the main top menu
require_once("topmenu.php");

// Get some marc fields
require_once("File/MARC.php");


$message = $_POST['message'];
$lname = $_POST['lname'];
$fname = $_POST['fname'];
$holidays = $_POST['holidays'];
$collection	= $_POST['collection'];
$email	= $_POST['email'];

// Check for holidays
$sweek = $phoneweek;
$holidays = get_holidays($holidays, $local_holiday, $sweek, $koha_db, $local_login, $local_password, $local_ip, $local_name);
echo "<hr>Holidays = $holidays<hr>";

if($_POST['daysAgo1'] > $_POST['daysAgo2']){
	$daysAgo[0] = $_POST['daysAgo1'] + $holidays;
	$daysAgo[1] = $_POST['daysAgo2'];
} else {
	$daysAgo[0] = $_POST['daysAgo2'] + $holidays;
	$daysAgo[1] = $_POST['daysAgo1'];
}

// Open the database
$conn2 = open_database($koha_db, $koha_login, $koha_password, $koha_ip, $koha_name);

//Check to make sure all neccessary data exists
$anyErrors = false;
if (count($daysAgo) < 2 ) {
	echo "You must fill in both 'Days Ago' boxes<br>";
	$anyErrors = true;
}
if($anyErrors){
	echo "Please use your browser's back button to return to the last page and fill in the missing data.<br>";
	echo "<br>Thank you. - Management<br>";
	die();
}
$daysAgo[0]++;

//Grab the data from the database
$sql = 'select  *, count(*), 
        GROUP_CONCAT(DISTINCT biblio.title ORDER BY items.biblionumber DESC SEPARATOR " <br> ") as titles,
		GROUP_CONCAT(DISTINCT items.itype ORDER BY items.biblionumber DESC SEPARATOR " <br> ") as itypes,
		reserves.timestamp, 
		DATE_ADD(reserves.timestamp, INTERVAL 7 day) as date7, 
		DATE_ADD(reserves.timestamp, INTERVAL 3 day) as date3,
		marc
	from 
		reserves , borrowers, biblio, items, biblioitems
	where 
	reserves.borrowernumber = borrowers.borrowernumber
		AND
	reserves.biblionumber = biblio.biblionumber
		AND
	items.biblionumber = items.biblionumber
		AND
	reserves.biblionumber = items.biblionumber
		AND
	cancellationdate IS NULL
		AND
	found = "W" 
		AND
	reserves.timestamp >= DATE_SUB(CURDATE(), INTERVAL '.$daysAgo[0].' DAY)
		AND
	reserves.timestamp <= DATE_SUB(CURDATE(), INTERVAL '.$daysAgo[1].' DAY) 
		AND 
	biblio.biblionumber = biblioitems.biblionumber';
$sql .= build_patron_categories($collection);
$sql .= " GROUP BY borrowers.borrowernumber ORDER BY borrowers.surname, borrowers.firstname, biblio.title";

// Perform Query
$resultSet = dbquery($conn2, $sql);
if (numrows($resultSet) == 0) die("No Patrons to Call");

echo '<table style="text-align: left; width: 100%;" border="1"
	cellpadding="2" cellspacing="2">
	<tr>
		<th><strong>On Shelf Date</th>
		<th><strong>Pick Up By</th>
		<th><strong>Patron ID</strong></th>
		<th><strong>Last Name</strong></th>
		<th><strong>First Name</strong></th>
		<th><strong>Phone</strong></th>
		<th><strong>Alt Phone<br />or email</strong></th>
		<th><strong>I Type</strong></th>
		<th><strong>Title</strong></th>
	</tr>';
	$color = 'white';
echo "<center><h2>Phone - Call List for Reserve Shelf</h2>";
echo "<br>";
echo "<h3>".date('l dS \of F Y h:i:s A')."</h3>";
echo "<br>";
//echo "<br><b>Number of Patrons to Call:  ".numrows($resultSet)."</b><br></center>";
	
while ($row = read_db_assoc($resultSet))
	{
	if ( $row['email'] == "" || $email )
		{
		$row = stripslashes_deep($row);
		echo "	<tr bgcolor='" . $color . "'>";
		echo "<td>" . substr($row['timestamp'], 0, 10) . "</td>";
		$pickupdate = $row['date3']; 
		if ( strpos($row['itypes'], "DVD") === false )
			$pickupdate = $row['date7'];
		echo "<td><b>" . substr($pickupdate, 0, 10) . "</b></td>";
		echo '<td> <a href="'. $addrkoha433 . $more_member . stripslashes($row['borrowernumber']) .'">'.stripslashes($row['userid']).'</a> </td>';
		echo "<td>" . $row['surname']. "</td>
			<td>" . $row['firstname'] . "</td>
			<td><b>" . $row['phone'] . "</b></td>
			<td>" . $row['altphone'] ."<br />" .$row['email'] . "</td>
			<td>" . $row['itypes'] . "</td>";
	//		<td><b><i>" . $row['titles'] . "</i></b></td>
	//	echo '<td> <b><a href="'. $addrkoha433 . $bib_detail . stripslashes($row['biblionumber']) .'">'.$row['titles'].'</a> </b></td>';	
				echo '<td><b> <a href="'.$addrkoha433. $bib_detail . 
					$row['biblionumber'] .'">' .$row['title'];
				$pretitle2 = getmarctag($row['marc'], "245", "b");
				$title2 =  substr($pretitle2,0,strlen($pretitle2)-1);
				echo '</a></b></td>';	
		echo "</tr>";
		if ($color == 'white') {$color = 'lightgray';} else {$color = 'white';}
		}
	}

function getmarctag($marc, $findtag, $subtag) 
{
$found = 0;
// Retrieve a set of MARC records from a z39 result string
$bibrecords = new File_MARC($marc, File_MARC::SOURCE_STRING);

try 
	{
	// Go through each record
	while ($record = $bibrecords->next()) 
		{
		// Iterate through the fields
		foreach ($record->getFields() as $tag => $subfields) 
			{
			// Skip everything except for 650 fields
			if ($tag == $findtag) 
					{
					foreach ($subfields->getSubfields() as $code => $value) {
						if ($code == $subtag) echo " <br /> ".substr($value,5,strlen($value)-5) ;
					$found = 1;
					//break;
					}
				}
			}
		}
	} 
catch (Exception $e) 
		{
//		echo 'Caught exception: ',  $e->getMessage(), "\n";
		}		
return $found; 	
}

// close database
disconnect($conn2);
?>
