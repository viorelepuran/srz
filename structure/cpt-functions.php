<?php
// Modify the columns for the 'Apartamente' post type
function modify_apartamente_columns($columns) {
    unset($columns['taxonomy-zona']);
    unset($columns['date']);

    $columns['tip_proprietate'] = 'Tip Proprietate';
    $columns['status'] = 'Status';
    $columns['agent'] = 'Agent';
    $columns['pret'] = 'Pret';

    return $columns;
}
add_filter('manage_apartamente_posts_columns', 'modify_apartamente_columns');

// Populate the custom columns
function populate_apartamente_columns($column, $post_id) {
    if ($column === 'tip_proprietate') {
        $tip_proprietate = get_post_meta($post_id, 'srz_property', true);
        echo esc_html($tip_proprietate ?: 'N/A');
    }

    if ($column === 'status') {
        $status = get_post_meta($post_id, 'sold', true);
        echo esc_html($status == '1' ? 'Vandut' : 'Disponibil');
    }

    if ($column === 'agent') {
        $agent_id = get_post_field('post_author', $post_id);
        if ($agent_id) {
            $user_info = get_userdata($agent_id);
            $display_name = $user_info->display_name;
            $image_id = get_user_meta($agent_id, 'imagine', true);
            $image_url = wp_get_attachment_image_url($image_id, 'thumbnail');

            echo '<div style="display: flex;align-items: flex-start;flex-direction: column;">';
            if ($image_url) {
                echo '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($display_name) . '" style="width: 100px; height: 70px; border-radius: 3px; object-fit: cover; object-position: top center;">';
            }
            echo esc_html($display_name);
            echo '</div>';
        } else {
            echo 'N/A';
        }
    }

    if ($column === 'pret') {
        echo 'TBD';
    }
}
add_action('manage_apartamente_posts_custom_column', 'populate_apartamente_columns', 10, 2);

// Make the custom columns sortable
function make_apartamente_columns_sortable($columns) {
    $columns['tip_proprietate'] = 'tip_proprietate';
    $columns['status'] = 'status';
    $columns['pret'] = 'pret';
    return $columns;
}
add_filter('manage_edit-apartamente_sortable_columns', 'make_apartamente_columns_sortable');

function add_apartamente_filters() {
    global $typenow;
    if ($typenow === 'apartamente') {
        // Enqueue Select2 CSS and JS
        wp_enqueue_style('select2-css', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css');
        wp_enqueue_script('select2-js', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js', ['jquery'], null, true);

        // Filter by post_author
        $selected_author = isset($_GET['author']) ? $_GET['author'] : '';
        ?>
        <select id="author-select" name="author" style="width: 200px;">
            <option value=""><?php esc_html_e('All Authors', 'textdomain'); ?></option>
            <?php
            $users = get_users(['role__in' => ['vanzari']]); // Adjust roles as needed
            foreach ($users as $user) {
                printf(
                    '<option value="%s" %s>%s</option>',
                    esc_attr($user->ID),
                    selected($selected_author, $user->ID, false),
                    esc_html($user->display_name)
                );
            }
            ?>
        </select>
        <?php

        // Filter by status
        $status_filter = isset($_GET['status']) ? $_GET['status'] : '';
        echo '<select name="status">';
        echo '<option value="">' . esc_html__('All Statuses', 'textdomain') . '</option>';
        echo '<option value="1"' . selected($status_filter, '1', false) . '>' . esc_html__('Vandut', 'textdomain') . '</option>';
        echo '<option value="0"' . selected($status_filter, '0', false) . '>' . esc_html__('Disponibil', 'textdomain') . '</option>';
        echo '</select>';

        // Filter by tip_proprietate
        $tip_proprietate_filter = isset($_GET['srz_property']) ? $_GET['srz_property'] : '';
        $tip_proprietate_values = ['vile', 'spatii-comerciale', 'apartamente']; // Add your property types here
        echo '<select name="srz_property">';
        echo '<option value="">' . esc_html__('All Property Types', 'textdomain') . '</option>';
        foreach ($tip_proprietate_values as $value) {
            echo '<option value="' . esc_attr($value) . '"' . selected($tip_proprietate_filter, $value, false) . '>' . esc_html($value) . '</option>';
        }
        echo '</select>';
        ?>

        <script>
        jQuery(document).ready(function($) {
            // Initialize Select2 for author dropdown
            $('#author-select').select2({
                placeholder: "<?php esc_html_e('Select an Author', 'textdomain'); ?>",
                allowClear: true,
            });
        });
        </script>
        <?php
    }
}
add_action('restrict_manage_posts', 'add_apartamente_filters');

// Modify query based on filters
function filter_apartamente_query($query) {
    global $pagenow;
    if ($pagenow === 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] === 'apartamente') {
        // Filter by status
        if (!empty($_GET['status'])) {
            $query->query_vars['meta_query'][] = [
                'key'   => 'sold',
                'value' => $_GET['status'],
            ];
        }

        // Filter by tip_proprietate
        if (!empty($_GET['srz_property'])) {
            $query->query_vars['meta_query'][] = [
                'key'   => 'srz_property',
                'value' => $_GET['srz_property'],
            ];
        }
    }
}
add_action('pre_get_posts', 'filter_apartamente_query');
