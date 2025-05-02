<?php
// Enqueue parent theme styles
function twentytwentyfive_child_enqueue_styles() {
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('child-style', get_stylesheet_uri(), array('parent-style'));
    
    wp_enqueue_script(
        'general-script',  // Handle for the script
        get_stylesheet_directory_uri() . '/js/general.js',  // Path to the script (adjust if needed)
        array('jquery'),  // Dependencies (e.g., jQuery)
        null,  // Version, set to null to avoid caching issues
        true  // Load script in footer
    );
    
    
    // Check if we're on a single post of the 'apartamente' post type
    if (is_singular('apartamente')) {
        // Enqueue the script
        wp_enqueue_script(
            'calculator-rate',  // Handle for the script
            get_stylesheet_directory_uri() . '/structure/calculator-rate.js',  // Path to the script (adjust if needed)
            array('jquery'),  // Dependencies (e.g., jQuery)
            null,  // Version, set to null to avoid caching issues
            true  // Load script in footer
        );

    // Localize the script to pass the AJAX URL
    wp_localize_script('calculator-rate', 'ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php'),
    ));
    }
}
add_action('wp_enqueue_scripts', 'twentytwentyfive_child_enqueue_styles');


/**
 * Check if the current user is logged in and has one of the specified allowed roles.
 *
 * @param array $allowed_roles An array of allowed roles.
 * @return bool True if the user is logged in and has one of the allowed roles, false otherwise.
 */

function is_user_in_allowed_roles($allowed_roles = ['manager', 'administrator', 'vanzari']) {
    if (!is_user_logged_in()) {
        return false;
    }
    $user = wp_get_current_user();
    
    // Check if user has one of the allowed roles
    return !empty(array_intersect($allowed_roles, $user->roles));
}

function user_can_manage($allowed_roles = ['manager', 'administrator']) {
    if (!is_user_logged_in()) {
        return false;
    }
    $user = wp_get_current_user();
    
    // Check if user has one of the allowed roles
    return !empty(array_intersect($allowed_roles, $user->roles));
}

function add_template_support_to_pages() {
    add_post_type_support('page', 'page-attributes');
}
add_action('init', 'add_template_support_to_pages');


// Add restrictions on uploads based on user role
function restrict_upload_by_user_role($file) {
    $max_file_size = 2 * 1024 * 1024; // Maximum file size in bytes (2MB)
    $allowed_file_types = ['image/jpeg', 'image/png'];
    $user = wp_get_current_user();
    $user_roles = $user->roles;

    // Check if user role is allowed to upload files
    if (in_array('editor', $user_roles) || in_array('administrator', $user_roles)) {
        // Admin and Editor roles bypass restrictions
        return $file;
    }

    // Restrict file size
    if ($file['size'] > $max_file_size) {
        $file['error'] = 'The file is too large. Maximum allowed size is 2MB.';
        return $file;
    }

    // Restrict file type
    if (!in_array($file['type'], $allowed_file_types)) {
        $file['error'] = 'Invalid file type. Only JPG and PNG files are allowed.';
        return $file;
    }

    return $file;
}
add_filter('wp_handle_upload_prefilter', 'restrict_upload_by_user_role');

// Restrict image dimensions after upload
function restrict_image_dimensions($file) {
    $max_width = 1920; // Maximum width in pixels
    $max_height = 1080; // Maximum height in pixels
    $user = wp_get_current_user();
    $user_roles = $user->roles;

    // Admin and Editor roles bypass restrictions
    if (in_array('editor', $user_roles) || in_array('administrator', $user_roles)) {
        return $file;
    }

    $image_data = getimagesize($file['file']);
    if ($image_data) {
        $width = $image_data[0];
        $height = $image_data[1];

        if ($width > $max_width || $height > $max_height) {
            wp_delete_file($file['file']); // Delete the uploaded file
            return new WP_Error('dimension_error', 'Image dimensions exceed the maximum allowed size of 1920x1080 pixels.');
        }
    }

    return $file;
}
add_filter('wp_handle_upload', 'restrict_image_dimensions', 10, 2);


function disable_apartamente_archive($query) {
    if (!is_admin() && $query->is_main_query() && is_post_type_archive('apartamente')) {
        $query->set_404(); // Redirects to a 404 page
    }
    if (!is_admin() && $query->is_main_query() && is_post_type_archive('ansambluri')) {
        $query->set_404(); // Redirects to a 404 page
    }
    
    // Ensure this only affects the main query on the front end
    if (!is_admin() && $query->is_main_query() && $query->get('post_type') === 'apartamente') {
        $meta_query = $query->get('meta_query') ?: [];

        $meta_query[] = [
            'key'     => 'sold',
            'value'   => '1',
            'compare' => '!=', // Exclude sold apartments
        ];

        $query->set('meta_query', $meta_query);
    }
}
add_action('pre_get_posts', 'disable_apartamente_archive');

