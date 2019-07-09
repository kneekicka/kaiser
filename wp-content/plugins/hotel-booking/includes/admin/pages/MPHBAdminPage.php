<?php

abstract class MPHBAdminPage {

	protected $menuOrder = 30;
	protected $name;

	public function __construct() {
		$this->addActions();
	}

	public function addActions(){
		add_action('admin_menu', array($this, 'createMenu'), $this->menuOrder);
	}

	abstract public function createMenu();

	public function getName(){
		return $this->name;
	}

	public function getUrl($additionalArgs = array()){
		$adminUrl = admin_url('admin.php');
		$args = array_merge(array(
			'page' => $this->getName()
		), $additionalArgs);
		$url = add_query_arg($args, $adminUrl);
		return $url;
	}
}
