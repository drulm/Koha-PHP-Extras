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
<title>KOHA Acquisitions</title>
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
<form action="acqreport.php" name="myform" method="POST">
  <h2 align="center">Acquisitions Reports By Budgets </h2>
  <p align="center"><em>Select your date ranges below for your  stats.</em></p>
  <table width="59%" align="center" >
    <tr>
      <td width="48%" height="40">
        <div align="center">
          <h2>
            <input type="submit" value="Submit Date Search" />
          </h2>
        </div></td>
      <td width="52%"><p align="center">
		    <strong>Reset dates &gt;<font size="+3"></font></strong><font size="+3"><strong>
		    <input type="reset" value="Clear Date Form" onClick="window.location.reload()" />
            </strong></font><br>
	    </p></td>
    </tr>
    <tr align="center" valign="top">
      <td><p><strong>Start Date:</strong><strong>
            <input name="startdate" style="cursor: text" onClick="ds_sh(this);" 
		  <?php
		  $curdate = time()-86400;
		  echo "value=".date(date("Y")."-1-1"); 
		  ?>
		  readonly="readonly" />
      </strong></p>
        <p><em>default is first day of current year</em><br />
        </font> </p></td>
      <td height="79"><p><strong>End Date:</strong>          <input name="enddate" id="enddate" style="cursor: text" onClick="ds_sh(this);" 
		  <?php
		  $curdate = time()-86400;
		  echo "value=".date("Y-m-d",$curdate); 
		  ?>
		  readonly="readonly" />
      </p>
        <p>          <em>default is yesterday </em></p>
        </td>
    </tr>
  </table>
  </form>

<form action="aqinvoice.php" name="myform2" method="GET">
  <h2>Invoice Search </h2>
  <table width="59%" align="center" >
    <tr align="center" valign="top">
      <td width="48%" height="45"><p><strong>Invoice No.</strong><strong>
           <input name="invoice" type="text" id="invoice" size="30" />
             <input type="submit" value="Submit Invoice Search" />
      </strong><br />
        </font> </p>
        </td>
      </tr>
  </table>
  </form>  

</center>

</body>
</html>
