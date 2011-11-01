<?php // $Id: template.php,v 1.20 2009/08/26 17:08:19 jmburnz Exp $
// adaptivethemes.com

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
/**
 * Implement HOOK_theme
 * - Add conditional stylesheets:
 *   For more information: http://msdn.microsoft.com/en-us/library/ms537512.aspx
 */
function adaptivetheme_theme(&$existing, $type, $theme, $path) {
  
  // Register a function so we can theme the theme settings form.
  // Added snippet to theme usernames on apachesolr search results as realnames
  return array(
    'system_settings_form' => array(
      'arguments' => array(
        'form' => NULL,
        'key' => 'adaptivetheme',
      ),
	),
	'apachesolr_breadcrumb_uid' => array(
      'arguments' => array('field' => NULL),
    ),
  );
  
  // Compute the conditional stylesheets.
  if (!module_exists('conditional_styles')) {
    include_once drupal_get_path('theme', 'adaptivetheme') .'/inc/template.conditional-styles.inc';
    // _conditional_styles_theme() only needs to be run once.
    if ($theme == 'adaptivetheme') {
      _conditional_styles_theme($existing, $type, $theme, $path);
    }
  }  
  $templates = drupal_find_theme_functions($existing, array('phptemplate', $theme));
  $templates += drupal_find_theme_templates($existing, '.tpl.php', $path);
  return $templates;

}

/**
 * Implementation of hook_preprocess()
 * 
 * This function checks to see if a hook has a preprocess file associated with 
 * it, and if so, loads it.
 * 
 * @param $vars
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered.
 */
 

function adaptivetheme_preprocess(&$vars, $hook) {
  global $user;                                            // Get the current user
  $vars['is_admin'] = in_array('admin', $user->roles);     // Check for Admin, logged in
  $vars['logged_in'] = ($user->uid > 0) ? TRUE : FALSE;
  
  if(is_file(drupal_get_path('theme', 'adaptivetheme') . '/inc/template.preprocess-' . str_replace('_', '-', $hook) . '.inc')) {
    include(drupal_get_path('theme', 'adaptivetheme') . '/inc/template.preprocess-' . str_replace('_', '-', $hook) . '.inc');
  }
}

/**
 * Include custom function<.
 */
include_once(drupal_get_path('theme', 'adaptivetheme') .'/inc/template.custom-functions.inc');

/**
 * Initialize theme settings
 */
if (is_null(theme_get_setting('user_notverified_display')) || theme_get_setting('rebuild_registry')) {

  // Auto-rebuild the theme registry during theme development.
  if (theme_get_setting('rebuild_registry')) {
    drupal_set_message(t('The theme registry has been rebuilt. <a href="!link">Turn off</a> this feature on production websites.', array('!link' => url('admin/build/themes/settings/'. $GLOBALS['theme']))), 'warning');
  }

  global $theme_key;
  // Get node types
  $node_types = node_get_types('names');

  /**
   * The default values for the theme variables. Make sure $defaults exactly
   * matches the $defaults in the theme-settings.php file.
   */
  $defaults = array(
    'user_notverified_display'              => 1,
    'breadcrumb'                            => 'yes',
    'breadcrumb_separator'                  => ' &#187; ',
    'breadcrumb_home'                       => 0,
    'breadcrumb_trailing'                   => 0,
    'breadcrumb_title'                      => 0,
    'search_snippet'                        => 1,
    'search_info_type'                      => 1,
    'search_info_user'                      => 1,
    'search_info_date'                      => 1,
    'search_info_comment'                   => 1,
    'search_info_upload'                    => 1,
    'mission_statement_pages'               => 'home',
    'taxonomy_display_default'              => 'only',
    'taxonomy_format_default'               => 'vocab',
    'taxonomy_enable_content_type'          => 0,
    'submitted_by_author_default'           => 1,
    'submitted_by_date_default'             => 1,
    'submitted_by_enable_content_type'      => 0,
    'rebuild_registry'                      => 0,
    'load_firebug_lite'                     => 0,
    'at_admin_theme'                        => 1,
    'at_admin_theme_node'                   => 1,
    'at_admin_theme_logo'                   => 0,
    'block_edit_links'                      => 1,
    'at_admin_hide_help'                    => 0,
    'layout_method'                         => '0',
    'layout_width'                          => '960px',
    'layout_sidebar_first_width'            => '240',
    'layout_sidebar_last_width'             => '240',
    'layout_enable_settings'                => 'off', // set to 'on' to enable, 'off' to disable
    'color_schemes'                         => 'colors-default.css',
    'color_enable_schemes'                  => 'off',  // set to 'on' to enable, 'off' to disable
  );

  // Make the default content-type settings the same as the default theme settings,
  // so we can tell if content-type-specific settings have been altered.
  $defaults = array_merge($defaults, theme_get_settings());

  // Set the default values for content-type-specific settings
  foreach ($node_types as $type => $name) {
    $defaults["taxonomy_display_{$type}"]         = $defaults['taxonomy_display_default'];
    $defaults["taxonomy_format_{$type}"]          = $defaults['taxonomy_format_default'];
    $defaults["submitted_by_author_{$type}"]      = $defaults['submitted_by_author_default'];
    $defaults["submitted_by_date_{$type}"]        = $defaults['submitted_by_date_default'];
  }

  // Get default theme settings.
  $settings = theme_get_settings($theme_key);

  // Don't save the toggle_node_info_ variables
  if (module_exists('node')) {
    foreach (node_get_types() as $type => $name) {
      unset($settings['toggle_node_info_'. $type]);
    }
  }
  // Save default theme settings
  variable_set(
    str_replace('/', '_', 'theme_'. $theme_key .'_settings'),
    array_merge($defaults, $settings)
  );
  // Force refresh of Drupal internals
  theme_get_setting('', TRUE);
}

