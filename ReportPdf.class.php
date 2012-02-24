<?php
## Copyright 2006 Kyle Hall

## Modified by Darrell Ulm 2008

## This file is part of koha-tools.

## koha-tools is free software; you can redistribute it and/or modify
## it under the terms of the GNU General Public License as published by
## the Free Software Foundation; either version 2 of the License, or
## (at your option) any later version.

## koha-tools is distributed in the hope that it will be useful,
## but WITHOUT ANY WARRANTY; without even the implied warranty of
## MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
## GNU General Public License for more details.

## You should have received a copy of the GNU General Public License
## along with koha-tools; if not, write to the Free Software
## Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

/**
  * This files contains the FPDF child class ReportPdf
  * This class generates pdf files of patron fines to print and mail.
  * @package koha-tools
  * @subpackage koha-reports
  * @author Kyle Hall
  * @copyright 2006
  */

require_once('fpdf/fpdf.php');
ini_set("memory_limit","256M");

/**
  * This class generates pdf files of patron fines to print and mail.
  * @package koha-tools
  * @subpackage koha-reports
  * @author Kyle Hall
  * @copyright 2006
  */
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
    $this->Cell(30,10,$libraryData['address2'].', 44224',0,0,'C');
    $this->Cell(80); //Move to the right
    if ( strlen( $libraryData['fax'] > 0 ) ) 
      $this->Cell(30,10,"Fax: " . $libraryData['fax'],0,0,'C');
    $this->Ln(20);
#    $this->Cell(20); //Move to the right
#    $this->Cell(30,10,$libraryData['address3'],0,0,'C');
#    $this->Ln();
	$this->SetFont('Arial','I',12);
    $this->Cell(70);
    $this->Cell(25,10, $noticePhrase.' NOTICE',0,0,'C');
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
    $this->Cell(130,10,'* THIS IS THE ONLY NOTICE YOU WILL RECEIVE *',0,0,'C');
	$this->Ln(8);
	$this->Cell(20);
    $this->Cell(130,10,'Library records indicate that the items shown below are overdue',0,0,'C');
	$this->Ln(8);
	$this->Cell(20);	
	$this->Cell(130,10,'If you have returned them, please excuse this notice',0,0,'C');
 	$this->Ln(8);
	$this->Cell(20);
	$this->Cell(130,10,'Questions? Please call circulation at (330) 688-3295',0,0,'C');

	$this->Ln(25);
	
    $this->createAddress($thisBorrowerOverdues['borrower']);

    $this->Ln(30);
    $this->SetFont('Arial','B',16);
    $this->Cell(0,0,$noticePhrase . " Notice - Please Return the Following Overdue Items");
    $this->Ln();
    $this->createOverdueItemsList($overDues = $thisBorrowerOverdues['itemsDue']);
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
    $header = array('Loc/Call', 'Author', 'Title', 'Barcode', 'Value', 'Date Due');
    //Colors, line width and bold font
    $this->SetFillColor( 128 );
    $this->SetTextColor( 255 );
    $this->SetDrawColor( 255 );
    $this->SetLineWidth( .3 );
    $this->SetFont( 'Arial', 'B', 10 );

    $this->Ln( 10 );

    //Header
    $w = array( 35, 35, 62, 18, 15, 20 );
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
    foreach ( $overDues as $row ) {
      $this->Cell( $w[0], 6, substr($row['location'].'/'.$row['itemcallnumber'],0,15), 'LR', 0, 'L', $fill );
      $this->Cell( $w[1], 6, substr($row['author'],0,18), 'LR', 0, 'L', $fill );
      $this->Cell( $w[2], 6, substr($row['title'],0,36), 'LR', 0, 'L', $fill );
	  $this->Cell( $w[3], 6, substr($row['barcode'],7,7), 'LR', 0, 'L', $fill );
      $this->Cell( $w[4], 6, $row['price'], 'LR', 0, 'L', $fill );
      $this->Cell( $w[5], 6, $row['date_due'], 'LR', 0, 'L', $fill );

      $this->Ln();
      $fill =! $fill;
    }
    $this->Cell( array_sum( $w ), 0, '', 'T' );
  }
}
?>