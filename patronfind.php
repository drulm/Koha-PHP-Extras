<?php
## Patron Lookup By Patron ID 

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

// Input Value
echo '<center><form action="patronfind.php" method="POST" name="myform">
	<h1>Patron Receipt Code Locate </h1>
	<h3> <input type="submit" value="Submit Information" /></h3>
	<table border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td><div align=right> Patron Code on a receipt: </div></td>
        <td><div align=left><input name="patid" type="text" id="patid" size="20" /></div></td>
      </tr>
    </table>
	</form>
	</center>';

	refocus("myform", "patid");

// create short variable names for form parameters
// Get parameters
$patid		= stripslashes($_POST['patid']);
if ($patid == NULL)
	{
	die('<center><b>You must enter some search criteria</b></center>');
	}

echo '<h2><i> List of Patrons Matching </i></h2>';

// Open the database
$conn = open_database($koha_db, $koha_login, $koha_password, $koha_ip, $koha_name);

//build query
$query = "select * from borrowers where borrowernumber = '".$patid."'";

// Perform Query
$result = dbquery($conn, $query);
if (numrows($result) == 0) die("no data found");

echo "<table border=1>";
echo "<tr>
	<th>Patron Record</th>
	<th>Lastname</th>
	<th>Firstname</th> 
	<th>Patron ID</th>
	<th>Card Number</th>
	<th>Street Address</th>
	<th>City</th> 
	<th>Phone</th> 
	<th>Birthday</th> 
	<th>M-F</th> 
	<th>Zipcode</th>";

while ($row = read_db_assoc($result))
	{
	$row = stripslashes_deep($row);
	echo "<tr>";
	echo '<td> <a href="'.$addrkoha433. $more_member . stripslashes($row['borrowernumber']) .'">'."Click to go<br>to Patron Record".'</a> </td>';	
	echo "<td> ". $row['surname'] ." </td>";
	echo "<td> ". $row['firstname'] ." </td>";
	echo "<td> ". $row['borrowernumber'] ." </td>";
	echo "<td> ". $row['cardnumber'] ." </td>";
	echo "<td> ". $row['streetaddress'] ." </td>";
	echo "<td> ". $row['city'] ." </td>";
	echo "<td> ". $row['phone'] ." </td>";
	echo "<td> ". $row['dateofbirth'] ." </td>";
	echo "<td> ". $row['sex'] ." </td>";
	echo "<td> ". $row['zipcode'] ." </td>";
	echo "</tr>";
	}
echo "</table>";
// close database
disconnect($conn);
?>
