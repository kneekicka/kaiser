<?php
/*
  Plugin Name: Hotel Booking
  Description: Reservation management plugin that allows you to receive, schedule and handle your bookings.
  Version: 0.1.4
  Author: MotoPress
  Author URI: http://www.getmotopress.com/
  License: GPLv2 or later
 */

/*
 * use add_theme_support('motopress-hotel-booking'); in your theme to override templates;
 * put new templates in mphb_templates folder in your theme;
 */


MPHotelBookingPlugin::setPluginDirPathAndUrl( __FILE__, ( isset( $plugin ) ? $plugin : null ), ( isset( $network_plugin ) ? $network_plugin : null ) );

class MPHotelBookingPlugin {

	private static $_pluginDirPath;
	private static $_pluginDirUrl;

	/**
	 * @todo complete description
	 * Fix for symlinked plugin
	 *
	 * @global string $wp_version
	 * @param string $file
	 * @param string|null $plugin
	 * @param string|null $network_plugin
	 */
	public static function setPluginDirPathAndUrl($file, $plugin, $network_plugin){
		global $wp_version;
		if (version_compare($wp_version, '3.9', '<') && isset($network_plugin)) {
			$mphbPluginFile = $network_plugin;
		} else {
			$mphbPluginFile = __FILE__;
		}

		$realDirName = basename(dirname($mphbPluginFile));
		$symlinkDirName = isset($plugin) ? basename(dirname($plugin)) : $realDirName;

		self::$_pluginDirPath = plugin_dir_path($mphbPluginFile);

		if (version_compare($wp_version, '3.9', '<')) {
			self::$_pluginDirUrl = plugin_dir_url($symlinkDirName . '/' . basename($mphbPluginFile));
		} else {
			self::$_pluginDirUrl = plugin_dir_url($mphbPluginFile);
		}
	}

	private $version;
	private $slug;
	private $prefix;
	private $pluginDir;
	private $pluginDirUrl;
	private $mainMenuSlug;
	private $mainMenuPage;
	private $mainMenuCapability;

	/**
	 *
	 * @var MPHBSettingsPage
	 */
	private $settingsPage;

	/**
	 *
	 * @var MPHBShortcodesPage
	 */
	private $shortcodesPage;

	/**
	 *
	 * @var MPHBRoomsGeneratorPage
	 */
	private $roomsGeneratorPage;

	/**
	 *
	 * @var MPHBReportsPage
	 */
	private $reportsPage;

	/**
	 *
	 * @var MPHBRoomTypeCPT
	 */
	private $roomTypeCPT;

	/**
	 *
	 * @var MPHBRoomCPT
	 */
	private $roomCPT;

	/**
	 *
	 * @var MPHBServiceCPT
	 */
	private $serviceCPT;

	/**
	 *
	 * @var MPHBBookingCPT
	 */
	private $bookingCPT;

	/**
	 *
	 * @var MPHBShortcodeSearch
	 */
	private $shortcodeSearch;

	/**
	 *
	 * @var MPHBShortcodeSearchResults
	 */
	private $shortcodeSearchResults;

	/**
	 *
	 * @var MPHBShortcodeCheckout
	 */
	private $shortcodeCheckout;

	/**
	 *
	 * @var MPHBShortcodeRooms
	 */
	private $shortcodeRooms;

	/**
	 *
	 * @var MPHBShortcodeRoom
	 */
	private $shortcodeRoom;

	/**
	 *
	 * @var MPHBShortcodeServices
	 */
	private $shortcodeServices;

	/**
	 *
	 * @var shortcodeBookingForm
	 */
	private $shortcodeBookingForm;

	/**
	 *
	 * @var MPHBShortcodeRoomRates
	 */
	private $shortcodeRoomRates;

	/**
	 *
	 * @var MPHBMailer
	 */
	private $mailer;

	/**
	 *
	 * @var MPHotelBookingPlugin
	 */
	private static $instance = null;

	// Components
	/**
	 *
	 * @var MPHBSettings
	 */
	private $settings;

	/**
	 *
	 * @var MPHBEmailSettings
	 */
	private $emailSettings;

