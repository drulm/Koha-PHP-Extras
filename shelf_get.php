<?php
## Shelf List Get

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

// Get some marc fields
require_once("File/MARC.php");

$_POST = stripslashes_deep($_POST);
$collection		= $_POST['collection'];
$callstart		= $_POST['callstart'];
$callend		= $_POST['callend'];
$circcount		= $_POST['circcount'];
$startdate		= $_POST['startdate'];
$enddate		= $_POST['enddate'];
$showrows		= $_POST['showrows'];
$excel			= $_POST['excel'];
$htmllist		= $_POST['htmllist'];
$images			= $_POST['images'];
$stitle			= $_POST['stitle'];
$scol			= $_POST['scol'];
$sortfield		= $_POST['sortfield'];
$stext			= $_POST['stext'];
$smarc			= $_POST['smarc'];
$lastseen		= $_POST['lastseen'];

if ($excel != "1" && $htmllist !=1) 
	{
	require_once("topmenu.php");
	echo '<h2 class="style8">Shelf List Results</h2>
  		<p class="style8">Sorted by Collection then Call </p>';
	}

$sendout 		= 0;
//comand line override!
if (sizeof($argv)>1)
	{
	$datesub		= $argv[16];
	$doctitle		= $argv[17];
	$collection	= $argv[1];
	if ($argv[1]!="%") 
		$collection	= explode(" ", $argv[1]);
	$callstart		= $argv[2];
	$callend		= $argv[3];
	$circcount		= $argv[4];
	$startdate		= $argv[5];
	$enddate		= $argv[6];
	if ($startdate == 0) 
		{ // get one month (-30)
		$enddate = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d"), date("Y")));
		$startdate   = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")-$datesub, date("Y")));
		}
	$showrows		= $argv[7]; 
	$excel			= $argv[8];
	$htmllist		= $argv[9];
	$images			= $argv[10];
	$stitle			= $argv[11];
	$scol			= $argv[12];
	$sendout		= $argv[13];
	$filesave 		= $argv[14];
	$modselect		= $argv[15];
	}
	
if ($smarc == "") $smarc = "650";

//print_r ($Names); echo "\n\n<br>";

function findloc($larr, $litem)
{
$lsz = sizeof($larr);
$jj = 0;
$lf = -1;
while ( $jj < $lsz )
	if ( $larr[$jj]['loc'] == $litem )
		{
		$lf = $jj;
		break;
		}
	else
		$jj++;
return $lf;
}

$out ="";

if ($excel != "1" && $htmllist !=1)
	{
	$curdate = time();
	echo date("M d, Y",$curdate)."<br>";
	echo "<h2>Collections Searched | <i>";
	foreach ($collection as $c) echo $c." | ";
	echo "</i></h2>";
	echo "<table border=1>";
	echo "<tr><td>Call Start</td> <td>Call End</td> <td>Start Date</td>  <td>End Date</td> <td>Circ Limit</td> <td> Show Rows </td>";
	echo "<tr>";
	echo "<td>".$callstart;
	if ($callstart == "") echo "[Assume 000]";
	echo "</td>";
	echo "<td>".$callend;
	if ($callend == "") echo "[Assume ZZZ]";
	echo "</td>";
	echo "<td>".$startdate."</td>";
	echo "<td>".$enddate."</td>";
	echo "<td>".$circcount."</td>";
	echo "<td>".$showrows."</td>";
	echo "</tr>";
	echo "</table>";
	echo "</center>";
	echo "<hr>";
	}

if ($showrows == "max") $showrows = 9999999999;
$rowtest = sprintf("%d",$showrows);
if ($rowtest == 0) $showrows = 1000;
if ($circcount == "max") $circcount=9999999999;

// Open the database
$conn = open_database($koha_db, $koha_login, $koha_password, $koha_ip, $koha_name);

// Get Authoried Values
$values = array(); $reptext = array();
get_authorised_values($conn, $status_lost, $values, $reptext);
$dvalues = array(); $dreptext = array();
get_authorised_values($conn, $status_damaged, $dvalues, $dreptext);
$nvalues = array(); $nreptext = array();
get_authorised_values($conn, $status_avail, $nvalues, $nreptext);

//---------------------------get shelf loc----------------------------
$query = 'select id,category,authorised_value,lib from authorised_values where category="LOC"
		ORDER BY authorised_value';
				
// Perform Query
$result = dbquery($conn, $query);
if (($num_rows = numrows($result)) == 0) die("<center><h4>Cannot find the location code</h4></center>");

$loc_codes = array();
while ($row = read_db_assoc($result))
	{
	$row = stripslashes_deep($row);
	$loc_codes[$i] = array();
	$loc_codes[$i]['loc'] = $row['authorised_value'];
	$loc_codes[$i]['loctext'] = $row['lib'];
	}

