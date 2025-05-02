<?php
// Define the shortcode function
function greeting_shortcode()
{ 
ob_start();
?>
    <link rel="stylesheet" id="feedback-css" href="<?php echo get_stylesheet_directory_uri(); ?>/feedback/style.css" media="all">
    <form class="feedbackForm" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" method="post">
        <input type="hidden" name="action" value="send_feedback_form_submission">

        <div class="grid grid-cols-3">
            <div class="feedback_card">
                <div class="fc_image">
                    <img src="<?= get_stylesheet_directory_uri() ?>/feedback/a.jpg" />
                    <h3>Departamentul de vanzari SudRezidential.ro reprezentat prin agentul dvs. personal si managerii acestuia.</h3>
                </div>
                <div class="fc_content">
                    <h2>GRAD DE SATISFACTIE AGENT IMOBILIAR</h2>
                    <div class="fc_content_center">
                        <div class="flex flex-col">
                            <label for="agent1"><img src="<?= get_stylesheet_directory_uri() ?>/feedback/f1.png" /></label>
                            <span>Excelent</span>
                            <input type="radio" id="agent1" name="agent" value="Excelent">
                        </div>
                        <div class="flex flex-col">
                            <label for="agent2"><img src="<?= get_stylesheet_directory_uri() ?>/feedback/f2.png" /></label>
                            <span>Bun</span>
                            <input type="radio" id="agent2" name="agent" value="Bun">
                        </div>
                        <div class="flex flex-col">
                            <label for="agent3"><img src="<?= get_stylesheet_directory_uri() ?>/feedback/f3.png" /></label>
                            <span>Acceptabil</span>
                            <input type="radio" id="agent3" name="agent" value="Acceptabil">
                        </div>
                        <div class="flex flex-col">
                            <label for="agent4"><img src="<?= get_stylesheet_directory_uri() ?>/feedback/f4.png" /></label>
                            <span>Modest</span>
                            <input type="radio" id="agent4" name="agent" value="Modest">
                        </div>
                        <div class="flex flex-col">
                            <label for="agent5"><img src="<?= get_stylesheet_directory_uri() ?>/feedback/f5.png" /></label>
                            <span>Slab</span>
                            <input type="radio" id="agent5" name="agent" value="Slab">
                        </div>
                    </div>
                </div>
                <div class="fc_options">
                    <label for="implicare_agent1" class="flex justify-between">
                        <span>Implicare</span>
                        <div>
                            <input type="checkbox" id="implicare_agent1" name="implicare_agent">
                        </div>
                    </label>
                    <label for="disponibilitate_agent1" class="flex justify-between">
                        <span>Disponibilitate</span>
                        <div>
                            <input type="checkbox" id="disponibilitate_agent1" name="disponibilitate_agent">
                        </div>
                    </label>
                    <label for="sustinere_agent1" class="flex justify-between">
                        <span>Sustinere</span>
                        <div>
                            <input type="checkbox" id="sustinere_agent1" name="sustinere_agent">
                        </div>
                    </label>
                    <label for="respect_agent1" class="flex justify-between">
                        <span>Respect</span>
                        <div>
                            <input type="checkbox" id="respect_agent1" name="respect_agent">
                        </div>
                    </label>
                    <label for="comunicare_agent1" class="flex justify-between">
                        <span>Comunicare</span>
                        <div>
                            <input type="checkbox" id="comunicare_agent1" name="comunicare_agent">
                        </div>
                    </label>
                </div>
            </div>



            <div class="feedback_card">
                <div class="fc_image">
                    <img src="<?= get_stylesheet_directory_uri() ?>/feedback/b.jpg" />
                    <h3>Departamentul Financiar Credit24h.ro reprezentat prin consilierul dvs. personal si managerii acestuia, inclusiv in relatia cu banca creditoare</h3>
                </div>
                <div class="fc_content">
                    <h2>GRAD DE SATISFACTIE CONSILIER FINANCIAR</h2>
                    <div class="fc_content_center">
                        <div class="flex flex-col">
                            <label for="consilier1"><img src="<?= get_stylesheet_directory_uri() ?>/feedback/f1.png" /></label>
                            <span>Excelent</span>
                            <input type="radio" id="consilier1" name="consilier" value="Excelent">
                        </div>
                        <div class="flex flex-col">
                            <label for="consilier2"><img src="<?= get_stylesheet_directory_uri() ?>/feedback/f2.png" /></label>
                            <span>Bun</span>
                            <input type="radio" id="consilier2" name="consilier" value="Bun">
                        </div>
                        <div class="flex flex-col">
                            <label for="consilier3"><img src="<?= get_stylesheet_directory_uri() ?>/feedback/f3.png" /></label>
                            <span>Acceptabil</span>
                            <input type="radio" id="consilier3" name="consilier" value="Acceptabil">
                        </div>
                        <div class="flex flex-col">
                            <label for="consilier4"><img src="<?= get_stylesheet_directory_uri() ?>/feedback/f4.png" /></label>
                            <span>Modest</span>
                            <input type="radio" id="consilier4" name="consilier" value="Modest">
                        </div>
                        <div class="flex flex-col">
                            <label for="consilier5"><img src="<?= get_stylesheet_directory_uri() ?>/feedback/f5.png" /></label>
                            <span>Slab</span>
                            
                            <input type="radio" id="consilier5" name="consilier" value="Slab">
                        </div>
                    </div>
                </div>
                <div class="fc_options">
                    <label for="implicare_consilier1" class="flex justify-between">
                        <span>Implicare</span>
                        <div>
                            <input type="checkbox" id="implicare_consilier1" name="implicare_consilier">
                        </div>
                    </label>
                    <label for="disponibilitate_consilier1" class="flex justify-between">
                        <span>Disponibilitate</span>
                        <div>
                            <input type="checkbox" id="disponibilitate_consilier1" name="disponibilitate_consilier">
                        </div>
                    </label>
                    <label for="sustinere_consilier1" class="flex justify-between">
                        <span>Sustinere</span>
                        <div>
                            <input type="checkbox" id="sustinere_consilier1" name="sustinere_consilier">
                        </div>
                    </label>
                    <label for="respect_consilier1" class="flex justify-between">
                        <span>Respect</span>
                        <div>
                            <input type="checkbox" id="respect_consilier1" name="respect_consilier">
                        </div>
                    </label>
                    <label for="comunicare_consilier1" class="flex justify-between">
                        <span>Comunicare</span>
                        <div>
                            <input type="checkbox" id="comunicare_consilier1" name="comunicare_consilier">
                        </div>
                    </label>
                </div>
            </div>



            <div class="feedback_card">
                <div class="fc_image">
                    <img src="<?= get_stylesheet_directory_uri() ?>/feedback/c.jpg" />
                    <h3>Departamentul Juridic Sudrezidential.ro reprezentat de consilierul juridic si managerii acestuia, inclusiv relatia cu cabinetul notarial si avocatul Sudrezidential.ro, daca a fost cazul.</h3>
                </div>
                <div class="fc_content">
                    <h2>GRAD DE SATISFACTIE REPREZENTANT DEPARTAMENT JURIDIC</h2>
                    <div class="fc_content_center">
                        <div class="flex flex-col">
                            <label for="juridic1"><img src="<?= get_stylesheet_directory_uri() ?>/feedback/f1.png" /></label>
                            <span>Excelent</span>
                            <input type="radio" id="juridic1" name="juridic" value="Excelent">
                        </div>
                        <div class="flex flex-col">
                            <label for="juridic2"><img src="<?= get_stylesheet_directory_uri() ?>/feedback/f2.png" /></label>
                            <span>Bun</span>
                            <input type="radio" id="juridic2" name="juridic" value="Bun">
                        </div>
                        <div class="flex flex-col">
                            <label for="juridic3"><img src="<?= get_stylesheet_directory_uri() ?>/feedback/f3.png" /></label>
                            <span>Acceptabil</span>
                            <input type="radio" id="juridic3" name="juridic" value="Acceptabil">
                        </div>
                        <div class="flex flex-col">
                            <label for="juridic4"><img src="<?= get_stylesheet_directory_uri() ?>/feedback/f4.png" /></label>
                            <span>Modest</span>
                            <input type="radio" id="juridic4" name="juridic" value="Modest">
                        </div>
                        <div class="flex flex-col">
                            <label for="juridic5"><img src="<?= get_stylesheet_directory_uri() ?>/feedback/f5.png" /></label>
                            <span>Slab</span>
                            <input type="radio" id="juridic5" name="juridic" value="Slab">
                        </div>
                    </div>
                </div>
                <div class="fc_options">
                    <label for="implicare_juridic1" class="flex justify-between">
                        <span>Implicare</span>
                        <div>
                            <input type="checkbox" id="implicare_juridic1" name="implicare_juridic">
                        </div>
                    </label>
                    <label for="disponibilitate_juridic1" class="flex justify-between">
                        <span>Disponibilitate</span>
                        <div>
                            <input type="checkbox" id="disponibilitate_juridic1" name="disponibilitate_juridic">
                        </div>
                    </label>
                    <label for="sustinere_juridic1" class="flex justify-between">
                        <span>Sustinere</span>
                        <div>
                            <input type="checkbox" id="sustinere_juridic1" name="sustinere_juridic">
                        </div>
                    </label>
                    <label for="respect_juridic1" class="flex justify-between">
                        <span>Respect</span>
                        <div>
                            <input type="checkbox" id="respect_juridic1" name="respect_juridic">
                        </div>
                    </label>
                    <label for="comunicare_juridic1" class="flex justify-between">
                        <span>Comunicare</span>
                        <div>
                            <input type="checkbox" id="comunicare_juridic1" name="comunicare_juridic">
                        </div>
                    </label>
                </div>
            </div>



            <div class="feedback_card">
                <div class="fc_image">
                    <img src="<?= get_stylesheet_directory_uri() ?>/feedback/d.jpg" />
                    <h3>Departamentul de Finisare si Echipare al apartamentului achizitionat de dvs. pus la dispozitie de catre reprezentantii proiectului imobiliar, inclusiv managerul proiectului rezidential si responsabilul privind finisajele</h3>
                </div>
                <div class="fc_content">
                    <h2>GRAD DE SATISFACTIE REPREZENTANT DEPARTAMENT DE FINISARE SI ECHIPARE</h2>
                    <div class="fc_content_center">
                        <div class="flex flex-col">
                            <label for="finisare1"><img src="<?= get_stylesheet_directory_uri() ?>/feedback/f1.png" /></label>
                            <span>Excelent</span>
                            <input type="radio" id="finisare1" name="finisare" value="Excelent">
                        </div>
                        <div class="flex flex-col">
                            <label for="finisare2"><img src="<?= get_stylesheet_directory_uri() ?>/feedback/f2.png" /></label>
                            <span>Bun</span>
                            <input type="radio" id="finisare2" name="finisare" value="Bun">
                        </div>
                        <div class="flex flex-col">
                            <label for="finisare3"><img src="<?= get_stylesheet_directory_uri() ?>/feedback/f3.png" /></label>
                            <span>Acceptabil</span>
                            <input type="radio" id="finisare3" name="finisare" value="Acceptabil">
                        </div>
                        <div class="flex flex-col">
                            <label for="finisare4"><img src="<?= get_stylesheet_directory_uri() ?>/feedback/f4.png" /></label>
                            <span>Slab</span>
                            <input type="radio" id="finisare4" name="finisare" value="Modest">
                        </div>
                        <div class="flex flex-col">
                            <label for="finisare5"><img src="<?= get_stylesheet_directory_uri() ?>/feedback/f5.png" /></label>
                            <span>Modest</span>
                            <input type="radio" id="finisare5" name="finisare" value="Slab">
                        </div>
                    </div>
                </div>
                <div class="fc_options">
                    <label for="implicare_finisare1" class="flex justify-between">
                        <span>Implicare</span>
                        <div>
                            <input type="checkbox" id="implicare_finisare1" name="implicare_finisare">
                        </div>
                    </label>
                    <label for="disponibilitate_finisare1" class="flex justify-between">
                        <span>Disponibilitate</span>
                        <div>
                            <input type="checkbox" id="disponibilitate_finisare1" name="disponibilitate_finisare">
                        </div>
                    </label>
                    <label for="sustinere_finisare1" class="flex justify-between">
                        <span>Sustinere</span>
                        <div>
                            <input type="checkbox" id="sustinere_finisare1" name="sustinere_finisare">
                        </div>
                    </label>
                    <label for="respect_finisare1" class="flex justify-between">
                        <span>Respect</span>
                        <div>
                            <input type="checkbox" id="respect_finisare1" name="respect_finisare">
                        </div>
                    </label>
                    <label for="comunicare_finisare1" class="flex justify-between">
                        <span>Comunicare</span>
                        <div>
                            <input type="checkbox" id="comunicare_finisare1" name="comunicare_finisare">
                        </div>
                    </label>
                </div>
            </div>



            <div class="feedback_card">
                <div class="fc_image">
                    <img src="<?= get_stylesheet_directory_uri() ?>/feedback/e.jpg" />
                    <h3>Reprezentantul legal al Vanzatorului/Dezvoltatorului imobiliar cu care ati interactionat pe durata tranzactiei si dupa aceasta, inclusiv echipa acestuia.</h3>
                </div>
                <div class="fc_content">
                    <h2>GRAD DE SATISFACTIE REPREZENTANT PRIVIND DEZVOLTATORUL IMOBILIAR</h2>
                    <div class="fc_content_center">
                        <div class="flex flex-col">
                            <label for="dezvoltator1"><img src="<?= get_stylesheet_directory_uri() ?>/feedback/f1.png" /></label>
                            <span>Excelent</span>
                            <input type="radio" id="dezvoltator1" name="dezvoltator" value="Excelent">
                        </div>
                        <div class="flex flex-col">
                            <label for="dezvoltator2"><img src="<?= get_stylesheet_directory_uri() ?>/feedback/f2.png" /></label>
                            <span>Bun</span>
                            <input type="radio" id="dezvoltator2" name="dezvoltator" value="Bun">
                        </div>
                        <div class="flex flex-col">
                            <label for="dezvoltator3"><img src="<?= get_stylesheet_directory_uri() ?>/feedback/f3.png" /></label>
                            <span>Acceptabil</span>
                            <input type="radio" id="dezvoltator3" name="dezvoltator" value="Acceptabil">
                        </div>
                        <div class="flex flex-col">
                            <label for="dezvoltator4"><img src="<?= get_stylesheet_directory_uri() ?>/feedback/f4.png" /></label>
                            <span>Modest</span>
                            <input type="radio" id="dezvoltator4" name="dezvoltator" value="Modest">
                        </div>
                        <div class="flex flex-col">
                            <label for="dezvoltator5"><img src="<?= get_stylesheet_directory_uri() ?>/feedback/f5.png" /></label>
                            <span>Slab</span>
                            <input type="radio" id="dezvoltator5" name="dezvoltator" value="Slab">
                        </div>
                    </div>
                </div>
                <div class="fc_options">
                    <label for="implicare_dezvoltator1" class="flex justify-between">
                        <span>Implicare</span>
                        <div>
                            <input type="checkbox" id="implicare_dezvoltator1" name="implicare_dezvoltator">
                        </div>
                    </label>
                    <label for="disponibilitate_dezvoltator1" class="flex justify-between">
                        <span>Disponibilitate</span>
                        <div>
                            <input type="checkbox" id="disponibilitate_dezvoltator1" name="disponibilitate_dezvoltator">
                        </div>
                    </label>
                    <label for="sustinere_dezvoltator1" class="flex justify-between">
                        <span>Sustinere</span>
                        <div>
                            <input type="checkbox" id="sustinere_dezvoltator1" name="sustinere_dezvoltator">
                        </div>
                    </label>
                    <label for="respect_dezvoltator1" class="flex justify-between">
                        <span>Respect</span>
                        <div>
                            <input type="checkbox" id="respect_dezvoltator1" name="respect_dezvoltator">
                        </div>
                    </label>
                    <label for="comunicare_dezvoltator1" class="flex justify-between">
                        <span>Comunicare</span>
                        <div>
                            <input type="checkbox" id="comunicare_dezvoltator1" name="comunicare_dezvoltator">
                        </div>
                    </label>
                </div>
            </div>



            <div class="feedback_card">
                <div class="fc_image">
                    <img src="<?= get_stylesheet_directory_uri() ?>/feedback/f.jpg" />
                </div>
                <div class="fc_content">
                    <h2>OBSERVATII:</h2>
                    <div class="fc_content_center">
                        <div class="flex flex-col">
                            <label for="dezvoltator1"><img src="<?= get_stylesheet_directory_uri() ?>/feedback/6.png" /></label>
                        </div>
                    </div>
                </div>
                <div class="fc_options text">
                    <textarea name="observatii" class="cool_textarea"></textarea>
                </div>
            </div>
        </div>
        
        <div class="optional_box">
            <h2>OPTIONAL. Ne puteti trimite formularul si fara a completa campurile de mai jos</h2>
            <div class="grid grid-cols-3">
                <div class="flex">
                    <label for="nume_prenume">Nume si Prenume</label>
                    <input type="text" id="nume_prenume" name="nume_prenume">
                </div>
                <div class="flex">
                    <label for="telefon">Numar de telefon</label>
                    <input type="text" id="telefon" name="telefon">
                </div>
                <div class="flex">
                    <label for="email">E-Mail</label>
                    <input type="text" id="email" name="email">
                </div>
            </div>
        </div>
        
        <label class="text-lg font-medium mb-2 flex items-center w-full mt-8">
            <input type="checkbox" name="termeni_conditii" required class="w-6 h-6 mr-3"> Sunt de acord cu termenii si conditiile SudRezidential.ro
        </label>
        <label class="text-lg font-medium mb-2 flex items-center w-full ">
            <input type="checkbox" name="prelucrare_date" required class="w-6 h-6 mr-3">Sunt de acord cu prelucrarea datelor cu caracter personal
        </label>
        <div class="form-message"></div>
        <input type="submit" value="Trimite" class="bg-green text-white font-normal px-10 py-2 text-center m-auto cursor-pointer block mt-5">
    </form>
    
<script>
(function( $ ){
    $('form.feedbackForm').on('submit', function (event) {
        event.preventDefault();
        var formData = $(this).serialize();

        $.ajax({
            url: '<?php echo esc_url(admin_url("admin-ajax.php")); ?>',
            type: 'post',
            data: formData,
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    // Display success message
                    window.location.href = "/formular-de-satisfactie-client/va-multumim-pentru-feedback/";
                } else {
                    // Display error message
                    $('.form-message').text(response.data).removeClass('success').addClass('error').fadeIn();
                }
            },
            error: function () {
                // Display error message for AJAX request failure
                $('.form-message').text('A intervenit o eroare tehnica. Va rugam sa incercati din nou mai tarziu.')
                    .removeClass('success').addClass('error').fadeIn();
            }
        });
    });
})( jQuery );
</script>
    <?php

	$output_string = ob_get_contents();
	ob_end_clean();
	return $output_string;
}
add_shortcode('formular-satisfactie', 'greeting_shortcode');