	/**
	 *
	 * @var MPHBSession
	 */
	private $session;

	/**
	 *
	 * @var MPHBAjax
	 */
	private $ajax;

	/**
	 *
	 * @var MPHBWizard
	 */
	private $wizard;

	/**
	 *
	 * @var MPHBImporter
	 */
	private $importer;

	/**
	 *
	 * @var MPHBFrontendMainScriptManager
	 */
	private $frontendMainScriptManager;

	/**
	 *
	 * @var MPHBAdminMainScriptManager
	 */
	private $adminMainScriptManager;

	/**
	 *
	 * @var MPHBEmails
	 */
	private $emails;

	/**
	 *
	 * @var MPHBRoomType
	 */
	private $currentRoomType;

	public static function getInstance() {
		if ( !isset( self::$instance ) ) {
			self::$instance = new self();
			self::$instance->afterConstruct();
		}
		return self::$instance;
	}

	private function __construct() {
		$this->pluginDir = self::$_pluginDirPath;
		$this->pluginDirUrl = self::$_pluginDirUrl;
		$this->slug = basename( $this->pluginDir );
		$this->version = '0.1.4';
		$this->prefix = 'mphb';
		$this->mainMenuSlug = 'mphb_booking_menu';
	}

	protected function __clone() {}

	public function afterConstruct() {
		$this->includeFiles();
		$this->addActions();

		// Settings
		 $this->settings = new MPHBSettings();
		 $this->emailSettings = new MPHBEmailSettings();

		 // Session
		 $this->session = new MPHBSession();

		// SubMenu Pages
		$this->settingsPage = new MPHBSettingsPage();
		$this->shortcodesPage = new MPHBShortcodesPage();
		$this->roomsGeneratorPage = new MPHBRoomsGeneratorPage();
		$this->reportsPage = new MPHBReportsPage();

		// Custom Post Types
		$this->roomTypeCPT = new MPHBRoomTypeCPT();
		$this->roomCPT = new MPHBRoomCPT();
		$this->serviceCPT = new MPHBServiceCPT();
		$this->bookingCPT = new MPHBBookingCPT();

		// Shortcodes
		$this->shortcodeSearch = new MPHBShortcodeSearch();
		$this->shortcodeSearchResults = new MPHBShortcodeSearchResult();
		$this->shortcodeCheckout = new MPHBShortcodeCheckout();
		$this->shortcodeRooms = new MPHBShortcodeRooms();
		$this->shortcodeRoom = new MPHBShortcodeRoom();
		$this->shortcodeServices = new MPHBShortcodeServices();
		$this->shortcodeBookingForm = new MPHBShortcodeBookingForm();
		$this->shortcodeRoomRates = new MPHBShortcodeRoomRates();

		$this->mailer = new MPHBMailer();
		$this->wizard = new MPHBWizard();
		$this->importer = new MPHBImporter();
		$this->frontendMainScriptManager = new MPHBFrontendMainScriptManager();
		$this->adminMainScriptManager = new MPHBAdminMainScriptManager();

		$this->emails = new MPHBEmails();
		new MPHBFixes();

		$this->ajax = new MPHBAjax();
	}

	public function requireOnce($relativePath){
		require_once $this->getPluginPath($relativePath);
	}

