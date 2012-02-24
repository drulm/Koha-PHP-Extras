<?php
## Acqui. Anomolies - Staff may have typed incorrect monetary amounts, this report may find those 

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

echo '<center><h2>Report by Vendor Totals</h2></center>';

// Open the database
$conn = open_database($koha_db, $koha_login, $koha_password, $koha_ip, $koha_name);

$query = "select id,aqorders.basketno,aqorders.biblionumber,ordernumber,
		title,quantity,aqorders.listprice,datereceived,freight,unitprice, rrp, ecost
	from
		aqorders,aqbasket,biblioitems,aqbooksellers 
	where
		aqorders.biblioitemnumber=biblioitems.biblioitemnumber and
		aqorders.basketno=aqbasket.basketno and
		aqbasket.booksellerid=aqbooksellers.id and aqorders.ordernumber 
	and
		(
		abs(unitprice - aqorders.listprice) > 20
	and
		quantityreceived IS NOT NULL
	or
		abs(rrp - aqorders.listprice) > 20  
	and 
		quantityreceived IS NOT NULL
	or
		abs(ecost - aqorders.listprice) > 20  
	and 
		quantityreceived IS NOT NULL
	or 
		rrp = 0
	and 
		quantityreceived IS NOT NULL
	or 
		(rrp = 0 or ecost = 0 or unitprice = 0 or aqorders.listprice = 0)
	and 
		quantityreceived IS NOT NULL
		)
	order by abs(unitprice - aqorders.listprice) desc
	limit 200";

// Perform Query
$result = dbquery($conn, $query);
if (numrows($result) == 0) die("no data found");

echo "<table border=1>";
echo "<tr> 
	<th>Title</b></td> 
	<th>Quantity</th> 
	<th>Listprice</th>
	<th>Unitprice</th>
	<th>Datereceived</th>
	<th>Freight</th>
	<th>RRP</th>
	<th>Est. Cost</th>
	<tr>";

while ($row = read_db_assoc($result))
	{
	$row = stripslashes_deep($row);
	echo "<tr align=right>";
	echo '<td> <a href="'.$addrkoha433. $aqui_edit1 . $row['ordernumber']
		. $aqui_edit2 . $row['id']
		. $aqui_edit3 . $row['basketno'] .'"> '
			.$row['title'].'</a> </td>';	
	echo "<td> ". $row['quantity'] ." </td>";
	echo "<td><b> ". $row['listprice'] ." </b></td>";
	echo "<td><b><i> ". $row['unitprice'] ." </i></b></td>";
	echo "<td> ". $row['datereceived'] ." </td>";
	echo "<td> ". $row['freight'] ." </td>";
	echo "<td> ". $row['rrp'] ." </td>";
	echo "<td> ". $row['ecost'] ." </td>";
	echo "</tr>";
	}
echo "</table>";

// close database
disconnect($conn);
?>
