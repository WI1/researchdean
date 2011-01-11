<?php
// $Id: user-profile.tpl.php,v 1.2.2.1 2008/10/15 13:52:04 dries Exp $

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
 *
 * @code
 *   print '<pre>'. check_plain(print_r($profile, 1)) .'</pre>';
 * @endcode
 *
 * @see user-profile-category.tpl.php
 *   Where the html is handled for the group.
 * @see user-profile-field.tpl.php
 *   Where the html is handled for each item in the group.
 *
 * Available variables:
 *	 - $account: Alle Profilvariablen des aktuellen profils
 *   - $user_profile: All user profile data. Ready for print.
 *   - $profile: Keyed array of profile categories and their items or other data
 *     provided by modules.
 *
 * @see template_preprocess_user_profile()
 */
 
$node = $content_profile->get_variables('profilewi1');
?>
<?php 
$fullname = $node['field_academic_title'][0]['value'];
$fullname .= ' ' . $account->profile_firstname;
$fullname .= ' ' . $account->profile_middlename;
$fullname .= ' ' . $account->profile_lastname;

drupal_set_title($fullname); ?>
<div  class="profileContainer vcard card">
	<div class="profilePicture grid_2"><?php echo $profile['user_picture']; ?></div>
	<div class="info grid_6">
    <p class="profileVita"><?php print $node['field_vita'][0]['value']; ?></p>
	</div>
</div>

<div class="clear"></div>

<div class="contact profile block grid_6">
		<?php if ($account->profile_usergroup != 'Student Assistants' or $account->profile_usergroup != 'Visiting Professors'): ?>
		<h2><?php print "Contact" ?></h2>
		<span class="email"><?php print spamspan($node['field_wi_email'][0]['email']); ?></span><br />
		<span class="telephone"><?php print $node['field_tel'][0]['value']; ?></span><br />
		<span class="room">Room <?php print $node['field_room'][0]['value']; ?></span><br /><br />
		
		<span class="street-address"><?php print $account->addresses['street']; ?></span><br />
        <span class="postal-code"><?php print $account->addresses['postal_code']; ?></span> 
		<span class="locality"><?php print $account->addresses['city']; ?></span><br />
        <span class="country"><?php print $account->country; ?></span>
		<?php endif; ?>
	<?php print views_embed_view('team_contact_profile','block_3',$account->name); ?>
</div>

<?php if ($account->profile_usergroup == 'Assistant Professors & Post-Docs' or $account->profile_usergroup == 'Research Associates and Doctoral Students') {
	// Projekte
	// ==========
	print "<div id=\"research-projects\" class=\"projects profile block grid_3\">";
	print "<h2>Research projects</h2>";
	
	// Outputs the research projects a user is participating in from the projects view
	print views_embed_view('projects_userprofile', 'block_1', $account->name);
	print "</div>";
	print "<div class=\"clear\"></div>";
	}
?>

<?php if ($account->profile_usergroup == 'Chair' or $account->profile_usergroup == 'Assistant Professors & Post-Docs' or $account->profile_usergroup == 'Research Associates and Doctoral Students'): ?>
	<!-- Kurse -->
	<div id="tabs-courses" class="profile block grid_6">
	<h2>Courses</h2>
	<!-- Outputs the courses a user is giving by using the courses view -->
	<?php print views_embed_view('courses_profile', 'default', $account->name); ?>
	</div>
<?php endif; ?>

<?php if ($account->profile_usergroup == 'Assistant Professors & Post-Docs' or $account->profile_usergroup == 'Research Associates and Doctoral Students') {
	// Events
	// ==========
	print "<div id=\"tabs-events\" class=\"profile block presentations grid_3\">";
	print "<h2>Events</h2>";
		
	// Outputs a view with calendar items associated with the current user
	// print views_embed_view('presentations', 'page_5');
	print "</div>";
	}
?>

<div class="clear"></div>

<?php if ($account->profile_usergroup != 'Student Assistants' and $account->profile_usergroup != 'Office Management' ): ?>
<div id="tabs-publications" class="profile block publications">
	<h2>Publications</h2>	
		<?php
		//print_r($profile);
		// Outputs user's publications from the standard biblio view
		$view_name = 'biblio_views';
		$display = 'page_5';
		//print_r($account);
		print views_embed_view($view_name, $display, $account->biblio_contributor_id);
		?>		
</div>
<?php endif; ?>
<?php if ($account->profile_usergroup == 'Research Associates and Doctoral Students'): ?>
<h2>Dissertation Topic</h2>
<div id="dissertation-abstract">
<?php print $node['field_dissertation_abstract'][0]['value']; ?>
</div>
<?php endif; ?>