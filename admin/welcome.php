<?php
	//	No direct calls to this script
	if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('No direct calls allowed!'); }
		
	//	Calculate the number of entries
	$checkedEntries_result = mysql_query("
		SELECT entry_id
		FROM
			" . $wpdb->prefix . "gwolle_gb_entries
		WHERE
			entry_isChecked = '1'
			AND
			entry_isDeleted = '0'
			AND
			entry_isSpam != '1'
	");
	$count['checked'] = mysql_num_rows($checkedEntries_result);
	
	$uncheckedEntries_result = mysql_query("
		SELECT entry_id
		FROM
			" . $wpdb->prefix . "gwolle_gb_entries
		WHERE
			entry_isChecked != '1'
			AND
			entry_isDeleted = '0'
			AND
			entry_isSpam != '1'
	");
	$count['unchecked'] = mysql_num_rows($uncheckedEntries_result);
	
	$spamEntries_result = mysql_query("
		SELECT entry_id
		FROM
			" . $wpdb->prefix . "gwolle_gb_entries
		WHERE
			entry_isSpam = '1'
			AND
			entry_isDeleted = '0'
	");
	$count['spam'] = mysql_num_rows($spamEntries_result);
	
	$count['all'] = $count['checked'] + $count['unchecked'] + $count['spam'];
?>

<div class="wrap">
	<div id="icon-gwolle-gb"><br /></div>
	<h2><?php _e('Guestbook',$textdomain); ?></h2>
	
	<?php if ($_REQUEST['msg'] == 'no-permission') { ?>
<!-- 		<div id="message" class="updated fade"><p><strong><?php _e('Error',$textdomain); ?>:</strong>&nbsp;<?php _e("You don't have the privileges to perform this action.",$textdomain); ?></p></div> -->
	<?php }
	elseif ($_REQUEST['msg'] == 'check-akismet-configuration') {
		echo '<div id="message" class="error fade"><p><strong>' . __('Error',$textdomain) . ':</strong> ' . __('Please check your Akismet configuration.',$textdomain) . '</p></div>';
	}
	elseif ($_REQUEST['msg'] == 'akismet-not-activated') {
		echo '<div id="message" class="error fade"><p><strong>' . __('Error',$textdomain) . ':</strong> ' . str_replace('%1','admin.php?page=gwolle-gb/settings.php',__('Please <a href="%1">enable Akismet</a> to use the spam feature of Gwolle-GB.',$textdomain)) . '</p></div>';
	}
	elseif ($_REQUEST['msg'] == 'successfully-uninstalled') {
    echo '<div id="message" class="updated fade"><p>'.__('<strong>Gwolle-GB has been successfully uninstalled.</strong> Thanks for using my plugin!',$textdomain).'</p></div>';
  }
	?>
	
	<div id="dashboard-widgets-wrap" class="gwolle_gb-overview">
    <div id="dashboard-widgets" class="metabox-holder">
      <div id="post-body">
        <div id="dashboard-widgets-main-content">
          <div class="postbox-container" style="width:49%;">
            <div id='left-sortables' class='meta-box-sortables'>
              <div id="dashboard_right_now" class="postbox " >
                <div class="handlediv" title="Klicken zum Umschalten"><br /></div>
                <h3 class='hndle'><span><?php _e('Welcome to the Guestbook!',$textdomain); ?></span></h3>
                <div class="inside">
                  <p class="sub"><?php _e('Overview',$textdomain); ?></p>
                  <div class="table">
                  	<table>
											<tbody>
												<tr class="first">
													<td class="first b"><a href="admin.php?page=gwolle-gb/entries.php">
														<?php echo $count['all']; ?>
													</a></td>
													<td class="t">
														<?php
															if ($count['all']==1) {
																_e('entry total',$textdomain);
															}
															else {
																_e('entries total',$textdomain);
															}
														?>
													</td>
													<td class="b"></td>
													<td class="last"></td>
												</tr>
												<tr>
													<td class="first b"><a href="admin.php?page=gwolle-gb/entries.php&amp;show=checked">
														<?php echo $count['checked']; ?>
													</a></td>
													<td class="t" style="color:#008000;">
														<?php
															if ($count['checked'] == 1) {
																_e('unlocked entry',$textdomain);
															}
															else {
																_e('unlocked entries',$textdomain);;
															}
														?>
													</td>
													<td class="b"></td>
													<td class="last"></td>
												</tr>
												<tr>
													<td class="first b"><a href="admin.php?page=gwolle-gb/entries.php&amp;show=unchecked">
														<?php echo $count['unchecked']; ?>
													</a></td>
													<td class="t" style="color:#FFA500;">
														<?php
															if ($count['unchecked'] == 1) {
																_e('new entry',$textdomain);
															}
															else {
																_e('new entries',$textdomain);
															}
														?>
													</td>
													<td class="b"></td>
													<td class="last"></td>
												</tr>
												<tr>
													<td class="first b"><a href="admin.php?page=gwolle-gb/entries.php&amp;show=spam">
														<?php echo $count['spam']; ?>
													</a></td>
													<td class="t" style="color:#FF0000;">
														<?php
															if ($count['spam'] == 1) {
																_e('spam entry',$textdomain);
															}
															else {
																_e('spam entries',$textdomain);
															}
														?>
													</td>
													<td class="b"></td>
													<td class="last"></td>
												</tr>
											</tbody>
										</table>
                  </div><!-- Table-DIV -->
                  <div class="versions">
      							<p>
      								<a class="button rbutton" href="admin.php?page=gwolle-gb/editor.php"><strong><?php _e('Write admin entry',$textdomain); ?></strong></a>
      								<?php _e('Here you can manage the entries.',$textdomain); ?>
      							</p>
  									<span>
  										<?php echo str_replace('%1','<strong>' . $userLevelNames[(int)get_option('gwolle_gb-access-level')] . '</strong>',__('Only users with the role %1 have access to the guestbook backend.',$textdomain)); ?>
  									</span>
  								</div>
                </div><!-- 'inside'-DIV -->
              </div><!-- 'dashboard_right_now'-DIV -->
              <div id="ngg_lastdonators" class="postbox " >
                <div class="handlediv" title="Klicken zum Umschalten"><br /></div>
                <h3 class='hndle'><span><?php _e('This plugin uses the following scripts/programs/images:',$textdomain); ?></span></h3>
                <div class="inside">
                  <div id="dashboard_server_settings" class="dashboard-widget-holder">
                    <div class="ngg-dashboard-widget">
                      <div class="dashboard-widget-content">
                	  		<ul class="settings">
      										<li><a href="http://akismet.com/tos/" target="_blank">Akismet</a></li>
      										<li><a href="http://miphp.net/blog/view/php4_akismet_class" target="_blank">Akismet PHP4-<?php _e('Class by',$textdomain); ?> Bret Kuhns</a></li>
      										<li><a href="http://www.achingbrain.net/stuff/php/akismet" target="_blank">Akismet PHP5-<?php _e('Class by',$textdomain); ?> Alex</a></li>
      										<li><a href="http://philipwilson.de/" target="_blank"><?php _e('Icons by',$textdomain); ?> Philip Wilson</a></li>
      										<li><a href="http://recaptcha.net/aboutus.html" target="_blank">Recaptcha</a></li>
      									</ul>
                		  </div><!-- 'dashboard-widget-content'-DIV -->
                    </div><!-- 'ngg-dashboard-widget'-DIV -->
                  </div><!-- 'dashboard_server_settings'-DIV -->
                </div><!-- 'inside'-DIV -->
              </div><!-- 'ngg_lastdonators'-DIV -->
            </div><!-- 'left_sortables'-DIV -->
          </div><!-- 'postbox-container'-DIV -->
          <div class="postbox-container" style="width:49%;">
				    <div id='right-sortables' class='meta-box-sortables'>
              <div id="dashboard_primary" class="postbox " >
                <div class="handlediv" title="Klicken zum Umschalten"><br /></div>
                <h3 class='hndle'><span><?php _e('Help',$textdomain); ?></span></h3>
                <div class="inside">
                  <?php _e('This is how the guestbook will be displayed on your page',$textdomain); ?>:
                  <br><br>
									<ul>
										<li><?php _e('Create a new article or page.',$textdomain); ?></li>
										<li><?php _e("Choose a heading (doesn't matter) and set &quot;[gwolle-gb]&quot; (without the quotes) as the content.", $textdomain); ?></li>
										<li><?php _e("Of course, you should disable comments on that new content; otherwise, your visitors might get a little confused. ;)",$textdomain); ?></li>
									</ul>
                </div><!-- 'inside'-DIV -->
              </div><!-- 'dashboard_primary'-DIV -->
            </div><!-- 'right-sortables'-DIV -->
          </div><!-- 'postbox-container'-DIV -->
        </div><!-- 'dashboard-widgets-main-content'-DIV -->
      </div><!-- 'post-body'-DIV -->
    </div><!-- 'dashboard-widgets'-DIV -->
  </div><!-- 'dashboard-widgets-wrap'-DIV -->