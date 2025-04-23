<?php
if (!defined('ABSPATH')) exit;

$properties = get_posts([
    'post_type' => 'properties',
    'posts_per_page' => -1,
    'tax_query' => [[
        'taxonomy' => 'es_category',
        'field'    => 'term_id',
        'terms'    => $term_id,
    ]],
]);

if ($properties) {
    echo '<table class="widefat"><thead><tr><th>Property</th><th>X (left %)</th><th>Y (top %)</th></tr></thead><tbody>';
    foreach ($properties as $property) {
        $id = $property->ID;
        $x = $coordinates[$id]['x'] ?? '';
        $y = $coordinates[$id]['y'] ?? '';

        // Fetch the property status from the ACF field
        $status = get_field('property_status', $id); // Assuming 'property_status' is the ACF field key
        if (!$status) {
            $status = 'default'; // Fallback if no status is found
        }

        echo "<tr data-id='{$id}' data-title='" . esc_attr($property->post_title) . "' data-status='{$status}'>";
        echo "<td>{$property->post_title}</td>";
        echo "<td><input type='number' step='1' name='property_coords[{$id}][x]' value='{$x}' class='coord-input' /></td>";
        echo "<td><input type='number' step='1' name='property_coords[{$id}][y]' value='{$y}' class='coord-input' /></td>";
        echo "</tr>";
    }
    echo '</tbody></table>';
} else {
    echo '<p>No properties found for selected category.</p>';
}

echo '<div class="plat-map-pin-preview-layer"></div>';
?>