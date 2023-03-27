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
	<form method="POST">
	<input type="hidden" name="galene_subaction" value="new_room">
		<div class="panel-heading columns mx-0 p-0 my-2">
			<div class="column my-auto"><?php echo esc_html(__("Rooms",'manager-for-galene-videoconference')) ?></div>
				<div class="column field is-grouped is-grouped-right is-align-self-center py-0">
					<p class="control">
						<input type="hidden" name="galene_subaction" value="new_room" >
						<button type="submit" name="galene_action"
								title="<?php echo esc_attr(__("Create a new room based on this template",'manager-for-galene-videoconference')) ?>"
								value="admin_screen_roomedit" class="button is-primary is-outlined">
							<figure class="image is-16x16 mr-2">
							  <img src="<?php echo esc_url(GALMGR_PLUGIN_URL . '/assets/magic-wand-outline.svg') ?>">
							</figure>
							  <?php echo esc_html(__("New room",'manager-for-galene-videoconference')) ?>
						</button>
					</p>
					<p class="control">
					  <div class="control">
						<div class="select">
						  <select name="new_room_preset" id="new_room_preset"  >
							<option value="conference"  ><?php echo esc_html(__("Conference",'manager-for-galene-videoconference')) ?></option>
							<option value="presentation"  ><?php echo esc_html(__("Seminar",'manager-for-galene-videoconference')) ?></option>
							<option value="closed_group"  ><?php echo esc_html(__("Closed group",'manager-for-galene-videoconference')) ?></option>
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
			<a class="image-button" title="<?php echo esc_attr(__("Edit this room",'manager-for-galene-videoconference')) ?>"
					href="<?php echo esc_url(Galmgr_util::add_arg([ 'galene_action' => 'admin_screen_roomedit', 'galene_room' => $room->id ])) ?>"  >
				<figure class="image is-24x24 mx-2">
				  <img src="<?php echo esc_url(GALMGR_PLUGIN_URL . '/assets/edit-outline.svg') ?>">
				</figure>
			</a>
		</div>
		<div class="column is-cursor-default">
			<div class="is-inline-block">
				<a class="is-underlined" title="<?php echo esc_attr(__("Edit this room",'manager-for-galene-videoconference')) ?>"
				href="<?php echo esc_url(Galmgr_util::add_arg([ 'galene_action' => 'admin_screen_roomedit', 'galene_room' => $room->id ])) ?>"  >
					<?php echo esc_html($room->displayName) ?>
				</a>
			</div>
			<div class="is-inline-block vertical-centered ml-4">	
				<a class="image-button is-info modal-button js-modal-trigger"  
						title="<?php echo esc_attr(__("Display short info including links and code",'manager-for-galene-videoconference')) ?>"
						data-target="modal-<?php echo esc_attr($room->id) ?>" aria-haspopup="true">
					<figure class="image is-24x24 mx-2">
					  <img  src="<?php echo esc_url(GALMGR_PLUGIN_URL . '/assets/info-circle.svg') ?>">
					</figure>
				</a>
			</div>
		</div>
		<div class="column is-narrow" title="<?php echo esc_attr(__("Permanently delete this room",'manager-for-galene-videoconference')) ?>">	
			<a  onclick="return confirm('<?php echo esc_html(__("Do you really want to delete this room? All data are lost.",'manager-for-galene-videoconference')) ?>');" class="image-button" href="<?php echo esc_url(Galmgr_util::add_arg([ 'galene_action' => 'admin_room_delete', 'galene_room' => $room->id ])) ?>"  >
				<figure class="image is-24x24 mx-2">
				  <img src="<?php echo esc_url(GALMGR_PLUGIN_URL . '/assets/trash-outline.svg') ?>">
				</figure>
			</a>
		</div>
  
  </div>
</label>  

	<div id="modal-<?php echo esc_attr($room->id) ?>" class="modal xis-large-modal">
	  <div class="modal-background"></div>
	  <div class="modal-card">
		<header class="modal-card-head">
		  <p class="modal-card-title"><?php echo esc_html(__("Shortinfo/Links",'manager-for-galene-videoconference')) ?></p>
		  <button class="delete" aria-label="close"></button>
		</header>
		<section class="modal-card-body"  >	
			<div class="field">
			<?php if($room->needs_code): ?>
			  <label class="label"><?php echo esc_html(__("Code",'manager-for-galene-videoconference')) ?></label>
			  <div class="field is-grouped">
				  <div class="control">
					<?php echo esc_html($room->room_accesscode) ?>
				  </div>
				  <div class="control">
					<button onclick="copyText('<?php echo esc_url($room->room_accesscode) ?>'); return false;"><?php echo esc_html(__("Copy code",'manager-for-galene-videoconference')) ?></button>
				  </div>
			  </div>
			</div> 
			<?php endif; ?>
			<div class="field">
			  <label class="label"><?php echo esc_html(__("Link for listener",'manager-for-galene-videoconference')) ?></label>
			  <div class="field is-grouped">
				  <div class="control">
					<a href="<?php echo esc_url( $room->{'urls'}['recipient']) ?>" target="_blank"><?php echo esc_html($room->{'urls'}['recipient']) ?></a>
				  </div>
				  <div class="control">
					<button onclick="copyText('<?php echo esc_url($room->{'urls'}['recipient']) ?>'); return false;"><?php echo esc_html(__("Copy link",'manager-for-galene-videoconference')) ?></button>
				  </div>
			  </div>
			</div> 
			
			<div class="field">
			  <label class="label"><?php echo esc_html(__("Link for presenter",'manager-for-galene-videoconference')) ?></label>
			  <div class="field is-grouped">
				  <div class="control">
					<a href="<?php echo esc_url($room->{'urls'}['presenter']) ?>" target="_blank"><?php echo esc_html($room->{'urls'}['presenter']) ?></a>
				  </div>
				  <div class="control">
					<button onclick="copyText('<?php echo esc_url($room->{'urls'}['presenter']) ?>'); return false;"><?php echo esc_html(__("Copy link",'manager-for-galene-videoconference')) ?></button>
				  </div>
			  </div>
			</div> 
			
			<div class="field">
			  <label class="label"><?php echo esc_html(__("Link for operator",'manager-for-galene-videoconference')) ?></label>
			  <div class="field is-grouped">
				  <div class="control">
					<a href="<?php echo esc_url($room->{'urls'}['admin']) ?>" target="_blank"><?php echo esc_html($room->{'urls'}['admin']) ?></a>
				  </div>
				  <div class="control">
					<button onclick="copyText('<?php echo esc_url($room->{'urls'}['admin']) ?>'); return false;"><?php echo esc_html(__("Copy link",'manager-for-galene-videoconference')) ?></button>
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
  <img src="<?php echo esc_url(GALMGR_PLUGIN_URL . '/assets/spinner.svg') ?>" alt="spinner">
</div>

