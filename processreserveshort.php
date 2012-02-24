<?php
## Process Reserve List -- This is a reserve pull list

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

ini_set("memory_limit","256M");
// Set this to your Koha staff intranet base address
require_once("kohafunctions.php");
// Set this to your Koha staff intranet base address
require_once("addrkoha.php");
// Print the main top menu
require_once("topmenu.php");

// Get some marc fields
require_once("File/MARC.php");

$dayofweek = jddayofweek ( cal_to_jd(CAL_GREGORIAN, date("m"),date("d"), date("Y")));

// Check for holidays
$sweek = $week;
$holidays = get_holidays($holidays, $local_holiday, $sweek, $koha_db, $local_login, $local_password, $local_ip, $local_name);

// Open the database
$conn = open_database($koha_db, $koha_login, $koha_password, $koha_ip, $koha_name);

$sql = 'select 	DISTINCT * , count(*),reserves.timestamp,
		GROUP_CONCAT(reservenotes ORDER BY reserves.priority ASC SEPARATOR " <br> ")
		from reserves, biblio, biblioitems 
		where 
		cancellationdate IS NULL 
		AND reserves.biblionumber = biblio.biblionumber
		AND found IS NULL
		AND biblio.biblionumber = biblioitems.biblionumber
		GROUP BY reserves.biblionumber
		ORDER BY reserves.biblionumber';
// Perform Query
$resultSet = dbquery($conn, $sql);
if (numrows($resultSet) == 0) die("No Patrons to Call");

$resultlines = array();
$i = 0;
while ($row = read_db_assoc($resultSet))
	{
	$row = stripslashes_deep($row);
	$resultlines[$i] = array();
	$resultlines[$i]['count'] = $row['count(*)'];
	$resultlines[$i]['bornum'] = $row['borrowernumber'];
	$resultlines[$i]['title'] = $row['title'];
	$resultlines[$i]['author'] = $row['author'];
	$resultlines[$i]['biblionumber'] = $row['biblionumber'];
	$resultlines[$i]['itemnumber'] = $row['itemnumber'];
	$resultlines[$i]['reservedate'] = $row['reservedate'];
	$resultlines[$i]['timestamp'] = $row['timestamp'];
	$resultlines[$i]['marc'] = $row['marc'];
	$resultlines[$i]['reservenotes'] = $row['GROUP_CONCAT(reservenotes ORDER BY reserves.priority ASC SEPARATOR " <br> ")'];
	$i++;
	}

$color = 'white';
echo "<center><h1>Reserve Report: In Stacks Pickup for Reserve Shelf</h1>";
echo "<h2>".date('l dS \of F Y h:i:s A')."</h2>";

$tyes = 0;
$tno = 0;
$freport = array();

$j=0;
foreach($resultlines as $row)
	{
	$sql2 = 'select  location, itype, itemcallnumber, count(*), barcode, itemlost, copynumber, itemnumber, 
				GROUP_CONCAT(itemcallnumber ORDER BY itemcallnumber ASC SEPARATOR "<br>") 
			from items 
			where 
				barcode > 0   
				'.$exclude_from_reserve2.'
				AND (notforloan = 0 or notforloan IS NULL)
				AND (itemlost = 0 OR itemlost IS NULL)
				AND (wthdrawn = 0 OR wthdrawn IS NULL)
				AND (itemcallnumber IS NOT NULL or itemcallnumber <>"")
				'.$exclude_from_reserve.'
				AND biblionumber = '.$row['biblionumber'].' 
				GROUP by biblionumber ';
	
	$allbib = 0;
	$resultSet2 = dbquery($conn, $sql2);

	$i2 = 0;
	while ($row2 = read_db_assoc($resultSet2))
		{
		$row2 = stripslashes_deep($row2);
		$allbib += $row2['count(*)'];
		$itemcallnumber = $row2['itemcallnumber'];
		$location = $row2['location'];
		$onloan = $row2['onloan'];
		$itype = $row2['itype'];
		$itemlost = $row2['itemlost'];
		$i2++;
		}

	$sql3 = 'select count(*), max(date_due), max(returndate),
				GROUP_CONCAT(itemcallnumber ORDER BY itemcallnumber ASC SEPARATOR "<br>") 
			from items, issues
			where 
			items.itemnumber = issues.itemnumber
			AND returndate IS NULL  
			AND (notforloan = 0 or notforloan IS NULL)
			AND (itemlost = 0 OR itemlost IS NULL)
			AND (wthdrawn = 0 OR wthdrawn IS NULL)
			AND  barcode > 0   
			'.$exclude_from_reserve2.'
			'.$exclude_from_reserve.'
			AND biblionumber = '.$row['biblionumber'].' 
			GROUP by items.itemnumber ';
	
	$outbib = 0;
	$whatisout = "";
	$resultSet3 = dbquery($conn, $sql3);
	$i3 = 0;
	while ($row3 = read_db_assoc($resultSet3))
		{
		$row3 = stripslashes_deep($row3);
		$outbib += $row3['count(*)'];
		$whatisout .= $row3['GROUP_CONCAT(itemcallnumber ORDER BY itemcallnumber ASC SEPARATOR "<br>")']."<br>";
		$i3++;
		}

	$sql4 = 'select count(*)
			from reserves
			where
			found = "W" 
    		AND biblionumber = '.$row['biblionumber'].' 
			GROUP by biblionumber ';
	
	$waitingbib = 0;
	$resultSet4 = dbquery($conn, $sql4);
	$i4 = 0;
	while ($row4 = read_db_assoc($resultSet4))
		{
		$row4 = stripslashes_deep($row4);
		$waitingbib += $row4['count(*)'];
		$i4++;
		}
// Count number available
	$tavail = $allbib - $outbib - $waitingbib;
	
	$tgrab =  min($row['count'], $tavail);

	if ($tavail >= 1 and strlen($itemcallnumber)>0) $tyes++;
	else $tno++;

	if ($tavail >= 1 and strlen($itemcallnumber)>0)
		{
		$freport[$j] = array();
		$freport[$j]['timestamp'] = $row['timestamp'];
		$freport[$j]['title'] = $row['title'];
		$freport[$j]['biblionumber'] = $row['biblionumber'];
		$freport[$j]['itemcallnumber'] = $itemcallnumber;
		if ($location == $periodical_special) $location = "* ".$location;
		$freport[$j]['location'] = $location;
		$freport[$j]['sortby'] = $location.$itemcallnumber;
		$freport[$j]['itype'] = $itype;
		$freport[$j]['tgrab'] = $tgrab;
		$freport[$j]['author'] = $row['author'];
		$freport[$j]['reservenotes'] = $row['reservenotes'];
		$freport[$j]['reservedate'] = $row['reservedate'];
		$freport[$j]['itemlost'] = $itemlost;
		$freport[$j]['whatisout'] = $whatisout;
		$freport[$j]['marc'] = $row['marc'];
		$j++;
		if ($color == 'white') $color = 'lightgray'; else $color = 'white';
		}
	}
