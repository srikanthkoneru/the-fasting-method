<?php
/**
 * @package BuddyBoss Child
 * The parent theme functions are located at /buddyboss-theme/inc/theme/functions.php
 * Add your own functions at the bottom of this file.
 */


/****************************** THEME SETUP ******************************/

/**
 * Sets up theme for translation
 *
 * @since BuddyBoss Child 1.0.0
 */
function buddyboss_theme_child_languages()
{
  /**
   * Makes child theme available for translation.
   * Translations can be added into the /languages/ directory.
   */

  // Translate text from the PARENT theme.
  load_theme_textdomain( 'buddyboss-theme', get_stylesheet_directory() . '/languages' );

  // Translate text from the CHILD theme only.
  // Change 'buddyboss-theme' instances in all child theme files to 'buddyboss-theme-child'.
  // load_theme_textdomain( 'buddyboss-theme-child', get_stylesheet_directory() . '/languages' );

}
add_action( 'after_setup_theme', 'buddyboss_theme_child_languages' );

/**
 * Enqueues scripts and styles for child theme front-end.
 *
 * @since Boss Child Theme  1.0.0
 */
function buddyboss_theme_child_scripts_styles()
{
  /**
   * Scripts and Styles loaded by the parent theme can be unloaded if needed
   * using wp_deregister_script or wp_deregister_style.
   *
   * See the WordPress Codex for more information about those functions:
   * http://codex.wordpress.org/Function_Reference/wp_deregister_script
   * http://codex.wordpress.org/Function_Reference/wp_deregister_style
   **/

  // Styles
  wp_enqueue_style( 'buddyboss-child-css', get_stylesheet_directory_uri().'/assets/css/custom.css' );

  // Javascript
  wp_enqueue_script( 'buddyboss-child-js', get_stylesheet_directory_uri().'/assets/js/custom.js' );
}
add_action( 'wp_enqueue_scripts', 'buddyboss_theme_child_scripts_styles', 9999 );


/****************************** CUSTOM FUNCTIONS ******************************/

add_action( 'login_enqueue_scripts', 'buddyboss_child_login_enqueue_scripts', 9999 );

/**
 * Buddyboss child theme overwrite login enqueue scripts.
 *
 * @return void
 */
function buddyboss_child_login_enqueue_scripts() {
  
  wp_enqueue_style( 'buddyboss-child-css', get_stylesheet_directory_uri().'/assets/css/custom.css' );
  wp_enqueue_script( 'buddyboss-child-js', get_stylesheet_directory_uri().'/assets/js/custom.js' );

}

/**
 * Setting up the cookie if it doesn't exist yet and grabbing the browser time zone string.
 */
function the_fasting_method_cookie() {

    ?>
    <script type="text/javascript">
        if ( navigator.cookieEnabled ) {
            document.cookie = "tribe_browser_time_zone=" + Intl.DateTimeFormat().resolvedOptions().timeZone + "; path=/";
        }
    </script>
    <?php

}
add_action( 'wp_head', 'the_fasting_method_cookie' );

/**
 * Calculating the event start time and time zone based on the browser time zone of the visitor.
 */
function the_fasting_method_local_time( $event_id ) {

    // Setting default values in case the cookie doesn't exist.
    $user_time_output = sprintf( '<small>%1$s %2$s<a href="">%3$s</a> %4$s </small>', esc_html__( 'Your time zone couldn\'t be detected.', 'the-fasting-method.' ), esc_html__( 'Try', 'the-fasting-method' ), esc_html__( 'reloading', 'the-fasting-method' ), esc_html__( 'the page.', 'the-fasting-method' ) );
    $browser_time_zone_string = esc_html__( 'not detected', 'the-fasting-method' );
    
    if ( isset( $_COOKIE['tribe_browser_time_zone'] ) ) {
        // Grab the time zone string from the cookie.
        $browser_time_zone_string = $_COOKIE['tribe_browser_time_zone'];
    
        // Grab the event time zone string.
        $event_time_zone_string = Tribe__Events__Timezones::get_event_timezone_string( $event_id );
    
        // Grab the event start date in UTC time from the database.
        $event_start_utc = tribe_get_event_meta( $event_id, '_EventStartDateUTC', true );
    
        // Set up the DateTime object.
        $event_start_date_in_utc_timezone = new DateTime( $event_start_utc, new DateTimeZone( 'UTC' ) );
    
        // Convert the UTC DateTime object into the browser time zone.
        $event_start_date_in_browser_timezone = $event_start_date_in_utc_timezone->setTimezone( new DateTimeZone( $browser_time_zone_string ) )->format( get_option( 'time_format' ) );
    
        // Grab the time zone abbreviation based on the browser time zone string.
        $browser_time_zone_abbreviation = Tribe__Timezones::abbr( 'now', $browser_time_zone_string );
    
        // Compile the output string with time zone abbreviation.
        $user_time_output = $event_start_date_in_browser_timezone . " " . $browser_time_zone_abbreviation;
    
        // Compile the string of the time zone for the tooltip.
        $browser_time_zone_string .= ' ' . esc_html__( 'detected', 'the-fasting-method' );
    }

    /**
     * Adding the event start time in the visitor's time zone.
     */
    if ( ! tribe_event_is_all_day( $event_id ) ) {
        echo "<div class='tribe-events-schedule--browser-time-zone'><p>";
        printf( '%1$s <span style=\'text-decoration-style: dotted; text-decoration-line: underline; cursor: help;\' title=\'%2$s (%3$s) %4$s\'>%5$s</span>: %6$s', esc_html__( 'Start time where', 'the-fasting-method' ), esc_html__( 'This is based on your browser time zone', 'the-fasting-method' ), $browser_time_zone_string, esc_html__( 'and it might not be fully accurate.', 'the-fasting-method' ), esc_html__( 'you are', 'the-fasting-method' ), $user_time_output );
        echo "</p></div>";
    }

}


?>