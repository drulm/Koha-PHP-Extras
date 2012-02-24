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

$enddate		= $_POST['enddate']." 00:00:00";
$startdate		= $_POST['startdate']." 23:59:59";
$library		= $_POST['library'];

$excel = 0;
$debug = 0;

?>
  <h2 align="center">Cataloging Statistics (in development!)</h2>
  <p align="left"> 
<?php
$curdate = time();
echo "<center><h4>Today's Date: ".date("M d, Y",$curdate)."</h4><hr>";
echo "<center><h4>Date Range ".$startdate." ... ".$enddate."</h4><hr>";

//require_once('connect_koha.php');

$desc = array();
$stat = array();

// Open the database
$conn = open_database($koha_db, $koha_login, $koha_password, $koha_ip, $koha_name);

$query = "select count(*) from action_logs where action='update_items.pl' and info='success' ";
$row = get_koha_stat($conn, $query, $startdate, $enddate, "timestamp");
array_push($desc, "Items Created or Modified from Action Log Table");
array_push($stat, $row['count(*)']);

$query = "select count(*) from aqorders where ";
$row = get_koha_stat($conn, $query, $startdate, $enddate, "datereceived");
array_push($desc, "Acqui. Orders Received");
array_push($stat, $row['count(*)']);

$query = "select sum(quantityreceived) from aqorders where ";
$row = get_koha_stat($conn, $query, $startdate, $enddate, "datereceived");
array_push($desc, "Acqui. Orders Received (includes multiple copies)");
array_push($stat, $row['sum(quantityreceived)']);

$query = "select count(*) from aqorders where ";
$row = get_koha_stat($conn, $query, $startdate, $enddate, "timestamp");
array_push($desc, "All Acqui. Orders Made");
array_push($stat, $row['count(*)']);

$query = "select count(*) from auth_header where ";
$row = get_koha_stat($conn, $query, $startdate, $enddate, "datecreated");
array_push($desc, "Authority Headers Created");
array_push($stat, $row['count(*)']);

$query = "select count(*) from biblio where ";
$row = get_koha_stat($conn, $query, $startdate, $enddate, "timestamp");
array_push($desc, "Biblio Table Modified Count");
array_push($stat, $row['count(*)']);

$query = "select count(*) from biblioitems where ";
$row = get_koha_stat($conn, $query, $startdate, $enddate, "timestamp");
array_push($desc, "BiblioItems Table Modified Count");
array_push($stat, $row['count(*)']);

$query = "select count(*) from biblioitems where ";
$row = get_koha_stat($conn, $query, $startdate, $enddate, "timestamp");
array_push($desc, "Deleted Biblios");
array_push($stat, $row['count(*)']);

$query = "select count(*) from deletedbiblioitems where ";
$row = get_koha_stat($conn, $query, $startdate, $enddate, "timestamp");
array_push($desc, "Deleted BiblioItems");
array_push($stat, $row['count(*)']);

$query = "select count(*) from items where ";
$row = get_koha_stat($conn, $query, $startdate, $enddate, "dateaccessioned");
array_push($desc, "Items Added");
array_push($stat, $row['count(*)']);

//-------------------------------------------------------
//---------------------------Print all Stats in Table------------------------
//---------------------------------------------------------------------------
// Print all these stats!
echo "<table>";
echo "<th>Description</th><th>Statistic</th>";
for ($j=0 ; $j<count($desc) ; $j++)
	echo "<tr><td align=right>".$desc[$j]."<td>".$stat[$j]."<tr>";
echo "</table><hr><br>";



//------------------------------------------------------------------------------
$query = "select authtypecode, count(*) from auth_header where authtypecode IS NOT NULL ";
if ($startdate != "") $query .= " and datecreated >= ".'"'.$startdate.'"'."  ";
if ($enddate != "") $query .= " and datecreated <= ".'"'.$enddate.'"'."   ";
$query .= " GROUP by authtypecode ";
// Perform Query
$result = dbquery($conn, $query);

