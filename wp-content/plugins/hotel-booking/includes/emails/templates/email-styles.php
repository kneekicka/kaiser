<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bg			 = MPHB()->getEmailSettings()->getBGColor();
$body		 = MPHB()->getEmailSettings()->getBodyBGColor();
$base		 = MPHB()->getEmailSettings()->getBaseColor();
$base_text	 = MPHBUtils::lightOrDark( $base, '#202020', '#ffffff' );
$text		 = MPHB()->getEmailSettings()->getBodyTextColor();

$bg_darker_10	 = MPHBUtils::hexDarker( $bg, 10 );
$body_darker_10	 = MPHBUtils::hexDarker( $body, 10 );
$base_lighter_20 = MPHBUtils::hexLighter( $base, 20 );
$base_lighter_40 = MPHBUtils::hexLighter( $base, 40 );
$text_lighter_20 = MPHBUtils::hexLighter( $text, 20 );

?>
#wrapper {
	background-color: <?php echo esc_attr( $bg ); ?>;
	margin: 0;
	padding: 70px 0 70px 0;
	-webkit-text-size-adjust: none !important;
	width: 100%;
}

#template_container {
	box-shadow: 0 1px 4px rgba(0,0,0,0.1) !important;
	background-color: <?php echo esc_attr( $body ); ?>;
	border: 1px solid <?php echo esc_attr( $bg_darker_10 ); ?>;
	border-radius: 3px !important;
}

#template_header {
	background-color: <?php echo esc_attr( $base ); ?>;
	border-radius: 3px 3px 0 0 !important;
	color: <?php echo esc_attr( $base_text ); ?>;
	border-bottom: 0;
	font-weight: bold;
	line-height: 100%;
	vertical-align: middle;
	font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
}

#template_header h1 {
	color: <?php echo esc_attr( $base_text ); ?>;
}

#template_footer td {
	padding: 0;
	-webkit-border-radius: 6px;
}

#template_footer #credit {
	border:0;
	color: <?php echo esc_attr( $base_lighter_40 ); ?>;
	font-family: Arial;
	font-size:12px;
	line-height:125%;
	text-align:center;
	padding: 0 48px 48px 48px;
}

#body_content {
	background-color: <?php echo esc_attr( $body ); ?>;
}

#body_content table td {
	padding: 48px;
}

#body_content table td td {
	padding: 12px;
}

#body_content table td th {
	padding: 12px;
}

#body_content p {
	margin: 0 0 16px;
}

#body_content_inner {
	color: <?php echo esc_attr( $text_lighter_20 ); ?>;
	font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
	font-size: 14px;
	line-height: 150%;
}

.td {
	color: <?php echo esc_attr( $text_lighter_20 ); ?>;
	border: 1px solid <?php echo esc_attr( $body_darker_10 ); ?>;
}

.text {
	color: <?php echo esc_attr( $text ); ?>;
	font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
}

.link {
	color: <?php echo esc_attr( $base ); ?>;
}

#header_wrapper {
	padding: 36px 48px;
	display: block;
}

h1 {
	color: <?php echo esc_attr( $base ); ?>;
	font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
	font-size: 30px;
	font-weight: 300;
	line-height: 150%;
	margin: 0;
	text-shadow: 0 1px 0 <?php echo esc_attr( $base_lighter_20 ); ?>;
	-webkit-font-smoothing: antialiased;
}

h2 {
	color: <?php echo esc_attr( $base ); ?>;
	display: block;
	font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
	font-size: 18px;
	font-weight: bold;
	line-height: 130%;
	margin: 16px 0 8px;
}

h3 {
	color: <?php echo esc_attr( $base ); ?>;
	display: block;
	font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
	font-size: 16px;
	font-weight: bold;
	line-height: 130%;
	margin: 16px 0 8px;
}

a {
	color: <?php echo esc_attr( $base ); ?>;
	font-weight: normal;
	text-decoration: underline;
}

img {
	border: none;
	display: inline;
	font-size: 14px;
	font-weight: bold;
	height: auto;
	line-height: 100%;
	outline: none;
	text-decoration: none;
	text-transform: capitalize;
}
<?php
