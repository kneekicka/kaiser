<?php
class MPHBSettingsPage extends MPHBAdminPage{

	protected $menuOrder = 20;
	private $tabs = array();
	protected $name = 'mphb_settings';
	private $hookSuffix;

	public function initFields(){

		$generalTab				 = $this->_generateGeneralTab();
		$adminEmailsTab			 = $this->_generateAdminEmailsTab();
		$customerEmailsTab		 = $this->_generateCustomerEmailsTab();
		$globalEmailSettingsTab	 = $this->_generateGlobalEmailSettingsTab();

		$this->tabs = array(
			$generalTab->getName()				 => $generalTab,
			$adminEmailsTab->getName()			 => $adminEmailsTab,
			$customerEmailsTab->getName()		 => $customerEmailsTab,
			$globalEmailSettingsTab->getName()	 => $globalEmailSettingsTab
		);

	}

	/**
	 *
	 * @return \MPHBSettingsTab
	 */
	private function _generateGeneralTab(){
		$generalTab = new MPHBSettingsTab('general', __('General', 'motopress-hotel-booking'), $this->name);

		// Pages
		$pagesGroup = new MPHBSettingsGroup('mphb_pages', __('Pages', 'motopress-hotel-booking'), $generalTab->getPseudoPageName());
		$resultPage = MPHBFieldFactory::create('mphb_search_results_page', array(
			'type' => 'page-select',
			'label' => __('Search Results Page', 'motopress-hotel-booking'),
			'default' => ''
		));
		$pagesGroup->addField($resultPage);


		$checkoutPage = MPHBFieldFactory::create('mphb_checkout_page', array(
			'type' => 'page-select',
			'label' => __('Checkout Page', 'motopress-hotel-booking'),
			'default' => ''
		));
		$pagesGroup->addField($checkoutPage);
		$generalTab->addGroup($pagesGroup);

		// Misc
		$miscGroup = new MPHBSettingsGroup('mphb_misc', __('Misc', 'motopress-hotel-booking'), $generalTab->getPseudoPageName());
		$unitsField = MPHBFieldFactory::create('mphb_square_unit', array(
			'type' => 'select',
			'label' => __('Square units', 'motopress-hotel-booking'),
			'list' => MPHB()->getSettings()->getSquareUnits()->getLabels(),
			'default' => 'm2'
		));
		$miscGroup->addField($unitsField);

		$currencyField = MPHBFieldFactory::create('mphb_currency_symbol', array(
			'type' => 'select',
			'label' => __('Currency', 'motopress-hotel-booking'),
			'list' => MPHB()->getSettings()->getCurrency()->getLabels(),
			'default' => 'USD'
		));
		$miscGroup->addField($currencyField);

		$checkInTimeField = MPHBFieldFactory::create('mphb_check_in_time', array(
			'type' => 'timepicker',
			'label' => __('Check-In Time', 'motopress-hotel-booking'),
			'default' => '11:00'
		));
		$miscGroup->addField($checkInTimeField);

		$checkInTimeField = MPHBFieldFactory::create('mphb_check_out_time', array(
			'type' => 'timepicker',
			'label' => __('Check-Out Time', 'motopress-hotel-booking'),
			'default' => '10:00'
		));
		$miscGroup->addField($checkInTimeField);

		$bedType = MPHBFieldFactory::create('mphb_bed_types', array(
			'type' => 'complex',
			'label' => __('Bed Types', 'motopress-hotel-booking'),
			'fields' => array(
				MPHBFieldFactory::create('type', array(
					'type' => 'text',
					'default' => '',
					'label' => __('Type', 'motopress-hotel-booking'),
				))
			),
			'default' => array(),
			'add_label' => __('Add bed type', 'motopress-hotel-booking')
		));
		$miscGroup->addField($bedType);

		$confirmationMode = MPHBFieldFactory::create('mphb_confirmation_mode', array(
			'type' => 'select',
			'label' => __('Confirmation Mode', 'motopress-hotel-booking'),
			'list' => array(
				'auto' => __('Auto', 'motopress-hotel-booking'),
				'manual' => __('Manual', 'motopress-hotel-booking')
			),
			'description' => __('In manual mode requires adiministratorom confirmation.', 'motopress-hotel-booking'),
			'default' => 'auto'
		));
		$miscGroup->addField($confirmationMode);

		if ( !current_theme_supports('motopress-hotel-booking') ) {
			$templateMode = MPHBFieldFactory::create('mphb_template_mode', array(
				'type' => 'select',
				'label' => __('Template Mode', 'motopress-hotel-booking'),
				'list' => array(
					'plugin' => __('Theme Mode', 'motopress-hotel-booking'),
					'theme' => __('Developer Mode', 'motopress-hotel-booking')
				),
				'description' => __('Choose Theme Mode to display the content with the styles of your theme. Choose Developer Mode to control appearance of the content with custom page templates, actions and filters. This option can\'t be changed if your theme is initially integrated with the plugin.', 'motopress-hotel-booking'),
				'disabled' => true,
				'default' => 'theme'
			));
			$miscGroup->addField($templateMode);
		}
		$generalTab->addGroup($miscGroup);

		return $generalTab;
	}

