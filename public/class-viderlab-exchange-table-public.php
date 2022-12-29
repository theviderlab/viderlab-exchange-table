<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://viderlab.com
 * @since      1.0.0
 *
 * @package    ViderLab_Exchange_Table
 * @subpackage ViderLab_Exchange_Table/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    ViderLab_Exchange_Table
 * @subpackage ViderLab_Exchange_Table/public
 * @author     ViderLab <soporte@viderlab.com>
 */
class ViderLab_Exchange_Table_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/viderlab-exchange-table-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/viderlab-exchange-table-public.js', array( 'jquery' ), $this->version, false );

	}

    public function show_exchange_rates_table( $atts = [], $content = null ) {

        ob_start();
        
        $this->exchange_rates_table();

        $s = ob_get_clean();

        return $s;
    }

    public static function exchange_rates_table() {
        $options = get_option( 'vet_options' );

        $current_date = strtotime(date('Y-m-d'));
        if( strtotime($options['vet_field_validity_start']) <= $current_date &&
            strtotime($options['vet_field_validity_end']) >= $current_date) {

            if( !isset($options['vet_field_table_display']) || 
                $options['vet_field_table_display'] == 'horizontal' ) {

                if( isset($options['vet_field_type_ref']) ) {
                    require plugin_dir_path( __FILE__ ) . 'partials/viderlab-exchange-table-horizontal-ref-1.php';
                } else {
                    require plugin_dir_path( __FILE__ ) . 'partials/viderlab-exchange-table-horizontal.php';          
                }

            } elseif( $options['vet_field_table_display'] == 'vertical' ) {

                if( isset($options['vet_field_type_ref']) ) {
                    require plugin_dir_path( __FILE__ ) . 'partials/viderlab-exchange-table-vertical-ref-1.php';
                } else {
                    require plugin_dir_path( __FILE__ ) . 'partials/viderlab-exchange-table-vertical.php';          
                }

            }
        }
    }

    public static function is_available() {
        $options = get_option( 'vet_options' );
        $current_date = strtotime(date('Y-m-d'));

        return strtotime($options['vet_field_validity_start']) <= $current_date &&
            strtotime($options['vet_field_validity_end']) >= $current_date;
    }

}
