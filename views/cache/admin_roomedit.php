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



<form class="box room-edit <?php echo sanitize_html_class($d['form_classes']) ?>" method="POST">
	<?php wp_nonce_field("admin_update_room","gal_form_id",false,true) ?>
	<input type="hidden" name="galene_room" id="galene_room" value="<?php echo esc_attr($d['room']->id) ?>" >
	<p class="panel-heading mb-3">
		<?php echo esc_html(__("Room",'manager-for-galene-videoconference')) ?>: "<?php echo esc_html(@$d['room']->displayName?$d['room']->displayName:__("no name",'manager-for-galene-videoconference')) ?>"
	</p>

	<div class="tabs is-boxed">
	  <ul class="tabs-menu">
		<li class="<?php echo sanitize_html_class((@$d['active_tab'] == 'settings-tab' || empty(@$d['active_tab']))?' is-active ':'') ?>" data-target="settings-tab">
		  <a>
			<span><?php echo esc_html(__("General",'manager-for-galene-videoconference')) ?></span>
		  </a>
		</li>
		<li class="<?php echo sanitize_html_class((@$d['active_tab'] == 'privileges-tab')? ' is-active ' : '') ?>" data-target="privileges-tab">
		  <a>
			<span><?php echo esc_html(__("Users",'manager-for-galene-videoconference')) ?></span>
		  </a>
		</li>
		<li class="<?php echo sanitize_html_class((@$d['active_tab'] == 'server-tab')? ' is-active ' : '') ?>" data-target="server-tab">
		  <a>
			<span><?php echo esc_html(__("Server",'manager-for-galene-videoconference')) ?></span>
		  </a>
		</li>
	  </ul>
	</div>

	<div class="tab-content box" id="settings-tab">

		<div class="columns">
			<div class="column">
				<div class="field">
				  <label class="label"><?php echo esc_html(__("Display name",'manager-for-galene-videoconference')) ?></label>
				  <div class="control">
					<input name="displayName" required="required" id="displayName" class="input" type="text" value="<?php echo esc_attr(@$d['room']->displayName) ?>" placeholder="<?php echo esc_attr(__("Display name",'manager-for-galene-videoconference')) ?>" >
				  </div>
				  <p class="help"><?php echo esc_html(__("Human readable name of room",'manager-for-galene-videoconference')) ?></p>
				</div>
				
				<div class="field">
				  <label class="label"><?php echo esc_html(__("Description",'manager-for-galene-videoconference')) ?></label>
				  <div class="control">
					<textarea name="description" id="description" class="textarea is-small" placeholder="<?php echo esc_attr(__("Description",'manager-for-galene-videoconference')) ?>"><?php echo esc_textarea(@$d['room']->description) ?></textarea>
				  </div>
				  <p class="help"><?php echo esc_html(__("Description that is visible on rooms listing",'manager-for-galene-videoconference')) ?></p>
				</div>

				<div class="field ">			
					<input type="checkbox" class="is-checkradio" name="show_on_roomslist" id="show_on_roomslist" <?php echo esc_attr(@$d['room']->show_on_roomslist?'checked' : '') ?>  >
					<label for="show_on_roomslist"><?php echo esc_html(__("Show this room on rooms listing",'manager-for-galene-videoconference')) ?></label>
					<p class="help"><?php echo esc_html(__("If checked, this room will be visible on the public rooms listing",'manager-for-galene-videoconference')) ?></p>
				</div>	
			</div>
			
			<div class="column has-background-white-ter">
				<input type="hidden" name="access" value="code">
				<label class="label"><?php echo esc_html(__("Room access conditions / Security",'manager-for-galene-videoconference')) ?></label>
				
				<div class="fld-access-code mb-3">
					<input type="checkbox" class="is-checkradio has-background-color hide-unchecked" name="needs_code" id="needs_code" <?php echo esc_attr(@$d['room']->needs_code?'checked' : '') ?>  >
					<label for="needs_code"><?php echo wp_kses(__("Use <b>Code</b>",'manager-for-galene-videoconference'),'data') ?></label>
					<div class="mt-2">
						<div class="field has-addons" >
						  <div class="control">
							<input name="room_accesscode" id="room_accesscode" class="input" type="text" value="<?php echo esc_attr(@$d['room']->room_accesscode) ?>" >
						  </div>
						  <div class="control">
							<a id="generate-code" class="button is-warning" title="Generate code">
								<figure class="image is-24x24">
								  <img src="<?php echo esc_url(GALMGR_PLUGIN_URL . '/assets/magic-wand-outline.svg') ?>">
								</figure>
							</a>
						  </div>
						</div>
					</div>
				</div>
				
				<div class="field ">			
					<input type="checkbox" class="is-checkradio has-background-color" name="needs_nickname" id="needs_nickname" <?php echo esc_attr(@$d['room']->needs_nickname?'checked' : '') ?>  >
					<label for="needs_nickname"><?php echo wp_kses(__("Needs <b>nickname</b>",'manager-for-galene-videoconference'),'data') ?></label>
					 <p class="help"><?php echo esc_html(__("Otherwise random name will be generated",'manager-for-galene-videoconference')) ?></p>
				</div>	
				
				<label class="label"><?php echo esc_html(__("Authentication necessary for:",'manager-for-galene-videoconference')) ?></label>
				<div class="field ">			
					<input type="checkbox" class="is-checkradio has-background-color" name="auth_for_other" id="auth_for_other" <?php echo esc_attr(@$d['room']->auth_for_other?'checked' : '') ?>  >
					<label for="auth_for_other"><?php echo esc_html(__("Attendance as listener",'manager-for-galene-videoconference')) ?></label>
				</div>	
				<div class="field ">			
					<input type="checkbox" class="is-checkradio has-background-color" name="auth_for_presentator" id="auth_for_presentator" <?php echo esc_attr(@$d['room']->auth_for_presentator?'checked' : '') ?>  >
					<label for="auth_for_presentator"><?php echo esc_html(__("Attendance as presenter",'manager-for-galene-videoconference')) ?></label>
				</div>	
				<div class="field ">			
					<input type="checkbox" class="is-checkradio has-background-color" name="auth_for_operator" id="auth_for_operator" <?php echo esc_attr(@$d['room']->auth_for_operator?'checked' : '') ?>  >
					<label for="auth_for_operator"><?php echo esc_html(__("Attendance as operator",'manager-for-galene-videoconference')) ?></label>
				</div>	
			</div>
		</div>
		
		<div class="field">
		  <label class="label"><?php echo esc_html(__("Link for listener",'manager-for-galene-videoconference')) ?></label>
		  <div class="field is-grouped">
			  <div class="control">
				<a href="<?php echo esc_url(@$d['room_urls']['recipient']) ?>" target="_blank"><?php echo esc_html(@$d['room_urls']['recipient']) ?></a>
			  </div>
			  <div class="control">
				<button onclick="copyText('<?php echo esc_url(@$d['room_urls']['recipient']) ?>'); return false;"><?php echo esc_html(__("Copy link",'manager-for-galene-videoconference')) ?></button>
			  </div>
		  </div>
		</div> 
		
		<div class="field">
		  <label class="label"><?php echo esc_html(__("Link for presenter",'manager-for-galene-videoconference')) ?></label>
		  <div class="field is-grouped">
			  <div class="control">
				<a href="<?php echo esc_url(@$d['room_urls']['presenter']) ?>" target="_blank"><?php echo esc_html(@$d['room_urls']['presenter']) ?></a>
			  </div>
			  <div class="control">
				<button onclick="copyText('<?php echo esc_url(@$d['room_urls']['presenter']) ?>'); return false;"><?php echo esc_html(__("Copy link",'manager-for-galene-videoconference')) ?></button>
			  </div>
		  </div>
		</div> 
		
		<div class="field">
		  <label class="label"><?php echo esc_html(__("Link for operator",'manager-for-galene-videoconference')) ?></label>
		  <div class="field is-grouped">
			  <div class="control">
				<a href="<?php echo esc_url(@$d['room_urls']['admin']) ?>" target="_blank"><?php echo esc_html(@$d['room_urls']['admin']) ?></a>
			  </div>
			  <div class="control">
				<button onclick="copyText('<?php echo esc_url(@$d['room_urls']['admin']) ?>'); return false;"><?php echo esc_html(__("Copy link",'manager-for-galene-videoconference')) ?></button>
			  </div>
		  </div>
		</div> 
		
	</div>
  
	<div class="tab-content" id="privileges-tab">
		<div class="box">
		<?php if(@$d['room']->id && $d['room']->id >= 0) : ?>
			<p class="is-pulled-right">
				<a class="button is-info" type="button" aria-haspopup="true" 
					href="<?php echo esc_url(Galmgr_util::add_arg(['galene_action' => 'admin_screen_userselect', 'galene_room' => $d['room']->id , 'room_display_name' => $d['room']->displayName ])); ?>">
						<?php echo esc_html(__("Edit accesslist",'manager-for-galene-videoconference')) ?>
				</a>
			</p>			
			  <table id="userlist" class="table is-striped is-fullwidth is-narrow is-hoverable" >
				<thead>
					<th class="text-vertical text-bottom is-size-7" ><?php echo esc_html(__("Operator",'manager-for-galene-videoconference')) ?></th>
					<th class="text-vertical text-bottom is-size-7" ><?php echo esc_html(__("Presenter",'manager-for-galene-videoconference')) ?></th>
					<th class="text-vertical text-bottom is-size-7" ><?php echo esc_html(__("Listener",'manager-for-galene-videoconference')) ?></th>
					<th class="text-bottom" ><?php echo esc_html(__("Display name",'manager-for-galene-videoconference')) ?></th>
					<th class="text-bottom" ><?php echo esc_html(__("Login name",'manager-for-galene-videoconference')) ?></th>
				</thead>
				<tbody>
			  <?php foreach(@$d['users'] as $user): if($user['type'] != 0) continue; ?>
				<tr>
					<td class="<?php echo sanitize_html_class($user['is_operator']? 'is-checkmark' : '') ?>"><div></div></td>
					<td class="<?php echo sanitize_html_class($user['is_presenter']? 'is-checkmark' : '') ?>"><div></div></td>
					<td class="<?php echo sanitize_html_class($user['is_other']? 'is-checkmark' : '') ?>"><div></div></td>
					<td><?php echo esc_html($user['displayName']) ?></td>
					<td><?php echo esc_html($user['login']) ?></td>
				</tr>			  
			  <?php endforeach; ?>
			  <tr><td colspan="6"><h6 class="is-size-6 my-1 has-text-centered has-text-weight-bold"><?php echo esc_html(__("Wordpress Roles",'manager-for-galene-videoconference')) ?></h6></td></tr>
			  <?php foreach(@$d['users'] as $user): if($user['type'] != 1) continue; ?>
				<tr>
					<td class="<?php echo sanitize_html_class($user['is_operator']? 'is-checkmark' : '') ?>"><div></div></td>
					<td class="<?php echo sanitize_html_class($user['is_presenter']? 'is-checkmark' : '') ?>"><div></div></td>
					<td class="<?php echo sanitize_html_class($user['is_other']? 'is-checkmark' : '') ?>"><div></div></td>
					<td><?php echo esc_html(Galmgr_util::translate_wprole($user['displayName'])) ?></td>
					<td></td>
				</tr>			  
			  <?php endforeach; ?>

				</tbody>
			  </table>
		<?php else: ?>
			<h4 class="subtitle is-4"><?php echo esc_html(__("Userslist can only be edited after saving roomsettings",'manager-for-galene-videoconference')) ?></h4>
		<?php endif; ?>
 		</div>	
	</div>
	
	<div class="tab-content" id="server-tab">
		<div class="columns is-flex-direction-row-reverse">
			<div class="column is-3   has-background-white-bis" >
				<iframe style="display:none;" name="dl_dummy_frame" id="dl_dummy_frame" ></iframe>
			
				<aside class="menu">
				  <p class="menu-label">
					<?php echo esc_html(__("Import/Export",'manager-for-galene-videoconference')) ?>
				  </p>
				  <ul class="menu-list">
					<li>
						<a target="dl_dummy_frame" href="<?php echo esc_url(Galmgr_util::add_arg([ 'galene_action' => 'admin_download_room_json', 'galene_room' => $d['room']->id ])) ?>" >
						  <span class="is-underlined"><?php echo esc_html(__("Export serverfile",'manager-for-galene-videoconference')) ?></span>
						  <p class="help">(<?php echo esc_html(__("Group config (.json) file for Galène server",'manager-for-galene-videoconference')) ?>)</p>
						</a>
					</li>
					<li class="import-srv-json is-primary">
						<a class="is-primary">
							<label for="import-srv-json" class="is-underlined"><?php echo esc_html(__("Import serverfile",'manager-for-galene-videoconference')) ?></label>
							<input type="file" id="import-srv-json" accept="*.json">
							<p class="help">(<?php echo esc_html(__("Group config (.json) file from Galène server",'manager-for-galene-videoconference')) ?>)</p>
						</a>
					</li>
				  </ul>
				</aside>			
			</div>				
			<div class="column" >
				<div class="field">
				  <label class="label"><?php echo esc_html(__("Galène group name",'manager-for-galene-videoconference')) ?></label>
				  <div class="control">
					<input required="required" name="galene_group" id="galene_group" class="input" type="text" value="<?php echo esc_attr(@$d['room']->galene_group) ?>" >
				  </div>
				  <p class="help"><?php echo esc_html(__("Name of Galène group (filename of .json config) on Galène Server",'manager-for-galene-videoconference')) ?></p>
				</div> 


				<div class="field">
				  <label class="label"><?php echo esc_html(__("Galène key",'manager-for-galene-videoconference')) ?></label>
				  <div class="control">
					<input required="required" name="key64" id="key64" class="input" type="text" value="<?php echo esc_attr($d['room']->key64) ?>" >
				  </div>
				  <p class="help"><?php echo esc_html(__("JWT key of Galène group (base64url format)",'manager-for-galene-videoconference')) ?></p>
				</div>
			</div>				
		</div>				
		
		<div class="card field">
			<header class="card-header">
				<p class="card-header-title"><?php echo esc_html(__("Options",'manager-for-galene-videoconference')) ?></p>
			</header>

			<div class="card-content columns is-multiline">
				<div class="field  column  is-one-quarter ">
					<label class="checkbox">
					  <input type="checkbox" name="allow_anonymous" id="allow_anonymous" <?php echo esc_attr(@$d['room']->allow_anonymous?'checked': '') ?> >
					  <?php echo esc_html(__("Allow anonymous attendance",'manager-for-galene-videoconference')) ?>
					</label>
					<p class="help"><?php echo esc_html(__("Attendance without nickname is possible",'manager-for-galene-videoconference')) ?></p>
				</div>			
				<div class="field  column  is-one-quarter  ">
					<label class="checkbox">
					  <input type="checkbox" name="allow_subgroups" id="allow_subgroups" <?php echo esc_attr(@$d['room']->allow_subgroups?'checked': '') ?> >
					  <?php echo esc_html(__("Allow subgroups",'manager-for-galene-videoconference')) ?>
					</label>
					<p class="help"><?php echo esc_html(__("Subgroup rooms (aka breakout rooms) can be createt",'manager-for-galene-videoconference')) ?></p>
				</div>			
				<div class="field  column  is-one-quarter ">
					<label class="checkbox">
					  <input type="checkbox" name="autolock" id="autolock" <?php echo esc_attr(@$d['room']->autolock?'checked': '') ?> >
					  <?php echo esc_html(__("Auto room locking",'manager-for-galene-videoconference')) ?>
					</label>
					<p class="help"><?php echo esc_html(__("Room will be locked automatically without operator",'manager-for-galene-videoconference')) ?></p>
				</div>			
				<div class="field  column  is-one-quarter ">
					<label class="checkbox">
					  <input type="checkbox" name="allow_recording" id="allow_recording" <?php echo esc_attr(@$d['room']->allow_recording?'checked': '') ?> >
					  <?php echo esc_html(__("Allow recording",'manager-for-galene-videoconference')) ?>
					</label>
					<p class="help"><?php echo esc_html(__("Allow session recording",'manager-for-galene-videoconference')) ?></p>
				</div>			
				<div class="field  column  is-one-quarter ">
					<label class="checkbox"><?php echo esc_html(__("Max. attendees",'manager-for-galene-videoconference')) ?></label>
					  <input type="number"  name="max_clients" id="max_clients" value="<?php echo esc_attr(@$d['room']->max_clients?$d['room']->max_clients:0) ?>">
					<p class="help"><?php echo esc_html(__("Limit number of attendees (unlimited set to 0)",'manager-for-galene-videoconference')) ?></p>
				</div>			
			</div>	
			<footer class="card-footer">	</footer>	
		</div>
		
	</div>

  

	<div class="field is-grouped is-grouped-right mt-3">
	  <p class="control">
		<button class="button is-primary" type="submit" name="galene_action" id="galene_action" value="admin_update_room" >
		  <?php echo esc_html(__("Save",'manager-for-galene-videoconference')) ?>
		</button>
	  </p>
	  <p class="control">
		<a class="button is-light" href="<?php echo esc_url(Galmgr_util::add_arg([ 'galene_action' => 'admin_screen_roomsettings' ])) ?>">
		  <?php echo esc_html(__("Cancel",'manager-for-galene-videoconference')) ?>
		</a>
	  </p>
	</div>
</form>

<div id="modal-useraccess" class="modal is-large-modal">
  <div class="modal-background"></div>
  <div class="modal-card">
    <header class="modal-card-head">
      <p class="modal-card-title"><?php echo esc_html(__("Select users",'manager-for-galene-videoconference')) ?></p>
      <button class="delete" aria-label="close"></button>
    </header>
    <section class="modal-card-body" data-iframe="<?php echo esc_url(@$d['iframe_src']) ?>" >	
	</section>
  </div>
</div>


</section>

<div class="preloader">
  <img src="<?php echo esc_url(GALMGR_PLUGIN_URL . '/assets/spinner.svg') ?>" alt="spinner">
</div>

