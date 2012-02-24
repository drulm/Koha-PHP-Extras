<?php
require_once('fpdf/fpdf.php');


class ReportPdf extends FPDF {
  /**
  * Adds a header to each page. Called automatically.
  */
  function Header(){
  }

  /**
  * Adds a footer to each page. Called automatically.
  */
  function Footer(){
// 		//Position at 1.5 cm from bottom
// 		$this->SetY(-15);
// 		//Arial italic 8
// 		$this->SetFont('Arial','I',8);
// 		//Page number
// 		$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
	}

  /**
  * Creates the late item notices.
  * @param array $libraryData A hashed array of data about the branch generating notices.
  * @param string $noticePhrase A string added to the top of each page.
  * @param array $noticeData An array of arrays.
  * @param string $message A message to be appended to the end of each notice.
  */
  public function createNotices($libraryData, $noticePhrase, $noticeData, $message){
    foreach ($noticeData as $thisBorrowerOverdues){
	
      $this->createPage($libraryData, $noticePhrase, $thisBorrowerOverdues, $message);
    }
  }

  /**
  * Creates the late item notices.
  * @param array $libraryData A hashed array of data about the branch generating notices.
  * @param string $noticePhrase A string added to the top of each page.
  * @param array $noticeData An array of arrays.
  * @param string $message A message to be appended to the end of each notice.
  */
  protected function createPage($libraryData, $noticePhrase, $thisBorrowerOverdues, $message){
    $this->AddPage();
    //Arial bold 15
    $this->SetFont('Arial','B',14);
    
    $this->Cell(20); //Move to the right
    $this->Cell(30,10,'Stow-Munroe Falls Library',0,0,'C');
	$this->Cell(20);
	$this->Image("koha_new_logo.jpg", 80,10);
    $this->Ln(6);
    $this->Cell(20); //Move to the right
    $this->Cell(30,10,$libraryData['address1'],0,0,'C');
    $this->Cell(80); //Move to the right
    $this->Cell(30,10,"Phone: " . $libraryData['phone'],0,0,'C');
    $this->Ln(6);
    $this->Cell(20); //Move to the right
    $this->Cell(30,10,$libraryData['address2']." ".$libraryData['address3'],0,0,'C');
    $this->Cell(80); //Move to the right
    if ( strlen( $libraryData['fax'] > 0 ) ) 
      $this->Cell(30,10,"Fax: " . $libraryData['fax'],0,0,'C');
    $this->Ln(10);
#    $this->Cell(20); //Move to the right
#    $this->Cell(30,10,$libraryData['address3'],0,0,'C');
#    $this->Ln();
	$this->SetFont('Arial','I',12);
    $this->Cell(70);
    $this->Cell(25,10, $noticePhrase.' **THIS IS A BILL**',0,0,'C');
	$curdate = time();
	$formatdate = date("M d, Y",$curdate);
    $this->Cell(65,10, $formatdate ,0,0,'R');

    $this->Ln(8);
	$this->SetFont('Arial','',12);
    if (strlen($message) > 0)
      $this->Cell(160,10,"Note: " . $message,0,0,'C');
	else
	  $this->Cell(160,10,"*****" . $message,0,0,'C');

	$this->Ln(8);
	$this->Cell(20);
    $this->Cell(130,10,'Library records show there are charges over $25 that have not been paid.',0,0,'C');
	$this->Ln(8);
	$this->Cell(20);
    $this->Cell(130,10,'Please pay this bill as soon as possible. If an item is lost you will either',0,0,'C');
	$this->Ln(8);
	$this->Cell(20);
	$this->Cell(130,10,'owe overdue fees OR a lost charge. If still unpaid 10 days',0,0,'C');
	$this->Ln(8);
	$this->Cell(20);
	$this->Cell(130,10,'after the date of this notice, your account will be sent to Unique',0,0,'C');
	$this->Ln(8);
	$this->Cell(20);
	$this->Cell(130,10,'Management Services for the collections and you will be charged an',0,0,'C');
 	$this->Ln(8);
	$this->Cell(20);
	$this->Cell(130,10,'additional $10. Questions? Please call circulation at (330) 688-3295',0,0,'C');

	$this->Ln(15);
//    $this->createAddress($thisBorrowerOverdues['borrower']->returnMailingAddress());
    $this->createAddress($thisBorrowerOverdues['borrower']);

    $this->Ln(30);
    $this->SetFont('Arial','B',12);
    $this->Cell(0,10,$noticePhrase . " Notice - Charges and Payments are as Follows");
    //$this->Ln();
    $totalcost = $this->createOverdueItemsList($overDues = $thisBorrowerOverdues['itemsDue']);
	$this->Ln();
	$totalcost += 0.000001;
	$this->Cell(143,6,'Total Fees= '.substr($totalcost,0,strpos($totalcost, ".")+3),0,0,'R');
  }

  /**
  * createAddress takes an array with keys of 'name', 'street', and 'cityStateZip',
  * and writes the address line to the pdf.
  * @param array $addressArray A hashed array of data about the address.
  */
  protected function createAddress( $addressArray ) {
    $lineSpacing = 6;
    $this->SetFont('Arial','B',14);
    $this->Cell(160,10,$addressArray['name'],0,0,'R');
    $this->Ln($lineSpacing);
    $this->Cell(160,10,$addressArray['street'],0,0,'R');
    $this->Ln($lineSpacing);
    $this->Cell(160,10,$addressArray['cityStateZip'],0,0,'R');
    $this->Ln();
  }

