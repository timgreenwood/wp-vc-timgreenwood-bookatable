<?php
/*
Plugin Name: VC - Bookatable widget
Plugin URI: https://www.timgreenwood.co.uk
Description: Use this widget for Visual Composer to get the Bookatable Booking Tool on your website.
Version: 1.0
Author: Tim Greenwood
Author URI: https://www.timgreenwood.co.uk
License: GPLv2 or later
*/

// don't load directly
if (!defined('ABSPATH')) die('-1');

class VCTimgreenwoodBookatableAddon {
    function __construct() {
        // We safely integrate with VC with this hook
        add_action( 'init', array( $this, 'integrateWithVC' ) );
 
        add_shortcode( 'tg_bookatable', array( $this, 'renderTimgreenwoodBookatable' ) );

        add_action( 'wp_enqueue_scripts', array( $this, 'loadTimgreenwoodBookatableCssAndJs' ) );
    }
 
    public function integrateWithVC() {
        // Check if Visual Composer is installed
        if ( ! defined( 'WPB_VC_VERSION' ) ) {
            // Display notice that Visual Compser is required
            add_action('admin_notices', array( $this, 'showVcVersionNotice' ));
            return;
        }

        vc_map( array(
            "name" => __("Book a Table widget", 'vc_extend'),
            "description" => __("Book A Table widget with toggle button", 'vc_extend'),
            "base" => "tg_bookatable",
            "class" => "",
            "controls" => "full",
            "icon" => plugins_url('assets/timgreenwood.png', __FILE__),
            "category" => __('Content', 'js_composer'),
            "params" => array(
              array(
                  "type" => "textfield",
                  "holder" => "div",
                  "class" => "",
                  "heading" => __("Book A Table Code", 'vc_extend'),
                  "param_name" => "content",
                  "value" => __("", 'vc_extend'),
                  "description" => __("This is the bookatable code.", 'vc_extend')
              ),
              array(
                  "type" => "textfield",
                  "holder" => "div",
                  "class" => "",
                  "heading" => __("Custom button text", 'vc_extend'),
                  "param_name" => "btn_txt",
                  "value" => 'Check availability', //Default button text
                  "description" => __("If you want custom text in the button", 'vc_extend')
              ),
              array(
                  "type" => "dropdown",
                  "holder" => "div",
                  "class" => "",
                  "heading" => __("Auto hide button?", 'vc_extend'),
                  "param_name" => "btn_hide",
                  "value" => ['auto-hide', 'always-show'],
                  "description" => __("Allows us to hide the button after it has been clicked, default is to hide on click", 'vc_extend')
              ),
            )
        ) );
    }
    
    /*
    Shortcode logic how it should be rendered
    */
    public function renderTimgreenwoodBookatable( $atts, $content = null ) {

      if ($content == null) return;
      
      extract( shortcode_atts( array(
        'btn_txt' => 'Check availability',
        'btn_hide' => 'auto-hide'
      ), $atts ) );

      if (isset($_POST['vc_inline']) && $_POST['vc_inline']) {
        // we are in edit mode so dont really do the script thing as it breaks VC!
        $output = '<script>function bookATable(connectionid){}</script>';
      } else {
        $output = '
                  <script type="text/javascript" src="https://bda.bookatable.com/deploy/lbui.direct.min.js"></script>
                  <script>
                  function bookATable(connectionid){
                    LBDirect_Embed({
                        connectionid  :  connectionid
                    });
                  }
                  </script>
                  ';
      }
      $output .= "
          <div class='tg_bookatable_toggle'>
            <a class='center button toggle-btn {$btn_hide}' href='#'>{$btn_txt}</a>
            <div class='toggle-widget'>
              <script>bookATable('{$content}');</script>
            </div>
          </div>
        ";

      return $output;
    }

    /*
    Load plugin css and javascript files
    */
    public function loadTimgreenwoodBookatableCssAndJs() {
      wp_register_style( 'tg_extend_style', plugins_url('assets/tg_bookatable.css', __FILE__) );
      wp_enqueue_style( 'tg_extend_style' );

      wp_enqueue_script( 'tg_extend_js', plugins_url('assets/tg_bookatable.js', __FILE__), array('jquery') );
    }

    /*
    Show notice if your plugin is activated but Visual Composer is not
    */
    public function showVcVersionNotice() {
        $plugin_data = get_plugin_data(__FILE__);
        echo '
        <div class="updated">
          <p>'.sprintf(__('<strong>%s</strong> requires <strong><a href="http://bit.ly/vcomposer" target="_blank">Visual Composer</a></strong> plugin to be installed and activated on your site.', 'vc_extend'), $plugin_data['Name']).'</p>
        </div>';
    }
}
// Initialize code
new VCTimgreenwoodBookatableAddon();