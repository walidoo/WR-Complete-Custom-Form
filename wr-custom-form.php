<?php
/*
  Plugin Name: WR Custom Form
  Plugin URI: www.walidrezk.com
  Description: Simple Custom Form .
  Version: 1.0
  Author: Walid Rezk
  Author URI: www.walidrezk.com
 */

/**
 * [registration_form <Custom Registration Form>]
 * @return   [<Custom Registration Form>]
 */
function registration_form( $username, $password, $email, $website, $first_name, $last_name, $nickname, $bio ) {
    echo '
    <style>
    div {
        margin-bottom:2px;
    }
     
    input{
        margin-bottom:4px;
    }
    </style>
    ';
 
    echo '
    <form action="' . $_SERVER['REQUEST_URI'] . '" method="post">
    <div>
    <div class="form-horizontal"><label for="username">Username <strong>*</strong></label></div>
    <div class="form-group"><input type="text" name="username" value="' . ( isset( $_POST['username'] ) ? $username : null ) . '">
    </div>
    </div>
     
    <div>
    <div class="form-horizontal"><label for="password">Password <strong>*</strong></label></div>
    <div class="form-group"><input type="password" name="password" value="' . ( isset( $_POST['password'] ) ? $password : null ) . '">
    </div>
     
    <div>
    <div class="form-horizontal"><label for="email">Email <strong>*</strong></label></div>
    <div class="form-group"><input type="text" name="email" value="' . ( isset( $_POST['email']) ? $email : null ) . '">
    </div>
    </div>
     
    <div>
    <div class="form-horizontal"><label for="website">Website</label></div>
    <div class="form-group"><input type="text" name="website" value="' . ( isset( $_POST['website']) ? $website : null ) . '">
    </div>
    </div>
     
    <div>
    <div class="form-horizontal"><label for="firstname">First Name</label></div>
    <div class="form-group"><input type="text" name="fname" value="' . ( isset( $_POST['fname']) ? $first_name : null ) . '">
    </div>
    </div>
     
    <div>
    <div class="form-horizontal"><label for="website">Last Name</label></div>
    <div class="form-group"><input type="text" name="lname" value="' . ( isset( $_POST['lname']) ? $last_name : null ) . '">
    </div>
    </div>
     
    <div>
    <div class="form-horizontal"><label for="nickname">Nickname</label></div>
    <div class="form-group"><input type="text" name="nickname" value="' . ( isset( $_POST['nickname']) ? $nickname : null ) . '">
    </div>
    </div>
     
    <div>
    <div class="form-horizontal"><label for="bio">About / Bio</label></div>
    <div class="form-group"><textarea name="bio">' . ( isset( $_POST['bio']) ? $bio : null ) . '</textarea>
    </div>
    </div>
    <input type="submit" name="submit" value="Register"/>
    </form>
    ';
}

function registration_validation( $username, $password, $email, $website, $first_name, $last_name, $nickname, $bio )  {
	global $reg_errors;
	$reg_errors = new WP_Error;
	if ( empty( $username ) || empty( $password ) || empty( $email ) ) {
    $reg_errors->add('field', 'Required form field is missing');
	}
	if ( 4 > strlen( $username ) ) {
    $reg_errors->add( 'username_length', 'Username too short. At least 4 characters is required' );
	}
	if ( username_exists( $username ) )
    $reg_errors->add('user_name', 'Sorry, that username already exists!');
	if ( ! validate_username( $username ) ) {
	    $reg_errors->add( 'username_invalid', 'Sorry, the username you entered is not valid' );
	}
	if ( 5 > strlen( $password ) ) {
        $reg_errors->add( 'password', 'Password length must be greater than 5' );
    }
    if ( !is_email( $email ) ) {
    $reg_errors->add( 'email_invalid', 'Email is not valid' );
	}
	if ( email_exists( $email ) ) {
    $reg_errors->add( 'email', 'Email Already in use' );
	}
	if ( ! empty( $website ) ) {
	    if ( ! filter_var( $website, FILTER_VALIDATE_URL ) ) {
	        $reg_errors->add( 'website', 'Website is not a valid URL' );
	    }
	}
	if ( is_wp_error( $reg_errors ) ) {
 
	    foreach ( $reg_errors->get_error_messages() as $error ) {
	     
	        echo '<div>';
	        echo '<strong> ERROR </strong>:';
	        echo $error . '<br/>';
	        echo '</div>';
	         
	    }
 
	}

}	

function complete_registration() {
    global $reg_errors, $username, $password, $email, $website, $first_name, $last_name, $nickname, $bio;
    if ( 1 > count( $reg_errors->get_error_messages() ) ) {
        $userdata = array(
        'user_login'    =>   $username,
        'user_email'    =>   $email,
        'user_pass'     =>   $password,
        'user_url'      =>   $website,
        'first_name'    =>   $first_name,
        'last_name'     =>   $last_name,
        'nickname'      =>   $nickname,
        'description'   =>   $bio,
        );
        $user = wp_insert_user( $userdata );
        echo 'Registration complete. Goto <a href="' . get_site_url() . '/login">login page</a>.';   
    }
}

