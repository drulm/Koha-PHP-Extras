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
?>
<html>
<head>
<title>KOHA New Emails</title>
<?php include("dateselector.htm"); ?>
</head>

<body>
<center>
<?php
// Set this to your Koha staff intranet base address
require_once('addrkoha.php');
echo '
  <h2 align="left" class="style8"><a href="koha_rep.htm"> </a><a href="'.$addrkoha433.'">Koha Intranet</a> | <a href="'.$addrkoha433.'cgi-bin/koha/reports-home.pl">Reports</a> </h2>
  ';
?>
<form action="new_emails_get.php" name="myform" method="POST">
  <h2 align="center">Pull all new paton emails past a selected date </h2>
  <p align="center">Select your date and click submit. <em>Default is yesterday. </em>
    </p>
  <table align="center" >
    <tr>
      <td width="50%" height="40">
        <div align="center">
          <h2>
            <input type="submit" value="SUBMIT SEARCH" />
          </h2>
        </div></td>
      <td width="50%"><p align="center">
		    <strong>Reset date&gt;<font size="+3"></font></strong><font size="+3"><strong>
		    <input type="reset" value="CLEAR FORM" onClick="window.location.reload()" />
            </strong></font><br>
	    </p></td>
    </tr>
    <tr align="left" valign="top">
      <td height="193" colspan="2"><h2 align="center"><strong>  Date before present </strong></h2>
        <p align="center"><strong><font size="+3">        
          <input name="startdate" style="cursor: text" onClick="ds_sh(this);" 
		  <?php
		  $curdate = time()-86400;
		  echo "value=".date("Y-m-d",$curdate); 
		  ?>
		  readonly="readonly" />
        </font></strong></p>        <p align="center"><strong>
            </strong><font size="+3"><br />
        </font> </p>        <h2 align="center"><font size="+3">        </font></h2></td>
      </tr>
  </table>
  </form>
</center>

</body>
</html>