$query = "select 
			title, author, itemcallnumber, itype, location, barcode, isbn, replacementprice, 
			issues, biblio.biblionumber as biblionumber, itemnumber, datelastseen, dateaccessioned,
			biblioitems.notes as notes, seriestitle, copyrightdate, abstract, issn, publishercode, 
			pages, size, place, url, lccn, notforloan, damaged, itemlost, wthdrawn, onloan
			,copynumber
			,marc
		from biblio, items, biblioitems
		where biblio.biblionumber = items.biblionumber and biblioitems.biblionumber = items.biblionumber and ";
if ($htmllist == 1 && sizeof($argv)>1)
	$query .= "mod(FLOOR(RAND() * 365 + biblio.biblionumber),".$modselect. ") <= 2 and ";
if ($startdate != "")
	$query .= " items.dateaccessioned >= ".'"'.$startdate.'"'." and ";
if ($enddate != "")
	$query .= " items.dateaccessioned <= ".'"'.$enddate.'"'." and ";
if ($lastseen != "")
	$query .= " items.datelastseen <= ".'"'.$lastseen.'"'." and ";
if ($callstart != "")
	$query .= " items.itemcallnumber >= ".'"'.$callstart.'"'." and ";
if ($callstart != "")
	$query .= " items.itemcallnumber <= ".'"'.$callend.'"'." and "; 
if ($stitle != "")
	$query .= " biblio.title LIKE ".'"%'.$stitle.'%"'." and "; 
if ($stext != "")
	$query .= " biblioitems.marc LIKE ".'"%'.$stext.'%"'." and "; 
$query .= " ( items.issues <= ".$circcount. " OR CONCAT(char(items.issues),'0') = '0' ) ";
if ($collection[0] != "%")
	{
	$query .= " and ";
	$query = $query .  "  ( ";
	$i = 0;
	while ($i < count($collection))
		{
		$query = $query . " items.location = ".' "'.stripslashes($collection[$i]).'" '." ";
		$i++;
		if ($i < count($collection)) $query = $query . " OR ";
		}
	$query = $query .  " ) ";
	}
if ($htmllist == 1) $query .= " group by items.location,biblio.biblionumber ";
else $query .=  " order by $sortfield ";
$query .=  " limit 0, " . $showrows ;

//echo "\n $query \n\n<br>";

// Perform Query
$result = dbquery($conn, $query);
$num_rows = numrows($result);

//-----------------------------------------------------------------------------
// Excel Export 
if ($excel == "1")
	{
	$header = "";
	$num_fields = numcols($result);
	$fields = field_names($result);
	for($i = 0; $i < $num_fields; $i++) $header .= $fields[$i][name]."\t";
	$header .= "\n";
	while ($row = read_db_assoc($result))
		{
		$row = stripslashes_deep($row);
		$line = '';
		foreach($row as $value) 
			{                                            
			if ((!isset($value)) OR ($value == "")) {
				$value = "\t";
			} else {
				$value = str_replace('"', '""', $value);
				$value = '"' . $value . '"' . "\t";
				}
			$line .= $value;
			}
		$data .= trim($line)."\n";
		}
	$data = str_replace("\r","",$data); 
	if ($data == "") $data = "\n(0) Records Found!\n";
    header("Content-type: application/x-msdownload");
	header("Content-Disposition: attachment; filename=extraction.xls");
	header("Pragma: no-cache");
	header("Expires: 0");
	print "$header"."$data";
	}
