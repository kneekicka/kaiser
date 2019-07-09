<?php
/**
 * Cherry Breadcrumbs class
 *
 * @package classes/shortcodes
 */
use tm_photo_gallery\classes\lib\FB;
use tm_photo_gallery\classes\Core;

/**
 * Description of Cherry_Breadcrumbs
 */
class TM_Gallery_Breadcrumbs extends Cherry_Breadcrumbs {

	/**
	 * Build breadcrumbs trail items array
	 */
	public function build_trail() {
		$this->is_extend = true;

		// do this for all other pages
		$this->add_network_home_link();
		$this->add_site_home_link();

		$this->add_folder_parents();

		// do this for all other pages
		$this->add_single_folder();

		/* Add paged items if they exist. */
		$this->add_paged_items();

		/**
		 * Filter final item array
		 *
		 * @since  4.0.0
		 * @var    array
		 */
		$this->items = apply_filters( 'cherry_breadcrumbs_items', $this->items, $this->args );
	}

	/**
	 * Add parent items for current folder.
	 */
	public function add_folder_parents() {
		if ( empty( $_SESSION['tm-gallery-breadcrumbs'] ) ) {
			return;
		}

		$bc      = $_SESSION['tm-gallery-breadcrumbs'];
		$gallery = $this->get_gallery_id();

		global $post;

		if ( ! $gallery || ! isset( $bc[ $gallery ] ) ) {
			return;
		}

		$key = str_replace( 'tm_pg_', '', get_post_type( $post->ID ) );

		if ( ! in_array( $post->ID, $bc[ $gallery ] ) ) {
			$_SESSION['tm-gallery-breadcrumbs'][ $gallery ][ $key ] = $post->ID;
		}

		if ( 'album' === $key ) {
			add_action( 'wp_footer', array( $this, 'clear_data' ) );
		}

		foreach ( array( 'page', 'set', 'album' ) as $type ) {

			if ( ! isset( $bc[ $gallery ][ $type ] ) ) {
				break;
			}

			if ( $key == $type ) {
				break;
			}

			$post_id = $bc[ $gallery ][ $type ];

			if ( 'set' === $type && ! $this->album_in_set( $post->ID, $post_id ) ) {
				break;
			}

			$parent = get_post( $post_id );

			$this->_add_item( 'link_format', $parent->post_title, $this->get_parent_link( $parent->ID ) );
		}

	}

	/**
	 * Check if passed album in processed set
	 *
	 * @param  int $album_id Album id.
	 * @param  int $set_id   Set id.
	 * @return bool
	 */
	public function album_in_set( $album_id, $set_id ) {
		$albums = get_post_meta( $set_id, 'tm_pg_order_albums', true );
		return is_array( $albums ) ? in_array( $album_id, $albums ) : false;
	}

	/**
	 * Clear set and album data after album showing
	 *
	 * @return void
	 */
	public function clear_data() {

		$gallery = $this->get_gallery_id();

		foreach ( array( 'set', 'album' ) as $key ) {
			if ( isset( $_SESSION[ $gallery ][ $key ] ) ) {
				unset( $_SESSION[ $gallery ][ $key ] );
			}
		}

	}

	/**
	 * Get url for passed element
	 *
	 * @param  int $id Element to get parent for
	 */
	public function get_parent_link( $id ) {

		$post_type = get_post_type( $id );
		$return    = get_permalink( $id );
		$gallery   = wp_cache_get( 'current_gallery', 'tm-gallery' );

		if ( ! in_array( $post_type, Core::$post_types ) ) {
			return $return;
		}

		if ( ! $gallery ) {
			return $return;
		}

		$permalink = get_option( 'permalink_structure' );

		if ( ! $permalink ) {
			$return = add_query_arg( array( 'parent_gallery' => intval( $gallery ) ), $return );
		} else {
			$gall   = get_post( intval( $gallery ) );
			$slug   = $gall->post_name;
			$return = str_replace( home_url( '/' ), home_url( '/gallery/' . $slug . '/' ), $return );
		}

		return $return;
	}

	/**
	 * Try to get gallery ID from query
	 *
	 * @return int|bool
	 */
	public function get_gallery_id() {

		$gallery = get_query_var( 'parent_gallery' );
		$id      = false;

		if ( ! $gallery ) {
			return false;
		}

		if ( is_numeric( $gallery ) ) {
			$id = $gallery;
		} else {
			global $wpdb;
			$id = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT ID FROM $wpdb->posts WHERE post_name = %s AND post_type= %s",
					$gallery, Core::$post_types['gallery']
				)
			);
			$id = intval( $id );
		}

		if ( $id ) {
			wp_cache_set( 'current_gallery', $id, 'tm-gallery' );
			return $id;
		} else {
			return false;
		}

	}

	/**
	 * Add single folder trailings
	 */
	private function add_single_folder() {
		global $post;
		$this->_add_item( 'target_format', get_the_title( $post->ID ) );
		$this->page_title = false;
	}
}
