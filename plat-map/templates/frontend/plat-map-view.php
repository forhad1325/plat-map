<?php
/* This template is for Map Frontview Shortcode */

if (!defined('ABSPATH')) exit;

$image_url = $args['image_url'];
$properties = $args['properties'];
$plugin_url = $args['plugin_url'];
?>

<div class="plat-map-frontend-wrapper" style="position: relative;">
    <img src="<?php echo esc_url($image_url); ?>" alt="Map" style="width: 100%; height: auto;">
    <div class="plat-map-pin-preview-layer">
        <?php foreach ($properties as $pid => $coords): 
            $x = isset($coords['x']) ? floatval($coords['x']) : '';
            $y = isset($coords['y']) ? floatval($coords['y']) : '';
            if ($x === '' || $y === '') continue;

            $title = get_the_title($pid);
            // Fetch the property status from the ACF field
            $status = get_field('property_status', $pid); // Assuming 'property_status' is the ACF field key
            if (!$status) {
                $status = 'default'; // Fallback if no status is found
            }

            $icon_map = [
                'build-your-dream'  => 'red.svg',
                'make-it-yours'     =>  'blue.svg',
                'coming-soon'       =>  'orange.svg',
                'move-in-ready'     =>  'green.svg',
            ];
            $icon_file = $icon_map[$status] ?? 'default.svg';
            $icon_url = $plugin_url . 'assets/images/status-icons/' . $icon_file;
        ?>
            <div class="plat-map-pin" style="position:absolute; left:<?php echo esc_attr($x); ?>%; top:<?php echo esc_attr($y); ?>%;" data-title="<?php echo esc_attr($title); ?>">
                <img src="<?php echo esc_url($icon_url); ?>" alt="<?php echo esc_attr($status); ?>">
            </div>
        <?php endforeach; ?>
    </div>
</div>
