<?php

/**
 * @file template.php
 */
function phptemplate_business_card($uid) {
	$hcardOutput = 'Noch keine Person eingetragen';

	if($uid) {
		$user = user_load($uid);

		$hcard = array(
			'url' => '/user/' . $user->uid,
			'given-name' => $user->profile_firstname,
			'family-name' => $user->profile_lastname,
			'street-address' => $user->addresses['street'],
			'postal-code' => $user->addresses['postal_code'],
			'locality' => $user->addresses['city'],
			'country-name' => $user->addresses['country'],
			'tel-work-value' => $user->addresses['tel'],
			'fax-work-value' => $user->addresses['fax'],
			'logo' => theme('user_picture', $user),
		);

		$hcardOutput =
'<div class="vcard" style="display: inline-block;">
	<span class="logo">' . $hcard['logo'] . '</span>
	<span class="fn n">
		<a class="url" href="' . $hcard['url'] . '">
			<span class="given-name">' . $hcard['given-name'] . '</span>
			<span class="family-name">' . $hcard['family-name'] . '</span>
		</a>
	</span>
	<div class="adr">
		<div class="street-address">' . $hcard['street-address'] . '</div>
		<span class="postal-code">' . $hcard['postal-code'] . '</span> <span class="locality">' . $hcard['locality'] . '</span>
		<div class="country-name hide">' . $hcard['country-name'] . '</div>
	</div>
	<div class="tel"><span class="type">Tel.</span>: <span class="value">' . $hcard['tel-work-value'] . '</span></div>
	<div class="tel"><span class="type">Fax</span>: <span class="value">' . $hcard['tel-work-fax'] . '</span></div>
</div>';
	}

	return $hcardOutput;
}

//
// PDF Creation options
//
///////////////////////////////////////////////////

/**
  * Helping format HTML for TCPDF
  */
function researchdean_preparehtml($html) {  
  
  $html = preg_replace('!(<div class="field.*?>)\s*!sm', '$1', $html);
  $html = preg_replace('!(<div class="field.*?>.*?</div>)\s*!sm', '$1', $html);
  $html = preg_replace('!<div( class="field-label.*?>.*?)</div>!sm', '<strong$1</strong>', $html);

  // Since TCPDF's writeHTML is so bad with <p>, do everything possible to make it look nice
  $html = preg_replace('!<(?:p(|\s+.*?)/?|/p)>!i', '<br$1 />', $html);
  $html = str_replace(array('<div', 'div>'), array('<span', 'span><br />'), $html);
  do {
    $prev = $html;
    $html = preg_replace('!(</span>)<br />(\s*?</span><br />)!s', '$1$2', $html);
  } while ($prev != $html);  /**/
  
  return $html;
  
}

/**
 * Format the TCPDF header
 *
 * @param $pdf
 *   current TCPDF object
 * @param $html
 *   contents of the body of the HTML from the original node
 * @param $font
 *   array with the font definition (font name, styles and size)
 * @see theme_print_pdf_tcpdf_header()
 */
function researchdean_print_pdf_tcpdf_header($pdf, $html, $font) {
  //preg_match('!<div class="print-logo">(.*?)</div>!si', $html, $tpl_logo);
  //preg_match('!<h1 class="print-title">(.*?)</h1>!si', $html, $tpl_title);
  //preg_match('!<div class="print-site_name">(.*?)</div>!si', $html, $tpl_site_name);

  //$ratio = 0;
  //$logo = '';
  //$logo_ret = preg_match('!src\s*=\s*(\'.*?\'|".*?"|[^\s]*)!i', $tpl_logo[1], $matches);
  //if ($logo_ret) {
  //  $logo = trim($matches[1], '\'"');
  //  $size = getimagesize($logo);
  //  $ratio = $size ? ($size[0] / $size[1]) : 0;
  //}

  // set header font
  //$pdf->setHeaderFont($font);
  // set header margin
  $pdf->SetHeaderMargin(0);
  // set header data
  //$pdf->SetHeaderData($logo, 10 * $ratio, html_entity_decode($tpl_title[1], ENT_QUOTES, 'UTF-8'), strip_tags($tpl_site_name[1]));

  $pdf->SetPrintHeader(false);
  
  return $pdf;
}

/**
 * Format the TCPDF page settings (margins, etc)
 *
 * @param $pdf
 *   current TCPDF object
 * @see theme_print_pdf_tcpdf_page()
 */
