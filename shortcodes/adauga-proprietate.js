jQuery(document).ready(function ($) {
    $('#agent-select').select2({
        placeholder: 'Selecteaza agent', // Placeholder text
        allowClear: true, // Allow clearing the selection
    });
    $('#ansamblu-select').select2({
        placeholder: 'Selecteaza ansamblu', // Placeholder text
        allowClear: true, // Allow clearing the selection
    });
    
    $('#proprietate_speciala').change(function() {
        if(this.checked) {
            $( "#callCenterProprietatiPhone" ).prop( "checked", true );
            $( "#distances" ).hide();
        }else{
            $( "#callCenterPhone" ).prop( "checked", true );
            $( "#distances" ).show();
        }   
    });
});

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('adauga-proprietate-form');
    const responseDiv = document.getElementById('form-response');

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        // Force TinyMCE editor to sync its content with the textarea
        if (typeof tinyMCE !== 'undefined') {
            tinyMCE.triggerSave(); // Sync all TinyMCE editors
        }

        const formData = new FormData(form);
        formData.append('action', 'adauga_proprietate'); // Add action parameter

        fetch(ajax_object.ajax_url, {
            method: 'POST',
            body: formData,
        })
            .then((response) => {
        console.log(formData);
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then((data) => {
                console.log(data);
                if (data.success) {
                    responseDiv.innerHTML = `<p>${data.message}</p>`;
                    
                } else {
                    responseDiv.innerHTML = `<p>Error: ${data.message}</p>`;
                }
            })
            .catch((error) => {
                responseDiv.innerHTML = `<p>An error occurred: ${error.message}</p>`;
            });
    });
});




