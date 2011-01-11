<?php // $Id: template.preprocess-page.inc,v 1.2 2009/08/25 03:49:57 jmburnz Exp $
// adaptivethemes.com
/**
 * Override or insert variables into page templates.
 *
 * @param $vars
 *   A sequential array of variables to pass to the theme template.
 * @param $hook
 *   The name of the theme function being called.
 */
global $theme;

// Don't display empty help from node_help().
if ($vars['help'] == "<div class=\"help\"> \n</div>") {
  $vars['help'] = '';
}
  
// Remove sidebars if disabled e.g., for Panels
if (!$vars['show_blocks']) {
  $vars['left'] = '';
  $vars['right'] = '';
}

// Add conditional stylesheets (for Internt Explorer).
if (!module_exists('conditional_styles')) {
  $vars['styles'] .= $vars['conditional_styles'] = variable_get('conditional_styles_'. $GLOBALS['theme'], '');
}

// Set variables for the logo and site_name.
if (!empty($vars['logo'])) {
  // Return the site_name even when site_name is disabled in theme settings.
  $vars['logo_alt_text'] = variable_get('site_name', '');
  $vars['linked_site_logo'] = '<a href="'. $vars['front_page'] .'" title="'. t('Home page') .'" rel="home"><img src="'. $vars['logo'] .'" alt="'. $vars['logo_alt_text'] .' '. t('logo') .'" /></a>';
}
if (!empty($vars['site_name'])) {
  $vars['linked_site_name'] = '<a href="'. $vars['front_page'] .'" title="'. t('Home page') .'" rel="home">'. $vars['site_name'] .'</a>';
}

// Set variables for the primary and secondary links.
if (!empty($vars['primary_links'])) {
  $vars['primary_menu'] = theme('links', $vars['primary_links'], array('class' => 'primary-links clear-block'));
}
if (!empty($vars['secondary_links'])) {
  $vars['secondary_menu'] = theme('links', $vars['secondary_links'], array('class' => 'secondary-links clear-block'));
}
  
// Admin welcome message with date for the admin theme.
global $user;
$welcome = t('Welcome') .' '. $user->name;
$conjunction = ', '. t('it\'s') .' ';
$todays_date = date("l, F d Y" , time()); 
$vars['admin_welcome'] = $welcome . $conjunction . $todays_date;

// Attribution.
$vars['attribution'] = "<div id=\"attribution\"><a href=\"http://adaptivethemes.com\">Premium Drupal Themes</a></div>"  ;

// Section class. The section class is printed on the body element and allows you to theme site sections.
// We use the path alias otherwise all nodes will be in "section-node".
$path_alias = drupal_get_path_alias($_GET['q']);
if (!$vars['is_front']) {
  list($section, ) = explode('/', $path_alias, 2);
  $vars['section_class'] = ' class="'. safe_string('section-'. $section) .'"';
}

// Body Classes. In Genesis these are printed on the #container wrapper div, not on the body.
$classes = explode(' ', $vars['body_classes']);

// Remove the useless page-arg(0) class.
if ($class = array_search(preg_replace('![^abcdefghijklmnopqrstuvwxyz0-9-]+!s', '', 'page-'. drupal_strtolower(arg(0))), $classes)) {
  unset($classes[$class]);
}

/** 
 * Optional Region body classes
 * Uncomment the following if you need to set a body class for each active region.
 */
/*        
if (!empty($vars['leaderboard'])) {
  $classes[] = 'leaderboard';
}
if (!empty($vars['header'])) {
  $classes[] = 'header-blocks';
}
if (!empty($vars['content_aside'])) {
  $classes[] = 'content-aside';
}
if (!empty($vars['secondary_content'])) {
  $classes[] = 'secondary-content';
}
if (!empty($vars['tertiary_content'])) {
  $classes[] = 'tertiary-content';
}
if (!empty($vars['footer'])) {
 $classes[] = 'footer';
}
*/

/**
 * Additional body classes to help out themers.
 */
if (!$vars['is_front']) {
  // Set classes based on Drupals internal path, e.g. page-node-1. 
  // Using the alias is fragile because path alias's can change, $normal_path is more reliable.
  $normal_path = drupal_get_normal_path($_GET['q']);
  $classes[] = safe_string('page-'. $normal_path);
  if (arg(2) == 'block') {
    $classes[] = 'page-block';
  }
  if (arg(0) == 'node') {
    if (arg(1) == 'add') {
      $classes[] = 'page-node-add'; // Add .node-add class.
    }
    elseif (is_numeric(arg(1)) && (arg(2) == 'edit' || arg(2) == 'delete')) {
      $classes[] = 'page-node-'. arg(2); // Add .node-edit or .node-delete classes.
    }
  }
}
$vars['classes'] = implode(' ', $classes); // Concatenate with spaces.
  
// THEME SETTINGS
// Display mission statement on all pages
if (theme_get_setting('mission_statement_pages') == 'all') {
  $vars['mission'] = theme_get_setting('mission', FALSE);  
}

