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
  <div class="panel-heading">
    <div class="is-hidden-tablet"><?php echo esc_html(__("Public rooms",'manager-for-galene-videoconference')) ?></div>

		<div class="columns is-size-6 is-hidden-mobile" >
			<div class="column  is-cursor-default">
					<?php echo esc_html(__("Room",'manager-for-galene-videoconference')) ?>
			</div>
			<div class="column  is-cursor-default">
				<?php echo esc_html(__("Description",'manager-for-galene-videoconference')) ?>
			</div>
			<div class="column has-text-centered is-cursor-default is-multiline is-one-third-desktop is-one-fifth-tablet">
				<?php echo esc_html(__("Access room as ...",'manager-for-galene-videoconference')) ?>
			</div>
		</div>
	
	</div>
  

  <?php foreach($rooms as $room): ?>
  <label class="panel-block is-block is-active">
	  <div class="columns" >
		  <div class="column  is-cursor-default">
				<?php echo esc_html($room->displayName) ?>
		  </div>
		  <div class="column  is-cursor-default is-size-7">
			<?php echo esc_html($room->description) ?>
		  </div>
		  <div class="column columns is-multiline is-one-third-desktop is-one-fifth-tablet">
			  <div class="column  py-1-mobile">
				  <a class="is-underlined" href='<?php echo esc_url( $room->urls['recipient']) ?>' >
					<?php echo esc_html(__("Listener",'manager-for-galene-videoconference')) ?>
				  </a>			  
			  </div>
			  <div class="column py-1-mobile">
				  <a class="is-underlined" href='<?php echo esc_url($room->urls['presenter']) ?>' >
					<?php echo esc_html(__("Presenter",'manager-for-galene-videoconference')) ?>
				  </a>			  			  
			  </div>
			  <div class="column py-1-mobile">
				  <a class="is-underlined" href='<?php echo esc_url( $room->urls['admin']) ?>' >
					<?php echo esc_html(__("Operator",'manager-for-galene-videoconference')) ?>
				  </a>			  			  
			  </div>
		  </div>
	  </div>
</label>  
  <?php endforeach; ?>
</nav>


</section>

<div class="preloader">
  <img src="<?php echo esc_url(GALMGR_PLUGIN_URL . '/assets/spinner.svg') ?>" alt="spinner">
</div>

