<?php
// Shortcode for displaying property price
function single_pret()
{
    $post_id = get_the_ID();
    if (!$post_id)
        return '';

    // Fetch metadata
    $pret = get_meta($post_id, 'pret');
    $vat = get_vat_label(get_meta($post_id, 'tip_pret'));
    $vanzare_inchiriere = get_meta($post_id, 'vanzare_sau_inchiriere');
    $mod_plata = $vanzare_inchiriere === 'inchiriere' ? '/' . get_meta($post_id, 'mod_plata') : '';
    $pret_promo = get_meta($post_id, 'pret_promo', 0);
    $tip_promotie = get_meta($post_id, 'tip_promotie', 0);
    $pret_promotional = get_meta($post_id, 'pret_promotional');
    $conditie_promotionala = get_meta($post_id, 'conditie_promotionala');
    $proprietate_speciala = get_meta($post_id, 'proprietate_speciala', 0);
    $sold = get_meta($post_id, 'sold', 0);

    $commission = ($proprietate_speciala || $vanzare_inchiriere === 'inchiriere') ? '' : '<a href="/povestea-sudrezidential/" title="Comision 0% povestea sudrezidential" class="no_commission">' . ICON_commission . 'Comision 0% cumpărător</a>';

    ob_start();
    if ($sold == 1) {
        echo '<div class="single pret">VANDUT</div>';
    } else {
        if ($pret_promo == 1 && $tip_promotie == 0) {
            echo '<div class="single pret old">' . format_price($pret) . '</div>';
            echo '<div class="single pret">' . format_price($pret_promotional, $mod_plata, $vat) . '</div>';
        } else {
            echo '<div class="single pret">' . format_price($pret, $mod_plata, $vat) . '</div>';
        }
        if ($tip_promotie == 1 && !empty($conditie_promotionala)) {
            echo '<div class="single conditie_promo">' . esc_html($conditie_promotionala) . '</div>';
        }
    }
    echo $commission;
    echo '<a href="#calculator" class="calculator-btn">Calculator Rate</a>';

    return ob_get_clean();
}

// Shortcode for displaying property location
function single_localizare()
{
    $post_id = get_the_ID();
    if (!$post_id)
        return '';

    // Fetch metadata
    $zona = get_meta($post_id, 'zona', []);
    $reper = get_meta($post_id, 'reper');
    $selecteaza_ansamblu = get_meta($post_id, 'selecteaza_ansamblu');

    ob_start();
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
    ?>
    <div class="content_box mobile_share">
        <script>
            function copyToClipboard() {
                (function ($) {
                    var url = window.location.href;
                    navigator.clipboard.writeText(url).then(function () {
                        console.log('URL copied to clipboard');
                        $(".confirmare").empty();
                        $(".confirmare").append('Copiat!');
                    }).catch(function (error) {
                        console.error('Error copying URL to clipboard: ', error);
                    });
                })(jQuery);
            }
        </script>
        <h3 class="agent_name text-center">Trimite linkul unui prieten pe</h3>
        <div class="flex" style="display: flex;justify-content: space-around;text-align: center;">
            <a href="https://wa.me/?text=<?= the_permalink() ?>" class="whatsapp_share" data-recordable="share_whatsapp"
                target="_blank" rel="noopener noreferrer">
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/svg/whatsapp.svg" loading="lazy" width="28"
                    height="28" alt="Trimite pe WhatsApp">
                <span>Whatsapp</span>
            </a>
            <a onclick="copyToClipboard()" class="copy" data-recordable="share_prieten_link" style="cursor:pointer;">
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/svg/copy.svg" loading="lazy" width="28" height="28"
                    alt="Copiază linkul paginii">
                <span class="confirmare">Copiază</span>
            </a>
        </div>
    </div>
    <?php
    echo '<div class="calculator-btn" style="display:inline-block;">ID Anunt: ' . $post_id . '</div>';
    if ($selecteaza_ansamblu) {
        $ans_titlu = get_the_title($selecteaza_ansamblu);
        $ans_link = get_the_permalink($selecteaza_ansamblu);
        echo '<a class="single_loc_ans" href="' . $ans_link . '">Vezi mai multe anunturi din <strong>' . $ans_titlu . '</strong></a>';
    }
    return ob_get_clean();
}


