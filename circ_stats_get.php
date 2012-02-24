<?php
## Circ Stats

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
require_once('addrkoha.php');
// Print the main top menu
require_once("topmenu.php");

$startdate		= $_POST['startdate']." 00:00:00";
$enddate		= $_POST['enddate']." 23:59:59";
$library		= $_POST['library'];

// Open the database
$conn = open_database($koha_db, $koha_login, $koha_password, $koha_ip, $koha_name);

$desc = array();
$stat = array();
echo "<br><center><h2>Authorized Statistical Circ Values<br />from " 
	. $_POST['startdate'] ." to ". $_POST['enddate'] ." for Branch ".$library."</h2></center>";
//------------------------Get the circ count in the time frame-----------------------------------
$query = "select count(*) from statistics where type = 'issue'";
$row = get_koha_stat($conn, $query, $startdate, $enddate, "datetime");
array_push($desc, "Circulation count<br>without renewals");
array_push($stat, $circnum = $row['count(*)']);

//-----------------------Get the renewal count-------------------
$query = "select count(*) from statistics where type = 'renew'";
$row = get_koha_stat($conn, $query, $startdate, $enddate, "datetime");
array_push($desc, "Staff and patron renewals");
array_push($stat, $renewnum  = $row['count(*)']);
array_push($desc, "<b>Circulation + Renewals <br>(use this number)</b>");
array_push($stat, $totcirc = $circnum + $renewnum);

//---------------------Count the returns in the date period---------------------------------------
$query = "select count(*) from statistics where type = 'return'";
$row = get_koha_stat($conn, $query, $startdate, $enddate, "datetime");
array_push($desc, "Returned items");
array_push($stat, $returns = $row['count(*)']);

//---------------------Find out amount outstanding from all patron accounts with activity during date-----
$query = "select sum(amountoutstanding) as sum_amount from accountlines
			where amountoutstanding < 0 and amountoutstanding > -800.00";
$row = get_koha_stat($conn, $query, $startdate, $enddate, "timestamp");
array_push($desc, "Payments made or fees waived <br>(all together) ");
array_push($stat, $amount_amount =  - $row['sum_amount']);

//---------------------Credits made during time perid--------------------------------------------------------
$query = "select sum(amountoutstanding) from accountlines 
			where amountoutstanding < 0 and amountoutstanding > -800.00 and accounttype='C'";
$row = get_koha_stat($conn, $query, $startdate, $enddate, "timestamp");
array_push($desc, "Payments made");
array_push($stat, $credits =  - $row['sum(amountoutstanding)']);

//-----------------------------Forgiven Payments-----------------------------
$query = "select sum(amountoutstanding) from accountlines 
			where amountoutstanding < 0 and amountoutstanding > -800.00 and accounttype='FOR' ";
$row = get_koha_stat($conn, $query, $startdate, $enddate, "timestamp");
array_push($desc, "Payments forgiven ");
array_push($stat,  $forgiven = - $row['sum(amountoutstanding)']);

//---------------------------Count of Reserves-------------------------------
$query = "select count(*) from reserves where ";
$row = get_koha_stat($conn, $query, $startdate, $enddate, "reservedate");
array_push($desc, "Reserves made");
array_push($stat,  $reserves = $row['count(*)']);

//---------------------------Reserves Picked Up--------------------------------
$query = "select count(*) from reserves where found = 'F' ";
$row = get_koha_stat($conn, $query, $startdate, $enddate, "timestamp");
array_push($desc, "Reserves picked up");
array_push($stat,  $reserves_picked_up = $row['count(*)']);

//---------------------------Reserves Cancelled-------------------------------
$query = "select count(*) from reserves where cancellationdate IS NOT NULL ";
$row = get_koha_stat($conn, $query, $startdate, $enddate, "timestamp");
array_push($desc, "Reserves cancelled");
array_push($stat,  $reserves_cancelled = $row['count(*)']);

//---------------------------Borrowers added----------------------------------
$query = "select count(*) from borrowers where ";
$row = get_koha_stat($conn, $query, $startdate, $enddate, "dateenrolled");
array_push($desc, "Borrowers added");
array_push($stat,  $borrowers_added = $row['count(*)']);

//---------------------------In House Use----------------------------------
if ($local_db) // Don't add this one if we aren't using the local DB
	{
	// Close the Koha database for a sec
	disconnect($conn);
	// Open the "local" database
	$conn = open_database($koha_db, $local_login, $local_password, $local_ip, $local_name);
	$query = "select sum(houseuse) from inhouseuse where houseuse > 0";
	$row = get_koha_stat($conn, $query, $startdate, $enddate, "timestamp");
	array_push($desc, "In House Use ");
	array_push($stat, $in_house =  - $row['sum(houseuse)']);
	disconnect($conn);
	}