echo "<br><b>Authority Headers Created by Type</b><br>";
echo "<table border=1><tr><th>Auth Type</th><th>Count</th>";
while ($row = read_db_assoc($result))
	{
	$row = stripslashes_deep($row);
	echo "<tr>";
	echo "<td> ". $row['authtypecode'] ." </td>";
	echo "<td> ". $row['count(*)'] ." </td>";
	echo "</tr>";
	}
echo "</table><hr>";

//------------------------------------------------------------------------------
$query = "select itemtype, count(*) from biblioitems where ";
if ($startdate != "") $query .= " timestamp >= ".'"'.$startdate.'"'."  ";
if ($enddate != "") $query .= " and timestamp <= ".'"'.$enddate.'"'."   ";
$query .= " GROUP by itemtype ";
// Perform Query
$result = dbquery($conn, $query);

echo "<br><b>BiblioItems Table Modified by Type</b><br>";
echo "<table border=1><tr><th>Item Type</th><th>Count</th>";
while ($row = read_db_assoc($result))
	{
	$row = stripslashes_deep($row);
	echo "<tr>";
	echo "<td> ". $row['itemtype'] ." </td>";
	echo "<td> ". $row['count(*)'] ." </td>";
	echo "</tr>";
	}
echo "</table> <br>";

//------------------------------------------------------------------------------
$query = "select location, count(*) from items where";
if ($startdate != "") $query .= " dateaccessioned >= ".'"'.$startdate.'"'."  ";
if ($enddate != "") $query .= " and dateaccessioned <= ".'"'.$enddate.'"'."   ";
$query .= " GROUP by location ";
// Perform Query
$result = dbquery($conn, $query);

echo "<br><b>Items Added by Collection Code</b><br>";
echo "<table border=1><tr><th>Item Type</th><th>Count</th>";
while ($row = read_db_assoc($result))
	{
	$row = stripslashes_deep($row);
	echo "<tr> <td> ". $row['location'] ." </td>";
	echo "<td> ". $row['count(*)'] ." </td>";
	echo "</tr>";
	}
echo "</table> <br>";


//------------------------------------------------------------------------------
$query = "select itype, count(*) from items where ";
if ($startdate != "") $query .= " dateaccessioned >= ".'"'.$startdate.'"'."  ";
if ($enddate != "") $query .= " and dateaccessioned <= ".'"'.$enddate.'"'."   ";
$query .= " GROUP by itype ";
// Perform Query
$result = dbquery($conn, $query);

echo "<br><b>Items Added by Type</b><br><table border=1>";
echo "<tr> <th>Item Type</th> <th>Count</th> ";
while ($row = read_db_assoc($result))
	{
	$row = stripslashes_deep($row);
	echo "<tr><td> ". $row['itype'] ." </td>";
	echo "<td> ". $row['count(*)'] ." </td>";
	echo "</tr>";
	}
echo "</table> <br>";


//------------------------------------------------------------------------------
/*$query = "select userid, ip, count(*) from sessions where userid = 'cat' or userid = 'smfplcirc' 
	GROUP by ip order by userid ";
// Perform Query
$result = dbquery($conn, $query);

echo "<br><b>Total Sessions Started by IP</b><br>";
echo "<table border=1>";
echo "<tr> <th>userid</th> <th>Session IP</th> <th>Count</th> ";
while ($row = read_db_assoc($result))
	{
	$row = stripslashes_deep($row);
	echo "<tr>";
	echo "<td> ". $row['userid'] ." </td>";
	echo "<td> ". $row['ip'] ." </td>";
	echo "<td> ". $row['count(*)'] ." </td>";
	echo "</tr>";
	}
echo "</table> <br>";

//------------------------------------------------------------------------------
$query = "select userid, ip, count(*) from sessions where userid = 'cat' or userid = 'smfplcirc'
 	GROUP by userid order by ip ";
// Perform Query
$result = dbquery($conn, $query);

echo "<br><b>Total Sessions Started by ID</b><br>";
echo "<table border=1><tr> <th>userid</th> <th>Session IP</th> <th>Count</th> ";
while ($row = read_db_assoc($result))
	{
	$row = stripslashes_deep($row);
	echo "<tr>";
	echo "<td> ". $row['userid'] ." </td>";
	echo "<td> ". $row['ip'] ." </td>";
	echo "<td> ". $row['count(*)'] ." </td>";
	echo "</tr>";
	}
echo "</table> <br>";
*/
//------------------------------------------------------------------------------
$query = "select location, count(*) from items GROUP by location order by location ";
// Perform Query
$result = dbquery($conn, $query);

