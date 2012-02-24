<?php
## ILL - Data Entry 

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
<form action="illsubmit.php" method="POST" name="myform">
  <h1>Register ILL Check Out</h1>
  <table width="70%" >
    <tr>
      <td width="35%">       
	  </td>
      <td width="10%"><div align="right">
        <h2><strong>Title
        </strong></h2>
      </div></td>
      <td width="55%"><font size="+3">
        <input type="text" name="title" size="20" maxlength="50" />
      </font></td>
    </tr>
    <tr>
      <td colspan="2"><div align="right">
        <h2><strong>Collections</strong></h2>
      </div></td>
      <td><font size="+3">
      <select name="collection" size="1">
			<?php echo $ill_collections; ?>
      </select>
      </font></td>
    </tr>
    <tr>
      <td colspan="2"><div align="right">
        <h2><strong>Call#</strong></h2>
      </div></td>
      <td><font size="+3">
      <input type="text" name="callno" size="20" maxlength="30" />
      </font></td>
    </tr>
    <tr>
      <td colspan="2"><div align="right">
        <h2><strong>Item Type</strong></h2>
      </div></td>
      <td><p><font size="+3">
</font><font size="+3">
<select name="itype" size="1" id="itype">
            <?php echo $ill_categories; ?>
          </select>
      </font></p>
      </td>
    </tr>
    <tr>
      <td colspan="2"><h2 align="right"><strong>Item Barcode (enter last) </strong></h2>
        <div align="right">
          <h2><strong>(the pink slip in the item)</strong></h2>
        </div></td>
      <td><font size="+3">
      <input type="text" name="barcode" size="20" maxlength="20" />
      </font></td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
      <td><input type="submit" value="SUBMITILL" /></td>
    </tr>
  </table>
</form>

<?php
// OK, main code starts - a little add on formatting
$title		= trim(addslashes($_POST['title']));
$collection	= trim(addslashes($_POST['collection']));
$callno		= trim(addslashes($_POST['callno']));
$itype		= trim(addslashes($_POST['itype']));
$barcode	= trim(addslashes($_POST['barcode']));

echo '<font color="red">';
$illerr = 0;
echo '<center><h4>';
if ( !$barcode )
	{
	echo 'Missing <b>BARCODE...</b>';
	$illerr++;
	}
if ( !$title )
	{
	echo ' Missing <b>TITLE...</b>';
	$illerr++;
	}
if ( !$callno )
	{
	echo ' Missing <b>CALL NUMBER...</b>';
	$illerr++;
	}
if ($illerr != 0)
	{
	echo '<br> '.$illerr.' fields empty, ILL is not yet accepted.<br>';
/*	echo '
	<script type="text/javascript">
	document.myform.title.focus();
	</script>
	';
*/	refocus("myform", "title");
	exit;
	}
echo '</h4></center>';
echo '</font><hr>';

// show the information provided
echo '<hr><h4><center>You Entered the Following Information:</center></h4>';
echo '<table>';
echo '<tr><td align=right><b>Title:</b></td><td> '.$title.'</td></tr>';
echo '<tr><td align=right><b>Collection:</b></td><td> '.$collection.'</td></tr>';
echo '<tr><td align=right><b>Call:</b></td><td> '.$callno.'</td></tr>';
echo '<tr><td align=right><b>I-Type:</b></td><td> '.$itype.'</td></tr>';
echo '<tr><td align=right><b>Barcode:</b></td><td> '.$barcode.'</td></tr>';
echo '</table>';

// Open the database
$conn = open_database($koha_db, $koha_login, $koha_password, $koha_ip, $koha_name);

// Construct the query to see if the circulation exists (check in barcode already)
$query2 = sprintf("SELECT * FROM items, borrowers, issues 
			WHERE borrowers.borrowernumber = issues.borrowernumber
			AND issues.itemnumber = items.itemnumber 
			AND issues.returndate IS NULL 
			AND items.barcode LIKE '%s'  
			ORDER BY issues.timestamp DESC ", mysql_real_escape_string($barcode));

// Perform Query to look for barcode already checked out
$result2 = dbquery($conn, $query2);
$num_rows2 = numrows($result2);
/*	echo '
	<script type="text/javascript">
	document.myform.title.focus();
	</script>
	';
*/	refocus("myform", "title");
if ($num_rows2 == 0) 
	{
	echo '<center><h4>';
	echo '<font color="red">';
	echo '<b><i> This item BARCODE does not exist, <br>' .
		  'or this item is not checked out currently. </i> </b> <br>' .
		  '<i>You must checkout an ILL item to the patron before entering in the ILL </i><br>'.
		  '<i>If the correct patron does not show up, then you must be sure to CHECK OUT THE ILL ITEM prior to entering the ILL 
		   information';
	echo '</font></h4></center>';
	exit;
	}
else 
	{
	echo '<center><h3>';
	echo "<br> The ILL barcode= <b> $barcode </b> is validated. <br>";
	echo '</h3></center>';
	}

// If the record exists, pull up the patron name for this checked out barcode to use
$row = read_db_assoc($result2);
$row = stripslashes_deep($row);

$patronid = $row['borrowernumber'];
$lname = $row['surname'];
$fname = $row['firstname'];
$fullname = $lname . ', ' . $fname;

echo '<center><h2> <a href="'.$addrkoha433. $more_member . $patronid 
	.'"> Click to RETURN to patron account:  '.$lname.' , '.$fname.' </a> </h2></center>';

// close KOHA database
disconnect($conn);

// Open the "local" database
$conn = open_database($koha_db, $local_login, $local_password, $local_ip, $local_name);

// Insert the ILL record into table
$sql = "INSERT INTO listill SET " . 
	"title='$title', " .
	"collection='$collection', " .
	"callno='$callno', " .
	"itype='$itype', " .
	"barcode='$barcode', " .
	"patronid='$patronid'," .
	'name= "'.$fullname.'" ,'.
	" date= CURDATE(), " .
	" time= CURTIME() " ;

// Insert the items into the local ILL database
$result = dbquery($conn, $sql);
echo "<hr><h2><center>ILL stored</center></h2>";

// close local database
disconnect($conn);
?>
