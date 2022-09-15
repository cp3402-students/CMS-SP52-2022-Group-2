<?php

/**
 * TABLEON Predefinition
 *
 * Handles predefined data for shortcode [tableon]
 *
 * @since   1.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class TABLEON_Predefinition {

    public $action = 'tableon_predefinition_table';

    public function __construct() {
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
            wp_enqueue_script('tableon-predefinition', TABLEON_ASSETS_LINK . 'js/admin/predefinition.js', ['data-table-23'], TABLEON_VERSION, true);
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
            add_action('wp_ajax_tableon_get_predefinition_table', array($this, 'get_table'));
            add_action('wp_ajax_tableon_save_table_predefinition_field', array($this, 'save'));
        }
    }

    /**
     * Draw table on admin panel by ajax
     *
     * @since 1.0.0
     * 
     * @return output
     */
    public function get_table() {
        $table_html_id = 'tableon-predefinition-table';

        echo TABLEON_HELPER::render_html('views/table.php', array(
            'table_html_id' => $table_html_id,
            'hide_text_search' => true,
            'table_view' => 'separated'
        )) . tableon()->draw_table_data([
            'mode' => 'json',
            'action' => $this->action,
            'per_page_position' => 'none',
            'per_page_sel_position' => 'none',
            'per_page' => -1,
            'use_flow_header' => 0,
            'table_data' => $this->get_table_columns_data(intval($_REQUEST['post_id']))
                ], $table_html_id);
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
        add_action($this->action, function () {
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
     * Get all columns data by $table_id
     *
     * @since 1.0.0
     * @param int $table_id table ID
     * @return array
     */
    private function get_table_columns_data($table_id) {
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
     * Rows of options
     *
     * @since 1.0.0
     * @param int $table_id table ID
     * @return array
     */
    private function get_rows($table_id) {
        $rows = [
            [
                'id' => $table_id,
                'title' => esc_html__('Posts ids', 'posts-table-filterable'),
                'value' => $this->get($table_id, 'ids'),
                'value_custom_field_key' => 'ids',
                'notes' => esc_html__('Using comma, set posts ids you want to show in the table. Example: 23,99,777. Set -1 if you do not want to use it.', 'posts-table-filterable'),
            ],
            [
                'id' => $table_id,
                'title' => esc_html__('Exclude posts ids', 'posts-table-filterable'),
                'value' => $this->get($table_id, 'ids_exclude'),
                'value_custom_field_key' => 'ids_exclude',
                'notes' => esc_html__('Using comma, set posts ids you want to hide in the table. Example: 24,101,888. Set -1 if you do not want to use it.', 'posts-table-filterable'),
            ],            
            [
                'id' => $table_id,
                'title' => esc_html__('Authors', 'posts-table-filterable'),
                'value' => $this->get($table_id, 'authors'),
                'value_custom_field_key' => 'authors',
                'notes' => esc_html__('Posts by authors ids. Example: 1,2,3. Set -1 if you do not want to use it.', 'posts-table-filterable'),
            ],            
            [
                'id' => $table_id,
                'title' => esc_html__('Included taxonomy', 'posts-table-filterable'),
                'value' => $this->get($table_id, 'by_taxonomy'),
                'value_custom_field_key' => 'by_taxonomy',
                'notes' => esc_html__('Display posts which relevant to the rule. Example: category:25,26|post_tag:19|rel:AND. Set -1 if you do not want to use it.', 'posts-table-filterable'),
            ],
            [
                'id' => $table_id,
                'title' => esc_html__('Excluded taxonomy', 'posts-table-filterable'),
                'value' => $this->get($table_id, 'not_by_taxonomy'),
                'value_custom_field_key' => 'not_by_taxonomy',
                'notes' => esc_html__('Exclude posts which relevant to the rule. Example: category:19|post_tag:21|rel:OR. Set -1 if you do not want to use it.', 'posts-table-filterable'),
            ],
        ];


        return $rows;
    }

    /**
     * Get table $table_id predefinitions
     *
     * @since 1.0.0
     * @param int $table_id table ID
     * @param string $key
     * @return array
     */
    public function get($table_id, $key = NULL) {
        $predefinition = [];

        if (tableon()->tables->get($table_id)) {
            $predefinition = tableon()->tables->get($table_id)['predefinition'];
        }

        if (!$predefinition) {
            $predefinition = [];
        } else {
            $predefinition = json_decode($predefinition, true);
        }


        //***

        if ($key) {
            return isset($predefinition[$key]) ? $predefinition[$key] : -1;
        }

        return $predefinition;
    }

    //*******************************************************************************

    /**
     * Save predefinition option by ajax
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function save() {

        $value = TABLEON_HELPER::sanitize_text($_REQUEST['value']);

        $table_id = intval($_REQUEST['post_id']);
        $predefinition = $this->get($table_id);
        $predefinition[TABLEON_HELPER::sanitize_text($_REQUEST['field'])] = $value;

        tableon()->tables->update_field($table_id, 'predefinition', json_encode($predefinition));

        die(json_encode([
            'value' => $value
        ]));
    }

    /**
     * Get posts by predefined taxonomy rules - for posts wp_query assembling
     *
     * @since 1.0.0
     * @param array $args
     * @param string $value
     * @param string $operator IN AND
     * @return array
     */
    public function get_by_taxonomy($args, $value, $operator = 'IN') {
        $taxonomy = [];
        $tmp = explode('|', $value);
        if (!empty($tmp)) {
            foreach ($tmp as $v) {
                $v = explode(':', $v);
                if (count($v) === 2) {
                    $taxonomy[$v[0]] = $v[1];
                }
            }
        }

        //***
        //category:25,26|post_tag:19|rel:AND
        if (!empty($taxonomy)) {

            if (!isset($args['tax_query'])) {
                $args['tax_query'] = [];
            }

            $tmp = ['relation' => 'AND']; //by default
            foreach ($taxonomy as $tax_key => $val) {
                if ($tax_key === 'rel') {

                    if ($operator === 'NOT IN') {
                        $val = strtolower($val);
                        switch ($val) {
                            case 'and':
                                $val = 'OR';
                                break;

                            case 'or':
                                $val = 'AND';
                                break;
                        }
                    }

                    $tmp['relation'] = strtoupper($val);
                    continue;
                }

                if (!taxonomy_exists($tax_key)) {
                    continue;
                }

                //***

                $tmp[] = array(
                    'taxonomy' => $tax_key,
                    'field' => 'term_id',
                    'terms' => explode(',', $val),
                    'operator' => $operator
                );
            }

            $args['tax_query'][] = $tmp;
        }

        return $args;
    }

}
