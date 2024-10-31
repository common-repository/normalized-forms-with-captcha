<?php
/*
Plugin Name: Normalized Forms with Captcha
Plugin URI: http://www.globalwebmethods.com
Description: Responsive Contact Form, Registration Form and Login Forms for custom pages with captcha. Fixes no emails from contact form problems and spam sign ups from robots.
Version: 1.0
Author: Trigve Hagen
Author URI: http://www.globalwebmethods.com
*/

/**************************** Captcha *******************************/

function gwb_create_random_string() {
	$num = rand(1, 2); $newNumber = '';
	for($i=0; $i<$num; $i++) { $count_same = rand(1, 3);
		for($j=0; $j<$count_same; $j++) { $numtwo = rand(97, 122); $newNumber .= chr($numtwo); }
		$count_same = rand(1, 3); for($k=0; $k<$count_same; $k++) $newNumber .= rand(0, 6);
	}
	return $newNumber;
}

function gwb_create_image() {
	$x = 150; $y = 55;
	$output = "images/textimage.jpg";
	$path_to_file = home_url( "/wp-content/plugins/normalized-forms-with-captcha/".$output );
	$image = imagecreate($x, $y);
	//$my_img = imagecreate( 200, 80 );
	$text = gwb_create_random_string();
	$background = imagecolorallocate( $image, 0, 0, 255 );
	$text_color = imagecolorallocate( $image, 255, 255, 0 );
	imagestring( $image, 10, 20, 20, $text, $text_color );
	imagesetthickness ( $image, 5 );

	$path = getcwd() . '/wp-content/plugins/normalized-forms-with-captcha/' . $output;
	if (file_exists($path)) unlink($path);
	
	imagepng( $image, $path );
	imagecolordeallocate( $image, $text_color );
	imagecolordeallocate( $image, $background );
	return array($path_to_file, $text);
}

/**************************** Redirects Register and Login link to Custom Register Page *******************************/

function my_redirect() { wp_redirect( home_url( '/register' ) ); exit(); }
add_action( 'wp_logout', 'my_redirect' );

function gwb_catch_register() { wp_redirect( home_url( '/register' ) ); exit(); }
add_action( 'login_form_register', 'gwb_catch_register' );

function gwb_filter_option() { add_filter( 'pre_option_users_can_register', '__return_zero' ); }
add_action( 'login_form_lostpassword', 'gwb_filter_option' );
add_action( 'login_form_retrievepassword', 'gwb_filter_option' );

