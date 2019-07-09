<?php

abstract class MPHBCustomPostType  {

	protected $postTypeName;
	protected $capability = 'edit_post';

	/**
	 *
	 * @var MPHBMetaBoxGroup[]
	 */
	protected $fieldGroups = array();

	public function __construct() {
		$this->addActions();
	}

	protected function addActions(){
		add_action('init', array($this, 'register'));
		add_action('init', array($this, 'initMetaBoxes'));
		add_action('admin_enqueue_scripts', array($this, 'enqueueAdminScripts'));
		add_action('save_post', array($this, 'saveMetaBoxes'), 10, 3);

		// Custom columns of %this_post_type% listing table
		add_filter( 'manage_' . $this->postTypeName . '_posts_columns', array($this, 'setManagePageCustomColumns') );
		add_filter( 'manage_edit-' . $this->postTypeName . '_sortable_columns', array($this, 'setManagePageCustomColumnsSortable') );
		add_action( 'manage_' . $this->postTypeName . '_posts_custom_column' , array($this, 'renderManagePageCustomColumns'), 10, 2 );
	}

	abstract public function register();

	public function enqueueAdminScripts(){
		if ( $this->isAdminSingleEditPage() ) {
			MPHB()->getAdminMainScriptManager()->enqueue();
		}
	}

	abstract public function initMetaBoxes();

	public function registerMetaBoxes(){
		foreach ( $this->fieldGroups as $group ) {
			$group->register();
		}
	}

	protected function isCanSave($postId){
		return current_user_can( $this->capability, $postId ) && ! wp_is_post_autosave( $postId ) && ! wp_is_post_revision( $postId );
	}

	public function saveMetaBoxes($postId, $post, $update){

		if ( empty($postId) || empty($post) ) {
			return;
		}
		// Dont' save meta boxes for revisions or autosaves
		if ( defined( 'DOING_AUTOSAVE' ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
			return;
		}

		// Check the post being saved == the $postId to prevent triggering this call for other save_post events
		if ( empty( $_POST['post_ID'] ) || $_POST['post_ID'] != $postId ) {
			return;
		}

		if ( $post->post_type == $this->getPostType() && $this->isAdminSingleEditPage() && $this->isCanSave($postId) ) {
			foreach ($this->fieldGroups as $metaGroup) {
				$metaGroup->setPostId($postId);
				$metaGroup->save();
			}
			remove_action('save_post', array($this, 'saveMetaBoxes'));
		}
	}

	public function getPostType() {
		return $this->postTypeName;
	}

	public function setManagePageCustomColumns($columns){
		return $columns;
	}

	public function setManagePageCustomColumnsSortable($columns){
		return $columns;
	}

	public function renderManagePageCustomColumns( $column, $postId ){}

	protected function isAdminListingPage(){
		global $typenow, $pagenow;
		return is_admin() && $pagenow === 'edit.php' && $typenow === $this->postTypeName;
	}

	public function isAdminSingleEditPage(){
		return $this->isAdminSingleAddNewPage() || $this->isAdminSingleEditExistingPage();
	}

	public function isAdminSingleAddNewPage(){
		global $typenow, $pagenow;
		return is_admin() && $typenow === $this->postTypeName && $pagenow === 'post-new.php';
	}
	public function isAdminSingleEditExistingPage(){
		global $typenow, $pagenow;
		return is_admin() && $typenow === $this->postTypeName && $pagenow === 'post.php';
	}

	/**
	 * Insert Post to DB
	 *
	 * @param array $postAttrs Attributes of post
	 * @return int The post ID on success. The value 0 on failure.
	 */
	public function insertPost($atts) {
		$postData = $this->_parsePostData($atts);
		if (!is_wp_error( $postData )) {
			$postId = wp_insert_post($postData['post']);
			if ( !is_wp_error($postId) ) {
				foreach($postData['post_meta'] as $postMetaName => $postMetaValue ) {
					add_post_meta($postId, $postMetaName, $postMetaValue);
				}
				if ($postData['thumbnail']) {
					set_post_thumbnail($postId, $postData['thumbnail']);
				}
				foreach($postData['taxonomies'] as $taxName => $terms) {
					wp_set_post_terms($postId, $terms, $taxName);
				}
			}
			return $postId;
		} else {
			return $postData;
		}
	}

	/**
	 *
	 * @param array $atts
	 * @param array $defaults
	 * @return array
	 */
	protected function _parsePostData($atts, $defaults = array()){
		$postData = array(
			'post' => array(),
			'thumbnail' => '',
			'post_meta' => array(),
			'taxonomies' => array()
		);

		$atts = array_merge($defaults, $atts);

		$postData['post']['post_type'] = $this->postTypeName;

		// @todo sanitize/validate data return error if not valid
		if ( isset($atts['post_status']) ) {
			$postData['post']['post_status'] = $atts['post_status'];
		}
		if (isset($atts['post_date'])) {
			$postData['post']['post_date'] = $atts['post_date'];
		}
		if (isset($atts['post_content'])) {
			$postData['post']['post_content'] = $atts['post_content'];
		}
		if (isset($atts['post_title'])) {
			$postData['post']['post_title'] = $atts['post_title'];
		}

		return $postData;
	}

	public function getManagePostsLink( $additionalArgs = array() ){
		$editUrl = admin_url('edit.php');
		$queryArgs = array_merge(array(
			'post_type' => $this->getPostType()
		), $additionalArgs);
		return add_query_arg( $queryArgs, $editUrl );
	}

	public function getPosts( $atts = array() ){
		$atts = wp_parse_args( $atts, array(
			'posts_per_page' => -1,
			'post_status'	 => array(
				'publish'
			)
			) );

		$atts['post_type']			 = $this->getPostType();
		$atts['ignore_sticky_posts'] = true;
		$atts['suppress_filters']	 = false;

		if ( isset( $atts['meta_query'] ) AND MPHB()->isWPVersion( '4.1', '<' ) ) {
			$metaQuery					 = $atts['meta_query'];
			unset( $atts['meta_query'] );
			$atts['mphb_fix_meta_query'] = true;
			$atts['mphb_meta_query']	 = $metaQuery;
		}

		return get_posts( $atts );
	}

}