// Load collapsed js on blocks page
if (theme_get_setting('at_admin_theme')) {
  if (arg(2) == 'block') {
    drupal_add_js('misc/collapse.js', 'core', 'header', FALSE, TRUE, TRUE);
    $path_to_core = path_to_theme() .'/js/core/';
    drupal_add_js($path_to_core .'admin.collapseblock.js', 'theme', 'header', FALSE, TRUE, TRUE);
    drupal_add_js($path_to_core .'jquery.cookie.js', 'theme', 'header', FALSE, TRUE, TRUE);
  }
}

/** 
 * Load Firebug lite
 */
if (theme_get_setting('load_firebug_lite')) {
  $path_to_core = path_to_theme() .'/js/core/';
  drupal_add_js($path_to_core .'firebug.lite.compressed.js', 'theme', 'header', FALSE, TRUE, TRUE);
}

/**
 * Add the color scheme stylesheet if color_enable_schemes is set to 'on'.
 * Note: you must have at minimum a color-default.css stylesheet in /css/theme/
 */
if (theme_get_setting('color_enable_schemes') == 'on') {
  drupal_add_css(drupal_get_path('theme', 'adaptivetheme') . '/css/theme/' . get_at_colors(), 'theme');
}

/**
 * Override of theme_node_form().
 */
function adaptivetheme_node_form($form) {
  if (theme_get_setting('at_admin_theme_node')) {
    if ((arg(0) == 'node' && arg(1) == 'add') || (is_numeric(arg(1)) && (arg(2) == 'edit'))) {
      $buttons = '<div class="buttons">'. drupal_render($form['buttons']) .'</div>';
      $sidebar = drupal_render($form['taxonomy']);
      $main = drupal_render($form);
      return "<div class='node-form clear-block'>
        <div class='node-col-last'>{$buttons}{$sidebar}</div>
        <div class='node-col-first'><div class='main'>{$main}{$buttons}</div></div>
        </div>";
    }
  }
  
   $form['biblio_cut_paste'] = array(
        '#type' => 'textarea',
        '#title' => t('BibTex'),
        '#required' => FALSE,
        '#default_value' => $form_state['values']['paste_data_bibtex'],
        '#description' => t('Paste a BibTex entry here'),
        '#size' => 60,
        '#weight' => -4,
        );
  $form['biblio_cut_paste']['paste_data_bibtex'] = '';
}

/**
 * Modify search results based on theme settings
 */
function adaptivetheme_preprocess_search_result(&$variables) {
  static $search_zebra = 'even';
  $search_zebra = ($search_zebra == 'even') ? 'odd' : 'even';
  $variables['search_zebra'] = $search_zebra;
  
  $result = $variables['result'];
  $variables['url'] = check_url($result['link']);
  $variables['title'] = check_plain($result['title']);

  // Check for existence. User search does not include snippets.
  $variables['snippet'] = '';
  if (isset($result['snippet']) && theme_get_setting('search_snippet')) {
    $variables['snippet'] = $result['snippet'];
  }
  
  $info = array();
  if (!empty($result['type']) && theme_get_setting('search_info_type')) {
    $info['type'] = check_plain($result['type']);
  }
  if (!empty($result['user']) && theme_get_setting('search_info_user')) {
    $info['user'] = $result['user'];
  }
  if (!empty($result['date']) && theme_get_setting('search_info_date')) {
    $info['date'] = format_date($result['date'], 'small');
  }
  if (isset($result['extra']) && is_array($result['extra'])) {
    // $info = array_merge($info, $result['extra']);  Drupal bug?  [extra] array not keyed with 'comment' & 'upload'
    if (!empty($result['extra'][0]) && theme_get_setting('search_info_comment')) {
      $info['comment'] = $result['extra'][0];
    }
    if (!empty($result['extra'][1]) && theme_get_setting('search_info_upload')) {
      $info['upload'] = $result['extra'][1];
    }
  }

  // Provide separated and grouped meta information.
  $variables['info_split'] = $info;
  $variables['info'] = implode(' - ', $info);

  // Provide alternate search result template.
  $variables['template_files'][] = 'search-result-'. $variables['type'];
}