// Send output to screen
else if ($htmllist != 1)
	{
	echo "QUERY: ".$query."<br>";
	echo "<center><h2><i>Number of Items: $num_rows </i></h2></center><br>";
	echo "<table border=1>";
	echo "<tr><th>Location</th><th>Call Number</th><th>Title</th>
		<th>Volume/Part</th>	
	    <th>On-Loan</th><th>Status</th><th>Image</th><th>Copy Number</th><th>Author</th><th>Marc Field</th>
		<th>Date Last Seen</th><th>Date Added</th><th>Circs</th><th>Barcode</th><th>I-Type</th><th>Price</th>
		<th>Series</th><th>ISBN</th><th>ISSN</th><th>LCCN</th></tr>";
	while ($row = read_db_assoc($result))
		{
		$row = stripslashes_deep($row);
		echo "<tr>";
		echo "<td> ". $row['location'] ." </td>";
		echo "<td> ". $row['itemcallnumber'] ." </td>";
		echo '<td> <b> <a href="'.$addrkoha433.$search_koha. $row['barcode'] .'">'.$row['title'].'</a> </b> </td>';
		echo "<td> ". $row['copynumber'] ."</td>";
		// notforloan, damaged, itemlost, wthdrawn
		if ( $row['onloan'] ) echo "<td><b>On Loan<br />Until<br />". $row['onloan'] ."</b></td>";
			else echo "<td>Not on loan</td>";
		echo "<td><b>";
		if ( $row['notforloan'] != 0 ) echo "Not For Loan, ";
		$lind = array_search($row['itemlost'], $values);
		$dind = array_search($row['itemlost'], $dvalues);
		$nind = array_search($row['itemlost'], $nvalues);
		if ( $row['damaged'] != 0 ) echo "Damaged (". $dreptext[ $dind ]."),";
		if ( $row['itemlost'] != 0 ) echo "Status (". $reptext[ $lind ]."),";
		if ( $row['wthdrawn'] != 0 ) echo "Withdrawn (". $nreptext[ $nind ]."),";
		echo "</b>";
		if ( $row['notforloan']==0 && $row['damaged']==0 && $row['itemlost']==0 && $row['wthdrawn']==0 ) echo "Can be<br /> Loaned";
		echo "</td>";
		if ($images)
				{
				$isbntrim = strtok(trim($row['isbn'])." ", " :(");
				echo '<td> <center> <img src="http://images.amazon.com/images/P/'. $isbntrim .'.01.TZZZZZZZ.jpg" > </center> </td>'; 
				}
		else	
				echo '<td> </td>';
		if (strpos($row['copynumber'], "_") == false)
			echo "<td> ". $row['copynumber'] ." </td>";
		else
			echo "<td></td>";
		
		echo "<td> ". $row['author'] ." </td>";
		echo "<td> ";
		$preband = getmarctag($row['marc'], $smarc);
		$band =  substr($preband,0,strlen($preband)-1);
		echo " </td>";
		$formatdate = date("F j, Y",strtotime($row['datelastseen']));
		echo "<td> ". $formatdate ." </td>";
		$formatdate = date("F j, Y",strtotime($row['dateaccessioned']));
		echo "<td> ". $formatdate ." </td>";
		echo "<td> ". $row['issues'] ." </td>";
		echo '<td> <a href="'.$addrkoha433. $more_detail .$row['biblionumber'].'">'.$row['barcode'].'</a> </td>';

		echo "<td> ". $row['itype'] ." </td>";
	    echo "<td> ". $row['replacementprice'] ." </td>";
		echo "<td> ". $row['seriestitle'] ." </td>";
		echo "<td> ". $row['isbn'] ." </td>";
		echo "<td> ". $row['issn'] ." </td>";
		echo "<td> ". $row['lccn'] ." </td>";
		echo "</tr>";
		}
	echo "</table>";
	}
else // HTML FOR WEB SITE
	{
	$out .= '<?php include("listheader.htm"); ?>';
	$source = $filesave;
	$rcnt = 1; 
	$cols = $scol;
	$color = 'lightblue';
	$out.= "<center><h2>".$doctitle."</h2></center>";
	$out.= "<table>";
	$out.= "<tr>";
	$loc = "";
	while ($row = read_db_assoc($result))
		{
		$row = stripslashes_deep($row);
		$oldloc = $loc;
		$loc = $row['location'];
		if ($oldloc <> $loc) 
			{
			$lkey = findloc($loc_codes, $loc);
			$ltextprint = explode("<br>", $loc_codes[$lkey]['loctext']);
			$out.= "</tr></table><hr><h3><center>"
				.substr($ltextprint[1],4,strlen($ltextprint[1])-4) ."</center></h3>";
			$rcnt = 1; 
			$out.= "<table border=0> <tr>";
			}
		$out.= "<td>";
		$out.= "<center>";
		$cditem = $row['itype'];
		if ($images && $cditem != "CD" && $cditem != "DVD" )
				{
				$isbntrim = strtok(trim($row['isbn'])." ", " :(");
				$out.= ' <img src="http://images.amazon.com/images/P/'. $isbntrim .'.01._THUMBZZZ_PB_PU_PU0_.jpg" > '.'  <br> ';
				}
		if ($cditem == "CD" || $cditem == "ADOC") 
				$out.= '<img src="http://a1.smfpl.org/prevsite/images/cdicon.gif" > '.' </b> <br> ';
		else if ($cditem == "DIG")
				$out.= '<img src="http://a1.smfpl.org/prevsite/images/opacplayaway.jpg" > '.' </b> <br> ';
		else if ($cditem == "DVD" || $cditem == "NFDV") 
				$out.= '<img src="http://a1.smfpl.org/prevsite/images/movieicon.gif" > '.' </b> <br> ';

		$out.= '<b> <a href="http://opac.smfpl.org/bib/'. $row['biblionumber'] .'">'.$row['title'].'</a> </b> <br> ';
		if ($row['author'] != "") $out.= " by:". $row['author'] ." <br>";
		$out.= '<b><a href="http://opac.smfpl.org/cgi-bin/koha/opac-reserve.pl?biblionumber='.$row['biblionumber'].'">Place Reserve</a><b>';
		$out.= "</center>";
		$out.= "<br></td>";
		if ($rcnt % $cols == 0) 
			{
			$out.= "</tr>";
			if ($i+1 < $num_rows) $out.= "<tr>";
			}
		$rcnt++;
		}
	$out.= "</table></DIV>";
	$out .= '<?php include("ender.htm"); ?>';
	echo $out;
	if ($sendout) 
		file_put_contents($source, $out) or die("Cannot write file");
	}	

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

