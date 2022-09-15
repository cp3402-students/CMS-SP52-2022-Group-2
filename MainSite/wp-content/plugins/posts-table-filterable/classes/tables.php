<?php

/**
 * TABLEON Tables
 *
 * Handles system tables
 *
 * @since   1.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

include_once TABLEON_PATH . 'classes/columns.php';
include_once TABLEON_PATH . 'classes/columns-fields-options.php';

include_once TABLEON_PATH . 'classes/tables-meta.php';
include_once TABLEON_PATH . 'classes/tables-filter.php';
include_once TABLEON_PATH . 'classes/tables-options.php';

class TABLEON_Tables {

    private $db_table = 'tableon_tables';
    private $db = null;

    public function __construct() {
        global $wpdb;
        $this->db = &$wpdb;
        $this->db_table = $this->db->prefix . $this->db_table;
        add_action('admin_init', array($this, 'admin_init'), 9999);
    }

    /**
     * Hook admin_init
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function admin_init() {
        if (TABLEON_HELPER::can_manage_data()) {

            $this->add_table_action();

            add_action('wp_ajax_tableon_create_table', array($this, 'create'));
            add_action('wp_ajax_tableon_save_table_field', array($this, 'update'));
            add_action('wp_ajax_tableon_delete_table', array($this, 'delete'));
            add_action('wp_ajax_tableon_clone_table', array($this, 'clone'));
        }
    }

    /**
     * Table action 
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function add_table_action() {
        add_action('tableon_admin_table', function () {
            return [
                0 => [],
                'thumbnail' => [
                    'title' => esc_html__('Thumb', 'posts-table-filterable'),
                    'order' => FALSE
                ],
                'title' => [
                    'title' => esc_html__('Title', 'posts-table-filterable'),
                    'order' => 'asc',
                    'editable' => 'textinput'
                ],
                'shortcode' => [
                    'title' => TABLEON_HELPER::draw_html_item('a', [
                        'href' => 'https://posts-table.com/shortcode/tableon/',
                        'target' => '_blank'
                            ], esc_html__('Shortcode', 'posts-table-filterable')),
                    'order' => FALSE
                ],
                'status' => [
                    'title' => esc_html__('Published', 'posts-table-filterable'),
                    'order' => FALSE
                ],
                'post_type' => [
                    'title' => esc_html__('Post type', 'posts-table-filterable'),
                    'editable' => 'select'
                ],
                'skin' => [
                    'title' => esc_html__('Skin', 'posts-table-filterable'),
                    'editable' => 'select'
                ],
                'actions' => [
                    'title' => esc_html__('Actions', 'posts-table-filterable'),
                    'order' => FALSE
                ]
            ];
        });
    }

    /**
     * Get table data
     *
     * @since 1.0.0
     * @param int $table_id table ID
     * @return array
     */
    public function get($table_id) {
        static $tables = [];

        if ($table_id > 0) {
            if (!isset($tables[$table_id])) {
                $tables[$table_id] = $this->db->get_row("SELECT * FROM {$this->db_table} WHERE id = {$table_id}", ARRAY_A);
            }
        } else {
            return [];
        }

        return $tables[$table_id];
    }

    /**
     * Create table by ajax
     *
     * @since 1.0.0
     * 
     * @return output string
     */
    public function create() {
        $this->db->insert($this->db_table, [
            'title' => esc_html__('New Table', 'posts-table-filterable'),
            'table_action' => TABLEON_Default::$action
        ]);

        $table_id = intval($this->db->insert_id);

        $cols = new TABLEON_Columns();
        $cols->create($table_id, 0, 'ID', 'id');
        $cols->create($table_id, 0, esc_html__('Thumbnail', 'posts-table-filterable'), 'thumbnail');
        $cols->create($table_id, 0, esc_html__('Title', 'posts-table-filterable'), 'post_title');
        $cols->create($table_id, 0, esc_html__('Gallery', 'posts-table-filterable'), 'gallery', sprintf(esc_html__('Details: %s', 'posts-table-filterable'), 'https://posts-table.com/how-to-attach-images-to-the-gallery-cell/'));

        die(json_encode($this->get_admin_table_rows()));
    }

    /**
     * Update table by ajax
     *
     * @since 1.0.0
     * 
     * @return output string
     */
    public function update() {
        $table_id = intval($_REQUEST['post_id']);
        $field = sanitize_key($_REQUEST['field']);
        $value = TABLEON_HELPER::sanitize_array($_REQUEST['value']);

        if ($table_id > 0) {
            switch ($field) {
                case 'title':
                case 'skin':
                case 'post_type':
                    $value = TABLEON_HELPER::sanitize_text($value);
                    $this->update_field($table_id, $field, $value);
                    break;

                case 'status':
                case 'thumbnail':
                    $value = intval($value);
                    $this->update_field($table_id, $field, $value);
                    break;
            }
        }

        die(json_encode([
            'value' => $value
        ]));
    }

    /**
     * Update table field
     *
     * @since 1.0.0
     * @param int $table_id table ID
     * @param string $field
     * @param string$value
     * @return void
     */
    public function update_field($table_id, $field, $value) {
        $this->db->update($this->db_table, [$field => $value], array('id' => $table_id));
    }

    /**
     * Delete table by ajax
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function delete() {
        $table_id = intval($_REQUEST['id']);

        //columns
        if ($columns = tableon()->columns->get_table_columns($table_id, ['fields' => 'id'])) {
            foreach ($columns as $c) {
                tableon()->columns->delete($c['id']);
            }
        }

        //meta
        if ($meta = tableon()->columns->meta->get_rows($table_id, ['fields' => 'id'])) {
            foreach ($meta as $c) {
                tableon()->columns->meta->delete($c['id']);
            }
        }


        //table
        $this->db->delete($this->db_table, ['id' => $table_id]);
    }

    /**
     * Clone table by ajax
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function clone() {
        $donor_table_id = intval($_REQUEST['id']);
        $table = $this->get($donor_table_id);

        if ($table) {
            unset($table['id']);

            $this->db->insert($this->db_table, [
                'title' => esc_html__('New Table', 'posts-table-filterable'),
                'table_action' => TABLEON_Default::$action
            ]);

            $new_table_id = intval($this->db->insert_id);
            $table['title'] = sprintf(esc_html__('%s (clone)', 'posts-table-filterable'), $table['title']);
            $this->db->update($this->db_table, $table, array('id' => $new_table_id));

            $columns = tableon()->columns->get_table_columns($donor_table_id);
            $meta = tableon()->columns->meta->get_rows($donor_table_id);

            if (!empty($columns)) {
                foreach ($columns as $c) {
                    unset($c['id']);
                    $c['table_id'] = $new_table_id;
                    $c['created'] = current_time('U', get_option('timezone_string'));
                    tableon()->columns->insert($c);
                }
            }

            if (!empty($meta)) {
                foreach ($meta as $m) {
                    unset($m['id']);
                    $m['table_id'] = $new_table_id;
                    tableon()->columns->meta->insert($m);
                }
            }
        }

        die(json_encode($this->get_admin_table_rows()));
    }

    /**
     * Get thumbnail column data
     *
     * @since 1.0.0
     * @param int $table_id table ID
     * @return string
     */
    private function get_thumbnail($table_id) {
        $attachment_id = $this->get($table_id)['thumbnail'];

        if ($attachment_id) {
            $img_src = wp_get_attachment_image_src($attachment_id, 'thumbnail');

            if (is_array($img_src) AND!empty($img_src[0])) {
                return TABLEON_HELPER::draw_html_item('a', array(
                            'href' => 'javasctipt: void(0);',
                            'onclick' => 'return tableon_change_thumbnail(this);',
                            'data-post-id' => $table_id
                                ), TABLEON_HELPER::draw_html_item('img', array(
                                    'src' => $img_src[0],
                                    'width' => 40,
                                    'alt' => ''
                )));
            }
        } else {
            return TABLEON_HELPER::draw_html_item('a', array(
                        'href' => 'javasctipt: void(0);',
                        'onclick' => 'return tableon_change_thumbnail(this);',
                        'data-post-id' => $table_id,
                        'class' => 'tableon-thumbnail'
                            ), TABLEON_HELPER::draw_html_item('img', array(
                                'src' => TABLEON_ASSETS_LINK . 'img/not-found.jpg',
                                'width' => 40,
                                'alt' => ''
            )));
        }
    }

    /**
     * Table all rows on admin panel
     *
     * @since 1.0.0
     * 
     * @return array
     */
    public function get_admin_table_rows() {

        $rows = [];
        $tables = $this->gets();

        if (!empty($tables)) {
            foreach ($tables as $t) {
                $table_id = intval($t['id']);

                $rows[] = [
                    'pid' => $table_id,
                    'thumbnail' => $this->get_thumbnail($table_id),
                    'title' => $this->get($table_id)['title'],
                    'shortcode' => TABLEON_HELPER::draw_html_item('input', [
                        'type' => 'text',
                        'class' => 'tableon-shortcode-copy-container',
                        'readonly' => 'readony',
                        'value' => "[tableon id={$table_id}]"
                    ]),
                    'status' => TABLEON_HELPER::draw_switcher('status', $this->get($table_id)['status'], $table_id, 'tableon_save_table_field'),
                    'post_type' => TABLEON_HELPER::draw_select([], $this->get_post_types(), $this->get($table_id)['post_type']),
                    'skin' => TABLEON_HELPER::draw_select([], tableon()->skins->get_skins(), $this->get($table_id)['skin']),
                    'actions' => TABLEON_HELPER::draw_html_item('a', array(
                        'href' => "javascript: tableon_main_table.call_popup({$table_id}); void(0);",
                        'class' => 'button tableon-dash-btn-single',
                        'title' => esc_html__('table options', 'posts-table-filterable')
                            ), '<span class="dashicons-before dashicons-admin-generic"></span>')
                    . TABLEON_HELPER::draw_html_item('a', [
                        'href' => "javascript: tableon_main_table.clone({$table_id});void(0);",
                        'title' => esc_html__('clone table', 'posts-table-filterable'),
                        'class' => 'button tableon-dash-btn-single'
                            ], '<span class="dashicons-before dashicons-admin-page"></span>')
                    . TABLEON_HELPER::draw_html_item('a', [
                        'href' => "javascript: tableon_main_table.delete({$table_id});void(0);",
                        'title' => esc_html__('delete table', 'posts-table-filterable'),
                        'class' => 'button tableon-dash-btn-single'
                            ], '<span class="dashicons-before dashicons-no"></span>')
                ];
            }
        }

        return ['rows' => $rows, 'count' => count($rows)];
    }

    /**
     * Get all existing tables
     *
     * @since 1.0.0
     * 
     * @return array
     */
    public function gets() {
        return $this->db->get_results("SELECT * FROM {$this->db_table} ORDER BY id DESC", ARRAY_A);
    }

    /**
     * Import data
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function import($data) {
        TABLEON_HELPER::import_mysql_table($this->db_table, $data);
    }

    /**
     * Is table exists
     *
     * @since 1.0.0
     * @param int $table_id table ID
     * @return boolean
     */
    public function is_exists($table_id) {
        return boolval($this->get($table_id));
    }

    /**
     * Get system post types
     *
     * @since 1.0.0 
     * 
     * @return array
     */
    private function get_post_types() {
        $post_types = get_post_types();
        unset($post_types['revision']);
        unset($post_types['nav_menu_item']);
        unset($post_types['custom_css']);
        unset($post_types['customize_changeset']);
        unset($post_types['oembed_cache']);
        unset($post_types['user_request']);
        unset($post_types['wp_block']);
        unset($post_types['scheduled-action']);
        return $post_types;
    }

}