$preport = sortByField($freport,'sortby');

	echo '<table style="text-align: left; width: 100%;" border="1"
			cellpadding="2" cellspacing="2">
			<tr bgcolor="lightgray">
				<th>Title<br>Click to goto item to mark lost <i>If item cannot be found</i>.</th>
				<th>Call Num</th>
				<th>Location</th>
				<th>Itype</th>
				<th>Get</th>
				<th>Author</th>';
	echo	'<th>Item-Note: <strong>Try to RETURN in Order Shown. <br>Right now, multiple reserves for different items must<br> be hand checked and resolved <br>(current month not allowed)</strong></th>
			<th>Reserve<br>Date</th>
			<th>Subject</th>
		</tr>';

//---------------------------------------------------------------------------------------------------------
// (Warning!) Hideous logic warning! (Warning!)
foreach($preport as $row)
	{
	echo "	<tr bgcolor='" . $color . "'>";
//	echo "<td><b>" . $row['title'] . "</b></td>";
	echo '<td><b> <a href="'.$addrkoha433 . $bib_detail . $row['biblionumber'] .'">'.$row['title'].'</a></b></td>';	
	
	$note_list = explode("<br>", trim($row['reservenotes']));
	$repl= array(".", "/", ":");
	$simpnote = rtrim(strtoupper(stripslashes(str_replace($repl, "", trim($note_list[0])))));
	$simptitle = rtrim(strtoupper(stripslashes(str_replace($repl, "", trim($row['title'])))));
	
	if ($row['itype'] != $periodical_special)
		{
		echo "<td>";
		if (  strcmp($simpnote,$simptitle)<>0  && $simpnote <> "" )
			{
			echo "<b><i>Special:</i></b> Generally *SEE NOTE AT RIGHT* for copy or special hold other than this callnumber: <br>";
			echo $row['itemcallnumber'] . "<br>";
			}
		else
			echo $row['itemcallnumber'] . "</b><br>";
		echo "</td>";
		}
	else
		echo "<td>" . "<b><i>Periodical: ".$row['itemcallnumber']." </i></b> See Note >>" . "</td>";
	echo "<td><b>"; 
	echo $row['location'];
	if ($row['itemlost'] == 7) echo "<br>(display area)"; 
	echo "</b></td>";
	echo "<td>" . $row['itype'] . "</td>";
	echo "<td><b>" . $row['tgrab'] . "</b></td>";
	echo "<td>" . $row['author'] . "</td>";
	
	if ( $simpnote <> $simptitle  && $simpnote <> "")
		{
		echo "<td>";
		$note_list = explode("<br>", $row['reservenotes']);
		$ii=1;
		echo "<b>Pull/check-in order:</b><br>";
		$oldnote = "";
		foreach ($note_list as $noteitem)
			{
			$noteitem = str_replace(" ", "", $noteitem);
			if ($noteitem <> "" && $noteitem <> $oldnote)
				{ 
				echo "<i>#".$ii.")  ".$noteitem."</i><br>";
				$oldnote = $noteitem;
				}
			$ii++;
			}
		//echo "<hr>";

		if ($row['itype'] == $periodical_special) 
			{
			$whatisout = str_replace("<br>", " <b>/</b> ", $row['whatisout']);
			if ($whatisout <> "")
				echo "<br><b>These are CHECKED-OUT:<br></b>".$whatisout;
			echo "</td>";
			}
		}
	else
		echo "<td> </td>";
	
	echo "<td>" . $row['timestamp'] . "</td>";
	
	echo "<td> ";
	$preband = getmarctag($row['marc'], "650");
	$band =  substr($preband,0,strlen($preband)-1);
	echo " </td>";

	echo "</tr>";
	
	if ($color == 'white') $color = 'lightgray'; else $color = 'white';
	}

echo "<br>Count = ". $tyes ."<br>";

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
