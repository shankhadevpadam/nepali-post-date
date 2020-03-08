<?php

/**
 * Main class for WP-Admin hooks
 *
 * This class loads all of our backend hooks and sets up admin interfaces
 *
 * @subpackage Admin interfaces
 * @author Padam Shankhadev
 * @since 1.0
 * @var opts - plugin options
 */
class Nepali_Post_Date_Admin
{

    private $version;
    private $opts;

    /**
     *  Nepali Post Date Admin constructor
     *
     * Constructor first checks for the plugin version, and if this is the first activation, plugin adds version info in the DB with autoload option set to false.
     * That way we can easily update across versions, if we decide to add options to the plugin, or change plugin settings and defaults
     *
     * @return void
     * @author Padam Shankhadev
     * @since 1.0
     */
    public function __construct()
    {

        $default_opts = array(
            'active' => array('date' => true, 'time' => true),
            'date_format' => 'd m y, l',
            'custom_date_format' => ''
        );

        $default_opts = apply_filters('npd_modify_default_opts', $default_opts);

        $this->opts = get_option('npd_opts', $default_opts);

        add_action('admin_init', array($this, 'register_settings'));
        add_filter('plugin_action_links_' . NEPALIPOSTDATE_BASENAME, array(&$this, 'add_settings_link'));

    }

    /**
     * Function that is hooked into the admin initialisation and registers settings
     *
     * @return void
     * @author Padam Shankhadev
     * @since 1.0
     */
    public function register_settings()
    {

        add_settings_section( 'npd_opts', __( 'Nepali Post Date Options', 'npdate'), array(&$this, 'settings_section_info' ), 'general' );

        add_settings_field( 'npd_opts[active]', __( 'Apply Nepali Date format to', 'npdate'), array(&$this, 'active_callback' ), 'general', 'npd_opts', $this->opts['active'] );
        add_settings_field( 'npd_opts[date_format]', __( 'Apply Date Format', 'npdate' ), array(&$this, 'date_format_callback' ), 'general', 'npd_opts', $this->opts['date_format'] );
        add_settings_field( 'npd_opts[custom_date_format]', __( 'Custom Date Format', 'npdate' ), array(&$this, 'custom_date_format_callback' ), 'general', 'npd_opts', $this->opts['custom_date_format'] );

        register_setting( 'general', 'npd_opts', array(&$this, 'sanitize_opts' ) );
    }

    /**
     * Function that displays the section heading information
     *
     * @author Padam Shankhadev
     * @since 1.0
     */
    public function settings_section_info()
    {
        echo '<div id="nepali-post-date"></div>';
    }

    /**
     * Function that displays the plugin activation checkbox
     *
     * @author Padam Shankhadev
     * @since 1.0
     */
    public function active_callback( $active )
    {
        $checked_time = checked( $active['time'], 1, false );
        $checked_date = checked( $active['date'], 1, false );

        echo "<input type=\"checkbox\" name=\"npd_opts[active][date]\" ${checked_date}>" . __( 'Date', 'npdate' ) . '&nbsp;&nbsp;&nbsp;';
        echo "<input type=\"checkbox\" name=\"npd_opts[active][time]\" ${checked_time}>" . __( 'Time', 'npdate' );
    }

    /**
     * Function that displays the string dateformat select box
     *
     * @author Padam Shankhadev
     * @since 1.0
     */
    public function date_format_callback($date_format)
    {

        $date_formats = apply_filters( 'npd_date_format_opts', array('d m y, l' => __('१ बैशाख २०७३, बुधबार', 'nepali-post-date' ), 'y, d m l' => __('२०७३, १ बैशाख बुधबार', 'nepali-post-date' ) ) );

        $html = '<select name="npd_opts[date_format]">';

        foreach ( $date_formats as $value => $label ) {

            $selected = selected( $date_format, $value, false );
            $html .= '<option value="' . $value . '" ' . $selected . '>' . $label . '</option>';

        }

        $html .= '</select>';

        echo $html;
    }

    /**
     * Display custom date setting
     *
     * @since 1.1
     */

    public function custom_date_format_callback( $custom_date_format )
    {

        echo '<input type="text" name="npd_opts[custom_date_format]" value="' . esc_attr( $custom_date_format ) . '"/> (eg: d m y, l)';
    }

    /**
     * Function that sanitizes plugin options on save
     *
     * @author Padam Shankhadev
     * @since 1.0
     * @param array $opts Nepali Post Date options
     * @return array $opts Sanitized options array
     *
     */
    public function sanitize_opts( $opts )
    {

        if ( isset( $opts['active']['date'] ) ) {
            $opts['active']['date'] = true;
        } else {
            $opts['active']['date'] = false;
        }

        if ( isset( $opts['active']['time'] ) ) {
            $opts['active']['time'] = true;
        } else {
            $opts['active']['time'] = false;
        }

        $opts['custom_date_format'] = esc_html( $opts['custom_date_format'] );

        return $opts;
    }

    public function add_settings_link( $links )
    {
        $admin_url = admin_url();
        $link = array( '<a href="' . $admin_url . 'options-general.php#nepali-post-date">Settings</a>' );

        return array_merge( $links, $link );
    }
}

?>