function single_detalii()
{
    $post_id = get_the_ID();
    if (!$post_id)
        return '';
        
    $srz_property = get_meta($post_id, 'srz_property');

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
        '1' => '1 Camera',
        '2' => '2 camere',
        '3' => '3 camere',
        '4' => '4 camere',
        '5' => '5 camere',
        '6' => '6 camere',
        '7' => '7 camere'
    ];
    ob_start();
    echo '<div class="wp-block-group is-content-justification-space-between is-layout-flex wp-container-core-group-is-layout-6 wp-block-group-is-layout-flex unit_details">';


    // Render details based on `srz_property` type
    switch ($srz_property) {
        case 'apartamente':
            echo render_detail(ICON_rooms, '', $room_labels[$numar_camere] ?? 'N/A');
            echo render_detail(ICON_etaj, 'Etaj:', $etaj);
            echo render_detail(ICON_su, 'S.U.:', $suprafata_utila, 'mp');
            echo render_detail(ICON_stadiu, 'Stadiu:', $stadiu_constructie);
            echo render_detail(ICON_bai, 'Grup sanitar:', $numar_grupuri_sanitare);
            echo render_detail(ICON_balcon, 'Balcon:', $numar_balcoane);
            break;

        case 'cladire':
            echo render_detail(ICON_teren, 'Teren:', $suprafata_teren, 'mp');
            echo render_detail(ICON_inaltime, 'Inaltime', $regim_inaltime);
            echo render_detail(ICON_su, 'S.U.:', $suprafata_utila, 'mp');
            echo render_detail(ICON_sc, 'S.C.:', $suprafata_construita, 'mp');
            break;

        case 'spatiu_comercial':
            echo render_detail(ICON_su, 'S.T.', $suprafata_total_utila, 'mp');
            echo render_detail(ICON_latime, 'Latime', $latime_vitrina, 'ml');
            echo render_detail(ICON_inaltime, 'Inaltime', $inaltime_spatiu_comercial);
            echo render_detail(ICON_parcare, 'Locuri parcare', $locuri_de_parcare);
            echo render_detail(ICON_calendar, 'An', $an_constructie);
            break;

        case 'teren':
            echo render_detail(ICON_teren, 'Teren:', $suprafata_teren, 'mp');
            echo render_detail(ICON_urbanism, 'Certificat urbanism', $certificat_urbanism);
            echo render_detail(ICON_categorie, 'Categorie teren', $categorie_teren);
            echo render_detail(ICON_urbanism, 'PUZ', $puz);
            break;

        case 'vila':
            echo render_detail(ICON_inaltime, 'Inaltime', $regim_inaltime);
            echo render_detail(ICON_rooms, '', $room_labels[$numar_camere] ?? 'N/A');
            echo render_detail(ICON_sc, 'S.C.:', $suprafata_construita, 'mp');
            echo render_detail(ICON_teren, 'Teren:', $suprafata_teren, 'mp');
            echo render_detail(ICON_stadiu, '', $stadiu_constructie);
            break;


    }

    echo '</div>';
    return ob_get_clean();
}

function single_gallery()
{
    $post_id = get_the_ID();
    if (!$post_id)
        return '';

    wp_enqueue_style('lightbox-css', 'https://cdn.jsdelivr.net/npm/lightbox2/dist/css/lightbox.min.css');
    wp_enqueue_script('lightbox-js', 'https://cdn.jsdelivr.net/npm/lightbox2/dist/js/lightbox.min.js', array('jquery'), null, true);

    // Enqueue custom script for slider initialization
    wp_enqueue_script('custom-slider-init', get_stylesheet_directory_uri() . '/js/slider-init.js', array('jquery', 'swiper-js', 'lightbox-js'), null, true);

    // Get 'galerie_imagini' meta
    $galerie_imagini = get_post_meta($post_id, 'galerie_imagini', true);
    if (!$galerie_imagini || !is_array($galerie_imagini))
        return '';

    ob_start();
    ?>
    <div class="swiper-container main-slider">
        <div class="swiper-wrapper">
            <?php foreach ($galerie_imagini as $image_id): ?>
                <?php
                $image_url = wp_get_attachment_image_url($image_id, 'large');
                $image_full_url = wp_get_attachment_image_url($image_id, 'full');
                ?>
                <div class="swiper-slide"><a href="<?php echo esc_url($image_full_url); ?>" data-lightbox="gallery"><img
                            src="<?php echo esc_url($image_url); ?>" alt=""></a></div>
            <?php endforeach; ?>
        </div>
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
    </div>
    <div class="swiper-container thumbnail-slider">
        <div class="swiper-wrapper">
            <?php foreach ($galerie_imagini as $image_id): ?>
                <?php $thumbnail_url = wp_get_attachment_image_url($image_id, 'thumbnail'); ?>
                <div class="swiper-slide">
                    <img src="<?php echo esc_url($thumbnail_url); ?>" alt="">
                </div>
            <?php endforeach; ?>
        </div>
    </div><?php
    return ob_get_clean();
}

