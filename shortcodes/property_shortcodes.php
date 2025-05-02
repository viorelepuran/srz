<?php
function get_apartamente_author($atts) {
    global $wpdb;

    // Get the current author ID (works on author archive pages)
    if (is_author()) {
        $current_author_id = get_queried_object_id();
    } else {
        $current_author_id = get_current_user_id();
    }

    if (!$current_author_id) {
        return '<p>No apartments found.</p>'; // No author detected, return empty
    }

    $paged = max(1, get_query_var('paged', 1));
    $posts_per_page = 18;
    $offset = ($paged - 1) * $posts_per_page;

    // Base query (Optimized)
    $query = "
        FROM {$wpdb->posts} p
        LEFT JOIN {$wpdb->postmeta} pm_price ON p.ID = pm_price.post_id AND pm_price.meta_key = 'priceSort'
        WHERE p.post_type = 'apartamente' 
        AND p.post_status = 'publish'
        AND EXISTS (
            SELECT 1 FROM {$wpdb->postmeta} pm_srz 
            WHERE pm_srz.post_id = p.ID 
            AND pm_srz.meta_key = 'srz_property' 
            AND pm_srz.meta_value = 'apartamente'
        )
        AND EXISTS (
            SELECT 1 FROM {$wpdb->postmeta} pm_sold 
            WHERE pm_sold.post_id = p.ID 
            AND pm_sold.meta_key = 'sold' 
            AND pm_sold.meta_value != '1'
        )
        AND EXISTS (
            SELECT 1 FROM {$wpdb->postmeta} pm_author 
            WHERE pm_author.post_id = p.ID 
            AND pm_author.meta_key = 'select_property_author' 
            AND pm_author.meta_value = %d
        )
    ";

    // Get total count
    $count_query = "SELECT COUNT(p.ID) " . $query;
    $total_posts = $wpdb->get_var($wpdb->prepare($count_query, $current_author_id));
    $total_pages = ceil($total_posts / $posts_per_page);

    // Final query to fetch results with pagination
    $final_query = "SELECT p.ID, p.post_title, pm_price.meta_value AS price " . $query . "
        ORDER BY CAST(pm_price.meta_value AS UNSIGNED) ASC
        LIMIT %d OFFSET %d
    ";

    $final_query = $wpdb->prepare($final_query, $current_author_id, $posts_per_page, $offset);
    $results = $wpdb->get_results($final_query);

    // Start output buffering
    ob_start();

    if (!empty($results)) {
        echo '<div class="apartamente-list">';
        foreach ($results as $post) {
            echo property_card($post->ID);
        }
        echo '</div>';

        // Pagination
        if ($total_pages > 1) {
            generate_pagination($total_pages, $paged);
        }
    } else {
        echo '<p>No apartments found.</p>';
    }

    return ob_get_clean();
}
add_shortcode('apartamente_author', 'get_apartamente_author');

