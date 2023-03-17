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



<form class="box" method="POST">
	<?php wp_nonce_field("admin_update_user","gal_form_id",false,true) ?>
	<input type="hidden" name="galene_user" id="galene_user" value="<?php echo $d['user']['id'] ?>" >
	<p class="panel-heading mb-3">
		<?php echo __("Edit user",'galene-mgr') ?>: <?php echo $d['user']['displayName'] ?>
	</p>

	<div class="tab-content" >
		
		<?php if ($d['user']['type'] == 0): ?>
		<div class="field">
		  <label class="label"><?php echo __("Display name",'galene-mgr') ?></label>
		  <div class="control">
			<input name="displayName" id="displayName" class="input" type="text" value="<?php echo $d['user']['displayName'] ?> " >
		  </div>
		  <p class="help"><?php echo __("Name that will be used during session",'galene-mgr') ?></p>
		</div> 
		<div class="field">
		  <label class="label"><?php echo __("Loginname",'galene-mgr') ?></label>
		  <div class="control">
			<input name="login" id="login" class="input" type="text" value="<?php echo $d['user']['login'] ?>" >
		  </div>
		  <p class="help"><?php echo __("Username for authentication",'galene-mgr') ?></p>
		</div> 

		<div class="field">
		  <label class="label"><?php echo __("Password",'galene-mgr') ?></label>
		  <div class="control">
			<input name="password_new" id="password_new" class="input" type="password"  >
		  </div>
		  <p class="help"><?php echo __("Login password, only use for new user or password change otherwise empty",'galene-mgr') ?></p>
		</div> 

		<div class="field">
		  <label class="label"><?php echo __("Password (repeat)",'galene-mgr') ?></label>
		  <div class="control">
			<input class="input" name="password_repeat" id="password_repeat" type="password"  >
		  </div>
		  <p class="help"><?php echo __("Password (repeat)",'galene-mgr') ?></p>
		</div> 
		<div class="field">
			<label class="checkbox">
			  <input type="checkbox" name="isAdmin" id="isAdmin" <?php echo (@$d['user']['isAdmin'] == 1 )?'checked':'' ?> >
			  <?php echo __("Is administrator",'galene-mgr') ?>
			</label>
			<p class="help"><?php echo __("If checked this user can login to the Galène manager (not room operator)",'galene-mgr') ?></p>
		</div>			
		<?php endif; ?>

	</div>
  
  

	<div class="field is-grouped is-grouped-right mt-3">
	  <p class="control">
		<button class="button is-primary" type="submit" name="galene_action" id="galene_action" value="admin_update_user" >
		  <?php echo __("Save",'galene-mgr') ?>
		</button>
	  </p>
	  <p class="control">
		<a class="button is-light" href="<?php echo Gal_util::add_arg([ 'galene_action' => 'admin_screen_usersettings' ]) ?>">
		  <?php echo __("Cancel",'galene-mgr') ?>
		</a>
	  </p>
	</div>
</form>


</section>

<div class="preloader">
  <img src="<?php echo GALENE_PLUGIN_URL . '/assets/spinner.svg'; ?>" alt="spinner">
</div>