	public function includeFiles() {

		// Functions
		$this->requireOnce('functions.php');
		$this->requireOnce('template-functions.php');

		// Session
		$this->requireOnce('includes/libraries/wp-session-manager/class-recursive-arrayaccess.php');
		$this->requireOnce('includes/libraries/wp-session-manager/class-wp-session.php');
		$this->requireOnce('includes/libraries/wp-session-manager/wp-session.php');
		$this->requireOnce('includes/MPHBSession.php');

		// Emorgifier
		$this->requireOnce( 'includes/libraries/Emogrifier/Emogrifier.php' );

		// Settings
		$this->requireOnce('includes/settings/MPHBSettings.php');
		$this->requireOnce('includes/settings/MPHBEmailSettings.php');

		// Input Fields
		$this->requireOnce('includes/admin/fields/MPHBInputField.php');
		$this->requireOnce('includes/admin/fields/MPHBTextField.php');
		$this->requireOnce('includes/admin/fields/MPHBCheckboxField.php');
		$this->requireOnce('includes/admin/fields/MPHBNumberField.php');
		$this->requireOnce('includes/admin/fields/MPHBEmailField.php');
		$this->requireOnce('includes/admin/fields/MPHBTextareaField.php');
		$this->requireOnce('includes/admin/fields/MPHBRichEditorField.php');
		$this->requireOnce('includes/admin/fields/MPHBSelectField.php');
		$this->requireOnce('includes/admin/fields/MPHBMultipleSelectField.php');
		$this->requireOnce('includes/admin/fields/MPHBDynamicSelectField.php');
		$this->requireOnce('includes/admin/fields/MPHBPageSelectField.php');
		$this->requireOnce('includes/admin/fields/MPHBDatePickerField.php');
		$this->requireOnce('includes/admin/fields/MPHBTimePickerField.php');
		$this->requireOnce('includes/admin/fields/MPHBGalleryField.php');
		$this->requireOnce('includes/admin/fields/MPHBAbstractComplexField.php');
		$this->requireOnce('includes/admin/fields/MPHBComplexHorizontalField.php');
		$this->requireOnce('includes/admin/fields/MPHBComplexVerticalField.php');
		$this->requireOnce('includes/admin/fields/MPHBTotalPriceField.php');
		$this->requireOnce('includes/admin/fields/MPHBServiceChooserField.php');
		$this->requireOnce('includes/admin/fields/MPHBColorPickerField.php');

		$this->requireOnce('includes/admin/fields/MPHBFieldFactory.php');

		// Input Groups
		$this->requireOnce('includes/admin/groups/MPHBInputGroup.php');
		$this->requireOnce('includes/admin/groups/MPHBMetaBoxGroup.php');
		$this->requireOnce('includes/admin/groups/MPHBSettingsGroup.php');
		$this->requireOnce('includes/admin/groups/MPHBSettingsTab.php');

		// Custom Post Types
		$this->requireOnce('includes/post_types/MPHBCustomPostType.php');
		$this->requireOnce('includes/post_types/MPHBRoomTypeCPT.php');
		$this->requireOnce('includes/post_types/MPHBRoomCPT.php');
		$this->requireOnce('includes/post_types/MPHBServiceCPT.php');
		$this->requireOnce('includes/post_types/MPHBBookingCPT.php');

		// Pages
		$this->requireOnce('includes/admin/pages/MPHBAdminPage.php');
		$this->requireOnce('includes/admin/pages/MPHBSettingsPage.php');
		$this->requireOnce('includes/admin/pages/MPHBShortcodesPage.php');
		$this->requireOnce('includes/admin/pages/MPHBRoomsGeneratorPage.php');
		$this->requireOnce('includes/admin/pages/MPHBReportsPage.php');

		// Entities
		$this->requireOnce('includes/MPHBRoomType.php');
		$this->requireOnce('includes/MPHBRoom.php');
		$this->requireOnce('includes/MPHBBooking.php');
		$this->requireOnce('includes/MPHBService.php');
		$this->requireOnce('includes/MPHBRoomRates.php');
		$this->requireOnce('includes/MPHBRoomRate.php');
		$this->requireOnce('includes/MPHBCustomer.php');

		// Views
		$this->requireOnce('includes/views/MPHBRoomTypeView.php');
		$this->requireOnce('includes/views/MPHBSingleRoomTypeView.php');
		$this->requireOnce('includes/views/MPHBLoopRoomTypeView.php');
		$this->requireOnce('includes/views/MPHBLoopServiceView.php');
		$this->requireOnce('includes/views/MPHBSingleServiceView.php');
		$this->requireOnce('includes/views/MPHBBookingView.php');

		// Shortcodes
		$this->requireOnce('includes/shortcodes/MPHBShortcode.php');
		$this->requireOnce('includes/shortcodes/MPHBShortcodeSearch.php');
		$this->requireOnce('includes/shortcodes/MPHBShortcodeSearchResult.php');
		$this->requireOnce('includes/shortcodes/MPHBShortcodeCheckout.php');
		$this->requireOnce('includes/shortcodes/MPHBShortcodeRooms.php');
		$this->requireOnce('includes/shortcodes/MPHBShortcodeServices.php');
		$this->requireOnce('includes/shortcodes/MPHBShortcodeRoom.php');
		$this->requireOnce('includes/shortcodes/MPHBShortcodeBookingForm.php');
		$this->requireOnce('includes/shortcodes/MPHBShortcodeRoomRates.php');

		// Widgets
		$this->requireOnce('includes/widgets/MPHBWidget.php');
		$this->requireOnce('includes/widgets/MPHBSearchAvailabilityWidget.php');
		$this->requireOnce('includes/widgets/MPHBRoomsWidget.php');

		// Other
		$this->requireOnce('includes/MPHBUtils.php');
		$this->requireOnce('includes/MPHBWizard.php');
		$this->requireOnce('includes/MPHBImporter.php');
		$this->requireOnce('includes/MPHBFrontendMainScriptManager.php');
		$this->requireOnce('includes/MPHBAdminMainScriptManager.php');
		$this->requireOnce('includes/MPHBFixes.php');

		// Ajax
		$this->requireOnce('includes/MPHBAjax.php');

		// Emails
		$this->requireOnce('includes/emails/MPHBEmailTemplater.php');
		$this->requireOnce('includes/emails/MPHBMailer.php');
		$this->requireOnce('includes/emails/MPHBAbstractEmail.php');
		$this->requireOnce('includes/emails/MPHBAdminEmail.php');
		$this->requireOnce('includes/emails/MPHBCustomerEmail.php');
		$this->requireOnce('includes/emails/MPHBEmails.php');
	}

