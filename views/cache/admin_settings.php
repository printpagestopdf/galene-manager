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



<form method="POST"  enctype="multipart/form-data" >
<?php wp_nonce_field("admin_update_settings","gal_form_id",false,true) ?>
<nav class="content">
  <p class="panel-heading"><?php echo esc_html(__("System settings",'manager-for-galene-videoconference')) ?></p>

	<div class="tabs is-boxed">
	  <ul class="tabs-menu">
		<li class="is-active" data-target="settings-tab">
		  <a>
			<span><?php echo esc_html(__("General",'manager-for-galene-videoconference')) ?></span>
		  </a>
		</li>
		<li class="" data-target="server-tab">
		  <a>
			<span><?php echo esc_html(__("Server connection",'manager-for-galene-videoconference')) ?></span>
		  </a>
		</li>
	  </ul>
	</div>
	<div class="tab-content box" id="settings-tab">
		<div class="field">
		  <label class="label"><?php echo esc_html(__("Public base link of Galène Server",'manager-for-galene-videoconference')) ?></label>
		  <div class="control">
			<input required="required" name="general[galene_url]" id="general[galene_url]" class="input" type="text" value="<?php echo esc_attr($d['settings']['general']['galene_url']) ?>" >
		  </div>
		  <p class="help"><?php echo esc_html(__("Galène link without name of room (e.g. https://mygalene.org/group/)",'manager-for-galene-videoconference')) ?></p>
		</div>

		<div class="field">
			<label class="label"><?php echo esc_html(__("Token lifetime",'manager-for-galene-videoconference')) ?></label>
			<div class="control">
			  <input required="required" name="general[galene_token_exp]" id="general[galene_token_exp]" class="input" min="0" oninput="validity.valid||(value='0');" type="number" value="<?php echo esc_attr(@$d['settings']['general']['galene_token_exp']?:30) ?>" >
			</div>
			<p class="help"><?php echo esc_html(__("Lifetime of the JWT token for Galène Server authentication",'manager-for-galene-videoconference')) ?></p>
		  </div>

		  <div class="field">
			<label class="label"><?php echo esc_html(__("Shift issued at",'manager-for-galene-videoconference')) ?></label>
			<div class="control">
			  <input required="required" name="general[galene_issued_at]" id="general[galene_issued_at]" class="input" min="0" oninput="validity.valid||(value='0');" type="number" value="<?php echo esc_attr(@$d['settings']['general']['galene_issued_at']?:0) ?>" >
			</div>
			<p class="help"><?php echo esc_html(__("Subtract seconds from now for JWT issued time (fix time difference between Galène server and manager host)",'manager-for-galene-videoconference')) ?></p>
		  </div>
	  
	</div>
	
	<div class="tab-content mx-5 box" id="server-tab">
		<div class="accordion">	
			<div class="card a-container mb-2 <?php echo esc_html((@$d['settings']['direct_server_access'] == 'filesystem')?' active ' : '') ?>">
			  <div class="panel-heading is-flex p-0 <?php echo esc_html((@$d['settings']['direct_server_access'] == 'filesystem')?' has-background-success ' : '') ?>">
				<div class="field is-flex is-align-items-center ml-2">			
					<input type="checkbox" 
							class="is-checkradio has-background-color act-as-radio is-small is-circle" 
							name="direct_server_access" 
							value="filesystem" 
							id="direct_server_access_filesystem" 
							<?php echo esc_html((@$d['settings']['direct_server_access'] == 'filesystem')?' checked ' : '') ?>  >
					<label for="direct_server_access_filesystem" class="clickable"></label>
				</div>	
				<div class="card-header-title  a-btn  mb-0 pl-0">
				  <div class="is-flex-grow-1"><?php echo esc_html(__("Direct filesystem access",'manager-for-galene-videoconference')) ?></div>
				 <span class="card-header-icon navbar-link " aria-label="more options"></span>
				</div>
			  </div>

			  <div class="card-content  px-5 a-panel">
				<div class="field">
				  <label class="label"><?php echo esc_html(__("Server path",'manager-for-galene-videoconference')) ?></label>
				  <div class="control">
					<input name="filesystem[server_path]" id="filesystem[server_path]" class="input" type="text" value="<?php echo esc_attr(@$d['settings']['filesystem']['server_path']) ?>" >
				  </div>
				  <p class="help"><?php echo esc_html(__("Full path to Galène group directory",'manager-for-galene-videoconference')) ?></p>
				</div>
			  
				<hr />
				<div class="field">
				  <div class="control">
					<a class="button is-outlined is-small is-warning"  target="_blank" href="<?php echo esc_url(Galmgr_util::add_arg([ 'galene_action' => 'admin_test_galene_access' ])) ?>" >
					  <?php echo esc_html(__("Test access to groups directory",'manager-for-galene-videoconference')) ?>
					</a>
				  </div>
				  <p class="help"><?php echo esc_html(__("Please save settings/changes before testing",'manager-for-galene-videoconference')) ?></p>
				</div>
				
			  </div>
			  <footer class="card-footer"></footer>
			</div>
		
			<div class="card a-container mb-2 <?php echo sanitize_html_class((@$d['settings']['direct_server_access'] == 'sftp')?' active ' : '') ?>">
			  <div class="panel-heading is-flex p-0 <?php echo sanitize_html_class((@$d['settings']['direct_server_access'] == 'sftp')?' has-background-success ' : '') ?>">
				<div class="field is-flex is-align-items-center ml-2">			
					<input type="checkbox" 
					class="is-checkradio has-background-color act-as-radio is-small is-circle" 
					name="direct_server_access" 
					value="sftp" 
					id="direct_server_access_sftp" 
					<?php echo esc_attr((@$d['settings']['direct_server_access'] == 'sftp')?' checked ' : '') ?>  >
					<label for="direct_server_access_sftp" class="clickable"></label>
				</div>	
				<div class="card-header-title  a-btn  mb-0 pl-0">
				  <div class="is-flex-grow-1"><?php echo esc_html(__("Server acces by SFTP",'manager-for-galene-videoconference')) ?></div>
				 <span class="card-header-icon navbar-link " aria-label="more options"></span>
				</div>
			  </div>
			  			  
			  <div class="card-content  px-5 a-panel">
			  

				<div class="field">
				  <label class="label"><?php echo esc_html(__("Server path",'manager-for-galene-videoconference')) ?></label>
				  <div class="control">
					<input name="sftp[server_path]" id="sftp[server_path]" class="input" type="text" value="<?php echo esc_attr(@$d['settings']['sftp']['server_path']) ?>" >
				  </div>
				  <p class="help"><?php echo esc_html(__("Path to Galène group directory at server",'manager-for-galene-videoconference')) ?></p>
				</div>
				
				<div class="field columns">
					<div class="field column">
					  <label class="label"><?php echo esc_html(__("Hostname/IP",'manager-for-galene-videoconference')) ?></label>
					  <div class="control">
						<input name="sftp[host]" id="sftp[host]" class="input" type="text" value="<?php echo esc_attr(@$d['settings']['sftp']['host']) ?>" >
					  </div>
					  <p class="help"><?php echo esc_html(__("Domainname (hostname) or IP to connect Server by SFTP",'manager-for-galene-videoconference')) ?></p>
					</div>
					
					<div class="field column">
					  <label class="label"><?php echo esc_html(__("Port",'manager-for-galene-videoconference')) ?></label>
					  <div class="control">
						<input name="sftp[port]" id="sftp[port]" class="input" type="text" value="<?php echo esc_attr(@$d['settings']['sftp']['port']) ?>" >
					  </div>
					  <p class="help"><?php echo esc_html(__("SSH/SFTP port number of Galène server",'manager-for-galene-videoconference')) ?></p>
					</div>
					
				
				</div>
				<hr />
				<h5 class="title is-5"><?php echo esc_html(__("Authentication",'manager-for-galene-videoconference')) ?></h5>
				<?php if (!defined('GALMGR_CRYPT_KEY')): ?>
					<div class="box">
							<?php echo esc_html(__("If you are using SFTP access, authentication data is stored in database. It is strongly advised to encrypt these.",'manager-for-galene-videoconference')) ?>
							&#32;<?php echo esc_html(__("Ask your Worpress administrator to add this line to wp-config.php before saving this form:",'manager-for-galene-videoconference')) ?><br>
							<code data-select-link-text="1" class="has-text-black-bis is-family-monospace"><?php echo esc_html("define( 'GALMGR_CRYPT_KEY', '" .  base64_encode(random_bytes(32))  . "' );") ?></code>
					</div>
				<?php endif; ?>

					<div class="field">
					  <label class="label"><?php echo esc_html(__("Username",'manager-for-galene-videoconference')) ?></label>
					  <div class="control">
						<input name="sftp[login]" id="sftp[login]" class="input" type="text" value="<?php echo esc_attr(@$d['settings']['sftp']['login']) ?>" >
					  </div>
					  <p class="help"><?php echo esc_html(__("Loginname at Galène Server for SSH/SFTP access",'manager-for-galene-videoconference')) ?></p>
					</div> 
				
				<h6 class="title is-6"><?php echo esc_html(__("Method of authentication",'manager-for-galene-videoconference')) ?></h6>
				<input class="is-checkradio is-info hide-descendant" 
						value="login_password" 
						id="sftp_auth_mode_lopw" 
						type="radio" 
						name="sftp[auth_mode]" 
						<?php echo esc_attr((@$d['settings']['sftp']['auth_mode'] == 'login_password')?' checked ' : '') ?> >
				<label class="ml-0 is-block" for="sftp_auth_mode_lopw"><?php echo esc_html(__("Username/Password",'manager-for-galene-videoconference')) ?></label>
				  				
				<div class="descendant-to-hide pl-5">

					<div class="field">
					  <label class="label"><?php echo esc_html(__("Password",'manager-for-galene-videoconference')) ?></label>
					  <div class="control">
						<input name="sftp[password_new]" id="sftp[password_new]" class="input" type="password"  >
					  </div>
					  <p class="help"><?php echo esc_html(__("Password at Galène Server for SSH/SFTP access",'manager-for-galene-videoconference')) ?></p>
					</div> 

					<div class="field">
					  <label class="label"><?php echo esc_html(__("Password (repeat)",'manager-for-galene-videoconference')) ?></label>
					  <div class="control">
						<input class="input" name="sftp[password_repeat]" id="sftp[password_repeat]" type="password"  >
					  </div>
					  <p class="help"><?php echo esc_html(__("Repeat your password",'manager-for-galene-videoconference')) ?></p>
					</div> 
				</div>
				  
				  
				<input class="is-checkradio is-info hide-descendant" 
						value="private_key" 
						id="sftp_auth_mode_key" 
						type="radio" 
						name="sftp[auth_mode]"
						<?php echo esc_attr((@$d['settings']['sftp']['auth_mode'] == 'private_key')?' checked ' : '') ?> >
				<label class="mt-5 ml-0 is-block" for="sftp_auth_mode_key"><?php echo esc_html(__("Public/Private key authentication",'manager-for-galene-videoconference')) ?></label>
					
				<div class="key_file_upload descendant-to-hide pl-5">
					<div class="has-text-weight-bold mt-5"><?php echo esc_html(__("Upload keyfile",'manager-for-galene-videoconference')) ?></div>
					<div class="file">
					  <label class="file-label">
						<input class="file-input" type="file" name="sftp-keyfile">
						<span class="file-cta">

						<span class="image is-24x24 mr-2">
						  <img src="<?php echo esc_url( GALMGR_PLUGIN_URL . '/assets/key-outline.svg') ?>">
						</span>

						  <span class="file-label">
							<?php echo esc_html(__("Select private key file",'manager-for-galene-videoconference')) ?>
						  </span>
						</span>
					  </label>
					</div>
					<p class="help"><?php echo esc_html(__("Upload your private key file to this app (RSA, DSA, ECDSA, EdDSA (Ed25519))",'manager-for-galene-videoconference')) ?></p>

					<div class="field ">			
						<input type="checkbox" class="is-checkradio  hide-descendant" value="1"
								name="sftp[is-key-encrypted]" id="sftp[is-key-encrypted]" 
								<?php echo esc_attr((@$d['settings']['sftp']['is-key-encrypted'] == '1')?'checked' : '') ?>  >
						<label for="sftp[is-key-encrypted]"><?php echo esc_html(__("Private key is encrypted with password",'manager-for-galene-videoconference')) ?></label>

						<div class="descendant-to-hide">

							<div class="field">
							  <label class="label"><?php echo esc_html(__("Password",'manager-for-galene-videoconference')) ?></label>
							  <div class="control">
								<input name="sftp[key_password_new]" id="sftp[key_password_new]" class="input" type="password"  >
							  </div>
							  <p class="help"><?php echo esc_html(__("Password for the private key file",'manager-for-galene-videoconference')) ?></p>
							</div> 

							<div class="field">
							  <label class="label"><?php echo esc_html(__("Password (repeat)",'manager-for-galene-videoconference')) ?></label>
							  <div class="control">
								<input class="input" name="sftp[key_password_repeat]" id="sftp[key_password_repeat]" type="password"  >
							  </div>
							  <p class="help"><?php echo esc_html(__("Repeat your password",'manager-for-galene-videoconference')) ?></p>
							</div> 
						</div>
					</div>
				</div>	
			  
				<hr />
				<div class="field">
				  <div class="control">
					<a class="button is-outlined is-small is-warning"  target="_blank" href="<?php echo esc_url(Galmgr_util::add_arg([ 'galene_action' => 'admin_test_galene_access' ])) ?>" >
					  <?php echo esc_html(__("Test SFTP access",'manager-for-galene-videoconference')) ?>
					</a>
				  </div>
				  <p class="help"><?php echo esc_html(__("Please save settings/changes before testing",'manager-for-galene-videoconference')) ?></p>
				</div>

			  </div>
			  <footer class="card-footer"></footer>
			</div>
			
		</div>	
	
 </div> 
</nav>
	<div class="field is-grouped is-grouped-right mt-3">
	  <p class="control">
		<button class="button is-primary" type="submit" name="galene_action" id="galene_action" value="admin_update_settings" >
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


</section>

<div class="preloader">
  <img src="<?php echo esc_url(GALMGR_PLUGIN_URL . '/assets/spinner.svg') ?>" alt="spinner">
</div>

