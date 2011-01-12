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
  return array(
    'system_settings_form' => array(
      'arguments' => array(
        'form' => NULL,
        'key' => 'adaptivetheme',
      ),
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

function adaptivetheme_node_more_link($node) {
	return '<div class="node-more-link">&hellip; ' . l('weiterlesen', 'node/' . $node->nid) . '</div>';
}

function adaptivetheme_node_submitted($node) {
	return sprintf('Verfasst von %s', theme('username', $node));
}

function adaptivetheme_addthis_button() {
	return '<div class="addthis_button_div">
		<a class="addthis_button" href="http://www.addthis.com/bookmark.php?v=250&amp;username=stoeckit"><img src="/sites/balanceonline.org/themes/balance/img/sm-share-en.gif" width="83" height="16" alt="Bookmark and Share" style="border:0"/></a>
	</div>';
}

/**
 * Overrides theme_event_ical_link: Format the ical link
 *
 * @param path
 *   The url for the ical feed
 */
function adaptivetheme_event_ical_link($path) {
	return '';
}

/**
 * Overrides theme_event_more_link: the 'read more' link for events
 *
 * @param string path
 *   The url to use for the read more link
 */
function adaptivetheme_event_more_link($path) {
	return '<div class="more-link">'. l('Alle Termine', $path) .'</div>';
}

/**
 * Overrides theme_event_upcoming_item: an individual upcoming event block item
 *
 * @param node
 *   The node to render as an upcoming event
 */
function adaptivetheme_event_upcoming_item($node, $types = array()) {
	$formatted_date = date('d.m.', strtotime($node->event_start));

	$output = l($formatted_date . ' | ' . $node->title, 'node/' . $node->nid, array('attributes' => array('title' => $node->title)));
	return $output;
}


/**
 * Outputs visibility information for a given set of Organic Groups
 *
 * @param array $groups
 *   e.g. 45 => 'ACHTINO' (og_groups_both)
 */
function adaptivetheme_visibility($groups) {
	$output = sprintf('<div class="visibility" title="Sichtbar für %s"></div>', implode(' | ', $groups));
	return $output;
}

function adaptivetheme_edit_link($node) {
	$output = '';

	if(in_array($node->type, array('project', 'focusgroup')) && arg(2) != 'info') {
		return '';
	}

	if(arg(2) != 'edit') {
		if(node_access('update', $node)) {
			$output = '<div id="balance-node-edit"><span class="famfam active balance-node-edit"></span>' . l(t('Edit'), 'node/' . $node->nid . '/edit') . '</div>';
		}
	} else {
		$output = '<span id="balance-node-edit-back">' . l('Zurück', 'node/' . $node->nid) . '</span>';
	}

	return $output;
}


/**
 * Outputs a formatted link to the parent focusgroup
 *
 * @param object node
 *   current node
 * @param object parent
 *   parent node
 */
function adaptivetheme_parent_focusgroup($node, $parent) {

	//echo '<div>';
	//echo "<b>Ansprechpartner</b><br>";
	//if($node->picture){
	//	echo '<img src="/'.$node->picture.'"><br>';
	//}
	//echo $node->name."<br>";
	//$userobj = unserialize($node->data);
	//echo "Telefon: ".$userobj['addresses']['phone']."<br>";
	//$userurl = drupal_get_path_alias('user/'.$node->uid);
	//echo '<a href="/'.$userurl.'/contact">Kontaktieren</a>';
	//echo "</div><br>";

	if($parent && user_access('view focusgroups')) {

		echo sprintf('<p id="parent-fg">Das Projekt %s ist Teil der Fokusgruppe %s</p>', $node->title, phptemplate_group_list_item($parent, TRUE, FALSE));

		if($node->field_projecthomepage[0]['url']){
			echo 'Projekthomepage<br><a href="'.$node->field_projecthomepage[0]['url'].'">'.$node->field_projecthomepage[0]['display_title'].'</a><br><br>';
		}
		if($node->field_synopsis[0]['view']){
			echo "Projektexposé<br>" . $node->field_synopsis[0]['view']."<br>";
		}
	}
}

/**
 * workaround if setlocale(LC_TIME, "de_DE"); doesn't work
 *
 * @param string start
 * @param string end
 * @todo working setLocale
 */
function adaptivetheme_timeframe($start, $end = '0000-00-00 00:00:00') {
	$dateString = _adaptivetheme_timeframe_original($start, $end);

	$daysEn = array('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday');
	$daysDe = array('Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag','Sonntag');

	return str_replace($daysEn, $daysDe, $dateString);
}

/**
 * Formats one or two dates in the form "Montag, den 30. bis Dienstag den 31.12.2010"
 * @param string start
 * @param string end
 */
function _adaptivetheme_timeframe_original($start, $end) {
	$start_date = substr($start, 0, 10);
	$end_date = substr($end, 0, 10);

	$start = strtotime($start);
	$end = strtotime($end);

	if(!$start) {
		return;
	} elseif(!$end || $start_date == $end_date) {
		return date('l, \d\e\n j.n.Y', $start);
	} else {
		if(date('Y', $start) == date('Y', $end)) {
			//same year
			if(date('n', $start) == date('n', $end)) {
				//same month
				return date('l, \d\e\n j.', $start) . ' bis ' . date('l, \d\e\n j.n.Y', $end);
			} else {
				//different month
				return date('l, \d\e\n j.n.', $start) . ' bis ' . date('l, \d\e\n j.n.Y', $end);
			}
		} else {
			//different year
			return date('l, \d\e\n j.n.Y.', $start) . ' bis ' . date('l, \d\e\n j.n.Y', $end);
		}
	}
}


/**
 * Override or insert PHPTemplate variables into the search_theme_form template.
 *
 * @param $vars
 *   A sequential array of variables to pass to the theme template.
 * @param $hook
 *   The name of the theme function being called (not used in this case.)
 */
function adaptivetheme_preprocess_search_block_form(&$vars, $hook) {
	// todo: replace this by a more drupal-way solution
	$vars['search_form'] = str_replace('Diese Website durchsuchen:', 'Suche …', $vars['search_form']);
}

/**
 * Replace username with display name
 * Copies large parts of theme_username
 *
 * @param array object
 * @return string
 */
function adaptivetheme_username($object) {
	// copy of theme_username from here on
	if ($object->uid && $object->name) {
		if(isset($object->profile_firstname)) {
			$full_name = implode(' ', array($object->profile_firstname, $object->profile_lastname));
		} else {
			$user = user_load($object->uid);
			$full_name = implode(' ', array($user->profile_firstname, $user->profile_lastname));
		}
		// Shorten the name when it is too long or it will break many tables.
		if (drupal_strlen($full_name) > 20) {
			$name = drupal_substr($full_name, 0, 18) .'…';
		}
		else {
			$name = $full_name;
		}

		if (user_access('access user profiles')) {
			$output = l($name, 'user/'. $object->uid, array('title' => t('View user profile.')));
		}
		else {
			$output = check_plain($name);
		}
	} else if ($object->name) {
		// Sometimes modules display content composed by people who are
		// not registered members of the site (e.g. mailing list or news
		// aggregator modules). This clause enables modules to display
		// the true author of the content.
		if ($object->homepage) {
	  $output = l($object->name, $object->homepage);
		}
		else {
	  $output = check_plain($object->name);
		}

		$output .= ' ('. t('not verified') .')';
	}
	else {
		$output = variable_get('anonymous', t('Anonymous'));
	}

	return $output;
}

/**
 * Prints menu item children of a given node id
 *
 * @param array node
 * @param string title
 * @return string
 */
function phptemplate_print_children($node, $title = '') {
	$current_menu_item = db_fetch_array(db_query("SELECT mlid FROM {menu_links} WHERE link_path = 'node/%d' AND link_title LIKE 'Fokusgruppe %'", $node->nid));
	$children = db_query("SELECT * FROM {menu_links} WHERE plid = %d AND link_path != 'node/%d' ORDER BY weight", $current_menu_item['mlid'], $node->nid);

	$children_items = array();
	while ($c = db_fetch_array($children)) {
		$children_items[] = l($c['link_title'], $c['link_path']);
	}

	return theme_item_list($children_items, $title);
}

/**
 * Formats Names: Jakob -> Jakobs, Andreas -> Andreas’
 *
 * @param string owner
 * @return string
 */
function phptemplate_owner($owner) {
	return $owner . (in_array(substr($owner, -1), array('s', 'x')) ? '’' : 's');
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
function phptemplate_group_list_item($g, $withTitle = TRUE, $withCreateLink = TRUE) {
	if($g->field_projectlogo[0]['filepath']) {
		$image = theme('imagecache', 'projectlogo_1-2c', $g->field_projectlogo[0]['filepath']);
	} else {
		$image = '';
	}

	$out = l($image, 'node/' . $g->nid, array('html' => TRUE, 'attributes' => array('title' => $g->title)));

	if($withTitle || $withCreateLink) {
		$out .= '<ul>';

		if($withTitle) {
			$out .= '<li class="group_title">' . l($g->title, 'node/' . $g->nid, array('html' => TRUE)) . '</li>';
		}
/*
		if($withCreateLink) {
			$out .= '<li class="node_add">' . l('Beitrag schreiben', 'node/add/blog', array('query' => 'gids[]='. $g->nid)) . '</li>';
		}
*/
		$out .= '</ul>';
	}


	return $out;
}

/**
 * Outputs a link to write a new og blog post in the active organic group
 *
 * @param object $node
 */
function adaptivetheme_og_add_blog_link($node) {
	global $user;
	list($txt, $subscription) = og_subscriber_count_link($node);

	if(($subscription == 'active' && module_invoke('blog', 'access', 'create', 'blog', $user)) || user_access('administer nodes')) {
		$links = module_invoke_all('og_create_links', $node);
		if($links['create_blog']) {
			return '<span class="famfam active balance-add-node"></span><span id="balance-add-node">' . $links['create_blog'] . '</span>';
		}
	}
}
function adaptivetheme_menu_local_task($link,$selected=0) {
	if (strpos($link, 'hide="true"')) { 
		return '';
	}
	return '<li '.($selected==1 ? ' class="active"': '').'>'.$link.'</li>';
}
function adaptivetheme_menu_item_link($link) {
	
	if ($link['path']=='node/%/edit') { 
		$link['localized_options']['attributes']['hide'] .= 'true';
	}
	return l($link['title'], $link['href'], $link['localized_options']);
}

function adaptivetheme_upload_form_current(&$form) {
	$header = array('', t('Description'),t('Delete'));
	//$header = array();
	drupal_add_tabledrag('upload-attachments', 'order', 'sibling', 'upload-weight');

	foreach (element_children($form) as $key) {
		// Add class to group weight fields for drag and drop.
		$form[$key]['weight']['#attributes']['class'] = 'upload-weight';

		$row = array('');
		$row[] = drupal_render($form[$key]['description']);
		$row[] = drupal_render($form[$key]['remove']);
		//  $row[] = drupal_render($form[$key]['list']);

		//    $row[] = drupal_render($form[$key]['size']);
		$rows[] = array('data' => $row, 'class' => 'draggable');
	}
	$output = '<br><br>'.theme('table', $header, $rows, array('id' => 'upload-attachments'));
	$output .= drupal_render($form);
	return $output;

}

function adaptivetheme_upload_form_new(&$form) {
	$files = & $form['files'];
	$files['#weight']=10;
	foreach ($files as $fileId =>$file) {
		if (is_int($fileId)) {
			unset($files[$fileId]['size']);
			$files[$fileId]['description']['#size']=50;
		}
	}
	$output = drupal_render($form);
	return $output;

}

function phptemplate_preprocess_flag(&$vars) {
  //$vars['link_text'] = '<span class="famfam active balance-like></span>';
}

function pn_node($node, $mode = 'n') {
  if (!function_exists('prev_next_nid')) {
    return NULL;
  }

  switch($mode) {
    case 'p':
      $n_nid = prev_next_nid($node->nid, 'prev');
      $link_text = 'previous';
      break;

    case 'n':
      $n_nid = prev_next_nid($node->nid, 'next');
      $link_text = 'next';
      break;

    default:
      return NULL;
  }

  if ($n_nid) {
    $n_node = node_load($n_nid);

    $options = array(
      'attributes' => array('class' => 'thumbnail'),
      'html'  => TRUE,
    );
    switch($n_node->type) {
      // For image nodes only
      case 'image':
        // This is an image node, get the thumbnail
        $html = l(image_display($n_node, 'thumbnail'), "node/$n_nid", $options);
        $html .= l($link_text, "node/$n_nid", array('html' => TRUE));
        return $html;

      // For video nodes only
      case 'video':
        foreach ($n_node->files as $fid => $file) {
          $html  = '<img src="' . base_path() . $file->filepath;
          $html .= '" alt="' . $n_node->title;
          $html .= '" title="' . $n_node->title;
          $html .= '" class="image image-thumbnail" />';
          $img_html = l($html, "node/$n_nid", $options);
          $text_html = l($link_text, "node/$n_nid", array('html' => TRUE));
          return $img_html . $text_html;
        }
      default:
        // Add other node types here if you want.
    }
  }
}

function phptemplate_preprocess_custom_pager(&$vars) {
  // if we're at the end, the nav_array item for this (eg first) is NULL;
  // no need to compare it to current index.
  $vars['first'] = empty($vars['nav_array']['first']) ? '' : l('Erste', 'node/' . $vars['nav_array']['first']);
  $vars['last'] = empty($vars['nav_array']['last']) ? '' : l('Letzte', 'node/' . $vars['nav_array']['last']);
}

function adaptivetheme_preprocess_node(&$vars) {
	// put read more link in own variable
	if(isset($vars['node']->links['node_read_more'])) {
		$l = $vars['node']->links['node_read_more'];
		$vars['node_read_more'] = l($l['title'], $l['href'], $l);
	}
	// remove all but the comment link in nodes from $links
	if(isset($vars['node']->links['comment_add'])) {
		$c = $vars['node']->links['comment_add'];
		$links = l($c['title'], $c['href'], $c);
	} else {
		$links = null;
	}

	$vars['links'] = $links;
}