// Layout settings - set the page width and layout method
if (theme_get_setting('layout_enable_settings') == 'on') {
  $vars['at_layout_width'] = theme_get_setting('layout_width');
  $method = theme_get_setting('layout_method');
  $right_width = theme_get_setting('layout_sidebar_last_width');
  $left_width = theme_get_setting('layout_sidebar_first_width');
  if ($method == '0') {
    $content_margin_right = $right_width;
    $content_margin_left  = $left_width;
    $pull_right           = $right_width;
    $styles = array();
    $styles[] = '.two-sidebars .content-inner{margin:0 '. $content_margin_right .'px 0 '. $content_margin_left .'px;}';
    $styles[] = '.sidebar-left .content-inner{margin-left:'. $content_margin_left .'px;;}';
    $styles[] = '.sidebar-right .content-inner{margin-right:'. $content_margin_right .'px;;}';
    $styles[] = '#sidebar-first{width:'. $left_width .'px;margin-left:-100%;}';
    $styles[] = '#sidebar-last{width:'. $right_width .'px;margin-left:-' .$pull_right .'px;}';
  }
  if ($method == '1') {
    $content_margin       = $right_width + $left_width;
    $content_margin_right = $right_width;
    $content_margin_left  = $left_width;
    $left_margin          = $right_width + $left_width;
    $right_margin         = $right_width;
    $styles = array();
    $styles[] = '.two-sidebars .content-inner{margin-right:'. $content_margin .'px;}';
    $styles[] = '.sidebar-left .content-inner{margin-right:'. $content_margin_left .'px;}';
    $styles[] = '.sidebar-right .content-inner{margin-right:'. $content_margin_right .'px;}';
    $styles[] = '#sidebar-first{width:'. $left_width .'px;margin-left:-'. $left_margin .'px;}';
    $styles[] = '#sidebar-last{width:'. $right_width .'px;margin-left:-'. $right_margin .'px;}';
    $styles[] = '.sidebar-left #sidebar-first{width:'. $left_width .'px;margin-left:-'. $left_width .'px;}';
  }
  if ($method == '2') {
    $content_margin = $right_width + $left_width;
    $left_margin    = $right_width + $left_width;
    $right_margin   = $right_width;
    $push_right     = $left_width;
    $styles = array();
    $styles[] = ".two-sidebars .content-inner{margin:0 0 0 $content_margin\px;}";
    $styles[] = ".sidebar-left .content-inner{margin-left:$left_margin\px;}";
    $styles[] = ".sidebar-right .content-inner{margin-left:$right_margin\px;}";
    $styles[] = "#sidebar-first{width:$left_width\px; margin-left: -100%;}";
    $styles[] = "#sidebar-last{width:$right_width\px; margin-left: -100%;}";
    $styles[] = ".two-sidebars #sidebar-last {width:$right_width\px; position: relative; left:$push_right\px;}";
  }
  $vars['at_layout'] = implode('', $styles);
}
  
// Template suggestions for admin theme
if (!$vars['is_front']) {
  if (theme_get_setting('at_admin_theme')) {
    if ((arg(0) == 'admin')) {
      $vars['template_files'][] = 'page-at_admin';
    }
    if (arg(2) == 'block') {
      $vars['template_files'][] = 'page';
    }
  }
  if (theme_get_setting('at_admin_theme_node')) {
    if ((arg(0) == 'node' && arg(1) == 'add') || (is_numeric(arg(1)) && (arg(2) == 'edit' || arg(2) == 'delete'))) {
      $vars['template_files'][] = 'page-at_admin';
    }
  }
}

/**
 * Load the admin theme CSS file.
 */
if (theme_get_setting('at_admin_theme') || theme_get_setting('at_admin_theme_node')) {
  if ((arg(0) == 'admin') || (arg(0) == 'node' && arg(1) == 'add') || (is_numeric(arg(1)) && (arg(2) == 'edit' || arg(2) == 'delete'))) {
    drupal_add_css(path_to_theme() .'/css/core/at-admin.css', 'theme', 'all');
  }
}

// Unset stuff to make theming easier.
if (theme_get_setting('at_admin_theme') || theme_get_setting('at_admin_theme_node')) {
  if ((arg(0) == 'admin') || (arg(0) == 'node' && arg(1) == 'add') || (is_numeric(arg(1)) && (arg(2) == 'edit' || arg(2) == 'delete'))) {
    // Unset vars
    unset($vars['primary_menu']);
    unset($vars['secondary_menu']);
    // Check whether logo is enabled for admin pages.
    if (theme_get_setting('at_admin_theme_logo') == 0) {
      unset($vars['linked_site_logo']);
    }
   // Check whether help is disabled
    if (theme_get_setting('at_admin_hide_help')) {
      unset($vars['help']);
    }
    // Unset CSS
    $css = drupal_add_css();
    $core_styles = array('dev.css', 'dark.css', 'starter.css');
    $path_to_core = path_to_theme() .'/css/core/';
    foreach ($core_styles as $stylesheet) {
      $file = $path_to_core . $stylesheet;
      unset($css['all']['theme'][$file]);
    }
    // Comment out the following if using the array/foreach loop below it to unset custom stylesheets.
    $theme_styles = path_to_theme() .'/css/theme/theme.css';
    // Uncomment and add to this array if you need to unset additional theme CSS files. If you don't it could mess up the admin themes.
    // $theme_styles = array('theme.css');
    // $path_to_theme = path_to_theme() .'/css/theme/';
    // foreach ($theme_styles as $stylesheet) {
    //   $file = $path_to_theme . $stylesheet;
    //   unset($css['all']['theme'][$file]);
    // }
    unset($css['all']['theme'][$theme_styles]);
    $vars['styles'] = drupal_get_css($css);
    return $vars;
  }
}
