<?php

/*
  Plugin Name: Our Test Plugin
  Description: A truly Amazing plugin.
  Version: 1.0.0
  Author: ParkOur619
  Author URI: https://github.com/parkour619
  Text Domain: wcpdomain
  Domain Path: /languages
*/

class WordCountAndTimePlugin {
    function __construct() {
        // Add action to create admin page
        add_action('admin_menu', [$this, 'adminPage']);
        // Add action to initialize plugin settings
        add_action('admin_init', [$this, 'settings']);
        // Add filter to modify content output
        add_filter('the_content', [$this, 'ifWrap']);
        //Add action to initialz=ize the language setting
        add_action('init', [$this, 'languages']);
    }

    // For Admin Panel Setting
    function adminPage () {
        // Add options page for Word Count settings
        add_options_page('Word Count Settings', esc_html__('Word Count', 'wcpdomain'), 'manage_options', 'word-count-settings-page', [$this, 'ourHTML']);
    }

    function ourHTML () {  ?>
        <div class="wrap">
            <h1>Word Count Setting</h1>
            <form action="options.php" method="POST">
                <?php
                // Output settings fields
                settings_fields('wordcountplugin');
                do_settings_sections('word-count-settings-page');
                submit_button();
                ?>
            </form>
        </div>
    <?php  } 

    function settings() {
        // Add settings section
        add_settings_section('wcp_first_section', null, null, 'word-count-settings-page');

        // For Location
        add_settings_field('wcp_location', 'Display Location', [$this, 'locationHTML'], 'word-count-settings-page', 'wcp_first_section');
        // Register setting for location
        register_setting('wordcountplugin', 'wcp_location', ['sanitize_callback' => [$this, 'sanitizeLocation'], 'default' => '0']);

        // For Headline Text
        add_settings_field('wcp_headline', ' Headline Text', [$this, 'headlineHTML'], 'word-count-settings-page', 'wcp_first_section');
        // Register setting for headline text
        register_setting('wordcountplugin', 'wcp_headdline', ['sanitize_callback' => 'sanitize_text_field', 'default' => 'Post Statistics']);

        // For Word Count
        add_settings_field('wcp_wordcount', ' Word Count', [$this, 'wordcountHTML'], 'word-count-settings-page', 'wcp_first_section');
        // Register setting for word count
        register_setting('wordcountplugin', 'wcp_wordcount', ['sanitize_callback' => 'sanitize_text_field', 'default' => '1']);

        // For Character Count
        add_settings_field('wcp_charactercount', ' Character Count', [$this, 'charactercountHTML'], 'word-count-settings-page', 'wcp_first_section');
        // Register setting for character count
        register_setting('wordcountplugin', 'wcp_charactercount', ['sanitize_callback' => 'sanitize_text_field', 'default' => '0']);

        // For Read Time
        add_settings_field('wcp_readtime', ' Read Time', [$this, 'readtimeHTML'], 'word-count-settings-page', 'wcp_first_section');
        // Register setting for read time
        register_setting('wordcountplugin', 'wcp_readtime', ['sanitize_callback' => 'sanitize_text_field', 'default' => '1']);
    }

    function sanitizeLocation($input) {
        // Validate and sanitize location input
        if ($input != '0' && $input != '1') {
            add_settings_error('wcp_location', 'wcp_location_error', 'Display location must be either beginning or end');
            return get_option('wcp_location');
        }
        return $input;
    }

    // For Location
    function locationHTML() { ?>
        <select name="wcp_location">
            <option value="0" <?php selected(get_option('wcp_location'), '0'); ?>>Beginning of Post</option>
            <option value="1" <?php selected(get_option('wcp_location'), '1'); ?>>End of Post</option>
        </select>
    <?php }

    // For Headline Text
    function headlineHTML() { ?>
        <input type="text" name="wcp_headdline" value="<?php echo esc_attr(get_option('wcp_headdline')); ?>">
    <?php }

    // For Word Count
    function wordcountHTML() { ?>
        <input type="checkbox" name="wcp_wordcount" value="1" <?php echo checked(get_option('wcp_wordcount'), '1'); ?>>
    <?php }

    // For Character Count
    function charactercountHTML() { ?>
        <input type="checkbox" name="wcp_charactercount" value="1" <?php echo checked(get_option('wcp_charactercount'), '1'); ?>>
    <?php }

    // For Read Time
    function readtimeHTML() { ?>
        <input type="checkbox" name="wcp_readtime" value="1" <?php echo checked(get_option('wcp_readtime'), '1'); ?>>
    <?php }

    // Show on front end 
    function ifWrap($content) {
        // Check if it's a main query and a single post, and if at least one option is enabled
        if (is_main_query() && is_single() && (get_option('wcp_wordcount', '1') || get_option('wcp_charactercount', '0') || get_option('wcp_readtime', '1')) ) {
            // Generate HTML based on options
            return $this->createHTML($content);
        }
        return $content;
    }

    function createHTML($content) {
        $html = '<h3>' . esc_html(get_option('wcp_headline', 'Post Statistics')) . '</h3><p>';

        // Get word count once because both word count and read time will need it.
        if (get_option('wcp_wordcount', '1') || get_option('wcp_readtime', '1')) {
            $wordCount = str_word_count(strip_tags($content));
        }

        // Word count
        if (get_option('wcp_wordcount', '1')) {
            $html .= esc_html__('This post has', 'wcpdomain') . ' ' . $wordCount . ' ' . esc_html__('words', 'wcpdomain') . '.<br>'; 
        }
        
        // Character count
        if (get_option('wcp_charactercount', '1')) {
            $html .= 'This post has ' . strlen(strip_tags($content)) . ' characters.<br>'; 
        }
        
        // Time to read
        if (get_option('wcp_readtime', '1')) {
            $html .= 'This post will take about approximately ' . round($wordCount/225) . ' minute(s) to read.<br>'; 
        }

        $html .= '</p>';

        // To show on the beginning or end of post
        if (get_option('wcp_location', '0') == '0') {
            return $html . $content;
        }
        return $content . $html;
    }

     // For Languages
     function languages() {
        load_plugin_textdomain('wcpdomain', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

}

// Create a new instance of the WordCountAndTimePlugin.
$wordCountAndTimePlugin = new WordCountAndTimePlugin;


the tezt