	public function addActions() {
		add_action( 'plugins_loaded', array($this, 'loadTextdomain') );
		add_action( 'admin_menu', array($this, 'createMenu'), 10 );

		add_action( 'admin_enqueue_scripts', array($this, 'registerAdminScripts'), 9 );
		add_action( 'wp_enqueue_scripts', array($this, 'registerFrontEndScripts'), 9 );

		add_action( 'wp_enqueue_scripts', array($this, 'enqueueFrontEndScripts'), 11 );

		add_action( 'the_post', array( $this, 'setCurrentRoomType') );
	}

	public function registerFrontEndScripts(){
		// Scripts Third-Party
		wp_register_script( 'mphb-canjs', $this->getPluginUrl('vendors/canjs/can.custom.min.js'), array('jquery'), $this->getVersion(), true );
		wp_register_script( 'mphb-magnific-popup', $this->getPluginUrl('vendors/magnific-popup/dist/jquery.magnific-popup.min.js'), array('jquery'), $this->getVersion(), true);
		wp_register_script( 'mphb-kbwood-plugin', $this->getPluginUrl('vendors/kbwood/datepick/jquery.plugin.min.js'), array('jquery'), $this->getVersion(), true );
		wp_register_script( 'mphb-kbwood-datepick', $this->getPluginUrl('vendors/kbwood/datepick/jquery.datepick.min.js'), array('jquery', 'mphb-kbwood-plugin'), $this->getVersion(), true );
		wp_register_script( 'mphb-flexslider', $this->getPluginUrl('vendors/woothemes-FlexSlider/jquery.flexslider-min.js'), array('jquery'), $this->getVersion(), true);
		wp_register_script('mphb-jquery-serialize-json', $this->getPluginUrl('vendors/jquery.serializeJSON/jquery.serializejson.min.js'), array('jquery'), $this->getVersion());

		// Sctipts
		wp_register_script( 'mphb', $this->getPluginUrl('assets/js/frontend/mphb.js'), array('jquery', 'mphb-canjs', 'mphb-kbwood-datepick'), $this->getVersion(), true);

		// Styles
		wp_register_style( 'mphb-kbwood-datepick-css', $this->getPluginUrl('vendors/kbwood/datepick/jquery.datepick.css'), null, $this->getVersion() );
		wp_register_style( 'mphb-magnific-popup-css', $this->getPluginUrl( 'vendors/magnific-popup/dist/magnific-popup.css' ), null, $this->getVersion());

		$useFixedFlexslider = apply_filters('mphb_use_fixed_flexslider_css', true);
		if ( $useFixedFlexslider ) {
			wp_register_style( 'mphb-flexslider-css', $this->getPluginUrl('assets/css/flexslider-fixed.css'), null, $this->getVersion());
		} else {
			wp_register_style( 'mphb-flexslider-css', $this->getPluginUrl('vendors/woothemes-FlexSlider/flexslider.css'), null, $this->getVersion());
		}

		wp_register_style( 'mphb', $this->getPluginUrl('assets/css/mphb.css', null, $this->getVersion()));
		wp_register_style( 'mphb-checkout', $this->getPluginUrl( 'assets/css/checkout.css' ), null, $this->getVersion());
	}

