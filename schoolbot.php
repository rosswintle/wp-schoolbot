<?php
/**
 * Plugin Name:     Schoolbot for WP
 * Plugin URI:      https://schoolbot.co.uk/
 * Description:     Schoolbot integration for WordPress
 * Author:          Ross Wintle
 * Author URI:      https://rosswintle.uk
 * Text Domain:     schoolbot
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Schoolbot
 */

define('SCHOOLBOT_URL', 'https://evenswindon.schoolbot.co.uk');
//define('SCHOOLBOT_URL', 'http://evenswindon.schoolbot.test');

function schoolbot_day_name( $dayno ) {
	$schoolbot_days_lookup = [
		1 => 'Monday',
		2 => 'Tuesday',
		3 => 'Wednesday',
		4 => 'Thursday',
		5 => 'Friday',
		6 => 'Saturday',
		7 => 'Sunday',
	];

	return $schoolbot_days_lookup[$dayno];
}

add_shortcode( 'schoolbot-menus', 'schoolbot_menus' );

function schoolbot_menus( $atts ) {
	$atts = shortcode_atts( [], $atts, 'schoolbot-menus' );

	$menus = wp_remote_get( SCHOOLBOT_URL . '/api/v1/menus' );

	if (is_wp_error( $menus )) {
		return "Error fetching menus";
	}

	$menus = json_decode($menus['body'], true);

	$output = '<h3>Menus</h3>';
	foreach ( $menus as $weekno => $weekdata ) {
		$output .= "<h3>Week " . esc_html($weekno) . "</h3>";
		foreach ( $weekdata as $dayno => $daydata ) {
			$output .= "<h4>" . schoolbot_day_name($dayno) . "</h4>";
			$output .= "<ul>";
			foreach ( $daydata as $order => $meal ) {
				$output .= "<li>" . esc_html($meal['meal']) . "</li>";
			}
			$output .= "</ul>";
		}
	}

	return $output;

}

add_shortcode( 'schoolbot-menus-table', 'schoolbot_menus_table' );

function schoolbot_menus_table( $atts ) {
	$atts = shortcode_atts( [], $atts, 'schoolbot-menus' );

	$menus = wp_remote_get( SCHOOLBOT_URL . '/api/v1/menus' );

	if (is_wp_error( $menus )) {
		return "Error fetching menus";
	}

	$menus = json_decode($menus['body'], true);

	$output = '<h3>Menus</h3>';
	$output .= '<table></tbody>';
	foreach ( $menus as $weekno => $weekdata ) {
		$output .= '<tr><td class="menu-table-week-heading" colspan="5"><strong>Week ' . esc_html($weekno) . '</strong></td></tr>';
		$output .= '<tr class="menu-table-day-headings"><td>Monday</td><td>Tuesday</td><td>Wednesday</td><td>Thursday</td><td>Friday</td></tr>';
		$output .= '<tr class="menu-table-week">';
		foreach ( $weekdata as $dayno => $daydata ) {
			$output .= '<td class="menu-table-day"><ul>';
			foreach ( $daydata as $order => $meal ) {
				$output .= "<li>" . esc_html($meal['meal']) . "</li>";
			}
			$output .= "</ul></td>";
		}
		$output .= '</tr>';
	}
	$output .= '</tbody></table>';

	return $output;

}

add_shortcode( 'schoolbot-todays-meals', 'schoolbot_todays_meals' );

function schoolbot_todays_meals( $atts ) {
	$atts = shortcode_atts( [], $atts, 'schoolbot-todays-meals' );

	$todays_meals = wp_remote_get( SCHOOLBOT_URL . '/api/v1/meals/today' );

	if (is_wp_error( $todays_meals )) {
		return "Error fetching todays meals";
	}

	$todays_meals = json_decode($todays_meals['body'], true);

	$output = "<h3>Todays Meals</h3><ul>";
	if (empty($todays_meals)) {
		$output .= 'There are no meals listed for today';
	}
	foreach ( $todays_meals as $meal ) {
		$output .= '<li>' . $meal['meal'] . '</li>';
	}
	$output .= "</ul>";

	return $output;

}


add_shortcode( 'schoolbot-holidays', 'schoolbot_holidays' );

function schoolbot_holidays( $atts ) {
	$atts = shortcode_atts( [], $atts, 'schoolbot-holidays' );

	$holidays = wp_remote_get( SCHOOLBOT_URL . '/api/v1/holidays' );

	if (is_wp_error( $holidays )) {
		return "Error fetching holidays";
	}

	$holidays = json_decode($holidays['body'], true);

	$output = "<h3>Holidays</h3><ul>";
	foreach ( $holidays as $holiday_name => $holiday_dates ) {
		$output .= "<li>" . esc_html($holiday_name) . ": " . date('l, jS F Y', strtotime($holiday_dates['start']['date'])) . " to " . date('l, jS F Y', strtotime($holiday_dates['end']['date'])) . "</li>";
	}
	$output .= "</ul>";

	return $output;

}

add_shortcode( 'schoolbot-terms', 'schoolbot_terms' );

function schoolbot_terms( $atts ) {
	$atts = shortcode_atts( [], $atts, 'schoolbot-terms' );

	$terms = wp_remote_get( SCHOOLBOT_URL . '/api/v1/terms' );

	if (is_wp_error( $terms )) {
		return "Error fetching term dates";
	}

	$terms = json_decode($terms['body'], true);

	$output = "<h3>Term dates</h3><ul>";
	foreach ( $terms as $term ) {
		$output .= "<li>" . esc_html($term['term_name']) . ": " . date('l, jS F Y', strtotime($term['term_start'])) . " to " . date('l, jS F Y', strtotime($term['term_end'])) . "</li>";
	}
	$output .= "</ul>";

	return $output;

}

add_shortcode( 'schoolbot-training-days', 'schoolbot_training_days' );

function schoolbot_training_days( $atts ) {
	$atts = shortcode_atts( [], $atts, 'schoolbot-training-days' );

	$training_days = wp_remote_get( SCHOOLBOT_URL . '/api/v1/trainingdays' );

	if (is_wp_error( $training_days )) {
		return "Error fetching training days";
	}

	$training_days = json_decode($training_days['body'], true);

	$output = "<h3>Training Days</h3><ul>";
	foreach ( $training_days as $training_day ) {
		$output .= "<li>" . date('l, jS F Y', strtotime($training_day['date'])) . "</li>";
	}
	$output .= "</ul>";

	return $output;

}


add_shortcode( 'schoolbot-faq', 'schoolbot_faq' );

function schoolbot_faq( $atts ) {
	$atts = shortcode_atts( [], $atts, 'schoolbot-faq' );

	$custom_messages = wp_remote_get( SCHOOLBOT_URL . '/api/v1/custommessages' );

	if (is_wp_error( $custom_messages )) {
		return "Error fetching frequently asked questions";
	}

	$custom_messages = json_decode($custom_messages['body'], true);

	$output = "<h3>Frequently asked questions</h3><ul>";
	foreach ( $custom_messages as $message ) {
		$output .= '<li>';
		$output .= '<h4 class="schoolbot-faq-question">' . $message['faq'] . '</h4>';
		$output .= '<span class="schoolbot-faq-answer">' . $message['response'] . '</span>';
		$output .= '</li>';
	}
	$output .= "</ul>";

	return $output;

}


