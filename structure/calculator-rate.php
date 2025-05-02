<?php
add_action('wp_ajax_nopriv_calc_rate', 'calc_rate');
add_action('wp_ajax_calc_rate', 'calc_rate');
function calc_rate() {
    // Get exchange rate and initialize parameters
    $euro = get_option('curs_valutar_euro');
    $ircc = 5.66;  // Default value if not set
    $marja_fixa = 2.3; // Fixed margin
    $loan = $_POST['suma']; 
    $avans = $_POST['avans']; 
    $loan_eur = $loan - $avans; 
    $ani = $_POST['perioada']; 
    $loan = ($loan - $avans) * $euro; 
    $avans_eur = $avans * $euro;

    // Interest rate calculation (ensure it's not zero)
    $interest = ($marja_fixa + $ircc) / 100 / 12;
    if ($interest == 0) {
        $interest = 0.01;  // Minimum interest rate to prevent zero interest
    }
    
    // Loan parameters
    $perioada_an = 12;
    $months = $perioada_an * $ani;

    // Helper functions
    function checkZero($value, $epsilon = 1e-6) {
        return abs($value) < $epsilon ? 0.0 : $value;
    }

    function pmt($rate, $periods, $present_value, $future_value = 0.0, $beginning = false) {
        $when = $beginning ? 1 : 0;
        if ($rate == 0) {
            return -($future_value + $present_value) / $periods;
        }
        return -($future_value + ($present_value * pow(1 + $rate, $periods))) / ((1 + $rate * $when) / $rate * (pow(1 + $rate, $periods) - 1));
    }

    function ipmt($rate, $period, $periods, $present_value, $future_value = 0.0, $beginning = false) {
        if ($period < 1 || $period > $periods) {
            return NAN;
        }
        if ($rate == 0) {
            return 0;
        }
        $payment = pmt($rate, $periods, $present_value, $future_value, $beginning);
        if ($beginning) {
            $interest = (fv($rate, $period - 2, $payment, $present_value, $beginning) - $payment) * $rate;
        } else {
            $interest = fv($rate, $period - 1, $payment, $present_value, $beginning) * $rate;
        }
        return checkZero($interest);
    }

    function ppmt($rate, $period, $periods, $present_value, $future_value = 0.0, $beginning = false) {
        $payment = pmt($rate, $periods, $present_value, $future_value, $beginning);
        $ipmt = ipmt($rate, $period, $periods, $present_value, $future_value, $beginning);
        return $payment - $ipmt;
    }

    function fv($rate, $periods, $payment, $present_value, $beginning = false) {
        $when = $beginning ? 1 : 0;
        if ($rate == 0) {
            $fv = -($present_value + ($payment * $periods));
            return checkZero($fv);
        }
        $initial = 1 + ($rate * $when);
        $compound = pow(1 + $rate, $periods);
        $fv = -(($present_value * $compound) + (($payment * $initial * ($compound - 1)) / $rate));
        return checkZero($fv);
    }

    // Calculate monthly payment and total credit
    $rata_lunara = round(pmt($interest, $months, $loan, 0, false), 2) * -1;
    $total_credit = round($months * $rata_lunara, 2);
    
    // Start table generation for loan schedule
    $table_calc = '<table class="scadentar"><thead><tr><td>Luna</td><td>Rata</td><td>Dobanda</td><td>Principal</td><td>Sold</td></tr></thead><tbody>';
    $sold_f = $loan;
    
    for ($range_month = 1; $range_month <= $months; $range_month++) {
        // Calculate interest and principal for this month
        $dobanda = round(ipmt($interest, $range_month, $months, $sold_f), 2) * -1;
        $principal_c = ppmt($interest, $range_month, $months, $sold_f) * -1;
        $principal = round($principal_c, 2);

        // Update remaining loan balance
        if ($range_month === 1) {
            $sold_f = $sold_f;
        } else {
            $sold_f -= $principal_c;
            $sold_f = $sold_f < 0 ? abs($sold_f) : $sold_f;
        }

        // Add row to the table
        $table_calc .= '<tr><td>' . $range_month . '</td><td>' . $rata_lunara . '</td><td>' . $dobanda . '</td><td>' . $principal . '</td><td>' . round($sold_f, 2) . '</td></tr>';
    }
    $table_calc .= '</tbody></table>';

    // Prepare response
    $response_error[] = array(
        'scadentar' => $table_calc,
        'suma' => round($loan, 2),
        'avans_eur' => round($avans_eur, 2),
        'luni' => $months,
        'rata_lunara' => $rata_lunara,
        'total_c' => $total_credit
    );

    wp_send_json($response_error);
}