	public function enqueueFrontEndScripts(){
		if ( is_singular($this->roomTypeCPT->getPostType()) ) {
			$this->getFrontendMainScriptManager()->enqueue();
		}

		if ( get_the_ID() == MPHB()->getSettings()->getCheckoutPageID() ) {
			$this->getFrontendMainScriptManager()->enqueue();
		}
	}

	public function registerAdminScripts() {
		// Scripts

		wp_register_script( 'mphb-canjs', $this->getPluginUrl('vendors/canjs/can.custom.min.js'), array('jquery'), $this->getVersion(), true );
		// @todo if possible concat kbwood scripts
		wp_register_script( 'mphb-kbwood-plugin', $this->getPluginUrl('vendors/kbwood/datepick/jquery.plugin.min.js'), array('jquery'), $this->getVersion(), true );
		wp_register_script( 'mphb-kbwood-datepick', $this->getPluginUrl('vendors/kbwood/datepick/jquery.datepick.min.js'), array('jquery', 'mphb-kbwood-plugin'), $this->getVersion(), true );
		wp_register_script('mphb-jquery-serialize-json', $this->getPluginUrl('vendors/jquery.serializeJSON/jquery.serializejson.min.js'), array('jquery'), $this->getVersion());
		wp_register_script('mphb-bgrins-spectrum', $this->getPluginUrl('vendors/bgrins-spectrum/build/spectrum-min.js'), array('jquery'), $this->getVersion(), true);


		wp_register_script( 'mphb-admin', $this->getPluginUrl() . 'assets/js/admin/admin.js', array('jquery', 'mphb-kbwood-datepick', 'mphb-canjs', 'mphb-bgrins-spectrum'), $this->getVersion(), true );

		// Styles
		wp_register_style( 'mphb-kbwood-datepick-css', $this->getPluginUrl('vendors/kbwood/datepick/jquery.datepick.css'), null, $this->getVersion() );
		wp_register_style( 'mphb-bgrins-spectrum', $this->getPluginUrl('vendors/bgrins-spectrum/build/spectrum_theme.css'), null, $this->getVersion());
		wp_register_style( 'mphb-admin-css', $this->getPluginUrl('assets/css/admin.css'), array('mphb-kbwood-datepick-css', 'mphb-bgrins-spectrum'), $this->getVersion() );
	}

	public function loadTextDomain() {
		load_plugin_textdomain( $this->slug, false, $this->slug . '/languages' );
	}

	public function getPrefix() {
		return $this->prefix;
	}

	public function addPrefix( $str, $separator = '-' ) {
		return $this->getPrefix() . $separator . $str;
	}

	public function getSlug() {
		return $this->slug;
	}

	public function getPluginDir() {
		return $this->pluginDir;
	}

	/**
	 * Retrieve full path for the relative to plugin root path.
	 *
	 * @param string $relativePath
	 * @return string
	 */
	public function getPluginPath($relativePath = '') {
		return $this->getPluginDir() . $relativePath;
	}

	public function getPluginUrl($relativePath = '') {
		return $this->pluginDirUrl . $relativePath;
	}

