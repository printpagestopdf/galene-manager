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



<form method="POST" >
<input type="hidden" name="galene_action" id="galene_action" value="admin_screen_useredit" >
<nav class="panel">
	<div class="panel-heading columns mx-0 p-0 my-2">
		<div class="column my-auto"><?php echo esc_html(__("Users",'manager-for-galene-videoconference')) ?></div>
		<div class="column field is-grouped is-grouped-right is-align-self-center py-0">
			<div class="control">
			<a  class="button is-outlined is-primary has-text-weight-normal" title="<?php echo esc_attr(__("Create a new user configuration",'manager-for-galene-videoconference')) ?>"
				href="<?php echo esc_url(Galmgr_util::add_arg([ 'galene_action' => 'admin_screen_useredit' ])) ?>" >
				<figure class="image is-16x16 mr-2">
				  <img src="<?php echo esc_url(GALMGR_PLUGIN_URL . '/assets/magic-wand-outline.svg') ?>">
				</figure>
				  <?php echo esc_html(__("New user",'manager-for-galene-videoconference')) ?>
			</a>
		</div>
		</div>
	</div>
   
  <?php foreach($d['users'] as $user): if($user['type'] != 0) continue; ?>
  <label class="panel-block is-block" >
	<div class="columns">
		<div class="column is-narrow">	
			<a class="image-button" title="<?php echo esc_attr(__("Edit user",'manager-for-galene-videoconference')) ?>"
			href="<?php echo esc_url(Galmgr_util::add_arg([ 'galene_action' => 'admin_screen_useredit', 'galene_user' => $user['id'] ])) ?>"  >
				<figure class="image is-24x24 mx-2">
				<img src="<?php echo esc_url(GALMGR_PLUGIN_URL . '/assets/edit-outline.svg') ?>">
				</figure>
			</a>
		</div>
		<div class="column">
			<a class="is-underlined"  title="<?php echo esc_attr(__("Display name",'manager-for-galene-videoconference')) ?>"
			href="<?php echo esc_url(Galmgr_util::add_arg([ 'galene_action' => 'admin_screen_useredit', 'galene_user' => $user['id'] ])) ?>"  >
				<?php echo esc_html($user['displayName']) ?>
			</a>
		</div>
		<div class="column is-cursor-default" title="<?php echo esc_attr(__("Loginname",'manager-for-galene-videoconference')) ?>"><?php echo esc_html($user['login']) ?></div>
		<div class="column is-cursor-default" title="<?php echo esc_attr(__("Is administrator",'manager-for-galene-videoconference')) ?>"><?php echo esc_html($user['isAdmin']?'(Admin)':'') ?></div>
		<div class="column is-narrow" title="<?php echo esc_attr(__("Permanently delete this user",'manager-for-galene-videoconference')) ?>">	
			<a  onclick="return confirm('<?php echo esc_html(__("Do you really want to delete this user? All data are lost.",'manager-for-galene-videoconference')) ?>');" 
				class="image-button" href="<?php echo esc_url(Galmgr_util::add_arg([ 'galene_action' => 'admin_user_delete', 'galene_user' => $user['id'] ])) ?>"  >
				<figure class="image is-24x24 mx-2">
				  <img src="<?php echo esc_url(GALMGR_PLUGIN_URL . '/assets/trash-outline.svg') ?>">
				</figure>
			</a>
		</div>
	</div>
  </label>
  <?php endforeach; ?>
</nav>
</form>


</section>

<div class="preloader">
  <img src="<?php echo esc_url(GALMGR_PLUGIN_URL . '/assets/spinner.svg') ?>" alt="spinner">
</div>

