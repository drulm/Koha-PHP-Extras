rem	$datesub		= $argv[16];
rem	$collection		= $argv[1];
rem	$callstart		= $argv[2];
rem	$callend		= $argv[3];
rem	$circcount		= $argv[4];
rem	$startdate		= $argv[5];
rem	$enddate		= $argv[6];
rem	if ($startdate == 0) 
rem		{ // get one month (-30)
rem		$enddate = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d"), date("Y")));
rem		$startdate   = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")-$datesub, date("Y")));
rem		}
rem	$showrows		= $argv[7]; 
rem	$excel			= $argv[8];
rem	$htmllist		= $argv[9];
rem	$images			= $argv[10];
rem	$stitle			= $argv[11];
rem	$scol			= $argv[12];
rem	$sendout		= $argv[13];
rem	$filesave 		= $argv[14];
rem	$modselect		= $argv[15];
rem	$datesub		= $argv[16];    //days ago!
rem	$doctitle		= $argv[17];
echo "running BOOK LIST PHP Script"

rem special
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" %% "" "" "1" 0 0 50 0 1 1 "" 4 1 "c:\overlooked.php" 337 1925 "Items you may have missed"
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" %% "on order" "on order" "max" 0 0 50 0 1 1 "" 4 1 "c:\booklist.php" 29 30 "On Order Items"


rem non-fiction
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" %% "000" "100" "max" 0 0 50 0 1 1 "" 4 1 "c:\000_100list.php" 13 600 "000  Computer science, information, and general works"
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" %% "100" "200" "max" 0 0 50 0 1 1 "" 4 1 "c:\100_200list.php" 13 600 "100  Philosophy and psychology"
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" %% "200" "300" "max" 0 0 50 0 1 1 "" 4 1 "c:\200_300list.php" 13 600 "200  Religion"
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" %% "300" "400" "max" 0 0 50 0 1 1 "" 4 1 "c:\300_400list.php" 13 600 "300  Social sciences"
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" %% "400" "500" "max" 0 0 50 0 1 1 "" 4 1 "c:\400_500list.php" 13 600 "400  Language"
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" %% "500" "600" "max" 0 0 50 0 1 1 "" 4 1 "c:\500_600list.php" 13 600 "500  Science"
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" %% "600" "700" "max" 0 0 50 0 1 1 "" 4 1 "c:\600_700list.php" 13 600 "600  Technology"
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" %% "700" "800" "max" 0 0 50 0 1 1 "" 4 1 "c:\700_800list.php" 13 600 "700  Arts and recreation"
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" %% "800" "900" "max" 0 0 50 0 1 1 "" 4 1 "c:\800_900list.php" 13 600 "800  Literature"
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" %% "900" "999zzzzz" "max" 0 0 50 0 1 1 "" 4 1 "c:\900_999list.php" 13 600 "900  History and geography"

rem fiction
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "MYS" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\mys_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "FIC" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\fic_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "FANT" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\fant_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "ROMAPB" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\romapb_list.php" 13 3650 ""

rem other collection codes
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "AGN" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\AGN_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "BBOCD" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\BBOCD_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "BBOT" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\BBOT_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "BIO" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\BIO_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "BOCD" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\BOCD_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "BOCDNF" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\BOCDNF_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "CA" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\CA_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "CAREER" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\CAREER_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "CD" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\CD_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "CLPB" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\CLPB_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "CX" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\CX_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "DVD" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\DVD_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "EXPR" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\EXPR_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "FANTPB" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\FANTPB_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "EXPRNF" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\EXPRNF_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "INSP" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\INSP_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "JA" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\JA_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "JBD" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\JBD_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "JBIO" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\JBIO_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "JCAL" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\JCAL_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "JCP" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\JCP_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "JE" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\JE_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "JEA" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\JEA_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "JEPB" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\JEPB_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "JF" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\JF_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "JGN" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\JGN_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "JNEWBIO" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\JNEWBIO_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "JNEWFIC" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\JNEWFIC_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "JNEWJE" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\JNEWJE_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "JNEWJP" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\JNEWJP_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "JNEWNF" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\JNEWNF_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "JNEWPSFIC" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\JNEWPSFIC_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "JNF" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\JNF_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "JNFA" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\JNFA_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "JP" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\JP_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "JPA" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\JPA_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "JPB" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\JPB_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "JPBS" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\JPBS_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "JPT" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\JPT_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "JRD" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\JRD_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "JRR" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\JRR_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "JTD" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\JTD_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "LPBIO" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\LPBIO_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "LPFIC" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\LPFIC_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "LPNF" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\LPNF_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "MYSPB" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\MYSPB_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "NEWFANT" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\NEWFANT_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "NEWFIC" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\NEWFIC_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "NEWINSP" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\NEWINSP_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "NEWLP" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\NEWLP_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "NEWMYS" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\NEWMYS_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "NEWNF" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\NEWNF_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "NEWYAFIC" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\NEWYAFIC_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "NEWYANF" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\NEWYANF_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "NF" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\NF_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "ORD" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\ORD_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "PB" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\PB_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "SFPB" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\SFPB_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "NEWSF" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\NEWSF_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "WEST" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\WEST_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "WESTPB" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\WESTPB_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "YAAV" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\YAAV_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "YAFIC" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\YAFIC_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "YANF" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\YANF_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "YAPB" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\YAPB_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "YAPBS" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\YAPBS_list.php" 13 3650 ""
"C:\Program Files\xampp\xampp\php\php" -f "C:\Program Files\xampp\xampp\htdocs\shelf_get.php" "YGN" "" "" "max" 0 0 8 0 1 1 "" 4 1 "c:\YGN_list.php" 13 3650 ""

