<?php
// Step 1: Add Submenu Page Under 'apartamente'
function custom_redirections_apartamente_menu() {
    add_submenu_page(
        'edit.php?post_type=apartamente', // Parent menu slug (for apartamente post type)
        __('Sterge proprietati vandute', 'textdomain'), // Page title
        __('Sterge vandute', 'textdomain'), // Menu title
        'manage_options', // Capability
        'custom-redirections', // Menu slug
        'custom_redirections_page' // Callback function
    );
}
add_action('admin_menu', 'custom_redirections_apartamente_menu');

function custom_redirections_page() {
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Sterge proprietati vandute', 'textdomain'); ?></h1>
        <p><?php esc_html_e('Aceasta pagina permite stergerea proprietatilor vandute si inserarea redirectionarilor 301.', 'textdomain'); ?></p>
        
        <?php 
        // Display the sold properties count and list
        $sold_properties = get_sold_properties_list();
        ?>
        <h2><?php esc_html_e('Numar total de proprietati vandute:', 'textdomain'); ?> <?php echo esc_html($sold_properties['count']); ?></h2>

        <h2><?php esc_html_e('Lista proprietatilor vandute:', 'textdomain'); ?></h2>
        <table class="wp-list-table widefat striped">
            <thead>
                <tr>
                    <th><?php esc_html_e('ID', 'textdomain'); ?></th>
                    <th><?php esc_html_e('Titlu', 'textdomain'); ?></th>
                    <th><?php esc_html_e('Redirectionare', 'textdomain'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sold_properties['list'] as $property): ?>
                    <tr>
                        <td><?php echo esc_html($property['id']); ?></td>
                        <td><?php echo esc_html($property['title']); ?></td>
                        <td><?php echo esc_url($property['redirect']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <form method="post">
            <?php submit_button('Proceseaza proprietatile'); ?>
        </form>
    </div>
    <?php

    // Check if the form was submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Trigger the custom redirections insertion function
        custom_insert_rank_math_redirections();
        echo '<p>' . esc_html__('Redirections have been processed.', 'textdomain') . '</p>';
    }
}

// Helper function to get sold properties and their redirections
function get_sold_properties_list() {
    $sold_properties = [
        'count' => 0,
        'list' => []
    ];

    $args = array(
        'post_type' => 'apartamente',
        'meta_query' => array(
            array(
                'key' => 'sold',
                'value' => '1',
                'compare' => '='
            )
        ),
        'posts_per_page' => -1
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();

            $post_id = get_the_ID();
            $post_type = get_post_meta($post_id, 'srz_property', true);

            // Determine the redirect URL
            $redirect_url = get_redirect_url($post_id, $post_type);

            $sold_properties['list'][] = [
                'id' => $post_id,
                'title' => get_the_title(),
                'redirect' => $redirect_url
            ];
        }

        // Count of sold properties
        $sold_properties['count'] = $query->found_posts;
    }

    wp_reset_postdata();

    return $sold_properties;
}

// Function to get redirect URL for a property
function get_redirect_url($post_id, $post_type) {
    if ($post_type === 'apartamente') {
        $camere = get_post_meta($post_id, 'numar_camere', true);
        switch ($camere) {
            case '0':
            case '1':
                return 'https://www.sudrezidential.ro/garsoniere-de-vanzare-bucuresti/';
            case '2':
                return 'https://www.sudrezidential.ro/apartamente-2-camere-de-vanzare-bucuresti/';
            case '3':
                return 'https://www.sudrezidential.ro/apartamente-3-camere-de-vanzare-bucuresti/';
            case '4':
                return 'https://www.sudrezidential.ro/apartamente-4-camere-de-vanzare-bucuresti/';
            case '5':
            default:
                return 'https://www.sudrezidential.ro/vanzare-apartamente/';
        }
    } elseif ($post_type === 'vile') {
        return 'https://www.sudrezidential.ro/vanzare-case-vile/';
    } elseif ($post_type === 'spatii-comerciale') {
        $tip = get_post_meta($post_id, 'tipul_proprietatii', true);
        if ($tip === 'vile') {
            return 'https://www.sudrezidential.ro/vanzare-case-vile/';
        } elseif ($tip === 'apartament') {
            return 'https://www.sudrezidential.ro/vanzare-apartamente/';
        } else {
            return 'https://www.sudrezidential.ro/proprietati-speciale/';
        }
    }

    return '';
}

