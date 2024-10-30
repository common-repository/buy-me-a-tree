<?php
   /*
   Plugin Name: Buy me a Tree
   Plugin URI: https://github.com/jordmondson/buy-me-a-tree/
   description: Buy me a tree is a simple WordPress plugin that allows user to add a "Buy me a Tree" button to their website. Taking the user to the website owners Ecologi profile.
   Version: 1.0.4
   Author: Jordan Edmondson

   Buy me a Tree is free software: you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation, either version 2 of the License, or
   any later version.
    
   Buy me a Tree is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
   GNU General Public License for more details.
    
    You should have received a copy of the GNU General Public License
    along with Buy me a Tree. If not, see https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html.
   */

const ECOSIA_URL = "https://ecologi.com/";
const DEVELOPERS_REFFERRAL_CODE = "buymeatree?r=6082aabfc20ae9d38b09d29d";

// Add html to footer
add_action('wp_footer', 'bmat_add_content');
function bmat_add_content()
{
    if (get_option('bmat_options')['special_link']) {
        echo ("<a href=\"" . get_option('bmat_options')['special_link'] . "\" target=”_blank”> <div class=\"bmat-button " . get_option('bmat_options')['bmat_location_class'] . "\"> 🌳  <span class=\"bmat-tooltiptext d-none d-md-inline\">Buy me a Tree</span>
        </div></a>");
    }
}


function bmat_enqueue_styles()
{
    wp_enqueue_style('buy-me-a-tree', plugin_dir_url(__FILE__) . '/assets/css/buy-me-a-tree.css');
}
add_action('wp_enqueue_scripts', 'bmat_enqueue_styles');


// Admin page
function bmt_setup_admin_menu()
{
    add_menu_page('Test Plugin Page', 'Buy me a Tree', 'manage_options', 'test-plugin', 'bmat_init_admin_page');
}
add_action('admin_menu', 'bmt_setup_admin_menu');

function bmat_init_admin_page()
{
    ?>
        <form action="options.php" method="post">
            <?php
            settings_fields('bmat_options');
            do_settings_sections('bmat_plugin'); ?>
            <input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e('Save'); ?>" />
        </form>
<?php
}

function bmat_register_settings()
{
    register_setting('bmat_options', 'bmat_options', 'bmat_options_validate');
    add_settings_section('bmat_settings', 'Buy me a Tree Settings', 'bmat_plugin_section_text', 'bmat_plugin');

    add_settings_field('bmat_url_option', 'Special link / URL', 'bmat_url_option', 'bmat_plugin', 'bmat_settings');
    add_settings_field('bmat_display_location_class_option', 'Location', 'bmat_display_location_class_option', 'bmat_plugin', 'bmat_settings');

}
add_action('admin_init', 'bmat_register_settings');

function bmat_options_validate($input)
{
    $url = trim($input['special_link']);

    if(!bmat_validate_url($url)) {
        $input['special_link'] = '';
    }

    return $input;
}

function bmat_validate_url(String $url)
{
    // Check url is the correct url for Ecosia
    $validUrls = [
        ECOSIA_URL,
        str_replace('https://', '', ECOSIA_URL),
        str_replace('http://', '', ECOSIA_URL),
        str_replace('http://', '', ECOSIA_URL),
        'www.' . str_replace('http://', '', ECOSIA_URL),
        'www.' . str_replace('http://', '', ECOSIA_URL),
        'www.' . ECOSIA_URL
    ];

    $startsWithValidUrl = false;
    foreach($validUrls as $validUrl) {
        if(strpos($url, $validUrl)) {
            $startsWithValidUrl = true;
            break;
        }
    }

    // Check referral code exists
    return $startsWithValidUrl || strpos('?r=', $url);
}

function bmat_plugin_section_text() 
{
    echo('<p>Enter your Ecosia special link or profile URL below. If you haven\'t signed up already, click <a href="' . ECOSIA_URL .  DEVELOPERS_REFFERRAL_CODE . '" target=”_blank”>here</a>.</p>');
    echo('<small>Please note: Buy me a Tree is not owned by Ecologi and does not act on its behalf.</small>');
}

function bmat_url_option() 
{
    $options = get_option( 'bmat_options' );
    echo("<input id='bmat_url_option' name='bmat_options[special_link]' type='text' value='" . esc_attr( $options['special_link'] ) . "' required /><small style='display: block'>Don't feel like signing up? Feel free to use https://ecologi.com/buymeatree?r=6082aabfc20ae9d38b09d29d</small>");
}

function bmat_display_location_class_option()
{
    $options = get_option('bmat_options');

    $html = "<div>
        <input type='radio' id='bmat-left' name='bmat_options[bmat_location_class]' value='bmat-left'";

    if (esc_attr($options['bmat_location_class']) == "bmat-left") {
        $html .= "checked>";
    } else {
        $html .= ">";
    }

    $html .= "<label for='bmat-left'>Left</label>
        </div>

        <div>
        <input type='radio' id='bmat-right' name='bmat_options[bmat_location_class]' value='bmat-right'";

    if (esc_attr($options['bmat_location_class']) == "bmat-right") {
        $html .= "checked>";
    } else {
        $html .= ">";
    }

    $html .= "<label for='bmat-right'>Right</label>
        </div>
    ";

    echo ($html);
}


