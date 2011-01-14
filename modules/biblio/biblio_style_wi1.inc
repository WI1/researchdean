<?php


// $Id: biblio_style_wi1.inc,v 1.9.2.15 2009/06/18 20:41:47 rjerome Exp $
/**
 * Get the style information
 *
 * @return
 *   The name of the style
 */
function biblio_style_wi1_info() {
  return array(
    'wi1' => 'Department of Information Systems 1, University Erlangen-Nuremberg (WI1)'
  );
}

function biblio_style_wi1_author_options() {
  $author_options = array(
    'BetweenAuthorsDelimStandard'     =>  ', ',      //4
    'BetweenAuthorsDelimLastAuthor'   =>  ', & ',     //5
    'AuthorsInitialsDelimFirstAuthor' =>  ', ',      //7
    'AuthorsInitialsDelimStandard'    =>  ', ',      //8
    'betweenInitialsDelim'            =>  '. ',      //9
    'initialsBeforeAuthorFirstAuthor' =>  FALSE,     //10
    'initialsBeforeAuthorStandard'    =>  FALSE,     //11
    'shortenGivenNames'               =>  TRUE,     //12
    'numberOfAuthorsTriggeringEtAl'   =>  6,         //13
    'includeNumberOfAuthors'          =>  6,         //14
    'customStringAfterFirstAuthors'   =>  ', et al.',//15
    'encodeHTML'                      =>  true
  );
  return $author_options;
}

/**
 * Apply a bibliographic style to the node
 *
 *
 * @param $node
 *   An object containing the node data to render
 * @param $base
 *   The base URL of the biblio module (defaults to /biblio)
 * @param $inline
 *   A logical value indicating if this is being rendered within the
 *   Drupal framwork (FALSE) or we are just passing back the html (true)
 * @return
 *   The styled biblio entry
 */
