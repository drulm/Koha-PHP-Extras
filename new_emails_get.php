<?php
## Cataloging Stats Get

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

$startdate		= $_POST['startdate'];

$excel = 0;
$debug = 0;

?>
  <h2>New Patron Registration Emails</h2> 
<?php
$curdate = time();
echo "<h4>Today's Date: ".date("M d, Y",$curdate)."</h4><hr>";
echo "<h4>Date Range ".$startdate." ... ".$enddate."</h4><hr>";

//require_once('connect_koha.php');

$desc = array();
$stat = array();

// Open the database
$conn = open_database($koha_db, $koha_login, $koha_password, $koha_ip, $koha_name);

//------------------------------------------------------------------------------
$query = "select * from borrowers where email IS NOT NULL and email <> '' ";
if ($startdate != "") $query .= " and dateenrolled >= ".'"'.$startdate.'"'."  ";
$query .= " order by dateenrolled limit 0,10000";
// Perform Query
$result = dbquery($conn, $query);

echo "<br><b>New Patrons with Email Addresses</b><br>";
echo "<table border=1><tr>
		<th>Last Name</th>
		<th>First Name</th>
		<th>Date Enrolled</th>
		<th>Email</th>
		</tr>
		";
while ($row = read_db_assoc($result))
	{
	$row = stripslashes_deep($row);
	echo "<tr>";
	echo "<td> ". $row['surname'] ." </td>";
	echo "<td> ". $row['firstname'] ." </td>";
	echo "<td> ". $row['dateenrolled'] ." </td>";
	echo "<td> ". $row['email'] ." </td>";
	echo "</tr>";
	}
echo "</table><hr><br />";

$query = "select * from borrowers where email IS NOT NULL and email <> '' ";
if ($startdate != "") $query .= " and dateenrolled >= ".'"'.$startdate.'"'."  ";
$query .= " order by dateenrolled limit 0,10000";
// Perform Query
$result = dbquery($conn, $query);

echo "<br /><b>Copy this into file</b><br /><br />";
echo "Last Name, First Name, Date Enrolled, Email<br />";
while ($row = read_db_assoc($result))
	{
	$row = stripslashes_deep($row);
	echo $row['surname'] .", ";
	echo $row['firstname'] .", ";
	echo $row['dateenrolled'] .", ";
	echo $row['email'] ." ";
	echo "\n <br />";
	}

// Close the Real Koha database
disconnect($conn);
?>
