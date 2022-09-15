<?php

/**
 * TABLEON Filter fields options
 *
 * Handles a table filter elements options
 *
 * @since   1.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class TABLEON_FilterFieldsOptions {

    public $table_html_id = '';
    public $action = 'tableon_filter_fields_options_table';

    public function __construct() {
        add_action('admin_init', array($this, 'admin_init'), 9999);

        add_filter('tableon_get_filter_field_options', function($args, $call_id) {

            $this->table_html_id = $call_id;

            if (isset($args['table_id']) AND intval($args['table_id']) > 0) {
                echo TABLEON_HELPER::render_html('views/table.php', array(
                    'table_html_id' => $this->table_html_id,
                    'hide_text_search' => true,
                    'table_view' => 'separated'
                )) . tableon()->draw_table_data([
                    'mode' => 'json',
                    'action' => $this->action,
                    'per_page_position' => 'none',
                    'per_page_sel_position' => 'none',
                    'per_page' => -1,
                    'table_data' => $this->get_field_data(intval($args['table_id']), $args['field_key'])
                        ], $this->table_html_id);
            }

            return '';
        }, 10, 2);
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
            add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
            add_action('wp_ajax_tableon_save_filter_field_option', array($this, 'save'));
        }
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
            wp_enqueue_script('tableon-filter-fields-options', TABLEON_ASSETS_LINK . 'js/admin/filter-fields-options.js', ['tableon-generated-tables'], TABLEON_VERSION, true);
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
        add_action($this->action, function () {
            return [
                0 => [
                //'ajax_action' => ''
                ],
                'title' => [
                    'title' => esc_html__('Title', 'posts-table-filterable')
                ],
                'value' => [
                    'title' => esc_html__('Value', 'posts-table-filterable')
                ],
                'notes' => [
                    'title' => esc_html__('Info', 'posts-table-filterable')
                ]
            ];
        });
    }

    /**
     * Get field data
     *
     * @since 1.0.0
     * @param int $table_id table ID
     * @param string $field_key
     * @return array
     */
    public function get_field_data($table_id, $field_key) {
        $rows = [];

        $profile = apply_filters(TABLEON::get_table_action($table_id), $table_id);

        if (isset($profile[$field_key]['filter_options'])) {
            $filter_options = $profile[$field_key]['filter_options'];
            if (!empty($filter_options)) {
                foreach ($filter_options as $filter_option) {
                    $key = $field_key . '-' . $filter_option;
                    $val = $this->get($table_id, $key);

                    switch ($filter_option) {
                        case 'title':

                            $rows[] = [
                                'pid' => 0,
                                'title' => esc_html__('Title', 'posts-table-filterable'),
                                'value' => TABLEON_HELPER::draw_html_item('input', [
                                    'class' => 'tableon-filter-field-option',
                                    'type' => 'text',
                                    'value' => strval($val),
                                    'data-table-id' => $table_id,
                                    'data-key' => $key
                                ]),
                                'notes' => esc_html__('Filter element title', 'posts-table-filterable'),
                            ];

                            break;

                        case 'placeholder':

                            $rows[] = [
                                'pid' => 0,
                                'title' => esc_html__('Placeholder', 'posts-table-filterable'),
                                'value' => TABLEON_HELPER::draw_html_item('input', [
                                    'class' => 'tableon-filter-field-option',
                                    'type' => 'text',
                                    'value' => strval($val),
                                    'data-table-id' => $table_id,
                                    'data-key' => $key
                                ]),
                                'notes' => esc_html__('Input placeholder', 'posts-table-filterable'),
                            ];

                            break;

                        case 'minlength':

                            $rows[] = [
                                'pid' => 0,
                                'title' => esc_html__('Minlength', 'posts-table-filterable'),
                                'value' => TABLEON_HELPER::draw_html_item('input', [
                                    'class' => 'tableon-filter-field-option',
                                    'type' => 'number',
                                    'value' => intval($val),
                                    'data-table-id' => $table_id,
                                    'data-key' => $key
                                ]),
                                'notes' => esc_html__('Input minlength', 'posts-table-filterable'),
                            ];

                            break;

                        case 'min':

                            $rows[] = [
                                'pid' => 0,
                                'title' => esc_html__('Min', 'posts-table-filterable'),
                                'value' => TABLEON_HELPER::draw_html_item('input', [
                                    'class' => 'tableon-filter-field-option',
                                    'type' => 'number',
                                    'value' => $val === '' ? -1 : $val,
                                    'data-table-id' => $table_id,
                                    'data-key' => $key
                                ]),
                                'notes' => esc_html__('Set custom min value. By default value is -1 and auto definition through database will be applied.', 'posts-table-filterable'),
                            ];

                            break;

                        case 'max':

                            $rows[] = [
                                'pid' => 0,
                                'title' => esc_html__('Max', 'posts-table-filterable'),
                                'value' => TABLEON_HELPER::draw_html_item('input', [
                                    'class' => 'tableon-filter-field-option',
                                    'type' => 'number',
                                    'value' => $val === '' ? -1 : $val,
                                    'data-table-id' => $table_id,
                                    'data-key' => $key
                                ]),
                                'notes' => esc_html__('Set custom min value. By default value is -1 and auto definition through database will be applied.', 'posts-table-filterable'),
                            ];

                            break;

                        case 'width':

                            $rows[] = [
                                'pid' => 0,
                                'title' => esc_html__('Width', 'posts-table-filterable'),
                                'value' => TABLEON_HELPER::draw_html_item('input', [
                                    'class' => 'tableon-filter-field-option',
                                    'type' => 'text',
                                    'value' => $val === '' ? '100%' : $val,
                                    'data-table-id' => $table_id,
                                    'data-key' => $key
                                ]),
                                'notes' => esc_html__('Container width. By default is 100%. Example: 50%, 150px', 'posts-table-filterable'),
                            ];

                            break;

                        case 'data-type':

                            $rows[] = [
                                'pid' => 0,
                                'title' => esc_html__('Data type', 'posts-table-filterable'),
                                'value' => TABLEON_HELPER::draw_select([
                                    'class' => 'tableon-filter-field-option',
                                    'type' => 'text',
                                    'value' => $val,
                                    'data-table-id' => $table_id,
                                    'data-key' => $key
                                        ], [
                                    'datetime' => 'datetime',
                                    'unixtimestamp' => 'unixtimestamp'
                                        ], $val),
                                'notes' => sprintf(esc_html__('In ACF is used %s data type', 'posts-table-filterable'), 'datetime'),
                            ];

                            break;

                        case 'as-mselect':

                            $ajax_data = [
                                'table_id' => $table_id,
                                'key' => $key
                            ];
                            $rows[] = [
                                'pid' => 0,
                                'title' => esc_html__('MultiSelect', 'posts-table-filterable'),
                                'value' => TABLEON_HELPER::draw_switcher($key, $val, $table_id, 'tableon_save_filter_field_option', $ajax_data),
                                'notes' => esc_html__('Display as multi-select', 'posts-table-filterable'),
                            ];

                            break;

                        case 'mselect-logic':

                            $rows[] = [
                                'pid' => 0,
                                'title' => esc_html__('Logic', 'posts-table-filterable'),
                                'value' => TABLEON_HELPER::draw_select([
                                    'style' => 'width: 100%',
                                    'class' => 'tableon-filter-field-option',
                                    'data-table-id' => $table_id,
                                    'data-key' => $key
                                        ], [
                                    'IN' => 'IN',
                                    'AND' => 'AND',
                                    'NOT IN' => 'NOT IN'
                                        ], $val),
                                'notes' => esc_html__('Logic of the selected terms. Mainly for multi-select.', 'posts-table-filterable'),
                            ];

                            break;

                        case 'exclude':

                            $rows[] = [
                                'pid' => 0,
                                'title' => esc_html__('Exclude', 'posts-table-filterable'),
                                'value' => TABLEON_HELPER::draw_html_item('input', [
                                    'class' => 'tableon-filter-field-option',
                                    'style' => 'width: 250px;',
                                    'type' => 'text',
                                    'value' => empty($val) ? '' : $val,
                                    'data-table-id' => $table_id,
                                    'data-key' => $key
                                ]),
                                'notes' => esc_html__('Using comma, write terms ids you want to exclude from the filter. Leave empty to disable.', 'posts-table-filterable'),
                            ];

                            break;

                        case 'include':

                            $rows[] = [
                                'pid' => 0,
                                'title' => esc_html__('Include', 'posts-table-filterable'),
                                'value' => TABLEON_HELPER::draw_html_item('input', [
                                    'class' => 'tableon-filter-field-option',
                                    'style' => 'width: 250px;',
                                    'type' => 'text',
                                    'value' => empty($val) ? '' : $val,
                                    'data-table-id' => $table_id,
                                    'data-key' => $key
                                ]),
                                'notes' => esc_html__('Using comma, write terms ids you want to see in the filter, another ones will not be displayed. Leave empty to disable.', 'posts-table-filterable'),
                            ];

                            break;
                    }
                }
            }
        }

        $data = apply_filters('tableon_one_column_filter_field_options', [
            'table_id' => $table_id,
            'field_key' => $field_key,
            'rows' => $rows
        ]);

        return ['rows' => $data['rows'], 'count' => count($data['rows'])];
    }

    /**
     * Save field option data by ajax
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function save() {

        $table_id = intval($_REQUEST['table_id']);
        $field_key = TABLEON_HELPER::sanitize_text($_REQUEST['key']);
        $value = TABLEON_HELPER::sanitize_text($_REQUEST['value']);

        $this->update($table_id, $field_key, $value);

        die(json_encode(['value' => $value]));
    }

    /**
     * Update field option
     *
     * @since 1.0.0
     * @param int $table_id table ID
     * @param string $field_key
     * @param string $value
     * @return void
     */
    private function update($table_id, $field_key, $value) {
        $options = tableon()->tables->get($table_id)['filter_options'];

        if ($options) {
            $options = json_decode($options, true);
        } else {
            $options = [];
        }

        $options[$field_key] = $value;
        tableon()->tables->update_field($table_id, 'filter_options', json_encode($options));
    }

    /**
     * Get field option data
     *
     * @since 1.0.0
     * @param int $table_id table ID
     * @param string $key
     * @return string
     */
    public function get($table_id, $key) {
        $res = NULL;
        if ($options = tableon()->tables->get($table_id)) {
            $options = tableon()->tables->get($table_id)['filter_options'];

            if ($options) {
                $options = json_decode($options, true);
            } else {
                $options = [];
            }

            if (isset($options[$key])) {
                $res = $options[$key];
            }
        }

        return $res;
    }

}
