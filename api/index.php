<?php
function verify_basic_auth($username, $password) {
    return $username === MICROSOFT_username && $password === MICROSOFT_password;
}

function get_previous_value($type) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'avg_price_chart';

    $sql = "SELECT data FROM {$table_name} ORDER BY id DESC LIMIT 1";
    $last_record = $wpdb->get_var($sql); // No need for prepare() since no user input is involved

    if ($last_record) {
        $decoded_data = json_decode($last_record, true);
        if (isset($decoded_data['data']) && is_array($decoded_data['data'])) {
            foreach ($decoded_data['data'] as $entry) {
                if (isset($entry['tip']) && $entry['tip'] === $type) {
                    return sanitize_text_field($entry['average']); // Prevent XSS attacks
                }
            }
        }
    }
    return '34';
}


function handle_custom_endpoint(WP_REST_Request $request) {
    global $wpdb;

    $table_name = $wpdb->prefix . 'avg_price_chart';
    $known_types = ["Garsoniera", "Garsoniera Dubla", "Studio", "Apartament 2 Camere", "Apartament 3 Camere", "Apartament 4 Camere"];

    // Decode JSON and validate
    $received_data = json_decode($request->get_body(), true);
    if (!isset($received_data['data']) || !is_array($received_data['data'])) {
        return new WP_REST_Response(['error' => 'Invalid JSON format'], 400);
    }

    // Check and update missing values
    foreach ($received_data['data'] as &$entry) {
        if (in_array($entry['tip'], $known_types) && is_null($entry['average'])) {
            $entry['average'] = get_previous_value($entry['tip']);
        }
    }

    // Encode JSON safely
    $compressed_json_string = wp_json_encode($received_data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    if (!$compressed_json_string) {
        return new WP_REST_Response(['error' => 'JSON encoding failed'], 500);
    }

    // Insert into database with error handling
    $inserted = $wpdb->insert(
        $table_name,
        ['data' => $compressed_json_string, 'timestamp' => current_time('mysql')]
    );

    if (!$inserted) {
        return new WP_REST_Response(['error' => 'Database insertion failed'], 500);
    }

    return new WP_REST_Response(['message' => 'Data inserted successfully.'], 200);
}



add_action( 'rest_api_init', function() {
    register_rest_route( 'microsoft/v1', '/sales_chart', array(
        'methods' => 'POST',
        'callback' => 'handle_custom_endpoint',
        'permission_callback' => function() {
            if ( !isset( $_SERVER['PHP_AUTH_USER'] ) || !isset( $_SERVER['PHP_AUTH_PW'] ) ) {
                return new WP_Error( 'rest_forbidden', 'Authentication header missing.', array( 'status' => 403 ) );
            }

            if ( !verify_basic_auth( $_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'] ) ) {
                return new WP_Error( 'rest_forbidden', 'Invalid credentials.', array( 'status' => 403 ) );
            }

            return true;
        },
    ) );
} );


function render_price_chart() {
    // Check if the current user is an admin
    global $wpdb;
    $table_name = $wpdb->prefix . 'avg_price_chart';
    $results = $wpdb->get_results(
        "SELECT * FROM {$table_name} ORDER BY id DESC LIMIT 24", 
        ARRAY_A  // This returns the results as associative arrays
    );
    $results = array_reverse($results);
    ob_start();
    ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <canvas id="myChart" width="400" height="200"></canvas>
    <div class="disclaimer_srz"><p>Disclaimer: Datele prezentate in grafic/analiza sunt strict informative si nu au caracter angajant, de orice natura pentru SudRezidential Real Estate SRL sau SudRezidential Broker SRL. Datele prezentate reprezinta o medie a pretului de tranzactionare pentru proprietatile aflate in portofoliul Sud Rezidential, a caror valoare nu depaseste o anumita suma stabilita ca valoare maxima de referinta. Informatiile prezentate nu reprezinta o recomandare de a cumpara sau a vinde un imobil, fiind prezentate in scop statistic si informativ. Nu ne asumam raspunderea pentru greselile de afisare sau erori ale sistemelor informatice, care pot afecta oricare dintre informatiile furnizate.</p></div>
    <script>let rawData = [<?php foreach ($results as $result) {echo $result['data'].',';}?>];</script>
    <script src="<?php echo get_stylesheet_directory_uri();?>/js/avg-price.js"></script>
    <?php
    return ob_get_clean();
}
add_shortcode('avg_price_chart', 'render_price_chart');

// Step 1: Add a custom cron interval
function custom_cron_schedule($schedules) {
    // Add custom schedules (2 AM and 2 PM daily)
    $schedules['twicedaily'] = array(
        'interval' => 12 * 60 * 60, // 12 hours in seconds
        'display'  => __('Twice Daily'),
    );
    return $schedules;
}
add_filter('cron_schedules', 'custom_cron_schedule');

// Step 2: Schedule the event
function schedule_curs_bnr_event() {
    // Schedule the event to run twice a day (12 hours interval)
    if (!wp_next_scheduled('get_bnr_curs_euro')) {
        wp_schedule_event(strtotime('05:00:00'), 'twicedaily', 'get_bnr_curs_euro');
        wp_schedule_event(strtotime('14:00:00'), 'twicedaily', 'get_bnr_curs_euro');
    }
}
add_action('wp', 'schedule_curs_bnr_event');

// Step 3: The function to grab the Euro value and store it
function curs_bnr() {
    // Fetch the XML data from the BNR website
    $content = @file_get_contents("https://www.bnro.ro/nbrfxrates.xml");
    
    if ($content === FALSE) {
        // Handle the error if the content cannot be fetched
        return;
    }
    
    // Parse the XML data
    $data = @simplexml_load_string($content);
    
    if ($data === FALSE) {
        // Handle the error if XML parsing fails
        return;
    }
    
    // Find the Euro rate by searching for the currency code EUR
    foreach ($data->Body->Cube->Rate as $rate) {
        if ((string)$rate['currency'] === 'EUR') {
            // Extract the Euro rate
            $euro = (string)$rate;
            
            // Store the Euro rate in the WordPress options table
            update_site_option('curs_valutar_euro', $euro);
            break;
        }
    }
}

// Step 4: Hook the function to the cron job
add_action('get_bnr_curs_euro', 'curs_bnr');