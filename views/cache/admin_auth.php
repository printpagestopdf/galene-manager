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



<nav class="panel">
	<p class="panel-heading">
		<?php echo esc_html(__("Login administration",'manager-for-galene-videoconference')) ?>
	</p>

	<form method="POST" class="box is-flex-grow-1">
		<?php wp_nonce_field("admin_auth","gal_form_id",false,true) ?>
		<input type="hidden" name='galene_action' value="<?php echo esc_attr(@$action) ?>" >
		<div class="field">
			<label class="label"><?php echo esc_html(__("Login name",'manager-for-galene-videoconference')) ?></label>
			<div class="control">
				<input class="input" type="text" placeholder="<?php echo esc_attr(__("Login name",'manager-for-galene-videoconference')) ?>" name="galene_login" id="galene_login" required="required" value="<?php echo esc_attr(@$presets['galene_user']) ?>">
			</div>
		</div>
		<div class="field">
			<label class="label"><?php echo esc_html(__("Password",'manager-for-galene-videoconference')) ?></label>
			<div class="control">
				<input class="input" type="password" placeholder="<?php echo esc_attr(__("Password",'manager-for-galene-videoconference')) ?>" name="galene_password" id="galene_password" required="required"  value="<?php echo esc_attr(@$presets['galene_password']) ?>">
			</div>
		</div>
		<div class="field">
			<button class="button is-info" type="submit"><?php echo esc_html(__("Next",'manager-for-galene-videoconference')) ?></button>
		</div>
	</form>

</nav>


</section>

<div class="preloader">
  <img src="<?php echo esc_url(GALMGR_PLUGIN_URL . '/assets/spinner.svg') ?>" alt="spinner">
</div>