function researchdean_print_pdf_tcpdf_page($pdf) {
  // set margins
  $pdf->SetMargins(12.5, 10, 12.5);
  // set auto page breaks
  $pdf->SetAutoPageBreak(TRUE, 15);
  // set image scale factor
  sscanf(PDF_PRODUCER, "TCPDF %d.%d.%d", $major, $minor, $build);
  $imagescale = (($major >= 4) && ($minor >= 6) && ($build >= 2)) ? 1 : 4;
  $pdf->setImageScale($imagescale);
  // set image compression quality
  $pdf->setJPEGQuality(100);

  return $pdf;
}

/**
 * Format the TCPDF page content
 *
 * @param $pdf
 *   current TCPDF object
 * @param $html
 *   contents of the body of the HTML from the original node
 * @param $font
 *   array with the font definition (font name, styles and size)
 * @see theme_print_pdf_tcpdf_content()
 */
function researchdean_print_pdf_tcpdf_content($pdf, $html, $font) {
  
  // Get current node object
  if ($node = menu_get_object()) {
    $nid = $node->nid;
    $covertext = $node->field_nl_content[0]['value'];
    $year = $node->field_issue[0]['value'];
	$coveredition = t('Issue') . ' ' . $node->title . '/' . $year;
	foreach ($node->field_newsentries as $key => $item) {
      $newsletter[] = $item;
    }
  }
  
 
  
  
  
  
  /*
  // get content
  preg_match('!<body.*?>(.*)</body>!sim', $html, $print_html);
  $pattern = '!(?:<div class="print-(?:logo|site_name|breadcrumb|footer)">.*?</div>|<hr class="print-hr" />)!si';
  $print_html[1] = preg_replace($pattern, '', $print_html[1]);
  
  // get newsletter items
  $pattern = '!(<div class="newsletter-item">.*?(?=<div class="newsletter-item">))!si';
  $count = preg_match_all($pattern, $print_html[1], $matches);
  
  // regex to find data within each newsletter item
  $pattern = "#<h3 class=\"node-title\">\s*(?P<title>.*)\s*</h3>(?:\s|\S)*?".
    "<img src=\"(?P<image>[^\"]*)\"(?:\s|\S)*?"."(?:\s|\S)*?".
    "<div class=\"node-content\">\s*(?P<content>.*)\s*</div>(?:\s|\S)*?".
    "<div class=\"info-box\">\s*(?P<info>.*)\s*</div>(?:\s|\S)*?".
    "#";
  
  // store all newsletter items in $newsletter
  // $newsletter[i]['title/image/content/info'][0]
  foreach ($matches[1] as $key => $item) {
    preg_match_all($pattern, $item, $m);
    $newsletter[] = $m; 
  }  
 */ 
  /*
  // Make CCK fields look better
  $matches[1] = preg_replace('!(<div class="field.*?>)\s*!sm', '$1', $matches[1]);
  $matches[1] = preg_replace('!(<div class="field.*?>.*?</div>)\s*!sm', '$1', $matches[1]);
  $matches[1] = preg_replace('!<div( class="field-label.*?>.*?)</div>!sm', '<strong$1</strong>', $matches[1]);

  // Since TCPDF's writeHTML is so bad with <p>, do everything possible to make it look nice
  $matches[1] = preg_replace('!<(?:p(|\s+.*?)/?|/p)>!i', '<br$1 />', $matches[1]);
  $matches[1] = str_replace(array('<div', 'div>'), array('<span', 'span><br />'), $matches[1]);
  do {
    $prev = $matches[1];
    $matches[1] = preg_replace('!(</span>)<br />(\s*?</span><br />)!s', '$1$2', $matches[1]);
  } while ($prev != $matches[1]);  */
  
  /*
  // cover text - $covertext
  $pattern = '!(?:<div id="cover-text">(.*?)</div>)!si';
  preg_match($pattern, $print_html, $m);
  $covertext = researchdean_preparehtml($m[1]);
  */
  /*
  // cover edition - $coveredition
  $pattern = '!(?:<h2 id="cover-edition">(.*?)</h2>)!si';
  preg_match($pattern, $print_html, $m);
  $coveredition = researchdean_preparehtml($m[1]);
  */
  
  // set content font
  // $pdf->setFont($font[0], $font[1], $font[2]);
 
  $pdf->setLineStyle($style=array('width' => 0.5));

  /***********************************************************************************************************/
  /* COVER PAGE */
  /***********************************************************************************************************/
  $coverfile = drupal_get_path('theme', 'researchdean') . '/templates/simplenews/cover.ai';
  
  // print cover page
  if (file_exists($coverfile)) {
    $pdf->ImageEps($coverfile, $x='0', $y='0', $w=210, $h=0);
  }
  
  // print information on current edition
  $pdf->SetXY(12.5, 25);
  $pdf->SetTextColor(137, 24, 45); /* dark red */
  $pdf->SetFont('helvetica', 'B', 16); 
  $pdf->Cell(0, 16, $coveredition, 0, false, 'L', 0, '', 0, false);          

  // print textbox
  $pdf->SetXY(23, 58);
  $pdf->SetTextColor(0, 0, 0); /* black */
  $pdf->SetFont('helvetica', 'N', 10); 
  $pdf->writeHTMLCell(102, 120, 23, 58, $covertext, 0, 0, false, true, 'L', false); 
  
  // print table of contents
  $pdf->SetXY(13, 170);
  $pdf->SetTextColor(137, 24, 45); /* dark red */
  $pdf->SetFont('helvetica', 'B', 12); 
  $pdf->Cell(0, 10, '>> '.t('table of contents'), 0, false, 'L', 0, '', 0, false);
  $pdf->SetDrawColor(137, 24, 45); /* dark red */
  $pdf->Line(12.5, 170, 197.5, 170); /* $style=array('width' => 1) */  
  $pdf->SetXY(23, 180);  
  $pdf->SetTextColor(0, 0, 0); /* black */
  $pdf->SetFont('helvetica', 'N', 10);
  
  $i = 1;
  // insert foreach here to generate table of contents
  foreach($newsletter as $key => $item) {
    
    $linked_node = node_load($item);
    
    $pdf->SetXY(23, $pdf->GetY());    
    //$item['title/image/content/info'][0]
    $pdf->MultiCell(10, 7, $i.'.', 0, 'L', 0, 0);      
    $pdf->MultiCell(165, 7, $linked_node->title, 0, 'L', 0, 1);
    $i++;    
  
  }          
  
  // start next page
  $pdf->AddPage(); 

  /***********************************************************************************************************/
  /* REGULAR PAGES */
  /***********************************************************************************************************/

  // flip through all newsletter items
  foreach($newsletter as $key => $item) {
   
    $linked_node = node_load($item); 
    
    //$item['title/image/content/info'][0]
    
    // title and line
    $pdf->SetDrawColor(137, 24, 45); /* dark red */
    $pdf->Line(12.5, $pdf->GetY(), 197.5, $pdf->GetY(), $style=array('width' => 0.4));  
    $pdf->SetXY(12.5, $pdf->GetY()+2);    
    $pdf->SetTextColor(137, 24, 45); /* dark red */
    $pdf->SetFont('helvetica', 'B', 12);
    // MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)
    $pdf->MultiCell(185, 14, ">> " . $linked_node->title, 0, 'L', 0, 1); 
	
    
    $y = $pdf->GetY();
    // image
    if (file_exists($linked_node->field_picture[0]['filepath'])) {
      $pdf->Image($linked_node->field_picture[0]['filepath'], 12.5, $y, $w=30, $h=65, $type='', $link='', $align='', $resize=true, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox='TL', $hidden=false, $fitonpage=false);
    }
    
    // text
    $pdf->SetTextColor(0, 0, 0); /* black */
    $pdf->SetFont('helvetica', 'N', 10); 
    
    // writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)
    $pdf->writeHTMLCell(143.5, 0, 54, $y, researchdean_preparehtml($linked_node->field_newstext[0]['value']), 0, 1, false, true, 'L', false);     
    
    if (!empty($linked_node->field_furtherinfo[0]['value'])) {
      $y = $pdf->GetY();
      $pdf->SetXY(54,$y);
      // infobox
      $pdf->SetFont('helvetica', 'B', 10); 
      //Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=0, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M')
      $pdf->Cell(0, 0, t('Further information'), 0, 1, 'L', 0, '', 0, false);
      $pdf->SetDrawColor(0, 0, 0); /* black */
      $pdf->Line(55, $y+4, 197.5, $y+4, $style=array('width' => 0.3));
      $pdf->SetTextColor(0, 0, 0); /* black */
      $pdf->SetY($pdf->GetY()+2);
      $pdf->SetFont('helvetica', 'N', 10); 
      $pdf->writeHTMLCell(143.5, 0, 54, $y+4, researchdean_preparehtml($linked_node->field_furtherinfo[0]['value']), 0, 1, false, true, 'L', false); 
    }
	
	$pdf->SetXY(12.5, $pdf->GetY()+8);
  }  
  
  //@$pdf->writeHTML($matches[1]);
  
  
  $doc_title = $year . '-' . $node->title . '-' . t('Forschungsdekan_Newsletter.pdf');
  $pdf->Output($doc_title, 'D');
  return $pdf;
}

