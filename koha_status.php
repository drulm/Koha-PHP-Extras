<?php
## Koha Status Search

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

// Open the database
$conn = open_database($koha_db, $koha_login, $koha_password, $koha_ip, $koha_name);

// Get Authoried Values
$values = array(); $reptext = array();
get_authorised_values($conn, $status_lost, $values, $reptext);
$dvalues = array(); $dreptext = array();
get_authorised_values($conn, $status_damaged, $dvalues, $dreptext);
$nvalues = array(); $nreptext = array();
get_authorised_values($conn, $status_avail, $nvalues, $nreptext);

// Below is code to be entered
?>

<center><form action="koha_status.php" method="POST">
  <h2>Lookup Items by Status Codes</h2>
  <p><em>choose one or more categories</em></p>
  <table width="672" height="274" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td colspan="4">
          <div align="center">
            <input type="submit" value="Submit Information" />
          </div>
          <div align="right">
          </div></td>
      </tr>
      <tr>
        <td width="121"><div align="center"><strong>Availiable (Lost) Status</strong></div></td>
        <td width="164"><select name="reporttype" size="1">
          <option value="" selected="selected">NOT SELECTED</option>
<?php // Get the lost options!
$i = 0;
foreach ($reptext as $rep)
	echo "<option value=".$values[$i++].">".$rep."</option>";
?>                                                                                                      </select></td>
        <td colspan="2"><div align="center"><strong><em>Additional Options</em></strong></div></td>
      </tr>
      <tr>
        <td><div align="center"><strong>Damaged Status</strong></div></td>
        <td><select name="dreporttype" size="1" id="dreporttype">
          <option value="" selected="selected">NOT SELETED</option>
<?php // Get the damaged options!
$i = 0;
foreach ($dreptext as $rep)
	echo "<option value=".$dvalues[$i++].">".$rep."</option>";
?>
           </select></td>
        <td><div align="center">Recent/All/Old Items, uses #of days below. Old status may need changed</div></td>
        <td><select name="nolditems" size="1" id="nolditems">
          <option value="0" selected="selected">Only Recent Items</option>
          <option value="1">All Items Regardless of Date</option>
          <option value="2">Only Old Items (not New)</option>
        </select></td>
      </tr>
      <tr>
        <td><div align="center"><strong>Not For Loan Status</strong></div></td>
        <td><select name="nreporttype" size="1" id="nreporttype">
          <option value="" selected="selected">NOT SELECTED</option>
 <?php // Get the not for loan (avail) options
 $i = 0;
foreach ($nreptext as $rep)
	echo "<option value=".$nvalues[$i++].">".$rep."</option>";
 ?>
        </select></td>
        <td width="216"><p align="center">Days since status changed, one full month is default</p>
        </td>
        <td width="171"><input name="ndayssince" type="text" id="ndayssince" value="31" size="10" maxlength="10" /></td>
      </tr>
      <tr>
        <td height="31"><div align="center"><strong>Sort</strong></div></td>
        <td><select name="nsortfield" size="1" id="nsortfield">
          <option value="items.timestamp">Date Last Status Change</option>
          <option value="datelastseen, items.location , items.itemcallnumber">Date Last Seen</option>
          <option value="items.location , items.itemcallnumber">Location / Call</option>
          <option value="biblio.title">Title</option>
          <option value="author">Author</option>
          <option value="items.timestamp DESC">Date Last Status Change DESC</option>
          <option value="datelastseen DESC, items.location , items.itemcallnumber">Date Last Seen DESC</option>
          <option value="items.location DESC, items.itemcallnumber DESC" selected>Location / Call DESC</option>
          <option value="biblio.title DESC">Title DESC</option>
          <option value="author DESC">Author DESC</option>
         </select></td>
        <td><p align="center"><strong>With Holds (Reserves) </strong></p>
        </td>
        <td><input name="withholds" type="checkbox" id="withholds" value="1"></td>
      </tr>
    </table>
    </form>
</center>

<?php
$_POST = stripslashes_deep($_POST);
$reporttype			= $_POST['reporttype'];
$dreporttype		= $_POST['dreporttype'];
$nreporttype		= $_POST['nreporttype'];
$nsortfield			= $_POST['nsortfield'];
$nolditems			= $_POST['nolditems'];
$ndayssince			= $_POST['ndayssince'];
$withholds			= $_POST['withholds'];

$smarc = "650";

if ($reporttype == NULL && $dreporttype == NULL && $nreporttype == NULL)
	die('<center><hr><b>You must enter at least one or more code types </b><hr></center>');

echo "<center><h4>Report Type:<i>";
if ($reporttype) echo $reptext[array_search($reporttype, $values)]." | ";
if ($dreporttype) echo $dreptext[array_search($dreporttype, $dvalues)]." | ";
if ($nreporttype) echo $nreptext[array_search($nreporttype, $nvalues)]." | ";
if ($withholds) echo "<b>On Reserve(Held)</b>";
echo '</i></h4><center>';

