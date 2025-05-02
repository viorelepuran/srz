<?php
// Get available zones dynamically
$zones = $wpdb->get_results(" SELECT t.term_id, t.slug, t.name, tt.parent FROM {$wpdb->terms} t JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id WHERE tt.taxonomy = 'zona' ");

// Organize zones into a parent-child hierarchy
$zone_hierarchy = [];
foreach ($zones as $zone) {
    if ($zone->parent == 0) {
        $zone_hierarchy[$zone->term_id] = [
            'name' => $zone->name,
            'slug' => $zone->slug,
            'children' => []
        ];
    } else {
        $zone_hierarchy[$zone->parent]['children'][] = [
            'name' => $zone->name,
            'slug' => $zone->slug
        ];
    }
}
?>
<form method="GET" action="" class="filterProperties">
    <div>
        <label>Numar camere:</label>
        <select name="numar_camere">
            <option value="all">Toate</option>
            <option value="1" <?php selected($numar_camere, '1'); ?>>1 Camera</option>
            <option value="2" <?php selected($numar_camere, '2'); ?>>2 Camere</option>
            <option value="3" <?php selected($numar_camere, '3'); ?>>3 Camere</option>
            <option value="4" <?php selected($numar_camere, '4'); ?>>4 Camere</option>
        </select>
    </div>

    <div id="zonaFilter">
        <label>Zona:</label>
        <select name="zona">
            <option value="all">Toate</option>
            <?php foreach ($zone_hierarchy as $parent_id => $parent): ?>
                <option value="<?php echo esc_attr($parent['slug']); ?>" <?php selected($zona, $parent['slug']); ?>>
                    <?php echo esc_html($parent['name']); ?>
                </option>
                <?php if (!empty($parent['children'])): ?>
                    <?php foreach ($parent['children'] as $child): ?>
                        <option value="<?php echo esc_attr($child['slug']); ?>" <?php selected($zona, $child['slug']); ?>>
                            &nbsp;&nbsp;&nbsp;— <?php echo esc_html($child['name']); ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <label>Pret minim:</label>
        <input type="number" name="min_price" value="<?php echo esc_attr($min_price); ?>" />
    </div>

    <div>
        <label>Pret maxim:</label>
        <input type="number" name="max_price" value="<?php echo esc_attr($max_price); ?>" />
    </div>

    <div>
        <label>Suprafata minima:</label>
        <input type="number" name="min_surface" value="<?php echo esc_attr($min_surface); ?>" />
    </div>

    <div>
        <label>Suprafata maxima:</label>
        <input type="number" name="max_surface" value="<?php echo esc_attr($max_surface); ?>" />
    </div>

    <div>
        <label>Ordoneaza:</label>
        <select name="order">
            <option value="ASC" <?php selected($order, 'ASC'); ?>>Pret Crescator</option>
            <option value="DESC" <?php selected($order, 'DESC'); ?>>Pret descrescator</option>
        </select>
    </div>

    <button type="submit">Aplica</button>
</form>