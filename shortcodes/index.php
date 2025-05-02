<?php
include_once 'property_card.php';
function adauga_proprietate_enqueue_assets() {
    // Register the CSS and JS files
    wp_register_style(
        'adauga-proprietate-css',
        get_stylesheet_directory_uri() . '/shortcodes/adauga-proprietate.css',
        array(),
        '1.0'
    );

    wp_register_script(
        'adauga-proprietate-js',
        get_stylesheet_directory_uri() . '/shortcodes/adauga-proprietate.js',
        array('jquery'), // Include jQuery if needed
        '1.0',
        true // Load in the footer
    );

    // Localize the script to pass the AJAX URL
    wp_localize_script('adauga-proprietate-js', 'ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php'),
    ));

    // Conditionally enqueue assets only when the shortcode is used
    if (is_page('adauga-proprietate')) { // Replace with the page slug where shortcode is used
        wp_enqueue_style('adauga-proprietate-css');
        wp_enqueue_script('adauga-proprietate-js');

        // Select2
        wp_enqueue_style('select2-css', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/css/select2.min.css');
        wp_enqueue_script('select2-js', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/js/select2.min.js', array('jquery'), null, true);
    }
}
add_action('wp_enqueue_scripts', 'adauga_proprietate_enqueue_assets');


function enqueue_media_uploader() {
    if (is_page('adauga-proprietate')) { // Replace with the page slug where shortcode is used
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_media(); // WordPress Media Uploader

        // Custom script for handling media uploads
        wp_enqueue_script(
            'custom-media-script',
            get_stylesheet_directory_uri() . '/shortcodes/media-uploader.js',
            array('jquery', 'jquery-ui-sortable', 'media-editor'), // Dependencies
            null,
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'enqueue_media_uploader');


function adauga_proprietate_shortcode_with_assets() {
    // Enqueue the assets
    wp_enqueue_style('adauga-proprietate-css');
    wp_enqueue_script('adauga-proprietate-js');

    // Render the form
    ob_start();
    include_once 'edit-add-property.php';
    return ob_get_clean();
}
add_shortcode('adauga_proprietate', 'adauga_proprietate_shortcode_with_assets');


function property_dashboard() {
    ob_start();
    include_once 'dashboard.php';
    return ob_get_clean();
}
add_shortcode('property_dashboard', 'property_dashboard');

 

function handle_adauga_proprietate() {
    // Verify nonce for security
    if (!isset($_POST['adauga_proprietate_nonce_field']) || 
        !wp_verify_nonce($_POST['adauga_proprietate_nonce_field'], 'adauga_proprietate_nonce_action')) {
        wp_send_json_error(array('message' => 'Nonce verification failed.'));
    }

    // Validate the required fields
    if (!isset($_POST['srz_property']) || empty($_POST['srz_property'])) {
        wp_send_json_error(array('message' => 'Tipul proprietății este obligatoriu.'));
    }

    // Check if we're creating or updating a post
    $editAdd = isset($_POST['editAdd']) ? intval($_POST['editAdd']) : 0;

    if ($editAdd === 1) {
        // Update existing post
        if (!isset($_POST['post_id']) || empty($_POST['post_id'])) {
            wp_send_json_error(array('message' => 'ID-ul postării este necesar pentru actualizare.'));
        }

        $post_id = intval($_POST['post_id']);
        $post = get_post($post_id);

        if (!$post || $post->post_type !== 'apartamente') {
            wp_send_json_error(array('message' => 'Postarea specificată nu există sau nu este validă.'));
        }

        // Update the post title if provided
        if (isset($_POST['title'])) {
            wp_update_post(array(
                'ID'         => $post_id,
                'post_title' => sanitize_text_field($_POST['title']),
            ));
        }
    } else {
        // Create a new post
        $post_id = wp_insert_post(array(
            'post_title'  => sanitize_text_field($_POST['title'] ?? 'New Property'),
            'post_type'   => 'apartamente',
            'post_status' => 'publish',
        ));

        if (!$post_id) {
            wp_send_json_error(array('message' => 'A apărut o eroare la salvarea proprietății.'));
        }
    }

    // List of fields to save
    $fields = array(
        'srz_property' => 'text',
        'proprietate_speciala' => 'boolean',
        'sold' => 'boolean',
        'propertyPhone' => 'string',
        'anunt_recomandat' => 'boolean',
        'select_property_author' => 'int',
        'vanzare_inchiriere' => 'text',
        'pret' => 'float',
        'currency' => 'text',
        'mod_plata' => 'text',
        'tip_pret' => 'text',
        'pret_promo' => 'boolean',
        'tip_promotie' => 'boolean',
        'pret_promotional' => 'float',
        'conditie_promotionala' => 'text',
        'selecteaza_ansamblu' => 'int',
        'reper' => 'text',
        'zona' => 'array',
        'magazin_interes' => 'float',
        'magazin_interes_copy' => 'text',
        'transport_interes' => 'float',
        'transport_interes_copy' => 'text',
        'scoala_interes' => 'float',
        'scoala_interes_copy' => 'text',
        'cafenea_interes' => 'float',
        'cafenea_interes_copy' => 'text',
        'farmacie_interes' => 'float',
        'farmacie_interes_copy' => 'text',
        'centru_interes' => 'float',
        'centru_interes_copy' => 'text',
        'video' => 'text',
        'numar_camere' => 'float',
        'casa_este_in_showroom' => 'boolean',
        'numar_dormitoare' => 'float',
        'etichete' => 'text',
        'compartimentare' => 'text',
        'stadiu_constructie' => 'text',
        'suprafata_utila' => 'float',
        'suprafata_total_utila' => 'float',
        'suprafata_construita' => 'float',
        'numar_balcoane' => 'int',
        'an_constructie' => 'int',
        'numar_grupuri_sanitare' => 'int',
        'etaj' => 'text',
        'regim_inaltime' => 'text',
        'suprafata_teren' => 'float',
        'locuri_de_parcare' => 'int',
        'latime_vitrina' => 'float',
        'inaltime_spatiu_comercial' => 'float',
        'certificat_urbanism' => 'boolean',
        'puz' => 'boolean',
        'categorie_teren' => 'text',
        'compartimentari' => 'array',
        'tip_imobil' => 'array',
        'finisajeTextarea' => 'text',
        'structura' => 'array',
        'bucatarie' => 'array',
        'destinatie' => 'array',
        'clasa_energetica' => 'text',
        'incalzire' => 'array',
        'acces' => 'array',
        'utilitati' => 'array',
        'vecinatati' => 'array',
    );

// Save the meta fields
foreach ($fields as $key => $type) {
    if (isset($_POST[$key])) {
        $value = wp_unslash($_POST[$key]);

        // Sanitize based on type
        switch ($type) {
            case 'int':
                $value = intval($value);
                break;
            case 'boolean':
                $value = $value ? 1 : 0;
                break;
            case 'array':
                $value = array_map('sanitize_text_field', (array) $value);
                break;
            case 'float':
                $value = str_replace(',', '.', $value); // Handle European decimals
                $value = floatval($value);
                break;
            case 'text':
            case 'string':
                $value = sanitize_text_field($value);
                break;
            default:
                error_log("Unexpected field type for $key: $type");
                break;
        }

        // Update post meta with the sanitized value
        update_post_meta($post_id, $key, $value);
    } else {
        // If the field is not set in $_POST, delete it from post meta
        delete_post_meta($post_id, $key);
    }
}


    // Handle WYSIWYG editors
    $editor_fields = array('prezentare', 'pret_si_modalitati_de_plata', 'localizare_si_vecinatati');
    foreach ($editor_fields as $field) {
        if (isset($_POST[$field])) {
        // Remove curly quotes
        $clean_content = str_replace(
            array('“', '”', '‘', '’'), // Curly quotes to remove
            '',                        // Replace with nothing (removal)
            $_POST[$field]
        );

        // Save sanitized content to the database
        update_post_meta($post_id, $field, wp_kses_post($clean_content));
        }
    }

    // Handle gallery fields
    $gallery_fields = array('galerie_imagini', 'galerie_schite');
    foreach ($gallery_fields as $field) {
        if (isset($_POST[$field])) {
            $gallery_ids = array_map('intval', explode(',', $_POST[$field]));
            update_post_meta($post_id, $field, $gallery_ids);
        }
    }
    
    
    // Handle zona taxonomy (custom taxonomy)
    if (isset($_POST['zona'])) {
        $zona_term_ids = array_map('intval', (array) $_POST['zona']); // Convert to integers
    
        if (!empty($zona_term_ids)) {
            // Assign the selected terms
            wp_set_post_terms($post_id, $zona_term_ids, 'zona', false);
        } else {
            // Remove all terms if an empty array is provided
            wp_set_post_terms($post_id, array(), 'zona', false);
        }
    }else{
        wp_set_post_terms($post_id, array(), 'zona', false);
    }



    add_missing_priceSort($post_id);
    // Send success response
    $message = $editAdd === 1 ? 'Proprietatea a fost actualizată cu succes!' : 'Proprietatea a fost adăugată cu succes!';
    wp_send_json_success(array('message' => $message, 'post_id' => $post_id));
}
add_action('wp_ajax_adauga_proprietate', 'handle_adauga_proprietate');




function add_missing_priceSort($post_id) {
    
    // Get the post type
    $post_type = get_post_type($post_id);
    
    // Check if the post type matches the specified type
    if ($post_type === 'apartamente') {

        $pret = get_post_meta($post_id, 'pret', true);
        $pret_promo = get_post_meta($post_id, 'pret_promo', true);
        $tip_promotie = get_post_meta($post_id, 'tip_promotie', true);
        $pret_promotional = get_post_meta($post_id, 'pret_promotional', true);

        // Ensure the values are numeric
        $pret = (float) str_replace(',', '', $pret);
        $pret_promotional = (float) str_replace(',', '', $pret_promotional);

        // Apply promotional price logic
        if ($pret_promo == 1 && $tip_promotie == 0 && !empty($pret_promotional) && $pret_promotional < $pret) {
            $pret = $pret_promotional; 
        }

        // Convert to integer (sorting requires whole numbers)
        $sanitized_price = (int) $pret;
        
        // Update the meta field
        update_post_meta($post_id, 'priceSort', $sanitized_price);
    }
}

// Add this to your theme's functions.php or a custom plugin
function anunturi_proiect_shortcode($atts) {
    ob_start();
    
    $atts = shortcode_atts( array(
        'ansamblu' => '',
    ), $atts, 'anunturi_proiect' );

    $ansamblu = $atts['ansamblu'];
    if (empty($ansamblu) && isset($_GET["ansamblu"])) {
        $ansamblu = $_GET["ansamblu"];
    }

    if (empty($ansamblu)) {
        $ansamblu = '';
    }

    $ansamblu_title = get_the_title($ansamblu);
    $descriere_scurta = get_post_meta($ansamblu, 'descriere_scurta');

    ?>
    <div class="archive-title">
        <div class="container">
            <h1><?php echo esc_html($ansamblu_title); ?></h1>
            <?php echo apply_filters('the_content', $descriere_scurta); ?>
        </div>
    </div>
     
                                <?php
                                $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

                                $args = array(
                                    'posts_per_page' => 99,
                                    'post_type'      => 'apartamente',
                                    'meta_query'     => array(
                                        'relation' => 'AND',
                                        array(
                                            'key'     => 'selecteaza_ansamblu',
                                            'value'   => $ansamblu,
                                            'compare' => 'LIKE',
                                        ),
                                        array(
                                            'key'     => 'sold',
                                            'value'   => '1',
                                            'compare' => '!=',
                                        ),
                                    ),
                                    'paged'          => $paged,
                                );

                                $the_query = new WP_Query($args);

                                if ($the_query->have_posts()) : ?>
                                    <div class="apartamente-list">
                                        <?php
                                        while ($the_query->have_posts()) : $the_query->the_post();
                                            echo property_card(get_the_ID());
                                        endwhile;
                                        ?>
                                    </div>
                                <?php else : ?>
                                    <p>No properties found.</p>
                                <?php endif;

                                wp_reset_postdata();
                                ?> 
                        
    <?php

    return ob_get_clean();
}
add_shortcode('anunturi_proiect', 'anunturi_proiect_shortcode');
