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



<form class="box room-edit <?php echo $d['form_classes'] ?>" method="POST">
	<?php wp_nonce_field("admin_update_room","gal_form_id",false,true) ?>
	<input type="hidden" name="galene_room" id="galene_room" value="<?php echo $d['room']->id ?>" >
	<p class="panel-heading mb-3">
		<?php echo __("Room",'galene-mgr') ?>: "<?php echo @$d['room']->displayName?$d['room']->displayName:__("no name",'galene-mgr') ?>"
	</p>

	<div class="tabs is-boxed">
	  <ul class="tabs-menu">
		<li class="<?php echo (@$d['active_tab'] == 'settings-tab' || empty(@$d['active_tab']))? ' is-active ' : ''; ?>" data-target="settings-tab">
		  <a>
			<span><?php echo __("General",'galene-mgr') ?></span>
		  </a>
		</li>
		<li class="<?php echo (@$d['active_tab'] == 'privileges-tab')? ' is-active ' : ''; ?>" data-target="privileges-tab">
		  <a>
			<span><?php echo __("Users",'galene-mgr') ?></span>
		  </a>
		</li>
		<li class="<?php echo (@$d['active_tab'] == 'server-tab')? ' is-active ' : ''; ?>" data-target="server-tab">
		  <a>
			<span><?php echo __("Server",'galene-mgr') ?></span>
		  </a>
		</li>
	  </ul>
	</div>

	<div class="tab-content box" id="settings-tab">

		<div class="columns">
			<div class="column">
				<div class="field">
				  <label class="label"><?php echo __("Display name",'galene-mgr') ?></label>
				  <div class="control">
					<input name="displayName" required="required" id="displayName" class="input" type="text" value="<?php echo @$d['room']->displayName ?>" placeholder="<?php echo __("Display name",'galene-mgr') ?>" >
				  </div>
				  <p class="help"><?php echo __("Human readable name of room",'galene-mgr') ?></p>
				</div>
				
				<div class="field">
				  <label class="label"><?php echo __("Description",'galene-mgr') ?></label>
				  <div class="control">
					<textarea name="description" id="description" class="textarea is-small" placeholder="<?php echo __("Description",'galene-mgr') ?>"><?php echo @$d['room']->description ?></textarea>
				  </div>
				  <p class="help"><?php echo __("Description that is visible on rooms listing",'galene-mgr') ?></p>
				</div>

				<div class="field ">			
					<input type="checkbox" class="is-checkradio" name="show_on_roomslist" id="show_on_roomslist" <?php echo @$d['room']->show_on_roomslist?'checked' : '' ?>  >
					<label for="show_on_roomslist"><?php echo __("Show this room on rooms listing",'galene-mgr') ?></label>
					<p class="help"><?php echo __("If checked, this room will be visible on the public rooms listing",'galene-mgr') ?></p>
				</div>	
			</div>
			
			<div class="column has-background-white-ter">
				<input type="hidden" name="access" value="code">
				<label class="label"><?php echo __("Room access conditions / Security",'galene-mgr') ?></label>
				
				<div class="fld-access-code mb-3">
					<input type="checkbox" class="is-checkradio has-background-color hide-unchecked" name="needs_code" id="needs_code" <?php echo @$d['room']->needs_code?'checked' : '' ?>  >
					<label for="needs_code"><?php echo __("Use <b>Code</b>",'galene-mgr') ?></label>
					<div class="mt-2">
						<div class="field has-addons" >
						  <div class="control">
							<input name="room_accesscode" id="room_accesscode" class="input" type="text" value="<?php echo @$d['room']->room_accesscode ?>" >
						  </div>
						  <div class="control">
							<a id="generate-code" class="button is-warning" title="Generate code">
								<figure class="image is-24x24">
								  <img src="<?php echo GALENE_PLUGIN_URL ?>/assets/magic-wand-outline.svg">
								</figure>
							</a>
						  </div>
						</div>
					</div>
				</div>
				
				<div class="field ">			
					<input type="checkbox" class="is-checkradio has-background-color" name="needs_nickname" id="needs_nickname" <?php echo @$d['room']->needs_nickname?'checked' : '' ?>  >
					<label for="needs_nickname"><?php echo __("Needs <b>nickname</b>",'galene-mgr') ?></label>
					 <p class="help"><?php echo __("Otherwise random name will be generated",'galene-mgr') ?></p>
				</div>	
				
				<label class="label"><?php echo __("Authentication necessary for:",'galene-mgr') ?></label>
				<div class="field ">			
					<input type="checkbox" class="is-checkradio has-background-color" name="auth_for_other" id="auth_for_other" <?php echo @$d['room']->auth_for_other?'checked' : '' ?>  >
					<label for="auth_for_other"><?php echo __("Attendance as listener",'galene-mgr') ?></label>
				</div>	
				<div class="field ">			
					<input type="checkbox" class="is-checkradio has-background-color" name="auth_for_presentator" id="auth_for_presentator" <?php echo @$d['room']->auth_for_presentator?'checked' : '' ?>  >
					<label for="auth_for_presentator"><?php echo __("Attendance as presenter",'galene-mgr') ?></label>
				</div>	
				<div class="field ">			
					<input type="checkbox" class="is-checkradio has-background-color" name="auth_for_operator" id="auth_for_operator" <?php echo @$d['room']->auth_for_operator?'checked' : '' ?>  >
					<label for="auth_for_operator"><?php echo __("Attendance as operator",'galene-mgr') ?></label>
				</div>	
			</div>
		</div>
		
		<div class="field">
		  <label class="label"><?php echo __("Link for listener",'galene-mgr') ?></label>
		  <div class="field is-grouped">
			  <div class="control">
				<a href="<?php echo @$d['room_urls']['recipient'] ?>" target="_blank"><?php echo @$d['room_urls']['recipient'] ?></a>
			  </div>
			  <div class="control">
				<button onclick="copyText('<?php echo @$d['room_urls']['recipient'] ?>'); return false;"><?php echo __("Copy link",'galene-mgr') ?></button>
			  </div>
		  </div>
		</div> 
		
		<div class="field">
		  <label class="label"><?php echo __("Link for presenter",'galene-mgr') ?></label>
		  <div class="field is-grouped">
			  <div class="control">
				<a href="<?php echo @$d['room_urls']['presenter'] ?>" target="_blank"><?php echo @$d['room_urls']['presenter'] ?></a>
			  </div>
			  <div class="control">
				<button onclick="copyText('<?php echo @$d['room_urls']['presenter'] ?>'); return false;"><?php echo __("Copy link",'galene-mgr') ?></button>
			  </div>
		  </div>
		</div> 
		
		<div class="field">
		  <label class="label"><?php echo __("Link for operator",'galene-mgr') ?></label>
		  <div class="field is-grouped">
			  <div class="control">
				<a href="<?php echo @$d['room_urls']['admin'] ?>" target="_blank"><?php echo @$d['room_urls']['admin'] ?></a>
			  </div>
			  <div class="control">
				<button onclick="copyText('<?php echo @$d['room_urls']['admin'] ?>'); return false;"><?php echo __("Copy link",'galene-mgr') ?></button>
			  </div>
		  </div>
		</div> 
		
	</div>
  
	<div class="tab-content" id="privileges-tab">
		<div class="box">
		<?php if(@$d['room']->id && $d['room']->id >= 0) : ?>
			<p class="is-pulled-right">
				<a class="button is-info" type="button" aria-haspopup="true" 
					href="<?php echo Gal_util::add_arg(['galene_action' => 'admin_screen_userselect', 'galene_room' => $d['room']->id , 'room_display_name' => $d['room']->displayName ]); ?>">
						<?php echo __("Edit accesslist",'galene-mgr') ?>
				</a>
			</p>			
			  <table id="userlist" class="table is-striped is-fullwidth is-narrow is-hoverable" >
				<thead>
					<th class="text-vertical text-bottom is-size-7" ><?php echo __("Operator",'galene-mgr') ?></th>
					<th class="text-vertical text-bottom is-size-7" ><?php echo __("Presenter",'galene-mgr') ?></th>
					<th class="text-vertical text-bottom is-size-7" ><?php echo __("Listener",'galene-mgr') ?></th>
					<th class="text-bottom" ><?php echo __("Display name",'galene-mgr') ?></th>
					<th class="text-bottom" ><?php echo __("Login name",'galene-mgr') ?></th>
				</thead>
				<tbody>
			  <?php foreach(@$d['users'] as $user): if($user['type'] != 0) continue; ?>
				<tr>
					<td class="<?php echo $user['is_operator']? 'is-checkmark' : '' ?>"><div></div></td>
					<td class="<?php echo $user['is_presenter']? 'is-checkmark' : '' ?>"><div></div></td>
					<td class="<?php echo $user['is_other']? 'is-checkmark' : '' ?>"><div></div></td>
					<td><?php echo $user['displayName'] ?></td>
					<td><?php echo $user['login'] ?></td>
				</tr>			  
			  <?php endforeach; ?>
			  <tr><td colspan="6"><h6 class="is-size-6 my-1 has-text-centered has-text-weight-bold"><?php echo __("Wordpress Roles",'galene-mgr') ?></h6></td></tr>
			  <?php foreach(@$d['users'] as $user): if($user['type'] != 1) continue; ?>
				<tr>
					<td class="<?php echo $user['is_operator']? 'is-checkmark' : '' ?>"><div></div></td>
					<td class="<?php echo $user['is_presenter']? 'is-checkmark' : '' ?>"><div></div></td>
					<td class="<?php echo $user['is_other']? 'is-checkmark' : '' ?>"><div></div></td>
					<td><?php echo Gal_util::translate_wprole($user['displayName']) ?></td>
					<td></td>
				</tr>			  
			  <?php endforeach; ?>

				</tbody>
			  </table>
		<?php else: ?>
			<h4 class="subtitle is-4"><?php echo __("Userslist can only be edited after saving roomsettings",'galene-mgr') ?></h4>
		<?php endif; ?>
 		</div>	
	</div>
	
	<div class="tab-content" id="server-tab">
		<div class="columns is-flex-direction-row-reverse">
			<div class="column is-3   has-background-white-bis" >
				<iframe style="display:none;" name="dl_dummy_frame" id="dl_dummy_frame" ></iframe>
			
				<aside class="menu">
				  <p class="menu-label">
					<?php echo __("Import/Export",'galene-mgr') ?>
				  </p>
				  <ul class="menu-list">
					<li>
						<a target="dl_dummy_frame" href="<?php echo Gal_util::add_arg([ 'galene_action' => 'admin_download_room_json', 'galene_room' => $d['room']->id ]) ?>" >
						  <span class="is-underlined"><?php echo __("Export serverfile",'galene-mgr') ?></span>
						  <p class="help">(<?php echo __("Group config (.json) file for Galène server",'galene-mgr') ?>)</p>
						</a>
					</li>
					<li class="import-srv-json is-primary">
						<a class="is-primary">
							<label for="import-srv-json" class="is-underlined"><?php echo __("Import serverfile",'galene-mgr') ?></label>
							<input type="file" id="import-srv-json" accept="*.json">
							<p class="help">(<?php echo __("Group config (.json) file from Galène server",'galene-mgr') ?>)</p>
						</a>
					</li>
				  </ul>
				</aside>			
			</div>				
			<div class="column" >
				<div class="field">
				  <label class="label"><?php echo __("Galène group name",'galene-mgr') ?></label>
				  <div class="control">
					<input required="required" name="galene_group" id="galene_group" class="input" type="text" value="<?php echo @$d['room']->galene_group ?>" >
				  </div>
				  <p class="help"><?php echo __("Name of Galène group (filename of .json config) on Galène Server",'galene-mgr') ?></p>
				</div> 


				<div class="field">
				  <label class="label"><?php echo __("Galène key",'galene-mgr') ?></label>
				  <div class="control">
					<input required="required" name="key64" id="key64" class="input" type="text" value="<?php echo $d['room']->key64 ?>" >
				  </div>
				  <p class="help"><?php echo __("JWT key of Galène group (base64url format)",'galene-mgr') ?></p>
				</div>
			</div>				
		</div>				
		
		<div class="card field">
			<header class="card-header">
				<p class="card-header-title"><?php echo __("Options",'galene-mgr') ?></p>
			</header>

			<div class="card-content columns is-multiline">
				<div class="field  column  is-one-quarter ">
					<label class="checkbox">
					  <input type="checkbox" name="allow_anonymous" id="allow_anonymous" <?php echo @$d['room']->allow_anonymous?'checked': '' ?> >
					  <?php echo __("Allow anonymous attendance",'galene-mgr') ?>
					</label>
					<p class="help"><?php echo __("Attendance without nickname is possible",'galene-mgr') ?></p>
				</div>			
				<div class="field  column  is-one-quarter  ">
					<label class="checkbox">
					  <input type="checkbox" name="allow_subgroups" id="allow_subgroups" <?php echo @$d['room']->allow_subgroups?'checked': '' ?> >
					  <?php echo __("Allow subgroups",'galene-mgr') ?>
					</label>
					<p class="help"><?php echo __("Subgroup rooms (aka breakout rooms) can be createt",'galene-mgr') ?></p>
				</div>			
				<div class="field  column  is-one-quarter ">
					<label class="checkbox">
					  <input type="checkbox" name="autolock" id="autolock" <?php echo @$d['room']->autolock?'checked': '' ?> >
					  <?php echo __("Auto room locking",'galene-mgr') ?>
					</label>
					<p class="help"><?php echo __("Room will be locked automatically without operator",'galene-mgr') ?></p>
				</div>			
				<div class="field  column  is-one-quarter ">
					<label class="checkbox">
					  <input type="checkbox" name="allow_recording" id="allow_recording" <?php echo @$d['room']->allow_recording?'checked': '' ?> >
					  <?php echo __("Allow recording",'galene-mgr') ?>
					</label>
					<p class="help"><?php echo __("Allow session recording",'galene-mgr') ?></p>
				</div>			
				<div class="field  column  is-one-quarter ">
					<label class="checkbox"><?php echo __("Max. attendees",'galene-mgr') ?></label>
					  <input type="number"  name="max_clients" id="max_clients" value="<?php echo @$d['room']->max_clients?$d['room']->max_clients:0 ?>">
					<p class="help"><?php echo __("Limit number of attendees (unlimited set to 0)",'galene-mgr') ?></p>
				</div>			
			</div>	
			<footer class="card-footer">	</footer>	
		</div>
		
	</div>

  

	<div class="field is-grouped is-grouped-right mt-3">
	  <p class="control">
		<button class="button is-primary" type="submit" name="galene_action" id="galene_action" value="admin_update_room" >
		  <?php echo __("Save",'galene-mgr') ?>
		</button>
	  </p>
	  <p class="control">
		<a class="button is-light" href="<?php echo Gal_util::add_arg([ 'galene_action' => 'admin_screen_roomsettings' ]) ?>">
		  <?php echo __("Cancel",'galene-mgr') ?>
		</a>
	  </p>
	</div>
</form>

<div id="modal-useraccess" class="modal is-large-modal">
  <div class="modal-background"></div>
  <div class="modal-card">
    <header class="modal-card-head">
      <p class="modal-card-title"><?php echo __("Select users",'galene-mgr') ?></p>
      <button class="delete" aria-label="close"></button>
    </header>
    <section class="modal-card-body" data-iframe="<?php echo @$d['iframe_src'] ?>" >	
	</section>
  </div>
</div>


</section>

<div class="preloader">
  <img src="<?php echo GALENE_PLUGIN_URL . '/assets/spinner.svg'; ?>" alt="spinner">
</div>

