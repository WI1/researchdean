<?php
// $Id: user-profile.tpl.php,v 1.2.2.2 2009/10/06 11:50:06 goba Exp $

/**
 * @file user-profile.tpl.php
 * Default theme implementation to present all user profile data.
 *
 * This template is used when viewing a registered member's profile page,
 * e.g., example.com/user/123. 123 being the users ID.
 *
 * By default, all user profile data is printed out with the $user_profile
 * variable. If there is a need to break it up you can use $profile instead.
 * It is keyed to the name of each category or other data attached to the
 * account. If it is a category it will contain all the profile items. By
 * default $profile['summary'] is provided which contains data on the user's
 * history. Other data can be included by modules. $profile['user_picture'] is
 * available by default showing the account picture.
 *
 * Also keep in mind that profile items and their categories can be defined by
 * site administrators. They are also available within $profile. For example,
 * if a site is configured with a category of "contact" with
 * fields for of addresses, phone numbers and other related info, then doing a
 * straight print of $profile['contact'] will output everything in the
 * category. This is useful for altering source order and adding custom
 * markup for the group.
 *
 * To check for all available data within $profile, use the code below.
 * @code
 *   print '<pre>'. check_plain(print_r($profile, 1)) .'</pre>';
 * @endcode
 *
 * Available variables:
 *   - $user_profile: All user profile data. Ready for print.
 *   - $profile: Keyed array of profile categories and their items or other data
 *     provided by modules.
 *
 * @see user-profile-category.tpl.php
 *   Where the html is handled for the group.
 * @see user-profile-item.tpl.php
 *   Where the html is handled for each item in the group.
 * @see template_preprocess_user_profile()
 */
//adds JS for collapsible fieldset
drupal_add_js('misc/collapse.js');
drupal_add_js('misc/drupal.js');

$pub_view_name = 'biblio_views';
$pub_view_display = 'page_5';

$node = $content_profile->get_variables('nutzerprofil');
//drupal_set_message('<pre>' . print_r($account, TRUE) . '</pre>');

$url = drupal_get_path_alias($_GET['q']);
$query = "args=" . $account->uid;
if (drupal.jsenabled && !empty($_GET['args']) == false ) {
drupal_goto($url, $query);
}
?>

<div class="profile">
  <div  class="profileContainer vcard card">
    <div class="profilePicture grid_3"><?php echo $profile['user_picture']; ?></div>
    <div class="info grid_6">
      <?php if ($node['field_junprof'][0]['value'] == 1):?>
	  <h3>Juniorprofessur</h3>
      <?php else: ?>
         <h3>Lehrstuhl</h3>
      <?php endif; ?>
      <p class="profileLink"><?php 
        $text = content_format('field_chair', $node['field_chair'][0]);	  
	  print l($text, $node['field_webseite_ls'][0]['url']); ?></p>
	  
	  <?php 
             $related_groups = $account->og_groups;
             if($related_groups): ?>
	     <?php  if (count($related_groups) > 1): ?>
	     <h3>Forschungsfelder</h3>
             <?php else: ?>
             <h3>Forschungsfeld</h3>
             <?php endif; ?>  
		<?php 
		// Quick fix for the problem, should actually be put into a function with the possibility to set different arguments for each group node type
		foreach($related_groups as $group) {
		  $group = (object) $group;
		  if ($group->type == 'forschungsfeld') {
			 print '<div class="group-list-item">' . phptemplate_group_list_item($group) . '</div>';
			}
		}
		?>
	  <?php endif; ?>
	  

    </div>
  </div>
<div class="clear"></div>
<div class="publications">
<h3>Publikationen</h3>
	  <?php 
	  //Embed codes for Person and Chair
	  if (module_exists('web_widgets')): ?>
		<?php $style = 'inline';
			  $path_chair = $base_url . '/widgets/chairs/' . $node['field_chair'][0]['value'] . '/widget_byyear';
			  $path_person = $base_url . '/widgets/users/' . $account->uid . '/widget_bytype';
		if (user_edit_access($account)): ?>
		<fieldset class="collapsible collapsed">	
			<legend>Meine Embed Codes f&uuml;r Publikationen</legend>
			<p>Nur f&uuml;r Sie: Javascript-Code zum Einbinden Ihrer Publikationen und derer Ihres Lehrstuhls in externe Webseiten.</p> 
			
			<h3>Meine pers&ouml;nlichen Publikationen</h3>
		    <?php print web_widgets_render_embed_code($style, $path_person, $width, $height); ?>
			
			<h3>Publikationen meines Lehrstuhls/meiner Professur</h3>
			<?php print web_widgets_render_embed_code($style, $path_chair, $width, $height); ?>
		 </fieldset>   
		<?php endif; ?>
		
	<?php endif; ?>

 <?php
   if(!empty($_GET['args'])) {
        $exhibitArgumentsUrl = 'publications-user';
        $nid = arg(1, drupal_get_normal_path($exhibitArgumentsUrl));
        //module_invoke('exhibit', 'block', 'view', 'facets');
        print node_view(node_load($nid));
        
      } else {
        print views_embed_view($pub_view_name, $pub_view_display, $account->name);
      }
	?>
 </div>
</div>