function generate_pagination($total_pages, $paged) {
    if ($total_pages > 1) {
        echo '<div class="pagination">';

        $range = 3; // Number of pages to show before the last page
        $displayed_pages = [];

        // Get current query parameters
        $query_args = $_GET;
        unset($query_args['paged']); // Remove existing 'paged' parameter

        // Always show first three pages
        for ($i = 1; $i <= 3; $i++) {
            if ($i <= $total_pages) {
                $displayed_pages[] = $i;
            }
        }

        // Always show last three pages
        for ($i = $total_pages - $range; $i <= $total_pages; $i++) {
            if ($i > 3) {
                $displayed_pages[] = $i;
            }
        }

        $displayed_pages = array_unique($displayed_pages);
        sort($displayed_pages);

        $prev_page = 0; // Track previous page to add "..."

        foreach ($displayed_pages as $page) {
            if ($prev_page > 0 && $page > $prev_page + 1) {
                echo '<span class="dots">...</span>'; // Add ellipsis when there's a gap
            }

            // Append correct query parameters for each page
            $query_args['paged'] = $page;
            $query_string = http_build_query($query_args);
            $pagination_url = '?' . esc_attr($query_string);

            echo '<a href="' . $pagination_url . '" ' . ($paged == $page ? 'class="active"' : '') . '>' . $page . '</a> ';
            $prev_page = $page;
        }

        echo '</div>';
    }
}

function set_minimum_sortPrice($post_id) {
    // Ensure the function runs only for the correct post type
    if (get_post_type($post_id) !== 'apartamente') {
        return;
    }

    // Get necessary meta values
    $pret_promo = get_post_meta($post_id, 'pret_promo', true);
    $tip_promotie = get_post_meta($post_id, 'tip_promotie', true);
    $pret_promotional = get_post_meta($post_id, 'pret_promotional', true);
    $pret = get_post_meta($post_id, 'pret', true);

    // Convert to float to ensure numeric comparison
    $pret_promotional = floatval($pret_promotional);
    $pret = floatval($pret);

    // Determine the minimum price
    if ($pret_promo == 1 && $tip_promotie == 0 && $pret_promotional > 0) {
        $min_price = min($pret, $pret_promotional);
    } else {
        $min_price = $pret;
    }

    // Update the 'sortPrice' meta field
    update_post_meta($post_id, 'sortPrice', $min_price);
}

// Run function when a post is saved/updated
add_action('save_post_apartamente', 'set_minimum_sortPrice');
 


// Register Custom Post Types
require_once get_stylesheet_directory() . '/structure/index.php';
require_once get_stylesheet_directory() . '/structure/cpt.php';
require_once get_stylesheet_directory() . '/structure/migrate-cpt.php';
require_once get_stylesheet_directory() . '/structure/cpt-functions.php';
require_once get_stylesheet_directory() . '/structure/cleanup.php';
require_once get_stylesheet_directory() . '/structure/single-apartament.php';
require_once get_stylesheet_directory() . '/structure/calculator-rate.php';
require_once get_stylesheet_directory() . '/shortcodes/index.php';
require_once get_stylesheet_directory() . '/shortcodes/property_shortcodes.php';
require_once get_stylesheet_directory() . '/ansamblu/index.php';
require_once get_stylesheet_directory() . '/api/index.php';
require_once get_stylesheet_directory() . '/secrets/index.php';
require_once get_stylesheet_directory() . '/svg/icons.php';
require_once get_stylesheet_directory() . '/feedback/feedback.php';
 
 


function enqueue_swiper_assets() {
    wp_enqueue_style('swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css', array(), null);
    wp_enqueue_script('swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js', array('jquery'), null, true);
}
add_action('wp_enqueue_scripts', 'enqueue_swiper_assets');

 
function sudrezidential_register_custom_blocks() {
    // Image slider: old way
    register_block_type( get_stylesheet_directory() . '/blocks/image-slider' );
    register_block_type_from_metadata(
        get_stylesheet_directory() . '/blocks/accordion-user',
        [
            'render_callback' => function ( $attributes ) {
                ob_start();
                include get_stylesheet_directory() . '/blocks/accordion-user/render.php';
                return ob_get_clean();
            }
        ]
    );
    register_block_type_from_metadata(
        get_stylesheet_directory() . '/blocks/location-map',
        [
            'render_callback' => function ( $attributes ) {
                ob_start();
                include get_stylesheet_directory() . '/blocks/location-map/render.php';
                return ob_get_clean();
            }
        ]
    );
    
}
add_action( 'init', 'sudrezidential_register_custom_blocks' );

