<?php
/**
 * Plugin Name: Post Accordion
 * Description: Displays posts in a toggle list format with the title as the header and body hidden in collapsible sections ordered alphabetically.
 * Version: 1.3
 * Author: elkielki
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Loads necessary scripts for the plugin
function tpl_enqueue_scripts() {
    // Loads jquery into wordpress
    wp_enqueue_script('jquery');

    // Loads the custom toggle script
    wp_enqueue_script('tpl-script', plugin_dir_url(__FILE__) . 'toggle-post-list.js', array('jquery'), null, true);
}

// Run the tpl_enqueue_scripts function when loading the frontend
add_action('wp_enqueue_scripts', 'tpl_enqueue_scripts');

// Displays posts grouped by symbols, numbers, and letters
function tpl_display_toggle_posts_alphabetical() {
    // defines the query parameters for fetching posts
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC'
    );

    // Fetches all standard WordPress posts in ascending alphabetical order based on title
    $query = new WP_Query($args);

    // If there are any posts
    if ($query->have_posts()) {
        // create a div to contain the resulting toggle list
        $output = '<div class="alphabetical-toggle-post-list">';

        // To store the posts based on the character that the title starts with
        $symbols = '';
        $numbers = '';
        $letters = array();

        // Loop through all fetched posts
        while ($query->have_posts()) {
            $query->the_post();

            // Get the first character of the post title
            $first_char = substr(get_the_title(), 0, 1);

            // If first character is a letter
            if (preg_match('/[A-Za-z]/', $first_char)) {

                // Group all posts regardless of uppercase or lowercase
                $first_letter = strtoupper($first_char);

                // Create new group if it doesn't exist
                if (!isset($letters[$first_letter])) {
                    $letters[$first_letter] = '';
                }
                
                // Build HTML structure for each post: clickable title and a toggleable content
                $letters[$first_letter] .= '<div class="toggle-post">';
                $letters[$first_letter] .= '<h3 class="toggle-title">' . get_the_title() . '</h3>';
                $letters[$first_letter] .= '<div class="toggle-content" style="display:none;">' . get_the_content() . '</div>';
                $letters[$first_letter] .= '</div>';
            
            } // If first character is a number
            elseif (preg_match('/[0-9]/', $first_char)) {
                
                // Build HTML structure
                $numbers .= '<div class="toggle-post">';
                $numbers .= '<h3 class="toggle-title">' . get_the_title() . '</h3>';
                $numbers .= '<div class="toggle-content" style="display:none;">' . get_the_content() . '</div>';
                $numbers .= '</div>';
            } // If first character is a symbol
            else {
                // Build HTML structure
                $symbols .= '<div class="toggle-post">';
                $symbols .= '<h3 class="toggle-title">' . get_the_title() . '</h3>';
                $symbols .= '<div class="toggle-content" style="display:none;">' . get_the_content() . '</div>';
                $symbols .= '</div>';
            }
        }

        // Add the posts with a title that start with symbol to the resulting string
        if (!empty($symbols)) {
            $output .= '<h2 class="alphabet-letter">Symbols</h2>';
            $output .= '<div class="toggle-group">' . $symbols . '</div>';
        }

        // Add the posts with a title that start with a number to the resulting string
        if (!empty($numbers)) {
            $output .= '<h2 class="alphabet-letter">Numbers</h2>';
            $output .= '<div class="toggle-group">' . $numbers . '</div>';
        }

        // add the posts with a title that start with a letter to the result string in alphabetical order
        foreach ($letters as $letter => $posts) {
            $output .= '<h2 class="alphabet-letter">' . $letter . '</h2>';
            $output .= '<div class="toggle-group">' . $posts . '</div>';
        }

        // Close the main container, reset global post data, and return toggle list
        $output .= '</div>';  
        wp_reset_postdata();
        return $output;
    } // If no posts are found 
    else {
        return '<p>No posts found.</p>';
    }
}

// Creates new WordPress shortcode to add toggle list to webpage
add_shortcode('toggle_posts_alphabetical', 'tpl_display_toggle_posts_alphabetical');