// Add an action hook to handle the form submission
add_action('wp_ajax_send_feedback_form_submission', 'send_feedback_form_submission');
add_action('wp_ajax_nopriv_send_feedback_form_submission', 'send_feedback_form_submission'); // For non-authenticated users
function send_feedback_form_submission() {
        // Capture and sanitize the form data
        $agent_rating = sanitize_text_field($_POST['agent']);
        $agent_implicare = isset($_POST['implicare_agent']) ? 1 : 0;
        $agent_disponibilitate = isset($_POST['disponibilitate_agent']) ? 1 : 0;
        $agent_sustinere = isset($_POST['sustinere_agent']) ? 1 : 0;
        $agent_respect = isset($_POST['respect_agent']) ? 1 : 0;
        $agent_comunicare = isset($_POST['comunicare_agent']) ? 1 : 0;
        
        
        $consilier_rating = sanitize_text_field($_POST['consilier']);
        $consilier_implicare = isset($_POST['implicare_consilier']) ? 1 : 0;
        $consilier_disponibilitate = isset($_POST['disponibilitate_consilier']) ? 1 : 0;
        $consilier_sustinere = isset($_POST['sustinere_consilier']) ? 1 : 0;
        $consilier_respect = isset($_POST['respect_consilier']) ? 1 : 0;
        $consilier_comunicare = isset($_POST['comunicare_consilier']) ? 1 : 0;
        
        
        $juridic_rating = sanitize_text_field($_POST['juridic']);
        $juridic_implicare = isset($_POST['implicare_juridic']) ? 1 : 0;
        $juridic_disponibilitate = isset($_POST['disponibilitate_juridic']) ? 1 : 0;
        $juridic_sustinere = isset($_POST['sustinere_juridic']) ? 1 : 0;
        $juridic_respect = isset($_POST['respect_juridic']) ? 1 : 0;
        $juridic_comunicare = isset($_POST['comunicare_juridic']) ? 1 : 0;
        
        
        $finisare_rating = sanitize_text_field($_POST['finisare']);
        $finisare_implicare = isset($_POST['implicare_finisare']) ? 1 : 0;
        $finisare_disponibilitate = isset($_POST['disponibilitate_finisare']) ? 1 : 0;
        $finisare_sustinere = isset($_POST['sustinere_finisare']) ? 1 : 0;
        $finisare_respect = isset($_POST['respect_finisare']) ? 1 : 0;
        $finisare_comunicare = isset($_POST['comunicare_finisare']) ? 1 : 0;
        
        
        $dezvoltator_rating = sanitize_text_field($_POST['dezvoltator']);
        $dezvoltator_implicare = isset($_POST['implicare_dezvoltator']) ? 1 : 0;
        $dezvoltator_disponibilitate = isset($_POST['disponibilitate_dezvoltator']) ? 1 : 0;
        $dezvoltator_sustinere = isset($_POST['sustinere_dezvoltator']) ? 1 : 0;
        $dezvoltator_respect = isset($_POST['respect_dezvoltator']) ? 1 : 0;
        $dezvoltator_comunicare = isset($_POST['comunicare_dezvoltator']) ? 1 : 0;
        
        
        $observatii = isset($_POST['observatii']) ? sanitize_textarea_field($_POST['observatii']) : '';
        $nume_prenume = isset($_POST['nume_prenume']) ? sanitize_text_field($_POST['nume_prenume']) : '';
        $telefon = isset($_POST['telefon']) ? sanitize_text_field($_POST['telefon']) : '';
        $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';


            // Compose the email content
            $email_content = "Agent Imobiliar: $agent_rating\n";
            $email_content .= "Implicare: " . ($agent_implicare ? 'Da' : 'Nespecificat') . "\n";
            $email_content .= "Disponibilitate: " . ($agent_disponibilitate ? 'Da' : 'Nespecificat') . "\n";
            $email_content .= "Sustinere: " . ($agent_sustinere ? 'Da' : 'Nespecificat') . "\n";
            $email_content .= "Respect: " . ($agent_respect ? 'Da' : 'Nespecificat') . "\n";
            $email_content .= "Comunicare: " . ($agent_comunicare ? 'Da' : 'Nespecificat') . "\n";
            
            $email_content .= "\n";
            
            $email_content .= "Consilier Financiar: $consilier_rating\n";
            $email_content .= "Implicare: " . ($consilier_implicare ? 'Da' : 'Nespecificat') . "\n";
            $email_content .= "Disponibilitate: " . ($consilier_disponibilitate ? 'Da' : 'Nespecificat') . "\n";
            $email_content .= "Sustinere: " . ($consilier_sustinere ? 'Da' : 'Nespecificat') . "\n";
            $email_content .= "Respect: " . ($consilier_respect ? 'Da' : 'Nespecificat') . "\n";
            $email_content .= "Comunicare: " . ($consilier_comunicare ? 'Da' : 'Nespecificat') . "\n";
            
            $email_content .= "\n";
            
            $email_content .= "Departament Juridic: $juridic_rating\n";
            $email_content .= "Implicare: " . ($juridic_implicare ? 'Da' : 'Nespecificat') . "\n";
            $email_content .= "Disponibilitate: " . ($juridic_disponibilitate ? 'Da' : 'Nespecificat') . "\n";
            $email_content .= "Sustinere: " . ($juridic_sustinere ? 'Da' : 'Nespecificat') . "\n";
            $email_content .= "Respect: " . ($juridic_respect ? 'Da' : 'Nespecificat') . "\n";
            $email_content .= "Comunicare: " . ($juridic_comunicare ? 'Da' : 'Nespecificat') . "\n";
            
            $email_content .= "\n";
            
            $email_content .= "Departament Finisare: $finisare_rating\n";
            $email_content .= "Implicare: " . ($finisare_implicare ? 'Da' : 'Nespecificat') . "\n";
            $email_content .= "Disponibilitate: " . ($finisare_disponibilitate ? 'Da' : 'Nespecificat') . "\n";
            $email_content .= "Sustinere: " . ($finisare_sustinere ? 'Da' : 'Nespecificat') . "\n";
            $email_content .= "Respect: " . ($finisare_respect ? 'Da' : 'Nespecificat') . "\n";
            $email_content .= "Comunicare: " . ($finisare_comunicare ? 'Da' : 'Nespecificat') . "\n";
            
            $email_content .= "\n";
            
            $email_content .= "Dezvoltator: $dezvoltator_rating\n";
            $email_content .= "Implicare: " . ($dezvoltator_implicare ? 'Da' : 'Nespecificat') . "\n";
            $email_content .= "Disponibilitate: " . ($dezvoltator_disponibilitate ? 'Da' : 'Nespecificat') . "\n";
            $email_content .= "Sustinere: " . ($dezvoltator_sustinere ? 'Da' : 'Nespecificat') . "\n";
            $email_content .= "Respect: " . ($dezvoltator_respect ? 'Da' : 'Nespecificat') . "\n";
            $email_content .= "Comunicare: " . ($dezvoltator_comunicare ? 'Da' : 'Nespecificat') . "\n";
            
            if($observatii){
                $email_content .= "\n";
                $email_content .= "Observatii:\n" . $observatii . "\n";
            }
            
            if($nume_prenume || $telefon || $email){
                $email_content .= "\n";
                if($nume_prenume){
                    $email_content .= "Nume si prenume: " . $nume_prenume . "\n";
                }
                if($telefon){
                    $email_content .= "Telefon: " . $telefon . "\n";
                }
                if($email){
                    $email_content .= "Email: " . $email . "\n";
                }
            }else{
                $email_content .= "\nFormular trimis anonim.";
            }
            
            // Email subject
            $email_subject = 'Formular satisfactie client';

            // Recipient's email address (Replace with your email address)
            $recipient_email = 'contact@sudrezidential.ro';
            
            // Sender's name
            $sender_name = 'SudRezidential';
            
            // Headers
            $headers = array(
                'From: ' . $sender_name . ' <contact@sudrezidential.ro>',
            );
            
            // Send the email
            $email_sent = wp_mail($recipient_email, $email_subject, $email_content, $headers);
            

            if ($email_sent) {
                // Email sent successfully
                wp_send_json_success('SUCCESS');
            } else {
                // Error in sending email
                wp_send_json_error('Error occurred while sending the email. Please try again later.');
            }
        
}