	/**
	 *
	 * @return \MPHBSettingsTab
	 */
	private function _generateAdminEmailsTab(){

		$tab = new MPHBSettingsTab( 'admin_emails', __( 'Admin Emails', 'motopress-hotel-booking' ), $this->name );

		do_action( 'mphb_generate_settings_admin_emails', $tab );

		return $tab;
	}

	/**
	 *
	 * @return \MPHBSettingsTab
	 */
	private function _generateCustomerEmailsTab(){

		$tab = new MPHBSettingsTab( 'customer_emails', __( 'Customer Emails', 'motopress-hotel-booking' ), $this->name );

		do_action( 'mphb_generate_settings_customer_emails', $tab );

		return $tab;
	}

	/**
	 *
	 * @return \MPHBSettingsTab
	 */
	private function _generateGlobalEmailSettingsTab(){
		$tab = new MPHBSettingsTab( 'global_emails', __( 'Email Settings', 'motopress-hotel-booking' ), $this->name );

		$emailGroup = new MPHBSettingsGroup( 'mphb_global_emails_settings_group', __( 'Email Sender Options', 'motopress-hotel-booking' ), $tab->getPseudoPageName() );

		$fromEmail = MPHBFieldFactory::create( 'mphb_email_from_email', array(
				'type'			 => 'email',
				'label'			 => __( 'From Email', 'motopress-hotel-booking' ),
				'default'		 => '',
				'placeholder'	 => MPHB()->getEmailSettings()->getDefaultFromEmail()
		) );

		$fromName = MPHBFieldFactory::create( 'mphb_email_from_name', array(
				'type'			 => 'text',
				'label'			 => __( 'From Name', 'motopress-hotel-booking' ),
				'default'		 => '',
				'placeholder'	 => MPHB()->getEmailSettings()->getDefaultFromName()
		) );

		$emailGroup->addField( $fromEmail );
		$emailGroup->addField( $fromName );

		$styleGroup = new MPHBSettingsGroup( 'mphb_global_emails_settings_style_group', __( 'Style', 'motopress-hotel-booking' ), $tab->getPseudoPageName() );

		$emailLogo = MPHBFieldFactory::create( 'mphb_email_logo', array(
				'type'		 => 'text',
				'label'		 => __( 'Logo Url', 'motopress-hotel-booking' ),
				'default'	 => '',
				'placeholder' => MPHB()->getEmailSettings()->getDefaultLogoUrl()
		) );

		$footerText = MPHBFieldFactory::create( 'mphb_email_footer_text', array(
				'type'			 => 'rich-editor',
				'label'			 => __( 'Footer Text', 'motopress-hotel-booking' ),
//				'description' => __('Default: ', 'motopress-hotel-booking') . MPHB()->getEmailSettings()->getDefaultFooterText(),
				'rows'			 => 3,
				'default'		 => MPHB()->getEmailSettings()->getDefaultFooterText()
		) );

	$baseColor = MPHBFieldFactory::create( 'mphb_email_base_color', array(
				'type'			 => 'color-picker',
				'label'			 => __( 'Base Color', 'motopress-hotel-booking' ),
//				'default'		 => '',
//				'description'	 => sprintf( __( 'Default: %s', 'motopress-hotel-booking' ), MPHB()->getEmailSettings()->getDefaultBaseColor() ),
				'default'		 => MPHB()->getEmailSettings()->getDefaultBaseColor(),
				'placeholder'	 => MPHB()->getEmailSettings()->getDefaultBaseColor()
		) );

		$bgColor = MPHBFieldFactory::create( 'mphb_email_bg_color', array(
				'type'			 => 'color-picker',
				'label'			 => __( 'Background Color', 'motopress-hotel-booking' ),
//				'default'		 => '',
//				'description'	 => sprintf( __( 'Default: %s', 'motopress-hotel-booking' ), MPHB()->getEmailSettings()->getDefaultBGColor() ),
				'default'		 => MPHB()->getEmailSettings()->getDefaultBGColor(),
				'placeholder'	 => MPHB()->getEmailSettings()->getDefaultBGColor()
		) );

		$bodyBGColor = MPHBFieldFactory::create( 'mphb_email_body_bg_color', array(
				'type'			 => 'color-picker',
				'label'			 => __( 'Body Background Color', 'motopress-hotel-booking' ),
//				'default'		 => '',
//				'description'	 => sprintf( __( 'Default: %s', 'motopress-hotel-booking' ), MPHB()->getEmailSettings()->getDefaultBodyBGColor() ),
				'default'		 => MPHB()->getEmailSettings()->getDefaultBodyBGColor(),
				'placeholder'	 => MPHB()->getEmailSettings()->getDefaultBodyBGColor()
		) );

		$bodyTextColor = MPHBFieldFactory::create( 'mphb_email_body_text_color', array(
				'type'			 => 'color-picker',
				'label'			 => __( 'Body Text Color', 'motopress-hotel-booking' ),
//				'default'		 => '',
//				'description'	 => sprintf( __( 'Default: %s', 'motopress-hotel-booking' ), MPHB()->getEmailSettings()->getDefaultBodyTextColor() ),
				'default'		 => MPHB()->getEmailSettings()->getDefaultBodyTextColor(),
				'placeholder'	 => MPHB()->getEmailSettings()->getDefaultBodyTextColor()
		) );

		$styleGroup->addField( $emailLogo );
		$styleGroup->addField( $footerText );
		$styleGroup->addField( $baseColor );
		$styleGroup->addField( $bgColor );
		$styleGroup->addField( $bodyBGColor );
		$styleGroup->addField( $bodyTextColor );

		$tab->addGroup( $emailGroup );
		$tab->addGroup( $styleGroup );

		return $tab;
	}

