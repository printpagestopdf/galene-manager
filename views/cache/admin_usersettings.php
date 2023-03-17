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



<form method="POST" >
<input type="hidden" name="galene_action" id="galene_action" value="admin_screen_useredit" >
<nav class="panel">
	<div class="panel-heading columns mx-0 p-0 my-2">
		<div class="column my-auto"><?php echo __("Users",'galene-mgr') ?></div>
		<div class="column field is-grouped is-grouped-right is-align-self-center py-0">
			<div class="control">
			<a  class="button is-outlined is-primary has-text-weight-normal" title="<?php echo __("Create a new user configuration",'galene-mgr') ?>"
				href="<?php echo Gal_util::add_arg([ 'galene_action' => 'admin_screen_useredit' ]) ?>" >
				<figure class="image is-16x16 mr-2">
				  <img src="<?php echo GALENE_PLUGIN_URL ?>/assets/magic-wand-outline.svg">
				</figure>
				  <?php echo __("New user",'galene-mgr') ?>
			</a>
		</div>
		</div>
	</div>
   
  <?php foreach($d['users'] as $user): if($user['type'] != 0) continue; ?>
  <label class="panel-block is-block" >
	<div class="columns">
		<div class="column is-narrow">	
			<a class="image-button" title="<?php echo __("Edit user",'galene-mgr') ?>"
			href="<?php echo Gal_util::add_arg([ 'galene_action' => 'admin_screen_useredit', 'galene_user' => $user['id'] ]) ?>"  >
				<figure class="image is-24x24 mx-2">
				<img src="<?php echo GALENE_PLUGIN_URL ?>/assets/edit-outline.svg">
				</figure>
			</a>
		</div>
		<div class="column">
			<a class="is-underlined"  title="<?php echo __("Display name",'galene-mgr') ?>"
			href="<?php echo Gal_util::add_arg([ 'galene_action' => 'admin_screen_useredit', 'galene_user' => $user['id'] ]) ?>"  >
				<?php echo $user['displayName'] ?>
			</a>
		</div>
		<div class="column is-cursor-default" title="<?php echo __("Loginname",'galene-mgr') ?>"><?php echo $user['login'] ?></div>
		<div class="column is-cursor-default" title="<?php echo __("Is administrator",'galene-mgr') ?>"><?php echo $user['isAdmin']?'(Admin)':'' ?></div>
		<div class="column is-narrow" title="<?php echo __("Permanently delete this user",'galene-mgr') ?>">	
			<a  onclick="return confirm('<?php echo __("Do you really want to delete this user? All data are lost.",'galene-mgr') ?>');" 
				class="image-button" href="<?php echo Gal_util::add_arg([ 'galene_action' => 'admin_user_delete', 'galene_user' => $user['id'] ]) ?>"  >
				<figure class="image is-24x24 mx-2">
				  <img src="<?php echo GALENE_PLUGIN_URL ?>/assets/trash-outline.svg">
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
  <img src="<?php echo GALENE_PLUGIN_URL . '/assets/spinner.svg'; ?>" alt="spinner">
</div>

