<?php

/**
 * Main Nepali Post Date plugin class.
 *
 * This class loads plugin options, sets filters and converts the date on selected hooks.
 *
 * @subpackage Frontend interfaces
 * @author Padam Shankhadev
 * @since 1.0
 * @var opts - plugin options
 */
class Nepali_Post_Date_Frontend
{

    private $opts;

    /**
     * Class Constructor
     *
     * Loads default options, sets default filter list and adds convert_date filter to selected locations
     *
     * @author Padam Shankhadev
     * @since 1.0
     */
    public function __construct()
    {

        $default_opts = array(
            'active' => array( 'date' => true, 'time' => true ),
            'date_format' => 'd m y, l',
            'custom_date_format' => ''
        );

        $default_opts = apply_filters( 'npd_modify_default_opts', $default_opts );

        $this->opts = get_option( 'npd_opts', $default_opts );

        $filter_list = array();

        if ($this->opts['active']['date']):
            $filter_list = array_merge( $filter_list, array( 'the_date', 'get_the_date' ) );
        endif;

        if ($this->opts['active']['time']) :
            $filter_list = array_merge( $filter_list, array( 'get_the_time', 'the_time' ) );
        endif;


        /**
         * Filter the list of applicable filter locations
         *
         * @since 1.0
         * @param array $filter_list List of filters for time appearance change
         *
         */
        $filters = apply_filters(
            'npd_filters',
            $filter_list
        );

        foreach ( $filters as $filter ) :

            add_filter( $filter, array( &$this, 'convert_date' ), 10, 1);

        endforeach;

        add_shortcode( 'nepali_post_date', array( &$this, 'nepali_post_date_shortcode') );
    }


    /**
     * Main plugin function which does the date conversion.
     *
     * @param string $orig_time Original time / date string
     * @author Padam Shankhadev
     * @since 1.0
     */

    public function convert_date( $orig_time )
    {
        global $post;
        $converted_date = '';
        $post_date = strtotime( $post->post_date );

        $date = new Nepali_Date();
        $nepali_calender = $date->eng_to_nep( date( 'Y', $post_date ), date( 'm', $post_date ), date( 'd', $post_date ) );
        $nepali_year = $date->convert_to_nepali_number( $nepali_calender['year'] );
        $nepali_month = $nepali_calender['nmonth'];
        $nepali_day = $nepali_calender['day'];
        $nepali_date = $date->convert_to_nepali_number( $nepali_calender['date'] );
        $nepali_hour = $date->convert_to_nepali_number( date( 'H', $post_date ) );
        $nepali_minute = $date->convert_to_nepali_number( date( 'i', $post_date ) );


        //If option not set as active return original string.
        if (!$this->opts['active']) {
            return $orig_time;
        }

        if ($this->opts['custom_date_format']) {
            $format = $this->opts['custom_date_format'];
        } else {
            $format = $this->opts['date_format'];
        }

        $converted_date = str_replace(array('l', 'd', 'm', 'y'), array($nepali_day, $nepali_date, $nepali_month, $nepali_year), $format);
        if ($this->opts['active']['time']) {
            $converted_date .= ' ' . $nepali_hour . ':' . $nepali_minute;
        }

        return $converted_date;

    }

    public function nepali_post_date_shortcode( $attrs = array() )
    {

        extract( shortcode_atts( array(
            'post_date' => time(),
        ), $attrs) );

        $post_date = strtotime( $post_date );
        $date = new Nepali_Date();
        $nepali_calender = $date->eng_to_nep( date( 'Y', $post_date ), date( 'm', $post_date ), date( 'd', $post_date ) );
        $nepali_year = $date->convert_to_nepali_number( $nepali_calender['year'] );
        $nepali_month = $nepali_calender['nmonth'];
        $nepali_day = $nepali_calender['day'];
        $nepali_date = $date->convert_to_nepali_number( $nepali_calender['date'] );
        $nepali_hour = $date->convert_to_nepali_number( date( 'H', $post_date ));
        $nepali_minute = $date->convert_to_nepali_number( date( 'i', $post_date ) );

        if ( $this->opts['custom_date_format'] ) {
            $format = $this->opts['custom_date_format'];
        } else {
            $format = $this->opts['date_format'];
        }

        $converted_date = str_replace( array( 'l', 'd', 'm', 'y' ), array( $nepali_day, $nepali_date, $nepali_month, $nepali_year ), $format );
        if ( $this->opts['active']['time'] ) {
            $converted_date .= ' ' . $nepali_hour . ':' . $nepali_minute;
        }

        return $converted_date;
    }

}

?>