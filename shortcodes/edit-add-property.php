<?php
if (is_user_in_allowed_roles()):
    $post_id = isset($_GET['post']) ? intval($_GET['post']) : 0;
    $action = isset($_GET['action']) && $_GET['action'] === 'edit';
    $addNew = 0;
    $meta_fields = [];
    $compartimentari_values = [];
    $tip_imobil_values = [];
    $selected_author_id = '';
    $textareaContent = '';
    $structura_values = [];
    $bucatarie_values = [];
    $destinatie_values = [];
    $incalzire_values = [];
    $acces_values = [];
    $utilitati_values = [];
    $vecinatati_values = [];
    $postTitle = '';
    $current_user_id = get_current_user_id();
    
    $users = get_users(array('role' => 'vanzari')); // Fetch users with 'vanzari' role

    // Check if edit mode is enabled and fetch post meta
    if ($post_id && $action) {
        $addNew = 1;
        $meta_fields = get_post_meta($post_id);
        $postTitle = get_the_title($post_id);
        //print_r($meta_fields);
        $tip_imobil_values = isset($meta_fields['tip_imobil'][0]) ? unserialize($meta_fields['tip_imobil'][0]) : [];
        $compartimentari_values = isset($meta_fields['compartimentari'][0]) ? unserialize($meta_fields['compartimentari'][0]) : [];
        $compartimentari_values = isset($compartimentari_values) && is_array($compartimentari_values) ? $compartimentari_values : [];

        $structura_values = isset($meta_fields['structura'][0]) ? unserialize($meta_fields['structura'][0]) : [];
        $bucatarie_values = isset($meta_fields['bucatarie'][0]) ? unserialize($meta_fields['bucatarie'][0]) : [];
        $destinatie_values = isset($meta_fields['destinatie'][0]) ? unserialize($meta_fields['destinatie'][0]) : [];
        $incalzire_values = isset($meta_fields['incalzire'][0]) ? unserialize($meta_fields['incalzire'][0]) : [];
        $incalzire_values = array($incalzire_values);
        $acces_values = isset($meta_fields['acces'][0]) ? unserialize($meta_fields['acces'][0]) : [];
        $utilitati_values = isset($meta_fields['utilitati'][0]) ? unserialize($meta_fields['utilitati'][0]) : [];
        $vecinatati_values = isset($meta_fields['vecinatati'][0]) ? unserialize($meta_fields['vecinatati'][0]) : [];

        // Decode serialized data or split comma-separated strings
        $finisaje = isset($meta_fields['finisaje'][0]) ? unserialize($meta_fields['finisaje'][0]) : [];
        $finisajeTextarea = isset($meta_fields['finisajeTextarea'][0]) ? explode(',', $meta_fields['finisajeTextarea'][0]) : [];
        $dotari = isset($meta_fields['dotari'][0]) ? unserialize($meta_fields['dotari'][0]) : [];
        // Trim whitespace from each item in the list (from comma-separated string)
        $finisajeTextarea = array_map('trim', $finisajeTextarea);
        if (!empty($finisajeTextarea)) {
            $textareaValues = $finisajeTextarea;
        } else {
            // Combine finisaje and dotari if finisajeTextarea is not available
            $textareaValues = array_merge($finisaje, array($dotari));
        }
        $textareaContent = implode(', ', $textareaValues);
        

        $selected_author_id = isset($meta_fields['select_property_author'][0]) ? $meta_fields['select_property_author'][0] : ''; 
        
        if($current_user_id != $selected_author_id && !user_can_manage()){
            echo 'Nu ai dreptul sa editezi aceasta proprietate!';
            return;
        }
    }
    ?>
    <form id="adauga-proprietate-form" method="post">
        <fieldset>
            <legend>1. Detalii Generale</legend>

            <label for="title">Titlu anunt</label><br>
            <input type="text" id="title" name="title" value="<?php echo $postTitle; ?>">
            <input type="hidden" id="editAdd" name="editAdd" value="<?php echo $addNew; ?>">
            <input type="hidden" id="post_id" name="post_id" value="<?php echo $post_id; ?>">

            <label>Selecteaza tipul proprietatii</label><br>
            <div class="radio-group">
                <input type="radio" id="apartament" name="srz_property" value="apartamente" <?php echo (isset($meta_fields['srz_property'][0]) && $meta_fields['srz_property'][0] === 'apartamente' ) ? 'checked' : ''; if($addNew == 0){ echo 'checked';} ?>>
                <label class="radio-button" for="apartament">Apartament</label>


                <input type="radio" id="vila" name="srz_property" value="vila" <?php echo (isset($meta_fields['srz_property'][0]) && $meta_fields['srz_property'][0] === 'vila') ? 'checked' : ''; ?>>
                <label class="radio-button" for="vila">Vila</label>

                <input type="radio" id="spatiu_comercial" name="srz_property" value="spatiu_comercial" <?php echo (isset($meta_fields['srz_property'][0]) && $meta_fields['srz_property'][0] === 'spatiu_comercial') ? 'checked' : ''; ?>>
                <label class="radio-button" for="spatiu_comercial">Spatiu
                    Comercial</label>

                <input type="radio" id="teren" name="srz_property" value="teren" <?php echo (isset($meta_fields['srz_property'][0]) && $meta_fields['srz_property'][0] === 'teren') ? 'checked' : ''; ?>>
                <label class="radio-button" for="teren">Teren</label>

                <input type="radio" id="cladire" name="srz_property" value="cladire" <?php echo (isset($meta_fields['srz_property'][0]) && $meta_fields['srz_property'][0] === 'cladire') ? 'checked' : ''; ?>>
                <label class="radio-button" for="cladire">Cladire</label>

                <div class="anunt_recomandat">
                    <input type="hidden" name="proprietate_speciala" value="0">
                    <input type="checkbox" id="proprietate_speciala" name="proprietate_speciala" value="1" <?php echo (isset($meta_fields['proprietate_speciala'][0]) && $meta_fields['proprietate_speciala'][0] === '1') ? 'checked' : ''; ?> style="width: 14px; accent-color: #ffffff; outline: 0px;">
                    <label class="radio-button" for="proprietate_speciala" style="width: 190px;">Proprietate
                        speciala?</label>
                </div>
            </div>

            <br>

            <fieldset>
                <div class="radio-group">
                    <div class="full">
                        <label>Proprietate vanduta?</label>
                        <div class="radio-group">
                            <input type="radio" id="nusold" name="sold" value="0" <?php echo (isset($meta_fields['sold'][0]) && $meta_fields['sold'][0] === '0') ? 'checked' : ''; ?>>
                            <label class="radio-button" for="nusold">Nu</label>
                            <input type="radio" id="dasold" name="sold" value="1" <?php echo (isset($meta_fields['sold'][0]) && $meta_fields['sold'][0] === '1') ? 'checked' : ''; ?>>
                            <label class="radio-button" for="dasold">Da</label>
                        </div>
                    </div> 
                    
                    <?php if (is_user_in_allowed_roles()): ?>

                        <div class="full">
                            <label>Anunt recomandat?</label>
                            <div class="radio-group">
                                <input type="radio" id="nurecomandat" name="anunt_recomandat" value="0" <?php echo (isset($meta_fields['anunt_recomandat'][0]) && $meta_fields['anunt_recomandat'][0] === '0') ? 'checked' : ''; ?>>
                                <label class="radio-button" for="nurecomandat">Nu</label>
                                <input type="radio" id="darecomandat" name="anunt_recomandat" value="1" <?php echo (isset($meta_fields['anunt_recomandat'][0]) && $meta_fields['anunt_recomandat'][0] === '1') ? 'checked' : ''; ?>>
                                <label class="radio-button" for="darecomandat">Da</label>
                            </div>
                        </div>
                        <div class="full">
                            <label for="agent-select">Selectează agent</label><br>
                            <select id="agent-select" name="select_property_author">
                                <option value="">-- Selectează agent --</option>
                                <?php
                                foreach ($users as $user) {
                                    $selected = ($selected_author_id == $user->ID) ? 'selected' : ''; // Check if the current user is the preselected one
                                    echo '<option value="' . esc_attr($user->ID) . '" ' . $selected . '>' . esc_html($user->display_name) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    <?php endif; ?>

                </div>
            </fieldset>
            
            
            <?php if (is_user_in_allowed_roles()): ?>

            <br>

            <fieldset>
                <div class="radio-group">
                    <div class="full">
                        <label>Numar de telefon</label>
                        <div class="radio-group choosePhone">
                            <input type="radio" id="agentPhone" name="propertyPhone" value="agent" <?php echo (isset($meta_fields['propertyPhone'][0]) && $meta_fields['propertyPhone'][0] === 'agent') ? 'checked' : ''; ?>>
                            <label class="radio-button" for="agentPhone">Agent</label>
                            <input type="radio" id="callCenterPhone" name="propertyPhone" value="callCenterPhone" <?php echo (isset($meta_fields['propertyPhone'][0]) && $meta_fields['propertyPhone'][0] === 'callCenterPhone') ? 'checked' : ''; ?>>
                            <label class="radio-button" for="callCenterPhone"><span>Call Center</span>0729.572.570</label>
                            <input type="radio" id="callCenterProprietatiPhone" name="propertyPhone" value="callCenterProprietati" <?php echo (isset($meta_fields['propertyPhone'][0]) && $meta_fields['propertyPhone'][0] === 'callCenterProprietati') ? 'checked' : ''; ?>>
                            <label class="radio-button" for="callCenterProprietatiPhone"><span>Call Center Proprietati Speciale</span>0738.294.999</label>
                        </div>
                    </div> 
                </div>
            </fieldset>
            <?php endif; ?>
        </fieldset>

        <br>

        <fieldset>
            <legend>2. Pret</legend>

            <label>Selecteaza tipul tranzactiei</label>
            <div class="radio-group">
                <input type="radio" id="vanzare" name="vanzare_sau_inchiriere" value="vanzare" <?php echo (isset($meta_fields['vanzare_sau_inchiriere'][0]) && $meta_fields['vanzare_sau_inchiriere'][0] === 'vanzare') ? 'checked' : ''; ?>>
                <label class="radio-button" for="vanzare">
                    Vanzare
                </label>
                <input type="radio" id="revanzare" name="vanzare_sau_inchiriere" value="revanzare" <?php echo (isset($meta_fields['revanzare'][0]) && $meta_fields['revanzare'][0] === '1') ? 'checked' : ''; ?>>
                <label class="radio-button" for="revanzare">
                    Revanzare
                </label>
                <input type="radio" id="inchiriere" name="vanzare_sau_inchiriere" value="inchiriere" <?php echo (isset($meta_fields['vanzare_sau_inchiriere'][0]) && $meta_fields['vanzare_sau_inchiriere'][0] === 'inchiriere') ? 'checked' : ''; ?>>
                <label class="radio-button" for="inchiriere">
                    Inchiriere
                </label>
            </div>

            <div class="radio-group">
                <div class="full">
                    <label for="pret">Pret</label><br>
                    <input type="number" id="pret" name="pret" <?php echo isset($meta_fields['pret'][0]) ? 'value="' . $meta_fields['pret'][0] . '"' : ''; ?>>
                </div>

                <div class="full">
                    <label>Moneda</label>
                    <div class="radio-group">
                        <input type="radio" id="eur" name="currency" value="eur" checked disabled <?php echo (isset($meta_fields['currency'][0]) && $meta_fields['currency'][0] === 'eur') ? 'checked' : ''; ?>>
                        <label class="radio-button" for="eur">EUR</label>
                        <input type="radio" id="ron" name="currency" value="ron" disabled <?php echo (isset($meta_fields['currency'][0]) && $meta_fields['currency'][0] === 'ron') ? 'checked' : ''; ?>>
                        <label class="radio-button" for="ron">RON</label>
                    </div>
                </div>

                <div class="full">
                    <label>Tip TVA</label>
                    <div class="radio-group">
                        <input type="radio" id="plustva" name="tip_pret" value="plustva" <?php echo (isset($meta_fields['tip_pret'][0]) && $meta_fields['tip_pret'][0] === 'plustva') ? 'checked' : ''; ?>>
                        <label class="radio-button" for="plustva">Plus TVA</label>
                        <input type="radio" id="nuseaplica" name="tip_pret" value="nuseaplica" <?php echo (isset($meta_fields['tip_pret'][0]) && $meta_fields['tip_pret'][0] === 'nuseaplica') ? 'checked' : ''; ?>>
                        <label class="radio-button" for="nuseaplica">Nu se aplica</label>
                    </div>
                </div>

                <div class="full">

                    <div class="modPlataInchiriere">
                        <label for="mod_plata">Mod Plata</label><br>
                        <select id="mod_plata" name="mod_plata">
                            <option value="total"  <?php echo (isset($meta_fields['mod_plata'][0]) && $meta_fields['mod_plata'][0] === 'total') ? 'selected' : ''; ?>>Total</option>
                            <option value="mp"     <?php echo (isset($meta_fields['mod_plata'][0]) && $meta_fields['mod_plata'][0] === 'mp') ? 'selected' : ''; ?>>MP</option>
                            <option value="luna"   <?php echo (isset($meta_fields['mod_plata'][0]) && $meta_fields['mod_plata'][0] === 'luna') ? 'selected' : ''; ?>>Luna</option>
                            <option value="noapte" <?php echo (isset($meta_fields['mod_plata'][0]) && $meta_fields['mod_plata'][0] === 'noapte') ? 'selected' : ''; ?>>Noapte</option>
                        </select>
                    </div>

                </div>
            </div>

            <br>
            <!-- Pret promotional -->
            <fieldset>
                <div class="radio-group">
                    <div class="full">
                        <label>Exista promotie?</label>
                        <div class="radio-group">
                            <input type="radio" id="nupromo" name="pret_promo" value="0" <?php echo (!isset($meta_fields['pret_promo'][0]) || $meta_fields['pret_promo'][0] === '0') ? 'checked' : ''; ?>>
                            <label class="radio-button" for="nupromo">Nu</label>
                            <input type="radio" id="dapromo" name="pret_promo" value="1" <?php echo (isset($meta_fields['pret_promo'][0]) && $meta_fields['pret_promo'][0] === '1') ? 'checked' : ''; ?>>
                            <label class="radio-button" for="dapromo">Da</label>
                        </div>
                    </div>

                    <div class="full">
                        <label>Tip promotie</label>
                        <div class="radio-group">
                            <input type="radio" id="pretredus" name="tip_promotie" value="0" <?php echo (isset($meta_fields['tip_promotie'][0]) && $meta_fields['tip_promotie'][0] === '0') ? 'checked' : ''; ?>>
                            <label class="radio-button" for="pretredus">Pret redus</label>
                            <input type="radio" id="alttip" name="tip_promotie" value="1" <?php echo (isset($meta_fields['tip_promotie'][0]) && $meta_fields['tip_promotie'][0] === '1') ? 'checked' : ''; ?>>
                            <label class="radio-button" for="alttip">Alt tip</label>
                        </div>
                    </div>

                    <div class="full">
                        <div class="hidePret pret_promotionalField">
                            <label for="pret_promotional">Pret promotional</label><br>
                            <input type="number" id="pret_promotional" name="pret_promotional" <?php echo isset($meta_fields['pret_promotional'][0]) ? 'value="' . $meta_fields['pret_promotional'][0] . '"' : ''; ?>>
                        </div>
                        <div class="hidePret conditie_promotionalaField">
                            <label for="conditie_promotionala">Conditie promotionala</label><br>
                            <input type="text" id="conditie_promotionala" name="conditie_promotionala" <?php echo isset($meta_fields['conditie_promotionala'][0]) ? 'value="' . $meta_fields['conditie_promotionala'][0] . '"' : ''; ?>>
                        </div>
                    </div>
                </div>
            </fieldset>


        </fieldset>

        <br>

        <fieldset>
            <legend>3. Descriere Generala</legend>
            <div class="full">
                <label for="prezentare">Descriere</label><br>
                <?php
                wp_editor(
                    $meta_fields['prezentare'][0] ?? '', // Initial content (empty in this case)
                    'prezentare', // ID of the textarea
                    array(
                        'textarea_name' => 'prezentare', // Name attribute for the form
                        'editor_height' => 200, // Height of the editor
                        'media_buttons' => false, // Enable media upload buttons
                        'teeny' => false, // Use the full editor
                    )
                );
                ?>
            </div>
            <div class="full">
                <label for="pret_si_modalitati_de_plata">Pret si modalitati de plata</label><br>
                <?php
                $pmp = $meta_fields['pret_si_modalitati_de_plata'][0];
                wp_editor(
                    $pmp ?? '', // Initial content (empty in this case)
                    'pret_si_modalitati_de_plata', // ID of the textarea
                    array(
                        'textarea_name' => 'pret_si_modalitati_de_plata', // Name attribute for the form
                        'editor_height' => 200, // Height of the editor
                        'media_buttons' => false, // Enable media upload buttons
                        'teeny' => false, // Use the full editor
                    )
                );
                ?>
            </div>
        </fieldset>

        <br>

        <fieldset>
            <legend>4. Localizare</legend>

            <div class="radio-group">
                <div class="full">
                    <label for="ansamblu-select">Selecteaza Ansamblu</label><br>
                    <select id="ansamblu-select" name="selecteaza_ansamblu">
                        <option value="">-- Selectează ansamblu --</option>
                        <?php
                        $selected_ansamblu_id = isset($meta_fields['selecteaza_ansamblu'][0]) ? $meta_fields['selecteaza_ansamblu'][0] : '';
                        // Query to retrieve posts from the custom post type 'ansambluri'
                        $ansambluri = new WP_Query(array(
                            'post_type' => 'ansambluri',
                            'posts_per_page' => -1, // Retrieve all posts
                            'orderby' => 'title',
                            'order' => 'ASC',
                        ));

                        // Populate the select options with the retrieved posts
                        if ($ansambluri->have_posts()) {
                            while ($ansambluri->have_posts()) {
                                $ansambluri->the_post();
                                $selected = ($selected_ansamblu_id == get_the_ID()) ? 'selected' : ''; // Check if the current user is the preselected one
                                echo '<option value="' . esc_attr(get_the_ID()) . '" ' . $selected . '>' . esc_html(get_the_title()) . '</option>';
                            }
                        } else {
                            echo '<option value="">Nu există ansambluri disponibile</option>';
                        }
                        wp_reset_postdata(); // Reset post data
                        ?>
                    </select>
                </div>

                <div class="full">
                    <label for="title">Reper</label><br>
                    <input type="text" id="title" name="reper" <?php echo isset($meta_fields['reper'][0]) ? 'value="' . $meta_fields['reper'][0] . '"' : ''; ?>>
                </div>

            </div>
            <br>
            <div class="full" id="taxonomy-zone" style="height: 280px; overflow-y: auto; border: 1px solid #ccc; padding: 10px;">
                <label for="zona">Selectati una sau mai multe zone:</label><br>
                <?php
                // Get all terms from the 'zona' taxonomy, including parent-child relationships
                $zona_terms = get_terms(array(
                    'taxonomy' => 'zona',
                    'hide_empty' => false, // Include terms even if they have no posts
                    'parent' => 0, // Get only top-level terms
                ));

                // Fetch the pre-checked values from $meta_fields['zona'][0]
                $pre_checked_zona = array();
                if (!empty($meta_fields['zona'][0])) {
                    $pre_checked_zona = unserialize($meta_fields['zona'][0]); // Deserialize the input data
                }

                // Recursive function to display child terms
                if (!function_exists('display_child_terms')) {
                    function display_child_terms($parent_id, $taxonomy, $pre_checked_zona)
                    {
                        $child_terms = get_terms(array(
                            'taxonomy' => $taxonomy,
                            'hide_empty' => false,
                            'parent' => $parent_id,
                        ));
                        if (!empty($child_terms) && !is_wp_error($child_terms)) {
                            echo '<ul style="margin-left: 20px;">'; // Indentation for child terms
                            foreach ($child_terms as $child) {
                                $checked = in_array((string) $child->term_id, $pre_checked_zona) ? 'checked' : '';
                                echo '<li>';
                                echo '<input type="checkbox" id="zona_' . esc_attr($child->term_id) . '" name="zona[]" value="' . esc_attr($child->term_id) . '" ' . $checked . '>';
                                echo '<label for="zona_' . esc_attr($child->term_id) . '">' . esc_html($child->name) . '</label>';
                                // Recursively display nested child terms
                                display_child_terms($child->term_id, $taxonomy, $pre_checked_zona);
                                echo '</li>';
                            }
                            echo '</ul>';
                        }
                    }
                }


                // Display top-level terms and their children
                if (!empty($zona_terms) && !is_wp_error($zona_terms)) {
                    echo '<ul>';
                    foreach ($zona_terms as $term) {
                        $checked = in_array((string) $term->term_id, $pre_checked_zona) ? 'checked' : '';
                        echo '<li>';
                        echo '<input type="checkbox" id="zona_' . esc_attr($term->term_id) . '" name="zona[]" value="' . esc_attr($term->term_id) . '" ' . $checked . '>';
                        echo '<label for="zona_' . esc_attr($term->term_id) . '">' . esc_html($term->name) . '</label>';
                        // Display child terms
                        display_child_terms($term->term_id, 'zona', $pre_checked_zona);
                        echo '</li>';
                    }
                    echo '</ul>';
                } else {
                    echo '<p>Nu există zone disponibile.</p>';
                }
                ?>
            </div>
            <br>
            <div class="radio-group" id="distances" <?php echo (isset($meta_fields['proprietate_speciala'][0]) && $meta_fields['proprietate_speciala'][0] === '1') ? 'style="display:none;"' : ''; ?>>
                <!-- Row 1 -->
                <div class="distance-row">
                    <label for="magazin_interes">Magazine <span style="color: red;">*</span></label>
                    <input type="number" id="magazin_interes" name="magazin_interes" <?php echo isset($meta_fields['magazin_interes'][0]) ? 'value="' . $meta_fields['magazin_interes'][0] . '"' : ''; ?>>
                    <div class="radio-group">
                        <input type="radio" id="magazine_minutes" name="magazin_interes_copy" value="minute" <?php echo (isset($meta_fields['magazin_interes_copy'][0]) && $meta_fields['magazin_interes_copy'][0] === 'minute') ? 'checked' : ''; ?>>
                        <label class="radio-button" for="magazine_minutes">Minute</label>
                        <input type="radio" id="magazine_meters" name="magazin_interes_copy" value="metri" <?php echo (isset($meta_fields['magazin_interes_copy'][0]) && $meta_fields['magazin_interes_copy'][0] === 'metri') ? 'checked' : ''; ?>>
                        <label class="radio-button" for="magazine_meters">Metri</label>
                        <input type="radio" id="magazine_km" name="magazin_interes_copy" value="km" <?php echo (isset($meta_fields['magazin_interes_copy'][0]) && $meta_fields['magazin_interes_copy'][0] === 'km') ? 'checked' : ''; ?>>
                        <label class="radio-button" for="magazine_km">Km</label>
                    </div>
                </div>
                <div class="distance-row">
                    <label for="transport_interes">Transport public</label>
                    <input type="number" id="transport_interes" name="transport_interes" <?php echo isset($meta_fields['transport_interes'][0]) ? 'value="' . $meta_fields['transport_interes'][0] . '"' : ''; ?>>
                    <div class="radio-group">
                        <input type="radio" id="transport_minutes" name="transport_interes_copy" value="minute" <?php echo (isset($meta_fields['transport_interes_copy'][0]) && $meta_fields['transport_interes_copy'][0] === 'minute') ? 'checked' : ''; ?>>
                        <label class="radio-button" for="transport_minutes">Minute</label>
                        <input type="radio" id="transport_meters" name="transport_interes_copy" value="metri" <?php echo (isset($meta_fields['transport_interes_copy'][0]) && $meta_fields['transport_interes_copy'][0] === 'metri') ? 'checked' : ''; ?>>
                        <label class="radio-button" for="transport_meters">Metri</label>
                        <input type="radio" id="transport_km" name="transport_interes_copy" value="km" <?php echo (isset($meta_fields['transport_interes_copy'][0]) && $meta_fields['transport_interes_copy'][0] === 'km') ? 'checked' : ''; ?>>
                        <label class="radio-button" for="transport_km">Km</label>
                    </div>
                </div>
                <div class="distance-row">
                    <label for="scoala_interes">Școală/Grădiniță</label>
                    <input type="number" id="scoala_interes" name="scoala_interes" <?php echo isset($meta_fields['scoala_interes'][0]) ? 'value="' . $meta_fields['scoala_interes'][0] . '"' : ''; ?>>
                    <div class="radio-group">
                        <input type="radio" id="school_minutes" name="scoala_interes_copy" value="minute" <?php echo (isset($meta_fields['scoala_interes_copy'][0]) && $meta_fields['scoala_interes_copy'][0] === 'minute') ? 'checked' : ''; ?>>
                        <label class="radio-button" for="school_minutes">Minute</label>
                        <input type="radio" id="school_meters" name="scoala_interes_copy" value="metri" <?php echo (isset($meta_fields['scoala_interes_copy'][0]) && $meta_fields['scoala_interes_copy'][0] === 'metri') ? 'checked' : ''; ?>>
                        <label class="radio-button" for="school_meters">Metri</label>
                        <input type="radio" id="school_km" name="scoala_interes_copy" value="km" <?php echo (isset($meta_fields['scoala_interes_copy'][0]) && $meta_fields['scoala_interes_copy'][0] === 'km') ? 'checked' : ''; ?>>
                        <label class="radio-button" for="school_km">Km</label>
                    </div>
                </div>

                <!-- Row 2 -->
                <div class="distance-row">
                    <label for="cafenea_interes">Cafenea/Restaurant</label>
                    <input type="number" id="cafenea_interes" name="cafenea_interes" <?php echo isset($meta_fields['cafenea_interes'][0]) ? 'value="' . $meta_fields['cafenea_interes'][0] . '"' : ''; ?>>
                    <div class="radio-group">
                        <input type="radio" id="restaurant_minutes" name="cafenea_interes_copy" value="minute" <?php echo (isset($meta_fields['cafenea_interes_copy'][0]) && $meta_fields['cafenea_interes_copy'][0] === 'minute') ? 'checked' : ''; ?>>
                        <label class="radio-button" for="restaurant_minutes">Minute</label>
                        <input type="radio" id="restaurant_meters" name="cafenea_interes_copy" value="metri" <?php echo (isset($meta_fields['cafenea_interes_copy'][0]) && $meta_fields['cafenea_interes_copy'][0] === 'metri') ? 'checked' : ''; ?>>
                        <label class="radio-button" for="restaurant_meters">Metri</label>
                        <input type="radio" id="restaurant_km" name="cafenea_interes_copy" value="km" <?php echo (isset($meta_fields['cafenea_interes_copy'][0]) && $meta_fields['cafenea_interes_copy'][0] === 'km') ? 'checked' : ''; ?>>
                        <label class="radio-button" for="restaurant_km">Km</label>
                    </div>
                </div>
                <div class="distance-row">
                    <label for="farmacie_interes">Farmacie/Spital</label>
                    <input type="number" id="farmacie_interes" name="farmacie_interes" <?php echo isset($meta_fields['farmacie_interes'][0]) ? 'value="' . $meta_fields['farmacie_interes'][0] . '"' : ''; ?>>
                    <div class="radio-group">
                        <input type="radio" id="pharmacy_minutes" name="farmacie_interes_copy" value="minute" <?php echo (isset($meta_fields['farmacie_interes_copy'][0]) && $meta_fields['farmacie_interes_copy'][0] === 'minute') ? 'checked' : ''; ?>>
                        <label class="radio-button" for="pharmacy_minutes">Minute</label>
                        <input type="radio" id="pharmacy_meters" name="farmacie_interes_copy" value="metri" <?php echo (isset($meta_fields['farmacie_interes_copy'][0]) && $meta_fields['farmacie_interes_copy'][0] === 'metri') ? 'checked' : ''; ?>>
                        <label class="radio-button" for="pharmacy_meters">Metri</label>
                        <input type="radio" id="pharmacy_km" name="farmacie_interes_copy" value="km" <?php echo (isset($meta_fields['farmacie_interes_copy'][0]) && $meta_fields['farmacie_interes_copy'][0] === 'km') ? 'checked' : ''; ?>>
                        <label class="radio-button" for="pharmacy_km">Km</label>
                    </div>
                </div>
                <div class="distance-row">
                    <label for="centru_interes">Centru</label>
                    <input type="number" id="centru_interes" name="centru_interes" <?php echo isset($meta_fields['centru_interes'][0]) ? 'value="' . $meta_fields['centru_interes'][0] . '"' : ''; ?>>
                    <div class="radio-group">
                        <input type="radio" id="center_minutes" name="centru_interes_copy" value="minute" <?php echo (isset($meta_fields['centru_interes_copy'][0]) && $meta_fields['centru_interes_copy'][0] === 'minute') ? 'checked' : ''; ?>>
                        <label class="radio-button" for="center_minutes">Minute</label>
                        <input type="radio" id="center_meters" name="centru_interes_copy" value="metri" <?php echo (isset($meta_fields['centru_interes_copy'][0]) && $meta_fields['centru_interes_copy'][0] === 'metri') ? 'checked' : ''; ?>>
                        <label class="radio-button" for="center_meters">Metri</label>
                        <input type="radio" id="center_km" name="centru_interes_copy" value="km" <?php echo (isset($meta_fields['centru_interes_copy'][0]) && $meta_fields['centru_interes_copy'][0] === 'km') ? 'checked' : ''; ?>>
                        <label class="radio-button" for="center_km">Km</label>
                    </div>
                </div>
            </div>



            <div class="full">
                <label for="localizare_si_vecinatati">Localizare si vecinatati
                </label><br>
                <?php
                wp_editor(
                    $meta_fields['localizare_si_vecinatati'][0] ?? '', // Initial content (empty in this case)
                    'localizare_si_vecinatati', // ID of the textarea
                    array(
                        'textarea_name' => 'localizare_si_vecinatati', // Name attribute for the form
                        'editor_height' => 200, // Height of the editor
                        'media_buttons' => false, // Enable media upload buttons
                        'teeny' => false, // Use the full editor
                    )
                );
                ?>
            </div>


        </fieldset>

        <br>

        <fieldset>
            <?php if ($post_id && $action) :?>
            <script>
                var initialGalleryImages = <?php echo json_encode(unserialize($meta_fields['galerie_imagini'][0])); ?>;
                var initialSchitaImages = <?php echo json_encode(unserialize($meta_fields['galerie_schite'][0])); ?>;
                var initialVideoUrl = <?php echo json_encode($meta_fields['video'][0]); ?>;
            </script>
            <?php endif;?>
            
            <legend>5. Media</legend>
            <div class="media-section">
                <div class="grid-container" id="gallery-preview" data-input-field="#gallery-ids">
                    <div class="upload-placeholder" id="upload-gallery-button">
                        <p>Adaugă imagini</p>
                    </div>
                </div>
                <input type="hidden" id="gallery-ids" name="galerie_imagini" value="">
                <button id="clear-gallery-button" class="clear-button" type="button">Sterge imagini</button>
            </div>

            <div class="media-section">
                <div class="grid-container" id="schita-preview" data-input-field="#schita-ids">
                    <div class="upload-placeholder" id="upload-schita-button">
                        <p>Adaugă schite</p>
                    </div>
                </div>
                <input type="hidden" id="schita-ids" name="galerie_schite" value="">
                <button id="clear-schita-button" class="clear-button" type="button">Sterge schite</button>
            </div>
            <br>
            <div class="youtube-section">
                <label for="youtube-url">Adaugă URL-ul YouTube:</label>
                <input type="text" id="youtube-url" name="video" placeholder="Introduceți URL-ul video-ului YouTube"
                    style="width: 100%; margin-bottom: 10px;">
                <button id="preview-youtube-button" type="button" style="margin-bottom: 10px;">Generați
                    Previzualizarea</button>
                <div id="youtube-preview" style="display: none; margin-top: 10px;">
                    <iframe id="youtube-iframe" width="560" height="315" frameborder="0" allowfullscreen></iframe>
                </div>
            </div>
        </fieldset>


        <br>

        <fieldset>
            <legend>6. Specificatii</legend>

            <div id="room-selection-group">
                <label>Numar camere</label><br>
                <div class="radio-group">
                    <input type="radio" id="room1" name="numar_camere" value="1" <?php echo (isset($meta_fields['numar_camere'][0]) && $meta_fields['numar_camere'][0] === '1') ? 'checked' : ''; ?>>
                    <label class="radio-button" id="room1-label" for="room1">1 camera</label>

                    <input type="radio" id="room2" name="numar_camere" value="2" <?php echo (isset($meta_fields['numar_camere'][0]) && $meta_fields['numar_camere'][0] === '2') ? 'checked' : ''; ?>>
                    <label class="radio-button" for="room2">2 camere</label>

                    <input type="radio" id="room3" name="numar_camere" value="3" <?php echo (isset($meta_fields['numar_camere'][0]) && $meta_fields['numar_camere'][0] === '3') ? 'checked' : ''; ?>>
                    <label class="radio-button" for="room3">3 camere</label>

                    <input type="radio" id="room4" name="numar_camere" value="4" <?php echo (isset($meta_fields['numar_camere'][0]) && $meta_fields['numar_camere'][0] === '4') ? 'checked' : ''; ?>>
                    <label class="radio-button" for="room4">4 camere</label>

                    <input type="radio" id="room5" name="numar_camere" value="5" <?php echo (isset($meta_fields['numar_camere'][0]) && $meta_fields['numar_camere'][0] === '5') ? 'checked' : ''; ?>>
                    <label class="radio-button" for="room5">5 camere</label>

                    <input type="radio" id="room6" name="numar_camere" value="6" <?php echo (isset($meta_fields['numar_camere'][0]) && $meta_fields['numar_camere'][0] === '6') ? 'checked' : ''; ?>>
                    <label class="radio-button" for="room6">6 camere</label>

                    <input type="radio" id="room7" name="numar_camere" value="7" <?php echo (isset($meta_fields['numar_camere'][0]) && $meta_fields['numar_camere'][0] === '7') ? 'checked' : ''; ?>>
                    <label class="radio-button" for="room7">7 camere</label>

                </div>

                <div class="radio-group" id="room-selection-group-vila">

                    <div class="full">
                        <label>Casa este in showroom?</label>
                        <div class="radio-group">
                            <input type="radio" id="casaNuShowroom" name="casa_este_in_showroom" value="0" <?php echo (isset($meta_fields['casa_este_in_showroom'][0]) && $meta_fields['casa_este_in_showroom'][0] === '0') ? 'checked' : ''; ?>>
                            <label class="radio-button" for="casaNuShowroom">Nu</label>
                            <input type="radio" id="casaDaShowroom" name="casa_este_in_showroom" value="1" <?php echo (isset($meta_fields['casa_este_in_showroom'][0]) && $meta_fields['casa_este_in_showroom'][0] === '1') ? 'checked' : ''; ?>>
                            <label class="radio-button" for="casaDaShowroom">Da</label>
                        </div>
                    </div>

                    <div class="full">
                        <label for="numar_dormitoare">Numar dormitoare</label><br>
                        <input type="number" id="numar_dormitoare" name="numar_dormitoare" <?php echo isset($meta_fields['numar_dormitoare'][0]) ? 'value="' . $meta_fields['numar_dormitoare'][0] . '"' : ''; ?>>
                    </div>
                </div>
            </div>
            <br>
            <fieldset>
                <label>Etichete (Pentru a adauga mai multe, separa-le cu virgula)</label><br>
                <input type="text" id="etichete" name="etichete" <?php echo isset($meta_fields['etichete'][0]) ? 'value="' . $meta_fields['etichete'][0] . '"' : ''; ?>>
            </fieldset>


            <div id="compartimentare">
                <label>Compartimentare</label><br>
                <div class="radio-group">
                    <input type="radio" id="decomandat" name="compartimentare" value="decomandat" <?php echo (isset($meta_fields['compartimentare'][0]) && $meta_fields['compartimentare'][0] === 'decomandat') ? 'checked' : ''; ?>>
                    <label class="radio-button" for="decomandat">Decomandat</label>

                    <input type="radio" id="semidecomandat" name="compartimentare" value="semidecomandat" <?php echo (isset($meta_fields['compartimentare'][0]) && $meta_fields['compartimentare'][0] === 'semidecomandat') ? 'checked' : ''; ?>>
                    <label class="radio-button" for="semidecomandat">Semidecomandat</label>

                    <input type="radio" id="nedecomandat" name="compartimentare" value="nedecomandat" <?php echo (isset($meta_fields['compartimentare'][0]) && $meta_fields['compartimentare'][0] === 'nedecomandat') ? 'checked' : ''; ?>>
                    <label class="radio-button" for="nedecomandat">Nedecomandat</label>
                </div>
            </div>



            <div class="radio-group four">


                <div id="stadiu_constructie">
                    <label>Stadiu constructie</label><br>
                    <div class="radio-group">
                        <input type="radio" id="in_constructie" name="stadiu_constructie" value="in_constructie" <?php echo (isset($meta_fields['stadiu_constructie'][0]) && $meta_fields['stadiu_constructie'][0] === 'in_constructie') ? 'checked' : ''; ?>>
                        <label class="radio-button" for="in_constructie">In constructie</label>

                        <input type="radio" id="finalizat" name="stadiu_constructie" value="finalizat" <?php echo (isset($meta_fields['stadiu_constructie'][0]) && $meta_fields['stadiu_constructie'][0] === 'finalizat') ? 'checked' : ''; ?>>
                        <label class="radio-button" for="finalizat">Finalizat</label>
                    </div>
                </div>

                <div id="suprafata_utilaX">
                    <label>Suprafata Utila</label><br>
                    <input type="number" id="suprafata_utila" name="suprafata_utila" <?php echo isset($meta_fields['suprafata_utila'][0]) ? 'value="' . $meta_fields['suprafata_utila'][0] . '"' : ''; ?>>
                </div>

                <div id="suprafata_total_utilaX">
                    <label>Suprafata Total Utila</label><br>
                    <input type="number" id="suprafata_total_utila" name="suprafata_total_utila" <?php echo isset($meta_fields['suprafata_total_utila'][0]) ? 'value="' . $meta_fields['suprafata_total_utila'][0] . '"' : ''; ?>>
                </div>

                <div id="suprafata_construitaX">
                    <label>Suprafata construita</label><br>
                    <input type="number" id="suprafata_construita" name="suprafata_construita" <?php echo isset($meta_fields['suprafata_construita'][0]) ? 'value="' . $meta_fields['suprafata_construita'][0] . '"' : ''; ?>>
                </div>

                <div id="numar_balcoaneX">
                    <label>Numar Balcoane</label><br>
                    <input type="number" id="numar_balcoane" name="numar_balcoane" <?php echo isset($meta_fields['numar_balcoane'][0]) ? 'value="' . $meta_fields['numar_balcoane'][0] . '"' : ''; ?>>
                </div>

                <div id="an_constructieX">
                    <label>An constructie</label><br>
                    <input type="number" id="an_constructie" name="an_constructie" <?php echo isset($meta_fields['an_constructie'][0]) ? 'value="' . $meta_fields['an_constructie'][0] . '"' : ''; ?>>
                </div>

                <div id="numar_grupuri_sanitareX">
                    <label>Numar grupuri sanitare</label><br>
                    <input type="number" id="numar_grupuri_sanitare" name="numar_grupuri_sanitare" <?php echo isset($meta_fields['numar_grupuri_sanitare'][0]) ? 'value="' . $meta_fields['numar_grupuri_sanitare'][0] . '"' : ''; ?>>
                </div>

                <div id="etajX">
                    <label>Etaj</label><br>
                    <select id="etaj" name="etaj">
                        <option value="demisol" <?php echo (isset($meta_fields['etaj'][0]) && $meta_fields['etaj'][0] === 'demisol') ? 'selected' : ''; ?>>Demisol</option>
                        <option value="parter" <?php echo (isset($meta_fields['etaj'][0]) && $meta_fields['etaj'][0] === 'parter') ? 'selected' : ''; ?>>Parter</option>
                        <option value="mansarda" <?php echo (isset($meta_fields['etaj'][0]) && $meta_fields['etaj'][0] === 'mansarda') ? 'selected' : ''; ?>>Mansarda</option>
                        <option value="1" <?php echo (isset($meta_fields['etaj'][0]) && $meta_fields['etaj'][0] === '1') ? 'selected' : ''; ?>>1</option>
                        <option value="2" <?php echo (isset($meta_fields['etaj'][0]) && $meta_fields['etaj'][0] === '2') ? 'selected' : ''; ?>>2</option>
                        <option value="3" <?php echo (isset($meta_fields['etaj'][0]) && $meta_fields['etaj'][0] === '3') ? 'selected' : ''; ?>>3</option>
                        <option value="4" <?php echo (isset($meta_fields['etaj'][0]) && $meta_fields['etaj'][0] === '4') ? 'selected' : ''; ?>>4</option>
                        <option value="5" <?php echo (isset($meta_fields['etaj'][0]) && $meta_fields['etaj'][0] === '5') ? 'selected' : ''; ?>>5</option>
                        <option value="6" <?php echo (isset($meta_fields['etaj'][0]) && $meta_fields['etaj'][0] === '6') ? 'selected' : ''; ?>>6</option>
                        <option value="7" <?php echo (isset($meta_fields['etaj'][0]) && $meta_fields['etaj'][0] === '7') ? 'selected' : ''; ?>>7</option>
                        <option value="8" <?php echo (isset($meta_fields['etaj'][0]) && $meta_fields['etaj'][0] === '8') ? 'selected' : ''; ?>>8</option>
                        <option value="9" <?php echo (isset($meta_fields['etaj'][0]) && $meta_fields['etaj'][0] === '9') ? 'selected' : ''; ?>>9</option>
                        <option value="10" <?php echo (isset($meta_fields['etaj'][0]) && $meta_fields['etaj'][0] === '10') ? 'selected' : ''; ?>>10</option>
                        <option value="11" <?php echo (isset($meta_fields['etaj'][0]) && $meta_fields['etaj'][0] === '11') ? 'selected' : ''; ?>>11</option>
                        <option value="12" <?php echo (isset($meta_fields['etaj'][0]) && $meta_fields['etaj'][0] === '12') ? 'selected' : ''; ?>>12</option>
                        <option value="13" <?php echo (isset($meta_fields['etaj'][0]) && $meta_fields['etaj'][0] === '13') ? 'selected' : ''; ?>>13</option>
                        <option value="14" <?php echo (isset($meta_fields['etaj'][0]) && $meta_fields['etaj'][0] === '14') ? 'selected' : ''; ?>>14</option>
                        <option value="15" <?php echo (isset($meta_fields['etaj'][0]) && $meta_fields['etaj'][0] === '15') ? 'selected' : ''; ?>>15</option>
                        <option value="16" <?php echo (isset($meta_fields['etaj'][0]) && $meta_fields['etaj'][0] === '16') ? 'selected' : ''; ?>>16</option>
                        <option value="17" <?php echo (isset($meta_fields['etaj'][0]) && $meta_fields['etaj'][0] === '17') ? 'selected' : ''; ?>>17</option>
                        <option value="18" <?php echo (isset($meta_fields['etaj'][0]) && $meta_fields['etaj'][0] === '18') ? 'selected' : ''; ?>>18</option>
                        <option value="19" <?php echo (isset($meta_fields['etaj'][0]) && $meta_fields['etaj'][0] === '19') ? 'selected' : ''; ?>>19</option>
                        <option value="20" <?php echo (isset($meta_fields['etaj'][0]) && $meta_fields['etaj'][0] === '20') ? 'selected' : ''; ?>>20</option>
                    </select>
                </div>

                <div id="regim_inaltimeX">
                    <label>Regim Inaltime</label><br>
                    <input type="text" id="regim_inaltime" name="regim_inaltime" <?php echo isset($meta_fields['regim_inaltime'][0]) ? 'value="' . $meta_fields['regim_inaltime'][0] . '"' : ''; ?>>
                </div>

                <div id="suprafata_terenX">
                    <label>Suprafata Teren</label><br>
                    <input type="number" id="suprafata_teren" name="suprafata_teren" <?php echo isset($meta_fields['suprafata_teren'][0]) ? 'value="' . $meta_fields['suprafata_teren'][0] . '"' : ''; ?>>
                </div>

                <div id="locuri_de_parcareX">
                    <label>Locuri de parcare</label><br>
                    <input type="number" id="locuri_de_parcare" name="locuri_de_parcare" <?php echo isset($meta_fields['locuri_de_parcare'][0]) ? 'value="' . $meta_fields['locuri_de_parcare'][0] . '"' : ''; ?>>
                </div>

                <div id="latime_vitrinaX">
                    <label id="label_latime_vitrina">Latime vitrina</label><br>
                    <input type="number" id="latime_vitrina" name="latime_vitrina" <?php echo isset($meta_fields['latime_vitrina'][0]) ? 'value="' . $meta_fields['latime_vitrina'][0] . '"' : ''; ?>>
                </div>

                <div id="inaltime_spatiu_comercialX">
                    <label>Inaltime Spatiu Comercial</label><br>
                    <input type="number" id="inaltime_spatiu_comercial" name="inaltime_spatiu_comercial" <?php echo isset($meta_fields['inaltime_spatiu_comercial'][0]) ? 'value="' . $meta_fields['inaltime_spatiu_comercial'][0] . '"' : ''; ?>>
                </div>

                <div id="certificat_urbanismX">
                    <label>Certificat urbanism</label><br>
                    <div class="radio-group">
                        <input type="radio" id="certificatNu" name="certificat_urbanism" value="0" <?php echo (isset($meta_fields['certificat_urbanism'][0]) && $meta_fields['certificat_urbanism'][0] === '0') ? 'checked' : ''; ?>>
                        <label class="radio-button" for="certificatNu">Nu</label>

                        <input type="radio" id="certificatDa" name="certificat_urbanism" value="1" <?php echo (isset($meta_fields['certificat_urbanism'][0]) && $meta_fields['certificat_urbanism'][0] === '1') ? 'checked' : ''; ?>>
                        <label class="radio-button" for="certificatDa">Da</label>
                    </div>
                </div>

                <div id="puzX">
                    <label>PUZ</label><br>
                    <div class="radio-group">
                        <input type="radio" id="puzNu" name="puz" value="0" <?php echo (isset($meta_fields['puz'][0]) && $meta_fields['puz'][0] === '0') ? 'checked' : ''; ?>>
                        <label class="radio-button" for="puzNu">Nu</label>

                        <input type="radio" id="puzDa" name="puz" value="1" <?php echo (isset($meta_fields['puz'][0]) && $meta_fields['puz'][0] === '1') ? 'checked' : ''; ?>>
                        <label class="radio-button" for="puzDa">Da</label>
                    </div>
                </div>

                <div id="categorie_terenX">
                    <label>Categorie Teren</label><br>
                    <input type="text" id="categorie_teren" name="categorie_teren" <?php echo isset($meta_fields['categorie_teren'][0]) ? 'value="' . $meta_fields['categorie_teren'][0] . '"' : ''; ?>>
                </div>


                <div class="full" id="compartimentari">
                    <label>Compartimentari</label><br>
                    <div class="radio-group">
                        <input type="checkbox" id="compartrigips" name="compartimentari[]" value="rigips" <?php echo in_array('rigips', $compartimentari_values) ? 'checked' : ''; ?>>
                        <label for="compartrigips">Rigips</label>
                        <input type="checkbox" id="compartcaramida" name="compartimentari[]" value="caramida" <?php echo in_array('caramida', $compartimentari_values) ? 'checked' : ''; ?>>
                        <label for="compartcaramida">Caramida</label>
                        <input type="checkbox" id="compartbeton" name="compartimentari[]" value="beton" <?php echo in_array('beton', $compartimentari_values) ? 'checked' : ''; ?>>
                        <label for="compartbeton">Beton</label>
                    </div>

                </div>


                <div class="full" id="tip_imobil">
                    <label>Tip Imobil</label><br>
                    <ul>
                        <li><label><input type="checkbox" id="tip_imobil-bloc_de_locuinte_mixte" name="tip_imobil[]"
                                    value="bloc_de_locuinte_mixte" <?php echo in_array('bloc_de_locuinte_mixte', $tip_imobil_values) ? 'checked' : ''; ?>>Bloc de locuinte mixte</label></li>
                        <li><label><input type="checkbox" id="tip_imobil-vila_individuala" name="tip_imobil[]"
                                    value="vila_individuala" <?php echo in_array('vila_individuala', $tip_imobil_values) ? 'checked' : ''; ?>>Vila individuala</label></li>
                        <li><label><input type="checkbox" id="tip_imobil-vila_tip_duplex" name="tip_imobil[]"
                                    value="vila_tip_duplex" <?php echo in_array('vila_tip_duplex', $tip_imobil_values) ? 'checked' : ''; ?>>Vila tip duplex</label></li>
                        <li><label><input type="checkbox" id="tip_imobil-vile_tip_triplex" name="tip_imobil[]"
                                    value="vile_tip_triplex" <?php echo in_array('vile_tip_triplex', $tip_imobil_values) ? 'checked' : ''; ?>>Vile tip triplex</label></li>
                        <li><label><input type="checkbox" id="tip_imobil-vile_insiruite" name="tip_imobil[]"
                                    value="vile_insiruite" <?php echo in_array('vile_insiruite', $tip_imobil_values) ? 'checked' : ''; ?>>Vile insiruite</label></li>
                        <li><label><input type="checkbox" id="tip_imobil-Bloc_de_apartamente" name="tip_imobil[]"
                                    value="Bloc_de_apartamente" <?php echo in_array('Bloc_de_apartamente', $tip_imobil_values) ? 'checked' : ''; ?>>Bloc de apartamente</label></li>
                        <li><label><input type="checkbox" id="tip_imobil-Complex_rezidential_nou" name="tip_imobil[]"
                                    value="Complex_rezidential_nou" <?php echo in_array('Complex_rezidential_nou', $tip_imobil_values) ? 'checked' : ''; ?>>Complex rezidential nou</label></li>
                        <li><label><input type="checkbox" id="tip_imobil-Centru_comercial" name="tip_imobil[]"
                                    value="Centru_comercial" <?php echo in_array('Centru_comercial', $tip_imobil_values) ? 'checked' : ''; ?>>Centru comercial</label></li>
                        <li><label><input type="checkbox" id="tip_imobil-CasaVila" name="tip_imobil[]"
                                    value="Casa/Vila" <?php echo in_array('Casa/Vila', $tip_imobil_values) ? 'checked' : ''; ?>>Casa/Vila</label></li>
                    </ul>
                </div>


                <div class="full" id="finisaje">
                    <label>Finisaje si Dotari</label><br>
                    
                    <button type="button" onclick="clearTextarea()">Clear All</button>
                    <textarea id="finisajeTextarea" readonly rows="5" cols="50" name="finisajeTextarea"
                        style="width: 100%;"><?php echo htmlspecialchars($textareaContent); ?></textarea>
                    <label>Finisaje</label><br>
                    <ul>
                        <li onclick="addToTextarea('parchet')">parchet</li>
                        <li onclick="addToTextarea('gresie')">gresie</li>
                        <li onclick="addToTextarea('faianta')">faianta</li>
                        <li onclick="addToTextarea('vopsea lavabila')">vopsea lavabila</li>
                        <li onclick="addToTextarea('usi de interior')">usi de interior</li>
                        <li onclick="addToTextarea('usa metalica antiefractie')">usa metalica antiefractie</li>
                        <li onclick="addToTextarea('obiecte sanitare')">obiecte sanitare</li>
                        <li onclick="addToTextarea('geamuri tripan')">geamuri tripan</li>
                        <li onclick="addToTextarea('centrala termica apartament')">centrala termica apartament</li>
                        <li onclick="addToTextarea('centrala termica de bloc')">centrala termica de bloc</li>
                        <li onclick="addToTextarea('calorifere')">calorifere</li>
                        <li onclick="addToTextarea('incalzire in pardoseala')">incalzire in pardoseala</li>
                        <li onclick="addToTextarea('glafuri interior')">glafuri interior</li>
                        <li onclick="addToTextarea('glafuri exterior')">glafuri exterior</li>
                        <li onclick="addToTextarea('system smart home')">system smart home</li>
                        <li onclick="addToTextarea('interfon')">interfon</li>
                        <li onclick="addToTextarea('videointerfon')">videointerfon</li>
                        <li onclick="addToTextarea('centrala termica proprie')">centrala termica proprie</li>
                        <li onclick="addToTextarea('tamplarie PVC')">tamplarie PVC</li>
                        <li onclick="addToTextarea('tamplarie Aluminiu')">tamplarie Aluminiu</li>
                        <li onclick="addToTextarea('marmura')">marmura</li>
                        <li onclick="addToTextarea('travertin')">travertin</li>
                        <li onclick="addToTextarea('piatra')">piatra</li>
                        <li onclick="addToTextarea('granit')">granit</li>
                        <li onclick="addToTextarea('mocheta')">mocheta</li>
                        <li onclick="addToTextarea('dusumea')">dusumea</li>
                        <li onclick="addToTextarea('tapet')">tapet</li>
                        <li onclick="addToTextarea('pereti lambrisati')">pereti lambrisati</li>
                        <li onclick="addToTextarea('panel')">panel</li>
                    </ul>
                    <label>Dotari</label><br>
                    <ul>
                        <li onclick="addToTextarea('mana curenta inox')">mana curenta inox</li>
                        <li onclick="addToTextarea('mana curenta metalica')">mana curenta metalica</li>
                        <li onclick="addToTextarea('balcoane zidite')">balcoane zidite</li>
                        <li onclick="addToTextarea('balcoane sticla securizata')">balcoane sticla securizata</li>
                        <li onclick="addToTextarea('balcoane traforaj metallic')">balcoane traforaj metallic</li>
                        <li onclick="addToTextarea('terasa multistrat')">terasa multistrat</li>
                        <li onclick="addToTextarea('balcon inchis')">balcon inchis</li>
                        <li onclick="addToTextarea('terasa necirculabila')">terasa necirculabila</li>
                        <li onclick="addToTextarea('pavaj')">pavaj</li>
                        <li onclick="addToTextarea('curte amenajata')">curte amenajata</li>
                        <li onclick="addToTextarea('gazon')">gazon</li>
                        <li onclick="addToTextarea('gard')">gard</li>
                        <li onclick="addToTextarea('lift')">lift</li>
                        <li onclick="addToTextarea('aer conditionat')">aer conditionat</li>
                        <li onclick="addToTextarea('pregatire aer conditionat')">pregatire aer conditionat</li>
                        <li onclick="addToTextarea('sensor pentru incendii')">sensor pentru incendii</li>
                        <li onclick="addToTextarea('monitorizare video')">monitorizare video</li>
                        <li onclick="addToTextarea('placari granit')">placari granit</li>
                        <li onclick="addToTextarea('placari marmura')">placari marmura</li>
                        <li onclick="addToTextarea('placari piatra')">placari piatra</li>
                        <li onclick="addToTextarea('placari ceramice')">placari ceramice</li>
                        <li onclick="addToTextarea('sistem supraveghere')">sistem supraveghere</li>
                        <li onclick="addToTextarea('terasa acoperita')">terasa acoperita</li>
                        <li onclick="addToTextarea('tavane casetate')">tavane casetate</li>
                        <li onclick="addToTextarea('jacuzzi')">jacuzzi</li>
                        <li onclick="addToTextarea('sala de fitness')">sala de fitness</li>
                        <li onclick="addToTextarea('telecomanda garaj')">telecomanda garaj</li>
                        <li onclick="addToTextarea('sistem smart home')">sistem smart home</li>
                    </ul>
                </div>


                <div class="full" id="structura">
                    <label>Structura</label><br>
                    <ul>
                        <li><label><input type="checkbox" id="structura-beton" name="structura[]"
                                    value="beton" <?php echo in_array('beton', $structura_values) ? 'checked' : ''; ?>>Beton</label></li>
                        <li><label><input type="checkbox" id="structura-caramida" name="structura[]"
                                    value="caramida" <?php echo in_array('caramida', $structura_values) ? 'checked' : ''; ?>>Caramida</label></li>
                        <li><label><input type="checkbox" id="structura-casa_modulara" name="structura[]"
                                    value="casa_modulara" <?php echo in_array('casa_modulara', $structura_values) ? 'checked' : ''; ?>>Casa Modulara</label></li>
                        <li><label><input type="checkbox" id="structura-lemn" name="structura[]" 
                                    value="lemn" <?php echo in_array('lemn', $structura_values) ? 'checked' : ''; ?>>Lemn</label></li>
                        <li><label><input type="checkbox" id="structura-structura_metalica_usoara" name="structura[]"
                                    value="structura_metalica_usoara" <?php echo in_array('structura_metalica_usoara', $structura_values) ? 'checked' : ''; ?>>Structura metalica usoara</label></li>
                        <li><label><input type="checkbox" id="structura-structura_metalica_grea" name="structura[]"
                                    value="structura_metalica_grea" <?php echo in_array('structura_metalica_grea', $structura_values) ? 'checked' : ''; ?>>Structura metalica grea</label></li>
                        <li><label><input type="checkbox" id="structura-panouri_sip_termoizolate" name="structura[]"
                                    value="panouri_sip_termoizolate" <?php echo in_array('panouri_sip_termoizolate', $structura_values) ? 'checked' : ''; ?>>Panouri sip termoizolate</label></li>
                    </ul>
                </div>

                <div class="full" id="bucatarie">
                    <label>Bucatarie</label><br>
                    <ul>
                        <li><label><input type="checkbox" id="bucatarie-bucatarie_mobilata" name="bucatarie[]"
                                    value="bucatarie_mobilata" <?php echo in_array('bucatarie_mobilata', $bucatarie_values) ? 'checked' : ''; ?>>Bucatarie mobilata</label></li>
                        <li><label><input type="checkbox" id="bucatarie-bucatarie_deschisa" name="bucatarie[]"
                                    value="bucatarie_deschisa" <?php echo in_array('bucatarie_deschisa', $bucatarie_values) ? 'checked' : ''; ?>>Bucatarie deschisa</label></li>
                        <li><label><input type="checkbox" id="bucatarie-bucatarie_inchisa" name="bucatarie[]"
                                    value="bucatarie_inchisa" <?php echo in_array('bucatarie_inchisa', $bucatarie_values) ? 'checked' : ''; ?>>Bucatarie inchisa</label></li>
                        <li><label><input type="checkbox" id="bucatarie-bucatarie_echipata" name="bucatarie[]"
                                    value="bucatarie_echipata" <?php echo in_array('bucatarie_echipata', $bucatarie_values) ? 'checked' : ''; ?>>Bucatarie echipata</label></li>
                    </ul>
                </div>

                <div class="full" id="destinatie">
                    <label>Destinatie</label><br>
                    <ul>
                        <li><label><input type="checkbox" id="destinatie-rezidential" name="destinatie[]"
                                    value="rezidential" <?php echo in_array('rezidential', $destinatie_values) ? 'checked' : ''; ?>>Rezidential</label></li>
                        <li><label><input type="checkbox" id="destinatie-birou" name="destinatie[]"
                                    value="birou" <?php echo in_array('birou', $destinatie_values) ? 'checked' : ''; ?>>Birou</label></li>
                        <li><label><input type="checkbox" id="destinatie-comercial" name="destinatie[]"
                                    value="comercial" <?php echo in_array('comercial', $destinatie_values) ? 'checked' : ''; ?>>Comercial</label></li>
                        <li><label><input type="checkbox" id="destinatie-vacanta" name="destinatie[]"
                                    value="vacanta" <?php echo in_array('vacanta', $destinatie_values) ? 'checked' : ''; ?>>Vacanta</label></li>
                    </ul>
                </div>


                <div class="full" id="clasa_energetica">
                    <label>Clasa energetica</label><br>
                    <div class="radio-group">
                        <input type="radio" id="clasaA" name="clasa_energetica" value="A" <?php echo (isset($meta_fields['clasa_energetica'][0]) && $meta_fields['clasa_energetica'][0] === 'A') ? 'checked' : ''; ?>>
                        <label class="radio-button" for="clasaA">A</label>

                        <input type="radio" id="clasaB" name="clasa_energetica" value="B" <?php echo (isset($meta_fields['clasa_energetica'][0]) && $meta_fields['clasa_energetica'][0] === 'B') ? 'checked' : ''; ?>>
                        <label class="radio-button" for="clasaB">B</label>

                        <input type="radio" id="clasaC" name="clasa_energetica" value="C" <?php echo (isset($meta_fields['clasa_energetica'][0]) && $meta_fields['clasa_energetica'][0] === 'C') ? 'checked' : ''; ?>>
                        <label class="radio-button" for="clasaC">C</label>

                        <input type="radio" id="clasaD" name="clasa_energetica" value="D" <?php echo (isset($meta_fields['clasa_energetica'][0]) && $meta_fields['clasa_energetica'][0] === 'D') ? 'checked' : ''; ?>>
                        <label class="radio-button" for="clasaD">D</label>

                        <input type="radio" id="clasaE" name="clasa_energetica" value="E" <?php echo (isset($meta_fields['clasa_energetica'][0]) && $meta_fields['clasa_energetica'][0] === 'E') ? 'checked' : ''; ?>>
                        <label class="radio-button" for="clasaE">E</label>

                        <input type="radio" id="clasaF" name="clasa_energetica" value="F" <?php echo (isset($meta_fields['clasa_energetica'][0]) && $meta_fields['clasa_energetica'][0] === 'F') ? 'checked' : ''; ?>>
                        <label class="radio-button" for="clasaF">F</label>

                        <input type="radio" id="clasaG" name="clasa_energetica" value="G" <?php echo (isset($meta_fields['clasa_energetica'][0]) && $meta_fields['clasa_energetica'][0] === 'G') ? 'checked' : ''; ?>>
                        <label class="radio-button" for="clasaG">G</label>

                    </div>
                </div>


                <div class="full" id="incalzire">
                    <label>Incalzire</label><br>
                    <ul>
                        <li><label><input type="checkbox" id="incalzire-centrala_proprie_a_imobilului" name="incalzire[]"
                                    value="centrala_proprie_a_imobilului" <?php echo in_array('centrala_proprie_a_imobilului', $incalzire_values) ? 'checked' : ''; ?>>Centrala proprie a imobilului</label></li>
                        <li><label><input type="checkbox" id="incalzire-pardoseala" name="incalzire[]"
                                    value="pardoseala" <?php echo in_array('pardoseala', $incalzire_values) ? 'checked' : ''; ?>>Incalzire in pardoseala</label></li>
                        <li><label><input type="checkbox" id="incalzire-calorifere" name="incalzire[]"
                                    value="calorifere" <?php echo in_array('calorifere', $incalzire_values) ? 'checked' : ''; ?>>Calorifere</label></li>
                        <li><label><input type="checkbox" id="incalzire-caloriferportprosop" name="incalzire[]"
                                    value="caloriferportprosop" <?php echo in_array('caloriferportprosop', $incalzire_values) ? 'checked' : ''; ?>>Calorifer portprosop</label></li>
                    </ul>
                </div>


                <div class="full" id="acces">
                    <label>Acces</label><br>
                    <ul>
                        <li><label><input type="checkbox" id="acces-acces_stradal" name="acces[]"
                                    value="acces_stradal" <?php echo in_array('acces_stradal', $acces_values) ? 'checked' : ''; ?>>Acces stradal</label></li>
                        <li><label><input type="checkbox" id="acces-acces_prin_spate" name="acces[]"
                                    value="acces_prin_spate" <?php echo in_array('acces_prin_spate', $acces_values) ? 'checked' : ''; ?>>Acces prin spate</label></li>
                        <li><label><input type="checkbox" id="acces-acces_mixt" name="acces[]" 
                        value="acces_mixt" <?php echo in_array('acces_mixt', $acces_values) ? 'checked' : ''; ?>>Acces
                                mixt</label></li>
                    </ul>
                </div>


                <div class="full" id="utilitati">
                    <label>Utilitati</label><br>
                    <ul>
                        <li><label><input type="checkbox" id="utilitati-apa" name="utilitati[]" 
                        value="apa" <?php echo in_array('apa', $utilitati_values) ? 'checked' : ''; ?>>Apa</label></li>
                        <li><label><input type="checkbox" id="utilitati-canalizare" name="utilitati[]"
                                    value="canalizare" <?php echo in_array('canalizare', $utilitati_values) ? 'checked' : ''; ?>>Canalizare</label></li>
                        <li><label><input type="checkbox" id="utilitati-curent" name="utilitati[]"
                                    value="curent" <?php echo in_array('curent', $utilitati_values) ? 'checked' : ''; ?>>Curent</label></li>
                        <li><label><input type="checkbox" id="utilitati-gaz" name="utilitati[]" 
                        value="gaz" <?php echo in_array('gaz', $utilitati_values) ? 'checked' : ''; ?>>Gaz</label></li>
                    </ul>
                </div>


                <div class="full" id="vecinatati">
                    <label>Vecinatati</label><br>
                    <ul>
                        <li><label><input type="checkbox" id="vecinatati-magazine" name="vecinatati[]"
                                    value="magazine" <?php echo in_array('magazine', $vecinatati_values) ? 'checked' : ''; ?>>Magazine</label></li>
                        <li><label><input type="checkbox" id="vecinatati-piata" name="vecinatati[]"
                                    value="piata" <?php echo in_array('piata', $vecinatati_values) ? 'checked' : ''; ?>>Piata</label></li>
                        <li><label><input type="checkbox" id="vecinatati-gradinita" name="vecinatati[]"
                                    value="gradinita" <?php echo in_array('gradinita', $vecinatati_values) ? 'checked' : ''; ?>>Gradinita</label></li>
                        <li><label><input type="checkbox" id="vecinatati-scoala" name="vecinatati[]"
                                    value="scoala" <?php echo in_array('scoala', $vecinatati_values) ? 'checked' : ''; ?>>Scoala</label></li>
                        <li><label><input type="checkbox" id="vecinatati-facultate" name="vecinatati[]"
                                    value="facultate" <?php echo in_array('facultate', $vecinatati_values) ? 'checked' : ''; ?>>Facultate</label></li>
                        <li><label><input type="checkbox" id="vecinatati-STB" name="vecinatati[]" 
                        value="STB" <?php echo in_array('STB', $vecinatati_values) ? 'checked' : ''; ?>>STB</label>
                        </li>
                        <li><label><input type="checkbox" id="vecinatati-metrou" name="vecinatati[]"
                                    value="metrou" <?php echo in_array('metrou', $vecinatati_values) ? 'checked' : ''; ?>>Metrou</label></li>
                        <li><label><input type="checkbox" id="vecinatati-bar/pub" name="vecinatati[]"
                                    value="bar/pub" <?php echo in_array('bar/pub', $vecinatati_values) ? 'checked' : ''; ?>>Bar/pub</label></li>
                        <li><label><input type="checkbox" id="vecinatati-restaurant" name="vecinatati[]"
                                    value="restaurant" <?php echo in_array('restaurant', $vecinatati_values) ? 'checked' : ''; ?>>Restaurant</label></li>
                        <li><label><input type="checkbox" id="vecinatati-mall" name="vecinatati[]" 
                        value="mall" <?php echo in_array('mall', $vecinatati_values) ? 'checked' : ''; ?>>Mall</label>
                        </li>
                        <li><label><input type="checkbox" id="vecinatati-parc" name="vecinatati[]" 
                        value="parc" <?php echo in_array('parc', $vecinatati_values) ? 'checked' : ''; ?>>Parc</label>
                        </li>
                        <li><label><input type="checkbox" id="vecinatati-autostrada" name="vecinatati[]"
                                    value="autostrada" <?php echo in_array('autostrada', $vecinatati_values) ? 'checked' : ''; ?>>Autostrada</label></li>
                        <li><label><input type="checkbox" id="vecinatati-farmacie_spital" name="vecinatati[]"
                                    value="farmacie_spital" <?php echo in_array('farmacie_spital', $vecinatati_values) ? 'checked' : ''; ?>>Farmacie, spital</label></li>
                        <li><label><input type="checkbox" id="vecinatati-sala_de_fitnes" name="vecinatati[]"
                                    value="sala_de_fitnes" <?php echo in_array('sala_de_fitnes', $vecinatati_values) ? 'checked' : ''; ?>>Sala de fitnes</label></li>
                    </ul>
                </div>

            </div>

        </fieldset>
        <?php wp_nonce_field('adauga_proprietate_nonce_action', 'adauga_proprietate_nonce_field'); ?>
        <button type="submit">Adaugă Proprietate</button>
    </form>

    <div id="form-response"></div>
<?php endif; ?>