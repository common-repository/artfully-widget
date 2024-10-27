<?php
/*
 * Plugin Name: Artfully Widget
 * Description: Adds an easy-to-use shortcode for embedding Artful.ly widgets for taking donations and selling tickets on your WordPress site.
 * Version: 1.1
 */
?>
<?php
define('NME_PLUGIN_URL', WP_PLUGIN_URL . '/' . dirname(plugin_basename(__FILE__)));

function nme_add_scripts() {
    wp_enqueue_script('artfully.js', 'https://artfully-production.s3.amazonaws.com/assets/artfully-v3.js', array('jquery'));
    wp_enqueue_style('artfully.css', 'https://artfully-production.s3.amazonaws.com/assets/themes/default.css');
    wp_enqueue_style('artfully-plugin.css', plugins_url('artfully-widget/css/artful_style.css'));
}

add_action('wp_head', 'nme_add_scripts', 1);

add_shortcode('art-event', 'art_event_data');

function art_event_data($atts) {
    extract(shortcode_atts(array(
                'id' => '21',
                    ), $atts));
    wp_enqueue_script( 'artfully_event', NME_PLUGIN_URL.'/js/artfully-event.js', false, false, true);
    wp_localize_script( 'artfully_event', 'artfully_event', array('eventId' => $id) );
    return '<div id="artfully-event"></div>';
}

add_shortcode('art-donation', 'art_donation_data');

function art_donation_data($atts) {
    extract(shortcode_atts(array(
                'id' => '21',
                    ), $atts));
    wp_enqueue_script( 'artfully_donation', NME_PLUGIN_URL.'/js/artfully-donation.js', false, false, true);
    wp_localize_script( 'artfully_donation', 'artfully_donation', array('donationId' => $id) );
    return '<div id="donation"></div>';
}

add_action('admin_enqueue_scripts', 'nme_load_admin_script');
function nme_load_admin_script() {
    wp_register_script('artful_setting_js', NME_PLUGIN_URL . '/js/artful-setting.js', array('jquery'));
    wp_enqueue_script('artful_setting_js');
    wp_localize_script('artful_setting_js','plugin',array('directory' => NME_PLUGIN_URL));
}

add_action('admin_menu', 'nme_artful_menu');
function nme_artful_menu() {
    add_options_page('Artful.ly', 'Artful.ly', 'manage_options', 'artful-settings', 'nme_artful_options');
}

function nme_artful_options() {
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }
    echo '<div class="wrap" style="margin-top:20px; margin-left:20px;">';
    echo '<h2>How to Use the Artful.ly Widget Plugin</h2>';
    echo '<span style="font-size:1.2em;">Hello! We hope using this plugin will help you integrate <a href="http://artful.ly" target="blank">artful.ly</a> into your site.</span><br/><br/>';
    echo 'All you need to get donations and events into your site is right below.';
    echo '<h3>Donations</h3>';
    echo 'For donations, you can add a shortcode to your pages.<br/><br/>';
    echo 'Here is a sample shortcode for donations : <code>[art-donation id="organizationID"]</code><br/><br/>';
    echo '<i>Make sure you replace "organizationID" with your actual Organization ID. Organization ID\'s can be found by logging into your <a href="http://artful.ly" target="blank">artful.ly</a> account.</i>';
    echo '<h3>Events</h3>';
    echo 'For events, simply add the following shortcode: <code>[art-event id="eventID"]</code><br/><br/>';
    echo '<i>Make sure you replace "eventID" with your actual event ID. Event IDs can be found by logging into your <a href="http://artful.ly" target="blank">artful.ly</a> account.</i>';
    echo '<h3>Automatically Insert Shortcodes</h3>';
    echo '<strong>If you forget the shortcodes, don\'t worry</strong> - there\'s a handy button on every post and page to access your <a href="http://artful.ly" target="blank">artful.ly</a> content. Look for the little Artful.ly icon on the right side of the buttons above your Visual Editor when you edit a post or page. Simple, wasn\'t it :-)<br/><br/>';
    echo '<span style="font-size:1.2em;">And of course, if you have any questions, please check out our help pages or submit a support request via our <a href="https://www.fracturedatlas.org/site/knowledgebase/topic/Artful.ly" target="blank">knowledgebase</a>.</span><br/><br/>';
    echo 'This plugin was created by <a href="http://fracturedatlas.org" target="_blank">Fractured Atlas</a>, Arrow Root Media and <a href="http://punktdigital.com">Punkt Digital</a>';
    echo '</div>';
}

add_action('admin_notices', 'nme_art_notice');
function nme_art_notice() {
    if (get_option('art_activated') != 'true') {
        echo '<div class="updated">
                <p>Thanks for activating Artful.ly. Visit your <a href="http://artful.ly">Artful.ly</a> settings page for more info</p>
                </div>';
        add_option('art_activated', 'true');
    } else {
        
    }
}

add_filter('plugin_action_links', 'nme_art_plugin_action_links', 10, 2);
function nme_art_plugin_action_links($links, $file) {
    static $this_plugin;

    if (!$this_plugin) {
        $this_plugin = plugin_basename(__FILE__);
    }

    if ($file == $this_plugin) {
        $settings_link = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/options-general.php?page=artful-settings">Settings</a>';
        array_push($links, $settings_link);
    }
    return $links;
}

add_action( 'init', 'artfully_buttons' );
function artfully_buttons() {
    if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) {
        return;
    }
    add_filter( 'mce_external_plugins', 'artfully_add_buttons' );
    add_filter( 'mce_buttons', 'artfully_register_buttons' );
  
}
function artfully_add_buttons( $plugin_array ) {
    $plugin_array['artfullybutton'] = plugins_url('artfully-widget/lib/tinymce/plugins/artfully-button/artful_editor.js');
    return $plugin_array;
}
function artfully_register_buttons( $buttons ) {
    array_push( $buttons, 'separator', 'artfulbutton' );
    return $buttons;
}

?>