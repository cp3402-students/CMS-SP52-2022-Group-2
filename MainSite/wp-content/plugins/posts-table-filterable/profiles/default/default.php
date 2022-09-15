<?php

/**
 * Default profile
 *
 * Generates and handles fields of the posts tables
 *
 * @since   1.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

include_once TABLEON_PATH . 'profiles/default/compatibility.php';
include_once TABLEON_PATH . 'profiles/default/universal.php';
//include_once TABLEON_PATH . 'profiles/default/upsells.php';//just as code-doc example
include_once TABLEON_PATH . 'profiles/default/single.php';

final class TABLEON_Default {

    public static $action = 'tableon_default_tables';
    public static $allowed_tags = '<p><br><br/><br/><hr><strong><b><em><span><a>';
    public static $synonyms = [];
    public $filter = null;
    public static $orderby_select_fields = [];
    public static $fields_options = ['css-font-size', 'css-font-family', 'css-color', 'css-background', 'css-media-hide'];

    public function __construct() {
        tableon_register_profile(self::$action, 'Default');
        add_action(self::$action, [$this, 'action'], 10, 2);

        add_action('admin_init', array($this, 'admin_init'), 9999);
        add_action('init', array($this, 'init'), 9999);

        add_action('tableon_filter_provider_default', function ($args, $filter_data) {
            return tableon()->filter->provider($filter_data, $args);
        }, 10, 2);

        //***
        //table here like drop-down list
        add_shortcode('tableon_drop_down', function ($args) {
            if (isset($args['id'])) {
                tableon()->include_assets();

                $data = [];
                $data['table_id'] = intval($args['id']);
                //$data['mode'] = isset($args['mode']) ? $args['mode'] : 'ajax';do not do it here
                $data['width'] = isset($args['width']) ? $args['width'] : '100%';
                $data['height'] = isset($args['height']) ? $args['height'] : '300';
                $data['placeholder'] = isset($args['placeholder']) ? $args['placeholder'] : tableon()->columns->options->get(intval($args['id']), 'text_search_placeholder', esc_html__('search by title', 'posts-table-filterable') . ' ...');
                $data['skin'] = isset($args['skin']) ? $args['skin'] : '';
                return TABLEON_HELPER::render_html('profiles/default/views/tableon_drop_down.php', $data);
            }
        });

        add_filter('tableon_extend_options', [$this, 'tableon_extend_options'], 10, 2);
        add_filter('tableon_table_classes', function ($args) {

            if (!isset($args['classes'])) {
                $args['classes'] = '';
            }

            if (isset($args['id'])) {
                if (TABLEON::get_table_action($args['id']) === self::$action) {
                    //$args['classes'] .= tableon()->columns->options->get($args['id'], 'is_cart_shown', 0) ? 'tableon-woocommerces-show-cart' : '';
                }
            }

            return $args;
        }, 10, 1);

        //***
        //lets add columns to the plugged-in extensions
        global $tableon_extend_ext_profiles;
        if (!empty($tableon_extend_ext_profiles[self::$action])) {
            foreach ($tableon_extend_ext_profiles[self::$action] as $hook) {
                add_filter($hook, function ($profile, $selected_columns_keys) {
                    return $this->extend_ext_profiles($profile, $selected_columns_keys);
                }, 10, 2);
            }
        }

        //***
        //example about manipulatuing of wp query on the fly
        add_filter('tableon_wp_query_args', function ($args, $table_id) {
            /*
              if ($table_id > 0 AND tableon()->columns->options->get($table_id, 'hide_in_cart_added', 0)) {
              $args['post__not_in'] = $this->get_ids_in_cart();//as an example of manipulations
              }
             */
            return $args;
        }, 10, 2);

        //***
        //process ordering by sort-by select
        add_filter('tableon_wp_query_args', function ($args, $table_id) {

            if (substr_count($args['orderby'], 'orderby_select_') > 0) {
                $args['orderby'] = str_replace('orderby_select_', '', $args['orderby']);

                switch ($args['orderby']) {
                    case 'id':
                        $args['order'] = 'asc';
                        break;
                    case 'id-desc':
                        $args['orderby'] = 'id';
                        $args['order'] = 'desc';
                        break;

                    case 'title':
                        $args['order'] = 'asc';
                        break;
                    case 'title-desc':
                        $args['orderby'] = 'title';
                        $args['order'] = 'desc';
                        break;

                    case 'sku'://as an example
                    case 'sku-desc':
                        $args['order'] = 'asc';

                        if ($args['orderby'] === 'sku-desc') {
                            $args['order'] = 'desc';
                        }

                        $args['orderby'] = 'meta_value';
                        $args['meta_key'] = '_sku';

                        break;

                    case 'comments':
                    case 'comments-desc':
                        $args['order'] = 'asc';

                        if ($args['orderby'] === 'comments-desc') {
                            $args['order'] = 'desc';
                        }

                        $args['orderby'] = 'comment_count';

                        break;

                    case 'modified':
                    case 'modified-desc':
                        $args['order'] = 'asc';

                        if ($args['orderby'] === 'modified-desc') {
                            $args['order'] = 'desc';
                        }

                        $args['orderby'] = 'post_modified';

                        break;

                    default:

                        if ($table_id > 0) {
                            $metas = tableon()->columns->meta->get_rows($table_id);
                            if (!empty($metas)) {
                                foreach ($metas as $m) {
                                    if ($args['orderby'] === $m['meta_key'] || $args['orderby'] === $m['meta_key'] . '-desc') {

                                        $args['order'] = 'asc';

                                        if ($args['orderby'] === $m['meta_key'] . '-desc') {
                                            $args['order'] = 'desc';
                                        }

                                        $args['meta_key'] = $m['meta_key'];
                                        if ($m['meta_type'] === 'number') {
                                            $args['orderby'] = 'meta_value_num';
                                        }

                                        break;
                                    }
                                }
                            }
                        }

                        break;
                }
            }

            return $args;
        }, 10, 2);
    }

    /**
     * Hook init
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function init() {
        //!!important do it here
        $this->init_orderby_select_fields();
    }

    /**
     * Hook admin_init
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function admin_init() {
        //!!important do it here
        $this->init_orderby_select_fields();
    }

    /**
     * Initialization of the fields of "order-by" drop-down
     *
     * @since 1.0.0
     *
     * @return void
     */
    private function init_orderby_select_fields() {
        self::$orderby_select_fields = apply_filters('tableon_default_catalog_orderby', [
            //'popularity' => TABLEON_Vocabulary::get(esc_html__('Sort by popularity', 'posts-table-filterable')),
            'date' => TABLEON_Vocabulary::get(esc_html__('Sort by latest', 'posts-table-filterable')),
            'title' => TABLEON_Vocabulary::get(esc_html__('Sort by name A-Z', 'posts-table-filterable')),
            'title-desc' => TABLEON_Vocabulary::get(esc_html__('Sort by name Z-A', 'posts-table-filterable')),
            'comments' => TABLEON_Vocabulary::get(esc_html__('Number of Comments: Ascending', 'posts-table-filterable')),
            'comments-desc' => TABLEON_Vocabulary::get(esc_html__('Number of Comments: Descending', 'posts-table-filterable')),
            'modified' => TABLEON_Vocabulary::get(esc_html__('Last Modified Date: Oldest to Newest', 'posts-table-filterable')),
            'modified-desc' => TABLEON_Vocabulary::get(esc_html__('Last Modified Date: Newest to Oldest', 'posts-table-filterable')),
            'menu_order' => TABLEON_Vocabulary::get(esc_html__('Sort by menu order', 'posts-table-filterable')),
            'rand' => TABLEON_Vocabulary::get(esc_html__('Sort by random', 'posts-table-filterable')),
            'id' => TABLEON_Vocabulary::get(esc_html__('Sort by post ID: Ascending', 'posts-table-filterable')),
            'id-desc' => TABLEON_Vocabulary::get(esc_html__('Sort by post ID: Descending', 'posts-table-filterable'))
        ]);
    }

    /**
     * Hook tableon_extend_options, adds more plugins settings
     *
     * @since 1.0.0
     * @param array $rows settings array
     * @param int $table_id table ID
     * @return array plugin settings
     */
    public function tableon_extend_options($rows, $table_id) {
        if ($table_id > 0) {
            if (TABLEON::get_table_action($table_id) === self::$action) {

                $rows[] = [
                    'id' => $table_id,
                    'title' => esc_html__('Show Sorting Dropdown', 'posts-table-filterable'),
                    'value' => TABLEON_HELPER::draw_switcher('is_sort_droptdown_shown', tableon()->columns->options->get($table_id, 'is_sort_droptdown_shown', 0), $table_id, 'tableon_save_table_option'),
                    'value_custom_field_key' => 'is_sort_droptdown_shown',
                    'notes' => esc_html__('Displays Sorting Dropdown list', 'posts-table-filterable')
                ];

                $rows[] = [
                    'id' => $table_id,
                    'title' => esc_html__('Sorting Dropdown Fields', 'posts-table-filterable'),
                    'value' => TABLEON_HELPER::draw_select([
                        'class' => 'tableon-multiple-select',
                        'multiple' => '',
                        'data-action' => 'tableon_save_table_option',
                        'data-values' => tableon()->columns->options->get($table_id, 'orderby_select_fields', ''),
                        'data-use-drag' => 1,
                            ], apply_filters('tableon_table_orderby_select_args', self::$orderby_select_fields, $table_id), explode(',', tableon()->columns->options->get($table_id, 'orderby_select_fields', ''))),
                    'value_custom_field_key' => 'orderby_select_fields',
                    'notes' => esc_html__('fields which you want to see in Sorting Dropdown fields. Press and wait to reorder.', 'posts-table-filterable')
                ];

                $rows[] = [
                    'id' => $table_id,
                    'title' => esc_html__('Sorting Dropdown position', 'posts-table-filterable'),
                    'value' => TABLEON_HELPER::draw_select([
                        'data-action' => 'tableon_save_table_option',
                            ], [
                        'right' => esc_html__('Right', 'posts-table-filterable'),
                        'left' => esc_html__('Left', 'posts-table-filterable'),
                            ], tableon()->columns->options->get($table_id, 'sorting_position', 'right')),
                    'value_custom_field_key' => 'sorting_position',
                    'notes' => esc_html__('Sorting Dropdown position.', 'posts-table-filterable')
                ];
            }
        }
        return $rows;
    }

    /**
     * Get post, data is cached for further requests
     *
     * @since 1.0.0
     *
     * @return object post
     */
    public static function get_post($post_id) {
        static $cache = [];

        if (!isset($cache[$post_id])) {
            $cache[$post_id] = get_post($post_id);
        }

        return $cache[$post_id];
    }

    /**
     * Init all possible fields data/output/filtration for table columns initialization - data center
     *
     * @since 1.0.0
     * @param int $table_id table ID
     * @param array $shortcode_args shortcode arguments
     * @return array post active fields
     */
    public function action($table_id = 0, $shortcode_args = []) {
        //$current_action = current_action();

        $post_statuses = 'publish';
        $post_type = 'post';

        if ($table_id > 0) {
            $post_statuses = tableon()->columns->options->get($table_id, 'post_statuses', 'publish');
            $post_type = tableon()->tables->get($table_id)['post_type'];
        }

        if (isset($shortcode_args['post_status'])) {
            $post_statuses = $shortcode_args['post_status'];
        }

        if (!$post_statuses) {
            $post_statuses = 'publish';
        }

        if (isset($shortcode_args['post_type'])) {
            $post_type = $shortcode_args['post_type'];
        }

        //***

        $profile = [
            0 => [
                'post_type' => $post_type,
                //false - in options we can select post type for this profile, true - not
                'post_type_fixed' => FALSE,
                'post_statuses' => $post_statuses,
                'filter_provider' => 'default'
            ],
            'id' => [
                'title' => esc_html__('ID', 'posts-table-filterable'),
                'order' => 'desc',
                'options' => TABLEON_Default::$fields_options,
                'action' => function ($post_id) {
                    return $post_id;
                }
            ],
            'thumbnail' => [
                'title' => esc_html__('Thumbnail', 'posts-table-filterable'),
                'order' => FALSE,
                'options' => ['thumbnail_width', 'thumbnail_no_link', 'thumbnail_preview_width', 'css-background', 'css-media-hide'],
                'action' => function ($post_id) use ($table_id, $shortcode_args) {
                    $post = self::get_post($post_id);
                    $full = $thumb = TABLEON_ASSETS_LINK . 'img/not-found.jpg';
                    $has_img = false;

                    //***

                    $width = TABLEON_Settings::get('thumbnail_size');

                    if ($table_id > 0) {
                        $options = tableon()->get_field_options($table_id, 'thumbnail');

                        if (isset($options['thumbnail_width'])) {
                            $width = intval($options['thumbnail_width']);
                        }
                    }

                    if (!$width) {
                        $width = TABLEON_Settings::get('thumbnail_size');
                    }

                    //***

                    $img = TABLEON_HELPER::draw_html_item('img', array(
                                'src' => apply_filters('tableon_no_img_found', $thumb, $table_id, $post_id),
                                'width' => $width,
                                'alt' => '',
                                'class' => 'tableon-thumbnail'
                    ));

                    if (has_post_thumbnail($post_id)) {
                        $img_src = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'thumbnail');

                        if (is_array($img_src) AND!empty($img_src[0])) {
                            $thumb = $img_src[0];
                            $img = TABLEON_HELPER::draw_html_item('img', array(
                                        'src' => $thumb,
                                        'width' => $width,
                                        'alt' => '',
                                        'class' => 'tableon-thumbnail'
                            ));

                            //***

                            $img_src = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'full');
                            $full = $img_src[0];
                            $has_img = true;
                        }
                    }

                    $preview_width = 400;
                    if ($table_id > 0) {
                        $thumbnail_options = tableon()->get_field_options($table_id, 'thumbnail');
                        if (isset($thumbnail_options['thumbnail_preview_width'])) {
                            $preview_width = $thumbnail_options['thumbnail_preview_width'];
                        }
                    }

                    if (isset($shortcode_args['preview_image_width'])) {
                        $preview_width = intval($shortcode_args['preview_image_width']);
                    }

                    if (is_object($post)) {

                        $no_link = apply_filters('tableon_disable_all_links_on_thumbs', false);

                        if (isset($thumbnail_options)) {
                            if (isset($thumbnail_options['thumbnail_no_link'])) {
                                $no_link = intval($options['thumbnail_no_link']);
                            }
                        }

                        if (isset($shortcode_args['disable_link_on_thumb'])) {
                            $no_link = intval($shortcode_args['disable_link_on_thumb']);
                        }

                        //+++

                        if ($no_link) {
                            return $img;
                        } else {
                            return TABLEON_HELPER::draw_html_item('a', array(
                                        'href' => get_permalink($post_id),
                                        'data-full-img' => $full,
                                        'data-width' => $preview_width,
                                        'data-title' => $post->post_title,
                                        'target' => '_blank',
                                        'onmouseover' => $has_img && $preview_width > 0 ? 'tableon_helper.image_preview(this)' : ''
                                            ), $img);
                        }
                    }
                }
            ],
            'post_title' => [
                'title' => esc_html__('Title', 'posts-table-filterable'),
                'order' => 'asc',
                'options' => array_merge(['title_as_text', 'show_labels'], TABLEON_Default::$fields_options),
                'filter' => true,
                'filter_view' => 'textinput',
                'filter_options' => ['placeholder', 'minlength', 'width'],
                'action' => function ($post_id) use ($table_id, $shortcode_args) {
                    $post = self::get_post($post_id);

                    if (is_object($post)) {
                        $as_text = false;

                        if ($table_id > 0) {
                            $options = tableon()->get_field_options($table_id, 'post_title');
                            $as_text = isset($options['title_as_text']) ? intval($options['title_as_text']) : 0;
                        } else {
                            $as_text = apply_filters('tableon_disable_all_links_on_titles', false);
                        }

                        if (isset($shortcode_args['post_title_as_text'])) {
                            $as_text = intval($shortcode_args['post_title_as_text']);
                        }

                        //***
                        $labels = '';
                        $show_labels = apply_filters('tableon_titles_labels', [], $post_id);
                        if (!empty($show_labels)) {
                            $labels = '<div class="tableon-post-tag-container">';

                            foreach ($show_labels as $label) {
                                $labels .= "<span class='tableon-post-tag {$label['css_class']}'>{$label['title']}</span>";
                            }

                            $labels .= '</div>';
                        }

                        //***

                        if ($as_text) {
                            return $labels . $post->post_title;
                        } else {
                            return $labels . TABLEON_HELPER::draw_html_item('a', array(
                                        'href' => get_permalink($post_id),
                                        'title' => $post->post_title,
                                        'target' => '_blank'
                                            ), $post->post_title);
                        }
                    }
                },
                'get_filter_query_args' => function ($args, $value) use ($shortcode_args) {

                    $value = trim(TABLEON_HELPER::strtolower($value));

                    if (!empty($value)) {

                        add_filter('posts_where', function ($where = '') use ($args, $value) {
                            $sql = "";

                            if (isset($args['tableon_text_search_by']) AND!empty($args['tableon_text_search_by'])) {
                                $sql = " AND (";
                                foreach ($args['tableon_text_search_by'] as $field) {
                                    $sql .= "LOWER({$field}) LIKE '%{$value}%' OR ";
                                }
                                $sql = trim($sql, ' OR ');
                                $sql .= ")";
                            }

                            $where .= $sql;
                            return $where;
                        }, 101);
                    }

                    return $args;
                },
                'get_filter_draw_data' => function ($table_id) {
                    return tableon()->filter->get_field_drawing_data($table_id, 'post_title');
                }
            ],
            'post_excerpt' => [
                'title' => esc_html__('Excerpt', 'posts-table-filterable'),
                'options' => TABLEON_Default::$fields_options,
                'filter' => true,
                'filter_view' => 'textinput',
                'filter_options' => ['placeholder', 'minlength', 'width'],
                'action' => function ($post_id) {
                    return TABLEON_HELPER::wrap_text_to_container(strip_tags(apply_filters('the_content', get_the_excerpt($post_id)), self::$allowed_tags), get_post_field('post_title', $post_id));
                },
                'get_filter_query_args' => function ($args, $value) {

                    $value = trim($value);

                    if (!empty($value)) {
                        add_filter('posts_where', function ($where = '') use ($value) {
                            $value = trim(TABLEON_HELPER::strtolower($value));
                            $where .= "  AND LOWER(post_excerpt) LIKE '%{$value}%'";
                            return $where;
                        }, 101);
                    }

                    return $args;
                },
                'get_filter_draw_data' => function ($table_id) {
                    return tableon()->filter->get_field_drawing_data($table_id, 'post_excerpt');
                }
            ],
            'post_content' => [
                'title' => esc_html__('Content', 'posts-table-filterable'),
                'options' => TABLEON_Default::$fields_options,
                'filter' => true,
                'filter_view' => 'textinput',
                'filter_options' => ['placeholder', 'minlength', 'width'],
                'action' => function ($post_id) {
                    return TABLEON_HELPER::wrap_text_to_container(strip_tags(apply_filters('the_content', get_the_content(null, false, $post_id)), self::$allowed_tags), get_post_field('post_title', $post_id));
                },
                'get_filter_query_args' => function ($args, $value) {

                    $value = trim($value);

                    if (!empty($value)) {
                        add_filter('posts_where', function ($where = '') use ($value) {
                            $value = trim(TABLEON_HELPER::strtolower($value));
                            $where .= "  AND LOWER(post_content) LIKE '%{$value}%'";
                            return $where;
                        }, 101);
                    }

                    return $args;
                },
                'get_filter_draw_data' => function ($table_id) {
                    return tableon()->filter->get_field_drawing_data($table_id, 'post_content');
                }
            ],
            'single' => [
                'title' => esc_html__('Single', 'posts-table-filterable'),
                'options' => ['css-background', 'css-media-hide'],
                'action' => function ($post_id) use ($table_id, $shortcode_args) {
                    $title = esc_html__('Single post data', 'posts-table-filterable') . ': ';
                    $title .= addslashes(get_post_field('post_title', $post_id));
                    $unique_id = uniqid('gp');

                    $skin = '';
                    if (isset($table_id) AND $table_id > 0) {
                        $skin = tableon()->skins->get($table_id);
                    }

                    if (isset($shortcode_args) AND isset($shortcode_args['skin'])) {
                        $skin = $shortcode_args['skin'];
                    }

                    return TABLEON_HELPER::draw_html_item('a', array(
                        'href' => '#',
                        'onclick' => "let skin=tableon_helper.get_closest_skin(this, \"{$skin}\"); return tableon_helper.call_popup(\"tableon_get_table_single_post\",{post_id: {$post_id}, skin: skin, not_paste:1}, \"{$unique_id}\", \"{$title}\");",
                        'title' => $title,
                        'class' => 'tableon-btn tableon-btn-1'
                            ), apply_filters('tableon_single_btn_text', '<i class="tableon-icon">&#xf1c6;</i>'));
                }
            ],
            'post_status' => [
                'title' => esc_html__('Status', 'posts-table-filterable'),
                'options' => TABLEON_Default::$fields_options,
                'order' => 'asc',
                'action' => function ($post_id) {
                    if (intval($post_id) > 0) {
                        return get_post_statuses()[get_post_field('post_status', $post_id)];
                    }
                }
            ],
            'post_author' => [
                'title' => esc_html__('Author', 'posts-table-filterable'),
                'options' => TABLEON_Default::$fields_options,
                'filter' => true,
                'filter_view' => 'select',
                'filter_options' => ['title'],
                'action' => function ($post_id) {
                    if (intval($post_id) > 0) {
                        $post = get_post($post_id);
                        $user_nicename = get_the_author_meta('user_nicename', $post->post_author);
                        return TABLEON_HELPER::draw_html_item('a', [
                                    'href' => get_author_posts_url($post->post_author, $user_nicename),
                                    'target' => '_blank'
                                        ], $user_nicename);
                    }
                },
                'get_filter_query_args' => function ($args, $value) {

                    if ($value) {
                        $args['author'] = $value[0];
                    }

                    return $args;
                },
                'get_filter_draw_data' => function ($table_id)use ($post_type) {
                    global $wpdb;
                    $options = [];
                    $sql = "SELECT DISTINCT(post_author) FROM `{$wpdb->prefix}posts` WHERE post_type='{$post_type}'";
                    $res = $wpdb->get_results($sql, ARRAY_A);

                    if (!empty($res)) {
                        foreach ($res as $r) {
                            $options[] = [
                                'id' => $r['post_author'],
                                'title' => get_user_by('id', $r['post_author'])->display_name
                            ];
                        }
                    }

                    $title = esc_html__('Author', 'posts-table-filterable');
                    $tmp = tableon()->filter->get_field_drawing_data($table_id, 'post_author');
                    if (isset($tmp['title']) AND!empty($tmp['title'])) {
                        $title = $tmp['title'];
                    }

                    return [
                'title' => $title,
                'view' => 'select',
                'options' => $options
                    ];
                }
            ],
            'post_date' => [
                'title' => esc_html__('Post date', 'posts-table-filterable'),
                'options' => TABLEON_Default::$fields_options,
                'order' => 'desc',
                'filter' => true,
                'filter_view' => 'calendar',
                'filter_options' => ['placeholder'],
                'action' => function ($post_id) {
                    if (intval($post_id) > 0) {
                        $post = get_post($post_id);
                        return date(apply_filters('tableon_date_format', get_option('date_format')), strtotime($post->post_date));
                    }
                },
                'get_filter_query_args' => function ($args, $value, $is_calendar_dir_to = false) {

                    add_filter('posts_where', function ($where = '') use ($value, $is_calendar_dir_to) {
                        $value = date('Y-m-d H:i:s', $value);
                        if ($is_calendar_dir_to) {
                            $where .= "  AND post_date <= '{$value}'";
                        } else {
                            $where .= "  AND post_date >= '{$value}'";
                        }

                        return $where;
                    }, 101);

                    return $args;
                },
                'get_filter_draw_data' => function ($table_id) {
                    return tableon()->filter->get_field_drawing_data($table_id, 'post_date');
                }
            ],
            'post_modified' => [
                'title' => esc_html__('Post modified', 'posts-table-filterable'),
                'options' => TABLEON_Default::$fields_options,
                'order' => 'desc',
                'filter' => true,
                'filter_view' => 'calendar',
                'filter_options' => ['placeholder'],
                'action' => function ($post_id) {
                    if (intval($post_id) > 0) {
                        $post = get_post($post_id);
                        return date(apply_filters('tableon_date_format', get_option('date_format')), strtotime($post->post_modified));
                    }
                },
                'get_filter_query_args' => function ($args, $value, $is_calendar_dir_to = false) {


                    add_filter('posts_where', function ($where = '') use ($value, $is_calendar_dir_to) {
                        $value = date('Y-m-d H:i:s', $value);

                        if ($is_calendar_dir_to) {
                            $where .= "  AND post_modified <= '{$value}'";
                        } else {
                            $where .= "  AND post_modified >= '{$value}'";
                        }

                        return $where;
                    }, 101);

                    return $args;
                },
                'get_filter_draw_data' => function ($table_id) {
                    return tableon()->filter->get_field_drawing_data($table_id, 'post_modified');
                }
            ],
            'comment_count' => [
                'title' => esc_html__('Comment count', 'posts-table-filterable'),
                'options' => TABLEON_Default::$fields_options,
                'order' => 'desc',
                'filter' => true,
                'filter_view' => 'range_slider',
                'filter_options' => ['title', 'width'],
                'action' => function ($post_id) {
                    return '<span class="tableon-num-cell">' . get_comments_number($post_id) . '</span>';
                },
                'get_filter_query_args' => function ($args, $value) {

                    $value = explode(':', trim($value));

                    if (!empty($value) AND is_array($value)) {
                        add_filter('posts_where', function ($where = '') use ($value) {
                            $where .= "  AND (comment_count >= {$value[0]} AND comment_count <= {$value[1]})";
                            return $where;
                        }, 101);
                    }

                    return $args;
                },
                'get_filter_draw_data' => function ($table_id)use ($post_type) {
                    $res = tableon()->filter->get_field_drawing_data($table_id, 'comment_count');

                    global $wpdb;
                    $sql = "SELECT MIN(comment_count) AS min FROM `{$wpdb->prefix}posts` WHERE post_type='{$post_type}'";
                    $r = $wpdb->get_results($sql, ARRAY_A);
                    $res['min'] = $r[0]['min'];
                    $sql = "SELECT MAX(comment_count) AS max FROM `{$wpdb->prefix}posts` WHERE post_type='{$post_type}'";
                    $r = $wpdb->get_results($sql, ARRAY_A);
                    $res['max'] = $r[0]['max'];

                    return $res;
                }
            ],
            'gallery' => [
                'title' => esc_html__('Gallery', 'posts-table-filterable'),
                'options' => ['css-background', 'css-media-hide'],
                'action' => function ($post_id) {
                    $post = self::get_post($post_id);

                    if (is_object($post)) {
                        $images_ids = TABLEON_HELPER::get_gallery_image_ids($post_id);

                        $img_data = [];
                        if (!empty($images_ids)) {
                            foreach ($images_ids as $attachment_id) {
                                $img = wp_get_attachment_image_src($attachment_id);
                                if (isset($img[0])) {
                                    $img_data[] = [
                                        'thumb' => $img[0],
                                        'original' => wp_get_attachment_image_src($attachment_id, 'full')[0],
                                        'title' => $post->post_title
                                    ];
                                }
                            }
                        }

                        if (!empty($img_data)) {
                            return TABLEON_HELPER::render_html('views/gallery.php', apply_filters('tableon_print_plugin_options', ['img_data' => $img_data]));
                        } else {
                            return '';
                        }
                    }
                }
            ],
                /*
                  'upsell' => [
                  'title' => esc_html__('Upsells', 'posts-table-filterable'),
                  'options' => ['css-background', 'css-media-hide'],
                  'action' => function($post_id) use($table_id, $shortcode_args) {
                  //return $this->get_field_data('upsell', $post_id);
                  $post = self::get_post($post_id);
                  $unique_id = uniqid('upt');

                  if (is_object($post)) {
                  $title = addslashes(sprintf("%s - Upsells", "#{$post_id}. {$post->post_title}"));

                  if (count($post->get_upsell_ids()) > 0) {

                  $skin = '';
                  if (isset($table_id) AND $table_id > 0) {
                  $skin = tableon()->skins->get($table_id);
                  }

                  if (isset($shortcode_args) AND isset($shortcode_args['skin'])) {
                  $skin = $shortcode_args['skin'];
                  }

                  return TABLEON_HELPER::draw_html_item('a', array(
                  'href' => "#",
                  'onclick' => "let skin=tableon_helper.get_closest_skin(this, \"{$skin}\"); return tableon_helper.call_popup(\"tableon_default_get_upsells_table\",{post_id: {$post->get_id()}, skin: skin, not_paste:1},\"{$unique_id}\", \"{$title}\");",
                  'title' => $post->post_title,
                  'class' => 'tableon-btn'
                  ), sprintf(TABLEON_Vocabulary::get(esc_html__('Upsells[%s]', 'posts-table-filterable')), count($post->get_upsell_ids())));
                  } else {
                  return '-';
                  }
                  }
                  }
                  ],
                 */
        ];

        //*** lets add woo taxonomies and attributes
        //get all posts taxonomies
        $taxonomy_objects = get_object_taxonomies($post_type, 'objects');
        unset($taxonomy_objects['post_format']);

        if (isset($taxonomy_objects['translation_priority'])) {
            unset($taxonomy_objects['translation_priority']);
        }

        if (!empty($taxonomy_objects)) {
            
            foreach ($taxonomy_objects as $t) {

                $profile[$t->name] = [
                    'title' => $t->label,
                    'options' => array_merge(['display_as_text'], TABLEON_Default::$fields_options),
                    'filter' => true,
                    'filter_view' => 'select',
                    'filter_options' => ['as-mselect', 'mselect-logic', 'exclude', 'include'],
                    'action' => function ($post_id) use ($t, $table_id) {
                        $res = '';
                        $post = self::get_post($post_id);
                        $include_only = wp_get_post_terms($post_id, $t->name, ['fields' => 'ids']);

                        if (empty($include_only)) {
                            $include_only = [-1];
                        }

                        $options = tableon()->filter->build_taxonomy_tree($t->name, tableon()->filter->get_terms($t->name, 0), [], $include_only);

                        if (!empty($options)) {
                            $links = [];
                            $display_as_text = false;

                            if ($table_id > 0) {
                                if ($tmp = tableon()->columns->get_by_field_key($table_id, $t->name)) {
                                    $tmp = json_decode($tmp['options'], true);
                                    if (isset($tmp['display_as_text'])) {
                                        $display_as_text = boolval(intval($tmp['display_as_text']));
                                    }
                                }
                            }

                            foreach ($options as $t) {
                                if (!$display_as_text) {
                                    $links[] = TABLEON_HELPER::draw_html_item('a', array(
                                                'href' => get_term_link($t['id']),
                                                'class' => 'tableon-tax-term tableon-' . $t['name'] . '-' . $t['slug'],
                                                'target' => '_blank',
                                                    ), TABLEON_Vocabulary::get($t['name']));
                                } else {
                                    $links[] = TABLEON_HELPER::draw_html_item('span', array(
                                                'class' => 'tableon-tax-term tableon-' . $t['name'] . '-' . $t['slug']
                                                    ), TABLEON_Vocabulary::get($t['name']));
                                }
                            }

                            $res = implode(' ', $links);
                        }


                        return $res;
                    },
                    'get_filter_query_args' => function ($args, $value) use ($t, $table_id) {

                        global $wp_taxonomies;
                        if (in_array($t->name, array_keys($wp_taxonomies))) {

                            $logic = 'IN';

                            if ($table_id > 0) {
                                $logic = tableon()->filter->fields_options->get($table_id, "{$t->name}-mselect-logic");
                                if (!in_array($logic, ['IN', 'NOT IN', 'AND'])) {
                                    $logic = 'IN';
                                }
                            }

                            $args['tax_query'][] = array(
                                'taxonomy' => $t->name,
                                'field' => 'term_id',
                                'terms' => (array) $value,
                                'operator' => $logic
                            );
                        }


                        return $args;
                    },
                    'get_filter_draw_data' => function ($table_id) use ($t) {
                        return tableon()->filter->get_taxonomy_drawing_data($t->name, $table_id);
                    }
                ];
            }
        }

        //***

        if ($table_id > 0) {
            tableon()->columns->meta->extend_profile_fields(self::$action, $table_id);
        }

        return apply_filters('tableon_profile_extend', apply_filters('ext_' . self::$action, $profile, $table_id, $shortcode_args), self::$action, $shortcode_args);
    }

    /**
     * Table $table_id fields for "order-by" drop down selected in its options
     *
     * @since 1.0.0
     * @param string $fields_keys fields keys
     * @param int $table_id table ID
     * @return array fields
     */
    public static function get_select_orderby_options($fields_keys = '', $table_id = 0) {
        if (!empty($fields_keys)) {
            $fields_keys = explode(',', $fields_keys);
            $fields = apply_filters('tableon_table_orderby_select_args', self::$orderby_select_fields, $table_id);
            $res = [];
            foreach ($fields_keys as $key) {
                if (isset($fields[$key])) {
                    $res[$key] = $fields[$key];
                }
            }

            return $res;
        }

        return [];
    }

    /**
     * Add columns to the plugged-in extensions
     *
     * @since 1.0.0
     * @param array $profile fields data
     * @param array $selected_columns_keys selected fields
     * @return array fields
     */
    public function extend_ext_profiles($profile, $selected_columns_keys) {

        $woo_profile = $this->action();

        if (!empty($selected_columns_keys)) {
            foreach ($selected_columns_keys as $key) {
                if (in_array($key, ['actions'])) {
                    continue;
                }

                //***

                if (isset($woo_profile[$key])) {
                    $profile[$key] = $woo_profile[$key];
                } else {

                    if (isset(TABLEON::$synonyms[$key])) {
                        $profile[$key] = $woo_profile[TABLEON::$synonyms[$key]];
                    } else {
                        $profile[$key] = [
                            'title' => esc_html__('not exists', 'posts-table-filterable'),
                            'action' => function ($post_id) {
                                return esc_html__('not exists', 'posts-table-filterable');
                            }
                        ];
                    }
                }
            }
        }

        return $profile;

    }

}

//+++

new TABLEON_Default();