	public function getAjaxUrl(){
		return admin_url('admin-ajax.php');
	}

	public function getVersion() {
		return $this->version;
	}

	public function getPagesList(){
		$pages = get_pages();
	}

	public function getMainMenuSlug() {
		return $this->mainMenuSlug;
	}

	/**
	 * @note available after 'admin_menu' 10 priority
	 * @return string
	 */
	public function getMainMenuCapability() {
		return $this->mainMenuCapability;
	}

	/**
	 * Retrieve Url of Motopress Hotel Booking Settings Page
	 *
	 * @return string Url
	 */
	public function getSettingsPageUrl() {
		return admin_url( 'admin.php?page=' . $this->settingsPage->getName() );
	}

	public function createMenu() {
		$this->mainMenuCapability = apply_filters( 'mphb_main_menu_capability', 'read' );
		$mainMenuPosition = apply_filters( 'mphb_main_menu_position', '57.5' );

		$this->mainMenuPage = add_menu_page( __( 'Bookings', $this->slug )
			, __( 'Bookings', $this->slug )
			, $this->mainMenuCapability
			, $this->mainMenuSlug
			, array($this, 'renderMainMenuPage')
			, null
			, $mainMenuPosition
		);
	}

	public function renderMainMenuPage() {
		// This will not shown because replaced by bookings cpt submenu
	}

	/**
	 *
	 * @return MPHBSettings;
	 */
	public function getSettings(){
		return $this->settings;
	}

	/**
	 *
	 * @return MPHBSettings;
	 */
	public function getEmailSettings(){
		return $this->emailSettings;
	}

	/**
	 * @return MPHBSession
	 */
	public function getSession(){
		return $this->session;
	}

	/**
	 * Retrieve relative to theme root path to templates.
	 *
	 * @return string
	 */
	public function getTemplatePath(){
		return apply_filters( 'mphb_template_path', 'mphb_templates/' );
	}

	/**
	 *
	 * @param WP_Post|int $post
	 */
	public function setCurrentRoomType($post){
		$this->currentRoomType = null;

		if ( is_int( $post ) ) {
			$post = get_post( $post );
		}

		if ( ! empty( $post->post_type ) && $post->post_type === MPHB()->getRoomTypeCPT()->getPostType() ) {
			$this->currentRoomType = new MPHBRoomType($post);
		}
	}

	/**
	 *
	 * @return MPHBRoomType
	 */
	public function getCurrentRoomType(){
		return $this->currentRoomType;
	}

	/**
	 *
	 * @return MPHBRoomTypeCPT
	 */
	public function getRoomTypeCPT() {
		return $this->roomTypeCPT;
	}

	/**
	 *
	 * @return MPHBRoomCPT
	 */
	public function getRoomCPT(){
		return $this->roomCPT;
	}

	/**
	 *
	 * @return MPHBServiceCPT
	 */
	public function getServiceCPT() {
		return $this->serviceCPT;
	}

	/**
	 *
	 * @return MPHBBookingCPT
	 */
	public function getBookingCPT() {
		return $this->bookingCPT;
	}

	/**
	 *
	 * @return MPHBShortcodeSearch
	 */
	public function getShortcodeSearch() {
		return $this->shortcodeSearch;
	}

	/**
	 *
	 * @return MPHBShortcodeCheckout
	 */
	public function getShortcodeCheckout(){
		return $this->shortcodeCheckout;
	}

	/**
	 *
	 * @return MPHBShortcodeServices
	 */
	public function getShortcodeServices(){
		return $this->shortcodeServices;
	}

	/**
	 *
	 * @return MPHBShortcodeBookingForm
	 */
	public function getShortcodeBookingForm(){
		return $this->shortcodeBookingForm;
	}

	/**
	 *
	 * @return MPHBShortcodeRoomRates
	 */
	public function getShortcodeRoomRates(){
		return $this->shortcodeRoomRates;
	}

	/**
	 *
	 * @return MPHBShortcodeSearchResults
	 */
	public function getShortcodeSearchResults(){
		return $this->shortcodeSearchResults;
	}

