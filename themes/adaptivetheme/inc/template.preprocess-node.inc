<?php // $Id: template.preprocess-node.inc,v 1.2 2009/08/25 03:49:57 jmburnz Exp $
// adaptivethemes.com
/**
 * Override or insert variables into the node templates.
 *
 * @param $vars
 *   A sequential array of variables to pass to the theme template.
 * @param $hook
 *   The name of the theme function being called.
 */
global $user;
global $theme;
 
// Set the node id.
$vars['node_nid'] = 'node-'. $vars['node']->nid;

// Special classes for nodes. Emulates Drupal 7 node classes for forward compatibility.
$classes = array();
$classes[] = 'node';
if ($vars['promote']) {
  $classes[] = 'node-promoted';
}
if ($vars['sticky']) {
  $classes[] = 'node-sticky';
}
if (!$vars['status']) {
  $classes[] = 'node-unpublished';
}
if ($vars['teaser']) {
  // Node is displayed as teaser.
  $classes[] = 'node-teaser';
}
if (isset($vars['preview'])) {
  $classes[] = 'node-preview';
}
// Add support for Skinr module classes http://drupal.org/project/skinr
if (function_exists('node_skinr_data')) {
  $classes[] = $vars['skinr'];
}
// Class for node type: "node-type-page", "node-type-story", "node-type-my-custom-type", etc.
$classes[] = 'node-'. $vars['node']->type;
$vars['classes'] = implode(' ', $classes); // Concatenate with spaces.
  
// Add node_bottom region content.
$vars['node_bottom'] = theme('blocks', 'node_bottom');

// Set messages if node is unpublished.
if (!$vars['node']->status) {
  if ($vars['page']) {
    drupal_set_message(t('%title is currently unpublished', array('%title' => $vars['node']->title)), 'warning'); 
  }
  else {
    $vars['unpublished'] = '<span class="unpublished">'. t('Unpublished') .'</span>';
  }
}
  
// Node Theme Settings
// Date & author
if (!function_exists('submitted_by_node_submitted')) {
  $date = t('Posted ') . format_date($vars['node']->created, 'medium');                 // Format date as small, medium, or large
  $author = theme('username', $vars['node']);
  $author_only_separator = t('Posted by ');
  $author_date_separator = t(' by ');
  $submitted_by_content_type = (theme_get_setting('submitted_by_enable_content_type') == 1) ? $vars['node']->type : 'default';
  $date_setting = (theme_get_setting('submitted_by_date_'. $submitted_by_content_type) == 1);
  $author_setting = (theme_get_setting('submitted_by_author_'. $submitted_by_content_type) == 1);
  $author_separator = ($date_setting) ? $author_date_separator : $author_only_separator;
  $date_author = ($date_setting) ? $date : '';
  $date_author .= ($author_setting) ? $author_separator . $author : '';
  $vars['submitted'] = $date_author;
}

// Taxonomy
$taxonomy_content_type = (theme_get_setting('taxonomy_enable_content_type') == 1) ? $vars['node']->type : 'default';
$taxonomy_display = theme_get_setting('taxonomy_display_'. $taxonomy_content_type);
$taxonomy_format = theme_get_setting('taxonomy_format_'. $taxonomy_content_type);
if ((module_exists('taxonomy')) && ($taxonomy_display == 'all' || ($taxonomy_display == 'only' && $vars['page']))) {
  $vocabularies = taxonomy_get_vocabularies($vars['node']->type);
  $output = '';
  $vocab_delimiter = '';
  foreach ($vocabularies as $vocabulary) {
    if (theme_get_setting('taxonomy_vocab_display_'. $taxonomy_content_type .'_'. $vocabulary->vid) == 1) {
      $terms = taxonomy_node_get_terms_by_vocabulary($vars['node'], $vocabulary->vid);
      if ($terms) {
        $output .= ($taxonomy_format == 'vocab') ? '<li class="vocab vocab-'. $vocabulary->vid .'"><span class="vocab-name">'. $vocabulary->name .':</span> <ul class="vocab-list">' : '';
        $links = array();
        foreach ($terms as $term) {        
          $links[] = '<li class="vocab-term">'. l($term->name, taxonomy_term_path($term), array('attributes' => array('rel' => 'tag', 'title' => strip_tags($term->description)))) .'</li>';        
        }
        if ($taxonomy_format == 'list') {
          $output .= $vocab_delimiter;    // Add comma between vocabularies
          $vocab_delimiter = ', ';        // Use a comma delimiter after first displayed vocabulary
        }
        $output .= implode(", ", $links);
        $output .= ($taxonomy_format == 'vocab') ? '</ul></li>' : '';
      }
    }
  }
  if ($output != '') {
    $output = '<ul class="taxonomy">'. $output .'</ul>';
  }
  $vars['terms'] = $output;
}
else {
  $vars['terms'] = '';
}

 
