<?php
## Holds Ratio List

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
?>

<center>
<h2> Held Over List, Long Overdue Holds ( <?php echo $_POST['ndayssince'] ?> days)</h2>
<form action="holdoverlist.php" method="POST">
   <p>Enter Number of Days: 
       <input name="ndayssince" type="text" id="ndayssince" value=" <?php echo $held_over_days; ?> " size="10" maxlength="10" />
        <input type="submit" value="Submit Information" />
   </p>
   <p>Sort by:
     <select name="sortfield" size="1" id="nsortfield">
       <option value="biblio.title" selected>Title</option>
       <option value="author">Author</option>
       <option value="items.location , items.itype">Location / Call</option>
       <option value="earlydate">Earliest Hold Date</option>
       <option value="biblio.title DESC">Title DESC</option>
       <option value="author DESC">Author DESC</option>
       <option value="items.location DESC, items.itype DESC">Location / Call DESC</option>
       <option value="earlydate DESC">Earliest Hold Date DESC</option>
                         </select>
</p>
</form>
</center>
<?php
$_POST = stripslashes_deep($_POST);
$held_over		= $_POST['ndayssince'];
$sortfield		= $_POST['sortfield'];

if ($held_over == NULL || $sortfield == NULL)
	{
	echo '<font color="red">';
	echo '<center>Click -submit- when ready. </center>';
	echo '</font>';
	exit();
	}

$conn = open_database($koha_db, $koha_login, $koha_password, $koha_ip, $koha_name);

// Get all the bibs that have some reserve, that have not been cancelled
$query = 
	"select *,
		min(reservedate) as earlydate
			from reserves, items, biblio, biblioitems
			where
			reserves.biblionumber = biblio.biblionumber
			and
			items.biblionumber = biblio.biblionumber
			and
			items.biblionumber = reserves.biblionumber
			and
			biblio.biblionumber = biblioitems.biblionumber
			and
			reservedate <= DATE_SUB(CURDATE(), INTERVAL $held_over DAY)
			and
			cancellationdate IS NULL
			and
			found IS NULL
			and 
			priority <> 0
		group by biblio.biblionumber
		order by $sortfield ";

// Perform Query
$result = dbquery($conn, $query);
if (($numrows = numrows($result)) == 0) die("No data found");

echo "<center><table border=1>";
echo "<tr> 
		<th>Title</th>
		<th>Author</th> 
		<th>Call No</th> 
		<th>Collection</th> 
		<th>Itype</th> 
		<th>Early Date</th> 
	</tr>";
	
// Loop through these possible reserves
while ($row = read_db_assoc($result))
	{
	$row = stripslashes_deep($row);
			echo "<tr>";
//			echo '<td> <a href="'.$addrkoha433.$bib_detail. 
				$row['biblionumber'] .'">'.$row['title'].'</a> </td>';	
				echo '<td><b> <a href="'.$addrkoha433. $bib_detail . 
					$row['biblionumber'] .'">' .$row['title'];
				$pretitle2 = getmarctag($row['marc'], "245", "b");
				$title2 =  substr($pretitle2,0,strlen($pretitle2)-1);
				echo '</a></b></td>';	
			echo "<td> ". $row['author'] ." </td>";
			echo "<td> ". $row['itemcallnumber'] ." </td>";
			echo "<td> ". $row['location'] ." </td>";
			echo "<td> ". $row['itype'] ." </td>";
			echo "<td> ". $row['earlydate'] ." </td>";
			echo "</tr>";
	}
echo "</table></center>";

function getmarctag($marc, $findtag, $subtag) 
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
						if ($code == $subtag) echo " <br /> ($findtag $subtag) ".substr($value,5,strlen($value)-5) ;
					$found = 1;
					//break;
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

// close the database
disconnect($conn);
?>