/**
 * Format the TCPDF footer contents
 *
 * @param $pdf
 *   current TCPDF object
 * @param $html
 *   contents of the body of the HTML from the original node
 * @param $font
 *   array with the font definition (font name, styles and size)
 * @see theme_print_pdf_tcpdf_footer()
 */
function researchdean_print_pdf_tcpdf_footer($pdf, $html, $font) {
  /* no footer required
  preg_match('!<div class="print-footer">(.*?)</div>!si', $html, $tpl_footer);
  $footer = trim(preg_replace('!</?div[^>]*?>!i', '', $tpl_footer[1]));

  // set footer font
  $font[2] *= 0.8;
  $pdf->setFooterFont($font);
  // set footer margin
  $pdf->SetFooterMargin(10);
  // set footer data
  $pdf->SetFooterData($footer);
  */
  return $pdf;
}

/**
 * Format the TCPDF footer layout
 *
 * @param $pdf
 *   current TCPDF object
 * @see theme_print_pdf_tcpdf_footer2()
 */
function researchdean_print_pdf_tcpdf_footer2($pdf) {
  
  if ($node = menu_get_object()) {
    $year = $node->field_issue[0]['value'];
    $coveredition = t('Issue') . ' ' . $node->title . '/' . $year;
  }
  
  // not on first page
  if ($pdf->PageNo() == 1) {
    return $pdf;
  }
  
  $pdf->SetTextColor(0, 64, 113); /* dark blue */
  $pdf->SetDrawColor(137, 24, 45); /* dark red */
  
  $pdf->SetFont('helvetica', 'B', 6); 
  
  $pdf->SetXY(12.5, 4.6);
  
  $pagenumtxt = t('Page !n', array('!n' => $pdf->PageNo()));
  $titletxt = t('News from the research dean') . ' - ' . $coveredition;
  
  //Print title and page number
  $pdf->MultiCell(120, 4.9, $titletxt, 0, 'L', 0, 0);      
  $pdf->MultiCell(65, 4.9, $pagenumtxt, 0, 'R', 0, 1);
  //$pdf->Line(12.5, 9.5, 197.5, 9.5, $style=array('width' => 0.5)); /* $style=array('width' => 1) */
  
  $pdf->SetTextColor(0, 0, 0); /* black */
  $pdf->SetDrawColor(0, 0, 0); /* black */  

  return $pdf;
}

