<?php
class MPHBShortcodeServices extends MPHBShortcode{

	protected $shortcodeName = 'mphb_services';

	/**
	 *
	 * @var boolean
	 */
	private $showAll = true;
	/**
	 *
	 * @var array
	 */
	private $ids = array();

	public function __construct() {
		parent::__construct();
	}

	public function addActions() {
		parent::addActions();
		$this->addTemplateActions();
	}

	private function addTemplateActions(){
		add_action('mphb_sc_services_service_details', array('MPHBLoopServiceView', 'renderFeaturedImage'), 10);
		add_action('mphb_sc_services_service_details', array( 'MPHBLoopServiceView', 'renderTitle'), 20);
		add_action('mphb_sc_services_service_details', array('MPHBLoopServiceView', 'renderExcerpt'), 30);
		add_action('mphb_sc_services_service_details', array( 'MPHBLoopServiceView', 'renderPrice'), 40);

		add_action('mphb_sc_services_after_loop', array($this, 'renderPagination'));
	}

	/**
	 *
	 * @param array $atts
	 * @param null $content
	 * @param string $shortcodeName
	 * @return string
	 */
	public function render($atts, $content = null, $shortcodeName){
		$atts = shortcode_atts(array(
			'ids' => ''
		), $atts, $shortcodeName);
		ob_start();

		$this->setup($atts);

		$services = $this->getServices();

		if ($services->have_posts()) {

			do_action('mphb_sc_services_before_loop', $services);

			while ($services->have_posts()) : $services->the_post();
				$this->renderService();
			endwhile;

			wp_reset_postdata();

			do_action('mphb_sc_services_after_loop', $services);
		} else {
			$this->showNotMatchedMessage();
		}

		$content = ob_get_clean();
		$wrapperClasses = 'mphb_sc_services-wrapper';
		return '<div class="' . esc_attr($wrapperClasses) . '">' . $content . '</div>';
	}

	public function setup($atts){
		$this->showAll = empty($atts['ids']);
		$this->ids = array_map('trim', explode(',', $atts['ids']));
	}

	public function renderPagination($services){

		$big = 999999;
		$search_for   = array( $big, '#038;' );
		$replace_with = array( '%#%', '&' );
		$pagination = paginate_links( array(
			'base'    => str_replace( $search_for, $replace_with, get_pagenum_link( $big ) ),
			'format'  => '?paged=%#%',
			'current' => max( 1, $this->getPagedVar() ),
			'total'   => $services->max_num_pages
		) );
		?>

		<?php if ( ! empty( $pagination ) ) { ?>
		<div id="mphb_sc_services-pagination" class="mphb-pagination">
			<?php echo $pagination; ?>
		</div>
		<?php };
	}

	private function getPagedVar(){
		if ( get_query_var( 'paged' ) ) {
			$paged = absint( get_query_var('paged') );
		} else if ( get_query_var( 'page' ) ) {
			$paged = absint( get_query_var( 'page' ) );
		} else {
			$paged = 1;
		}
		return $paged;
	}

	public function getServices(){
		$queryAtts = array(
			'post_type' => MPHB()->getServiceCPT()->getPostType(),
			'post_status' => 'publish',
			'paged' => $this->getPagedVar(),
			'orderby' => 'menu_order',
			'ignore_sticky_posts' => true
		);
		if ( !$this->showAll ) {
			$queryAtts['post__in'] = $this->ids;
		}
		return new WP_Query($queryAtts);
	}

	private function renderService(){
		$service = new MPHBService(  get_the_ID() );
		do_action('mphb_sc_services_before_service', $service);
		?>
		<div class="mphb-service <?php echo join(' ', mphb_tmpl_get_filtered_post_class()); ?>">
			<?php do_action('mphb_sc_services_service_details', $service); ?>
		</div>
	<?php
		do_action('mphb_sc_services_after_service', $service);
	}

	private function showNotMatchedMessage(){
		?>
		<p class="mphb-not-found"><?php _e('No services matched criteria.', 'motopress-hotel-booking'); ?></p>
		<?php
	}

}