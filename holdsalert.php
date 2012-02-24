<?php
## Holds Ratio List

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

// Set this to your Koha staff intranet base address
require_once('addrkoha.php');
// Set this to your Koha staff intranet base address
require_once("kohafunctions.php");
// Print the main top menu
require_once("topmenu.php");

// Get some marc fields
require_once("File/MARC.php");

echo "<center><h2>Holds Alert Ratio = $reserve_alert_ratio <br /> DVD Ratio = $reserve_alert_ratio_dvd </h2></center>";

$conn = open_database($koha_db, $koha_login, $koha_password, $koha_ip, $koha_name);

// Get all the bibs that have some reserve, that have not been cancelled
$query = 
	'select title, author, reserves.biblionumber , marc, count(*)
	from 
		reserves, biblio, biblioitems
	where  		
		biblio.biblionumber = reserves.biblionumber
		and biblio.biblionumber = biblioitems.biblionumber
		and found IS NULL
		and cancellationdate IS NULL
	group by reserves.biblionumber
	order by title';

// Perform Query
$result = dbquery($conn, $query);
if (($numrows = numrows($result)) == 0) die("No data found");

echo "<table border=1>";
echo "<tr> 
		<th>Title</th>
		<th>Author</th> 
		<th>Call No.</th> 
		<th>Itype</th> 
		<th>Collection</th> 
		<th>Reserves</th> 
		<th>Items</th> 
		<th>On Order<br>Pending</th> 
		<th>You should<br>order</th> 
		<th>Verified<br />Always Check #On Order</th> 
	</tr>";
	
// Loop through these possible reserves
while ($row = read_db_assoc($result))
	{
	$row = stripslashes_deep($row);
	$rescount = $row['count(*)'];
	
	// Pull some item information - good enough, don't loop through it, although we could
	$query2 = 
		"select count(*), itype, location, itemcallnumber, barcode
		from items
		where
		biblionumber = ".$row['biblionumber']." 
		group by biblionumber";
	$result2 = dbquery($conn, $query2);

	$row2 = read_db_assoc($result2);
	$row2 = stripslashes_deep($row2);

	$itemcount = $row2['count(*)'];
	$orderedbar = stripos($row2['barcode'], "on order");
	$orderedcall = stripos($row2['itemcallnumber'], "on order");
	
	// Pull some counts of acqui. orders
	$query3 = 
		"select count(*), sum(quantity), sum(quantityreceived)
		from aqorders
		where
		biblionumber = ".$row['biblionumber']." 
		group by biblionumber";

	$result3 = dbquery($conn, $query3);

	$row3 = read_db_assoc($result3);
	$row3 = stripslashes_deep($row3);

	$ordcount = $row3['count(*)'];
	$ordquant = $row3['sum(quantity)'] - $row3['sum(quantityreceived)'];
	
	if ($itemcount > 0) 
		{
		// Calculate some stuff
		$dvdfind = stripos($row2['location'], 'DVD');
		$ordernum = preg_replace("/[^0-9]/", '', $row2['itemcallnumber']);
		$on_order = stripos($row2['itemcallnumber'], 'on order');
		if ($on_order !== false && $ordernum == "") $ordernum = 1;
		if ($on_order !== false) $ordquant = max( $ordernum, $ordquant ); 
		// figure out the number to order
		$rescalc = floor($rescount / ($itemcount + $ordquant) / $reserve_alert_ratio)  ;
		$dvdcalc = floor($rescount / ($itemcount + $ordquant) / $reserve_alert_ratio_dvd)  ;
		if (($dvdfind === false && $rescalc >= 1) || ($dvdfind >= 0 && $dvdcalc >= 1))
			{
			echo "<tr>";
				echo '<td><b> <a href="'.$addrkoha433. $bib_detail . 
					$row['biblionumber'] .'">' .$row['title'];
				$pretitle2 = getmarctag($row['marc'], "245", "b");
				$title2 =  substr($pretitle2,0,strlen($pretitle2)-1);
				echo '</a></b></td>';	
				
				echo "<td> ". $row['author'] ." </td>";
				echo "<td> ". $row2['itemcallnumber'] ." </td>";
				echo "<td> ". $row2['itype'] ." </td>";
				echo "<td> ". $row2['location'] ." </td>";
				echo "<td> ". $rescount ." </td>";
				echo "<td> ". $itemcount ." </td>";
				echo "<td> ". $ordquant ." </td>";
				if ($dvdfind === false) 
					echo "<td><b>". $rescalc ."</b></td>";
				else 
					echo "<td><b>". $dvdcalc ."</b></td>";
				// Look for a call number of "ordered"
				if ($orderedbar===false && $orderedcall===false)
					echo "<td><p3><b><i>Probable, check holds".$row3['itemcallnumber']."</i></b></p3></td>";
				else
					echo "<td>Perhaps: check number<br>of items on order</td>";
			echo "</tr>";
			}
		}
	}
echo "</table>";

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
						if ($code == $subtag) echo " <br /> ($findtag $subtag) ".substr($value,5,strlen($value)-5) ;
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

// close the database
disconnect($conn);
?>
