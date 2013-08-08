<?php
// $Id: print.tpl.php,v 1.8.2.17 2010/08/18 00:33:34 jcnventura Exp $

/**
 * @file
 * Default print module template
 *
 * @ingroup print
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php print $print['language']; ?>" xml:lang="<?php print $print['language']; ?>">
  <head>
    <?php print $print['head']; ?>
    <?php print $print['base_href']; ?>
    <title><?php print $print['title']; ?></title>
    <?php print $print['scripts']; ?>
    <?php print $print['sendtoprinter']; ?>
    <?php print $print['robots_meta']; ?>
    <?php print $print['favicon']; ?>
    <?php print $print['css']; ?>
  </head>
  <body>
    <?php if (!empty($print['message'])) {
      print '<div class="print-message">'. $print['message'] .'</div><p />';
    } ?>
    <div class="print-logo"><?php print $print['logo']; ?></div>
    <div class="print-site_name"><?php print $print['site_name']; ?></div>
    <p />
    <div class="print-breadcrumb"><?php print $print['breadcrumb']; ?></div>
    <hr class="print-hr" />
    <h1 class="print-title"><?php print $print['title']; ?></h1>
    <div class="print-submitted"><?php print $print['submitted']; ?></div>
    <div class="print-created"><?php print $print['created']; ?></div>
    <div class="print-content"><?php print $print['content']; ?></div>
    <div class="print-taxonomy"><?php print $print['taxonomy']; ?></div>
    <div class="print-footer"><?php print $print['footer_message']; ?></div>
    <hr class="print-hr" />
    <div class="print-source_url"><?php print $print['source_url']; ?></div>
    <div class="print-links"><?php print $print['pfp_links']; ?></div>
  </body>
</html>

<?php

function print_pdf_tcpdf_header($pdf, $html, $font) { }
function print_pdf_tcpdf_page($pdf) { }
function print_pdf_tcpdf_content($pdf, $html, $font) { }
function print_pdf_tcpdf_footer($pdf, $html, $font) { }

function print_pdf_tcpdf_footer2($pdf) {
  $o = '';
  $title = t('Invoice', array(), $language);
  $complete_title = $title .' '. $myorg->title .' : '. $organization->title .' - '. $node->number;

  $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true); 
  $pdf->SetCreator(PDF_CREATOR);
  $pdf->SetAuthor("hawthorn - New Media");
  $pdf->SetTitle($complete_title);
  $pdf->SetSubject($title);
  $pdf->SetKeywords($title, $myorg->title, $organization->title, $node->number);
  $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, 10);
  $pdf->setPrintHeader(false);
  $pdf->setPrintFooter(false);
  $margins = $pdf->getMargins();
  //$pageWidth = $pdf->getPageWidth() - $margins['left'] - $margins['right'];
  // width of area for hawthorn billing - 110mm
  $pageWidth = 110;
  // margin on left side - 40mm
  $pdf_margin_left = 40;
  //$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
  $pdf->SetAutoPageBreak(TRUE, 10);
  $pdf->AddPage();
  $pdf->setDrawColor(215, 223, 226);
  $pdf->setFillColor(215, 223, 226);

  //$pdf->SetFont("oranda_btb", "B", 14);
  //$headerleft = variable_get('site_name', '') .'<br />'. variable_get('site_slogan', '');
  //$pdf->writeHTMLCell($pageWidth *.5, 0, $pdf->getX(), $pdf->getY(), $headerleft, 0 ,0 ,0, false, 'L');
  // hawthorn logo
  $pdf->ImageEps($file=$path.'images/hawthorn.ai', $x=10, $y=15, $w=0, $h=0, $link='', $useBoundingBox=true, $align='', $palign='', $border=0, $fitonpage=false);
  // write header
  $pdf->SetFont("oranda_btb", "B", 18);
  $o = $title;
  if ($language1) $o .= "\n" . t('Invoice', array(), $language1);
  $pdf->SetXY($pdf_margin_left, 21);
  $pdf->Cell(0, 0, $o, 0, false, 'L', 0, '', 0, false, 'M', 'M');
  
  // write footer
  $pdf->SetXY(0, 0);
  $pdf->ImageEps($file=$path.'images/hawthorn2.ai', $x=10, $y=250, $w=0, $h=0, $link='', $useBoundingBox=true, $align='', $palign='', $border=0, $fitonpage=false);
  
  // write address and account information @ 174mm
  $x = 160;
  $pdf->SetFont("oranda_bt", "N", 8);
  $pdf->SetXY($x, 21);
  $o =  $myorg->title ."\n";
  $o .= $myorg->address ."\n";
  if ($myorg->country == 'DE') {
    if ($myorg->zip) $o .= $myorg->zip .' ';
    $o .= $myorg->city ."\n";
  } else {
    $o .= $myorg->city ."\n";
    if ($myorg->zip) $o .= $myorg->zip .' ';
  }
  if ($myorg->provstate) $o .= $myorg->provstate .' ';
  $o .= t($countries[$myorg->country], array(), $language) ."\n";
  $o .= "\n";
  if ($myorg->phone) $o .= $myorg->phone . "\n\n";
  $o .= "www.hawthorn.de";
  if ($myorg->email) $o .= "\n" . $myorg->email;
  $pdf->MultiCell(0, 0, $o, 0, 'L' ,0, 1, $x, $pdf->getY(), true, 0, false, true, 0, 'B');
  
  if (isset($myorg->bank_info) && $myorg->bank_info) {
    $pdf->SetFont("oranda_bti", "I", 8);
    $o = "\n". t('Bank information', array(), $language);
    $pdf->MultiCell(0, 0, $o, 0, 'L' ,0, 1, $x, $pdf->getY(), true, 0, false, true, 0, 'B');
    $pdf->SetFont("oranda_bt", "N", 8);
    $o = $myorg->bank_info;
    $pdf->MultiCell(0, 0, $o, 0, 'L' ,0, 1, $x, $pdf->getY()-2, true, 0, false, true, 0, 'B');
  }
  if  ($myorg->taxid) {
    $pdf->SetFont("oranda_bti", "I", 8);
    $o = "\n". t('Tax ID', array(), $language);
    $pdf->MultiCell(0, 0, $o, 0, 'L' ,0, 1, $x, $pdf->getY(), true, 0, false, true, 0, 'B');
    $pdf->SetFont("oranda_bt", "N", 8);
    $o = $myorg->taxid;
    $pdf->MultiCell(0, 0, $o, 0, 'L' ,0, 1, $x, $pdf->getY()-2, true, 0, false, true, 0, 'B');
  }
    
  $pdf->SetXY($pdf_margin_left, 25);
  $pdf->SetFont("oranda_bt", "N", 8);
  $pdf->writeHTMLCell($pageWidth *.5, 0, $pdf->getX(), $pdf->getY(), variable_get('storm_report_header', ''), 0 ,1 ,0, false, 'R');
  //$pdf->MultiCell(0, 0, $o, 0 ,'C', 0, 1,$pdf->getX(), $pdf->getY() + 10);
  
  $pdf->SetXY($pdf_margin_left, 35);
  $y = $pdf->getY();

  $pdf->SetFont("oranda_btb", "B", 8);
  $o = t('Bill to', array(), $language);
  if ($language1) $o .= "\n". t('Bill to', array(), $language1);
  $pdf->MultiCell($pageWidth *.4, 0, $o, 'B', 'L', 0, 1, $pdf_margin_left, $y);
  $pdf->SetFont("oranda_bt", "N", 8);
  $o =  $organization->title ."\n";
  $o .= $organization->address ."\n";
  if ($organization->country == 'DE') {
    if ($organization->zip) $o .= $organization->zip .' ';
    $o .= $organization->city ."\n";
  } else {
    $o .= $organization->city ."\n";
    if ($organization->zip) $o .= $organization->zip .' ';
  }
  if ($organization->provstate) $o .= $organization->provstate .' ';
  $o .= t($countries[$organization->country], array(), $language) ."\n";;
  //if ($organization->taxid) {
  //  $o .= t('Tax ID', array(), $language);
  //  if ($language1) $o .= ' / '. t('Tax ID', array(), $language1);
  //  $o .= ' : '. $organization->taxid;
  //}
  $pdf->SetFont("oranda_bt", "N", 8);
  $pdf->MultiCell($pageWidth *.4, 20, $o, 0, 'L' ,0, 1, $pdf_margin_left, $pdf->getY(), true, 0, false, true, 20, 'B');
  $destY = $pdf->getY();

  $w = ($pageWidth *.5) / 3;

  $pdf->SetFont("oranda_btb", "B", 8);

  $o = t('Invoice#', array(), $language);
  if ($language1) $o .= "\n". t('Invoice#', array(), $language1);
  $pdf->MultiCell($w-2, 0, $o, 1, 'L', 1, 0, $pdf_margin_left + $pageWidth *.5, $y);

  $o = t('Currency', array(), $language);
  if ($language1) $o .= "\n". t('Currency', array(), $language1);
  $pdf->MultiCell($w-3, 0, $o, 1, 'L', 1, 0);
  $o = t('Date', array(), $language);
  if ($language1) $o .= "\n". t('Date', array(), $language1);
  $pdf->MultiCell($w+5, 0, $o, 1, 'L', 1, 1);
  //$o = t('Reference', array(), $language);
  //if ($language1) $o .= "\n". t('Reference', array(), $language1);
  //$pdf->MultiCell($w, 0, $o, 1, 'L', 1, 1);

  $pdf->SetFont("oranda_bt", "N", 8);
  $h = $pdf->getY();
  $pdf->MultiCell($w-2, 0, $node->number, 1, 'L', 0, 0, $pdf_margin_left + $pageWidth *.5);
  //$pdf->MultiCell($w, 0, $node->reference?$node->reference:'-' , 1, 'L', 0, 1, $pdf_margin_left + $pageWidth*.5 + $w*3);
  $h = $pdf->getY() - $h;
  $pdf->MultiCell($w-3, $h, $mycurrency, 1, 'L', 0, 0);
  $pdf->MultiCell($w+5, $h, format_date($node->requestdate, 'custom', $dateformat), 1, 'L', 0, 1);

  $pdf->SetFont("oranda_btb", "B", 8);

  $o = t('Due total', array(), $language);
  if ($language1) $o .= "\n". t('Due total', array(), $language1);
  $pdf->MultiCell($w*2 - 5, 0, $o, 1, 'L', 1, 0, $pdf_margin_left + $pageWidth *.5);

  $o = t('Due date', array(), $language);
  if ($language1) $o .= "\n". t('Due date', array(), $language1);
  $pdf->MultiCell($w + 5, 0, $o, 1, 'L', 1, 1);

  //$o = t('Terms', array(), $language);
  //if ($language1) $o .= "\n". t('Terms', array(), $language1);
  //$pdf->MultiCell($w, 0, $o, 1, 'L', 1, 1);

  $pdf->SetFont("oranda_btb", "B", 8);
  $o = $mycurrency .' '. number_format(sprintf('%.2f', $node->total),2,$decimalsep,'');
  if ($organization->currency != $myorg->currency) {
    $o .= "\n". $organization->currency .' '. number_format(sprintf('%.2f', $node->totalcustomercurr),2,$decimalsep,'');
  }
  $pdf->MultiCell($w*2 - 5, 8, $o, 1, 'C', 0, 0, $pdf_margin_left + $pageWidth *.5);
  $pdf->SetFont("oranda_bt", "N", 8);
  $pdf->MultiCell($w + 5, 8, format_date($node->duedate, 'custom', $dateformat), 1, 'L', 0, 1);
  //$pdf->MultiCell($w, 12, variable_get('storminvoice_payment_terms', ''), 1, 'L', 0, 1);
  
  // here possibly some nice short message...
  
  $pdf->setXY($pdf_margin_left, 90);
  
  $pdf->SetFont("oranda_btb", "B", 8);
  
  $notax ? $w = .85 : $w = .55;

  $o = t('Description', array(), $language);
  if ($language1) $o .= "\n". t('Description', array(), $language1);
  $pdf->MultiCell($pageWidth * $w, 0, $o, 1, 'L', 1, 0);

  if (!$notax) {
    $o = t('Amount', array(), $language);
    if ($language1) $o .= "\n". t('Amount', array(), $language1);
    $pdf->MultiCell($pageWidth * .15, 0, $o, 1, 'C', 1, 0);

    if ($node->tax1) {
    $o = t(variable_get('storm_tax1_name', 'Tax 1'), array(), $language);
    if ($language1) $o .= "\n". t(variable_get('storm_tax1_name', 'Tax 1'), array(), $language);
    $pdf->MultiCell($pageWidth * .15, 0, $o, 1, 'C', 1, 0);
    }

    if ($node->tax2) {
    $o = t(variable_get('storm_tax2_name', 'Tax 2'), array(), $language);
    if ($language1) $o .= "\n". t(variable_get('storm_tax2_name', 'Tax 2'), array(), $language);
    $pdf->MultiCell($pageWidth * .15, 0, $o, 1, 'C', 1, 0);
    }

    $o = t('Total', array(), $language);
    if ($language1) $o .= "\n". t('Total', array(), $language1);
    $pdf->MultiCell($pageWidth * .15, 0, $o, 1, 'C', 1, 1);
  }
  else {
    $o = t('Amount', array(), $language);
    if ($language1) $o .= "\n". t('Total', array(), $language1);
    $pdf->MultiCell($pageWidth * .15, 0, $o, 1, 'C', 1, 1);
  }

  $pdf->SetFont("oranda_bt", "N", 8);
  $items = storminvoice_getitems($node->vid);
  $rows = array();
  $pdf->setFillColor(235,235,235);
  $c = 0;
  foreach ($items as $i) {
    if ($c==2) $c=0;
    $y = $pdf->getY();
    $h = $pdf->getY();
    $pdf->MultiCell($pageWidth * $w, 0, $i->description, 1, 'L', $c, 1, $pdf_margin_left);
    $h = $pdf->getY() - $h;
    $pdf->setY($y);
    $pdf->setX($pdf_margin_left + $pageWidth * $w);
    if (!$notax) {
      $pdf->Cell($pageWidth * .15, $h, number_format(sprintf('%.2f', $i->amount),2,$decimalsep,''), 1, 0, 'R', $c);
      if ($node->tax1) {
      $pdf->Cell($pageWidth * .15, $h, number_format(sprintf('%.2f', $i->tax1),2,$decimalsep,''), 1, 0, 'R', $c);
      }
      if ($node->tax2) {
      $pdf->Cell($pageWidth * .15, $h, number_format(sprintf('%.2f', $i->tax2),2,$decimalsep,''), 1, 0, 'R', $c);
      }
    }
    $pdf->Cell($pageWidth * .15, $h, number_format(sprintf('%.2f', $i->total),2,$decimalsep,''), 1, 1, 'R', $c);
    $c++;
  }
  
  $pdf->setFillColor(215, 223, 226);
  $pdf->SetFont("oranda_btb", "B", 8);
  $y = $pdf->getY();
  if ($node->tax1 && !$notax) {
    $pdf->MultiCell($pageWidth * .30, $h, t('Net total', array(), $language), 1, 'R', 0, 0, $pdf_margin_left + $pageWidth * .55);  
    $pdf->MultiCell($pageWidth * .15, $h, number_format(sprintf('%.2f', $node->amount),2,$decimalsep,''), 1, 'R', 0, 1);
    $pdf->MultiCell($pageWidth * .30, $h, t(variable_get('storm_tax1_name', 'Tax 1').' '.'total', array(), $language), 1, 'R', 0, 0, $pdf_margin_left + $pageWidth * .55);  
    $pdf->MultiCell($pageWidth * .15, $h, number_format(sprintf('%.2f', $node->tax1),2,$decimalsep,''), 1, 'R', 0, 1);
    $pdf->MultiCell($pageWidth * .30, $h, t('Due total', array(), $language), 1, 'R', 1, 0, $pdf_margin_left + $pageWidth * .55);  
  } else {
    $pdf->MultiCell($pageWidth * .45, $h, t('Due total', array(), $language), 1, 'R', 1, 0, $pdf_margin_left + $pageWidth * .4);  
  }
  $pdf->MultiCell($pageWidth * .15, $h, number_format(sprintf('%.2f', $node->total),2,$decimalsep,''), 1, 'R', 1, 1);

  if ($notax) {
    $pdf->SetFont("oranda_btb", "B", 6);
    $o = t('Due total includes no taxes', array(), $language);
    $pdf->MultiCell($pageWidth, 0, $o,0 , 'L' ,0, 1, $pdf_margin_left, $pdf->getY() + 1);
  }

  $y = $pdf->getY() + 10;
  $pdf->setXY($pdf_margin_left,$y);
  $pdf->SetFont("oranda_btb", "B", 8);
  $pdf->Cell($pageWidth, 0, t('Payment', array(), $language), 'B', 0, 'L');
  $pdf->SetFont("oranda_bt", "N", 8);
  $pdf->MultiCell($pageWidth, 0, t(variable_get('storminvoice_payment_modes', ''), array(), $language), 0 , 'L' ,0, 1, $pdf_margin_left, $pdf->getY()+5, true, 0, true);

  if ($status=='paid') {
    $y = $pdf->getY() + 5;
    $pdf->setY($y);
    $pdf->SetFont("helvetica", "B", 14);
    $pdf->Cell(0, 12, t('Paid in full', array(), $language), 0 ,1,'C');
  }
  
  // greetings
  $y = $pdf->getY() + 10;
  $pdf->setXY($pdf_margin_left,$y);
  $pdf->SetFont("oranda_bt", "N", 8);
  $pdf->MultiCell($pageWidth, 0, t('Sincerely,', array(), $language), 0 , 'L' ,0, 1, $pdf_margin_left, $y, true, 0, true);
  $y = $pdf->getY() + 10;
  $pdf->MultiCell($pageWidth, 0, $myorg->title, 0 , 'L' ,0, 1, $pdf_margin_left, $y, true, 0, true);
  $y = $y - 10;
  if (file_exists($path.'images/sign_'.$user->uid.'.ai')) {
    $pdf->ImageEps($file=$path.'images/sign_'.$user->uid.'.ai', $pdf_margin_left, $y, $w=0, $h=0, $link='', $useBoundingBox=true, $align='', $palign='', $border=0, $fitonpage=false);
  }
  /**/
  
  $filename = strtolower('invoice_'. str_replace('/', '-', $node->number)) .'.pdf';

  //Close and output PDF document
  if ($output == 'screen') {
    $pdf->Output($filename, "I");
  }
}
?>