	/**
	 *
	 * @return MPHBShortcodeRooms
	 */
	public function getShortcodeRooms(){
		return $this->shortcodeRooms;
	}

	/**
	 *
	 * @return MPHBShortcodeRoom
	 */
	public function getShortcodeRoom(){
		return $this->shortcodeRoom;
	}

	/**
	 *
	 * @return MPHBMailer
	 */
	public function getMailer(){
		return $this->mailer;
	}

	/**
	 *
	 * @return MPHBAjax
	 */
	public function getAjax(){
		return $this->ajax;
	}

	/**
	 *
	 * @return MPHBSettingsPage
	 */
	public function getSettingsPage(){
		return $this->settingsPage;
	}

	/**
	 *
	 * @return MPHBShortcodesPage
	 */
	public function getShortcodesPage(){
		return $this->shortcodesPage;
	}

	/**
	 *
	 * @return MPHBRoomsGeneratorPage
	 */
	public function getRoomsGeneratorPage(){
		return $this->roomsGeneratorPage;
	}

	/**
	 *
	 * @return MPHBReportsPage
	 */
	public function getReportsPage(){
		return $this->reportsPage;
	}

	/**
	 *
	 * @return MPHBImporter
	 */
	public function getImporter(){
		return $this->importer;
	}

	/**
	 *
	 * @return MPHBFrontendMainScriptManager
	 */
	public function getFrontendMainScriptManager(){
		return $this->frontendMainScriptManager;
	}

	/**
	 *
	 * @return MPHBAdminMainScriptManager
	 */
	public function getAdminMainScriptManager(){
		return $this->adminMainScriptManager;
	}

	/**
	 *
	 * @return MPHBEmails
	 */
	public function getEmails(){
		return $this->emails;
	}

	/**
	 *
	 * @param array $parameters Array with keys 'mphb_adults', 'mphb_chidls', 'mphb_check_in_date', 'mphb_check_out_date'.
	 */
	public function storeSearchParameters( $parameters ){
		MPHB()->getSession()->set('mphb_search_parameters', $parameters);
	}

	/**
	 *
	 * @return array Array with keys 'mphb_adults', 'mphb_chidls', 'mphb_check_in_date', 'mphb_check_out_date' filled stored values or empty strings.
	 */
	public function getStoredSearchParameters(){

		$params = MPHB()->getSession()->get('mphb_search_parameters');

		$params = !is_null($params) ? $params : array();

		return array(
			'mphb_check_in_date' => isset($params['mphb_check_in_date']) ? $params['mphb_check_in_date'] : '',
			'mphb_check_out_date' => isset($params['mphb_check_out_date']) ? $params['mphb_check_out_date'] : '',
			'mphb_adults' => isset($params['mphb_adults']) ? $params['mphb_adults'] : '',
			'mphb_childs' => isset($params['mphb_childs']) ? $params['mphb_childs'] : '',
		);
	}

	/**
	 *
	 * @return bool
	 */
	public function hasStoredSearchParameters(){
		return mphb_has_cookie('mphb_search_parameters');
	}

	/**
	 *
	 * @param string $version version to compare with wp version
	 * @param string $operator Optional. Possible operators are: <, lt, <=, le, >, gt, >=, ge, ==, =, eq, !=, <>, ne respectively. Default =.
This parameter is case-sensitive, values should be lowercase.
	 * @return bool
	 */
	public function isWPVersion($version, $operator = '='){
		global $wp_version;
		return version_compare($wp_version, $version, $operator);
	}

	static public function activate() {
		MPHB()->getRoomTypeCPT()->register();
		MPHB()->getBookingCPT()->register();
		flush_rewrite_rules();
     }

	 static public function deactivate() {
		flush_rewrite_rules();
	 }

}

register_activation_hook( __FILE__, array( 'MPHotelBookingPlugin', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'MPHotelBookingPlugin', 'deactivate') );
MPHotelBookingPlugin::getInstance();

/**
 *
 * @return MPHotelBookingPlugin
 */
function MPHB() {
	return MPHotelBookingPlugin::getInstance();
}