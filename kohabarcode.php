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

?>
<center><form action="kohabarcode.php" method="POST" name="myform">
  <h3>Partial Barcode Search</h3>
<table width="562" height="110" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td width="180"><h3 align="right"><strong>Enter barcode:</strong></h3></td>
        <td width="191"><div align="center">
          <input name="barcode" type="text" id="barcode" />
        </div></td>
        <td width="191"><input type="submit" value="Submit Information" /></td>
      </tr>
  </table>
    </form>
<?php
refocus("myform", "barcode");

// Get parameters
$barcode		= stripslashes($_POST['barcode']);
if ($barcode == NULL) die('<br><b>Please enter a barcode</b><br>');

echo '<h2><i> List of Items Matching Barcode </i></h2>';

$conn = open_database($koha_db, $koha_login, $koha_password, $koha_ip, $koha_name);

$query = "select * from biblio, items 
		where items.biblionumber = biblio.biblionumber and items.barcode LIKE '%".$barcode."%' 
		order by items.location , items.itemcallnumber limit 0,100";

$result = dbquery($conn, $query);
if (($num_rows = numrows($result)) == 0) die("No Rows Found");

echo "Rows Returned = <i>$num_rows </i><br><br>";
echo "<table border=1>";
echo "<tr> <th>Location</th> 
	<th>Item Call Number</th> 
	<th>Datelastseen</th> 
	<th>Copy Number</th> 
	<th>Title</th> 
	<th>Author</th> 
	<th>Barcode</th> 
	<th>I-type</th>";
while ($row = read_db_assoc($result))
	{
	$row = stripslashes_deep($row);
	echo "<tr>";
	echo "<td> ". $row['location'] ." </td>";
	echo "<td> ". $row['itemcallnumber'] ." </td>";
	$formatdate = date("F j, Y",strtotime($row['datelastseen']));
	echo "<td> ". $formatdate ." </td>";
	if (strpos($row['copynumber'], "_") == false)
		echo "<td> ". $row['copynumber'] ." </td>";
	else
		echo "<td></td>";
	echo '<td> <b> <a href="'.$addrkoha433. $search_koha . $row['barcode'] .'">'.$row['title'].'</a> </b> </td>';	
	echo "<td> ". $row['author'] ." </td>";
	echo '<td> <a href="'.$addrkoha433 . $more_detail . $row['biblionumber'] . '">'.$row['barcode'].'</a> </td>';
	echo "<td> ". $row['itype'] ." </td>";
	echo "</tr>";
	}
echo "</table>";

// close database
disconnect($conn);
?>