function single_main()
{
    $post_id = get_the_ID();
    if (!$post_id)
        return '';
    $srz_property = get_meta($post_id, 'srz_property');
    $finisajeTextarea = get_meta($post_id, 'finisajeTextarea');
    $finisaje = maybe_unserialize(get_meta($post_id, 'finisaje')); // Unserialize the array
    $dotari = maybe_unserialize(get_meta($post_id, 'dotari')); // Unserialize the array

    $prezentare = get_meta($post_id, 'prezentare');
    $localizare_si_vecinatati = get_meta($post_id, 'localizare_si_vecinatati');
    $pret_si_modalitati_de_plata = get_meta($post_id, 'pret_si_modalitati_de_plata');
    $selecteaza_ansamblu = get_meta($post_id, 'selecteaza_ansamblu');
    $proprietate_speciala = get_meta($post_id, 'proprietate_speciala', 0);
    $video = get_meta($post_id, 'video');
    $videoAnsamblu = get_meta($selecteaza_ansamblu, 'video');
    // Room labels
    $caracteristici_labels = [
        'apartamente' => [
            [
                'label' => 'Suprafata utila:',
                'meta_field' => 'suprafata_utila',
                'type' => 'int',
                'suffix' => 'mp'
            ],
            [
                'label' => 'Suprafata total utila:',
                'meta_field' => 'suprafata_total_utila',
                'type' => 'int',
                'suffix' => 'mp'
            ],
            [
                'label' => 'Suprafata construita:',
                'meta_field' => 'suprafata_construita',
                'type' => 'int',
                'suffix' => 'mp'
            ],
            [
                'label' => 'Compartimentare:',
                'meta_field' => 'compartimentare',
                'type' => 'text',
                'suffix' => ''
            ],
            [
                'label' => 'Regim inaltime:',
                'meta_field' => 'regim_inaltime',
                'type' => 'text',
                'suffix' => ''
            ],
            [
                'label' => 'Structura:',
                'meta_field' => 'structura',
                'type' => 'array',
                'suffix' => ''
            ],
            [
                'label' => 'Tip imobil:',
                'meta_field' => 'tip_imobil',
                'type' => 'array',
                'suffix' => ''
            ],
            [
                'label' => 'Compartimentari:',
                'meta_field' => 'compartimentari',
                'type' => 'array',
                'suffix' => ''
            ]
        ],
        'vila' => [
            [
                'label' => 'Tip imobil:',
                'meta_field' => 'tip_imobil',
                'type' => 'array',
                'suffix' => ''
            ],
            [
                'label' => 'Suprafata utila:',
                'meta_field' => 'suprafata_utila',
                'type' => 'int',
                'suffix' => 'mp'
            ],
            [
                'label' => 'Suprafata total utila:',
                'meta_field' => 'suprafata_total_utila',
                'type' => 'int',
                'suffix' => 'mp'
            ],
            [
                'label' => 'Suprafata construita:',
                'meta_field' => 'suprafata_construita',
                'type' => 'int',
                'suffix' => 'mp'
            ],
            [
                'label' => 'An constructie:',
                'meta_field' => 'an_constructie',
                'type' => 'int',
                'suffix' => ''
            ],
            [
                'label' => 'Numar dormitoare:',
                'meta_field' => 'numar_camere',
                'type' => 'int',
                'suffix' => ''
            ],
            [
                'label' => 'Bucatarie:',
                'meta_field' => 'bucatarie',
                'type' => 'array',
                'suffix' => ''
            ],
            [
                'label' => 'Grupuri sanitare:',
                'meta_field' => 'numar_grupuri_sanitare',
                'type' => 'int',
                'suffix' => ''
            ],
            [
                'label' => 'Locuri parcare:',
                'meta_field' => 'locuri_de_parcare',
                'type' => 'int',
                'suffix' => ''
            ],
            [
                'label' => 'Balcoane:',
                'meta_field' => 'numar_balcoane',
                'type' => 'int',
                'suffix' => ''
            ],
            [
                'label' => 'Structura:',
                'meta_field' => 'structura',
                'type' => 'array',
                'suffix' => ''
            ],
            [
                'label' => 'Destinatie:',
                'meta_field' => 'destinatie',
                'type' => 'array',
                'suffix' => ''
            ]
        ],
        'spatiu_comercial' => [
            [
                'label' => 'Suprafata total:',
                'meta_field' => 'suprafata_total_utila',
                'type' => 'int',
                'suffix' => 'mp'
            ],
            [
                'label' => 'Latime vitrina:',
                'meta_field' => 'latime_vitrina',
                'type' => 'int',
                'suffix' => ''
            ],
            [
                'label' => 'Inaltime imobil:',
                'meta_field' => 'inaltime_spatiu_comercial',
                'type' => 'int',
                'suffix' => ''
            ],
            [
                'label' => 'An constructie:',
                'meta_field' => 'an_constructie',
                'type' => 'int',
                'suffix' => ''
            ],
            [
                'label' => 'Regim inaltime:',
                'meta_field' => 'regim_inaltime',
                'type' => 'text',
                'suffix' => ''
            ],
            [
                'label' => 'Structura:',
                'meta_field' => 'structura',
                'type' => 'array',
                'suffix' => ''
            ],
            [
                'label' => 'Compartimentare:',
                'meta_field' => 'compartimentari',
                'type' => 'array',
                'suffix' => ''
            ],
            [
                'label' => 'Tip imobil:',
                'meta_field' => 'tip_imobil',
                'type' => 'array',
                'suffix' => ''
            ],
            [
                'label' => 'Destinatie:',
                'meta_field' => 'destinatie',
                'type' => 'array',
                'suffix' => ''
            ],
            [
                'label' => 'Incalzire:',
                'meta_field' => 'incalzire',
                'type' => 'array',
                'suffix' => ''
            ]
        ],
        'teren' => [
            [
                'label' => 'Suprafata teren:',
                'meta_field' => 'suprafata_teren',
                'type' => 'int',
                'suffix' => 'mp'
            ],
            [
                'label' => 'Deschidere:',
                'meta_field' => 'latime_vitrina',
                'type' => 'int',
                'suffix' => 'ml'
            ],
            [
                'label' => 'Certificat urbanism:',
                'meta_field' => 'certificat_urbanism',
                'type' => 'choice',
                'suffix' => ''
            ],
            [
                'label' => 'PUZ:',
                'meta_field' => 'puz',
                'type' => 'choice',
                'suffix' => ''
            ],
            [
                'label' => 'Regim inaltime:',
                'meta_field' => 'regim_inaltime',
                'type' => 'text',
                'suffix' => ''
            ],
            [
                'label' => 'Categorie teren:',
                'meta_field' => 'categorie_teren',
                'type' => 'text',
                'suffix' => ''
            ],
            [
                'label' => 'Acces:',
                'meta_field' => 'acces',
                'type' => 'array',
                'suffix' => ''
            ],
            [
                'label' => 'Utilitati:',
                'meta_field' => 'utilitati',
                'type' => 'array',
                'suffix' => ''
            ],
            [
                'label' => 'Vecinatati:',
                'meta_field' => 'vecinatati',
                'type' => 'array',
                'suffix' => ''
            ]
        ],
        'cladire' => [
            [
                'label' => 'Suprafata teren:',
                'meta_field' => 'suprafata_teren',
                'type' => 'int',
                'suffix' => 'mp'
            ],
            [
                'label' => 'Deschidere:',
                'meta_field' => 'latime_vitrina',
                'type' => 'int',
                'suffix' => 'ml'
            ],
            [
                'label' => 'Certificat urbanism:',
                'meta_field' => 'certificat_urbanism',
                'type' => 'choice',
                'suffix' => ''
            ],
            [
                'label' => 'Regim inaltime:',
                'meta_field' => 'regim_inaltime',
                'type' => 'text',
                'suffix' => ''
            ],
            [
                'label' => 'Categorie teren:',
                'meta_field' => 'categorie_teren',
                'type' => 'text',
                'suffix' => ''
            ],
            [
                'label' => 'Acces:',
                'meta_field' => 'acces',
                'type' => 'array',
                'suffix' => ''
            ],
            [
                'label' => 'Utilitati:',
                'meta_field' => 'utilitati',
                'type' => 'array',
                'suffix' => ''
            ],
            [
                'label' => 'Incalzire:',
                'meta_field' => 'incalzire',
                'type' => 'array',
                'suffix' => ''
            ],
            [
                'label' => 'Vecinatati:',
                'meta_field' => 'vecinatati',
                'type' => 'array',
                'suffix' => ''
            ]
        ]
    ];

    // Initialize $textareaValues
    if (!empty($finisajeTextarea)) {
        // If $finisajeTextarea is not empty, use its value
        $textareaValues = $finisajeTextarea;
        // Convert to an array by splitting on commas
        $itemsArray = explode(", ", $textareaValues);
    } else {
        // If $finisajeTextarea is empty, combine $finisaje and $dotari
        $combinedValues = array_merge((array) $finisaje, (array) $dotari);
        $itemsArray = $combinedValues; // No need to explode, as these are already arrays
    }


    $galerie_schite = get_post_meta($post_id, 'galerie_schite', true);
    ob_start();

    // Check if the $srz_property exists in the $caracteristici_labels array
    if (isset($caracteristici_labels[$srz_property])) {
        echo '<div class="has-base-background-color has-background content_box">';
        echo '<h2 class="wp-block-heading" style="font-size: clamp(0.875rem, 0.875rem + ((1vw - 0.2rem) * 0.542), 1.25rem);font-weight: 500;">Caracteristici</h2>';
        echo '<ul class="caracteristici">'; // Start an unordered list for displaying metadata

        // Loop through the labels and meta fields for the given post type
        foreach ($caracteristici_labels[$srz_property] as $field) {
            $label = $field['label']; // Retrieve the label
            $meta_field = $field['meta_field']; // Retrieve the meta field name
            $suffix = $field['suffix'];
            $type = $field['type'];
            $meta_value = get_post_meta($post_id, $meta_field, true); // Retrieve the meta value

            // Display the label and value if the meta value is not empty
            if (!empty($meta_value)) {

                echo '<li>' . esc_html($label) . '<strong>';
                switch ($type) {
                    case 'text':
                        echo format_string($meta_value);
                        break;
                    case 'array':
                        echo format_string(implode(', ', $meta_value));
                        break;
                    case 'int':
                        echo $meta_value;
                        break;
                    case 'choice':
                        var_dump($meta_value);
                        break;
                }
                echo $suffix . '</strong></li>';
            }
        }

        echo '</ul>'; // End the unordered list
        if ($galerie_schite || is_array($galerie_schite)) {
            ?>
            <br>
            <div class="swiper-container main-slider">
                <div class="swiper-wrapper">
                    <?php foreach ($galerie_schite as $image_id): ?>
                        <?php
                        $image_url = wp_get_attachment_image_url($image_id, 'large');
                        $image_full_url = wp_get_attachment_image_url($image_id, 'full');
                        ?>
                        <div class="swiper-slide"><a href="<?php echo esc_url($image_full_url); ?>" data-lightbox="galleryS"><img
                                    src="<?php echo esc_url($image_url); ?>" alt=""></a></div>
                    <?php endforeach; ?>
                </div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
            <?php
        }

        echo '</div>';
    }

    if ($itemsArray) {
        echo '<div class="has-base-background-color has-background content_box">';
        echo '<h2 class="wp-block-heading" style="font-size: clamp(0.875rem, 0.875rem + ((1vw - 0.2rem) * 0.542), 1.25rem);font-weight: 500;">Finisaje si dotari</h2>';
        echo "<ul class='finisaje'>";
        foreach ($itemsArray as $item) {
            echo "<li>" . ICON_check . htmlspecialchars($item) . "</li>";
        }
        echo "</ul>";
        echo '</div>';
    }

    if ($selecteaza_ansamblu) {
        $ans_titlu = get_the_title($selecteaza_ansamblu);
        $ans_link = get_the_permalink($selecteaza_ansamblu);
        $descriere_scurta = get_post_meta($selecteaza_ansamblu, 'descriere_scurta', true);
        $suprafata_teren = get_post_meta($selecteaza_ansamblu, 'suprafata_teren', true);
        $regim_inaltime = get_post_meta($selecteaza_ansamblu, 'regim_inaltime', true);
        $data_predare = get_post_meta($selecteaza_ansamblu, 'data_predare', true);
        $numar_de_blocuri = get_post_meta($selecteaza_ansamblu, 'numar_de_blocuri', true);
        $numar_de_apartamente = get_post_meta($selecteaza_ansamblu, 'numar_de_apartamente', true);
        $numar_de_parcari = get_post_meta($selecteaza_ansamblu, 'numar_de_parcari', true);

        echo '<div class="has-custom-albastru-background-color has-background content_box has-base-color">';
        echo '<h2 class="wp-block-heading " style="font-size: clamp(0.875rem, 0.875rem + ((1vw - 0.2rem) * 0.542), 1.25rem);font-weight: 500;">Despre ' . $ans_titlu . '</h2>';
        if ($descriere_scurta) {
            echo wpautop($descriere_scurta);
        }
        echo '<ul class="caracteristici">';
        if ($suprafata_teren) {
            echo '<li><span class="flex">' . ICON_teren . 'Suprafata teren</span><strong>' . $suprafata_teren . ' mp</strong></li>';
        }
        if ($regim_inaltime) {
            echo '<li><span class="flex">' . ICON_inaltime . 'Regim inaltime</span><strong>' . $regim_inaltime . ' mp</strong></li>';
        }
        if ($data_predare) {
            echo '<li><span class="flex">' . ICON_calendar . 'Data predare</span><strong>' . $data_predare . ' mp</strong></li>';
        }
        if ($numar_de_blocuri) {
            echo '<li><span class="flex">' . ICON_nr_blocuri . 'Numar de blocuri</span><strong>' . $numar_de_blocuri . ' mp</strong></li>';
        }
        if ($numar_de_apartamente) {
            echo '<li><span class="flex">' . ICON_nr_imobile . 'Numar de imobile</span><strong>' . $numar_de_apartamente . ' mp</strong></li>';
        }
        if ($numar_de_parcari) {
            echo '<li><span class="flex">' . ICON_parcare . 'Numar de parcari</span><strong>' . $numar_de_parcari . ' mp</strong></li>';
        }
        echo '</ul>';
        echo '<a class="single_loc_ans has-custom-galben-color" href="' . $ans_link . '">Afla mai multe detalii despre <strong>' . $ans_titlu . '</strong></a>';
        echo '</div>';
    }

    if ($prezentare) {
        echo '<div class="has-base-background-color has-background content_box">';
        echo '<h2 class="wp-block-heading" style="font-size: clamp(0.875rem, 0.875rem + ((1vw - 0.2rem) * 0.542), 1.25rem);font-weight: 500;">Prezentare</h2>';
        echo wpautop($prezentare);
        if(!$proprietate_speciala){
            $magazin_interes = get_post_meta($post_id, 'magazin_interes', true);
            $magazin_interes_copy = get_post_meta($post_id, 'magazin_interes_copy', true);
            $transport_interes = get_post_meta($post_id, 'transport_interes', true);
            $transport_interes_copy = get_post_meta($post_id, 'transport_interes_copy', true);
            $scoala_interes = get_post_meta($post_id, 'scoala_interes', true);
            $scoala_interes_copy = get_post_meta($post_id, 'scoala_interes_copy', true);
            $cafenea_interes = get_post_meta($post_id, 'cafenea_interes', true);
            $cafenea_interes_copy = get_post_meta($post_id, 'cafenea_interes_copy', true);
            $farmacie_interes = get_post_meta($post_id, 'farmacie_interes', true);
            $farmacie_interes_copy = get_post_meta($post_id, 'farmacie_interes_copy', true);
            $centru_interes = get_post_meta($post_id, 'centru_interes', true);
            $centru_interes_copy = get_post_meta($post_id, 'centru_interes_copy', true);

            echo '<ul class="caracteristici boxed">';
            echo '<li><span class="flex">' . ICON_magazin . 'Magazin</span><span>' . $magazin_interes . ' ' . $magazin_interes_copy . '</span></li>';
            echo '<li><span class="flex">' . ICON_transport_public . 'Transport public</span><span>' . $transport_interes . ' ' . $transport_interes_copy . '</span></li>';
            echo '<li><span class="flex">' . ICON_scoala . 'Scoala/Gradinita</span><span>' . $scoala_interes . ' ' . $scoala_interes_copy . '</span></li>';
            echo '<li><span class="flex">' . ICON_cafenea . 'Cafenea/Restaurant</span><span>' . $cafenea_interes . ' ' . $cafenea_interes_copy . '</span></li>';
            echo '<li><span class="flex">' . ICON_farmacie . 'Farmacie/Spital</span><span>' . $farmacie_interes . ' ' . $farmacie_interes_copy . '</span></li>';
            echo '<li><span class="flex">' . ICON_centru . 'Centru</span><span>' . $centru_interes . ' ' . $centru_interes_copy . '</span></li>';

            echo '</ul>';
        }
        echo '</div>';
    }
    if ($localizare_si_vecinatati) {
        echo '<div class="has-base-background-color has-background content_box">';
        echo '<h2 class="wp-block-heading" style="font-size: clamp(0.875rem, 0.875rem + ((1vw - 0.2rem) * 0.542), 1.25rem);font-weight: 500;">Puncte de interes</h2>';
        echo wpautop($localizare_si_vecinatati);
        echo '</div>';
    }
    if ($videoAnsamblu || $video) {
        if ($videoAnsamblu) {
            $video = $videoAnsamblu;
        }
        // Function to extract Video ID and generate an embed URL
        function getEmbedUrl($video)
        {
            // Parse the URL
            $parsed_url = parse_url($video);

            // If it's a Shorts URL
            if (strpos($video, '/shorts/') !== false) {
                $video_id = explode('/shorts/', $video)[1]; // Extract ID from Shorts URL
            }
            // If it's a Regular YouTube URL
            elseif (isset($parsed_url['query'])) {
                parse_str($parsed_url['query'], $query_params);
                if (isset($query_params['v'])) {
                    $video_id = $query_params['v'];
                }
            }
            // If it's already an embed URL or another case
            else {
                $video_id = basename($parsed_url['path']); // Get last part of URL
            }

            // Return formatted embed URL
            return "https://www.youtube.com/embed/" . htmlspecialchars($video_id);
        }

        // Get the correct embed URL
        $embedUrl = getEmbedUrl($video);
        echo '<div class="has-base-background-color has-background content_box">';
        echo '<h2 class="wp-block-heading" style="font-size: clamp(0.875rem, 0.875rem + ((1vw - 0.2rem) * 0.542), 1.25rem);font-weight: 500;">Video prezentare</h2>';
        echo '<div class="video-container">';
        echo '<iframe src="' . $embedUrl . '" allowfullscreen></iframe>';
        echo '</div>';
        echo '</div>';
    }
    if ($pret_si_modalitati_de_plata) {
        echo '<div class="has-base-background-color has-background content_box">';
        echo '<h2 class="wp-block-heading" style="font-size: clamp(0.875rem, 0.875rem + ((1vw - 0.2rem) * 0.542), 1.25rem);font-weight: 500;">Pret si modalitati de plata</h2>';
        echo wpautop($pret_si_modalitati_de_plata);
        echo '</div>';
    }
    
    if ($srz_property === 'apartamente') {
        echo '<div class="has-base-background-color has-background content_box">';
        echo '<h2 class="wp-block-heading" style="font-size: clamp(0.875rem, 0.875rem + ((1vw - 0.2rem) * 0.542), 1.25rem);font-weight: 500;">Dinamica preturilor - Cum au evoluat preturile proprietatilor vandute recent</h2>';
        echo do_shortcode('[avg_price_chart]');
        echo '</div>';
    }
    return ob_get_clean();
}

