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
  <div class="panel-heading">
    <div class="is-hidden-tablet"><?php echo __("Public rooms",'galene-mgr') ?></div>

		<div class="columns is-size-6 is-hidden-mobile" >
			<div class="column  is-cursor-default">
					<?php echo __("Room",'galene-mgr') ?>
			</div>
			<div class="column  is-cursor-default">
				<?php echo __("Description",'galene-mgr') ?>
			</div>
			<div class="column has-text-centered is-cursor-default is-multiline is-one-third-desktop is-one-fifth-tablet">
				<?php echo __("Access room as ...",'galene-mgr') ?>
			</div>
		</div>
	
	</div>
  

  <?php foreach($rooms as $room): ?>
  <label class="panel-block is-block is-active">
	  <div class="columns" >
		  <div class="column  is-cursor-default">
				<?php echo $room->displayName ?>
		  </div>
		  <div class="column  is-cursor-default is-size-7">
			<?php echo $room->description ?>
		  </div>
		  <div class="column columns is-multiline is-one-third-desktop is-one-fifth-tablet">
			  <div class="column  py-1-mobile">
				  <a class="is-underlined" href='<?php echo $room->urls['recipient'] ?>' >
					<?php echo __("Listener",'galene-mgr') ?>
				  </a>			  
			  </div>
			  <div class="column py-1-mobile">
				  <a class="is-underlined" href='<?php echo $room->urls['presenter'] ?>' >
					<?php echo __("Presenter",'galene-mgr') ?>
				  </a>			  			  
			  </div>
			  <div class="column py-1-mobile">
				  <a class="is-underlined" href='<?php echo $room->urls['admin'] ?>' >
					<?php echo __("Operator",'galene-mgr') ?>
				  </a>			  			  
			  </div>
		  </div>
	  </div>
</label>  
  <?php endforeach; ?>
</nav>


</section>

<div class="preloader">
  <img src="<?php echo GALENE_PLUGIN_URL . '/assets/spinner.svg'; ?>" alt="spinner">
</div>

