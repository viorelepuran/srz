(function($) {
    $(document).ready(function() {
        // Function to handle slider value update
        function updateSliderValue(slider, input) {
            input.addEventListener('change', function() {
                slider.noUiSlider.set(this.value);
            });
        }

        // Slider initialization
        var slider = document.getElementById('amount-slider');
        var amount = document.getElementById('amount');
        noUiSlider.create(slider, {
            start: parseInt(amount.placeholder),
            step: parseInt(amount.dataset.step),
            range: {
                "min": parseInt(amount.dataset.min),
                "max": parseInt(amount.dataset.max)
            }
        });

        var slider_y = document.getElementById('period-slider');
        var amount_y = document.getElementById('period');
        noUiSlider.create(slider_y, {
            start: parseInt(amount_y.placeholder),
            step: parseInt(amount_y.dataset.step),
            range: {
                "min": parseInt(amount_y.dataset.min),
                "max": parseInt(amount_y.dataset.max)
            }
        });

        var slider_avans = document.getElementById('avans-slider');
        var avans = document.getElementById('avans');
        noUiSlider.create(slider_avans, {
            start: parseInt(avans.placeholder),
            step: parseInt(avans.dataset.step),
            range: {
                "min": parseInt(avans.dataset.min),
                "max": parseInt(avans.dataset.max)
            }
        });

        // Updating input value when slider changes
        slider.noUiSlider.on('update', function(values, handle) {
            amount.value = parseFloat(values[handle]).toFixed(0);
        });

        slider_y.noUiSlider.on('update', function(values, handle) {
            amount_y.value = parseFloat(values[handle]).toFixed(0);
        });

        slider_avans.noUiSlider.on('update', function(values, handle) {
            avans.value = parseFloat(values[handle]).toFixed(0);
        });

        // Handle the AJAX request when the button is clicked
        $(".credit24_btn_rate").click(function() {
            $("#calculator-rate-wrapper").append('<div class="preloader-cc"><img src="/wp-content/uploads/2023/03/icons8-loading-circle.gif"/></div>');
            var suma = $("#amount").val();
            var avans = $("#avans").val();
            var perioada = $("#period").val();
            var data = {
                'action': 'calc_rate',
                'suma': suma,
                'avans': avans,
                'perioada': perioada,
            };

            jQuery.post(ajax_object.ajax_url, data, function(response) {
                var scadentar = response[0]["scadentar"];
                var suma = response[0]["suma"];
                var avans_eur = response[0]["avans_eur"];
                var luni = response[0]["luni"];
                var rata_lunara = response[0]["rata_lunara"];
                var total_c = response[0]["total_c"];

                setTimeout(function() {
                    $("#show_rate").addClass('overflow');
                    $("#show_rate").empty();
                    $("#show_rate").append(scadentar);
                    $("#calculator-rate-wrapper").empty();
                    $("#recalculate").show();
                    $("#calculator-rate-wrapper").append('<div class="credit_details"><strong>Suma imprumutata: </strong>' + suma + ' RON</div><div class="credit_details"><strong>Avans: </strong>' + avans_eur + ' RON</div><div class="credit_details"><strong>Perioada: </strong>' + luni + ' luni</div><div class="credit_details"><strong>Rata Lunara: </strong>' + rata_lunara + ' RON</div><div class="credit_details"><strong>Total Credit: </strong>' + total_c + ' RON</div>');
                }, 1500);
            });
        });
    });
})(jQuery);
