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

// Get parameters

function removeapos($s)
{
$rem = array("'", "/");
$fixed = str_replace($rem, "", $s);
return $fixed;
}

$out = "";
$out .= '<?php include("listheader.htm"); ?>';
$out .="<h3><center>Recently Returned Items</center></h3>";

$images = 1;

$conn = open_database($koha_db, $koha_login, $koha_password, $koha_ip, $koha_name);

$query = 'select biblio.biblionumber, title, author, isbn, volume, itemcallnumber
		from 
		statistics, items, biblio, biblioitems
		where 
		statistics.itemnumber = items.itemnumber
		and
		items.biblionumber = biblio.biblionumber
		and
		biblio.biblionumber = biblioitems.biblionumber
		and
		type = "return"
		and
		datetime >= CURDATE()
		and 
		itype = "GE"
		order by datetime desc
		limit 0,20';

/*$result = dbquery($conn, $query);
if (($num_rows = numrows($result)) == 0) die("No Rows Found");
$out .= "<b><marquee SCROLLAMOUNT=2 SCROLLDELAY=1>";	
$i = 0;

while ($row = read_db_assoc($result))
	{
	$row = stripslashes_deep($row);

	$out .= '<a target="_parent" href="http://opac.smfpl.org/cgi-bin/koha/opac-detail.pl?bib='.$row['biblionumber'].'">'.$row['title'];

	$cditem = $row['itemtype'];
//	if ($images && $cditem != "CD" && $cditem != "DVD")
//			{
			$isbntrim = strtok(trim($row['isbn'])." ", " :(");
			$out.= '<img align="middle" src="http://images.amazon.com/images/P/'. $isbntrim .'.01.THUMBZZZ.jpg">';
//			}
			
/*	if ($cditem == "CD" || $cditem == "ADOC") 
			$out.= '<img align="middle" src="http://smfpl.org/images/cdicon.gif" > '.'  ';
	else if ($cditem == "DIG")
			$out.= '<img align="middle" src="http://smfpl.org/images/opacplayaway.jpg" > '.'  ';
	else if ($cditem == "DVD" || $cditem == "NFDV") 
			$out.= '<img align="middle" src="http://smfpl.org/images/movieicon.gif" > '.'  ';
*/
/*				
	if ($row['author'] != "") 
		$out .= ", by:".$row['author'] ;
	
		
	$out .= "</a> &nbsp; &nbsp; &nbsp; &nbsp; ";

	$i++;
	}
$out .= "</marquee></b><hr>";
*/
//----------------------New Effect--------------------------------------------------------------------

$result = dbquery($conn, $query);
if (($num_rows = numrows($result)) == 0) die("No Rows Found");
$i = 0;
$out .= '<div id="myslides">';
while ($row = read_db_assoc($result))
	{
	$row = stripslashes_deep($row);
	$out .= "<div>";
	$cditem = $row['itemtype'];
	$isbntrim = strtok(trim($row['isbn'])." ", " :(");
	$out .= "<table><tr>";
	$out .= '<td><img align="middle" src="http://images.amazon.com/images/P/'. $isbntrim .'.01.SCMZZZZZZZ.jpg"></td>';
	$out .= '<td><p><a target="_parent" href="http://opac.smfpl.org/cgi-bin/koha/opac-detail.pl?bib='.$row['biblionumber'].'">'.$row['title'].'</a></p>';
	$out .= "<p>by:".$row['author']."</p></td></table>" ;
	$out .= "</div>";
	$i++;
	}
$out .= "</div>";


//---------------------------------------------------------------------------------------------------------
$result = dbquery($conn, $query);
if (($num_rows = numrows($result)) == 0) die("No Rows Found");
$i = 0;

$out .= "<table align=center>
	<tr> <th>Title</th> <th>Cover</th><th>Author</th> <th>Call Number</th> </tr>";
while ($row = read_db_assoc($result))
	{
	$out .= "<tr>";
	
	$row = stripslashes_deep($row);

	$out .= '<td><a target="_parent" href="http://opac.smfpl.org/cgi-bin/koha/opac-detail.pl?bib='.$row['biblionumber'].'">'. $row['title'] ."</td> ";

	$cditem = $row['itemtype'];
//	if ($images && $cditem != "CD" && $cditem != "DVD")
//			{
			$isbntrim = strtok(trim($row['isbn'])." ", " :(");
			$out.= '<td><img align="middle" src="http://images.amazon.com/images/P/'. $isbntrim .'.01.THUMBZZZ.jpg"></a></td>';
//			}
			
/*	if ($cditem == "CD" || $cditem == "ADOC") 
			$out.= '<img align="middle" src="http://smfpl.org/images/cdicon.gif" > '.'  ';
	else if ($cditem == "DIG")
			$out.= '<img align="middle" src="http://smfpl.org/images/opacplayaway.jpg" > '.'  ';
	else if ($cditem == "DVD" || $cditem == "NFDV") 
			$out.= '<img align="middle" src="http://smfpl.org/images/movieicon.gif" > '.'  ';
*/
//	$out .= "<td>".$row['volume']."</td>" ;			
			
	if ($row['author'] != "") 
		$out .= "<td>by:".$row['author']."</td>" ;
	else $out .= "<td>&nbsp;  </td>";
	$out .= "<td>".$row['itemcallnumber']."</td>" ;	
	$out .= "</tr>";
	
	$i++;
	}
$out .= "</table>";
$out .= '<?php include("ender.htm"); ?>';

file_put_contents("c:/dump/recentret_list.php", $out);

echo $out."
";

// close database
disconnect($conn);
?>