/**
 * Override username theming to display/hide 'not verified' text
 */
function adaptivetheme_username($object) {
  if ($object->uid && $object->name) {
    // Shorten the name when it is too long or it will break many tables.
    if (drupal_strlen($object->name) > 20) {
      $name = drupal_substr($object->name, 0, 15) .'...';
    }
    else {
      $name = $object->name;
    }
    if (user_access('access user profiles')) {
      $output = l($name, 'user/'. $object->uid, array('attributes' => array('title' => t('View user profile.'))));
    }
    else {
      $output = check_plain($name);
    }
  }
  else if ($object->name) {
    // Sometimes modules display content composed by people who are
    // not registered members of the site (e.g. mailing list or news
    // aggregator modules). This clause enables modules to display
    // the true author of the content.
    if (!empty($object->homepage)) {
      $output = l($object->name, $object->homepage, array('attributes' => array('rel' => 'nofollow')));
    }
    else {
      $output = check_plain($object->name);
    }
    // Display or hide 'not verified' text
    if (theme_get_setting('user_notverified_display') == 1) {
      $output .= ' ('. t('not verified') .')';
    }
  }
  else {
    $output = variable_get('anonymous', t('Anonymous'));
  }
  return $output;
}

/**
 * Set default form file input size 
 */
function adaptivetheme_file($element) {
  $element['#size'] = 60;
  return theme_file($element);
}

/**
 * Return a themed breadcrumb trail.
 *
 * @param $breadcrumb
 *   An array containing the breadcrumb links.
 * @return
 *   A string containing the breadcrumb output.
 */
function adaptivetheme_breadcrumb($breadcrumb) {
  // Determine if we are to display the breadcrumb.
  $show_breadcrumb = theme_get_setting('breadcrumb_display');
  if ($show_breadcrumb == 'yes' || $show_breadcrumb == 'admin' && arg(0) == 'admin') {

    // Optionally get rid of the homepage link.
    $show_breadcrumb_home = theme_get_setting('breadcrumb_home');
    if (!$show_breadcrumb_home) {
      array_shift($breadcrumb);
    }

    // Return the breadcrumb with separators.
    if (!empty($breadcrumb)) {
      $breadcrumb_separator = theme_get_setting('breadcrumb_separator');
      $trailing_separator = $title = '';
      if (theme_get_setting('breadcrumb_title')) {
        $trailing_separator = $breadcrumb_separator;
        $title = menu_get_active_title();
      }
      elseif (theme_get_setting('breadcrumb_trailing')) {
        $trailing_separator = $breadcrumb_separator;
      }
      return implode($breadcrumb_separator, $breadcrumb) . $trailing_separator . $title;
    }
  }
  // Otherwise, return an empty string.
  return '';
}

// Override theme_button for expanding graphic buttons
function adaptivetheme_button($element) {
  if (isset($element['#attributes']['class'])) {
    $element['#attributes']['class'] = 'form-'. $element['#button_type'] .' '. $element['#attributes']['class'];
  }
  else {
    $element['#attributes']['class'] = 'form-'. $element['#button_type'];
  }

  // Wrap visible inputs with span tags for button graphics
  if (stristr($element['#attributes']['style'], 'display: none;') || stristr($element['#attributes']['class'], 'fivestar-submit')) {
    return '<input type="submit" '. (empty($element['#name']) ? '' : 'name="'. $element['#name'] .'" ')  .'id="'. $element['#id'] .'" value="'. check_plain($element['#value']) .'" '. drupal_attributes($element['#attributes']) ." />\n";
  }
  else {
    return '<span class="button-wrapper"><span class="button"><span><input type="submit" '. (empty($element['#name']) ? '' : 'name="'. $element['#name'] .'" ')  .'id="'. $element['#id'] .'" value="'. check_plain($element['#value']) .'" '. drupal_attributes($element['#attributes']) ." /></span></span></span>\n";
  }
}

/**
 * Format a group of form items.
 *
 * @param $element 
 *   An associative array containing the properties of the element. 
 *
 * @return
 *   A themed HTML string representing the form item group.
 */
