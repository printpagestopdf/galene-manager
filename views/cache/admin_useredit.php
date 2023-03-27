<?php class_exists('Galmgr_View_Generator') or exit; ?>

<section>
<nav class="navbar mb-3" role="navigation" aria-label="main navigation">
  <div class="navbar-brand">
	  <div href='#' class="navbar-item">
	  <a class="navbar-item" href="<?php echo get_permalink() ?>" >
		  <img  src="<?php echo esc_url(GALMGR_PLUGIN_URL . '/assets/videocall-pngrepo-com.png') ?>" >
		  </a>
		</div>
	<a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false" data-target="main_menu">
	  <span aria-hidden="true"></span>
	  <span aria-hidden="true"></span>
	  <span aria-hidden="true"></span>
	</a>

  </div>
  <div id="main_menu" class="navbar-menu">
    <div class="navbar-start">
		<?php if( @$is_admin === true): ?>
      <a class="navbar-item is-tab <?php echo sanitize_html_class(@$d['admin_screen_roomsettings']) ?>" href="<?php echo esc_url(Galmgr_util::add_arg([ 'galene_action' => 'admin_screen_roomsettings' ])) ?>">
        <?php echo esc_html(__("Room settings",'manager-for-galene-videoconference')) ?>
      </a>
      <a class="navbar-item is-tab <?php echo sanitize_html_class(@$d['admin_screen_usersettings']) ?>" href="<?php echo esc_url(Galmgr_util::add_arg([ 'galene_action' => 'admin_screen_usersettings' ])) ?>">
        <?php echo esc_html(__("Users",'manager-for-galene-videoconference')) ?>
      </a>
      <a class="navbar-item is-tab <?php echo sanitize_html_class(@$d['admin_screen_roommgr']) ?>" href="<?php echo esc_url(Galmgr_util::add_arg([ 'galene_action' => 'admin_screen_settings' ])) ?>">
        <?php echo esc_html(__("System settings",'manager-for-galene-videoconference')) ?>
      </a>

	    <?php else: ?>
		  <a class="navbar-item" href="<?php echo get_permalink() ?>" >
			<?php echo esc_html(__("Rooms",'manager-for-galene-videoconference')) ?>
		  </a>
	    <?php endif; ?>
       </div>
   </div>

    <div class="navbar-end">
      <div class="navbar-item">
        <div class="buttons">		  
		<?php if( @$is_admin === true): ?>
          <a class="button is-light" href="<?php echo esc_url(Galmgr_util::add_arg([ 'galene_action' => 'admin_action_logout' ])) ?>" >
            <?php echo esc_html(__("Logout",'manager-for-galene-videoconference')) ?>
          </a>
	    <?php else: ?>
          <a class="button is-light"  href="<?php echo esc_url(Galmgr_util::add_arg([ 'galene_action' => 'admin_screen_roomsettings' ])) ?>" >
            <?php echo esc_html(__("Administration",'manager-for-galene-videoconference')) ?>
          </a>
	    <?php endif; ?>
        </div>
      </div>
    </div>

</nav>



<?php if(is_array(@$d['msg'])): foreach($d['msg'] as $m): ?>

<article class="message autohide is-small <?php echo sanitize_html_class( $m['type']) ?>">
  <div class="message-header is-rounded">
    <div><?php echo esc_html($m['title']) ?></div>
    <button class="delete" type="button" aria-label="delete"></button>
  </div>
</article>

<?php endforeach; endif; ?>



<form class="box" method="POST">
	<?php wp_nonce_field("admin_update_user","gal_form_id",false,true) ?>
	<input type="hidden" name="galene_user" id="galene_user" value="<?php echo esc_attr($d['user']['id']) ?>" >
	<p class="panel-heading mb-3">
		<?php echo esc_html(__("Edit user",'manager-for-galene-videoconference')) ?>: <?php echo esc_html($d['user']['displayName']) ?>
	</p>

	<div class="tab-content" >
		
		<?php if (@$d['user']['type'] == 0): ?>
		<div class="field">
		  <label class="label"><?php echo esc_html(__("Display name",'manager-for-galene-videoconference')) ?></label>
		  <div class="control">
			<input name="displayName" id="displayName" class="input" type="text" value="<?php echo esc_attr($d['user']['displayName']) ?> " >
		  </div>
		  <p class="help"><?php echo esc_html(__("Name that will be used during session",'manager-for-galene-videoconference')) ?></p>
		</div> 
		<div class="field">
		  <label class="label"><?php echo esc_html(__("Loginname",'manager-for-galene-videoconference')) ?></label>
		  <div class="control">
			<input name="login" id="login" class="input" type="text" value="<?php echo esc_attr($d['user']['login']) ?>" >
		  </div>
		  <p class="help"><?php echo esc_html(__("Username for authentication",'manager-for-galene-videoconference')) ?></p>
		</div> 

		<div class="field">
		  <label class="label"><?php echo esc_html(__("Password",'manager-for-galene-videoconference')) ?></label>
		  <div class="control">
			<input name="password_new" id="password_new" class="input" type="password"  >
		  </div>
		  <p class="help"><?php echo esc_html(__("Login password, only use for new user or password change otherwise empty",'manager-for-galene-videoconference')) ?></p>
		</div> 

		<div class="field">
		  <label class="label"><?php echo esc_html(__("Password (repeat)",'manager-for-galene-videoconference')) ?></label>
		  <div class="control">
			<input class="input" name="password_repeat" id="password_repeat" type="password"  >
		  </div>
		  <p class="help"><?php echo esc_html(__("Password (repeat)",'manager-for-galene-videoconference')) ?></p>
		</div> 
		<div class="field">
			<label class="checkbox">
			  <input type="checkbox" name="isAdmin" id="isAdmin" <?php echo esc_attr((@$d['user']['isAdmin'] == 1 )?'checked':'') ?> >
			  <?php echo esc_html(__("Is administrator",'manager-for-galene-videoconference')) ?>
			</label>
			<p class="help"><?php echo esc_html(__("If checked this user can login to the Galène manager (not room operator)",'manager-for-galene-videoconference')) ?></p>
		</div>			
		<?php endif; ?>

	</div>
  
  

	<div class="field is-grouped is-grouped-right mt-3">
	  <p class="control">
		<button class="button is-primary" type="submit" name="galene_action" id="galene_action" value="admin_update_user" >
		  <?php echo esc_html(__("Save",'manager-for-galene-videoconference')) ?>
		</button>
	  </p>
	  <p class="control">
		<a class="button is-light" href="<?php echo esc_url(Galmgr_util::add_arg([ 'galene_action' => 'admin_screen_usersettings' ])) ?>">
		  <?php echo esc_html(__("Cancel",'manager-for-galene-videoconference')) ?>
		</a>
	  </p>
	</div>
</form>


</section>

<div class="preloader">
  <img src="<?php echo esc_url(GALMGR_PLUGIN_URL . '/assets/spinner.svg') ?>" alt="spinner">
</div>

