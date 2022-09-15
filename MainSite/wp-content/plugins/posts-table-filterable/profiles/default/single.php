<?php

/**
 * Shortcode [tableon_single]
 *
 * Generates a single post HTML table
 *
 * @see https://posts-table.com/shortcode/tableon_single/
 * @since   1.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

final class TABLEON_Single {

    public $setting_key = 'single_post_columns';
    private $action = 'tableon_single_post';
    public $default_columns = 'title,value';

    public function __construct() {

        add_action('tableon_extend_settings', function($rows) {

            $selected = explode(',', TABLEON_Settings::get($this->setting_key));
            $options = [];

            foreach (apply_filters(TABLEON_Default::$action, 0, []) as $key => $value) {
                if (isset($value['title'])) {
                    if ($key !== 'single') {
                        $options[$key] = $value['title'];
                    }
                }
            }

            //+++

            $rows[] = [
                'id' => 0,
                'title' => esc_html__('Single', 'posts-table-filterable'),
                'value' => [
                    'value' => TABLEON_HELPER::draw_select([
                        'class' => 'tableon-multiple-select',
                        'multiple' => '',
                        'data-action' => 'tableon_save_settings_field',
                        'data-values' => TABLEON_Settings::get($this->setting_key)
                            ], $options, $selected),
                    'custom_field_key' => $this->setting_key
                ],
                'notes' => esc_html__('Columns for table Single and shortcode [tableon_single]. Press and wait to reorder.', 'posts-table-filterable')
            ];

            return $rows;
        }, 10, 1);

        add_action('tableon_extend_settings_default', function($defaults) {
            $defaults[$this->setting_key] = $this->default_columns;
            return $defaults;
        }, 10, 1);

        //***
        //profile
        add_action($this->action, function () {
            return apply_filters('tableon_single_post_extend', [
                0 => [
                //'ajax_action' => ''
                ],
                'title' => [
                    'title' => TABLEON_Vocabulary::get(esc_html__('Title', 'posts-table-filterable'))
                ],
                'value' => [
                    'title' => TABLEON_Vocabulary::get(esc_html__('Value', 'posts-table-filterable'))
                ]
            ]);
        });

        //***

        add_filter('tableon_get_table_single_post', function($what, $table_html_id) {
            $args = [];
            if (!empty($what['columns'])) {
                $args['columns'] = $what['columns'];
            }
            if (isset($what['skin'])) {
                $args['skin'] = $what['skin'];
            }

            $args['post_type'] = get_post_type(intval($what['post_id']));

            $args['table_view'] = 'separated';

            return $this->draw_table(intval($what['post_id']), $table_html_id, $args);
        }, 10, 2);

        //***

        add_shortcode('tableon_single', function($args) {
            tableon()->include_assets();

            $args = (array) $args;

            $post_id = 0;

            if (!isset($args['columns'])) {
                $args['columns'] = TABLEON_Settings::get($this->setting_key, $args);
            }

            if (isset($args['id'])) {
                $post_id = intval($args['id']);
                unset($args['id']);
            } else {
                global $post;
                if (is_object($post)) {
                    $post_id = $post->ID;
                }
            }

            if (isset($args['table_id'])) {
                $args['id'] = intval($args['table_id']); //by code id is table id in [tableon] shortcode
            }

            //***

            if ($post_id > 0) {
                $args['post_type'] = get_post_type($post_id);
                return $this->draw_table($post_id, uniqid('t'), $args);
            } else {
                return '<div class="tableon-notice">' . sprintf(esc_html__('Post #%s does not exists!', 'posts-table-filterable'), $post_id) . '</div>';
            }
        });
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
    private function draw_table($post_id, $table_html_id, $args = []) {
        return TABLEON_HELPER::render_html('views/table.php', array(
                    'table_html_id' => $table_html_id,
                    'hide_text_search' => true,
                    'post_type' => isset($args['post_type']) ? strval($args['post_type']) : 'post',
                    'classes' => 'tableon-data-table-self-call',
                    'style' => isset($args['skin']) ? tableon()->skins->get_theme_css($args['skin'], $table_html_id) : '',
                    'skin' => isset($args['skin']) ? $args['skin'] : '',
                    'table_view' => isset($args['table_view']) ? $args['table_view'] : ''
                )) . tableon()->draw_table_data([
                    'mode' => 'json',
                    'action' => $this->action,
                    'columns' => $this->default_columns,
                    'css_classes' => $this->action,
                    'per_page' => -1,
                    'per_page_position' => 'none',
                    'per_page_sel_position' => 'none',
                    'use_flow_header' => 0,
                    'table_data' => $this->get_table_data($post_id, $table_html_id, $args)
                        ], $table_html_id);
    }

    /**
     * Get table data
     *
     * @since 1.0.0
     * @param int $post_id post ID
     * @param string $table_html_id table HTML id
     * @param array $args shortcode arguments
     * @return array table data
     */
    private function get_table_data($post_id, $table_html_id = 0, $args = []) {
        $table_data = [];

        if ($post_id > 0) {

            $woo_profile = apply_filters(TABLEON_Default::$action, NULL);

            if (isset($args['columns'])) {
                $columns = explode(',', $args['columns']);
            } else {
                $columns = explode(',', TABLEON_Settings::get($this->setting_key));
            }


            if (!empty($columns)) {
                foreach ($columns as $field_key) {

                    $add = TRUE;
                    if (!isset($woo_profile[$field_key])) {
                        $add = FALSE;
                        if (isset(TABLEON::$synonyms[$field_key])) {
                            $field_key = TABLEON::$synonyms[$field_key];
                            $add = TRUE;
                        }
                    }

                    //***

                    if ($add) {
                        $value = $woo_profile[$field_key]['action']($post_id);

                        //***

                        $upsell_as = 'button';
                        $upsells_columns = $cross_columns = $variations_columns = '';


                        if (isset($args['upsells_as'])) {
                            $upsell_as = $args['upsells_as'];
                            if (isset($args['upsells_per_page'])) {
                                $upsells_per_page = $args['upsells_per_page'];
                            }
                        }

                        if (isset($args['upsells_columns'])) {
                            $upsells_columns = $args['upsells_columns'];
                        }


                        //***

                        if ($field_key === 'upsell' AND $upsell_as === 'table') {
                            $value = do_shortcode("[tableon_upsells id={$post_id} per_page={$upsells_per_page} columns='{$upsells_columns}']");
                        }

                        if ($field_key === 'favourites') {
                            $table_data[] = [
                                'title' => "<b data-key='favourites'>{$woo_profile[$field_key]['title']}</b>",
                                'value' => $value
                            ];
                        } elseif ($field_key === 'compare') {
                            $table_data[] = [
                                'title' => "<b data-key='compare'>{$woo_profile[$field_key]['title']}</b>",
                                'value' => $value
                            ];
                        } else {
                            $table_data[] = [
                                'title' => "<b>{$woo_profile[$field_key]['title']}</b>",
                                'value' => $value
                            ];
                        }
                    }
                }
            }

            //***

            return $table_data;
        }

        return $table_data;
    }

}

new TABLEON_Single();
