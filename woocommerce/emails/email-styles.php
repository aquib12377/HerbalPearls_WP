<?php
/**
 * Transactional email styles — warm champagne colors.
 *
 * @package HerbalPearls
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bg              = '#FAF5E8';
$body            = '#FFFFFF';
$base            = '#F4ECD8';
$text            = '#2A2520';
$text_secondary  = '#4D3826';
$link            = '#A0793F';
$gold            = '#A0793F';
$gold_dark       = '#8C6B3A';
$footer_text     = '#8C7B5E';
?>
body {
	background-color: <?php echo esc_html( $bg ); ?>;
}
#wrapper {
	background-color: <?php echo esc_html( $bg ); ?>;
}
#body_content {
	background-color: <?php echo esc_html( $body ); ?>;
}
#body_content table td {
	font-family: 'Inter', system-ui, -apple-system, sans-serif;
	color: <?php echo esc_html( $text ); ?>;
}
#body_content h1,
#body_content h2,
#body_content h3 {
	font-family: 'Cormorant Garamond', Georgia, 'Times New Roman', serif;
	color: <?php echo esc_html( $text ); ?>;
}
#body_content a {
	color: <?php echo esc_html( $link ); ?>;
	text-decoration: underline;
}
#header_wrapper {
	background-color: <?php echo esc_html( $body ); ?>;
	border-bottom: 3px solid <?php echo esc_html( $gold ); ?>;
	padding: 36px 0;
}
#body_content table td td {
	padding: 12px 0;
}
#template_footer {
	border-top: 1px solid <?php echo esc_html( $base ); ?>;
	padding: 24px 0;
}
#template_footer td {
	color: <?php echo esc_html( $footer_text ); ?>;
	font-size: 12px;
}
.button {
	background: <?php echo esc_html( $gold ); ?> !important;
	color: #FFFFFF !important;
	border-radius: 6px !important;
	padding: 12px 28px !important;
	font-weight: 600 !important;
	text-decoration: none !important;
	display: inline-block !important;
}
.button:hover {
	background: <?php echo esc_html( $gold_dark ); ?> !important;
}
.order_details {
	background: <?php echo esc_html( $base ); ?>;
	border-radius: 8px;
	padding: 16px;
}
.order_details td {
	color: <?php echo esc_html( $text_secondary ); ?>;
}