function get_real_estate_listings($atts) {
    global $wpdb;

    // Define default values and allow users to pass parameters
    $atts = shortcode_atts([
        'type' => 'apartamente', // Default: 'apartamente'. Other values: 'revandute', 'vile', 'inchirieri'
        'numar_camere' => '',
        'zona' => '',
        'posts_per_page' => '',
        'filter' => '' // If 0, hide filters
    ], $atts, 'real_estate_listings');

    $type = sanitize_text_field($atts['type']);
    
    // Get filter values from URL parameters
    $zona = (!empty($atts['zona']) ? sanitize_text_field($atts['zona']) : null);
    if (isset($_GET['zona']) && $_GET['zona'] !== '') {
        $zona = ($_GET['zona'] === 'all') ? null : sanitize_text_field($_GET['zona']);
    }

    // Get numar_camere from attributes and URL
    $numar_camere = (isset($atts['numar_camere']) && $atts['numar_camere'] !== '') ? sanitize_text_field($atts['numar_camere']) : null;
    if (isset($_GET['numar_camere']) && $_GET['numar_camere'] !== '') {
        $numar_camere = ($_GET['numar_camere'] === 'all') ? null : sanitize_text_field($_GET['numar_camere']);
    }
    
    $min_price = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? intval($_GET['min_price']) : null;
    $max_price = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? intval($_GET['max_price']) : null;
    
    $min_surface = isset($_GET['min_surface']) && $_GET['min_surface'] !== '' ? intval($_GET['min_surface']) : null;
    $max_surface = isset($_GET['max_surface']) && $_GET['max_surface'] !== '' ? intval($_GET['max_surface']) : null;
    
    $order = isset($_GET['order']) && in_array($_GET['order'], ['ASC', 'DESC']) ? sanitize_text_field($_GET['order']) : 'ASC';

    $paged = max(1, get_query_var('paged', 1));
    $posts_per_page = isset($atts['posts_per_page']) && $atts['posts_per_page'] !== '' ? intval($atts['posts_per_page']) : 24;
    $offset = ($paged - 1) * $posts_per_page;

    // Base query suprafata_utila
    $query = "
        FROM {$wpdb->posts} p
        LEFT JOIN {$wpdb->postmeta} pm_price ON p.ID = pm_price.post_id AND pm_price.meta_key = 'priceSort'
        LEFT JOIN {$wpdb->postmeta} pm_surface ON p.ID = pm_surface.post_id AND pm_surface.meta_key = 'suprafata_utila'
        WHERE p.post_type = 'apartamente' 
        AND p.post_status = 'publish'
        AND EXISTS (
            SELECT 1 FROM {$wpdb->postmeta} pm_sold 
            WHERE pm_sold.post_id = p.ID 
            AND pm_sold.meta_key = 'sold' 
            AND pm_sold.meta_value != '1'
        )
    ";

    // Modify query based on type
    switch ($type) {
        case 'revandute':
            $query .= "
                AND EXISTS (
                    SELECT 1 FROM {$wpdb->postmeta} pm_srz 
                    WHERE pm_srz.post_id = p.ID 
                    AND pm_srz.meta_key = 'srz_property' 
                    AND pm_srz.meta_value = 'apartamente'
                )
                AND NOT EXISTS (
                    SELECT 1 FROM {$wpdb->postmeta} pm_special 
                    WHERE pm_special.post_id = p.ID 
                    AND pm_special.meta_key = 'proprietate_speciala' 
                    AND pm_special.meta_value = '1'
                )
                AND EXISTS (
                    SELECT 1 FROM {$wpdb->postmeta} pm_vanzare 
                    WHERE pm_vanzare.post_id = p.ID 
                    AND pm_vanzare.meta_key = 'vanzare_sau_inchiriere' 
                    AND pm_vanzare.meta_value != 'inchiriere'
                )
                AND EXISTS (
                    SELECT 1 FROM {$wpdb->postmeta} pm_stadiu 
                    WHERE pm_stadiu.post_id = p.ID 
                    AND pm_stadiu.meta_key = 'stadiu_constructie' 
                    AND pm_stadiu.meta_value = 'finalizat'
                )
            ";
            break;
        
        case 'vile':
            $query .= "
                AND NOT EXISTS (
                    SELECT 1 FROM {$wpdb->postmeta} pm_special 
                    WHERE pm_special.post_id = p.ID 
                    AND pm_special.meta_key = 'proprietate_speciala' 
                    AND pm_special.meta_value = '1'
                )
                AND EXISTS (
                    SELECT 1 FROM {$wpdb->postmeta} pm_srz 
                    WHERE pm_srz.post_id = p.ID 
                    AND pm_srz.meta_key = 'srz_property' 
                    AND pm_srz.meta_value = 'vila'
                )
                AND NOT EXISTS (
                    SELECT 1 FROM {$wpdb->postmeta} pm_structura 
                    WHERE pm_structura.post_id = p.ID 
                    AND pm_structura.meta_key = 'structura' 
                    AND pm_structura.meta_value LIKE '%casa_modulara%'
                )
                AND EXISTS (
                    SELECT 1 FROM {$wpdb->postmeta} pm_vanzare 
                    WHERE pm_vanzare.post_id = p.ID 
                    AND pm_vanzare.meta_key = 'vanzare_sau_inchiriere' 
                    AND pm_vanzare.meta_value = 'vanzare'
                )
            ";
            break;
        
        case 'modulare':
            $query .= "
                AND NOT EXISTS (
                    SELECT 1 FROM {$wpdb->postmeta} pm_special 
                    WHERE pm_special.post_id = p.ID 
                    AND pm_special.meta_key = 'proprietate_speciala' 
                    AND pm_special.meta_value = '1'
                )
                AND EXISTS (
                    SELECT 1 FROM {$wpdb->postmeta} pm_srz 
                    WHERE pm_srz.post_id = p.ID 
                    AND pm_srz.meta_key = 'srz_property' 
                    AND pm_srz.meta_value = 'vila'
                )
                AND EXISTS (
                    SELECT 1 FROM {$wpdb->postmeta} pm_structura 
                    WHERE pm_structura.post_id = p.ID 
                    AND pm_structura.meta_key = 'structura' 
                    AND pm_structura.meta_value LIKE '%casa_modulara%'
                )
                AND EXISTS (
                    SELECT 1 FROM {$wpdb->postmeta} pm_vanzare 
                    WHERE pm_vanzare.post_id = p.ID 
                    AND pm_vanzare.meta_key = 'vanzare_sau_inchiriere' 
                    AND pm_vanzare.meta_value = 'vanzare'
                )
            ";
            break;
        
        case 'speciale':
            $query .= "
                AND EXISTS (
                    SELECT 1 FROM {$wpdb->postmeta} pm_special 
                    WHERE pm_special.post_id = p.ID 
                    AND pm_special.meta_key = 'proprietate_speciala' 
                    AND pm_special.meta_value = '1'
                )
                AND EXISTS (
                    SELECT 1 FROM {$wpdb->postmeta} pm_vanzare 
                    WHERE pm_vanzare.post_id = p.ID 
                    AND pm_vanzare.meta_key = 'vanzare_sau_inchiriere' 
                    AND pm_vanzare.meta_value != 'inchiriere'
                )
            ";
            break;
        
        case 'inchirieri':
            $query .= "
                AND EXISTS (
                    SELECT 1 FROM {$wpdb->postmeta} pm_vanzare 
                    WHERE pm_vanzare.post_id = p.ID 
                    AND pm_vanzare.meta_key = 'vanzare_sau_inchiriere' 
                    AND pm_vanzare.meta_value = 'inchiriere'
                )
            ";
            break;
        
        case 'promo':
            $query .= "
                AND EXISTS (
                    SELECT 1 FROM {$wpdb->postmeta} pm_pret_promo 
                    WHERE pm_pret_promo.post_id = p.ID 
                    AND pm_pret_promo.meta_key = 'pret_promo' 
                    AND pm_pret_promo.meta_value = '1'
                )
            ";
            break;
        
        default: // 'apartamente'
            $query .= "
                AND EXISTS (
                    SELECT 1 FROM {$wpdb->postmeta} pm_srz 
                    WHERE pm_srz.post_id = p.ID 
                    AND pm_srz.meta_key = 'srz_property' 
                    AND pm_srz.meta_value = 'apartamente'
                )
                AND EXISTS (
                    SELECT 1 FROM {$wpdb->postmeta} pm_vanzare 
                    WHERE pm_vanzare.post_id = p.ID 
                    AND pm_vanzare.meta_key = 'vanzare_sau_inchiriere' 
                    AND pm_vanzare.meta_value = 'vanzare'
                )
            ";
            break;
    }

    // Add optional filters
    if (!is_null($numar_camere)) {
        $query .= $wpdb->prepare(" AND EXISTS (SELECT 1 FROM {$wpdb->postmeta} pm_camere WHERE pm_camere.post_id = p.ID AND pm_camere.meta_key = 'numar_camere' AND pm_camere.meta_value = %s)", $numar_camere);
    }
if (!is_null($zona)) {
    // Split the comma-separated string and trim each term.
    $zonas = array_map('trim', explode(',', $zona));
    
    // Create a string of placeholders like "%s, %s, %s" based on how many terms there are.
    $placeholders = implode(', ', array_fill(0, count($zonas), '%s'));
    
    // Build the query using the IN clause and pass the terms safely.
    $query .= $wpdb->prepare(
        " AND EXISTS (
            SELECT 1 FROM {$wpdb->term_relationships} tr
            INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
            INNER JOIN {$wpdb->terms} t ON tt.term_id = t.term_id
            WHERE tt.taxonomy = 'zona'
              AND t.slug IN ($placeholders)
              AND tr.object_id = p.ID
        )",
        ...$zonas  // Using the splat operator (PHP 5.6+)
    );
}

    if (!is_null($min_price)) {
        $query .= $wpdb->prepare(" AND CAST(pm_price.meta_value AS UNSIGNED) >= %d", $min_price);
    }
    if (!is_null($max_price)) {
        $query .= $wpdb->prepare(" AND CAST(pm_price.meta_value AS UNSIGNED) <= %d", $max_price);
    }

    if (!is_null($min_surface)) {
        $query .= $wpdb->prepare(" AND CAST(pm_surface.meta_value AS UNSIGNED) >= %d", $min_surface);
    }
    if (!is_null($max_surface)) {
        $query .= $wpdb->prepare(" AND CAST(pm_surface.meta_value AS UNSIGNED) <= %d", $max_surface);
    }

    // Get total count
    $count_query = "SELECT COUNT(p.ID) " . $query;
    $total_posts = $wpdb->get_var($count_query);
    $total_pages = ceil($total_posts / $posts_per_page);

    // Final query with pagination
    $final_query = "SELECT p.ID, p.post_title, pm_price.meta_value AS price " . $query . "
        ORDER BY CAST(pm_price.meta_value AS UNSIGNED) " . esc_sql($order) . "
        LIMIT %d OFFSET %d
    ";
    $final_query = $wpdb->prepare($final_query, $posts_per_page, $offset);
    $results = $wpdb->get_results($final_query);
    
    
    //var_dump($final_query);
    // Start output buffering
    ob_start();

    // Filter form (only if not disabled)
    if (!isset($atts['filter']) || $atts['filter'] !== '0') {
        include 'property-filter-form.php'; // External file to include the form (better for code organization)
    }

    if (!empty($results)) { 
        echo '<div class="apartamente-list">';
        foreach ($results as $post) {
            echo property_card($post->ID);
        }
        echo '</div>';

        // Pagination
        if ($total_pages > 1) {
            generate_pagination($total_pages, $paged);
        }
    } else {
        echo '<p>No properties found.</p>';
    }

    return ob_get_clean();
}

