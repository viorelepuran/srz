<?php
// Add custom action links in the admin menu for each post type
function add_migration_buttons_to_admin_view($which) {
    if (get_current_screen()->id === 'edit-apartamente' && $which === 'top') {
        $base_url = admin_url('edit.php?post_type=apartamente');
        echo '<a href="' . esc_url($base_url . '&start_migration=apartamente') . '" class="button button-primary">Start Migration of Apartamente</a>';
        echo '<a href="' . esc_url($base_url . '&start_migration=vile') . '" class="button button-secondary">Start Migration of Vile</a>';
        echo '<a href="' . esc_url($base_url . '&start_migration=spatii-comerciale') . '" class="button button-secondary">Start Migration of Spatii-Comerciale</a>';
    }
}
add_action('manage_posts_extra_tablenav', 'add_migration_buttons_to_admin_view');

// Process the migration when any button is clicked
function handle_individual_migration() {
    if (isset($_GET['post_type']) && $_GET['post_type'] === 'apartamente' && isset($_GET['start_migration'])) {
        $post_type_to_migrate = sanitize_text_field($_GET['start_migration']);
        
        if (in_array($post_type_to_migrate, ['apartamente', 'vile', 'spatii-comerciale'])) {
            migrate_posts_with_custom_field([$post_type_to_migrate]);
            
            // Redirect back to the post list with a completed notice
            wp_redirect(admin_url('edit.php?post_type=apartamente&migration=completed&type=' . $post_type_to_migrate));
            exit;
        }
    }
}
add_action('admin_init', 'handle_individual_migration');

// Display a notice when migration is completed
function show_individual_migration_completed_notice() {
    if (isset($_GET['migration']) && $_GET['migration'] === 'completed' && isset($_GET['type'])) {
        $migrated_type = esc_html($_GET['type']);
        echo '<div class="notice notice-success is-dismissible"><p>Migration of ' . ucfirst($migrated_type) . ' completed successfully!</p></div>';
    }
}
add_action('admin_notices', 'show_individual_migration_completed_notice');

