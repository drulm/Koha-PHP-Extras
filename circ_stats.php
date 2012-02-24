<?php
## Circ Stats Get _ the HTML part of the code (mostly!)

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

// Open the database
$conn = open_database($koha_db, $koha_login, $koha_password, $koha_ip, $koha_name);
?>

<html>
<head>
<title>KOHA Circ Stats</title>
<?php include("dateselector.htm"); ?>
</head>

<body>
<center>
<form action="circ_stats_get.php" name="myform" method="POST">
  <h2 align="center">Circlation Statistics</h2>
  <p align="center">Select your date ranges below for your circ stats. <em>Default is yesterday. </em>
    </p>
  <table width="50%" height="367" border="0" align="center" bordercolor="#666666" bgcolor="#FFFFFF" >
    <tr>
      <td height="69" bgcolor="#FFFFFF">
        <div align="center">
          <p>
            <input type="submit" value="SUBMIT SEARCH" />
      </p>
          <p>
          <input type="reset" value="CLEAR FORM" onclick="window.location.reload()" />
          <br>
	    </p>
        </div>        </td>
      <td height="69" >
	   <!--
	  <p><strong>Library Branch: </strong>                 
            </p>
              <p>
			
                <select name="library">
					 <?php
					$values2 = array(); $lib2= array();
					get_branches($conn, $values2, $lib2);
					$i = 0;
					foreach ($values2 as $brcode)
						{
						echo '<option selected="selected" value="'.$brcode.'">'.$lib2[$i].'</option>';
						$i++;
						}
					?>
                </select>
			-->
	  </td>
    </tr>
    <tr align="left" valign="top" bgcolor="#94E2D7">
      <td width="48%" height="193" bgcolor="#FFFFFF"><p align="center"><strong> 1. Pick Start Date</strong></p>
        <p align="center"> 
          <input name="startdate" style="cursor: text" onclick="ds_sh(this);" 
		  <?php
		  $curdate = time()-86400;
		  echo "value=".date("Y-m-d",$curdate); 
		  ?>
		  readonly="readonly" />
          </p>        <p align="center">
           <br />
           </p>        </td>
      <td width="52%" height="193" bgcolor="#FFFFFF"><p align="center"><strong>2. End Date</strong></p>
        <p align="center">
          <input name="enddate" id="enddate" style="cursor: text" onclick="ds_sh(this);" 
		  <?php
		  $curdate = time()-86400;
		  echo "value=".date("Y-m-d",$curdate); 
		  ?>
		  readonly="readonly" />
        </p></td>
    </tr>
  </table>
  </form>
</center>

<?php
// close database
disconnect($conn);
?>
</body>
</html>