function single_sidebar()
{
    $post_id = get_the_ID();
    if (!$post_id)
        return '';
    $srz_property = get_meta($post_id, 'srz_property');
    $property_author = get_meta($post_id, 'select_property_author');
    $propertyPhone = get_meta($post_id, 'propertyPhone');
    
    $agent_img_id = get_user_meta($property_author, 'imagine', true);
    $agent_image = wp_get_attachment_image_src($agent_img_id, 'large');
    $user_info = get_userdata($property_author);
    $agent_email = $user_info->user_email;
    $agent_name = $user_info->display_name;
    $agent_phone = get_user_meta($property_author, 'numar_de_telefon', true);
    
    if($propertyPhone === 'callCenterPhone'){
        $agent_phone = '0729572570';
    }else if($propertyPhone === 'callCenterProprietati'){
        $agent_phone = '0738294999';
    }
    
    $formattedNumber = formatPhoneNumber($agent_phone);


    ob_start();
    echo '<div class="has-base-background-color has-background content_box">';
    echo '<img src="'.$agent_image[0].'" alt="'.$agent_name.'" class="agent_feat_img" />';
    echo '<h3 class="agent_name text-center">';
    echo $agent_name;
    echo '</h3>';
    echo '<a href="tel:'.$formattedNumber['link'].'" class="agent_phone trackEvent" data-event="apel_agent" data-category="anunt" data-label="click">';
    echo ICON_mobilePhone .' ' .$formattedNumber['visible'];
    echo '</a>';
    echo '<a href="mailto:'.$agent_email.'" class="agent_phone agent_email trackEvent" data-event="adresa_email" data-category="anunt" data-label="click">';
    echo $agent_email;
    echo '</a>';
    echo '<a class="mobile_phone trackEvent" href="https://wa.me/40729572570?text=Buna+ziua%2C+Sunt+interesat%2Fa+de+anuntul+dvs.&oq=Buna+ziua%2C+Sunt+interesat%2Fa+de+anuntul+dvs." target="_blank"data-event="chat_whatsapp" data-category="anunt" data-label="click">';
    echo 'Programeaza vizionare';
    echo '</a>';
    echo '<span class="orelse">sau</span>';
    echo '<div class="cf_property">';
    echo do_shortcode('[contact-form-7 id="9629" title="Contact Apartamente"]');
    echo '</div>';
    echo '<a class="mobile_phone whatsapp trackEvent" href="https://wa.me/40729572570?text=Buna+ziua%2C+Sunt+interesat%2Fa+de+anuntul+dvs.&amp;oq=Buna+ziua%2C+Sunt+interesat%2Fa+de+anuntul+dvs." target="_blank" data-event="chat_whatsapp" data-category="anunt" data-label="click">'.ICON_whatsapp.' WHATSAPP</a>';
    echo '</div>';
    ?>
    <div class="content_box mobile_share sidebar has-custom-albastru-background-color has-base-color">
        <h3 class="agent_name text-center">Trimite linkul unui prieten pe</h3>
        <div class="flex" style="display: flex;justify-content: space-around;text-align: center;">
            <a href="https://wa.me/?text=<?= the_permalink() ?>" class="whatsapp_share" data-recordable="share_whatsapp"
                target="_blank" rel="noopener noreferrer">
                <?php echo ICON_whatsapp;?>
                <span>Whatsapp</span>
            </a>
            <a onclick="copyToClipboard()" class="copy" data-recordable="share_prieten_link" style="cursor:pointer;">
                <?php echo ICON_link;?>
                <span class="confirmare">Copiază</span>
            </a>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

function calculator_rate()
{
    $post_id = get_the_ID();
    if (!$post_id)
        return '';

    $pret = get_meta($post_id, 'pret');
    $vat = get_meta($post_id, 'tip_pret');
    $vanzare_inchiriere = get_meta($post_id, 'vanzare_sau_inchiriere');
    $pret_promo = get_meta($post_id, 'pret_promo', 0);
    $tip_promotie = get_meta($post_id, 'tip_promotie', 0);
    $pret_promotional = get_meta($post_id, 'pret_promotional');
    $sold = get_meta($post_id, 'sold', 0);

    if ($vanzare_inchiriere === 'inchiriere')
        return '';
        
    // Check if the promotional price is applied
    if ($pret_promo === 1 && $tip_promotie === 0 && $sold != 1 && $vanzare_inchiriere != 'inchiriere' && $pret_promotional) {
        $pret = $pret_promotional;
    }
    
    // Check if VAT is 'plustva' and if so, increase the price by 19%
    if ($vat === 'plustva') {
        $pret = $pret + ($pret * 0.19);
    }
    $loan = $pret;
    $avans = $pret * 0.15;
    ob_start();

?>
<script src="https://refreshless.com/nouislider/dist/nouislider.js?v=1581"></script>

<link href="https://refreshless.com/nouislider/dist/nouislider.css?v=1581" rel="stylesheet">
<div class="content_box">
    <h2>Calculator rate</h2>
    <div id="calculator" class="col-xs-12 col-md-8 col-md-offset-2">
        <div class="wp-block-columns are-vertically-aligned-top is-layout-flex wp-container-core-columns-is-layout-2 wp-block-columns-is-layout-flex">
            <div class="wp-block-column is-vertically-aligned-top is-layout-flow wp-block-column-is-layout-flow">
                <fieldset>
                    <div class="form-group">
                        <label for="amount">Introduceti suma dorita</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="amount" value="<?php echo $loan; ?>" placeholder="<?php echo $loan; ?>" inputmode="numeric" pattern="^([1-9][0-9]{3,5}|1000000)$" maxlength="7" data-min="25000" data-max="1000000" data-step="100">
                            <div class="input-group-addon">€</div>
                        </div>
                    </div>
                    <div class="slider">
                        <div id="amount-slider" class="slider-bar"></div>
                        <div class="row">
                            <div class="col-xs-6">
                                <small>min 25.000 €</small>
                            </div>
                            <div class="col-xs-6 text-right">
                                <small>max 1.000.000 €</small>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <div class="form-group">
                        <label for="avans">Avans</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="avans" value="<?php echo $avans; ?>" placeholder="<?php echo $avans; ?>" inputmode="numeric" pattern="^([1-9][0-9]{3,5}|1000000)$" maxlength="7" data-min="<?php echo $avans; ?>" data-max="<?php echo $loan / 2; ?>" data-step="100">
                            <div class="input-group-addon">€</div>
                        </div>
                    </div>
                    <div class="slider">
                        <div id="avans-slider" class="slider-bar"></div>
                        <div class="row">
                            <div class="col-xs-6">
                                <small>min <?php echo $avans; ?> €</small>
                            </div>
                            <div class="col-xs-6 text-right">
                                <small>max <?php echo $loan / 2; ?> €</small>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <div class="form-group">
                        <label for="period">Perioada de rambursare in ani</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="period" value="30" placeholder="30" inputmode="numeric" pattern="^([1-9][0-9]{3,5}|30)$" maxlength="7" data-min="1" data-max="30" data-step="1">
                            <div class="input-group-addon">Ani</div>
                        </div>
                    </div>
                    <div class="slider">
                        <div id="period-slider" class="slider-bar"></div>
                        <div class="row">
                            <div class="col-xs-6">
                                <small>min 1 an</small>
                            </div>
                            <div class="col-xs-6 text-right">
                                <small>max 30 ani</small>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
            <div class="wp-block-column is-vertically-aligned-top is-layout-flow wp-block-column-is-layout-flow calculator-blue">
                <div id="calculator-rate-wrapper">
                    <div class="blur"></div>
                    <h2 style="font-size: clamp(0.875rem, 0.875rem + ((1vw - 0.2rem) * 0.542), 1.25rem);font-weight: 500;">Introdu caracteristicile creditului pe care vrei sa il accesezi, iar calculatorul iti va afisa intregul context pentru creditul dorit.</h2>
                    <div class="cta">
                        <div class="credit24_btn_rate">Calculeaza acum</div>
                    </div>
                </div>
            </div>
        </div>
        <div id="recalculate" style="display:none;">
            <div class="recalculate_wrap">
                <div class="credit24_btn_rate recalculate">Recalculeaza</div>
                <a href="https://simulator.credit24h.ro/" class="credit24_btn_rate">Aplica Acum</a>
            </div>
        </div>
        <div id="show_rate"></div>
        <div class="disclaimer_srz">
            <p>* Calculatorul de rate are caracter pur informativ si nu are caracter angajant, de orice natura, pentru Sud Rezidential Real Estate SRL (www.sudrezidential.ro) sau SudRezidential Broker SRL (www.Credit24h.ro);

* Dobânda DAE utilizata pentru calcul este pur orientativă și poate suferi oricand modificări valorice, în funcție de conditiile pietei financiar-bancare, profilul clientului și a perioadei de finanțare.

* Acordarea unui credit este condiţionată de îndeplinirea condiţiilor de eligibilitate de catre solicitant. In functie de tipul de credit, institutia de finantare selectata, este posibil ca alte taxe si comisioane sau anumite conditii specifice sa fie aplicabile.

Mai multe detalii pe <a href="https://www.credit24h.ro/" target="_blank" rel="nofollow">www.credit24h.ro</a> sau telefon <a href="tel:0731111609" target="_blank" rel="nofollow">0731.111.609</a> Call Center Credit24h.ro</p>
        </div>
    </div>
</div>

<?php
    return ob_get_clean();
}




// Register shortcodes without <p> tags
add_shortcode('single_pret', function () {
    return shortcode_unautop(single_pret());
});

add_shortcode('single_localizare', function () {
    return shortcode_unautop(single_localizare());
});

add_shortcode('single_detalii', function () {
    return shortcode_unautop(single_detalii());
});

add_shortcode('single_gallery', function () {
    return shortcode_unautop(single_gallery());
});

add_shortcode('single_main', function () {
    return single_main();
});

add_shortcode('single_sidebar', function () {
    return shortcode_unautop(single_sidebar());
});

add_shortcode('calculator_rate', function () {
    return shortcode_unautop(calculator_rate());
});
?>