	public function addActions(){
		parent::addActions();
		add_action('admin_init', array($this, 'initFields'));
		add_action('admin_init', array($this, 'registerSettings'));
		add_action('admin_enqueue_scripts', array($this, 'enqueueAdminScripts'));
	}

	public function enqueueAdminScripts(){
		$screen = get_current_screen();
		if ( !is_null($screen) && $screen->id === $this->hookSuffix ) {
			MPHB()->getAdminMainScriptManager()->enqueue();
		}
	}

	public function createMenu(){
		$this->hookSuffix = add_submenu_page(MPHB()->getMainMenuSlug()
			, __('MotoPress Hotel Booking Settings', 'motopress-hotel-booking')
			, __('Settings', 'motopress-hotel-booking')
			, 'manage_options'
			, $this->name
			, array($this, 'renderPage')
		);
		add_action('load-' . $this->hookSuffix, array($this, 'save'));
	}

	public function renderPage(){
		if (isset($_GET['settings-updated']) && $_GET['settings-updated']) {
			add_settings_error(
				'mphbSettings',
				esc_attr('settings_updated'),
				__('Settings saved.', 'motopress-hotel-booking'),
				'updated'
			);
		}
		settings_errors('mphbSettings', false);
		$this->renderTabs();
		$tabName = $this->detectTab();
		if (isset($this->tabs[$tabName])) {
			$this->tabs[$tabName]->render();
		}
	}

	private function renderTabs(){
		    echo '<h1 class="nav-tab-wrapper">';
			if (is_array($this->tabs)) {
				foreach ($this->tabs as $tabId => $tab) {
					$class = ($tabId == $this->detectTab()) ? ' nav-tab-active' : '';
					echo '<a href="' . esc_url(add_query_arg(array('page' => $this->name, 'tab' => $tabId), admin_url('admin.php'))) . '" class="nav-tab' . $class . '">' . esc_html($tab->getLabel()) . '</a>';
				}
			}
			echo '</h1>';
	}

	private function detectTab(){
		$defaultTab = 'general';
		$tab = isset($_GET['tab']) ? $_GET['tab'] : $defaultTab;
		return $tab;
	}

	public function save(){
		$tabName = $this->detectTab();
		if ( isset($this->tabs[$tabName]) && !empty($_POST) && $this->isCanSave() ) {
			$this->tabs[$tabName]->save();
		}
	}

	private function isCanSave(){
		return current_user_can('manage_options');
	}

	public function registerSettings(){
		foreach ( $this->tabs as $tab ) {
			$tab->register();
		}
	}

}
