<?php
	
$event_id = $event->ID;
/**
 * Block: Event Date Time
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events/blocks/event-datetime.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @version 5.0.1
 *
 */

$event_id = get_the_ID();
 
/**
 * Setting up the cookie if it doesn't exist yet and grabbing the browser time zone string.
 */
if ( ! isset( $_COOKIE['tribe_browser_time_zone'] ) ) { ?>
    <script type="text/javascript">
        if ( navigator.cookieEnabled ) {
            document.cookie = "tribe_browser_time_zone=" + Intl.DateTimeFormat().resolvedOptions().timeZone + "; path=/";
        }
    </script>
<?php }
 
/**
 * Calculating the event start time and time zone based on the browser time zone of the visitor.
 */
 
// Setting default values in case the cookie doesn't exist.
$user_time_output = "<small>Your time zone couldn't be detected. Try <a href=''>reloading</a> the page.</small>";
$browser_time_zone_string = "not detected";
 
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
    $browser_time_zone_string .= ' detected';
}
$event = get_post( $event_id );

/**
 * If a yearless date format should be preferred.
 *
 * By default, this will be true if the event starts and ends in the current year.
 *
 * @since 0.2.5-alpha
 *
 * @param bool    $use_yearless_format
 * @param WP_Post $event
 */
$use_yearless_format = apply_filters( 'tribe_events_event_block_datetime_use_yearless_format',
	(
		tribe_get_start_date( $event_id, false, 'Y' ) === date_i18n( 'Y' )
		&& tribe_get_end_date( $event_id, false, 'Y' ) === date_i18n( 'Y' )
	),
	$event
);

$time_format      = tribe_get_time_format();
$date_format      = tribe_get_date_format( ! $use_yearless_format );
$timezone         = get_post_meta( $event_id, '_EventTimezone', true );
$show_time_zone   = $this->attr( 'showTimeZone' );
$local_start_time = tribe_get_start_date( $event_id, true, Tribe__Date_Utils::DBDATETIMEFORMAT );
$time_zone_label  = $this->attr( 'timeZoneLabel' );

if ( is_null( $show_time_zone ) ) {
	$show_time_zone = tribe_get_option( 'tribe_events_timezones_show_zone', false );
}

if ( is_null( $time_zone_label ) ) {
	$time_zone_label = Tribe__Events__Timezones::is_mode( 'site' ) ? Tribe__Events__Timezones::wp_timezone_abbr( $local_start_time ) : Tribe__Events__Timezones::get_event_timezone_abbr( $event_id );
}

$formatted_start_date = tribe_get_start_date( $event_id, false, $date_format );
$formatted_start_time = tribe_get_start_time( $event_id, $time_format );
$formatted_end_date   = tribe_get_end_date( $event_id, false, $date_format );
$formatted_end_time   = tribe_get_end_time( $event_id, $time_format );
$separator_date       = get_post_meta( $event_id, '_EventDateTimeSeparator', true );
$separator_time       = get_post_meta( $event_id, '_EventTimeRangeSeparator', true );

if ( empty( $separator_time ) ) {
	$separator_time = tec_events_get_time_range_separator();
}
if ( empty( $separator_date ) ) {
	$separator_date = tec_events_get_date_time_separator();
}

$is_all_day        = tribe_event_is_all_day( $event_id );
$is_same_day       = $formatted_start_date == $formatted_end_date;
$is_same_start_end = $formatted_start_date == $formatted_end_date && $formatted_start_time == $formatted_end_time;

$event_id = $this->get( 'post_id' );

$default_classes = [ 'tribe-events-schedule', 'tribe-clearfix' ];

// Add the custom classes from the block attributes.
$classes = isset( $attributes['className'] ) ? array_merge( $default_classes, [ $attributes['className'] ] ) : $default_classes;
?>
<div <?php tribe_classes( $classes ); ?>>
	<h2 class="tribe-events-schedule__datetime">
		<span class="tribe-events-schedule__date tribe-events-schedule__date--start">
			<?php echo esc_html( $formatted_start_date ); ?>
		</span>

		<?php if ( ! $is_all_day ) : ?>
			<span class="tribe-events-schedule__separator tribe-events-schedule__separator--date">
				<?php echo esc_html( $separator_date ); ?>
			</span>
			<span class="tribe-events-schedule__time tribe-events-schedule__time--start">
				<?php echo esc_html( $formatted_start_time ); ?>
			</span>
		<?php elseif ( $is_same_day ) : ?>
			<span class="tribe-events-schedule__all-day"><?php echo esc_html__( 'All day', 'the-events-calendar' ); ?></span>
		<?php endif; ?>

		<?php if ( ! $is_same_start_end ) : ?>
			<?php if ( ! $is_all_day || ! $is_same_day ) : ?>
				<span class="tribe-events-schedule__separator tribe-events-schedule__separator--time">
					<?php echo esc_html( $separator_time ); ?>
				</span>
			<?php endif; ?>

			<?php if ( ! $is_same_day ) : ?>
				<span class="tribe-events-schedule__date tribe-events-schedule__date--end">
					<?php echo esc_html( $formatted_end_date ); ?>
				</span>

				<?php if ( ! $is_all_day ) : ?>
					<span class="tribe-events-schedule__separator tribe-events-schedule__separator--date">
						<?php echo esc_html( $separator_date ); ?>
					</span>
					<span class="tribe-events-schedule__time tribe-events-schedule__time--end">
						<?php echo esc_html( $formatted_end_time ); ?>
					</span>
				<?php endif; ?>

			<?php elseif ( ! $is_all_day ) : ?>
				<span class="tribe-events-schedule__time tribe-events-schedule__time--end">
					<?php echo esc_html( $formatted_end_time ); ?>
				</span>
			<?php endif; ?>

			<?php if ( $show_time_zone ) : ?>
				<span class="tribe-events-schedule__timezone"><?php echo esc_html( $time_zone_label ); ?></span>
			<?php endif; ?>
		<?php endif; ?>
	</h2>
    <?php
    /**
     * Adding the event start time in the visitor's time zone.
     */
    if ( ! tribe_event_is_all_day( $event_id ) ) {
        echo "<div class='tribe-events-schedule--browser-time-zone'><p>";
        echo "Start time where <span style='text-decoration-style: dotted; text-decoration-line: underline; cursor: help;' title='This is based on your browser time zone (" . $browser_time_zone_string . ") and it might not be fully accurate.'>you are</span>: " . $user_time_output;
        echo "</p></div>";
    }
    ?>
</div>
