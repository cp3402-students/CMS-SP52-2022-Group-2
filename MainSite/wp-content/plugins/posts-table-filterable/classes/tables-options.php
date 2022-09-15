<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class TABLEON_TablesOptions {

    public $field_options = null;

    public function __construct() {
        $this->field_options = new TABLEON_ColumnsFieldsOptions();
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        add_action('admin_init', array($this, 'admin_init'), 9999);
    }

    /**
     * Hook admin_enqueue_scripts
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function admin_enqueue_scripts() {
        if (isset($_GET['page']) AND $_GET['page'] == 'tableon') {
            wp_enqueue_script('tableon-tables-options', TABLEON_ASSETS_LINK . 'js/admin/tables-options.js', ['data-table-23'], TABLEON_VERSION, true);
        }
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
            add_action('wp_ajax_tableon_get_tables_options', array($this, 'get_options'));
            add_action('wp_ajax_tableon_save_table_option', array($this, 'save'));
        }
    }

    /**
     * Get table options by ajax
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function get_options() {
        $table_id = 'tableon_options_columns_table';
        echo tableon()->draw_table_data([
            'mode' => 'json',
            'action' => 'tableon_options_columns_table',
            'per_page_position' => 'none',
            'per_page_sel_position' => 'none',
            'per_page' => -1,
            'use_flow_header' => 0,
            'table_data' => $this->get_options_data(intval($_REQUEST['post_id']))
                ], $table_id, FALSE);

        exit;
    }

    /**
     * Table action 
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function add_table_action() {
        add_action('tableon_options_columns_table', function () {
            return [
                0 => [
                //'ajax_action' => ''
                ],
                'title' => [
                    'title' => esc_html__('Title', 'posts-table-filterable')
                ],
                'value' => [
                    'title' => esc_html__('Value', 'posts-table-filterable'),
                    'editable' => 'textinput',
                    'custom_field_key' => true
                ],
                'notes' => [
                    'title' => esc_html__('Info', 'posts-table-filterable')
                ]
            ];
        });
    }

    /**
     * Get option data: per_page_values
     *
     * @since 1.0.0 
     * @param int $table_id table ID
     * @param boolean $as_array how to return
     * @return string | array
     */
    public function get_per_page_sel_pp($table_id, $as_array = false) {
        $per_page_values = $this->get($table_id, 'per_page_values', '10,20,30,40,50,60,70,80,90,100');

        if ($per_page_values) {
            if ($as_array) {
                $per_page_values = explode(',', $per_page_values);
            } else {
                $per_page_values = $per_page_values;
            }
        } else {
            if ($as_array) {
                $per_page_values = range(10, 100, 10);
            } else {
                $per_page_values = implode(',', range(10, 100, 10));
            }
        }

        return $per_page_values;
    }

    /**
     * Get option data: order_disabled
     *
     * @since 1.0.0 
     * @param int $table_id table ID
     * @param boolean $as_array how to return
     * @return string | array
     */
    public function get_order_disabled($table_id) {
        if ($res = $this->get($table_id, 'order_disabled')) {
            $res = explode(',', $res);
        } else {
            $res = [];
        }

        return $res;
    }

    /**
     * Get options table rows
     *
     * @since 1.0.0 
     * @param int $table_id table ID
     * @return array
     */
    private function get_rows($table_id) {
        $profile_action = TABLEON::get_table_action($table_id);

        $disable_post_type_selection = false;
        $selected_post_type = $this->get($table_id, 'post_type', 'post');
        if ($profile_action) {
            $profile = apply_filters($profile_action, $table_id);
            if (isset($profile[0]['post_type_fixed'])) {
                if ($profile[0]['post_type_fixed']) {
                    $disable_post_type_selection = true;
                    $selected_post_type = $profile[0]['post_type'];
                }
            }
        }

        //lock post type selection if its manifested in the selected profile
        $post_type_selector_atts = ['style' => 'width: 100%'];
        if ($disable_post_type_selection) {
            $post_type_selector_atts['disabled'] = true;
        }

        $rows = [
            [
                'id' => $table_id,
                'title' => esc_html__('Per page default', 'posts-table-filterable'),
                'value' => $this->get($table_id, 'per_page_default', 10),
                'value_custom_field_key' => 'per_page_default',
                'notes' => esc_html__('How many posts per page display in the table by default. Example: 10', 'posts-table-filterable'),
            ],
            [
                'id' => $table_id,
                'title' => esc_html__('Per page values', 'posts-table-filterable'),
                'value' => $this->get_per_page_sel_pp($table_id),
                'value_custom_field_key' => 'per_page_values',
                'notes' => esc_html__('Values displayed in the per-page drop-down. Example: 10,20,30,40,50,60,70,80,90,100', 'posts-table-filterable'),
            ],
            [
                'id' => $table_id,
                'title' => esc_html__('Pagination position', 'posts-table-filterable'),
                'value' => TABLEON_HELPER::draw_select([
                    'style' => 'width: 100%'
                        ], [
                    'tb' => esc_html__('Top and Bottom', 'posts-table-filterable'),
                    't' => esc_html__('Top', 'posts-table-filterable'),
                    'b' => esc_html__('Bottom', 'posts-table-filterable'),
                    'none' => esc_html__('None', 'posts-table-filterable')
                        ], $this->get($table_id, 'pagination_position', 'tb')),
                'value_custom_field_key' => 'pagination_position',
                'notes' => esc_html__('Set none to hide pagination', 'posts-table-filterable'),
            ],
            [
                'id' => $table_id,
                'title' => esc_html__('Per page drop-down position', 'posts-table-filterable'),
                'value' => TABLEON_HELPER::draw_select([
                    'style' => 'width: 100%'
                        ], [
                    'tb' => esc_html__('Top and Bottom', 'posts-table-filterable'),
                    't' => esc_html__('Top', 'posts-table-filterable'),
                    'b' => esc_html__('Bottom', 'posts-table-filterable'),
                    'none' => esc_html__('None', 'posts-table-filterable')
                        ], $this->get($table_id, 'per_page_sel_position', 'tb')),
                'value_custom_field_key' => 'per_page_sel_position',
                'notes' => esc_html__('Set none to hide drop-down', 'posts-table-filterable'),
            ],
            [
                'id' => $table_id,
                'title' => esc_html__('Default order by', 'posts-table-filterable'),
                'value' => TABLEON_HELPER::draw_select([
                    'style' => 'width: 100%'
                        ], $this->get_orderby_fields($table_id), $this->get($table_id, 'default_orderby', 'id')),
                'value_custom_field_key' => 'default_orderby',
                'notes' => esc_html__('How to order posts after table initialization', 'posts-table-filterable')
            ],
            [
                'id' => $table_id,
                'title' => esc_html__('Default order', 'posts-table-filterable'),
                'value' => TABLEON_HELPER::draw_select([
                    'style' => 'width: 100%'
                        ], [
                    'asc' => 'asc',
                    'desc' => 'desc'
                        ], $this->get($table_id, 'default_order', 'desc')),
                'value_custom_field_key' => 'default_order',
                'notes' => esc_html__('Posts order direction', 'posts-table-filterable'),
            ],
            [
                'id' => $table_id,
                'title' => esc_html__('Use flying header', 'posts-table-filterable'),
                'value' => TABLEON_HELPER::draw_switcher('use_flow_header', $this->get($table_id, 'use_flow_header', 1), $table_id, 'tableon_save_table_option'),
                'value_custom_field_key' => 'use_flow_header',
                'notes' => esc_html__('Flying header while table scrolling. Disabled in the compact view mode on the mobile devices', 'posts-table-filterable')
            ],
            [
                'id' => $table_id,
                'title' => esc_html__('Show print button', 'posts-table-filterable'),
                'value' => TABLEON_HELPER::draw_switcher('show_print_button', $this->get($table_id, 'show_print_button', 0), $table_id, 'tableon_save_table_option'),
                'value_custom_field_key' => 'show_print_button',
                'notes' => esc_html__('Show print button to print table current page', 'posts-table-filterable')
            ],
            [
                'id' => $table_id,
                'title' => esc_html__('Show search by title', 'posts-table-filterable'),
                'value' => TABLEON_HELPER::draw_switcher('show_text_search', $this->get($table_id, 'show_text_search', true), $table_id, 'tableon_save_table_option'),
                'value_custom_field_key' => 'show_text_search',
                'notes' => sprintf(esc_html__('Show search by post title textinput. To make it works with post content text and post excerpt text use shortcode attributes %s', 'posts-table-filterable'), TABLEON_HELPER::draw_html_item('a', [
                            'href' => 'https://posts-table.com/shortcode/tableon/',
                            'target' => '_blank'
                                ], 'tableon_text_search_by'))
            ],
            [
                'id' => $table_id,
                'title' => esc_html__('Min symbols count', 'posts-table-filterable'),
                'value' => $this->get($table_id, 'text_search_min_symbols', 3),
                'value_custom_field_key' => 'text_search_min_symbols',
                'notes' => esc_html__('Minimum symbols for search by title when search is possible', 'posts-table-filterable')
            ],
            [
                'id' => $table_id,
                'title' => esc_html__('Search by title placeholder', 'posts-table-filterable'),
                'value' => $this->get($table_id, 'text_search_placeholder', esc_html__('search by title', 'posts-table-filterable') . ' ...'),
                'value_custom_field_key' => 'text_search_placeholder',
                'notes' => esc_html__('Text of placeholder for the search by title input', 'posts-table-filterable')
            ],
            [
                'id' => $table_id,
                'title' => esc_html__('Hide filter form', 'posts-table-filterable'),
                'value' => TABLEON_HELPER::draw_switcher('hide_filter_form', $this->get($table_id, 'hide_filter_form', false), $table_id, 'tableon_save_table_option'),
                'value_custom_field_key' => 'hide_filter_form',
                'notes' => esc_html__('Hide filter form, and show it by button click. Useful if the filter form is quite big', 'posts-table-filterable')
            ],
            [
                'id' => $table_id,
                'title' => esc_html__('Disable filter form', 'posts-table-filterable'),
                'value' => TABLEON_HELPER::draw_switcher('disable_filter_form', $this->get($table_id, 'disable_filter_form', false), $table_id, 'tableon_save_table_option'),
                'value_custom_field_key' => 'disable_filter_form',
                'notes' => esc_html__('Switch off filter form', 'posts-table-filterable')
            ],
            [
                'id' => $table_id,
                'title' => esc_html__('Use load more button', 'posts-table-filterable'),
                'value' => TABLEON_HELPER::draw_switcher('use_load_more_button', $this->get($table_id, 'use_load_more_button', false), $table_id, 'tableon_save_table_option'),
                'value_custom_field_key' => 'use_load_more_button',
                'notes' => esc_html__('Use load more button instead of pagination. If it is activated, pagination and per-page drop-down will be hidden', 'posts-table-filterable')
            ],
            [
                'id' => $table_id,
                'title' => esc_html__('Post statuses', 'posts-table-filterable'),
                'value' => TABLEON_HELPER::draw_select([
                    'class' => 'tableon-multiple-select',
                    'multiple' => '',
                    'data-action' => 'tableon_save_table_option',
                    'data-values' => $this->get($table_id, 'post_statuses', 'publish'),
                    'data-use-drag' => 0,
                        ], get_post_stati(), explode(',', $this->get($table_id, 'post_statuses', 'publish'))),
                'value_custom_field_key' => 'post_statuses',
                'notes' => esc_html__('Use some post statuses on the same time or select one. Publish status is by default.', 'posts-table-filterable')
            ],
            [
                'id' => $table_id,
                'title' => esc_html__('Disable columns order-by', 'posts-table-filterable'),
                'value' => TABLEON_HELPER::draw_select([
                    'class' => 'tableon-multiple-select',
                    'multiple' => '',
                    'data-action' => 'tableon_save_table_option',
                    'data-values' => implode(',', $this->get_order_disabled($table_id)),
                    'data-use-drag' => 0,
                        ], $this->get_orderable_profile_columns($table_id), $this->get_order_disabled($table_id)),
                'value_custom_field_key' => 'order_disabled',
                'notes' => esc_html__('Disable columns data ordering on the site front in the columns where it is possible', 'posts-table-filterable')
            ],
            [
                'id' => $table_id,
                'title' => esc_html__('Compact view width', 'posts-table-filterable'),
                'value' => $this->get($table_id, 'compact_view_width', -1),
                'value_custom_field_key' => 'compact_view_width',
                'notes' => esc_html__('For small mobile devices. Here you can set window width less of which the table will be displayed in compact mode. Example: 500. To disable this behaviour set -1', 'posts-table-filterable'),
            ],
            [
                'id' => $table_id,
                'title' => esc_html__('Table custom HTML ID', 'posts-table-filterable'),
                'value' => $this->get($table_id, 'table_html_id', ''),
                'value_custom_field_key' => 'table_html_id',
                'notes' => esc_html__('Attach to table constant html id which you can use for targeted CSS customizations. Remember that ID should be unique! If you not understand it - leave this field empty.', 'posts-table-filterable'),
            ],
            [
                'id' => $table_id,
                'title' => esc_html__('Display cell info', 'posts-table-filterable'),
                'value' => TABLEON_HELPER::draw_switcher('display_cell_info', $this->get($table_id, 'display_cell_info', false), $table_id, 'tableon_save_table_option'),
                'value_custom_field_key' => 'display_cell_info',
                'notes' => esc_html__('Display cell info on its mouse hover', 'posts-table-filterable')
            ]
        ];


        return apply_filters('tableon_extend_options', $rows, $table_id);
    }

    /**
     * Get columns by which posts can be ordered on the site front
     *
     * @since 1.0.0 
     * @param int $table_id table ID
     * @return array
     */
    private function get_orderable_profile_columns($table_id) {
        $res = [];
        $profile = apply_filters(TABLEON::get_table_action($table_id), $table_id);
        if (!empty($profile)) {
            foreach ($profile as $key => $value) {
                if (isset($value['order'])) {
                    if ($value['order']) {
                        $res[$key] = $value['title'];
                    }
                }
            }
        }

        return $res;
    }

    /**
     * Get SELECTED columns by which posts can be ordered on the site front
     *
     * @since 1.0.0 
     * @param int $table_id table ID
     * @return array
     */
    public function get_orderby_fields($table_id) {
        $profile_action = TABLEON::get_table_action($table_id);
        $can_fields = [];

        if (!empty($profile_action)) {
            $profile = apply_filters($profile_action, $table_id);
            if (!empty($profile)) {
                foreach ($profile as $key => $f) {
                    if (isset($f['order']) AND $f['order']) {
                        $can_fields[$key] = $f['title'];
                    }
                }
            }
        }

        return $can_fields;
    }

    /**
     * Get a table options data
     *
     * @since 1.0.0 
     * @param int $table_id table ID
     * @return array
     */
    private function get_options_data($table_id) {
        $columns = [];
        $fields = 'title,value,notes';

        //***

        $found_options = $this->get_rows($table_id);

        //***

        if (!empty($fields) AND!empty($found_options)) {
            $fields = explode(',', $fields);

            foreach ($found_options as $c) {
                $tmp = [];
                $tmp['pid'] = $c['id']; //VERY IMPORTANT AS IT POST ID IN THE TABLES CELLS ACTIONS

                foreach ($fields as $field) {
                    switch ($field) {
                        case 'title':
                            $tmp[$field] = $c['title'];
                            break;

                        case 'value':

                            if (isset($c['value_custom_field_key']) AND!empty($c['value_custom_field_key'])) {
                                $tmp[$field] = [
                                    'value' => $c['value'],
                                    'custom_field_key' => $c['value_custom_field_key']
                                ];
                            } else {
                                $tmp[$field] = $c['value'];
                            }

                            break;

                        case 'notes':
                            $tmp[$field] = $c['notes'];
                            break;

                        default:
                            $tmp[$field] = TABLEON_Vocabulary::get(esc_html__('Wrong type', 'posts-table-filterable'));
                            break;
                    }
                }

                $columns[] = $tmp;
            }
        }


        return ['rows' => $columns, 'count' => count($found_options)];
    }

    /**
     * Get an option data
     *
     * @since 1.0.0 
     * @param int $table_id table ID
     * @param string $key
     * @param mixed $default
     * @return array
     */
    public function get($table_id, $key, $default = NULL) {
        static $options = [];
        $res = $default;

        //+++

        if (!isset($options[$table_id])) {
            $options[$table_id] = [];
        }

        if (empty($options[$table_id])) {
            if (tableon()->tables->get($table_id)) {
                if ($options[$table_id] = tableon()->tables->get($table_id)['options']) {
                    $options[$table_id] = json_decode($options[$table_id], true);
                } else {
                    $options[$table_id] = [];
                }
            }
        }

        //+++

        if (isset($options[$table_id][$key])) {
            $res = $options[$table_id][$key];
        }

        if (is_numeric($res)) {
            $res = intval($res);
        }

        return $res;
    }

    //*******************************************************************************

    /**
     * Save option data by ajax
     *
     * @since 1.0.0 
     * 
     * @return void
     */
    public function save() {

        $table_id = intval($_REQUEST['post_id']);

        if (is_int($_REQUEST['value'])) {
            $value = intval($value);
        } else {
            $value = TABLEON_HELPER::sanitize_text($_REQUEST['value']);
        }

        //***

        if ($options = tableon()->tables->get($table_id)['options']) {
            $options = json_decode($options, true);
        } else {
            $options = [];
        }

        $options[TABLEON_HELPER::sanitize_text($_REQUEST['field'])] = $value;
        tableon()->tables->update_field($table_id, 'options', json_encode($options));

        die(json_encode([
            'value' => $value
        ]));
    }

}
