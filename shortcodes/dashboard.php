<?php
if (is_user_in_allowed_roles()):
    global $wpdb;

    // Get current user data
    $current_user = wp_get_current_user();
    $current_user_id = get_current_user_id();
    $current_user_roles = $current_user->roles;

    // Check if the current user has the 'vanzari' role
    $is_vanzari = in_array('vanzari', $current_user_roles);

    // Determine the current page and offset
    $posts_per_page = 20;
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    $offset = ($paged - 1) * $posts_per_page;

    // Get filter values (sanitize inputs)
    $srz_property = isset($_GET['srz_property']) ? sanitize_text_field($_GET['srz_property']) : '';
    $post_status = isset($_GET['post_status']) ? sanitize_text_field($_GET['post_status']) : '';
    $property_author = isset($_GET['select_property_author']) ? intval($_GET['select_property_author']) : 0;

// Base query
$sql = "SELECT SQL_CALC_FOUND_ROWS p.* FROM {$wpdb->posts} p
        WHERE p.post_type = %s";

// Query parameters array
$query_params = ['apartamente'];

// Filter by post status
if (!empty($post_status) && in_array($post_status, ['draft', 'publish'])) {
    $sql .= " AND p.post_status = %s";
    $query_params[] = $post_status;
} else {
    $sql .= " AND p.post_status IN ('draft', 'publish')";
}

// Filter by srz_property (Avoiding LEFT JOIN duplication)
if (!empty($srz_property) && in_array($srz_property, ['apartamente', 'vila', 'spatiu_comercial', 'teren', 'cladire'])) {
    $sql .= " AND p.ID IN (
                SELECT post_id FROM {$wpdb->postmeta}
                WHERE meta_key = 'srz_property' AND meta_value = %s
             )";
    $query_params[] = $srz_property;
}

// If user is 'vanzari', limit to their posts
if ($is_vanzari) {
    $sql .= " AND p.post_author = %d";
    $query_params[] = $current_user_id;
} elseif (!empty($property_author)) {
    // Filter by specific author if admin selects one
    $sql .= " AND p.post_author = %d";
    $query_params[] = $property_author;
}

// Apply pagination correctly
$sql .= " ORDER BY p.post_date DESC LIMIT %d OFFSET %d";
array_push($query_params, $posts_per_page, $offset);

// Prepare and execute query
$prepared_sql = $wpdb->prepare($sql, ...$query_params);
$posts = $wpdb->get_results($prepared_sql);

// Get total number of posts for pagination
$total_posts = $wpdb->get_var("SELECT FOUND_ROWS()");
if(user_can_manage()):?>
<form method="GET" action="">
    <div style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">

        <!-- Property Type Filter -->
        <label for="srz_property">Tip Proprietate:</label>
        <select name="srz_property" id="srz_property">
            <option value="">Toate</option>
            <option value="apartamente" <?php selected($_GET['srz_property'] ?? '', 'apartamente'); ?>>Apartamente</option>
            <option value="vila" <?php selected($_GET['srz_property'] ?? '', 'vila'); ?>>Vila</option>
            <option value="spatiu_comercial" <?php selected($_GET['srz_property'] ?? '', 'spatiu_comercial'); ?>>Spatiu Comercial</option>
            <option value="teren" <?php selected($_GET['srz_property'] ?? '', 'teren'); ?>>Teren</option>
            <option value="cladire" <?php selected($_GET['srz_property'] ?? '', 'cladire'); ?>>Cladire</option>
        </select>

        <!-- Post Status Filter -->
        <label for="post_status">Status:</label>
        <select name="post_status" id="post_status">
            <option value="">Toate</option>
            <option value="publish" <?php selected($_GET['post_status'] ?? '', 'publish'); ?>>Published</option>
            <option value="draft" <?php selected($_GET['post_status'] ?? '', 'draft'); ?>>Draft</option>
        </select>

        <!-- Property Author Filter -->
        <?php
        // Get users with the 'vanzari' role
        $vanzari_users = get_users(['role' => 'vanzari']);
        ?>
        <label for="select_property_author">Agent:</label>
        <select name="select_property_author" id="select_property_author">
            <option value="">Toti</option>
            <?php foreach ($vanzari_users as $user): ?>
                <option value="<?php echo esc_attr($user->ID); ?>" <?php selected($_GET['select_property_author'] ?? '', $user->ID); ?>>
                    <?php echo esc_html($user->display_name); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <!-- Submit Button -->
        <button type="submit">Filter</button>
    </div>