function gwb_redirect_to_custom_login() {
    if ( $_SERVER['REQUEST_METHOD'] == 'GET' ) {
        $redirect_to = isset( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : null;
        if ( is_user_logged_in() ) { $this->redirect_logged_in_user( $redirect_to ); exit; }
        $login_url = home_url( 'register' );
        if ( ! empty( $redirect_to ) ) $login_url = add_query_arg( 'redirect_to', $redirect_to, $login_url );
        wp_redirect( $login_url ); exit;
    }
}
add_action( 'login_form_login', 'gwb_redirect_to_custom_login' );

/**************************** Sessions *******************************/

function gwb_start_session() { if(!session_id()) session_start(); }
add_action('init', 'gwb_start_session', 1);

function gwb_session_destroy() { session_destroy (); }
add_action('wp_logout', 'gwb_session_destroy');
add_action('wp_login', 'gwb_session_destroy');

/**************************** Errors *******************************/

function gwb_registration_errors() {
	if(isset($_SESSION['username_taken'])) echo $_SESSION['username_taken'];
	if(isset($_SESSION['invalid_username'])) echo $_SESSION['invalid_username'];
	if(isset($_SESSION['all_fields'])) echo $_SESSION['all_fields'];
	if(isset($_SESSION['invalid_email'])) echo $_SESSION['invalid_email'];
	if(isset($_SESSION['email_registered'])) echo $_SESSION['email_registered'];
	if(isset($_SESSION['captcha'])) echo $_SESSION['captcha'];
	if(isset($_SESSION['passwords_match'])) echo $_SESSION['passwords_match'];
}

function gwb_login_errors() {
	if(isset($_SESSION['login_all_fields'])) echo $_SESSION['login_all_fields'];
	if(isset($_SESSION['login_password'])) echo $_SESSION['login_password'];
}

/**************************** Login & Register *******************************/

function gwb_login_member() {
	if(isset($_POST['gwb_user_login']) && wp_verify_nonce($_POST['gwb_login_nonce'], 'gwb-login-nonce')) {
		$user = get_userdatabylogin($_POST['gwb_user_login']);
		$pass = sanitize_text_field($_POST['gwb_user_pass']);
 
		if(!$user || !$pass) { $_SESSION['login_all_fields'] = 'Please fill in all fields.'; wp_redirect( home_url( '/register' ) ); exit(); }
		if(!wp_check_password($_POST['gwb_user_pass'], $user->user_pass, $user->ID)) { $_SESSION['login_password'] = 'Incorrect password.'; wp_redirect( home_url( '/register' ) ); exit(); }

		wp_setcookie($_POST['gwb_user_login'], $_POST['gwb_user_pass'], true);
		wp_set_current_user($user->ID, $_POST['gwb_user_login']);	
		do_action('wp_login', $_POST['gwb_user_login']); wp_redirect(home_url( 'wp-admin' )); exit;
	}
}
add_action('init', 'gwb_login_member');

function gwb_login_form() { if(!is_user_logged_in()) $output = gwb_login_form_fields(); return $output; }
add_shortcode('gwb_login_form', 'gwb_login_form');

function gwb_login_form_fields() {
	ob_start();
	echo '<p>'.gwb_login_errors().'</p>';
	echo '<form action="" method="post">';
	echo '<p>Username (required) <br/><input type="text" name="gwb_user_login" required/></p>';
	echo '<p>Password (required) <br/><input type="password" name="gwb_user_pass" required/></p>';
	echo '<input type="hidden" name="gwb_login_nonce" value="'. wp_create_nonce('gwb-login-nonce') .'"/>';
	echo '<p style="text-align:right;"><a href="'. wp_lostpassword_url() .'" id="lost-password">Forgot Password</a>';
	echo '<input id="gwb_login_submit" type="submit" value="Login"/></p></form>';
	return ob_get_clean();
}

function gwb_add_new_member() {
  	if (isset( $_POST["gwb_user_login"] ) && wp_verify_nonce($_POST['gwb_register_nonce'], 'gwb-register-nonce')) {
		$user_login = $_SESSION['gwb_user_login'] = sanitize_text_field($_POST["gwb_user_login"]);	
		$user_email = $_SESSION['gwb_user_email'] = sanitize_email($_POST["gwb_user_email"]);
		$user_first = $_SESSION['gwb_user_first'] = sanitize_text_field($_POST["gwb_user_first"]);
		$user_last = $_SESSION['gwb_user_last'] = sanitize_text_field($_POST["gwb_user_last"]);
		$user_pass = sanitize_text_field($_POST["gwb_user_pass"]);
		$pass_confirm = sanitize_text_field($_POST["gwb_user_pass_confirm"]);
		$robot = sanitize_text_field( $_POST["gwb_robot"] );
		$cap_text = sanitize_text_field( $_POST["cap"] );
		require_once(ABSPATH . WPINC . '/registration.php');
		
		if($robot != $cap_text) { $_SESSION['captcha'] = 'Captcha do not match.'; wp_redirect( home_url( '/register' ) ); exit(); }
		if(username_exists($user_login)) { $_SESSION['username_taken'] = 'Username already taken.'; wp_redirect( home_url( '/register' ) ); exit(); }
		if(!validate_username($user_login)) { $_SESSION['invalid_username'] = 'Invalid username.'; wp_redirect( home_url( '/register' ) ); exit(); }
		if($user_login == '' || $user_email == '' || $user_first == '' || $user_last == '' || $pass_confirm == '' || $robot == '' || $cap_text == '') { $_SESSION['all_fields'] = 'Please fill in all fields.'; wp_redirect( home_url( '/register' ) ); exit(); }
		if(!is_email($user_email)) { $_SESSION['invalid_email'] = 'Invalid Email.';  wp_redirect( home_url( '/register' ) ); exit(); }
		if(email_exists($user_email)) { $_SESSION['email_exists'] = 'Email already registered.';  wp_redirect( home_url( '/register' ) ); exit(); }
		if($user_pass != $pass_confirm) { $_SESSION['passwords_match'] = 'Passwords do not match.'; wp_redirect( home_url( '/register' ) ); exit(); }

		$new_user_id = wp_insert_user( array( 'user_login' => $user_login, 'user_pass' => $user_pass, 'user_email' => $user_email, 'first_name' => $user_first, 'last_name' => $user_last, 'user_registered' => date('Y-m-d H:i:s'), 'role' => 'subscriber' ) );
		if($new_user_id) { 
			wp_new_user_notification($new_user_id);
			wp_setcookie($user_login, $user_pass, true);
			wp_set_current_user($new_user_id, $user_login);	
			do_action('wp_login', $user_login);
			wp_redirect(home_url()); exit;
		}
	}
}
add_action('init', 'gwb_add_new_member');

function gwb_registration_form() {
	if(!is_user_logged_in()) {
		$registration_enabled = get_option('users_can_register');
		if($registration_enabled) $output = gwb_registration_form_fields();
		else $output = __('User registration is not enabled');
		return $output;
	}
}
add_shortcode('gwb_register_form', 'gwb_registration_form');

function gwb_registration_form_fields() {
	ob_start();
	echo '<p>'.gwb_registration_errors().'</a>';
	echo '<form action="" method="POST">';
	echo '<p>Username (required) <br/>';
	echo '<input type="text" name="gwb_user_login" pattern="[a-zA-Z0-9 ]+" value="' . ( isset( $_SESSION["gwb_user_login"] ) ? esc_attr( $_SESSION["gwb_user_login"] ) : '' ) . '" required/></p>';
	echo '<p>Email (required) <br/>';
	echo '<input type="email" name="gwb_user_email" value="' . ( isset( $_SESSION["gwb_user_email"] ) ? esc_attr( $_SESSION["gwb_user_email"] ) : '' ) . '" required/></p>';
	echo '<p>First Name (required) <br/>';
	echo '<input type="text" name="gwb_user_first" pattern="[a-zA-Z ]+" value="' . ( isset( $_SESSION["gwb_user_first"] ) ? esc_attr( $_SESSION["gwb_user_first"] ) : '' ) . '" /></p>';
	echo '<p>Last Name (required) <br/>';
	echo '<input type="text" name="gwb_user_last" pattern="[a-zA-Z ]+" value="' . ( isset( $_SESSION["gwb_user_last"] ) ? esc_attr( $_SESSION["gwb_user_last"] ) : '' ) . '" /></p>';
	echo '<p>Password (required) <br/>';
	echo '<input type="password" name="gwb_user_pass" required/></p>';
	echo '<p>Password Again (required) <br/>';
	echo '<input type="password" name="gwb_user_pass_confirm" required/></p>';
	$args = array(); $args = gwb_create_image();
	echo '<p><input type="hidden" name="cap" value="'.$args[1].'"/>';
	echo 'Robot? (required) <img src="'.$args[0].'" style="border:1px solid #777;"/><br/>';
	echo '<input type="text" name="gwb_robot" pattern="[a-zA-Z0-9 ]+" size="40" required/></p>';
	echo '<input type="hidden" name="gwb_register_nonce" value="'. wp_create_nonce('gwb-register-nonce') .'"/>';
	echo '<p style="text-align:right;"><input type="submit" name="gwb_submitted" value="Register"/></p></form>';
	return ob_get_clean();
}

/**************************** Contact Form and Send Mail *******************************/

function gwb_load_form() {
	echo '<form action="' . esc_url( $_SERVER['REQUEST_URI'] ) . '" method="post">';
	echo '<p>Your Name (required) <br/>';
	echo '<input type="text" name="gwb_name" pattern="[a-zA-Z0-9 ]+" value="' . ( isset( $_POST["gwb_name"] ) ? esc_attr( $_POST["gwb_name"] ) : '' ) . '" size="40" /></p>';
	echo '<p>Your Email (required) <br/>';
	echo '<input type="email" name="gwb_email" value="' . ( isset( $_POST["gwb_email"] ) ? esc_attr( $_POST["gwb_email"] ) : '' ) . '" size="40"/></p>';
	echo '<p>Subject (required) <br/>';
	echo '<input type="text" name="gwb_subject" pattern="[a-zA-Z ]+" value="' . ( isset( $_POST["gwb_subject"] ) ? esc_attr( $_POST["gwb_subject"] ) : '' ) . '" size="40" /></p>';
	echo '<p>Your Message (required) <br/>';
	echo '<textarea rows="10" cols="35" name="gwb_message">' . ( isset( $_POST["gwb_message"] ) ? esc_attr( $_POST["gwb_message"] ) : '' ) . '</textarea></p>';
	$args = array(); $args = gwb_create_image();
	//foreach($args as $arg) echo $arg . "<br />";
	echo '<p><input type="hidden" name="cap" value="'.$args[1].'"/>';
	echo 'Robot? (required) <img src="'.$args[0].'" style="border:1px solid #777;"/><br/>';
	echo '<input type="text" name="gwb_robot" pattern="[a-zA-Z0-9 ]+" value="' . ( isset( $_POST["gwb_robot"] ) ? esc_attr( $_POST["gwb_robot"] ) : '' ) . '" size="40" /></p>';
	echo '<p style="text-align:right;"><input type="submit" name="gwb_submitted" value="Send"></p></form>';
}

function gwb_send_mail() {
	if ( isset( $_POST['gwb_submitted'] ) ) {
		$name = sanitize_text_field( $_POST["gwb_name"] );
		$email = sanitize_email( $_POST["gwb_email"] );
		$subject = sanitize_text_field( $_POST["gwb_subject"] );
		$message = esc_textarea( $_POST["gwb_message"] );
		$robot = sanitize_text_field( $_POST["gwb_robot"] );
		$cap_text = sanitize_text_field( $_POST["cap"] );
		if($robot != $cap_text) { echo '<p>Please try again later.</p>'; exit(); }
		
		// Fill in emails here
		$to_args = array(	get_option( 'admin_email' ), '', '', '', '' );
		
		$headers = "From: $name <$email>" . "\r\n";
		$errors = array(); $count = 0;
		foreach($to_args as $to) {
			if($to != '') {
				if( wp_mail( $to, $subject, $message, $headers ) ) $error[$count] = false;
				else $error[$count] = true;
				$count++;
			}
		}
		foreach($errors as $error) { if($error = true) { echo '<p>Please try again later.</p>'; exit(); } }
		echo '<div><p>Thanks for contacting us. We will get back with you shortly.</p></div>';
	}
}

function gwb_contact_fix() { ob_start(); gwb_send_mail(); gwb_load_form(); return ob_get_clean(); }
add_shortcode( 'gwb_contact_form', 'gwb_contact_fix' );

?>