document.addEventListener('DOMContentLoaded', function () {
    
const vanzareInchiriereInputs = document.querySelectorAll('input[name="vanzare_sau_inchiriere"]');
const modPlataInchiriereField = document.querySelector('.modPlataInchiriere');

// Function to toggle the visibility of Mod Plata field
function toggleModPlataVisibility() {
    const selectedInput = document.querySelector('input[name="vanzare_sau_inchiriere"]:checked');
    if (selectedInput && selectedInput.value === 'inchiriere') {
        modPlataInchiriereField.style.display = 'block'; // Show Mod Plata
    } else {
        modPlataInchiriereField.style.display = 'none'; // Hide Mod Plata
    }
}

// Initially call the function to check the state on page load
toggleModPlataVisibility();

// Add event listeners to all vanzare_sau_inchiriere radio buttons
vanzareInchiriereInputs.forEach(input => {
    input.addEventListener('change', toggleModPlataVisibility);
});
    
    const pretPromoInputs = document.querySelectorAll('input[name="pret_promo"]');
    const tipPromotieInputs = document.querySelectorAll('input[name="tip_promotie"]');
    const tipPromotieField = document.querySelector('.radio-group input[name="tip_promotie"]').parentNode.parentNode;
    const pretPromotionalField = document.querySelector('.pret_promotionalField');
    const conditiePromotionalaField = document.querySelector('.conditie_promotionalaField');

    // Initial State
    function updateFields() {
        const pretPromoValue = document.querySelector('input[name="pret_promo"]:checked').value;
        const tipPromotieValue = document.querySelector('input[name="tip_promotie"]:checked')?.value;

        if (pretPromoValue === '1') {
            tipPromotieField.style.display = 'block'; // Show tip_promotie field

            if (tipPromotieValue === '0') {
                pretPromotionalField.style.display = 'block'; // Show pret_promotional
                conditiePromotionalaField.style.display = 'none'; // Hide conditie_promotionala
            } else if (tipPromotieValue === '1') {
                pretPromotionalField.style.display = 'none'; // Hide pret_promotional
                conditiePromotionalaField.style.display = 'block'; // Show conditie_promotionala
            }
        } else {
            tipPromotieField.style.display = 'none'; // Hide tip_promotie field
            pretPromotionalField.style.display = 'none'; // Hide pret_promotional
            conditiePromotionalaField.style.display = 'none'; // Hide conditie_promotionala
        }
    }

    // Event Listeners
    pretPromoInputs.forEach(input => {
        input.addEventListener('change', updateFields);
    });

    tipPromotieInputs.forEach(input => {
        input.addEventListener('change', updateFields);
    });

    // Run on Page Load
    updateFields();
    
        
            // Get elements
    const propertyTypeRadios = document.querySelectorAll('input[name="srz_property"]');
    
    const roomSelectionGroup = document.getElementById('room-selection-group');
    const roomSelectionGroupVila = document.getElementById('room-selection-group-vila');
    const compartimentare = document.getElementById('compartimentare');
    const stadiu_constructie = document.getElementById('stadiu_constructie');
    const room1Label = document.getElementById('room1-label');
    
    const suprafata_utilaX = document.getElementById('suprafata_utilaX');
    const suprafata_total_utilaX = document.getElementById('suprafata_total_utilaX');
    const suprafata_construitaX = document.getElementById('suprafata_construitaX');
    const numar_balcoaneX = document.getElementById('numar_balcoaneX');
    const an_constructieX = document.getElementById('an_constructieX');
    const numar_grupuri_sanitareX = document.getElementById('numar_grupuri_sanitareX');
    const etajX = document.getElementById('etajX');
    const regim_inaltimeX = document.getElementById('regim_inaltimeX');
    const suprafata_terenX = document.getElementById('suprafata_terenX');
    const locuri_de_parcareX = document.getElementById('locuri_de_parcareX');
    
    const latime_vitrinaX = document.getElementById('latime_vitrinaX');
    const label_latime_vitrina = document.getElementById('label_latime_vitrina');
    
    const inaltime_spatiu_comercialX = document.getElementById('inaltime_spatiu_comercialX');
    const certificat_urbanismX = document.getElementById('certificat_urbanismX');
    const puzX = document.getElementById('puzX');
    const categorie_terenX = document.getElementById('categorie_terenX');
    const finisajeX = document.getElementById('finisaje');
    const compartimentariX = document.getElementById('compartimentari');
    const tip_imobilX = document.getElementById('tip_imobil');
    const structuraX = document.getElementById('structura');
    const destinatieX = document.getElementById('destinatie');
    const bucatarieX = document.getElementById('bucatarie');
    const clasa_energeticaX = document.getElementById('clasa_energetica');
    const incalzireX = document.getElementById('incalzire');
    const accesX = document.getElementById('acces');
    const utilitatiX = document.getElementById('utilitati');
    const vecinatatiX = document.getElementById('vecinatati');
    const proprietate_speciala = document.getElementById('proprietate_speciala');
    
    // Function to update UI based on property type
    function updateRoomLabels() {
        const selectedType = document.querySelector('input[name="srz_property"]:checked')?.value;
        
        // Reset defaults
        roomSelectionGroup.style.display = 'none';
        roomSelectionGroupVila.style.display = 'none';
        compartimentare.style.display = 'none';
        stadiu_constructie.style.display = 'none';
        
        suprafata_utilaX.style.display = 'none';
        suprafata_total_utilaX.style.display = 'none';
        suprafata_construitaX.style.display = 'none';
        numar_balcoaneX.style.display = 'none';
        an_constructieX.style.display = 'none';
        numar_grupuri_sanitareX.style.display = 'none';
        etajX.style.display = 'none';
        regim_inaltimeX.style.display = 'none';
        suprafata_terenX.style.display = 'none';
        locuri_de_parcareX.style.display = 'none';
        latime_vitrinaX.style.display = 'none';
        inaltime_spatiu_comercialX.style.display = 'none';
        certificat_urbanismX.style.display = 'none';
        puzX.style.display = 'none';
        categorie_terenX.style.display = 'none';
        finisajeX.style.display = 'none';
        compartimentariX.style.display = 'none';
        tip_imobilX.style.display = 'none';
        structuraX.style.display = 'none';
        destinatieX.style.display = 'none';
        bucatarieX.style.display = 'none';
        clasa_energeticaX.style.display = 'none';
        incalzireX.style.display = 'none';
        accesX.style.display = 'none';
        utilitatiX.style.display = 'none';
        vecinatatiX.style.display = 'none';
        
        if (selectedType === 'apartamente') { 
            roomSelectionGroup.style.display = 'block';
            roomSelectionGroupVila.style.display = 'none';
            room1Label.textContent = '1 camera';
            compartimentare.style.display = 'block'; 
            stadiu_constructie.style.display = 'block'; 
            
            suprafata_utilaX.style.display = 'block';
            suprafata_total_utilaX.style.display = 'block';
            suprafata_construitaX.style.display = 'block';
            numar_balcoaneX.style.display = 'block';
            an_constructieX.style.display = 'block';
            numar_grupuri_sanitareX.style.display = 'block';
            etajX.style.display = 'block';
            finisajeX.style.display = 'block';
            tip_imobilX.style.display = 'block';
            compartimentariX.style.display = 'block';
            regim_inaltimeX.style.display = 'block';
            structuraX.style.display = 'block';
        } else if (selectedType === 'vila') {
            roomSelectionGroup.style.display = 'block';
            roomSelectionGroupVila.style.display = 'flex';
            room1Label.textContent = '1 camere';
            stadiu_constructie.style.display = 'block'; 
            
            suprafata_utilaX.style.display = 'block';
            suprafata_total_utilaX.style.display = 'block';
            suprafata_construitaX.style.display = 'block';
            numar_balcoaneX.style.display = 'block';
            an_constructieX.style.display = 'block';
            regim_inaltimeX.style.display = 'block';
            suprafata_terenX.style.display = 'block';
            locuri_de_parcareX.style.display = 'block';
            finisajeX.style.display = 'block';
            numar_grupuri_sanitareX.style.display = 'block';
            tip_imobilX.style.display = 'block';
            compartimentariX.style.display = 'block';
            structuraX.style.display = 'block';
            destinatieX.style.display = 'block';
            bucatarieX.style.display = 'block';
            clasa_energeticaX.style.display = 'block';
        } else if (selectedType === 'spatiu_comercial') {
            stadiu_constructie.style.display = 'block'; 
            an_constructieX.style.display = 'block';
            regim_inaltimeX.style.display = 'block';
            suprafata_terenX.style.display = 'block';
            suprafata_total_utilaX.style.display = 'block';
            locuri_de_parcareX.style.display = 'block';
            latime_vitrinaX.style.display = 'block';
            label_latime_vitrina.textContent = 'Latime vitrina';
            finisajeX.style.display = 'block';
            tip_imobilX.style.display = 'block';
            compartimentariX.style.display = 'block';
            inaltime_spatiu_comercialX.style.display = 'block';
            structuraX.style.display = 'block';
            incalzireX.style.display = 'block';
            destinatieX.style.display = 'block';
        } else if (selectedType === 'teren') {
            suprafata_terenX.style.display = 'block';
            certificat_urbanismX.style.display = 'block';
            puzX.style.display = 'block';
            categorie_terenX.style.display = 'block';
            accesX.style.display = 'block';
            utilitatiX.style.display = 'block';
            vecinatatiX.style.display = 'block';
            regim_inaltimeX.style.display = 'block';
            latime_vitrinaX.style.display = 'block';
            label_latime_vitrina.textContent = 'Deschidere';
        } else if (selectedType === 'cladire') {
            suprafata_utilaX.style.display = 'block';
            suprafata_total_utilaX.style.display = 'block';
            suprafata_construitaX.style.display = 'block';
            an_constructieX.style.display = 'block';
            regim_inaltimeX.style.display = 'block';
            suprafata_terenX.style.display = 'block';
            puzX.style.display = 'block';
            finisajeX.style.display = 'block';
            incalzireX.style.display = 'block';
            accesX.style.display = 'block';
            utilitatiX.style.display = 'block';
            vecinatatiX.style.display = 'block';
            latime_vitrinaX.style.display = 'block';
            label_latime_vitrina.textContent = 'Deschidere';
        }
    }

    // Add event listeners to property type radios
    propertyTypeRadios.forEach(radio => {
        radio.addEventListener('change', updateRoomLabels);
    });

    // Initialize the UI on page load
    updateRoomLabels();
});


    

    // Function to toggle items in the textarea
    function addToTextarea(item) {
        const textarea = document.getElementById('finisajeTextarea');
        let currentContent = textarea.value;

        // Split existing content into an array
        let items = currentContent ? currentContent.split(', ') : [];

        if (items.includes(item)) {
            // Remove the item if it exists
            items = items.filter(i => i !== item);
        } else {
            // Add the item if it doesn't exist
            items.push(item);
        }

        // Update the textarea with items separated by commas
        textarea.value = items.join(', ');

        // Update the list item styles
        updateListStyles(items);
    }

    // Function to clear all items from the textarea
    function clearTextarea() {
        const textarea = document.getElementById('finisajeTextarea');
        textarea.value = '';
        updateListStyles([]); // Clear all styles
    }

    // Function to update the list item styles
    function updateListStyles(selectedItems) {
        const listItems = document.querySelectorAll('#finisaje ul li');
        listItems.forEach(li => {
            if (selectedItems.includes(li.textContent.trim())) {
                li.style.backgroundColor = '#d4edda'; // Greenish background for selected items
            } else {
                li.style.backgroundColor = ''; // Default background for unselected items
            }
        });
    }

    // Initialize the list styles based on initial textarea content
    document.addEventListener('DOMContentLoaded', () => {
        const textarea = document.getElementById('finisajeTextarea');
        const initialItems = textarea.value ? textarea.value.split(', ') : [];
        updateListStyles(initialItems);
    });