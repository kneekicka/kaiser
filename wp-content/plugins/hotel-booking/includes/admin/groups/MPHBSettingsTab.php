<?php

class MPHBSettingsTab {

	private $nonceName;
	private $nonceSaveAction;
	
	/**
	 *
	 * @var MPHBSettingsGroup[]
	 */
	protected $groups = array();
	protected $name;
	protected $label;
	protected $pageName;
	protected $pseudoPageName;

	/**	 
	 *
	 * @param string $name
	 * @param string $label
	 * @param string $pageName
	 */
	public function __construct($name, $label, $pageName) {
		$this->name = $name;
		$this->label = $label;
		$this->pageName = $pageName;
		$this->pseudoPageName = $pageName . '_' . $this->name;
		$this->nonceName = '_nonce_' . $this->pseudoPageName;
		$this->nonceSaveAction = '_save_' . $this->pseudoPageName;
	}

	/**
	 *
	 * @param MPHBSettingsGroup $group
	 */
	public function addGroup( MPHBSettingsGroup $group ) {
		$this->groups[] = $group;
	}	

	/**
	 *
	 * @return MPHBSettingsGroup[]
	 */
	public function getGroups() {
		return $this->groups;
	}

	/**
	 *
	 * @return string
	 */
	function getNonceName(){
		return $this->nonceName;
	}

	/**
	 *
	 * @return string
	 */
	function getNonceSaveAction(){
		return $this->nonceSaveAction;
	}

	/**
	 *
	 * @return string
	 */
	function getName(){
		return $this->name;
	}

	/**
	 *
	 * @return string
	 */
	function getLabel(){
		return $this->label;
	}

	/**
	 *
	 * @return string
	 */
	function getPageName(){
		return $this->pageName;
	}

	/**
	 *
	 * @return string
	 */
	function getPseudoPageName(){
		return $this->pseudoPageName;
	}	
	
	public function register(){		
		foreach ($this->groups as $group) {
			$group->register();
		}		
	}

	public function render(){
		$destinationUrl = add_query_arg(
			array(
				'page' => $this->getPageName(),
				'tab' => $this->getName()
			),
			admin_url('admin.php')
		);
		
		printf('<form action="%s" method="POST">', $destinationUrl);
		wp_nonce_field( $this->getNonceSaveAction(), $this->getNonceName() );
		settings_fields( $this->getPseudoPageName() );
		do_settings_sections( $this->getPseudoPageName() );
		submit_button();
		echo '</form>';
	}

	public function save() {
		if ( $this->checkNonce()) {

			foreach ($this->groups as $group) {
				$group->save();
			}
			
			wp_redirect(
				add_query_arg(
					array(
						'page' => $this->getPageName(),
						'tab' => $this->getName(),
						'settings-updated' => 'true'
					),
					admin_url('admin.php')
				)
			);
		}		
	}

	/**
	 *
	 * @return boolean
	 */
	private function checkNonce(){
		return isset($_POST[$this->nonceName]) && wp_verify_nonce($_POST[$this->nonceName], $this->nonceSaveAction);
	}

}