<?php
function property_card($post_id) {
    if (!$post_id) return ''; // Prevent errors if no post ID is provided

    ob_start();
    $recomandatCss = '';
    $srz_property = get_meta($post_id, 'srz_property');
    $image_url = get_the_post_thumbnail_url($post_id, 'large');
    $link_url = get_the_permalink($post_id);
    $title = get_the_title($post_id);

    // Fetch metadata
    $zona = get_meta($post_id, 'zona', []);
    $reper = get_meta($post_id, 'reper');
    $pret = get_meta($post_id, 'pret');
    $vat = get_vat_label(get_meta($post_id, 'tip_pret'));
    $vanzare_inchiriere = get_meta($post_id, 'vanzare_sau_inchiriere');
    $mod_plata = $vanzare_inchiriere === 'inchiriere' ? '/' . get_meta($post_id, 'mod_plata') : '';
    $pret_promo = get_meta($post_id, 'pret_promo', 0);
    $tip_promotie = get_meta($post_id, 'tip_promotie', 0);
    $pret_promotional = get_meta($post_id, 'pret_promotional');
    $conditie_promotionala = get_meta($post_id, 'conditie_promotionala');
    $proprietate_speciala = get_meta($post_id, 'proprietate_speciala', 0);
    $property_author      = get_meta($post_id, 'select_property_author');
    $recomandat = get_meta($post_id, 'anunt_recomandat', 0);
    $agent_img_id = get_user_meta($property_author, 'imagine', true);
    $agent_image = wp_get_attachment_image_src($agent_img_id, 'thumbnail');
    $user_info = get_userdata($property_author);
    $agent_name = $user_info->display_name;
    $agent_link = get_author_posts_url($property_author);
 
    if($recomandat == 1){
        $recomandatCss = 'promoAd';
    }
    
    // Fetch metadata
    $meta_keys = [
        'srz_property',
        'numar_camere',
        'etaj',
        'suprafata_utila',
        'stadiu_constructie',
        'numar_grupuri_sanitare',
        'numar_balcoane',
        'suprafata_teren',
        'regim_inaltime',
        'suprafata_construita',
        'suprafata_total_utila',
        'latime_vitrina',
        'inaltime_spatiu_comercial',
        'locuri_de_parcare',
        'an_constructie',
        'certificat_urbanism',
        'categorie_teren',
        'puz'
    ];
    $meta_values = array_map(fn($key) => get_meta($post_id, $key), $meta_keys);
    extract(array_combine($meta_keys, $meta_values));

    // Room labels
    $room_labels = [
        '0' => 'Garsoniera',
        '1' => 'Studio',
        '2' => '2 camere',
        '3' => '3 camere',
        '4' => '4 camere',
        '5' => '5 camere',
        '6' => '6 camere',
        '7' => '7 camere'
    ];
    echo '<div class="propertyApartmentCard '.$recomandatCss.'">';
    echo '<div>';
    echo '<a href="'.$link_url.'" class="cardLink">';
    echo '<img src="' . esc_url($image_url) . '" alt="' . esc_attr(get_the_title($post_id)) . '" class="custom-featured-image">';
    echo '</a>';
    echo '<div class="cardContent">';
    echo '<h2 style="font-size: clamp(0.875rem, 0.875rem + ((1vw - 0.2rem) * 0.542), 1.15rem);font-weight: 500;margin: 0;">'.$title.'</h2>';
    if (!empty($zona)) {
        $term = get_term($zona[0]); // Fetch the first zone term
        if ($term) {
            echo '<div class="single_loc">' . ICON_locatie;
            echo esc_html($term->name);
            if (!empty($reper)) {
                echo ', ' . esc_html($reper);
            }
            echo '</div>';
        }
    }
    
    
    if ($pret_promo == 1 && $tip_promotie == 0) {
        echo '<div class="single-pret-old">';
        echo '<div class="single pret old">' . format_price((float)$pret) . '</div>';
        echo '<div class="single pret">' . format_price((float)$pret_promotional, $mod_plata, $vat) . '</div>';
        echo '</div>';
    } else {
        echo '<div class="single pret">' . format_price((float)$pret, $mod_plata, $vat) . '</div>';
    }
    if ($pret_promo == 1 &&$tip_promotie == 1 && !empty($conditie_promotionala)) {
        echo '<div class="single conditie_promo">' .ICON_gift. esc_html($conditie_promotionala) . '</div>';
    }
    
    echo '<div class="wp-block-group is-content-justification-space-between is-layout-flex wp-container-core-group-is-layout-6 wp-block-group-is-layout-flex unit_details">';


    // Render details based on `srz_property` type
    switch ($srz_property) {
        case 'apartamente':
            echo render_detail(ICON_rooms, '', $room_labels[$numar_camere] ?? 'N/A');
            echo render_detail(ICON_su, 'S.U.:', $suprafata_utila, 'mp');
            echo '<div class="wp-block-group is-vertical is-content-justification-center is-layout-flex wp-container-core-group-is-layout-5 wp-block-group-is-layout-flex">'.ICON_stadiu.'Imobil nou</div>';
            break;

        case 'cladire':
            echo render_detail(ICON_teren, '', $suprafata_teren, 'mp');
            echo render_detail(ICON_inaltime, '', $regim_inaltime);
            break;

        case 'spatiu_comercial':
            echo render_detail(ICON_su, '', $suprafata_total_utila, 'mp');
            echo render_detail(ICON_latime, '', $latime_vitrina, 'ml deschidere');
            break;

        case 'teren':
            echo render_detail(ICON_latime, '', $latime_vitrina, 'ml deschidere');
            echo render_detail(ICON_teren, '', $suprafata_teren, 'mp');
            break;

        case 'vila':
            echo render_detail(ICON_rooms, '', $room_labels[$numar_camere] ?? 'N/A');
            echo render_detail(ICON_sc, '', $suprafata_construita, 'mp');
            echo render_detail(ICON_teren, '', $suprafata_teren, 'mp');
            break;


    }

    echo '</div>';
    echo '</div>';
    echo '</div>';
    
    
     
    echo '<div class="cardAuthor">';
    echo '<div class="cardAuthorDetails">'; 
    echo '<a href="'.$agent_link.'" title="'.$agent_name.'">';
    echo '<img src="'.$agent_image[0].'" alt="'.$agent_name.'" class="agent_feat_img" />';
    echo $agent_name;
    echo '</a>';
    echo '</div>';
    echo '<span class="cardId">ID: '.$post_id.'</span>';
    echo '</div>';
    
    
    echo '</div>';
    
    return ob_get_clean();
}