// Step 2: Function to Insert Redirections
function custom_insert_rank_math_redirections() {
    global $wpdb;

    // Define the post types to query
    $post_types = array('apartamente', 'vile', 'spatii-comerciale');

    // Query posts of the specified types where the meta field 'sold' is 1
    $args = array(
        'post_type' => $post_types,
        'meta_query' => array(
            array(
                'key' => 'sold',
                'value' => '1',
                'compare' => '='
            )
        ),
        'posts_per_page' => -1 // Get all posts
    );

    $query = new WP_Query($args);

    // Loop through each post and insert a redirection
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();

            $post_id = get_the_ID();
            $post_type = get_post_type();

            // Insert redirection for the post
            insert_rank_math_redirection($post_id, $post_type);
        }
    }

    // Reset the global post data
    wp_reset_postdata();
}

// Step 3: Insert Redirection Function
function insert_rank_math_redirection($post_id, $post_type) {
    global $wpdb;

    // Get the post URL without the domain part
    $post_url = ltrim(str_replace(home_url(), '', get_permalink($post_id)), '/');

    // Determine the redirect URL based on the post type and meta fields
    $redirect_to = '';

    if ($post_type === 'apartamente') {
        $camere = get_post_meta($post_id, 'numar_camere', true);
        switch ($camere) {
            case '0':
            case '1':
                $redirect_to = 'https://www.sudrezidential.ro/garsoniere-de-vanzare-bucuresti/';
                break;
            case '2':
                $redirect_to = 'https://www.sudrezidential.ro/apartamente-2-camere-de-vanzare-bucuresti/';
                break;
            case '3':
                $redirect_to = 'https://www.sudrezidential.ro/apartamente-3-camere-de-vanzare-bucuresti/';
                break;
            case '4':
                $redirect_to = 'https://www.sudrezidential.ro/apartamente-4-camere-de-vanzare-bucuresti/';
                break;
            case '5':
            default:
                $redirect_to = 'https://www.sudrezidential.ro/vanzare-apartamente/';
                break;
        }
    } elseif ($post_type === 'vile') {
        $redirect_to = 'https://www.sudrezidential.ro/vanzare-case-vile/';
    } elseif ($post_type === 'spatii-comerciale') {
        $tip = get_post_meta($post_id, 'tipul_proprietatii', true);
        if ($tip === 'vile') {
            $redirect_to = 'https://www.sudrezidential.ro/vanzare-case-vile/';
        } elseif ($tip === 'apartament') {
            $redirect_to = 'https://www.sudrezidential.ro/vanzare-apartamente/';
        } else {
            $redirect_to = 'https://www.sudrezidential.ro/proprietati-speciale/';
        }
    }

    // Calculate the length of the pattern string
    $pattern_length = strlen($post_url);

    // Prepare the serialized sources data with dynamic length
    $sources = 'a:1:{i:0;a:3:{s:6:"ignore";s:0:"";s:7:"pattern";s:' . $pattern_length . ':"' . $post_url . '";s:10:"comparison";s:5:"exact";}}';

    // Prepare the data to be inserted
    $data = array(
        'sources' => $sources,
        'url_to' => $redirect_to,
        'header_code' => 301,
        'status' => 'active',
        'created' => current_time('mysql'), // WordPress function to get the current time
    );

    // Specify the format of the data
    $format = array('%s', '%s', '%d', '%s', '%s');

    // Insert the data into the rank_math_redirections table
    $insert_success = $wpdb->insert($wpdb->prefix . 'rank_math_redirections', $data, $format);

    // Check if the insert was successful
    if ($insert_success) {
        // Delete the post
        wp_delete_post($post_id, true); // Set the second parameter to true to force deletion
    }
}
