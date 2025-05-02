<?php
// Helper function to format prices
function format_price($price, $mod_plata = '', $vat = '')
{
    return number_format((float) $price, 0, '.', '.') . ' €' . $mod_plata . ($vat ? '<span class="vat"> ' . esc_html($vat) . '</span>' : '');
}
// Helper function to get metadata with a fallback
function get_meta($post_id, $key, $default = '')
{
    $meta = get_post_meta($post_id, $key, true);
    return $meta !== '' ? $meta : $default;
}

// Helper function to determine VAT label
function get_vat_label($vat)
{
    return $vat === 'nuseaplica' ? '(Nu se aplica TVA)' : '(+ TVA)';
}

function format_string($string)
{
    // Replace underscores with spaces
    $string = str_replace('_', ' ', $string);
    // Convert the first character of the string to uppercase
    $string = ucfirst(strtolower($string));
    return $string;
}

function formatPhoneNumber($number) {
    // Remove any non-numeric characters
    $cleanNumber = preg_replace('/[^0-9+]/', '', $number);

    // If the number starts with '+40', it's a Romanian number, so we format it accordingly
    if (substr($cleanNumber, 0, 3) === '+40') {
        // Format as a link number (+40 is part of the country code, so we leave it)
        $linkNumber = $cleanNumber;

        // Remove the '+40' from the number for the visible format
        $visibleNumber = substr($cleanNumber, 3);
    } else {
        // If no '+40' is present, assume it's a local number
        $linkNumber = '+4' . $cleanNumber;
        $visibleNumber = substr($cleanNumber, 0, 4) . '.' . substr($cleanNumber, 4, 3) . '.' . substr($cleanNumber, 7);
    }

    // Format visible number with dots for readability
    return [
        'visible' => $visibleNumber,
        'link' => $linkNumber
    ];
}


function render_detail($icon, $label, $value, $suffix = '')
{
    if (!$value)
        return '';
    return "
        <div class='wp-block-group is-vertical is-content-justification-center is-layout-flex wp-container-core-group-is-layout-5 wp-block-group-is-layout-flex'>{$icon}{$label}" . esc_html($value) . "{$suffix}
        </div>";
}