// Build the query
$query = "select * , items.timestamp as times";
if ($withholds) $query .= ' , GROUP_CONCAT(borrowers.borrowernumber ORDER BY borrowers.borrowernumber DESC SEPARATOR "<br>") as bnum,
	GROUP_CONCAT(borrowers.surname ORDER BY borrowers.borrowernumber DESC SEPARATOR "<br>") as lname,
	GROUP_CONCAT(borrowers.firstname ORDER BY borrowers.borrowernumber DESC SEPARATOR "<br>") as fname,
	GROUP_CONCAT(borrowers.phone ORDER BY borrowers.borrowernumber DESC SEPARATOR "<br>") as pphone, 
	GROUP_CONCAT(concat(borrowers.surname,",",borrowers.firstname," / ",borrowers.phone) 
		ORDER BY borrowers.borrowernumber DESC SEPARATOR "<br>") as fullname, 
	biblio.title as title ';
$query .= " from biblio, items, biblioitems ";
if ($withholds) $query .= " ,reserves, borrowers ";
$query .= " where biblio.biblionumber = items.biblionumber
		and  biblio.biblionumber = biblioitems.biblionumber ";
if ($withholds) $query .= " and biblio.biblionumber = reserves.biblionumber 
		and reserves.borrowernumber = borrowers.borrowernumber	
		and reserves.cancellationdate IS NULL
		and reserves.found IS NULL ";
if ($nolditems == 0) 
	$query .= "	and items.timestamp > DATE_SUB(CURDATE() , INTERVAL ". $ndayssince ." DAY) "; 
else if ($nolditems == 2)
	$query .= "	and items.timestamp <= DATE_SUB(CURDATE() , INTERVAL ". $ndayssince ." DAY) "; 
if ($reporttype != NULL) $query .= " AND itemlost = ".$reporttype; 
if ($nreporttype != NULL) $query .= " AND notforloan = ".$nreporttype;
if ($dreporttype != NULL) $query .= " AND binding = ".$dreporttype;
if ($withholds) $query .= "	GROUP BY biblio.biblionumber ";
/*if ($nsortfield == 0)	$query .= " order by items.location , items.itemcallnumber ";
else if ($nsortfield == 1)	$query .= " order by datelastseen DESC, items.location , items.itemcallnumber ";
else if ($nsortfield == 2)	$query .= " order by biblio.title ";
else if ($nsortfield == 3)	$query .= " order by author ";
else if ($nsortfield == 4)	$query .= " order by items.timestamp DESC";
*/
$query .= " order by $nsortfield ";
$query .= " limit 0,1000 "; // Limit so it isn't a big mess!

$result = dbquery($conn, $query);
if (numrows($result) == 0) die("No Items Found");

echo "<table border=1>";
echo "<tr> 
	<th>Location</th>
	<th>Item Call Number</th>
	<th>Last Status Change Date<br><i>use for trace, missing</i></th>
	<th>Multivolumepart</th>
	<th>Title<br><i>Click to biblio</i></th>
	<th>Author</th>
	<th>Barcode<br><i>Click to status change screen</i></th>
	<th>I-type</th>
	<th>Subject</th>
	<th>Datelastseen<br><i>last checked in or updated</i></th>";
if ($withholds) echo "<th>Name</th>";

while ($row = read_db_assoc($result))
	{
	$row = stripslashes_deep($row);
	echo "<tr>";
	echo "<td> ". $row['location'] ." </td>";
	echo "<td> ". $row['itemcallnumber'] ." </td>";
	echo "<td> ". $row['times'] ." </td>";
	if (strpos($row['multivolumepart'], "_") == false)
		echo "<td> ". $row['multivolumepart'] ." </td>";
	else
		echo "<td></td>";
	echo '<td> <b> <a href="'.$addrkoha433. $bib_detail . $row['biblionumber'] .'">'.$row['title'].'</a> </b> </td>';	
	echo "<td> ". $row['author'] ." </td>";
	echo '<td> <a href="'.$addrkoha433. $more_detail .$row['biblionumber'].'">'.$row['barcode'].'</a> </td>';
	echo "<td> ". $row['itype'] ." </td>";
	echo "<td> ";
	$preband = getmarctag($row['marc'], $smarc);
	$band =  substr($preband,0,strlen($preband)-1);
	echo " </td>";
	$formatdate = date("F j, Y",strtotime($row['datelastseen']));
	echo "<td> ". $formatdate ." </td>";
	if ($withholds) echo "<td>".$row['fullname']."</td>";
	echo "</tr>";
	}
echo "</table>";

// marc get function
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
			// Skip everything except for marc fields
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