// Migrate posts with custom fields function (same as before)
function migrate_posts_with_custom_field($post_types_to_migrate) {
    $batch_size = 100;

    foreach ($post_types_to_migrate as $post_type) {
        $offset = 0;
        
        do {
            $args = [
                'post_type'      => $post_type,
                'posts_per_page' => $batch_size,
                'post_status'    => 'any',
                'offset'         => $offset,
                'meta_query'     => [
                    [
                        'key'     => '_migrated',
                        'compare' => 'NOT EXISTS',
                    ],
                ],
            ];
            $posts = get_posts($args);

            foreach ($posts as $post) {
                // Add your migration logic here (same as original code)
                $tipul_proprietatii = get_post_meta($post->ID, 'tipul_proprietatii', true);
                
                if (is_array($tipul_proprietatii)) {
                    $tipul_proprietatii = reset($tipul_proprietatii);
                }
                $regim_inaltime = get_post_meta($post->ID, 'inaltime_imobil', true);
                
                // Post type specific migrations
                if ($post_type === 'spatii-comerciale') {
                    update_post_meta($post->ID, 'proprietate_speciala', '1');

                    switch ($tipul_proprietatii) {
                        case 'spatiu':
                            $suprafata_totala = get_post_meta($post->ID, 'suprafata_totala', true);
                            $tip_spatiu = get_post_meta($post->ID, 'tip_spatiu', true);
                            update_post_meta($post->ID, 'regim_inaltime', $regim_inaltime);
                            update_post_meta($post->ID, 'suprafata_total_utila', $suprafata_totala);
                            update_post_meta($post->ID, 'tip_imobil', $tip_spatiu);
                            update_post_meta($post->ID, 'srz_property', 'spatiu_comercial');
                            break;

                        case 'vile':
                            update_post_meta($post->ID, 'regim_inaltime', $regim_inaltime);
                            update_post_meta($post->ID, 'srz_property', 'vila');
                            break;

                        case 'apartament':
                            update_post_meta($post->ID, 'regim_inaltime', $regim_inaltime);
                            update_post_meta($post->ID, 'srz_property', 'apartamente');
                            break;

                        case 'cladire':
                            $deschidere = get_post_meta($post->ID, 'deschidere', true);
                            update_post_meta($post->ID, 'regim_inaltime', $regim_inaltime);
                            update_post_meta($post->ID, 'latime_vitrina', $deschidere);
                            update_post_meta($post->ID, 'srz_property', 'cladire');
                            break;

                        case 'teren':
                            $deschidere = get_post_meta($post->ID, 'deschidere', true);
                            update_post_meta($post->ID, 'latime_vitrina', $deschidere);
                            update_post_meta($post->ID, 'srz_property', 'teren');
                            break;
                    }
                    update_post_meta($post->ID, 'propertyPhone', 'callCenterProprietati');
                    // Spatii-commercial specific logic
                }

                if ($post_type === 'apartamente') {
                    update_post_meta($post->ID, 'regim_inaltime', $regim_inaltime);
                    update_post_meta($post->ID, 'srz_property', 'apartamente');
                    update_post_meta($post->ID, 'propertyPhone', 'callCenterPhone');
                }

                if ($post_type === 'vile') {
                    update_post_meta($post->ID, 'srz_property', 'vila');
                    update_post_meta($post->ID, 'propertyPhone', 'callCenterPhone');
                }

                // Process repeater fields for 'etichete' and 'labels'
                $repeater_count = intval(get_post_meta($post->ID, 'etichete', true));
                $labels_count = intval(get_post_meta($post->ID, 'labels', true));

                $combined_values = [];

                // Process 'etichete' repeater field
                if ($repeater_count > 0) {
                    for ($i = 0; $i < $repeater_count; $i++) {
                        $field_key = "etichete_{$i}_eticheta";
                        $value = get_post_meta($post->ID, $field_key, true);
                        if (!empty($value)) {
                            $combined_values[] = $value;
                        }
                    }
                }

                // Process 'labels' repeater field
                if ($labels_count > 0) {
                    for ($i = 0; $i < $labels_count; $i++) {
                        $field_key = "labels_{$i}_label";
                        $value = get_post_meta($post->ID, $field_key, true);
                        if (!empty($value)) {
                            $combined_values[] = $value;
                        }
                    }
                }

                // Combine and save to 'etichete_labels_combined' meta field
                if (!empty($combined_values)) {
                    $combined_string = implode(', ', $combined_values);
                    update_post_meta($post->ID, 'etichete', $combined_string);
                }
                
                $vanzare_sau_inchiriere = get_post_meta($post->ID, 'vanzare_sau_inchiriere', true);
                if ($vanzare_sau_inchiriere === 'inchiriere') {
                    $pret_inchiriere = get_post_meta($post->ID, 'pret_inchiriere', true);
                    update_post_meta($post->ID, 'pret', $pret_inchiriere);
                    update_post_meta($post->ID, 'propertyPhone', 'callCenterPhone');
                }
                
                
                $tva = get_post_meta($post->ID, 'tva', true);
                if($tva === 'Nu se aplica TVA' || $tva === 'Fara TVA'){
                    update_post_meta($post->ID, 'tip_pret', 'nuseaplica');
                }else{
                    update_post_meta($post->ID, 'tip_pret', 'plustva');
                }
                
                $revanzare = get_post_meta($post->ID, 'revanzare', true);
                if($revanzare === '1' && $vanzare_sau_inchiriere === 'vanzare'){
                    update_post_meta($post->ID, 'tip_pret', 'nuseaplica');
                }
                
                $numar_camere = get_post_meta($post->ID, 'numar_camere', true);
                if($numar_camere === '0'){
                    update_post_meta($post->ID, 'numar_camere', '1');
                }
                
                // Change post type if necessary
                if ($post_type !== 'apartamente') {
                    $updated_post = [
                        'ID'        => $post->ID,
                        'post_type' => 'apartamente',
                    ];
                    wp_update_post($updated_post);
                }
                // Mark as migrated
                update_post_meta($post->ID, '_migrated', '1');
            }
            $offset += $batch_size;
        } while (count($posts) === $batch_size);
    }
}
