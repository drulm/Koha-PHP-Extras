<?php
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

// Do not change, this version works for 3 and not also for 2, use the previous version for 2
// A combined version is being worked on
$koha_version = 3;

// CHANGE ME!
// Set this to your Koha staff intranet base address
$addrkoha433 = "http://yourname.org:8080";

//--------------------------------------------------------Main Koha Server

// If running postgres -> CHANGE ME!
// Set this to your Koha IP Database Type
$koha_db = "mysql";

// CHANGE ME!
// Set this to your Koha IP Address
$koha_ip = "192.168.0.100";

// CHANGE ME!
// Set this to your Koha MySQL login id
$koha_login = "yourlogin";

// CHANGE ME!
// Set this to your Koha MySQL login password
$koha_password = "yourpassword";

// CHANGE ME! (if DB name is not koha)
// Set this to your Koha MySQL database name
$koha_name = "kohadb";

// CHANGE ME! (perhaps), set to "" for no CSS. If incorrect, will run slowly!
// CSS
//$koha_intranet_css = "/intranet-tmpl/npl/en/includes/intranet.css";
//$koha_intranet_colors = "/intranet-tmpl/npl/en/includes/colors.css";
$koha_intranet_css = "/intranet-tmpl/prog/en/css/staff-global.css";
$koha_intranet_colors = "";

// CHANGE ME! Set to 1 only if setting up local database!
// LOCALDATABASE for alternate ILL, alternate holidays, and special labels for each spine label
//--------------------------------------------------------Local Koha Database
// Set to 1 if you want to use the KOHAEXTRAS local database, set to 0 otherwise
$local_db = 1;
//$local_db = 0;

// CHANGE ME!
// Set this to your local IP Address for KOHAEXTRAS local database
$local_ip = "127.0.0.1";

// CHANGE ME!
// Set this to your local KOHAEXTRAS MySQL login id
$local_login = "locallogin";

// CHANGE ME!
// Set this to your local KOHAEXTRAS MySQL login password
$local_password = "localdb";

// CHANGE ME!
// Set this to your Koha MySQL database name
$local_name = "ill";

// This is for future multi-branch code if any
$multi_branch = 0;

// CHANGE ME!
// BORROWERS (this option may not be in use, checking)
//--------------------------------------------------------Borrower Types
$borrowerTypes = array('PA','YV','TE','NV','ST','IL','OR');
$smfpl_yes = 1;

// CHANGE ME!
// RESERVES
//--------------------------------------------------------SQL to exclude from reserve pull list
// Incomment one and follow pattern, or to be safe just uncomment the blank one.
#$exclude_from_reserve = '';
// SQL to state that these items are not included for daily hold retrieval list
$exclude_from_reserve = ' AND itype <> "JREF" AND itype <> "REF" AND itype <> "GENH" AND itype <> "EXPR" AND itype <> "GAME" AND itype <> "ICD" AND itype <> "IVT" AND itype <> "IGE"';
$exclude_from_reserve2 = ' AND barcode NOT LIKE "%order%" ';
$periodical_special = "PER";		// What is the periodical ITYPE code, this can be useful so set
$reserve_regular_hold_days = 8;		// Hold onto how long before we cancel, regular hold time
$reserve_short_hold_type = "DVD";	// Give an ITYPE that has a short hold 
$reserve_short_hold_days = 4;		// How long do I hold the short hold UTYPE
$reserve_alert_ratio = 10;			// For reserve alert, the number of reserves per item
$reserve_alert_ratio_dvd = 20;		// For reserve alert, the number of reserves per item
$held_over_days = 60;				// Number of days reserve/hold is over to be on hold over alert list

// CHANGE ME! (overdues may have a bug, checking)
//-------------------- Overdue Settings
$overdue_start = 21;				// Days ago range start (ex. 14 days over due, 21 generates a notice)
$overdue_end = 21;					// Days ago range end
$overdue_special_items = 'DVD NDVD NFDV NNDV';   // These are ITYPES that have a different setting that start/end
$overdue_special_daysago = 7;		// For the above items generate a report after ex. 7 days

// CHANGE ME!
//-------------------- Bill Settings
$bill_start = 50;					// Money overdue how many days start (end should be same usually)
$bill_end = 50;						// Money overdue how many days ending
$bill_currency_cutoff = 25;			// How much currency owed until bill generated?

