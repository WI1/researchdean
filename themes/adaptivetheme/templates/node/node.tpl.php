<?php // $Id: node.tpl.php,v 1.3 2009/08/23 18:06:16 jmburnz Exp $
// adaptivethemes.com

/**
 * @file node.tpl.php
 * Theme implementation to display a node.
 *
 * Available variables:
 * - $title: the (sanitized) title of the node.
 * - $content: Node body or teaser depending on $teaser flag.
 * - $picture: The authors picture of the node output from
 *   theme_user_picture().
 * - $date: Formatted creation date (use $created to reformat with
 *   format_date()).
 * - $links: Themed links like "Read more", "Add new comment", etc. output
 *   from theme_links().
 * - $name: Themed username of node author output from theme_user().
 * - $node_url: Direct url of the current node.
 * - $terms: the themed list of taxonomy term links output from theme_links().
 * - $submitted: themed submission information output from
 *   theme_node_submitted().
 *
 * Other variables:
 * - $node: Full node object. Contains data that may not be safe.
 * - $type: Node type, i.e. story, page, blog, etc.
 * - $comment_count: Number of comments attached to the node.
 * - $uid: User ID of the node author.
 * - $created: Time the node was published formatted in Unix timestamp.
 * - $zebra: Outputs either "even" or "odd". Useful for zebra striping in
 *   teaser listings.
 * - $id: Position of the node. Increments each time it's output.
 *
 * Helper variables:
 * - $node_nid: Outputs a unique id for each node.
 * - $classes: Outputs dynamic classes for advanced themeing.
 *
 * Node status variables:
 * - $teaser: Flag for the teaser state.
 * - $page: Flag for the full page state.
 * - $promote: Flag for front page promotion state.
 * - $sticky: Flags for sticky post setting.
 * - $status: Flag for published status.
 * - $comment: State of comment settings for the node.
 * - $readmore: Flags true if the teaser content of the node cannot hold the
 *   main body content.
 * - $is_front: Flags true when presented in the front page.
 * - $logged_in: Flags true when the current user is a logged-in member.
 * - $is_admin: Flags true when the current user is an administrator.
 *
 * @see template_preprocess()
 * @see template_preprocess_node()
 * @see genesis_preprocess_node()
 */
?>
<div id="<?php print $node_nid; ?>" class="<?php print $classes; ?>">
    <?php if (!$page): ?>
      <h3 class="node-title">
        <a href="<?php print $node_url; ?>" rel="bookmark"><?php print $title; ?></a>
        <?php print $unpublished; ?>
      </h3>
    <?php endif; ?>


    <?php print $picture; ?>

    <div class="node-content"><?php print $content; ?></div>

    <?php if ($terms): ?>
      <div class="node-terms"><?php print $terms; ?></div>
    <?php endif; ?>
    
   <?php if ($submitted && $links): ?>
		<div class="node-links node-submitted">
	      <?php //print $submitted; ?>
          
           <?php if (!drupal_is_front_page()): ?>
	      		<?php // print $links; ?>
	       <?php endif; ?>
         
         
         </div>
    <?php endif; ?>
   
    <?php if ($node_bottom && !$teaser): ?>
      <div id="node-bottom">
        <?php print $node_bottom; ?>
      </div>
    <?php endif; ?>

</div> <!-- /node -->