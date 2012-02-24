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
?>
<html>
<head>
<title>KOHA Shelf List</title>
<?php include("dateselector.htm"); ?>
<style type="text/css">
<!--
.style1 {font-weight: bold}
-->
</style>
</head>

<?php
// Set this to your Koha staff intranet base address
require_once('addrkoha.php');
// Set this to your Koha staff intranet base address
require_once("kohafunctions.php");

echo '
  <h2 align="left" class="style8"><a href="koha_rep.htm"> </a><a href="'.$addrkoha433.'">Koha Intranet</a> | <a href="'.$addrkoha433.'cgi-bin/koha/reports-home.pl">Reports</a> </h2>
  ';
// Open the database
$conn = open_database($koha_db, $koha_login, $koha_password, $koha_ip, $koha_name);
// Get Authoried Values
$values = array(); $reptext = array();
get_authorised_values($conn, $status_collection, $values, $reptext);
?>

<center>
  <form action="shelf_get.php" name="myform" method="POST">
  <h2 align="center">Shelf Lists / Weeding Lists / HTML Lists</h2>
  <p align="center">Combine any of the search critera below, On the results page, click title for BIB and barcode for the ITEM/Status.</p>
  <table border="1" align="center" >
    <tr bordercolor="#000000">
      <td width="31%"><p class="style1">Collection Codes
        <input type="submit" value="SUBMIT SEARCH" />
      </p>
        <p>
          <input name="images" type="checkbox" id="images" value="1" />
          <strong>Images </strong>(slower)</p>
        <p>You may select multiples by holding [Ctrl] key </p></td>
      <td width="32%" bgcolor="#FFFFFF"><font size="+3">
	
	<select name="collection[]" MULTIPLE size="8">  
		<option selected="selected" value="%">*ALL* CODES</option>
<?php
// Get the lost options!
$i = 0;
foreach ($reptext as $rep)
	{
	$replace = explode("<br>", $values[$i++]);
	echo "<option value=".$replace[0].">".$replace[0]."</option>";
	}
?>
</select>

</font></td>
      <td width="37%" bgcolor="#FFFFFF"><p><input type="checkbox" name="excel" value="1"> 
		  <strong>Export to Excel</strong></p>
        <p><strong>or</strong></p>
        <p>
		    <input name="htmllist" type="checkbox" id="htmllist" value="1" />
            <strong>Html-List / </strong> Columns <font size="+3">
            <input name="scol" type="text" id="scol" value="3" size="3" maxlength="3" />
            </font>        </p>
        
          <p><strong><font size="+3">
		    </font></strong>Reset fields &gt; <strong><font size="+3">
		    <input type="reset" value="CLEAR FORM" style="background-color:#881111; color: #ffffff;" onClick="window.location.reload()" />
		    </font></strong><br>
	    </p></td>
    </tr>
    <tr bordercolor="#000000">
      <td><p><strong>Title</strong></p>
        </td>
      <td bgcolor="#FFFFFF"><font size="+3">
        <input name="stitle" type="text" id="stitle" size="30" maxlength="30" />
</font>title</td>
      <td bgcolor="#FFFFFF"><div align="center">
        <p>Rows <font size="+3">            <input name="showrows" type="text" id="showrows" value="100" size="12" maxlength="12" />
          </font>(max=all)</p>
        </div></td>
    </tr>
    <tr bordercolor="#000000">
      <td><strong>Call# Range</strong></td>
      <td bgcolor="#FFFFFF"><font size="+3">
        <input name="callstart" type="text" size="30" maxlength="30" />
</font>start call<font size="+3">&nbsp; </font><font size="+3">&nbsp;
        </font></td>
      <td bgcolor="#FFFFFF"><font size="+3">
        <input name="callend" type="text" size="30" maxlength="30" />
</font>end call <font size="+3">&nbsp;
        </font></td>
    </tr>
    <tr bordercolor="#000000">
      <td><strong>Full Marc Text Search<br />(i.e. subjects/series/etc.)</strong> <br />(slower - use with care) </td>
      <td bgcolor="#FFFFFF"><font size="+3">
        <input name="stext" type="text" id="stext" size="30" maxlength="30" />
      </font>text</td>
      <td bgcolor="#FFFFFF"><font size="+3">
        <input name="smarc" type="text" id="smarc" value="650" size="5" maxlength="5" />
      </font>special marc field to include </td>
    </tr>
    <tr bordercolor="#000000">
      <td><strong>Circ'ed less than </strong></td>
      <td bgcolor="#FFFFFF"><font size="+3">
        <input name="circcount" type="text" value="max" size="7" maxlength="7" />
</font><strong>&gt;=circ'ed for weeding</strong> max=all</td>
      <td bgcolor="#FFFFFF"><div align="center"><font size="+3">
          <select name="sortfield" size="1" id="sortfield">
              <option value="items.location" selected>Location / Call</option>
              <option value="title, author">Title</option>
              <option value="author, title">Author</option>
              <option value="datelastseen">Date Last Seen</option>
              <option value="items.dateaccessioned">Date Added</option>
              <option value="items.location DESC, items.itemcallnumber DESC">Location / Call DESC</option>
              <option value="title DESC, author DESC">Title DESC</option>
              <option value="author DESC, title DESC">Author DESC</option>
              <option value="datelastseen DESC">Date Last Seen DESC</option>
              <option value="items.dateaccessioned DESC">Date Added DESC</option>
            </select>
      </font>sort by </div></td>
    </tr>
    <tr align="left" valign="top" bordercolor="#000000" bgcolor="#94E2D7">
      <td height="326" bgcolor="#FFFFFF"><p><strong>Acqui. Start</strong><strong><font size="+3">
        <input onClick="ds_sh(this);" name="startdate" readonly="readonly" style="cursor: text" />
      </font></strong></p>
        <p><strong>
            </strong><font size="+3"><br />
        </font> </p>        </td>
      <td height="326" bgcolor="#FFFFFF"><p><strong>Acqui. End</strong><font size="+3">
      <input name="enddate" id="enddate" style="cursor: text" onClick="ds_sh(this);" readonly="readonly" />
      </font></p>
        </td>
      <td height="326" bgcolor="#FFFFFF"><p><strong>Last Seen Date </strong><font size="+3">
      <input name="lastseen" id="lastseen" style="cursor: text" onClick="ds_sh(this);" readonly="readonly" />
      </font>        </p>
        </td>
    </tr>
  </table>
  </form>
</center>

</body>
</html>
