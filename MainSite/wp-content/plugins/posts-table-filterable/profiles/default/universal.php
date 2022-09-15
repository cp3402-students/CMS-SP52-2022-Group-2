<?php

/**
 * For shortcodes generation
 *
 * Parent class for posts shortcodes (examples): [tableon_upsells], [tableon_cross_sells]
 *
 * @since   1.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

abstract class TABLEON_Universal {

    public $slug = '';
    public $default_columns = 'id,thumbnail,post_title';
    public $setting_key = '';
    public $filter_provider = '';
    public $settings_columns_title = '';

    public function __construct() {
        $this->setting_key = "{$this->slug}_columns";
        $this->filter_provider = "tableon_{$this->slug}";
        add_action("tableon_filter_provider_{$this->filter_provider}", [$this, 'filter_provider'], 10, 3);

        add_filter("tableon_default_get_{$this->slug}_table", function($what, $table_html_id) {
            $skin = '';
            if (isset($what['skin'])) {
                $skin = $what['skin'];
            }

            return $this->get_table(intval($what['post_id']), $table_html_id, ['mode' => 'json', 'skin' => $skin, 'classes' => 'tableon-data-table-self-call']);
        }, 10, 2);

        add_shortcode("tableon_{$this->slug}", function($args) {
            tableon()->include_assets();

            $args = (array) $args;

            if (isset($args['columns']) AND empty($args['columns'])) {
                unset($args['columns']);
            }

            $args['classes'] = "tableon_{$this->slug}_columns";

            //+++

            $post_id = 0;

            if (isset($args['id'])) {
                $post_id = intval($args['id']);
                unset($args['id']);
            } else {
                global $post;
                if (is_object($post)) {
                    $post_id = $post->ID;
                }
            }

            //+++

            if ($post_id > 0) {
                return $this->get_table($post_id, uniqid("{$this->slug}-"), (array) $args);
            } else {
                return '<div class="tableon-notice">' . esc_html__('TABLEON: Set post id please in the shortcode attribute!', 'posts-table-filterable') . '</div>';
            }
        });

        //***

        add_action('tableon_extend_settings', function($rows) {

            $selected = explode(',', TABLEON_Settings::get($this->setting_key));
            $options = [];

            foreach (apply_filters(TABLEON_Default::$action, 0, []) as $key => $value) {
                if (isset($value['title'])) {
                    $options[$key] = $value['title'];
                }
            }

            //+++

            $rows[] = [
                'id' => 0,
                'title' => $this->settings_columns_title,
                'value' => [
                    'value' => TABLEON_HELPER::draw_select([
                        'class' => 'tableon-multiple-select',
                        'multiple' => '',
                        'data-action' => 'tableon_save_settings_field',
                        'data-values' => TABLEON_Settings::get($this->setting_key)
                            ], $options, $selected),
                    'custom_field_key' => $this->setting_key
                ],
                'notes' => sprintf(esc_html__('Columns for %s and shortcode [%s]. Press and wait to reorder.', 'posts-table-filterable'), $this->settings_columns_title, $this->filter_provider)
            ];

            return $rows;
        }, 10, 1);

        add_action('tableon_extend_settings_default', function($defaults) {
            $defaults[$this->setting_key] = $this->default_columns;
            return $defaults;
        }, 10, 1);
    }

    public function filter_provider($args, $filter_data, $shortcode_args) {
        if (is_array($filter_data) AND isset($filter_data['post_id']) AND intval($filter_data['post_id']) > 0) {
            if (!$args['post__in'] = (TABLEON_Default::get_post(intval($filter_data['post_id'])))->get_upsell_ids()) {
                $args['post__in'] = [-1];
            }
        }

        return $args;
    }

    /**
     * Get table HTML
     *
     * @since 1.0.0
     * @param int $post_id post ID
     * @param string $table_html_id table HTML id
     * @param array $args shortcode arguments
     * @return string table HTML
     */
    public function get_table($post_id, $table_html_id, $args = []) {
        $args['table_html_id'] = $table_html_id;

        if (!isset($args['mode']) OR!in_array($args['mode'], ['json', 'to_json', 'ajax'])) {
            $args['mode'] = 'json';
        }

        if (!isset($args['columns'])) {

            if (!$columns = TABLEON_Settings::get($this->setting_key)) {
                $columns = $this->default_columns;
            }

            $args['columns'] = $args['fields'] = $columns; //fields is for db
        } else {
            $args['fields'] = $args['columns'];
        }

        //synonims replacement
        if (!empty($args['columns'])) {
            $tmp = explode(',', $args['columns']);
            foreach ($tmp as $field_key) {
                if (isset(TABLEON::$synonyms[$field_key])) {
                    $replacement = TABLEON::$synonyms[$field_key];
                    $tmp = array_map(function ($v) use ($field_key, $replacement) {
                        return $v == $field_key ? $replacement : $v;
                    }, $tmp);
                }
            }

            $args['fields'] = $args['columns'] = implode(',', $tmp);
        }



        if (isset($args['id'])) {
            unset($args['id']); //because id here can be recognized in tableon()->do_shortcode as table_id
        }

        if (isset($args['table_id']) AND intval($args['table_id']) > 0) {
            $args['id'] = intval($args['table_id']);
        }

        if (!isset($args['per_page_sel_position'])) {
            $args['per_page_sel_position'] = 'none';
        }

        if (!isset($args['per_page_position'])) {
            $args['per_page_position'] = 't';
        }

        if (!isset($args['hide_text_search'])) {
            $args['hide_text_search'] = true;
        }

        if (!isset($args['orderby'])) {
            $args['orderby'] = 'post_title';
        }

        if (!isset($args['order'])) {
            $args['order'] = 'asc';
        }

        $args['filter_provider'] = $this->filter_provider;
        $args['filter_data'] = ['post_id' => $post_id];

        //***

        switch ($args['mode']) {
            case 'ajax':
                //special mode which after loading transfrom to json state mode - good for one page table only!!
                //$args['mode'] = 'to_json';
                break;

            case 'json':
            case 'to_json':
                //in json mode we need to get all posts
                $args['table_data'] = tableon()->get_table_data(true, array_merge($args, ['per_page' => -1]));
                break;
        }

        if (!isset($args['per_page'])) {
            $args['per_page'] = 10;
        }

        $args['use_flow_header'] = 0;

        return tableon()->do_shortcode($args);
    }

}