function adaptivetheme_fieldset($element) {
  if ($element['#collapsible']) {
    drupal_add_js('misc/collapse.js');

    if (!isset($element['#attributes']['class'])) {
      $element['#attributes']['class'] = '';
    }

    $element['#attributes']['class'] .= ' collapsible';
    if ($element['#collapsed']) {
     $element['#attributes']['class'] .= ' collapsed';
    }
  }
  // Custom fieldset CSS class from element #title.
  $css_class = 'fieldset-'. safe_string($element['#title']);

  $element['#attributes']['class'] .= (!empty($element['#attributes']['class']) ? " " : "") . $css_class;

  return '<fieldset'. drupal_attributes($element['#attributes']) .'>'. ($element['#title'] ? '<legend>'. $element['#title'] .'</legend>' : '') . ($element['#description'] ? '<div class="description">'. $element['#description'] .'</div>' : '') . $element['#children'] . $element['#value'] ."</fieldset>\n";
}

/**
 * Modify the theme settings form for our theme.
 *
 * This is incldued here to make it easier to set up the theme, so 
 * you only have one file to worry about search/repeace the theme name.
 */
function adaptivetheme_system_settings_form($form) {
  // Theme the crap out of the theme settings fieldsets.
  $form['theme_settings']['#title'] = t('Drupal core theme settings');
  $form['theme_settings']['#collapsible'] = TRUE;
  $form['theme_settings']['#collapsed']   = TRUE;
  $form['theme_settings']['#prefix'] = '<div class="theme-settings-settings-wrapper">';
  $form['theme_settings']['#suffix'] = '</div>';
  $form['logo']['#collapsible'] = TRUE;
  $form['logo']['#collapsed']   = TRUE;
  $form['logo']['#prefix'] = '<div class="logo-settings-wrapper">';
  $form['logo']['#suffix'] = '</div>';
  $form['favicon']['#collapsible'] = TRUE;
  $form['favicon']['#collapsed']   = TRUE;
  $form['favicon']['#prefix'] = '<div class="favicon-settings-wrapper">';
  $form['favicon']['#suffix'] = '</div>';
  $form['node_info']['#collapsible'] = TRUE;
  $form['node_info']['#collapsed']   = TRUE;
  $form['node_info']['#prefix'] = '<div class="node-info-settings-wrapper">';
  $form['node_info']['#suffix'] = '</div>';
  $form['theme_specific']['#title'] = t('Advanced theme settings');
  $form['theme_specific']['#collapsible'] = TRUE;
  $form['theme_specific']['#collapsed']   = TRUE;
  return drupal_render($form);
}

//
// PDF Creation options
//
///////////////////////////////////////////////////

/**
  * Helping format HTML for TCPDF
  */
function adaptivetheme_preparehtml($html) {  
  
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
function adaptivetheme_print_pdf_tcpdf_header($pdf, $html, $font) {
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
function adaptivetheme_print_pdf_tcpdf_page($pdf) {
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
function adaptivetheme_print_pdf_tcpdf_content($pdf, $html, $font) {
  
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
  $covertext = adaptivetheme_preparehtml($m[1]);
  */
  /*
  // cover edition - $coveredition
  $pattern = '!(?:<h2 id="cover-edition">(.*?)</h2>)!si';
  preg_match($pattern, $print_html, $m);
  $coveredition = adaptivetheme_preparehtml($m[1]);
  */
  
  // set content font
  // $pdf->setFont($font[0], $font[1], $font[2]);
 
  $pdf->setLineStyle($style=array('width' => 0.5));

  /***********************************************************************************************************/
  /* COVER PAGE */
  /***********************************************************************************************************/
  $coverfile = drupal_get_path('theme', 'adaptivetheme') . '/templates/simplenews/cover.ai';
  
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
    $pdf->writeHTMLCell(143.5, 0, 54, $y, adaptivetheme_preparehtml($linked_node->field_newstext[0]['value']), 0, 1, false, true, 'L', false);     
    
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
      $pdf->writeHTMLCell(143.5, 0, 54, $y+4, adaptivetheme_preparehtml($linked_node->field_furtherinfo[0]['value']), 0, 1, false, true, 'L', false); 
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
function adaptivetheme_print_pdf_tcpdf_footer($pdf, $html, $font) {
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
function adaptivetheme_print_pdf_tcpdf_footer2($pdf) {
  
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
function adaptivetheme_node_widget_form($form) {

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
	if($g->field_projectlogo[0]['filepath']) {
		$image = theme('imagecache', 'projectlogo_1-2c', $g->field_projectlogo[0]['filepath']);
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
function adaptivetheme_apachesolr_breadcrumb_uid($field) {
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
