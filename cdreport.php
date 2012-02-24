<?php
## CD Report By Patron ID 

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
?>


	
<?php
// Set this to your Koha staff intranet base address
require_once("kohafunctions.php");
// Set this to your Koha staff intranet base address
require_once('addrkoha.php');
// Print the main top menu
require_once("topmenu.php");

// Get some marc fields
require_once("File/MARC.php");

?>



<center><form action="cdreport.php" method="POST">
	<h1>CD Report with Call Numbers </h1>
	<h2> Use CNTRL-F to search for anything </h2>
	<h3 align="center"> <input type="submit" value="Submit Information" /></h3>
    <div align="center">
      <table border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="191"> <div align="right"><strong>Artist (100a field only):</strong></div></td><td width="282"><div align=left><input name="artist" type="text" id="artist" size="20" /></div></td>
        </tr>
      </table>
    </div>
    <p align="center"><strong>Load All Groups (using 110a field or Various Artists)</strong>
	  <input name="groups" type="checkbox" id="groups" value="1">
      <em><br>
      Loads everything, slower, wait patiently and leave it up for the day. Does <strong>not</strong> work with 110a Artists search box </em></p>
</form>
</center>

<?php

// Input Value
/*echo '<center><form action="cdreport.php" method="POST">
	<h1>CD Report with Call Numbers </h1>
	<h3> <input type="submit" value="Submit Information" /></h3>
	<table border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td><div align=right> Artist </div></td>
        <td><div align=left><input name="artist" type="text" id="artist" size="20" /></div></td>
      </tr>
    </table>
	</form>
	</center>';
*/
// create short variable names for form parameters
// Get parameters
$artist		= stripslashes($_POST['artist']);
$groups		= stripslashes($_POST['groups']);

/*if ($artist == NULL)
	{
	die('<center><b>You must enter some search criteria</b></center>');
	}
*/

// Open the database
$conn = open_database($koha_db, $koha_login, $koha_password, $koha_ip, $koha_name);

//build query
$query = "select  biblio.biblionumber, itemcallnumber, author, copynumber, title, marc
	from biblio
	inner join biblioitems on biblio.biblionumber = biblioitems.biblionumber
	inner join items on biblio.biblionumber = items.biblionumber
	where
	itemtype = 'CD' and
	(location = 'CD' or location = 'NEWCD') and ";
	if ($groups == 1) 
		$query .= " ( author IS NULL or ";
	$query .= " author LIKE '%".$artist."%'";
	if ($groups == 1) 
		$query .= " ) ";
	$query .= "order by itemcallnumber, author, title ";
/*$query = "select biblio.biblionumber, itemcallnumber, author, multivolumepart, title, marc
	from biblio, biblioitems, items
	where
	itemtype = 'CD' and
	location = 'CD' and
	biblio.biblionumber = biblioitems.biblionumber and
	biblioitems.biblionumber = items.biblionumber and
	biblio.biblionumber = items.biblionumber and ";
	if ($groups == 1) 
		$query .= " ( author IS NULL or ";
	$query .= " author LIKE '%".$artist."%'";
	if ($groups == 1) 
		$query .= " ) ";
	$query .= "order by itemcallnumber, author, title ";
*/

// Perform Query
$result = dbquery($conn, $query);
if (numrows($result) == 0) die("no data found");

echo "<table border=1>";
echo "<tr>
	<th>Call Number</th>
	<th>Artist</th>
	<th>Title (click for detail)</th> 
	<th>Category <click to edit items)</th>";

while ($row = read_db_assoc($result))
	{
	$row = stripslashes_deep($row);
	echo "<tr>";
//	echo '<td> <a href="'.$addrkoha433. $more_member . stripslashes($row['borrowernumber']) .'">'."Click to go<br>to Patron Record".'</a> </td>';	
//	echo "<td> ". $row['itemcallnumber'] ." </td>";
	echo '<td> <a href="'.$addrkoha433 . $edit_item . $row['biblionumber'] .'">'.$row['itemcallnumber'].'</a> </td>';	

	echo "<td> ";
	if ($row['author'] == NULL)
		{
		//echo "<center><i> --group-- ". substr($row[marc],0,strlen($row[marc])) ."</i></center>";
		$preband = getmarctag($row['marc'], "110");
		$band =  substr($preband,0,strlen($preband)-1);
		if ( ! $preband )
			echo "<b><i>Various Artists</i></b>";
		else 
			echo $band ; 
		}
	else
		echo $row['author'] ;
	echo " </td>";
	
	echo '<td> <a href="'.$addrkoha433 . $bib_detail . $row['biblionumber'] .'">'.$row['title'].'</a> </td>';	

	if ($row['copynumber'] != NULL && substr($row['copynumber'],0,1) != "1_" )
		echo "<td> ". $row['copynumber'] ." </td>";
	else 
		echo '<td> <a href="'.$addrkoha433 . $edit_item . $row['biblionumber'] .'">ON ORDER<br> or item error,<br>Edit e-Volume/Copy</a> </td>';	
	echo "</tr>";
	}
echo "</table>";


function getmarctag($marc, $findtag) 
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
						echo substr($value,5,strlen($value)-5) . "  <i>($findtag)/ </i> ";
					$found = 1;
					break;
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
disconnect($conn);
?>
