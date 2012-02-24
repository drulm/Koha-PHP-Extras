<?php
## Look for reserves that have been cancelled by timeout, does not work yet for manually cancelled

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
require_once("kohafunctions.php");

// Set this to your Koha staff intranet base address
require_once("addrkoha.php");

// Print the main top menu
require_once("topmenu.php");

// Check for holidays
$sweek = $cancelled_reserve_week;
$holidays = any_holidays($holidays, $local_holiday, $sweek, $koha_db, $local_login, $local_password, $local_ip, $local_name);

echo "Number of holidays = $holidays <br /><hr />";

$holiday_rrhd = $reserve_regular_hold_days + $holidays;
$holiday_rshd = $reserve_short_hold_days + $holidays;

//echo "Regular Cancel Days: $holiday_rrhd   DVD Cancal Days: $holiday_rshd <br />";

// Open the database
$conn = open_database($koha_db, $koha_login, $koha_password, $koha_ip, $koha_name);

$query = 'select surname, firstname, biblio.title, author, reserves.timestamp, items.itype, items.barcode, 
			items.itemnumber, biblio.biblionumber, biblio.author, items.location, items.itemcallnumber, 
			items.copynumber,
			reserves.branchcode
		from 
			reserves, biblio, borrowers, items
		where 
			reserves.biblionumber = biblio.biblionumber
		and
			reserves.borrowernumber = borrowers.borrowernumber
		and
			reserves.itemnumber = items.itemnumber
		and
			found = "W"
		and
			(
				(items.itype <> "'.$reserve_short_hold_type.'"
					and
				DATE(reserves.waitingdate) <= DATE_SUB(CURDATE(), INTERVAL '.$reserve_regular_hold_days .' DAY)
					and
				DATE(reserves.waitingdate) >= DATE_SUB(CURDATE(), INTERVAL '. $holiday_rrhd .' DAY)
					and
				cancellationdate IS NULL) 
					or 
				(items.itype = "'.$reserve_short_hold_type.'"
					and
				DATE(reserves.waitingdate) <= DATE_SUB(CURDATE(), INTERVAL '.$reserve_short_hold_days  .' DAY)
					and
				DATE(reserves.waitingdate) >= DATE_SUB(CURDATE(), INTERVAL '. $holiday_rshd .' DAY)
					and
				cancellationdate IS NULL) 
			)
		order by branchcode, surname, firstname
		';

//echo "<br> $query <br>";

// Perform Query
$result = dbquery($conn, $query);



if (numrows($result) == 0) echo "No Hold Time Exceeded Cancelled Reserves Found";

echo "<center><h3>Reserves Past Hold Date</h3></center>";
echo "<table border=1>";
echo "<tr>
		<th>Last Name</th>
		<th>First Name</th>
		<th>Title</th>
		<th>Author</th>
		<th>Barcode</th>
		<th>I-type</th>
		<th>Location</th>
		<th>Item Call Number</th>
		<th>Datelastseen</th>
		<th>Copy Number</th>";
if ($multi_branch) 
	echo "<th>Branch</th>";
echo	"</tr>";
	
while ($row = read_db_assoc($result))
	{
	$row = stripslashes_deep($row);
	echo "<tr>";
		echo "<td><b> ". $row['surname'] ."</b> </td>";
		echo "<td><b> ". $row['firstname'] ."</b> </td>";
		echo "<td> ". $row['title'] ."</td>";
		echo "<td> ". $row['author'] ." </td>";
		echo "<td> ". $row['barcode'] ." </td>";
		echo "<td> ". $row['itype'] ." </td>";
		echo "<td> ". $row['location'] ." </td>";
		echo "<td> ". $row['itemcallnumber'] ." </td>";
		$formatdate = date("F j, Y",strtotime($row['timestamp']));
		echo "<td> ". $formatdate ." </td>";
		if (strpos($row['copynumber'], "_") == false)
			echo "<td> ". $row['copynumber'] ." </td>";
		else
			echo "<td></td>";
		if ($multi_branch)
			echo "<td> ". $row['branchcode'] ." </td>";
	echo "</tr>";
	}
echo "</table>";

$rrhd1 = $reserve_regular_hold_days + 1;
$rshd1 = $reserve_short_hold_days + 1;

$query = 'SELECT surname, firstname, biblio.title, author, old_reserves.timestamp, items.itype, items.barcode, 
				items.itemnumber, biblio.biblionumber, biblio.author, items.location, items.itemcallnumber, 
				items.copynumber,
				old_reserves.branchcode
			FROM old_reserves, biblio, borrowers, items
			WHERE 
				(found IS NULL or found = "W")
			AND 
				waitingdate IS NOT NULL 
			AND 
				priority = 0
			and
				old_reserves.biblionumber = biblio.biblionumber
			and
				old_reserves.borrowernumber = borrowers.borrowernumber
			and
				old_reserves.itemnumber = items.itemnumber
			and
				DATE(old_reserves.cancellationdate) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)
			and 
				DATE(items.timestamp) <= DATE_SUB( old_reserves.cancellationdate, INTERVAL 1 DAY)
		order by surname, firstname
		';
// Perform Query
//echo "-->".$query;
$result = dbquery($conn, $query);

echo "<br /><hr>";
if (numrows($result) == 0) echo "No Patron/Staff Cancelled Reserves Found";

echo "<center><h3>Patron/Staff Cancelled Reserves</h3></center>";
echo "<table border=1>";
echo "<tr>
		<th>Last Name</th>
		<th>First Name</th>
		<th>Title</th>
		<th>Author</th>
		<th>Barcode</th>
		<th>I-type</th>
		<th>Location</th>
		<th>Item Call Number</th>
		<th>Datelastseen</th>
		<th>Copy Number</th>";
if ($multi_branch) 
	echo "<th>Branch</th>";
echo "</tr>";
	
while ($row = read_db_assoc($result))
	{
	$row = stripslashes_deep($row);
	echo "<tr>";
		echo "<td><b> ". $row['surname'] ."</b> </td>";
		echo "<td><b> ". $row['firstname'] ."</b> </td>";
		echo "<td> ". $row['title'] ."</td>";
		echo "<td> ". $row['author'] ." </td>";
		echo "<td> ". $row['barcode'] ." </td>";
		echo "<td> ". $row['itype'] ." </td>";
		echo "<td> ". $row['location'] ." </td>";
		echo "<td> ". $row['itemcallnumber'] ." </td>";
		$formatdate = date("F j, Y",strtotime($row['timestamp']));
		echo "<td> ". $formatdate ." </td>";
		if (strpos($row['copynumber'], "_") == false)
			echo "<td> ". $row['copynumber'] ." </td>";
		else
			echo "<td></td>";
		if ($multi_branch)
			echo "<td> ". $row['branchcode'] ." </td>";
	echo "</tr>";
	}
echo "</table>";

// done with this database
disconnect($conn);
?>