// Register a single shortcode
add_shortcode('real_estate_listings', 'get_real_estate_listings');

//[real_estate_listings type="apartamente"]
//[real_estate_listings type="revandute"]
//[real_estate_listings type="vile"]
//[real_estate_listings type="inchirieri"]
//[real_estate_listings type="modulare"]
//[real_estate_listings type="speciale"]
//[real_estate_listings type="promo"]

function related_listings() {
    global $wpdb, $post;

    if (!$post) {
        return '<p>No properties found.</p>';
    }

    $current_post_id = $post->ID;

    // Get the current post's 'srz_property' meta value
    $current_srz_property = get_post_meta($current_post_id, 'srz_property', true);

    if (!$current_srz_property) {
        return '<p>No related properties found.</p>';
    }

    $posts_per_page = 3;

    // Query to find related posts with the same 'srz_property' value, sorted by date
    $query = "
        SELECT p.ID, p.post_title, p.post_date
        FROM {$wpdb->posts} p
        WHERE p.post_type = 'apartamente' 
        AND p.post_status = 'publish'
        AND EXISTS (
            SELECT 1 FROM {$wpdb->postmeta} pm_sold 
            WHERE pm_sold.post_id = p.ID 
            AND pm_sold.meta_key = 'sold' 
            AND pm_sold.meta_value != '1'
        )
        AND EXISTS (
            SELECT 1 FROM {$wpdb->postmeta} pm_srz 
            WHERE pm_srz.post_id = p.ID 
            AND pm_srz.meta_key = 'srz_property' 
            AND pm_srz.meta_value = %s
        )
        AND p.ID != %d
        ORDER BY p.post_date DESC
        LIMIT %d
    ";

    $final_query = $wpdb->prepare($query, $current_srz_property, $current_post_id, $posts_per_page);
    $results = $wpdb->get_results($final_query);

    // Start output buffering
    ob_start();

    if (!empty($results)) {
        echo '<div class="apartamente-list">';
        foreach ($results as $post) {
            echo '<div class="swiper-slide">';
            echo property_card($post->ID);
            echo '</div>';
        }
        echo '</div>';
    } else {
        echo '<p>No related properties found.</p>';
    }

    return ob_get_clean();
}

add_shortcode('related_listings', 'related_listings');