<?php
## Acqui Fix, Possible Acqui Items to Repair

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

echo "<h3><center>List of acqui. items may require repair</center></h3>";
echo '<h2><i> List of Items Matching </i></h2>';
// connect to the web site 
// Open the database
$conn = open_database($koha_db, $koha_login, $koha_password, $koha_ip, $koha_name);

$query = "select aqorders.basketno, aqbooksellers.id, aqorders.biblionumber, aqorders.ordernumber,itemtype,name,title 
		from aqorders,aqbasket,biblioitems,aqbooksellers 
		where
		aqorders.biblioitemnumber=biblioitems.biblioitemnumber and
		aqorders.basketno=aqbasket.basketno and
		aqbasket.booksellerid=aqbooksellers.id and aqorders.ordernumber 
			not in
		(select ordernumber from aqorderbreakdown)
			order by itemtype,name,title ";

// Perform Query
$result = dbquery($conn, $query);
if (numrows($result) == 0) die("no data found");

echo "<table border=1>";
echo "<tr> 
	<th>itemtype</th>
	<th>name</th>
	<th>title</th>
	<th>basketno</th>
	<th>id</th>
	<th>biblionumber</th>
	<th>ordernumber</th>";

while ($row = read_db_assoc($result))
	{
	$row = stripslashes_deep($row);
	echo "<tr>";
	echo "<td> ". $row['itemtype'] ." </td>";
	echo "<td> ". $row['name'] ." </td>";
	echo '<td> <a href="'.$addrkoha433. $aqui_edit1 . $row['ordernumber']
		. $aqui_edit2 . $row['id']
		. $aqui_edit3 . $row['basketno'] .'"> '
			.$row['title'].'</a> </td>';	
	echo "<td> ". $row['basketno'] ." </td>";
	echo "<td> ". $row['id'] ." </td>";
	echo "<td> ". $row['biblionumber'] ." </td>";
	echo "<td> ". $row['ordernumber'] ." </td>";
	echo "</tr>";
	}
echo "</table>";

// close database
disconnect($conn);
?>
