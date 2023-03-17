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
		<?php echo sprintf( __("Enter Room '%1s' as '%2s'",'galene-mgr') ,$room->displayName,$d['g_type_disp']) ?>
	</p>

	<form method="POST" class="box is-flex-grow-1">
		<?php wp_nonce_field("access_room","gal_form_id",false,true) ?>
		<input type="hidden" name='galene_action' value="access_room" >
		<input type="hidden" name='galene_room' value="<?php echo $room->id ?>" >
		<?php if(in_array('name',$parts)): ?>
		<div class="field">
			<?php if(in_array('code',$parts)): ?>
			<label class="label"><?php echo __("Displayname",'galene-mgr') ?></label>
			<?php else: ?>
			<label class="label"><?php echo __("Login",'galene-mgr') ?></label>
			<?php endif; ?>
			<div class="control">
				<input class="input" type="text" name="galene_login" id="galene_login" required="required" value="<?php echo @$d['galene_login'] ?>">
			</div>
		</div>
		<?php endif; ?>
		<?php if(in_array('code',$parts)): ?>
		<div class="field">
			<label class="label"><?php echo __("Code",'galene-mgr') ?></label>
			<div class="control">
				<input class="input" type="text" name="galene_code" id="galene_code" required="required"  value="<?php echo @$d['galene_code'] ?>">
			</div>
		</div>
		<?php endif; ?>
		<?php if(in_array('password',$parts)): ?>
		<div class="field">
			<label class="label"><?php echo __("Password",'galene-mgr') ?></label>
			<div class="control">
				<input class="input" type="password" name="galene_password" id="galene_password" required="required"  value="<?php echo @$d['galene_password'] ?>">
			</div>
		</div>
		<?php endif; ?>
		<div class="field">
			<button class="button is-info" type="submit"><?php echo __("Next",'galene-mgr') ?></button>
		</div>
	</form>

</nav>


</section>

<div class="preloader">
  <img src="<?php echo GALENE_PLUGIN_URL . '/assets/spinner.svg'; ?>" alt="spinner">
</div>

