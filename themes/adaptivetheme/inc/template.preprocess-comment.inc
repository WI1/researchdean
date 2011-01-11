<?php // $Id: template.preprocess-comment.inc,v 1.2 2009/08/25 03:49:57 jmburnz Exp $
// adaptivethemes.com
/**
 * Override or insert variables in comment templates.
 *
 * @param $vars
 *   A sequential array of variables to pass to the theme template.
 * @param $hook
 *   The name of the theme function being called.
 */
global $user;

// Special classes for comments, emulates Drupal 7 for forward compatibility.
// Load the node object that the current comment is attached to.
$node = node_load($vars['comment']->nid);
$classes = array();
$classes[]  = 'comment';
if ($vars['status'] != 'comment-published') {
  $classes[] = $vars['status'];
}
else {
  if ($vars['comment']->uid == 0) {
    $classes[] = 'comment-by-anonymous';
  }
  if ($vars['comment']->uid === $vars['node']->uid) {
    $classes[] = 'comment-by-node-author';
  }
  if ($vars['comment']->uid === $vars['user']->uid) {
    $classes[] = 'comment-by-viewer';
  }
  if ($vars['comment']->new) {
    $classes[] = 'comment-new';
  }
  $classes[] = $vars['zebra'];
}
$vars['classes'] = implode(' ', $classes);

// If comment subjects are disabled, don't display them.
if (variable_get('comment_subject_field', 1) == 0) {
  $vars['title'] = '';
}
  
// Date & author
$submitted_by = t('by ') .'<span class="comment-name">'.  theme('username', $vars['comment']) .'</span>';
$submitted_by .= t(' - ') .'<span class="comment-date">'.  format_date($vars['comment']->timestamp, 'small') .'</span>';     // Format date as small, medium, or large
$vars['submitted'] = $submitted_by;

// Set messages if comment is unpublished.
if ($vars['comment']->status == COMMENT_NOT_PUBLISHED) {
  drupal_set_message(t('Comment #!id !title is currently unpublished', array('!id' => $vars['id'], '!title' => $vars['title'])), 'warning');
  $vars['unpublished'] = '<span class="unpublished">'. t('Unpublished') .'</span>';
}
