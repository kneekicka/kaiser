<?php
/**
 * Gallery widget class
 *
 * @package classes/widgets
 */

namespace tm_photo_gallery\classes\widgets;

use tm_photo_gallery\classes\View;
use tm_photo_gallery\models\Gallery;
use tm_photo_gallery\classes\Shortcode;

/**
 * Class gallery widget
 */
class Gallery_widget extends \WP_Widget {

	/**
	 * Gallery_widget constructor.
	 */
	function __construct() {
		$this->widget_cssclass		 = 'tm-gallery-container';
		$this->widget_description	 = esc_attr__( 'Shows gallery list.', 'tm_gallery' );
		$this->widget_id			 = 'tm-gallery';
		$this->widget_name			 = esc_attr__( 'TM Photo gallery', 'tm_gallery' );

		$widget_ops = array(
			'classname'		 => $this->widget_cssclass,
			'description'	 => $this->widget_description,
		);

		parent::__construct( $this->widget_id, $this->widget_name, $widget_ops );
		// add_action('save_post', array($this, 'flush_widget_cache'));
		// add_action('deleted_post', array($this, 'flush_widget_cache'));
		// add_action('switch_theme', array($this, 'flush_widget_cache'));
	}

	/**
	 * Widget form creation
	 *
	 * @param array $instance
	 */
	function form( $instance ) {
		// Check values
		if ( $instance ) {
			$gallery_id = esc_attr( $instance['gallery_id'] );
		} else {
			$gallery_id = '';
		}
		$posts = get_posts( Gallery::get_instance()->get_content_params( $_REQUEST ) );
		View::get_instance()->render_html( 'widgets/gallery_list', array( 'galleries' => $posts, 'widget_object' => $this, 'gallery_id' => $gallery_id ), true );
	}

	/**
	 * Update widget
	 *
	 * @param array $new_instance
	 * @param array $old_instance
	 *
	 * @return array
	 */
	function update( $new_instance, $old_instance ) {
		$instance				 = $old_instance;
		// Fields
		$instance['gallery_id']	 = strip_tags( $new_instance['gallery_id'] );
		return $instance;
	}

	/**
	 * Display widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	function widget( $args, $instance ) {
		extract( $args );
		// these are the widget options
		$title = apply_filters( 'widget_title', $instance['gallery_id'] );
		echo Shortcode::get_instance()->show_shortcode( array( 'gallery_id' => $instance['gallery_id'] ) );
	}
}
