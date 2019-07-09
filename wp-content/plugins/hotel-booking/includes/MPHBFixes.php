<?php

class MPHBFixes {

	const FIX_META_QUERY_FLAG = 'mphb_fix_meta_query';

	public function __construct(){
		add_filter( 'posts_clauses', array($this, 'fixOldWPMetaQuery'), 10, 2 );
	}

	/**
	 * Allow old wordpress (<4.1) support complex meta query
	 *
	 * @global WPDB $wpdb
	 * @param array $pieces
	 * @param WP_Query $wp_query
	 * @return array
	 */
	public function fixOldWPMetaQuery( $pieces, $wp_query ){
		global $wpdb;
		if ( $wp_query->get( self::FIX_META_QUERY_FLAG ) ) {
			$metaQuery = $wp_query->get( 'mphb_meta_query' );
			if ( !empty( $metaQuery ) ) {

				MPHB()->requireOnce( 'includes/MPHB_WP_Meta_Query.php' );
				$metaQueryObj = new MPHB_WP_Meta_Query( $metaQuery );

				$clauses = $metaQueryObj->get_sql( 'post', $wpdb->posts, 'ID', $wp_query );

				$pieces['join'] .= $clauses['join'];
				$pieces['where'] .= $clauses['where'];
				$pieces['groupby'] = "{$wpdb->posts}.ID";
			}
		}

		return $pieces;
	}

}
