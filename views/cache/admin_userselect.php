<?php class_exists('Galmgr_View_Generator') or exit; ?>
<?php if(is_array(@$d['msg'])): foreach($d['msg'] as $m): ?>

<article class="message is-small autohide <?php echo esc_html($m['type']) ?>">
  <div class="message-header is-rounded">
    <p><?php echo esc_html($m['title']) ?></p>
    <button class="delete" aria-label="delete"></button>
  </div>
</article>

<?php endforeach; endif; ?>

<form method="POST" >
<?php wp_nonce_field("admin_update_userselect","gal_form_id",false,true) ?>
<input type="hidden" name="galene_room" id="galene_room" value="<?php echo esc_html($d['room']) ?>" >

<p class="panel-heading mb-3">
	<?php echo esc_html(__("Edit accesslist of room",'manager-for-galene-videoconference')) ?>: <?php echo esc_html($d['room_display_name']) ?>
</p>


<div class="content">
  <table class="table is-striped is-fullwidth is-narrow is-hoverable" >
	<thead>
		<th class="text-vertical text-bottom is-size-7" ><?php echo esc_html(__("Operator",'manager-for-galene-videoconference')) ?></th>
		<th class="text-vertical text-bottom is-size-7" ><?php echo esc_html(__("Presenter",'manager-for-galene-videoconference')) ?></th>
		<th class="text-vertical text-bottom is-size-7" ><?php echo esc_html(__("Listener",'manager-for-galene-videoconference')) ?></th>
		<th class="text-bottom" ><?php echo esc_html(__("Displayname",'manager-for-galene-videoconference')) ?></th>
		<th class="text-bottom" ><?php echo esc_html(__("Loginname",'manager-for-galene-videoconference')) ?></th>
	</thead>
	<tbody>
  <?php foreach($d['users'] as $user): if($user['type'] != 0) continue; ?>
	<tr>
		<td>
			<div class="field">
			  <input class="is-checkradio is-success is-circle"  
					id="acc[<?php echo esc_html($user['id']) ?>][is_operator]"  
					name="acc[<?php echo esc_html($user['id']) ?>][is_operator]" 
					type="checkbox" 
					<?php echo esc_html($user['is_operator']? 'checked="checked"' : '') ?>  >
			  <label for="acc[<?php echo esc_html($user['id']) ?>][is_operator]"></label>
			</div>
		</td>
		<td>
			<div class="field">
			  <input class="is-checkradio is-success is-circle"  
					id="acc[<?php echo esc_html($user['id']) ?>][is_presenter]"  
					name="acc[<?php echo esc_html($user['id']) ?>][is_presenter]" 
					type="checkbox" 
					<?php echo esc_html($user['is_presenter']? 'checked="checked"' : '') ?>  >
			  <label for="acc[<?php echo esc_html($user['id']) ?>][is_presenter]"></label>
			</div>
		</td>
		<td>
			<div class="field">
			  <input class="is-checkradio is-success is-circle"  
					id="acc[<?php echo esc_html($user['id']) ?>][is_other]"  
					name="acc[<?php echo esc_html($user['id']) ?>][is_other]" 
					type="checkbox" 
					<?php echo esc_html($user['is_other']? 'checked="checked"' : '') ?>  >
			  <label for="acc[<?php echo esc_html($user['id']) ?>][is_other]"></label>
			</div>
		</td>
		<td><?php echo esc_html($user['displayName']) ?></td>
		<td><?php echo esc_html($user['login']) ?></td>
	</tr> 
  <?php endforeach; ?>
	<tr><td colspan="6"><h6 class="is-size-6 my-1 has-text-centered has-text-weight-bold"><?php echo esc_html(__("Wordpress Roles",'manager-for-galene-videoconference')) ?></h6></td></tr>
  <?php foreach($d['users'] as $user): if($user['type'] != 1) continue; ?>
	<tr>
		<td>
			<div class="field">
			  <input class="is-checkradio is-success is-circle"  
					id="acc[<?php echo esc_html($user['id']) ?>][is_operator]"  
					name="acc[<?php echo esc_html($user['id']) ?>][is_operator]" 
					type="checkbox" 
					<?php echo esc_html($user['is_operator']? 'checked="checked"' : '') ?>  >
			  <label for="acc[<?php echo esc_html($user['id']) ?>][is_operator]"></label>
			</div>
		</td>
		<td>
			<div class="field">
			  <input class="is-checkradio is-success is-circle"  
					id="acc[<?php echo esc_html($user['id']) ?>][is_presenter]"  
					name="acc[<?php echo esc_html($user['id']) ?>][is_presenter]" 
					type="checkbox" 
					<?php echo esc_html($user['is_presenter']? 'checked="checked"' : '') ?>  >
			  <label for="acc[<?php echo esc_html($user['id']) ?>][is_presenter]"></label>
			</div>
		</td>
		<td>
			<div class="field">
			  <input class="is-checkradio is-success is-circle"  
					id="acc[<?php echo esc_html($user['id']) ?>][is_other]"  
					name="acc[<?php echo esc_html($user['id']) ?>][is_other]" 
					type="checkbox" 
					<?php echo esc_html($user['is_other']? 'checked="checked"' : '') ?>  >
			  <label for="acc[<?php echo esc_html($user['id']) ?>][is_other]"></label>
			</div>
		</td>
		<td><?php echo esc_html(Galmgr_util::translate_wprole($user['displayName'])) ?></td>
		<td></td>
	</tr>
  
  <?php endforeach; ?>
	</tbody>
  </table>
</div>

<div class="field is-grouped is-grouped-right mt-3">
  <p class="control">
	<button class="button is-primary" type="submit" name="galene_action" id="galene_action" value="admin_update_userselect" >
	  <?php echo esc_html(__("Save",'manager-for-galene-videoconference')) ?>
	</button>
  </p>
  <p class="control">
	<a class="button is-light" href="<?php echo esc_html(Galmgr_util::add_arg([ 'galene_action' => 'admin_screen_roomedit', 'galene_room' => $d['room'], 'active_tab' => 'privileges-tab', ])) ?>#userlist">
	  <?php echo esc_html(__("Cancel",'manager-for-galene-videoconference')) ?>
	</a>
  </p>
</div>


</form>
<div class="preloader">
  <img src="<?php echo esc_url(GALMGR_PLUGIN_URL . '/assets/spinner.svg') ?>" alt="spinner">
</div>