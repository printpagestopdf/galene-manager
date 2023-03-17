<?php class_exists('Gal_View_Generator') or exit; ?>
<?php if(is_array(@$d['msg'])): foreach($d['msg'] as $m): ?>

<article class="message is-small autohide <?php echo $m['type'] ?>">
  <div class="message-header is-rounded">
    <p><?php echo $m['title'] ?></p>
    <button class="delete" aria-label="delete"></button>
  </div>
</article>

<?php endforeach; endif; ?>

<form method="POST" >
<?php wp_nonce_field("admin_update_userselect","gal_form_id",false,true) ?>
<input type="hidden" name="galene_room" id="galene_room" value="<?php echo $d['room'] ?>" >

<p class="panel-heading mb-3">
	<?php echo __("Edit accesslist of room",'galene-mgr') ?>: <?php echo $d['room_display_name'] ?>
</p>


<div class="content">
  <table class="table is-striped is-fullwidth is-narrow is-hoverable" >
	<thead>
		<th class="text-vertical text-bottom is-size-7" ><?php echo __("Operator",'galene-mgr') ?></th>
		<th class="text-vertical text-bottom is-size-7" ><?php echo __("Presenter",'galene-mgr') ?></th>
		<th class="text-vertical text-bottom is-size-7" ><?php echo __("Listener",'galene-mgr') ?></th>
		<th class="text-bottom" ><?php echo __("Displayname",'galene-mgr') ?></th>
		<th class="text-bottom" ><?php echo __("Loginname",'galene-mgr') ?></th>
	</thead>
	<tbody>
  <?php foreach($d['users'] as $user): if($user['type'] != 0) continue; ?>
	<tr>
		<td>
			<div class="field">
			  <input class="is-checkradio is-success is-circle"  
					id="acc[<?php echo $user['id'] ?>][is_operator]"  
					name="acc[<?php echo $user['id'] ?>][is_operator]" 
					type="checkbox" 
					<?php echo $user['is_operator']? 'checked="checked"' : '' ?>  >
			  <label for="acc[<?php echo $user['id'] ?>][is_operator]"></label>
			</div>
		</td>
		<td>
			<div class="field">
			  <input class="is-checkradio is-success is-circle"  
					id="acc[<?php echo $user['id'] ?>][is_presenter]"  
					name="acc[<?php echo $user['id'] ?>][is_presenter]" 
					type="checkbox" 
					<?php echo $user['is_presenter']? 'checked="checked"' : '' ?>  >
			  <label for="acc[<?php echo $user['id'] ?>][is_presenter]"></label>
			</div>
		</td>
		<td>
			<div class="field">
			  <input class="is-checkradio is-success is-circle"  
					id="acc[<?php echo $user['id'] ?>][is_other]"  
					name="acc[<?php echo $user['id'] ?>][is_other]" 
					type="checkbox" 
					<?php echo $user['is_other']? 'checked="checked"' : '' ?>  >
			  <label for="acc[<?php echo $user['id'] ?>][is_other]"></label>
			</div>
		</td>
		<td><?php echo $user['displayName'] ?></td>
		<td><?php echo $user['login'] ?></td>
	</tr> 
  <?php endforeach; ?>
	<tr><td colspan="6"><h6 class="is-size-6 my-1 has-text-centered has-text-weight-bold"><?php echo __("Wordpress Roles",'galene-mgr') ?></h6></td></tr>
  <?php foreach($d['users'] as $user): if($user['type'] != 1) continue; ?>
	<tr>
		<td>
			<div class="field">
			  <input class="is-checkradio is-success is-circle"  
					id="acc[<?php echo $user['id'] ?>][is_operator]"  
					name="acc[<?php echo $user['id'] ?>][is_operator]" 
					type="checkbox" 
					<?php echo $user['is_operator']? 'checked="checked"' : '' ?>  >
			  <label for="acc[<?php echo $user['id'] ?>][is_operator]"></label>
			</div>
		</td>
		<td>
			<div class="field">
			  <input class="is-checkradio is-success is-circle"  
					id="acc[<?php echo $user['id'] ?>][is_presenter]"  
					name="acc[<?php echo $user['id'] ?>][is_presenter]" 
					type="checkbox" 
					<?php echo $user['is_presenter']? 'checked="checked"' : '' ?>  >
			  <label for="acc[<?php echo $user['id'] ?>][is_presenter]"></label>
			</div>
		</td>
		<td>
			<div class="field">
			  <input class="is-checkradio is-success is-circle"  
					id="acc[<?php echo $user['id'] ?>][is_other]"  
					name="acc[<?php echo $user['id'] ?>][is_other]" 
					type="checkbox" 
					<?php echo $user['is_other']? 'checked="checked"' : '' ?>  >
			  <label for="acc[<?php echo $user['id'] ?>][is_other]"></label>
			</div>
		</td>
		<td><?php echo Gal_util::translate_wprole($user['displayName']) ?></td>
		<td></td>
	</tr>
  
  <?php endforeach; ?>
	</tbody>
  </table>
</div>

<div class="field is-grouped is-grouped-right mt-3">
  <p class="control">
	<button class="button is-primary" type="submit" name="galene_action" id="galene_action" value="admin_update_userselect" >
	  <?php echo __("Save",'galene-mgr') ?>
	</button>
  </p>
  <p class="control">
	<a class="button is-light" href="<?php echo Gal_util::add_arg([ 'galene_action' => 'admin_screen_roomedit', 'galene_room' => $d['room'], 'active_tab' => 'privileges-tab', ]) ?>#userlist">
	  <?php echo __("Cancel",'galene-mgr') ?>
	</a>
  </p>
</div>


</form>
<div class="preloader">
  <img src="<?php echo GALENE_PLUGIN_URL . '/assets/spinner.svg'; ?>" alt="spinner">
</div>