echo "<hr><hr><br><b>Collection Count by Collection Code - For ANY Date</b><br>";
echo "<table border=1><tr> <th>Location (collection) Code</th> <th>Count</th> </tr> ";
while ($row = read_db_assoc($result))
	{
	$row = stripslashes_deep($row);
	echo "<tr>";
	echo "<td> ". $row['location'] ." </td>";
	echo "<td> ". $row['count(*)'] ." </td>";
	echo "</tr>";
	}
echo "</table> <br>";

//------------------------------------------------------------------------------
$query = "select itype, count(*) from items GROUP by itype order by itype ";
// Perform Query
$result = dbquery($conn, $query);

echo "<hr><hr><br><b>Collection Count by Item Type - For ANY Date</b><br>";
echo "<table border=1>";
echo "<tr> <th>Item Type</th> <th>Count</th> </tr> ";
while ($row = read_db_assoc($result))
	{
	$row = stripslashes_deep($row);
	echo "<tr>";
	echo "<td> ". $row['itype'] ." </td>";
	echo "<td> ".$row['count(*)'] ." </td>";
	echo "</tr>";
	}
echo "</table> <br>";


//------------------------------------------------------------------------------
$query = 'select author, barcode, itype, location, biblio.biblionumber, title from 
items, biblio where items.biblionumber = biblio.biblionumber and (itype is NULL or location is NULL ) ';
// Perform Query
$result = dbquery($conn, $query);

echo "<hr><hr><br><b>Items with NULL ITYPES or COLLECTION CODES</b><br>";
echo "<hr><hr><br><b>Please Fix these BIBs!</b><br>";
echo "<table border=1>";
echo "<tr> <th>BIBLIO NUMBER - Link to BIB</th>  <th>Author</th> <th>BARCODE</th> <th>Itype</th> <th>Col. Code</th> </tr> ";
while ($row = read_db_assoc($result))
	{
	$row = stripslashes_deep($row);
	echo "<tr>";
	echo '<td> <a href="'.$addrkoha433.'cgi-bin/koha/detail.pl?bib='. $row['biblionumber'] .'">'.$row['title'].'</a> </td>';	
	echo "<td> ". $row['author'] ." </td>";
	echo "<td> ". $row['barcode'] ." </td>";
	echo "<td> ". $row['itype'] ." </td>";
	echo "<td> ". $row['location'] ." </td>";
	echo "</tr>";
	}
echo "</table> <br>";

/*
//------------------------------------------------------------------------------
$query = "select userid, ip, count(*) from sessions group by ip order by ip,userid ";
// Perform Query
$result = dbquery($conn, $query);

echo "<br><b>All Sessions Started by IP</b><br>";
echo "<table border=1>";
echo "<tr> <td><b>userid</b></td> <td><b>Session IP</b></td> <td><b>Count</b></td> ";
while ($row = read_db_assoc($result))
	{
	$row = stripslashes_deep($row);
	echo "<tr>";
	echo "<td> ". $row['userid'] ." </td>";
	echo "<td> ". $row['ip'] ." </td>";
	echo "<td> ". $row['count(*)'] ." </td>";
	echo "</tr>";
	}
echo "</table> <br>";
*/

// Close the Real Koha database
disconnect($conn);
?>
