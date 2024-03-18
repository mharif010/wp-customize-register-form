<?php 
//customize a register form by custom fields 
add_action('register_form', function () {
	$first_name = $_POST['first_name'] ?? '';
	$birthday = $_POST['birthday'] ?? '';
?>
	<p>
		<label for="first_name">
			<?php _e('First Name', 'reg-form'); ?>
		</label>
		<input type="text" name="first_name" id="first_name" value="<?php echo $first_name; ?>">
	</p>
	<p>
		<label for="birthday">
			<?php _e('Birth Date', 'reg-form'); ?>
		</label>
		<input type="date" name="birthday" id="birthday" value="<?php echo $birthday; ?>">
	</p>
<?php
});

add_filter('registration_errors', function ($errors, $sanitized_user_login, $user_email) {
	if (empty($_POST['first_name'])) {
		$errors->add('first_name_blank', __('<strong>Error:</strong> First name cannot be blank', 'reg-form'));
	}
	if (empty($_POST['birthday'])) {
		$errors->add('birthday_blank', __('<strong>Error:</strong> Date of Birth cannot be blank', 'reg-form'));
	}
	return $errors;
}, 10, 3);

add_action('user_register', function ($user_id) {
	if (!empty($_POST['first_name'])) {
		update_user_meta($user_id, 'first_name', $_POST['first_name']);
	}
	if (!empty($_POST['birthday'])) {
		update_user_meta($user_id, 'birthday', $_POST['birthday']);
	}
}, 10, 1);

add_action('show_user_profile', 'create_field_on_admin_dashboard');
add_action('edit_user_profile', 'create_field_on_admin_dashboard');
function create_field_on_admin_dashboard($user)
{
?>
	<h3>It's Your Birthday 12</h3>
	<table class="form-table">
		<tr>
			<th>
				<label for="birthday">Birthday</label>
			</th>
			<td>
				<input type="date" class="regular-text ltr" id="birthday" name="birthday" value="<?= esc_attr(get_user_meta($user->ID, 'birthday', true)) ?>" title="Please use YYYY-MM-DD as the date format." pattern="(19[0-9][0-9]|20[0-9][0-9])-(1[0-2]|0[1-9])-(3[01]|[21][0-9]|0[1-9])" required>
				<p class="description">
					Please enter your birthday date.
				</p>
			</td>
		</tr>
	</table>
<?php
}

// Add the save action to user's own profile editing screen update.
add_action(
	'personal_options_update',
	'usermeta_form_field_birthday_update'
);

add_action(
	'edit_user_profile_update',
	'usermeta_form_field_birthday_update'
);
function wporg_usermeta_form_field_birthday_update($user_id)
{

	if (!current_user_can('edit_user', $user_id)) {
		return false;
	}

	return update_user_meta(
		$user_id,
		'birthday',
		$_POST['birthday']
	);
}
