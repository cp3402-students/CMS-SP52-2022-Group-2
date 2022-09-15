<?php
/**
 * Template for generating HTML structure of a table
 *
 * Uses for all TABLEON shortcodes
 *
 * @see https://posts-table.com/codex/
 * @version 1.0.0
 */
if (!defined('ABSPATH'))
    die('No direct access allowed');

if (!isset($action)) {
    $action = '';
}

if (!isset($table_id)) {
    $table_id = 0;
}

if (!isset($classes)) {
    $classes = '';
}

if (!isset($search_data_key)) {
    $search_data_key = 'post_title';
}

if (!isset($text_search_min_symbols)) {
    $text_search_min_symbols = '';
}

if (!isset($placeholder)) {
    $placeholder = '';
}

if (!isset($hide_filter_form)) {
    $hide_filter_form = false;
}

if (!isset($has_filter)) {
    $has_filter = false;
}

if (!isset($sorting_position)) {
    $sorting_position = 'right';
}

if (!isset($orderby_select)) {
    $orderby_select = '';
}

if (!isset($published)) {
    $published = true;
}

if (!isset($skin)) {
    $skin = '';
}

if (!isset($post_type)) {
    $post_type = '';
}
?>

<?php if (boolval($published)): ?>

    <div class='data-table-23 tableon-data-table <?php if (isset($table_view) AND $table_view) echo 'data-table-23-' . esc_attr__($table_view); ?> <?php esc_attr_e($action) ?> <?php esc_attr_e($classes) ?>' data-skin="<?php esc_attr_e($skin) ?>" data-post-type="<?php esc_attr_e($post_type) ?>" id='<?php esc_attr_e($table_html_id) ?>'>
        <input type="search" data-key="<?php esc_attr_e($search_data_key) ?>" value="" minlength="<?php esc_attr_e($text_search_min_symbols) ?>" class="tableon-text-search" <?php if ($hide_text_search): ?>style="display: none;"<?php endif; ?> placeholder="<?php esc_attr_e($placeholder) ?>" />


        <?php if (isset($filter) AND!empty($filter)): ?>
            <div class="tableon-filter-data" style="display: none;"><?php
                echo wp_kses($filter, [
                    'div' => [
                        'class' => true,
                        'id' => true,
                        'data-skin' => true,
                        'data-table-id' => true,
                        'style' => true,
                        'data-post-type' => true
                    ],
                    'input' => [
                        'type' => true,
                        'data-key' => true,
                        'value' => true,
                        'minlength' => true,
                        'class' => true,
                        'placeholder' => true
                    ],
                    'table' => [
                        'class' => true,
                        'id' => true
                    ],
                    'thead' => [],
                    'tfoot' => [],
                    'tbody' => [
                        'style' => true,
                    ],
                    'th' => [
                        'data-key' => true,
                        'style' => true,
                        'class' => true
                    ],
                    'tr' => [
                        'data-pid' => true
                    ],
                    'td' => [
                        'class' => true,
                        'data-field-type' => true,
                        'data-pid' => true,
                        'data-key' => true,
                        'data-field' => true
                    ],
                    'a' => [
                        'href' => true,
                        'onclick' => true,
                        'class' => true
                    ],
                    'i' => [
                        'class' => true
                    ]
                ]);
                ?></div>

            <?php
            if ($has_filter):
                ?>
                <?php
                if ($hide_filter_form) {
                    echo TABLEON_HELPER::draw_html_item('a', [
                        'href' => 'javascript: void(0);',
                        'onclick' => 'javascript: tableon_show_filter(this);void(0);',
                        'class' => 'tableon-btn tableon-filter-show-btn'
                            ], apply_filters('tableon_show_filter_btn_txt', '<i class="tableon-icon">&#xf0b0;</i>'));
                }
                ?>
                <div class="tableon-filter-list  <?php if ($hide_filter_form): ?>tableon-hidden<?php endif; ?>"></div>
            <?php endif; ?>

            <div class="tableon-clearfix"></div>
        <?php endif; ?>

        <div class="tableon-order-select-zone" <?php if ($sorting_position === 'right'): ?>style="float:right;"<?php endif; ?>>
            <?php
            if (!empty($orderby_select)):
                $first_option = [0 => esc_html__('Sorted by table', 'posts-table-filterable')];
                $orderby_select = array_merge($first_option, $orderby_select);
                ?>
                <div class="tableon-order-select" style="display: none;"><?php echo json_encode($orderby_select) ?></div>
            <?php endif; ?>
        </div>  

        <div class="tableon-clearfix"></div>

        <div class="table23-place-loader"><?php echo TABLEON_Vocabulary::get(esc_html__('Loading ...', 'posts-table-filterable')) ?></div>
        <table class="tableon-table"></table>

    </div>


    <?php echo(isset($style) ? "<style data-table23-skin='" . esc_attr($skin) . "' data-table23-skin-of='" . esc_attr($table_html_id) . "'>" . wp_kses_post($style) . '</style>' : '') ?>
<?php else: ?>

    <div class="tableon-notice"><strong><?php printf(TABLEON_Vocabulary::get(esc_html__('Table %s is not active!', 'posts-table-filterable')), $table_id) ?></strong></div>

<?php endif; ?>