//---------------------------Print all Stats in Table------------------------
//---------------------------------------------------------------------------
// Print all these stats!
echo "<table>";
echo "<th>Description</th><th>Statistic</th>";
for ($j=0 ; $j<count($desc) ; $j++)
	echo "<tr><td align=right>".$desc[$j]."<td>".$stat[$j]."<tr>";
echo "</table><hr><br>";

//---------------------------Lists Section-----------------------------------

//---------------------------Borrowers added by category----------------------------------
//----------------------------------------------------------------------------------------
// Re - Open KOHA the database
$conn = open_database($koha_db, $koha_login, $koha_password, $koha_ip, $koha_name);

$query = "select categorycode, count(*) from borrowers  where borrowernumber IS NOT NULL ";
if ($startdate != "") $query .= " and dateenrolled >= ".'"'.$startdate.'"'."  ";
if ($enddate != "") $query .= " and dateenrolled <= ".'"'.$enddate.'"'."  ";
$query .= " group by categorycode";

// Perform Query
$result = dbquery($conn, $query);

$bow = $borrowers_added;
if ($borrowers_added == 0) $bow = 0;
echo "<h3><center>Borrowers Added by Category</center></h3>";
echo "<table border=1>";
echo "<tr><th>Category</th><th>Number Added</th><th>Percentage 0..100</th></tr>";
while ($row = read_db_assoc($result))
	{
	$row = stripslashes_deep($row);
	echo "<tr>";
	echo "<td> ". $row['categorycode'] ." </td>";
	echo "<td> ". $row['count(*)'] ." </td>";
	echo "<td> ";
	echo printf ("%7.2f", 100 * $row['count(*)'] / $borrowers_added );
	echo " </td>";
	echo "</tr>";
	}
echo "</table><hr><br>";

//-------------------------Issues by Item-Type-------------------------------------------
$query = "select itemtype, count(*) from statistics where (type = 'issue' or type = 'renew') ";
if ($startdate != "") $query .= " and datetime >= ".'"'.$startdate.'"'."  ";
if ($enddate != "") $query .= " and `datetime` <= ".'"'.$enddate.'"'."  ";
$query .= "group by itemtype ";

// Perform Query
$result = dbquery($conn, $query);

echo "<h3><center>Issues by Itemtype from Statistics Table</center></h3>";
echo "<table border=1>";
echo "<tr> <th>Itemtype</th> <th>Count</th> <th>Percentage 0..100</th>";
$issuecount = 0;
while ($row = read_db_assoc($result))
	{
	$row = stripslashes_deep($row);
	echo "<tr>";
	echo "<td> ". $row['itemtype'] ." </td>";
	echo "<td> ". $row['count(*)'] ." </td>";
	echo "<td> ";
	echo printf ("%7.2f", 100 * $row['count(*)'] / $totcirc );
	echo " </td>";
	echo "</tr>";
	$issuecount += $row['count(*)'];
	}
echo "</table>";
echo "<b>Issue count total with renewalls included : ".$issuecount."</b><hr><br>";


//---------------------------Issues by Collect Code (Location)--------------------------
$query = "select location, count(*) 
	 		from statistics, items
			where statistics.itemnumber = items.itemnumber and
			(type = 'issue' or type = 'renew') ";
if ($startdate != "") $query .= " and datetime >= ".'"'.$startdate.'"'."  ";
if ($enddate != "") $query .= " and datetime <= ".'"'.$enddate.'"'."  ";
$query .= "group by location ";

// Perform Query
$result = dbquery($conn, $query);

echo "<h3><center>Issues by Collection from Statistics Table</center></h3>";
echo "<table border=1>";
echo "<tr> <th>Collection Code</th> <th>Count</th> <th>Percentage 0..100</th>";
$issuecount = 0;
while ($row = read_db_assoc($result))
	{
	$row = stripslashes_deep($row);
	echo "<tr>";
	echo "<td> ". $row['location'] ." </td>";
	echo "<td> ". $row['count(*)'] ." </td>";
	$issuecount += $row['count(*)'];
	echo "<td> ";
	echo printf ("%7.2f", 100 * $row['count(*)'] / $totcirc );
	echo " </td>";
	echo "</tr>";
	}
echo "<tr>";
echo "<td> Renewed items, do not have a category </td>";
echo "<td> ". ((int)$totcirc - (int)$issuecount) ." </td>";
echo "<td> ";
echo printf ("%7.2f", 100 * ($totcirc - $issuecount) / $totcirc );
echo " </td>";
echo "</tr>";

echo "</table>";
echo "<hr>";

echo "<b>Issue count total (with renewals)= ".$totcirc."</b><hr><br>";

// Close the Real Koha database
disconnect($conn);
?>