  /**
  * createOverdueItemsList writes the table of overdue items to the pdf.
  * @param array $overdues Array of overdue items for a given borrower.
  */
  function createOverdueItemsList( $overDues ) {     
//    $header = array('Charge Type', 'Item #', 'Title',  'Fee', 'Date');
    $header = array('Barcode - Title List', 'Fees', 'Type', 'Dates');
    //Colors, line width and bold font
    $this->SetFillColor( 128 );
    $this->SetTextColor( 255 );
    $this->SetDrawColor( 255 );
    $this->SetLineWidth( .3 );
    $this->SetFont( 'Arial', 'B', 10 );

    $this->Ln( 10 );

    //Header
    $w = array(127, 11, 11, 20);
    for ( $i=0; $i < count( $header ); $i++ ) {
      $this->Cell( $w[$i], 7, $header[$i], 1, 0, 'C', 1 );
    }
    $this->Ln();

    //Color and font restoration
    $this->SetFillColor( 224, 235, 255);
    $this->SetTextColor( 0 );
    $this->SetFont( '' );

    //Data
    $fill = 0;
	$totalcost = 0.0;
	
	//Take apart the rows
	//$itemcallnumber_arr = explode("<br>", $overDues[0]['itemcallnumber']);
	//$author_arr = explode("<br>", $overDues[0]['author']);
	//$author is the barcode now!!! (change!) -d.u.
	
	$title_arr = explode("<br>", $overDues[0]['title']);
	$price_arr = explode("<br>", $overDues[0]['price']);
	$date_due_arr = explode("<br>", $overDues[0]['date_due']);
	$type_arr = explode("<br>", $overDues[0]['itemcallnumber']);
	$author_arr = explode("<br>", $overDues[0]['author']);
	//$dispute_arr = explode("<br>", $overDues[0]['dispute']);
	
	$i=0;
	$j=0;
	$k=0;
	$overDues2 = array();
	$payments = array();
	foreach ( $date_due_arr as $row2 )
		{
		if ($price_arr[$k] > 0)
			{
			$overDues2[$i] = array();
			$overDues2[$i]['title'] = $title_arr[$k];
			$overDues2[$i]['price'] = $price_arr[$k];
			$overDues2[$i]['date_due'] = $date_due_arr[$k];
			$overDues2[$i]['itemcallnumber'] = $type_arr[$k];
			$overDues2[$i]['author'] = $author_arr[$k];
			//$overDues2[$i]['dispute'] = $dispute_arr[$k];
			$i++;
			}
		else if ($price_arr[$k] < 0)
			{
			$payments[$j] = array();
			$payments[$j]['title'] = $title_arr[$k];
			$payments[$j]['price'] = $price_arr[$k];
			$payments[$j]['date_due'] = $date_due_arr[$k];
			$payments[$j]['itemcallnumber'] = $type_arr[$k];
			$payments[$i]['author'] = $author_arr[$k];
			//$overDues2[$i]['dispute'] = $dispute_arr[$k];
			$j++;
			}
		$k++;
		}
	
    foreach ( $overDues2 as $row ) {
	  $austr = substr('['.trim(stripslashes($row['author'])).']         ',0,10);
      $this->Cell( $w[0], 4, substr($austr. trim(stripslashes($row['title'])),0,75), 'LR', 0, 'L', $fill );
      $this->Cell( $w[1], 4, substr($row['price'],0,strpos($row['price'], ".")+3), 'LR', 0, 'R', $fill );
	  $this->Cell( $w[2], 4, $row['itemcallnumber'], 'LR', 0, 'C', $fill );
      $this->Cell( $w[3], 4, $row['date_due'], 'LR', 0, 'R', $fill );
	  //$this->Cell( $w[4], 4, $row['dispute'], 'LR', 0, 'C', $fill );

	  $totalcost += (float)$row['price'];
      $this->Ln();
      $fill =! $fill;
    }

	$over = 0;
    foreach ( $payments as $row ) {
	  if ((float)$row['price'] <> 0.0)
		  {
		  $austr = substr('[Payment'.$row['author'].']         ',0,10);
		  $this->Cell( $w[0], 4, substr($austr.$row['title'],0,75), 'LR', 0, 'L', $fill );
		  $this->Cell( $w[1], 4, substr($row['price'],0,strpos($row['price'], ".")+3), 'LR', 0, 'R', $fill );
		  $this->Cell( $w[2], 4, $row['itemcallnumber'], 'LR', 0, 'C', $fill );
		  $this->Cell( $w[3], 4, $row['date_due'], 'LR', 0, 'R', $fill );
	      //$this->Cell( $w[4], 4, $row['dispute'], 'LR', 0, 'C', $fill );
		  $totalcost += (float)$row['price'];
		  $this->Ln();
		  $fill =! $fill;
		  $over++;
		  if ($over >= $j) break;
		  }
    }

    $this->Cell( array_sum( $w ), 0, '', 'T' );
  
  
  return $totalcost;
  }

}
?>