function my_initial_import() {
    $import_dir = get_stylesheet_directory() . '/initial-import';

    // Declare file paths first
    $footer_file = trailingslashit($import_dir) . 'footer-banner-slider.html';
    $vanzare_ap = trailingslashit($import_dir) . 'vanzare-apartamente.html';
    $vanzare_case = trailingslashit($import_dir) . 'case-vile.html';
    $fluxul_nostru = trailingslashit($import_dir) . 'fluxul-nostru.html';

    // Initialize post IDs
    $footer_post_id = 0;
    $vanzare_ap_id = 0;
    $vanzare_case_id = 0;
    $fluxul_nostru_id = 0;

    // 1. Check & Create Footer Post
    $existing_footer_post = get_page_by_title('Footer Content', OBJECT, 'wp_block');
    if (!$existing_footer_post && file_exists($footer_file)) {
        $footer_content = file_get_contents($footer_file);
        $footer_post_id = wp_insert_post([
            'post_title'   => 'Footer Content',
            'post_content' => $footer_content,
            'post_status'  => 'publish',
            'post_type'    => 'wp_block',
        ]);
    } else {
        $footer_post_id = $existing_footer_post->ID ?? 0;
    }

    // 2. Check & Create Fluxul Nostru Post
    $existing_flux_post = get_page_by_title('Fluxul Nostru', OBJECT, 'wp_block');
    if (!$existing_flux_post && file_exists($fluxul_nostru)) {
        $fluxul_nostru_content = file_get_contents($fluxul_nostru);
        $fluxul_nostru_id = wp_insert_post([
            'post_title'   => 'Fluxul Nostru',
            'post_content' => $fluxul_nostru_content,
            'post_status'  => 'publish',
            'post_type'    => 'wp_block',
        ]);
    } else {
        $fluxul_nostru_id = $existing_flux_post->ID ?? 0;
    }

    // 3. Check & Create Vanzare Apartamente Post
    $existing_vanzare_ap_post = get_page_by_title('Apartamente noi Bucuresti | Ilfov', OBJECT, 'page');
    if (!$existing_vanzare_ap_post && file_exists($vanzare_ap)) {
        $vanzare_ap_content = file_get_contents($vanzare_ap);
        $vanzare_ap_content = str_replace(['86471', '86369'], [$footer_post_id, $fluxul_nostru_id], $vanzare_ap_content);
        $vanzare_ap_id = wp_insert_post([
            'post_title'   => 'Apartamente noi Bucuresti | Ilfov',
            'post_content' => $vanzare_ap_content,
            'post_status'  => 'publish',
            'post_name'    => 'vanzare-apartamente',
            'post_type'    => 'page',
        ]);
    } else {
        $vanzare_ap_id = $existing_vanzare_ap_post->ID ?? 0;
    }

    // 4. Check & Create Vanzare Case-Vile Post
    $existing_vanzare_case_post = get_page_by_title('Case-Vile noi Bucuresti | Ilfov', OBJECT, 'page');
    if (!$existing_vanzare_case_post && file_exists($vanzare_case)) {
        $vanzare_case_content = file_get_contents($vanzare_case);
        $vanzare_case_content = str_replace(['86471', '86369'], [$footer_post_id, $fluxul_nostru_id], $vanzare_case_content);
        $vanzare_case_id = wp_insert_post([
            'post_title'   => 'Case-Vile noi Bucuresti | Ilfov',
            'post_content' => $vanzare_case_content,
            'post_status'  => 'publish',
            'post_name'    => 'vanzare-case-vile',
            'post_type'    => 'page',
        ]);
    } else {
        $vanzare_case_id = $existing_vanzare_case_post->ID ?? 0;
    }

    // 5. Process Other Files
    if (is_dir($import_dir)) {
        $files = scandir($import_dir);
        foreach ($files as $file) {
            // Skip special files
            if (in_array($file, ['.', '..', 'footer-banner-slider.html', 'vanzare-apartamente.html', 'case-vile.html', 'fluxul-nostru.html'])) {
                continue;
            }

            $file_path = trailingslashit($import_dir) . $file;

            // Extract post ID from filename (assuming file is named like "123.html")
            $post_id = intval(pathinfo($file, PATHINFO_FILENAME));
            if (!$post_id) {
                continue;
            }

            $content = file_get_contents($file_path);

            // 6. Replace placeholders with created post IDs
            if ($footer_post_id) {
                $content = str_replace(['86471', '86369'], [$footer_post_id, $fluxul_nostru_id], $content);
            }

            // 7. Update the post with modified content.
            wp_update_post([
                'ID'           => $post_id,
                'post_content' => $content,
            ]);
        }
    }
}
add_action('after_switch_theme', 'my_initial_import');
 