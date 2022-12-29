<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://viderlab.com
 * @since      1.0.0
 *
 * @package    ViderLab_Exchange_Table
 * @subpackage ViderLab_Exchange_Table/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    ViderLab_Exchange_Table
 * @subpackage ViderLab_Exchange_Table/admin
 * @author     Vider <soporte@viderlab.com>
 */
class ViderLab_Exchange_Table_Admin {

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
	 * Array of currency codes.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $currencies    An array with the formar [currency_code] = currency_name.
	 */
	private $currencies;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
        $this->currencies = $this->currencies();
	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/viderlab-exchange-table-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/viderlab-exchange-table-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Tourist Packages admin menu.
	 *
	 * @since    1.0.0
	 */
    public function create_menus( ) {
        $hook = add_management_page(
            'Currency Exchange Table', 
            'Currency Exchange Table', 
            'manage_options', 
            'viderlab-exchange-table', 
            [ $this, 'admin_page' ], 
            '' 
        );
    }

    /**
     * Display callback for the submenu page.
	 *
	 * @since    1.0.0
     */
    public function admin_page() { 
        // check user capabilities
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        // add error/update messages

        // check if the user have submitted the settings
        // WordPress will add the "settings-updated" $_GET parameter to the url
        if ( isset( $_GET['settings-updated'] ) ) {
            // add settings saved message with the class of "updated"
            add_settings_error( 'vet_messages', 'vet_message', __( 'Settings Saved', 'viderlab-exchange-table' ), 'updated' );
        }

        // show error/update messages
        settings_errors( 'vet_messages' );
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <form action="options.php" method="post">
                <?php
                // output security fields for the registered setting "vet"
                settings_fields( 'vet' );
                // output setting sections and their fields
                // (sections are registered for "vet", each field is registered to a specific section)
                do_settings_sections( 'vet' );
                // output save settings button
                submit_button( __('Save Settings', 'viderlab-exchange-table') );
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Define components of admin settings page.
	 *
	 * @since    1.0.0
     */
    public function settings_init() {
        // Register a new setting for "vet" page.
        register_setting( 'vet', 'vet_options' );
        $options = get_option( 'vet_options' );

        // Register a new section in the "vet" page.
        add_settings_section(
            'vet_exchange_rates',
            __( 'Exchange Rates', 'viderlab-exchange-table' ), 
            [ $this, 'exchange_rates' ],
            'vet'
        );

        // Register a new section in the "vet" page.
        add_settings_section(
            'vet_validity',
            __( 'Validity', 'viderlab-exchange-table' ), 
            [ $this, 'validity' ],
            'vet'
        );

        // Register a new section in the "vet" page.
        add_settings_section(
            'vet_config',
            __( 'Configuration', 'viderlab-exchange-table' ), 
            [ $this, 'configuration' ],
            'vet'
        );

        // Register a new field in the "vet_field_ref_currency" section, inside the "vet" page.
        add_settings_field(
            'vet_field_ref_currency', // As of WP 4.6 this value is used only internally.
                                    // Use $args' label_for to populate the id inside the callback.
            __( 'Reference currency', 'viderlab-exchange-table' ),
            [ $this, 'field_select_currency' ],
            'vet',
            'vet_config',
            array(
                'label_for'         => 'vet_field_ref_currency',
                'class'             => 'vet_row',
                'vet_custom_data'   => 'custom',
                'description'       => esc_html__( 'Set the reference currency.', 'viderlab-exchange-table' ),
                'value'             => $options['vet_field_ref_currency'],
                )
        );

        // Register a new field in the "vet_field_type_ref" section, inside the "vet" page.
        $checkbox_text = __('Rates are referenced to 1 unit of the reference currency.', 'viderlab-exchange-table' );
        $checkbox_desc = esc_html__( 'If checked the table will show the reference currency with a value 
        of 1 and the rest of the rates referenced to that.', 'viderlab-exchange-table' );
        add_settings_field(
            'vet_field_type_ref', // As of WP 4.6 this value is used only internally.
                                    // Use $args' label_for to populate the id inside the callback.
            __( 'Type of reference', 'viderlab-exchange-table' ),
            [ $this, 'field_checkbox' ],
            'vet',
            'vet_config',
            array(
                'label_for'         => 'vet_field_type_ref',
                'class'             => 'vet_row',
                'vet_custom_data'   => 'custom',
                'checkbox_text'     => $checkbox_text,
                'description'       => $checkboc_desc,
            )
        );

        // Register a new field in the "vet_field_show_date" section, inside the "vet" page.
        $checkbox_text = __('Show the current date on the table.', 'viderlab-exchange-table' );
        $checkbox_desc = esc_html__( 'If checked the table will show the current date on the table.', 'viderlab-exchange-table' );
        add_settings_field(
            'vet_field_show_date', // As of WP 4.6 this value is used only internally.
                                    // Use $args' label_for to populate the id inside the callback.
            __( 'Show date', 'viderlab-exchange-table' ),
            [ $this, 'field_checkbox' ],
            'vet',
            'vet_config',
            array(
                'label_for'         => 'vet_field_show_date',
                'class'             => 'vet_row',
                'vet_custom_data'   => 'custom',
                'checkbox_text'     => $checkbox_text,
                'description'       => $checkboc_desc,
            )
        );

        // Register a new field in the "vet_field_table_display" section, inside the "vet" page.
        $radio_text = array(
            __('Horizontal', 'viderlab-exchange-table' ),
            __('Vertical', 'viderlab-exchange-table' ),
        );
        $radio_values = array(
            'horizontal',
            'vertical',
        );
        $radio_desc = esc_html__( 'Define the format of the table.', 'viderlab-exchange-table' );
        add_settings_field(
            'vet_field_table_display', // As of WP 4.6 this value is used only internally.
                                    // Use $args' label_for to populate the id inside the callback.
            __( 'Table display', 'viderlab-exchange-table' ),
            [ $this, 'field_radio' ],
            'vet',
            'vet_config',
            array(
                'label_for'         => 'vet_field_table_display',
                'class'             => 'vet_row',
                'vet_custom_data'   => 'custom',
                'radio_text'        => $radio_text,
                'radio_values'      => $radio_values,
                'description'       => $radio_desc,
            )
        );

        // Register a new field in the "vet_field_quantity" section, inside the "vet" page.
        add_settings_field(
            'vet_field_quantity', // As of WP 4.6 this value is used only internally.
                                    // Use $args' label_for to populate the id inside the callback.
            __( 'Number of currencies', 'viderlab-exchange-table' ),
            [ $this, 'field_quantity' ],
            'vet',
            'vet_config',
            array(
                'label_for'         => 'vet_field_quantity',
                'class'             => 'vet_row',
                'vet_custom_data'   => 'custom',
            )
        );

        $currency_text = __( 'Currency %d', 'viderlab-exchange-table' );
        $currency_desc = esc_html__( "Set the currency %d", 'viderlab-exchange-table' );
        for( $i = 1; $i <= $options['vet_field_quantity']; $i++ ) {
            // Register a new field in the "vet_field_currency_X" section, inside the "vet" page.
            add_settings_field(
                "vet_field_currency_$i", // As of WP 4.6 this value is used only internally.
                                        // Use $args' label_for to populate the id inside the callback.
                sprintf($currency_text, $i),
                [ $this, 'field_select_currency' ],
                'vet',
                'vet_config',
                array(
                    'label_for'         => "vet_field_currency_$i",
                    'class'             => 'vet_row',
                    'vet_custom_data'   => 'custom',
                    'description'       => sprintf($currency_desc, $i),
                    'value'             => $options["vet_field_currency_$i"],
                )
            );
        }

        $rate_desc =  esc_html__( 'Set the value for this currency.', 'viderlab-exchange-table' );
        for( $i = 1; $i <= $options['vet_field_quantity']; $i++ ) {
            // If currency is not defined.
            if($options["vet_field_currency_$i"] == "") continue;

            // Register a new field in the "vet_field_rate_input_X" section, inside the "vet" page.
            add_settings_field(
                "vet_field_rate_input_$i", // As of WP 4.6 this value is used only internally.
                                        // Use $args' label_for to populate the id inside the callback.
                $this->currencies[$options["vet_field_currency_$i"]]." (".$options["vet_field_currency_$i"].")",
                [ $this, 'field_rate_input' ],
                'vet',
                'vet_exchange_rates',
                array(
                    'label_for'         => "vet_field_rate_input_$i",
                    'class'             => 'vet_row',
                    'vet_custom_data'   => 'custom',
                    'description'       => $rate_desc,
                    'value'             => $options["vet_field_rate_input_$i"],
                )
            );
        }

        // Register a new field in the "vet_field_quantity" section, inside the "vet" page.
        add_settings_field(
            'vet_field_validity_start', // As of WP 4.6 this value is used only internally.
                                    // Use $args' label_for to populate the id inside the callback.
            __( 'Start date', 'viderlab-exchange-table' ),
            [ $this, 'field_validity_date' ],
            'vet',
            'vet_validity',
            array(
                'label_for'         => 'vet_field_validity_start',
                'class'             => 'vet_row',
                'vet_custom_data'   => 'custom',
            )
        );

        // Register a new field in the "vet_field_quantity" section, inside the "vet" page.
        add_settings_field(
            'vet_field_validity_end', // As of WP 4.6 this value is used only internally.
                                    // Use $args' label_for to populate the id inside the callback.
            __( 'End date', 'viderlab-exchange-table' ),
            [ $this, 'field_validity_date' ],
            'vet',
            'vet_validity',
            array(
                'label_for'         => 'vet_field_validity_end',
                'class'             => 'vet_row',
                'vet_custom_data'   => 'custom',
            )
        );
    }

    /**
     * Exchane Rates section callback function.
     *
     * @param array $args  The settings array, defining title, id, callback.
	 *
	 * @since    1.0.0
     */
    public function exchange_rates( $args ) {
        ?>
        <p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Define manually the exchange rates.', 'viderlab-exchange-table' ); ?></p>
        <?php
    }

    /**
     * Validity section callback function.
     *
     * @param array $args  The settings array, defining title, id, callback.
	 *
	 * @since    1.0.0
     */
    public function validity( $args ) {
        ?>
        <p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Define the validity of the exchange rates.', 'viderlab-exchange-table' ); ?></p>
        <?php
    }

    /**
     * Configutation section callback function.
     *
     * @param array $args  The settings array, defining title, id, callback.
	 *
	 * @since    1.0.0
     */
    public function configuration( $args ) {
        ?>
        <p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Configuration of the exchange rates table.', 'viderlab-exchange-table' ); ?></p>
        <?php
    }

    /**
     * Field callback function.
     *
     * WordPress has magic interaction with the following keys: label_for, class.
     * - the "label_for" key value is used for the "for" attribute of the <label>.
     * - the "class" key value is used for the "class" attribute of the <tr> containing the field.
     * Note: you can add custom key value pairs to be used inside your callbacks.
     *
     * @param array $args
	 *
	 * @since    1.0.0
     */
    public function field_select_currency( $args ) {
        // Get the value of the setting we've registered with register_setting()
        $options = get_option( 'vet_options' );
        ?>
        <select 
                id="<?php echo esc_attr( $args['label_for'] ); ?>"
                data-custom="<?php echo esc_attr( $args['vet_custom_data'] ); ?>"
                name="vet_options[<?php echo esc_attr( $args['label_for'] ); ?>]">
                <?php $this->currency_options( $args['value'] ); ?>
        </select>
        <p class="description">
            <?php echo $args['description']; ?>
        </p>
        <?php
    }

    /**
     * Field callback function.
     *
     * WordPress has magic interaction with the following keys: label_for, class.
     * - the "label_for" key value is used for the "for" attribute of the <label>.
     * - the "class" key value is used for the "class" attribute of the <tr> containing the field.
     * Note: you can add custom key value pairs to be used inside your callbacks.
     *
     * @param array $args
	 *
	 * @since    1.0.0
     */
    public function field_checkbox( $args ) {
        // Get the value of the setting we've registered with register_setting()
        $options = get_option( 'vet_options' );
        ?>
        <label for="<?php echo esc_attr( $args['label_for'] ); ?>">
            <input type="checkbox" 
                id="<?php echo esc_attr( $args['label_for'] ); ?>"
                data-custom="<?php echo esc_attr( $args['vet_custom_data'] ); ?>"
                name="vet_options[<?php echo esc_attr( $args['label_for'] ); ?>]"
                value="on"
                <?php checked($options[$args['label_for']], "on"); ?>>
            <?php echo $args['checkbox_text']; ?>    
        </label>
        <p class="description">
            <?php echo $args['description']; ?>  
        </p>
        <?php
    }

    /**
     * Field callback function.
     *
     * WordPress has magic interaction with the following keys: label_for, class.
     * - the "label_for" key value is used for the "for" attribute of the <label>.
     * - the "class" key value is used for the "class" attribute of the <tr> containing the field.
     * Note: you can add custom key value pairs to be used inside your callbacks.
     *
     * @param array $args
	 *
	 * @since    1.0.0
     */
    public function field_radio( $args ) {
        // Get the value of the setting we've registered with register_setting()
        $options = get_option( 'vet_options' );

        foreach($args['radio_values'] as $i => $radio_value):
            ?>
            <label for="<?php echo esc_attr( $args['label_for'] ); ?>">
                <input type="radio" 
                    id="<?php echo esc_attr( $args['label_for'] ); ?>"
                    data-custom="<?php echo esc_attr( $args['vet_custom_data'] ); ?>"
                    name="vet_options[<?php echo esc_attr( $args['label_for'] ); ?>]"
                    value="<?php echo $radio_value; ?>"
                    <?php checked($options[$args['label_for']], $radio_value); ?>>
                <?php echo $args['radio_text'][$i]; ?>    
            </label>
            <?php
        endforeach;
        ?>
        <p class="description">
            <?php echo $args['description']; ?>  
        </p>
        <?php
    }

    /**
     * Field callback function.
     *
     * WordPress has magic interaction with the following keys: label_for, class.
     * - the "label_for" key value is used for the "for" attribute of the <label>.
     * - the "class" key value is used for the "class" attribute of the <tr> containing the field.
     * Note: you can add custom key value pairs to be used inside your callbacks.
     *
     * @param array $args
	 *
	 * @since    1.0.0
     */
    public function field_quantity( $args ) {
        // Get the value of the setting we've registered with register_setting()
        $options = get_option( 'vet_options' );
        ?>
            <input type="number" 
                min="1"
                id="<?php echo esc_attr( $args['label_for'] ); ?>"
                data-custom="<?php echo esc_attr( $args['vet_custom_data'] ); ?>"
                name="vet_options[<?php echo esc_attr( $args['label_for'] ); ?>]"
                value=<?php echo $options[$args['label_for']]; ?>>
        <p class="description">
            <?php esc_html_e( 'Define the number of currencies that will appear on the table.', 'viderlab-exchange-table' ); ?>
        </p>
        <?php
    }

    /**
     * Field callback function.
     *
     * WordPress has magic interaction with the following keys: label_for, class.
     * - the "label_for" key value is used for the "for" attribute of the <label>.
     * - the "class" key value is used for the "class" attribute of the <tr> containing the field.
     * Note: you can add custom key value pairs to be used inside your callbacks.
     *
     * @param array $args
	 *
	 * @since    1.0.0
     */
    public function field_rate_input( $args ) {
        // Get the value of the setting we've registered with register_setting()
        $options = get_option( 'vet_options' );
        ?>
            <input type="number" 
                min="0"
                step="0.0001"
                id="<?php echo esc_attr( $args['label_for'] ); ?>"
                data-custom="<?php echo esc_attr( $args['vet_custom_data'] ); ?>"
                name="vet_options[<?php echo esc_attr( $args['label_for'] ); ?>]"
                value=<?php echo $options[$args['label_for']]; ?>>
        <p class="description">
            <?php echo $args['description']; ?>
        </p>
        <?php
    }

    /**
     * Field callback function.
     *
     * WordPress has magic interaction with the following keys: label_for, class.
     * - the "label_for" key value is used for the "for" attribute of the <label>.
     * - the "class" key value is used for the "class" attribute of the <tr> containing the field.
     * Note: you can add custom key value pairs to be used inside your callbacks.
     *
     * @param array $args
	 *
	 * @since    1.0.0
     */
    public function field_validity_date( $args ) {
        // Get the value of the setting we've registered with register_setting()
        $options = get_option( 'vet_options' );
        ?>
            <input type="date" 
                id="<?php echo esc_attr( $args['label_for'] ); ?>"
                data-custom="<?php echo esc_attr( $args['vet_custom_data'] ); ?>"
                name="vet_options[<?php echo esc_attr( $args['label_for'] ); ?>]"
                value=<?php echo $options[$args['label_for']]; ?>>
        <p class="description">
            <?php esc_html_e( 'Define the start date of the validation range.', 'viderlab-exchange-table' ); ?>
        </p>
        <?php
    }

    /**
     * Populates a HTML select field with currencies option values.
     *
     * @param string $selected_currency  The actual selected value.
	 *
	 * @since    1.0.0
     */
    private function currency_options($selected_currency = null) {
        ?>
        <option value="" <?php selected( $selected_currency, $code ); ?>>
        <?php _e('-- Select a currency --', 'viderlab-exchange-table'); ?> 
        </option>
        <?php
        foreach($this->currencies as $code => $currency) {
            ?>
            <option value="<?php echo $code; ?>" <?php selected( $selected_currency, $code ); ?>>
                <?php echo $currency." (".$code.")"; ?>
            </option>
            <?php
        }
    }

    /**
     * Returns an array with all the available currencies 
     * on the format [currency_code] => currency_name.
     *
	 * @since    1.0.0
     */
    private static function currencies() {
        return array(
            'AED' => __( 'UAE Dirham', 'viderlab-exchange-table'),
            'AFN' => __( 'Afghani', 'viderlab-exchange-table'),
            'ALL' => __( 'Lek', 'viderlab-exchange-table'),
            'AMD' => __( 'Armenian Dram', 'viderlab-exchange-table'),
            'ANG' => __( 'Netherlands Antillian Guilder', 'viderlab-exchange-table'),
            'AOA' => __( 'Kwanza', 'viderlab-exchange-table'),
            'ARS' => __( 'Argentine Peso', 'viderlab-exchange-table'),
            'AUD' => __( 'Australian Dollar', 'viderlab-exchange-table'),
            'AWG' => __( 'Aruban Guilder', 'viderlab-exchange-table'),
            'AZN' => __( 'Azerbaijanian Manat', 'viderlab-exchange-table'),
            'BAM' => __( 'Convertible Marks', 'viderlab-exchange-table'),
            'BBD' => __( 'Barbados Dollar', 'viderlab-exchange-table'),
            'BDT' => __( 'Taka', 'viderlab-exchange-table'),
            'BGN' => __( 'Bulgarian Lev', 'viderlab-exchange-table'),
            'BHD' => __( 'Bahraini Dinar', 'viderlab-exchange-table'),
            'BIF' => __( 'Burundi Franc', 'viderlab-exchange-table'),
            'BMD' => __( 'Bermudian Dollar', 'viderlab-exchange-table'),
            'BND' => __( 'Brunei Dollar', 'viderlab-exchange-table'),
            'BOB' => __( 'Boliviano', 'viderlab-exchange-table'),
            'BRL' => __( 'Brazilian Real', 'viderlab-exchange-table'),
            'BSD' => __( 'Bahamian Dollar', 'viderlab-exchange-table'),
            'BTN' => __( 'Ngultrum', 'viderlab-exchange-table'),
            'BWP' => __( 'Pula', 'viderlab-exchange-table'),
            'BYR' => __( 'Belarussian Ruble', 'viderlab-exchange-table'),
            'BZD' => __( 'Belize Dollar', 'viderlab-exchange-table'),
            'CAD' => __( 'Canadian Dollar', 'viderlab-exchange-table'),
            'CDF' => __( 'Congolese Franc', 'viderlab-exchange-table'),
            'CHF' => __( 'Swiss Franc', 'viderlab-exchange-table'),
            'CLP' => __( 'Chilean Peso', 'viderlab-exchange-table'),
            'CNY' => __( 'Yuan Renminbi', 'viderlab-exchange-table'),
            'COP' => __( 'Colombian Peso', 'viderlab-exchange-table'),
            'CRC' => __( 'Costa Rican Colon', 'viderlab-exchange-table'),
            'CUP' => __( 'Cuban Peso', 'viderlab-exchange-table'),
            'CVE' => __( 'Cape Verde Escudo', 'viderlab-exchange-table'),
            'CZK' => __( 'Czech Koruna', 'viderlab-exchange-table'),
            'DJF' => __( 'Djibouti Franc', 'viderlab-exchange-table'),
            'DKK' => __( 'Danish Krone', 'viderlab-exchange-table'),
            'DOP' => __( 'Dominican Peso', 'viderlab-exchange-table'),
            'DZD' => __( 'Algerian Dinar', 'viderlab-exchange-table'),
            'EEK' => __( 'Kroon', 'viderlab-exchange-table'),
            'EGP' => __( 'Egyptian Pound', 'viderlab-exchange-table'),
            'ERN' => __( 'Nakfa', 'viderlab-exchange-table'),
            'ETB' => __( 'Ethiopian Birr', 'viderlab-exchange-table'),
            'EUR' => __( 'Euro', 'viderlab-exchange-table'),
            'FJD' => __( 'Fiji Dollar', 'viderlab-exchange-table'),
            'FKP' => __( 'Falkland Islands Pound', 'viderlab-exchange-table'),
            'GBP' => __( 'Pound Sterling', 'viderlab-exchange-table'),
            'GEL' => __( 'Lari', 'viderlab-exchange-table'),
            'GHS' => __( 'Cedi', 'viderlab-exchange-table'),
            'GIP' => __( 'Gibraltar Pound', 'viderlab-exchange-table'),
            'GMD' => __( 'Dalasi', 'viderlab-exchange-table'),
            'GNF' => __( 'Guinea Franc', 'viderlab-exchange-table'),
            'GTQ' => __( 'Quetzal', 'viderlab-exchange-table'),
            'GYD' => __( 'Guyana Dollar', 'viderlab-exchange-table'),
            'HKD' => __( 'Hong Kong Dollar', 'viderlab-exchange-table'),
            'HNL' => __( 'Lempira', 'viderlab-exchange-table'),
            'HRK' => __( 'Croatian Kuna', 'viderlab-exchange-table'),
            'HTG' => __( 'Gourde', 'viderlab-exchange-table'),
            'HUF' => __( 'Forint', 'viderlab-exchange-table'),
            'IDR' => __( 'Rupiah', 'viderlab-exchange-table'),
            'ILS' => __( 'New Israeli Sheqel', 'viderlab-exchange-table'),
            'INR' => __( 'Indian Rupee', 'viderlab-exchange-table'),
            'IQD' => __( 'Iraqi Dinar', 'viderlab-exchange-table'),
            'IRR' => __( 'Iranian Rial', 'viderlab-exchange-table'),
            'ISK' => __( 'Iceland Krona', 'viderlab-exchange-table'),
            'JMD' => __( 'Jamaican Dollar', 'viderlab-exchange-table'),
            'JOD' => __( 'Jordanian Dinar', 'viderlab-exchange-table'),
            'JPY' => __( 'Yen', 'viderlab-exchange-table'),
            'KES' => __( 'Kenyan Shilling', 'viderlab-exchange-table'),
            'KGS' => __( 'Som', 'viderlab-exchange-table'),
            'KHR' => __( 'Riel', 'viderlab-exchange-table'),
            'KMF' => __( 'Comoro Franc', 'viderlab-exchange-table'),
            'KPW' => __( 'North Korean Won', 'viderlab-exchange-table'),
            'KRW' => __( 'Won', 'viderlab-exchange-table'),
            'KWD' => __( 'Kuwaiti Dinar', 'viderlab-exchange-table'),
            'KYD' => __( 'Cayman Islands Dollar', 'viderlab-exchange-table'),
            'KZT' => __( 'Tenge', 'viderlab-exchange-table'),
            'LAK' => __( 'Kip', 'viderlab-exchange-table'),
            'LBP' => __( 'Lebanese Pound', 'viderlab-exchange-table'),
            'LKR' => __( 'Sri Lanka Rupee', 'viderlab-exchange-table'),
            'LRD' => __( 'Liberian Dollar', 'viderlab-exchange-table'),
            'LSL' => __( 'Loti', 'viderlab-exchange-table'),
            'LTL' => __( 'Lithuanian Litas', 'viderlab-exchange-table'),
            'LVL' => __( 'Latvian Lats', 'viderlab-exchange-table'),
            'LYD' => __( 'Libyan Dinar', 'viderlab-exchange-table'),
            'MAD' => __( 'Moroccan Dirham', 'viderlab-exchange-table'),
            'MDL' => __( 'Moldovan Leu', 'viderlab-exchange-table'),
            'MGA' => __( 'Malagasy Ariary', 'viderlab-exchange-table'),
            'MKD' => __( 'Denar', 'viderlab-exchange-table'),
            'MMK' => __( 'Kyat', 'viderlab-exchange-table'),
            'MNT' => __( 'Tugrik', 'viderlab-exchange-table'),
            'MOP' => __( 'Pataca', 'viderlab-exchange-table'),
            'MRO' => __( 'Ouguiya', 'viderlab-exchange-table'),
            'MUR' => __( 'Mauritius Rupee', 'viderlab-exchange-table'),
            'MVR' => __( 'Rufiyaa', 'viderlab-exchange-table'),
            'MWK' => __( 'Kwacha', 'viderlab-exchange-table'),
            'MXN' => __( 'Mexican Peso', 'viderlab-exchange-table'),
            'MYR' => __( 'Malaysian Ringgit', 'viderlab-exchange-table'),
            'MZN' => __( 'Metical', 'viderlab-exchange-table'),
            'NAD' => __( 'Namibia Dollar', 'viderlab-exchange-table'),
            'NGN' => __( 'Naira', 'viderlab-exchange-table'),
            'NIO' => __( 'Cordoba Oro', 'viderlab-exchange-table'),
            'NOK' => __( 'Norwegian Krone', 'viderlab-exchange-table'),
            'NPR' => __( 'Nepalese Rupee', 'viderlab-exchange-table'),
            'NZD' => __( 'New Zealand Dollar', 'viderlab-exchange-table'),
            'OMR' => __( 'Rial Omani', 'viderlab-exchange-table'),
            'PAB' => __( 'Balboa', 'viderlab-exchange-table'),
            'PEN' => __( 'Nuevo Sol', 'viderlab-exchange-table'),
            'PGK' => __( 'Kina', 'viderlab-exchange-table'),
            'PHP' => __( 'Philippine Peso', 'viderlab-exchange-table'),
            'PKR' => __( 'Pakistan Rupee', 'viderlab-exchange-table'),
            'PLN' => __( 'Zloty', 'viderlab-exchange-table'),
            'PYG' => __( 'Guarani', 'viderlab-exchange-table'),
            'QAR' => __( 'Qatari Rial', 'viderlab-exchange-table'),
            'RON' => __( 'New Leu', 'viderlab-exchange-table'),
            'RSD' => __( 'Serbian Dinar', 'viderlab-exchange-table'),
            'RUB' => __( 'Russian Ruble', 'viderlab-exchange-table'),
            'RWF' => __( 'Rwanda Franc', 'viderlab-exchange-table'),
            'SAR' => __( 'Saudi Riyal', 'viderlab-exchange-table'),
            'SBD' => __( 'Solomon Islands Dollar', 'viderlab-exchange-table'),
            'SCR' => __( 'Seychelles Rupee', 'viderlab-exchange-table'),
            'SDG' => __( 'Sudanese Pound', 'viderlab-exchange-table'),
            'SEK' => __( 'Swedish Krona', 'viderlab-exchange-table'),
            'SGD' => __( 'Singapore Dollar', 'viderlab-exchange-table'),
            'SHP' => __( 'Saint Helena Pound', 'viderlab-exchange-table'),
            'SLL' => __( 'Leone', 'viderlab-exchange-table'),
            'SOS' => __( 'Somali Shilling', 'viderlab-exchange-table'),
            'SRD' => __( 'Surinam Dollar', 'viderlab-exchange-table'),
            'STD' => __( 'Dobra', 'viderlab-exchange-table'),
            'SVC' => __( 'El Salvador Colon', 'viderlab-exchange-table'),
            'SYP' => __( 'Syrian Pound', 'viderlab-exchange-table'),
            'SZL' => __( 'Lilangeni', 'viderlab-exchange-table'),
            'THB' => __( 'Baht', 'viderlab-exchange-table'),
            'TJS' => __( 'Somoni', 'viderlab-exchange-table'),
            'TND' => __( 'Tunisian Dinar', 'viderlab-exchange-table'),
            'TOP' => __( 'Pa\'anga', 'viderlab-exchange-table'),
            'TRY' => __( 'Turkish Lira', 'viderlab-exchange-table'),
            'TTD' => __( 'Trinidad and Tobago Dollar', 'viderlab-exchange-table'),
            'TWD' => __( 'New Taiwan Dollar', 'viderlab-exchange-table'),
            'TZS' => __( 'Tanzanian Shilling', 'viderlab-exchange-table'),
            'UAH' => __( 'Hryvnia', 'viderlab-exchange-table'),
            'UGX' => __( 'Uganda Shilling', 'viderlab-exchange-table'),
            'USD' => __( 'US Dollar', 'viderlab-exchange-table'),
            'UYU' => __( 'Peso Uruguayo', 'viderlab-exchange-table'),
            'UZS' => __( 'Uzbekistan Sum', 'viderlab-exchange-table'),
            'VEF' => __( 'Bolivar Fuerte', 'viderlab-exchange-table'),
            'VES' => __( 'Bolivar Soberano', 'viderlab-exchange-table'),
            'VND' => __( 'Dong', 'viderlab-exchange-table'),
            'VUV' => __( 'Vatu', 'viderlab-exchange-table'),
            'WST' => __( 'Tala', 'viderlab-exchange-table'),
            'XAF' => __( 'CFA Franc BEAC', 'viderlab-exchange-table'),
            'XCD' => __( 'East Caribbean Dollar', 'viderlab-exchange-table'),
            'XOF' => __( 'CFA Franc BCEAO', 'viderlab-exchange-table'),
            'XPF' => __( 'CFP Franc', 'viderlab-exchange-table'),
            'YER' => __( 'Yemeni Rial', 'viderlab-exchange-table'),
            'ZAR' => __( 'Rand', 'viderlab-exchange-table'),
            'ZMK' => __( 'Zambian Kwacha', 'viderlab-exchange-table'),
            'ZWD' => __( 'Zimbabwe Dollar', 'viderlab-exchange-table'),
        );
    }
}
