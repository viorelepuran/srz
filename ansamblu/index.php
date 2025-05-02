<?php
// Trebuie adaugat: 
function get_video_block_url($post_id) {
    // Get the post content
    $post = get_post($post_id);

    if (!$post || empty($post->post_content)) {
        return false; // Return false if the post doesn't exist or has no content
    }

    // Parse the blocks in the content
    $blocks = parse_blocks($post->post_content);

    // Loop through the blocks to find a 'core/video' block
    foreach ($blocks as $block) {
        if ($block['blockName'] === 'core/video') {
            // Check if the block has a 'src' attribute (video URL)
            if (!empty($block['attrs']['src'])) {
                return esc_url($block['attrs']['src']); // Return the video URL
            }
        }
    }

    return false; // No video block or URL found
}
//get_video_block_url($post_id);

function ansambluri_paginated_shortcode($atts) {
    // Get shortcode attributes
    $atts = shortcode_atts(
        array(
            'zona' => '', // Default: show all
            'posts_per_page' => 24, // Adjust as needed
        ),
        $atts,
        'ansambluri_list'
    );

    // Get current page number
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

    // WP_Query arguments
    $args = array(
        'post_type'      => 'ansambluri',
        'posts_per_page' => $atts['posts_per_page'],
        'paged'          => $paged,
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
    );

    // If zona is provided, add taxonomy query
    if (!empty($atts['zona'])) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'zona',
                'field'    => 'slug',
                'terms'    => $atts['zona'],
            ),
        );
    }

    // Custom Query
    $query = new WP_Query($args);

    ob_start(); // Start output buffering

    if ($query->have_posts()) :
        echo '<div class="ansambluri-list">';
        while ($query->have_posts()) : $query->the_post();
            echo ansamblu_card(get_the_ID());
        endwhile;
        echo '</div>';

        // Pagination
        $big = 999999999;
        echo '<div class="pagination">';
        echo paginate_links(array(
            'base'      => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
            'format'    => '?paged=%#%',
            'current'   => max(1, get_query_var('paged')),
            'total'     => $query->max_num_pages,
            'prev_text' => '&laquo;', // This is << in HTML entities
            'next_text' => '&raquo;', // This is >> in HTML entities
        ));

        echo '</div>';

        wp_reset_postdata(); // Reset query
    else :
        echo '<p>No ansambluri found.</p>';
    endif;

    return ob_get_clean(); // Return buffered output
}

add_shortcode('list_ansambluri', 'ansambluri_paginated_shortcode');


function ansamblu_card($postID){
    echo $postID;
}