/**
 * Theme widget node form as a table row.
 */
function researchdean_node_widget_form($form) {

  if (!empty($form['title']['#default_value'])) {
    $title = $form['title']['#default_value'];
  } else {
    $title = '[Titel fehlt]';
  }

  $output = '';
  $output = '<fieldset class="collapsible collapsed"><legend>' . $title . '</legend>';
  $output .= drupal_render($form);
  $output .='</fieldset>';
  
  return $output;
}

/**
 * Outputs a HTML list for organic groups
 *
 * @param array groups
 */
function phptemplate_group_list($groups) {
	$out = '';

	foreach($groups as $g) {
		$out .= '<div class="group-list-item">' . phptemplate_group_list_item($g) . '</div>';
	}

	return $out;
}

/**
 * Outputs a formatted group badge to use in a list
 *
 * @param array g
 * @param boolean with_text
 * @return string
 */
function phptemplate_group_list_item($g, $withTitle = TRUE, $withCreateLink = FALSE) {
	if($g->field_bild[0]['filepath']) {
		$image = theme('imagecache', 'forschungsfeldlogo_2c', $g->field_bild[0]['filepath']);
	} else {
		$image = '';
	}

	$out = l($image, 'node/' . $g->nid, array('html' => TRUE, 'attributes' => array('title' => $g->title)));

	if($withTitle || $withCreateLink) {
		if(isset($g->user_is_active) && $g->user_is_active === '0') {
			$pending = '<br />' . t('Wartet auf Bestätigung', NULL, 'de');
		} else {
			$pending = '';
		}
		
		$out .= '<ul>';

		if($withTitle) {
			$out .= '<li class="group_title">' . l($g->title, 'node/' . $g->nid, array('html' => TRUE)) . $pending . '</li>';
		}

		if($withCreateLink) {
			$out .= '<li class="node_add">' . l('Beitrag schreiben', 'node/add/blog', array('query' => 'gids[]='. $g->nid)) . '</li>';
		}

		$out .= '</ul>';
	}


	return $out;
}
// Code for displaying authors in apache SolR facets as Realnames .. added snippet to _theme function as well!
/**
* Theme function to change name of ApacheSolr Search's user facet.
*/
function researchdean_apachesolr_breadcrumb_uid($field) {
  $uid = $field;
  if ($uid == 0) {
    return variable_get('anonymous', t('Anonymous'));
  }
  else {
    $user = realname_get_user($uid);
    $realname = $user->name;
    return($realname);
  }
}
