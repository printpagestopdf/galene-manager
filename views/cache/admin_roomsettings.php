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
	<form method="POST">
	<input type="hidden" name="galene_subaction" value="new_room">
		<div class="panel-heading columns mx-0 p-0 my-2">
			<div class="column my-auto"><?php echo __("Rooms",'galene-mgr') ?></div>
				<div class="column field is-grouped is-grouped-right is-align-self-center py-0">
					<p class="control">
						<input type="hidden" name="galene_subaction" value="new_room" >
						<button type="submit" name="galene_action"
								title="<?php echo __("Create a new room based on this template",'galene-mgr') ?>"
								value="admin_screen_roomedit" class="button is-primary is-outlined">
							<figure class="image is-16x16 mr-2">
							  <img src="<?php echo GALENE_PLUGIN_URL ?>/assets/magic-wand-outline.svg">
							</figure>
							  <?php echo __("New room",'galene-mgr') ?>
						</button>
					</p>
					<p class="control">
					  <div class="control">
						<div class="select">
						  <select name="new_room_preset" id="new_room_preset"  >
							<option value="conference"  ><?php echo __("Conference",'galene-mgr') ?></option>
							<option value="presentation"  ><?php echo __("Seminar",'galene-mgr') ?></option>
							<option value="closed_group"  ><?php echo __("Closed group",'galene-mgr') ?></option>
						  </select>
						</div>		  
					  </div>
					</p>
				</div>
		</div>
	</form>


  <?php foreach($d['rooms'] as $room): ?>
  <label class="panel-block is-block" >
	<div class="columns">
		<div class="column is-narrow">	
			<a class="image-button" title="<?php echo __("Edit this room",'galene-mgr') ?>"
					href="<?php echo Gal_util::add_arg([ 'galene_action' => 'admin_screen_roomedit', 'galene_room' => $room->id ]) ?>"  >
				<figure class="image is-24x24 mx-2">
				  <img src="<?php echo GALENE_PLUGIN_URL ?>/assets/edit-outline.svg">
				</figure>
			</a>
		</div>
		<div class="column is-cursor-default">
			<div class="is-inline-block">
				<a class="is-underlined" title="<?php echo __("Edit this room",'galene-mgr') ?>"
				href="<?php echo Gal_util::add_arg([ 'galene_action' => 'admin_screen_roomedit', 'galene_room' => $room->id ]) ?>"  >
					<?php echo $room->displayName ?>
				</a>
			</div>
			<div class="is-inline-block vertical-centered ml-4">	
				<a class="image-button is-info modal-button js-modal-trigger"  
						title="<?php echo __("Display short info including links and code",'galene-mgr') ?>"
						data-target="modal-<?php echo $room->id ?>" aria-haspopup="true">
					<figure class="image is-24x24 mx-2">
					  <img  src="<?php echo GALENE_PLUGIN_URL ?>/assets/info-circle.svg">
					</figure>
				</a>
			</div>
		</div>
		<div class="column is-narrow" title="<?php echo __("Permanently delete this room",'galene-mgr') ?>">	
			<a  onclick="return confirm('<?php echo __("Do you really want to delete this room? All data are lost.",'galene-mgr') ?>');" class="image-button" href="<?php echo Gal_util::add_arg([ 'galene_action' => 'admin_room_delete', 'galene_room' => $room->id ]) ?>"  >
				<figure class="image is-24x24 mx-2">
				  <img src="<?php echo GALENE_PLUGIN_URL ?>/assets/trash-outline.svg">
				</figure>
			</a>
		</div>
  
  </div>
</label>  

	<div id="modal-<?php echo $room->id ?>" class="modal xis-large-modal">
	  <div class="modal-background"></div>
	  <div class="modal-card">
		<header class="modal-card-head">
		  <p class="modal-card-title"><?php echo __("Shortinfo/Links",'galene-mgr') ?></p>
		  <button class="delete" aria-label="close"></button>
		</header>
		<section class="modal-card-body"  >	
			<div class="field">
			<?php if($room->needs_code): ?>
			  <label class="label"><?php echo __("Code",'galene-mgr') ?></label>
			  <div class="field is-grouped">
				  <div class="control">
					<?php echo $room->room_accesscode ?>
				  </div>
				  <div class="control">
					<button onclick="copyText('<?php echo $room->room_accesscode ?>'); return false;"><?php echo __("Copy code",'galene-mgr') ?></button>
				  </div>
			  </div>
			</div> 
			<?php endif; ?>
			<div class="field">
			  <label class="label"><?php echo __("Link for listener",'galene-mgr') ?></label>
			  <div class="field is-grouped">
				  <div class="control">
					<a href="<?php echo $room->{'urls'}['recipient'] ?>" target="_blank"><?php echo $room->{'urls'}['recipient'] ?></a>
				  </div>
				  <div class="control">
					<button onclick="copyText('<?php echo $room->{'urls'}['recipient'] ?>'); return false;"><?php echo __("Copy link",'galene-mgr') ?></button>
				  </div>
			  </div>
			</div> 
			
			<div class="field">
			  <label class="label"><?php echo __("Link for presenter",'galene-mgr') ?></label>
			  <div class="field is-grouped">
				  <div class="control">
					<a href="<?php echo $room->{'urls'}['presenter'] ?>" target="_blank"><?php echo $room->{'urls'}['presenter'] ?></a>
				  </div>
				  <div class="control">
					<button onclick="copyText('<?php echo $room->{'urls'}['presenter'] ?>'); return false;"><?php echo __("Copy link",'galene-mgr') ?></button>
				  </div>
			  </div>
			</div> 
			
			<div class="field">
			  <label class="label"><?php echo __("Link for operator",'galene-mgr') ?></label>
			  <div class="field is-grouped">
				  <div class="control">
					<a href="<?php echo $room->{'urls'}['admin'] ?>" target="_blank"><?php echo $room->{'urls'}['admin'] ?></a>
				  </div>
				  <div class="control">
					<button onclick="copyText('<?php echo $room->{'urls'}['admin'] ?>'); return false;"><?php echo __("Copy link",'galene-mgr') ?></button>
				  </div>
			  </div>
			</div> 
		</section>
	  </div>
	</div>


  <?php endforeach; ?>
  
</nav>



</section>

<div class="preloader">
  <img src="<?php echo GALENE_PLUGIN_URL . '/assets/spinner.svg'; ?>" alt="spinner">
</div>