</form>

<?php
endif;
// Display the posts
if (!empty($posts)) {
    echo '<table class="agentDashboartTable">
        <thead>
            <tr>
                <th>Titlu</th>
                <th>Tip Proprietate</th>
                <th>Pret</th>
                <th>Status</th>';
                if(user_can_manage()){echo '<th>Agent</th>';}
                echo '<th>Actiuni</th>
            </tr>
        </thead>
        <tbody>';
    foreach ($posts as $post) {
        $post_id = $post->ID;
        $property_meta = get_post_meta($post->ID, 'srz_property', true);
        $formatted_property = ucfirst(str_replace('_', ' ', $property_meta));
        $pret = get_meta($post_id, 'pret');
        $vat = get_vat_label(get_meta($post_id, 'tip_pret'));
        $vanzare_inchiriere = get_meta($post_id, 'vanzare_sau_inchiriere');
        $mod_plata = $vanzare_inchiriere === 'inchiriere' ? '/' . get_meta($post_id, 'mod_plata') : '';
        $pret_promo = get_meta($post_id, 'pret_promo', 0);
        $tip_promotie = get_meta($post_id, 'tip_promotie', 0);
        $pret_promotional = get_meta($post_id, 'pret_promotional');
        $sold = get_meta($post_id, 'sold', 0);
        $disponibilitate = 'Disponibil';
        
        $soldClass = '';
        if($sold == 1){
            $disponibilitate = 'Vandut';
            $soldClass = 'bgred';
        }

        if ($pret_promo == 1 && $tip_promotie == 0) {
            $echoPret = format_price((float)$pret_promotional, $mod_plata, $vat);
        } else {
            $echoPret = format_price((float)$pret, $mod_plata, $vat);
        }
        
        $agentID = $post->post_author;
        $user_info = get_userdata($agentID);
        $display_name = $user_info ? $user_info->display_name : 'Unknown User';
        $image_id = get_user_meta($agentID, 'imagine', true);
        $thumbnail_url = wp_get_attachment_image_url($image_id, 'thumbnail');
        $post_status = $post->post_status;
        $postStatus = 'Publicat';
        $statusClass = '';
        $viewPost = get_permalink($post_id);
        if($post_status === 'draft'){
            $postStatus = 'Asteapta revizuire';
            $statusClass = 'bgyellow';
            $viewPost = get_preview_post_link($post_id);
        }
 
        echo '<tr class="'.$soldClass.' '.$statusClass.'">';
        echo '<td>' .esc_html($post->post_title) . '<span class="vat">#'.$post_id.'-' .esc_html($disponibilitate) . '</span></td>';
        echo '<td>' .esc_html($formatted_property) . '</td>';
        echo '<td>' . $echoPret . '</td>';
        echo '<td>' .esc_html($postStatus) . '</td>';
        if(user_can_manage()){
            echo '<td><div class="agentDashboardData"><img src="'.$thumbnail_url.'">'.$display_name.'</div></td>';
        }
        echo '<td><div class="actions">';
        echo '<a class="view-btn" href="'.$viewPost.'" target="_blank">'.ICON_view.'</a>';
        echo '<a class="edit-btn" href="/adauga-proprietate/?post='.$post_id.'&action=edit" target="_blank">'.ICON_edit.'</a>';
        if(user_can_manage()){
            echo '<div class="publish-btn" data-id="'.$post_id.'">'.ICON_publish.'</div>';
        }
        echo '</div></td>';
        echo '</tr>';
    }
    echo '</tbody>
    </table>';

    // Generate pagination links
    $total_pages = ceil($total_posts / $posts_per_page);
    echo '<div class="paginatieTabel">';
    echo paginate_links([
        'total'   => $total_pages,
        'current' => $paged,
    ]);
    echo '</div>';
} else {
    echo '<p>No posts found.</p>';
}
endif; ?>