function biblio_style_wi1($node, $base = 'biblio', $inline = FALSE) {
  /*
   * Authors
   */
  $author_options = biblio_style_wi1_author_options();
  $authors = theme('biblio_format_authors', $node->biblio_contributors[1], $author_options, $inline);
  if (empty($authors) && count($node->biblio_contributors[5])) { // if no authors substitute corp author if available
    foreach ($node->biblio_contributors[5] as $rank => $author) {
      $authors .= (empty($authors)) ? '' : ', ';
      $authors .= (variable_get('biblio_author_links', 1)) ?  theme('biblio_author_link',$author['name'], $author['cid'], variable_get('biblio_base', 'biblio'), $inline) : $author['name'];
    }
  }
  if (empty($authors)) $authors = '[' . t('Anonymous') . ']';  // use anonymous if we still have nothing.
  if (!empty ($node->biblio_citekey)&&(variable_get('biblio_display_citation_key',0))) {
    $output .= '[' . check_plain($node->biblio_citekey) . '] ';
  }
  $output .= '<span class="biblio-authors">' . $authors . "</span> \n";
  $output .= (strrpos($authors, '.') == strlen($authors)) ? ".&nbsp;" : " ";
  
  /*
   * Year
   */
  if (isset ($node->biblio_year)) {
        $output .= "" . check_plain($node->biblio_year) . ".&nbsp;";
      }

	/*
	 * Title
	 */
	$output .= '<span class="biblio-title-wi1">';
	$link = ((variable_get('biblio_link_title_url', 0) && !empty($node->biblio_url)) ? $node->biblio_url : ($inline ? "$base/viewinline/$node->nid":"node/$node->nid" ) ) ;
	$options['attributes'] = (variable_get('biblio_links_target_new_window',null) && variable_get('biblio_link_title_url', 0) && !empty($node->biblio_url)) ? array('target'=>'_blank') : null;
	$options['html'] = TRUE;
	$output .= l($node->title, $link, $options);
	//$output .= $inline ? l("$node->title", "$base/viewinline/$node->nid") : l("$node->title", "node/$node->nid");
	!empty($node->biblio_edition) ? $output .= '</span> (' . check_plain($node->biblio_edition). '). ' : $output .= ".</span> \n";
  
  /*
   * Publication Types
   */
  switch ($node->biblio_type) {
	case 100 : 	// Book
		
	case 1001 : 
		   // Edited Volumes
	
	case 101 : 	// Book section
		
	case 102 : 	// Journal Papers

	case 104 : 	// Conference Papers
		
	case 1002 : // Working Papers
    
	case 1000 : // Executive Briefing Note
    
	case 129 : 	// Misc
    
	default :
      
	  
	  if (count($node->biblio_contributors[2]) > 1) {
		$output .= ($node->biblio_type != 1001) ? 'In ' . theme('biblio_format_authors', $node->biblio_contributors[2], $author_options, 2, $inline) . ' (Eds.), ': "";
		} else {
	   $output .= ($node->biblio_type != 1001 && !empty($node->biblio_contributors[2])) ? 'In ' . theme('biblio_authors', $node->biblio_contributors[2], 'wi1', 2, $inline) . ' (Ed.), ': "";
	   }
	   
	  if ($node->biblio_type == 101 || $node->biblio_type == 104 || $node->biblio_type == 116 || $node->biblio_type == 102) {
			if(!empty($node->biblio_pages)) {
			$output .= !empty ($node->biblio_secondary_title) ? '' . check_plain($node->biblio_secondary_title) . '' : '';
			} else {
			$output .= !empty ($node->biblio_secondary_title) ? '' . check_plain($node->biblio_secondary_title) . '. ' : '';
			}
	  } else {
	  $output .= !empty ($node->biblio_secondary_title) ? '' . check_plain($node->biblio_secondary_title) . '. ' : '';
	  }
	  
      $output .= !empty ($node->biblio_volume) ? check_plain(', ' . $node->biblio_volume) . ($node->biblio_issue ? '(' . check_plain($node->biblio_issue) . ')' : '') : '';
      if ($node->biblio_number) $output .= '('. check_plain($node->biblio_number) . ')';
	  if ($node->biblio_pages) $output .= ': ' . check_plain($node->biblio_pages) . '. ';
    
	break; // generic
  }
//  /*  if ($node->biblio_date) $output .= ', '. check_plain($node->biblio_date);
	
	/*
	 * Publisher &
	 * Place published
	 */
	 
    //if ($node->biblio_place_published) $output .= check_plain($node->biblio_place_published) . ', ';
    if ($node->biblio_place_published && $node->biblio_type!=104) $output .= check_plain($node->biblio_place_published) . '';
	
	// Conference Papers only exhibit location
	if ($node->biblio_place_published && $node->biblio_type==104) $output .= ' ' . check_plain($node->biblio_place_published) . '';
	$output .= !empty ($node->biblio_publisher) ? ': ' . check_plain($node->biblio_publisher) . '. ' : '';

	
    
    //if ($node->biblio_url) $output .= '<a href="' . check_plain($node->biblio_url). '" title="Link to publication">Link</a>, ';
    //if ($node->biblio_isbn) $output .= 'ISBN: ' . check_plain($node->biblio_isbn);
//  */

  
  if (substr($output, -1) != '.' && substr($output, -2) != '. ') {
  $output .= '.';
// temporary!!!
  if ($node->biblio_type == 1001) {
	$output = '';
  }
  
  return $output;
  } else {
// temporary!!!
  if ($node->biblio_type == 1001) {
	$output = '';
  }
  
  return $output;
  }
}
 
/**
 *
 */
function _wi1_format_author($author) {
  $format = $author['prefix'] . ' ' . $author['lastname'] . ', ';
  $format .= !empty ($author['firstname']) ? ' ' . drupal_substr($author['firstname'], 0, 1) . '.' : '';
  $format .= !empty ($author['initials']) ? $author['initials'] . '.' : '';
  return $format;
}
