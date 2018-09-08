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

define('SCHOOLBOT_URL', 'https://yourdomain.schoolbot.co.uk');

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

	$output = "";
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

add_shortcode( 'schoolbot-training-days', 'schoolbot_training_days' );
