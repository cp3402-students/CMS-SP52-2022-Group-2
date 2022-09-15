<?php
/**
 * Template for wrapping shortcode [tableon] into <div> to simulate drop-down list
 * 
 * @see https://posts-table.com/shortcode/tableon_drop_down/
 * @version 1.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$table_html_id = uniqid('t-');
?>
<div class="tableon-text-search-container" style="width: <?php echo esc_attr($width) ?>;" data-table-id="<?php echo esc_attr($table_html_id) ?>">
    <input type="search" value="" autocomplete="off" placeholder="<?php echo esc_attr($placeholder) ?>" /><br />
    <div class="tableon-text-search-wrapper" style="display: none; max-height: <?php echo esc_attr($height) ?>px; overflow: auto; overflow-x: hidden;"><?php echo do_shortcode("[tableon id=" . esc_attr($table_id) . " skin='" . esc_attr($skin) . "' table_html_id='" . esc_attr($table_html_id) . "' not_load_on_init=1]") ?></div>
</div>

