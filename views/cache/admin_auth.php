<?php class_exists('Gal_View_Generator') or exit; ?>

<section>
<nav class="navbar mb-3" role="navigation" aria-label="main navigation">
  <div class="navbar-brand">
	  <div href='#' class="navbar-item">
	  <a class="navbar-item" href="<?php echo get_permalink() ?>" >
		  <img  src="<?php echo GALENE_PLUGIN_URL . '/assets/videocall-pngrepo-com.png'; ?>" >
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
      <a class="navbar-item is-tab <?php echo @$d['admin_screen_roomsettings'] ?>" href="<?php echo Gal_util::add_arg([ 'galene_action' => 'admin_screen_roomsettings' ]) ?>">
        <?php echo __("Room settings",'galene-mgr') ?>
      </a>
      <a class="navbar-item is-tab <?php echo @$d['admin_screen_usersettings'] ?>" href="<?php echo Gal_util::add_arg([ 'galene_action' => 'admin_screen_usersettings' ]) ?>">
        <?php echo __("Users",'galene-mgr') ?>
      </a>
      <a class="navbar-item is-tab <?php echo @$d['admin_screen_roommgr'] ?>" href="<?php echo Gal_util::add_arg([ 'galene_action' => 'admin_screen_settings' ]) ?>">
        <?php echo __("System settings",'galene-mgr') ?>
      </a>

	    <?php else: ?>
		  <a class="navbar-item" href="<?php echo get_permalink() ?>" >
			<?php echo __("Rooms",'galene-mgr') ?>
		  </a>
	    <?php endif; ?>
       </div>
   </div>

    <div class="navbar-end">
      <div class="navbar-item">
        <div class="buttons">		  
		<?php if( @$is_admin === true): ?>
          <a class="button is-light" href="<?php echo Gal_util::add_arg([ 'galene_action' => 'admin_action_logout' ]) ?>" >
            <?php echo __("Logout",'galene-mgr') ?>
          </a>
	    <?php else: ?>
          <a class="button is-light"  href="<?php echo Gal_util::add_arg([ 'galene_action' => 'admin_screen_roomsettings' ]) ?>" >
            <?php echo __("Administration",'galene-mgr') ?>
          </a>
	    <?php endif; ?>
        </div>
      </div>
    </div>

</nav>



<?php if(is_array(@$d['msg'])): foreach($d['msg'] as $m): ?>

<article class="message autohide is-small <?php echo $m['type'] ?>">
  <div class="message-header is-rounded">
    <div><?php echo $m['title'] ?></div>
    <button class="delete" type="button" aria-label="delete"></button>
  </div>
</article>

<?php endforeach; endif; ?>



<nav class="panel">
	<p class="panel-heading">
		<?php echo __("Login administration",'galene-mgr') ?>
	</p>

	<form method="POST" class="box is-flex-grow-1">
		<?php wp_nonce_field("admin_auth","gal_form_id",false,true) ?>
		<input type="hidden" name='galene_action' value="<?php echo @$action ?>" >
		<div class="field">
			<label class="label"><?php echo __("Login name",'galene-mgr') ?></label>
			<div class="control">
				<input class="input" type="text" placeholder="<?php echo __("Login name",'galene-mgr') ?>" name="galene_login" id="galene_login" required="required" value="<?php echo @$presets['galene_user'] ?>">
			</div>
		</div>
		<div class="field">
			<label class="label"><?php echo __("Password",'galene-mgr') ?></label>
			<div class="control">
				<input class="input" type="password" placeholder="<?php echo __("Password",'galene-mgr') ?>" name="galene_password" id="galene_password" required="required"  value="<?php echo @$presets['galene_password'] ?>">
			</div>
		</div>
		<div class="field">
			<button class="button is-info" type="submit"><?php echo __("Next",'galene-mgr') ?></button>
		</div>
	</form>

</nav>


</section>

<div class="preloader">
  <img src="<?php echo GALENE_PLUGIN_URL . '/assets/spinner.svg'; ?>" alt="spinner">
</div>