// CHANGE ME! MANY SCREENS WILL NOT WORK UNLESS THESE ARE SET, find the setting in admin -> auth values
//--------------------------------------------------------Status categories, look in SQL Authorised Values
//----- You need to look all of these up in your database to see what they are!!!!
//$status_lost = "ITEMLOST";			// For status search, LOST item category code
$status_lost = "LOST";			// For status search, LOST item category code
//$status_damaged = "BINDING";		// Damaged or binding category code
$status_damaged = "DAMAGED";		// Damaged or binding category code
//$status_avail = "NOTFORLOAN";		// Not for loan category code
$status_avail = "NOT_LOAN";		// Not for loan category code
//$status_collection = "Shelf_Loc";	// Collection/Location Code category code
$status_collection = "LOC";			// Collection/Location Code category code

// CHANGE ME!
//----------------------Patron age verification for computer use
$patronage_full = 0;				// set this to 1 for more information

// CHANGE ME!
//--------------------------------------------------------Local Holiday Management On=1 Off=0
$local_holiday = 1;
$week = array(1,0,0,0,0,0,1); 						// days of week...1 means "holiday" (normal)
$phoneweek = array(1,0,0,0,0,0,0); 					// For calling report only (phone)
$cancelled_reserve_week = array(0,0,0,0,0,0,0); 	// Reserves Calling Holidays
$email_hold_week = array(0,0,0,0,0,0,0); 			// Email Hold List Holidays

// CHANGE ME! IMPORTANT, MANY OPTIONS WILL NOT WORK UNLESS THESE ARE SET FOR ILL
//--------------------------------------------------------ILL Categories
//If you are using this, change your ILL categories to what you need. Leave in <option ...> </option>
$ill_categories = 	"<option selected>IGE</option>
            		<option>ICD</option>
            		<option>IVT</option>";
$ill_collections =	"<option selected>ILLLOCAL</option>
        			<option>ILLOCLC</option>
        			<option>ILLMORE</option>
        			<option>ILLNEO</option>";

// Leave everything below alone, in progress for Koha 2
//---------------------------------------------------------Link Prefix or Postfix
// KOHA 3
if ($koha_version == 3)
	{
	$bib_detail = "cgi-bin/koha/catalogue/detail.pl?biblionumber=";
	$edit_bib = "cgi-bin/koha/cataloguing/addbiblio.pl?biblionumber=";
	$edit_item = "cgi-bin/koha/cataloguing/additem.pl?biblionumber=";
	$view_marc = "cgi-bin/koha/catalogue/MARCdetail.pl?biblionumber=";
	$reportpage = "cgi-bin/koha/reports/reports-home.pl";
	$search_koha = "cgi-bin/koha/catalogue/search.pl?q=";
	$more_detail = "cgi-bin/koha/catalogue/moredetail.pl?biblionumber=";
	$aqui_edit1 = "cgi-bin/koha/acqui/neworderempty.pl?ordnum=";
	$aqui_edit2 = "&booksellerid=";
	$aqui_edit3 = "&basketno=";
	$more_member = "cgi-bin/koha/members/moremember.pl?borrowernumber=";
	$aqui_budget_link = "cgi-bin/koha/admin/aqbudget.pl?op=add_form&aqbudgetid=";
	$aqui_basket_link = "cgi-bin/koha/acqui/basket.pl?basketno=";
	$aqui_receive_link = "cgi-bin/koha/acqui/parcels.pl?supplierid=";
	}
// KOHA 2
else
	{
	$bib_detail = "cgi-bin/koha/detail.pl?bib=";
	$edit_bib = "cgi-bin/koha/acqui.simple/addbiblio.pl?oldbiblionumber=";
	$edit_item = "cgi-bin/koha/acqui.simple/additem.pl?bibid=";
	$view_marc = "cgi-bin/koha/MARCdetail.pl?bib=";
	$reportpage = "cgi-bin/koha/reports-home.pl";
	$search_koha = "search?q=";
	$more_detail = "cgi-bin/koha/detail.pl?bib=";
	$aqui_edit1 = "cgi-bin/koha/acqui/newbiblio.pl?ordnum=";
	$aqui_edit2 = "&booksellerid=";
	$aqui_edit3 = "&basketno=";
	$more_member = "cgi-bin/koha/members/moremember.pl?bornum=";
	}
?>
