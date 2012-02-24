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

<center>
  <form action="spineget.php" method="POST" name="myform" >
    <h2 align="center"><strong><font size="5">Quick Spine Label</font></strong></h2>
    <p align="left">Instructions: Just beep or enter in a barcode or partial barcode. Then press CNTL-p to print. 
		Backspace to enter another. You may change the number of characters per row and/or the font size for special 
	labels as needed. Click [New Barcode] to start over and scan next book.</p>
    <table width="55%" height="258" >
    <tr>
      <td width="35%" height="57"><p><span class="style1"><span class="style5">ENTER BARCODE</span><span class="style5"></span><strong> * </strong></span></p>
        </td>
      <td width="26%"><strong>
        <input name="sbarcode" type="text" size="20" maxlength="20"  />
        <font size="4">        </font>          </strong>
		  
	    </td>
      <td><strong><font size="4">
        <input type="submit" name="Submit" value="Search"  />
      </font></strong></td>
      </tr>
    <tr>
      <td height="63"><strong>Optional text to print on last line </strong></td>
      <td colspan="2"><strong>
        <input name="sopt" type="text" id="sopt" size="20" maxlength="20"  />
</strong></td>
    </tr>
    <tr>
      <td height="63"><strong>Characters per row (preset)<br />
      </strong></td>
      <td colspan="2"><strong>
        <input name="swidth" type="text" id="swidth" value="8" size="20" maxlength="20" />
      </strong></td>
    </tr>
    <tr>
      <td height="63"><strong>Custom font size (preset)</strong></td>
      <td colspan="2"><strong>
        <input name="sfont" type="text" id="sfont" value="3" size="20" maxlength="20" />
      </strong></td>
    </tr>
  </table>

    <hr />
  </form>
</center>

<?php
refocus("myform", "sbarcode");

/*$sbarcode		= $_POST['sbarcode'];
$optional		= $_POST['sopt'];
$width			= $_POST['swidth'];
$fsize			= $_POST['sfont'];

if ( !$sbarcode or strlen($sbarcode)<5)
	{
	echo '<center><h4>';
	echo 'You need to enter valid barcode or at least 5 digits<br>';
	echo '<br> Please go to the previous page and try again.<br>';
	echo '</h4></center>';
	exit;
	}

// Open the database
$conn = open_database($koha_db, $koha_login, $koha_password, $koha_ip, $koha_name);

$mod_title = '%' . mysql_real_escape_string($sbarcode) . '%';
// Check the old cardcode
$query = sprintf("select  * from `items` WHERE barcode LIKE '%s'",
				$mod_title);

// Perform Query
$result = dbquery($conn, $query);
$num_rows = numrows($result);
if ($num_rows == 0) 			// cardcode does not exist case
	{
	echo '<center><h4>';
	echo 'This barcode does not exist, please return to previous screen';
	echo '</h4></center>';
	exit;
	}
else if ($num_rows > 1)
	{
	echo '<center><h4>';
	echo 'Your search returned '.$num_rows.' barcodes. <br>';
	echo 'You must be more specific to the barcode of the item';
	echo '</h4></center>';
	exit;
	}

// Check result
// This shows the actual query sent to MySQL, and the error. Useful for debugging.
// Open the "local" database
$conn2 = open_database($koha_db, $local_login, $local_password, $local_ip, $local_name);

echo '<font size='.$fsize.' face="Courier New"><b>';

$row = read_db_assoc($result);
$row = stripslashes_deep($row);

$loc = $row['location'];
$fullbarcode = $row['barcode'];

$query2 = sprintf("select * from `spinelabel` WHERE location = '%s'",$loc);

// Perform Queries
$result2 = dbquery($conn2, $query2);
if (numrows($result2) == 0) die("This location does not exist, please return to previous screen");

while ($row2 = read_db_assoc($result2))
	{
	$row2 = stripslashes_deep($row2);
	$spine = $row2['spine'];
	}
	
echo "<center>Spine Label For: $fullbarcode</center><hr><br>";
	
if ($spine != NULL) echo printsize($spine, $width, 0, 1) ;

if ($row['itemcallnumber'] != NULL) 
	echo printsize($row['itemcallnumber'] , $width, 1, 1);
$copyvol = $row['multivolumepart'];
if (strrchr ( $copyvol , '_' )==NULL && $copyvol!=NULL)
	echo printsize($copyvol, $width, 0, 1) ;
if (optional != NULL)
	echo printsize($optional, $width, 0, 1);
echo '</b></font>';

disconnect($conn);
disconnect($conn2);

//---------------------------------------------------------------------
//-------------------------------FUNCTIONS-----------------------------
//---------------------------------------------------------------------

function printsize($s,$n,$spacebreak,$nolf)
{
$s = trim($s);
//echo strlen($s)."/";
if (strlen($s) == $n)
	echo $s."<br>";
else
	{
	$k=0; $mod=0;
	while ($k < strlen($s))
		{
		$sub = substr($s,$k,1);
		if ($sub==',' && $spacebreak==1)
			{
			echo ',<br>';
			$mod = 0;
			}
		else
			{
			echo $sub;
			$mod = $mod + 1;
			//echo "(".$k.",".$mod.") ";
			}
		$k = $k + 1;
		if ($k < strlen($s) && $mod%$n ==0 && $mod!=0 && $nolf==1) echo '<br>';
		}
	if ($mod != 0 && $mod%$n != 0 ) 
		echo '<br>';
	}
}
*/
?>

