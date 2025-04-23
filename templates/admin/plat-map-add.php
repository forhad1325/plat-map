<?php
/* This templaet is for New plat Map Add from Dashboard */

$selected_term = get_post_meta($post->ID, '_plat_map_term', true);
$map_image_id = get_post_meta($post->ID, '_plat_map_image_id', true);
$coordinates = get_post_meta($post->ID, '_plat_map_coordinates', true) ?: [];

$terms = get_terms(['taxonomy' => 'es_category', 'hide_empty' => false]);
$map_image_url = $map_image_id ? wp_get_attachment_image_url($map_image_id, 'large') : '';
?>

<div class="plat_map_property_category_dropdown">
    <label><strong>Select Category:</strong></label>
    <select id="plat_map_category" name="plat_map_term">
        <option value="">-- Select --</option>
        <?php foreach ($terms as $term): ?>
            <option value="<?= esc_attr($term->term_id); ?>" <?= selected($selected_term, $term->term_id); ?>>
                <?= esc_html($term->name); ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

<div class="plat_map_options">
    <div class="plat_map_image">
        <label><strong>Upload plat Map Image:</strong></label><br>
        <input type="hidden" id="plat_map_image_id" name="plat_map_image_id" value="<?= esc_attr($map_image_id); ?>" />
        <button type="button" class="button upload_plat_map_image">Upload Image</button>
        <div id="plat_map_image_preview" style="margin-top: 10px;">
            <?php if ($map_image_url): ?>
                <img src="<?= esc_url($map_image_url); ?>" style="max-width:100%; height:auto;" />
            <?php endif; ?>
        </div>
    </div>

    <div id="property_coordinates_container" class="property_query_items">
        <?php
        if ($selected_term) {
            $this->render_property_coordinates_fields($selected_term, $coordinates);
        }
        ?>
    </div>
</div>

<?php if ($post->ID): ?>
    <div style="margin-top: 20px; background: #f9f9f9; padding: 10px; border: 1px dashed #ccc;">
        <strong>Use this shortcode to display the plat map on the frontend:</strong><br>
        <input type="text" readonly value='[plat_map id="<?php echo esc_attr($post->ID); ?>"]' style="width: 100%; margin-top: 5px;" onclick="this.select();" />
    </div>
<?php endif; ?>