function custom_registration_function() {
    if ( isset($_POST['submit'] ) ) {
        registration_validation(
        $_POST['username'],
        $_POST['password'],
        $_POST['email'],
        $_POST['website'],
        $_POST['fname'],
        $_POST['lname'],
        $_POST['nickname'],
        $_POST['bio']
        );
         
        // sanitize user form input
        global $username, $password, $email, $website, $first_name, $last_name, $nickname, $bio;
        $username   =   sanitize_user( $_POST['username'] );
        $password   =   esc_attr( $_POST['password'] );
        $email      =   sanitize_email( $_POST['email'] );
        $website    =   esc_url( $_POST['website'] );
        $first_name =   sanitize_text_field( $_POST['fname'] );
        $last_name  =   sanitize_text_field( $_POST['lname'] );
        $nickname   =   sanitize_text_field( $_POST['nickname'] );
        $bio        =   esc_textarea( $_POST['bio'] );
 
        // call @function complete_registration to create the user
        // only when no WP_error is found
        complete_registration(
        $username,
        $password,
        $email,
        $website,
        $first_name,
        $last_name,
        $nickname,
        $bio
        );
    }
 
    registration_form(
        $username,
        $password,
        $email,
        $website,
        $first_name,
        $last_name,
        $nickname,
        $bio
        );
}

// Register a new shortcode: [cr_custom_registration]
add_shortcode( 'cr_custom_registration', 'custom_registration_shortcode' );
 
// The callback function that will replace [book]
function custom_registration_shortcode() {
    ob_start();
    custom_registration_function();
    return ob_get_clean();
}


/**
 * [login_form description]
 * @return   [<Custom Login Form>]
 */
function login_form( $username, $password ) {
    echo '
    <style>
    div {
        margin-bottom:2px;
    }
     
    input{
        margin-bottom:4px;
    }
    </style>
    ';
 
    echo '
    <form action="' . $_SERVER['REQUEST_URI'] . '" method="post">
    <div>
    <div class="form-horizontal"><label for="username">Username <strong>*</strong></label></div>
    <div class="form-group"><input type="text" name="username" value="' . ( isset( $_POST['username'] ) ? $username : null ) . '">
    </div>
    </div>
     
    <div>
    <div class="form-horizontal"><label for="password">Password <strong>*</strong></label></div>
    <div class="form-group"><input type="password" name="password" value="' . ( isset( $_POST['password'] ) ? $password : null ) . '">
    </div>
    </div>
     
    <input type="submit" name="submit" value="Log In"/>
    </form>
    ';
}

function login_validation( $username, $password )  {
    global $reg_errors;
    $reg_errors = new WP_Error;
    if ( empty( $username ) || empty( $password ) ) {
    $reg_errors->add('field', 'Required form field is missing');
    }
    if ( 4 > strlen( $username ) ) {
    $reg_errors->add( 'username_length', 'Username too short. At least 4 characters is required' );
    }
    if ( !username_exists( $username ) )
    $reg_errors->add('user_name', 'Sorry, that username does not exist!');
    if ( ! validate_username( $username ) ) {
        $reg_errors->add( 'username_invalid', 'Sorry, the username you entered is not valid' );
    }
    if ( 5 > strlen( $password ) ) {
        $reg_errors->add( 'password', 'Password length must be greater than 5' );
    }

    if ( is_wp_error( $reg_errors ) ) {
 
        foreach ( $reg_errors->get_error_messages() as $error ) {
         
            echo '<div>';
            echo '<strong>ERROR</strong>:';
            echo $error . '<br/>';
            echo '</div>';
             
        }
 
    }
}   

function complete_login() {
    global $reg_errors, $username, $password;
    if ( 1 > count( $reg_errors->get_error_messages() ) ) {
        echo 'Registration complete. Goto <a href="' . get_site_url() . '">Site page</a>.';   
    }
}

function custom_login_function() {
    if ( isset($_POST['submit'] ) ) {
        login_validation(
        $_POST['username'],
        $_POST['password']
        );
         
        // sanitize user form input
        global $username, $password;
        $username   =   sanitize_user( $_POST['username'] );
        $password   =   esc_attr( $_POST['password'] );
        // call @function complete_login to login
        // only when no WP_error is found
        complete_login(
        $username,
        $password
        );
    }
 
    login_form(
        $username,
        $password
        );
}

// Register a new shortcode: [cr_custom_login]
add_shortcode( 'cr_custom_login', 'custom_login_shortcode' );
 
// The callback function that will replace [book]
function custom_login_shortcode() {
    ob_start();
    custom_login_function();
    return ob_get_clean();
}

/*register_activation_hook(__FILE__, 'wr_custom_form_activation');
function wr_custom_form_activation()
{
    // Don't forget to exit() because wp_redirect doesn't exit automatically
    // exit( wp_redirect( admin_url( 'options-general.php?page=myplugin_settings' ) ) );
    return 'www';
}*/