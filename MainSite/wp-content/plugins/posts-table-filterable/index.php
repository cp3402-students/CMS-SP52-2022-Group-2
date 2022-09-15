<?php
/*
  Plugin Name: TableOn - Posts Table Filterable
  Plugin URI: https://posts-table.com/
  Description: Plugin for displaying site posts in table format. Tables makes focus for your site visitors on the things they want to get, nothing superfluous, just what the client wants, and full attention to what is offered!
  Requires at least: WP 4.9
  Tested up to: WP 6.0
  Author: realmag777
  Author URI: https://pluginus.net/
  Version: 1.0.2
  Requires PHP: 7.0
  Tags: posts table,posts filter,table,filter
  Text Domain: posts-table-filterable
  Domain Path: /languages
  Forum URI: https://pluginus.net/support/forum/tableon-posts-table-filterable
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

//+++

define('TABLEON_PATH', plugin_dir_path(__FILE__));
define('TABLEON_LINK', plugin_dir_url(__FILE__));
define('TABLEON_ASSETS_LINK', TABLEON_LINK . 'assets/');
define('TABLEON_ASSETS_PATH', TABLEON_PATH . 'assets/');
define('TABLEON_PLUGIN_NAME', plugin_basename(__FILE__));
define('TABLEON_VERSION', '1.0.2');

require_once TABLEON_PATH . 'install.php';

//classes
//include_once TABLEON_PATH . 'lib/storage.php';
include_once TABLEON_PATH . 'lib/helper.php';
include_once TABLEON_PATH . 'classes/settings.php';
include_once TABLEON_PATH . 'classes/vocabulary.php';
include_once TABLEON_PATH . 'classes/predefinition.php';
include_once TABLEON_PATH . 'classes/filter.php';
include_once TABLEON_PATH . 'classes/skins.php';
include_once TABLEON_PATH . 'classes/tables.php';
include_once TABLEON_PATH . 'lib/rate_alert.php';

//profiles
include_once TABLEON_PATH . 'profiles/default/default.php';

//09-08-2022
class TABLEON {

    public $tables = null;
    public $filter = null;
    public $predefinition = null;
    public $skins = null;
    public $columns = null;
    public $settings = null;
    public $vocabulary = null;
    public static $tables_actions = [];
    public static $synonyms = [
        'title' => 'post_title',
        'excerpt' => 'post_excerpt',
        'content' => 'post_content',
        'author' => 'post_author'
    ];
    public $extend_ext_profiles = [];
    public $default_columns = ['id', 'thumbnail', 'title', 'gallery'];
    public $notes_for_free = true;
    public static $app_mode = 'dev'; //dev, production

    public function __construct() {
        $this->settings = new TABLEON_Settings();
        $this->vocabulary = new TABLEON_Vocabulary();

        add_shortcode('tableon', array($this, 'do_shortcode'));
        add_shortcode('tableon_button', array($this, 'do_shortcode_button'));
        add_action('wp_ajax_tableon_get_table_data', array($this, 'get_table_data'));
        add_action('wp_ajax_nopriv_tableon_get_table_data', array($this, 'get_table_data'));

        add_action('wp_ajax_tableon_get_smth', array($this, 'get_smth'));
        add_action('wp_ajax_nopriv_tableon_get_smth', array($this, 'get_smth'));

        add_action('wp_ajax_tableon_import_data', array($this, 'import_data'));

        global $active_tables_actions;
        //self::$tables_actions['default'] = esc_html__('Default', 'posts-table-filterable');
        if (isset($active_tables_actions)) {
            self::$tables_actions = array_merge(self::$tables_actions, $active_tables_actions);
        }


        $this->filter = new TABLEON_Filter();
        $this->predefinition = new TABLEON_Predefinition();
        $this->skins = new TABLEON_Skins();
        $this->tables = new TABLEON_Tables();
        $this->columns = new TABLEON_Columns();

        add_action('admin_init', function () {
            if (TABLEON_HELPER::can_manage_data()) {
                add_filter('plugin_action_links_' . TABLEON_PLUGIN_NAME, function ($links) {
                    return array_merge(array(
                '<a href="' . admin_url('admin.php?page=tableon') . '">' . esc_html__('Posts Tables', 'posts-table-filterable') . '</a>',
                '<a target="_blank" href="' . esc_url('https://posts-table.com/') . '"><span class="icon-book"></span>&nbsp;' . esc_html__('Documentation', 'posts-table-filterable') . '</a>'
                    ), $links);
                }, 50);
            }
        }, 9999);

        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));

        if (is_admin()) {
            $this->rate_alert = new TABLEON_RATE_ALERT($this->notes_for_free);
        }
    }

    /**
     * Hook init
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function init() {

        load_plugin_textdomain('posts-table-filterable', false, dirname(plugin_basename(__FILE__)) . '/languages');

        if (TABLEON_HELPER::can_manage_data()) {
            add_action('admin_menu', function () {
                add_menu_page('TABLEON', 'TABLEON', 'publish_posts', 'tableon', function () {

                    $args = [];
                    $table_html_id = 'tableon-admin-table';
                    $args['main_table'] = $this->draw_table_data([
                                'action' => 'tableon_admin_table',
                                //'mode' => 'to_json',
                                'mode' => 'json',
                                'orderby' => 'id',
                                'order' => 'desc',
                                'per_page_sel_pp' => -1,
                                'per_page' => -1,
                                'table_data' => $this->tables->get_admin_table_rows(),
                                'use_flow_header' => 0
                                    ], $table_html_id) . TABLEON_HELPER::render_html('views/table.php', array(
                                'table_html_id' => $table_html_id,
                                'hide_text_search' => false,
                                'table_view' => 'separated'
                    ));

                    //+++

                    $args['settings_table'] = $this->settings->draw_table();

                    echo TABLEON_HELPER::render_html('views/options.php', apply_filters('tableon_print_plugin_options', $args));
                }, 'dashicons-tableon', '57.1');
            }, 99);
        }

        //***

        add_action('wp_print_footer_scripts', function () {
            echo TABLEON_HELPER::render_html('views/popup.php');
        });
    }

    /**
     * Hook admin_enqueue_scripts
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function admin_enqueue_scripts() {

        wp_enqueue_style('tableon-system', TABLEON_ASSETS_LINK . 'css/admin/system.css', [], TABLEON_VERSION);

        if (isset($_GET['page']) AND $_GET['page'] == 'tableon') {

            wp_enqueue_media();

            wp_enqueue_script('tableon-helper', TABLEON_ASSETS_LINK . 'js/helper.js', [], TABLEON_VERSION, true);

            wp_enqueue_style('selectm-23', TABLEON_ASSETS_LINK . 'css/selectm-23.css', [], TABLEON_VERSION);
            wp_enqueue_script('selectm-23', TABLEON_ASSETS_LINK . 'js/selectm-23.js', [], TABLEON_VERSION);

            wp_enqueue_style('tableon-growls', TABLEON_ASSETS_LINK . 'css/growls.css', [], TABLEON_VERSION);
            wp_enqueue_style('tableon-popup-23', TABLEON_ASSETS_LINK . 'css/popup-23.css', [], TABLEON_VERSION);
            wp_enqueue_style('tableon-switcher-23', TABLEON_ASSETS_LINK . 'css/switcher-23.css', [], TABLEON_VERSION);
            wp_enqueue_style('tableon-options', TABLEON_ASSETS_LINK . 'css/admin/options.css', [], TABLEON_VERSION);

            wp_enqueue_script('data-table-23', TABLEON_ASSETS_LINK . 'js/data-table-23/data-table-23.js', [], TABLEON_VERSION, true);
            wp_enqueue_style('data-table-23', TABLEON_ASSETS_LINK . 'js/data-table-23/data-table-23.css', [], TABLEON_VERSION);

            wp_enqueue_script('tableon-generated-tables', TABLEON_ASSETS_LINK . 'js/admin/generated-tables.js', ['data-table-23'], TABLEON_VERSION, true);
            wp_enqueue_script('popup-23', TABLEON_ASSETS_LINK . 'js/popup-23.js', [], TABLEON_VERSION);
            wp_enqueue_script('alasql', TABLEON_ASSETS_LINK . 'js/alasql.min.js', [], '0.5.5', true);

            wp_enqueue_script('tableon-horizontal-scrollbar-23', TABLEON_ASSETS_LINK . 'js/horizontal-scrollbar-23.js', array(), TABLEON_VERSION);
            wp_enqueue_style('tableon-horizontal-scrollbar-23', TABLEON_ASSETS_LINK . 'css/horizontal-scrollbar-23.css', array(), TABLEON_VERSION);

            wp_enqueue_script('tableon-spectrum', TABLEON_ASSETS_LINK . 'js/spectrum/spectrum.min.js', array(), TABLEON_VERSION);
            wp_enqueue_style('tableon-spectrum', TABLEON_ASSETS_LINK . 'js/spectrum/spectrum.min.css', array(), TABLEON_VERSION);

            //codeEditor
            $custom_css_settings = [];
            $custom_css_settings['codeEditor'] = wp_enqueue_code_editor(array(
                'type' => 'text/css',
                'lineNumbers' => true,
                'indentUnit' => 2,
                'tabSize' => 2
            ));
            wp_localize_script('jquery', 'custom_css_settings', $custom_css_settings);
            wp_enqueue_script('code-editor');
            wp_enqueue_style('code-editor');
            wp_enqueue_style('wp-codemirror');
            wp_enqueue_script('htmlhint');
            wp_enqueue_script('csslint');
            wp_enqueue_script('jshint');

            wp_enqueue_script('tableon-options', TABLEON_ASSETS_LINK . 'js/admin/options.js', ['data-table-23', 'jquery', 'jquery-ui-core', 'jquery-ui-sortable'], TABLEON_VERSION, true);

            $this->wp_localize_script('tableon-options');
        }
    }

    /**
     * Hook wp_localize_script
     *
     * @since 1.0.0
     * @param string $handle string handle
     * @return void
     */
    public function wp_localize_script($handle) {

        wp_localize_script($handle, 'tableon_helper_vars', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'profiles' => [], //for tables data collection
            'assets_url' => TABLEON_ASSETS_LINK,
            'mode' => self::$app_mode,
            'flags' => [], //flags for custom js actions which should not be inited twice
            'selected_lang' => apply_filters('tableon_current_lang', get_locale()),
            'lang' => apply_filters('tableon_helper_lang', [
                'loading' => TABLEON_Vocabulary::get(esc_html__('Loading ...', 'posts-table-filterable')),
                'no_data' => TABLEON_Vocabulary::get(esc_html__('No Data!', 'posts-table-filterable')),
                'error' => TABLEON_Vocabulary::get(esc_html__('Error!', 'posts-table-filterable')),
                'creating' => TABLEON_Vocabulary::get(esc_html__('Creating', 'posts-table-filterable')),
                'created' => TABLEON_Vocabulary::get(esc_html__('Created!', 'posts-table-filterable')),
                'saving' => TABLEON_Vocabulary::get(esc_html__('Saving', 'posts-table-filterable')),
                'saved' => TABLEON_Vocabulary::get(esc_html__('Saved!', 'posts-table-filterable')),
                'adding' => TABLEON_Vocabulary::get(esc_html__('Adding', 'posts-table-filterable')),
                'added' => TABLEON_Vocabulary::get(esc_html__('Added!', 'posts-table-filterable')),
                'deleting' => TABLEON_Vocabulary::get(esc_html__('Deleting', 'posts-table-filterable')),
                'deleted' => TABLEON_Vocabulary::get(esc_html__('Deleted!', 'posts-table-filterable')),
                'updating' => TABLEON_Vocabulary::get(esc_html__('Updating', 'posts-table-filterable')),
                'cloning' => TABLEON_Vocabulary::get(esc_html__('Cloning', 'posts-table-filterable')),
                'cloned' => TABLEON_Vocabulary::get(esc_html__('Cloned!', 'posts-table-filterable')),
                'sure' => TABLEON_Vocabulary::get(esc_html__('Are you sure?', 'posts-table-filterable')),
                'm_notice' => TABLEON_Vocabulary::get(esc_html__('Notice!', 'posts-table-filterable')),
                'm_warning' => TABLEON_Vocabulary::get(esc_html__('Warning!', 'posts-table-filterable')),
                'm_error' => TABLEON_Vocabulary::get(esc_html__('Error!', 'posts-table-filterable')),
                'reset' => TABLEON_Vocabulary::get(esc_html__('Reset', 'posts-table-filterable')),
                'select_table_thumb' => TABLEON_Vocabulary::get(esc_html__('Select table thumbnail', 'posts-table-filterable')),
                'shortcodes_help' => TABLEON_Vocabulary::get(esc_html__('Shortcodes Help', 'posts-table-filterable')),
                'help' => TABLEON_Vocabulary::get(esc_html__('Help', 'posts-table-filterable')),
                'calendar23_names' => apply_filters('tableon_get_calendar_names', []),
                'select' => TABLEON_Vocabulary::get(esc_html__('select', 'posts-table-filterable')),
                'select_all_prod' => TABLEON_Vocabulary::get(esc_html__('Select all posts', 'posts-table-filterable')),
                'show_filter' => apply_filters('tableon_show_filter_btn_txt', '<span class="dashicons-before dashicons-filter"></span>' . TABLEON_Vocabulary::get(esc_html__('show', 'posts-table-filterable'))),
                'hide_filter' => apply_filters('tableon_show_filter_btn_txt_hide', '<span class="dashicons-before dashicons-filter"></span>' . TABLEON_Vocabulary::get(esc_html__('hide', 'posts-table-filterable'))),
                'filter_field_popup_title' => TABLEON_Vocabulary::get(esc_html__('Table #{0}; filter field: {1}', 'posts-table-filterable')),
                'post_title' => TABLEON_Vocabulary::get(esc_html__('Post title', 'posts-table-filterable')),
                'importing' => TABLEON_Vocabulary::get(esc_html__('Importing', 'posts-table-filterable')) . ' ...',
                'imported' => TABLEON_Vocabulary::get(esc_html__('Imported!', 'posts-table-filterable')),
                'online' => TABLEON_Vocabulary::get(esc_html__('Online!', 'posts-table-filterable')),
                'offline' => TABLEON_Vocabulary::get(esc_html__('Offline!', 'posts-table-filterable')),
            ])
                ]
        );
    }

    /**
     * Include assets only on the pages where TABLEON functionality uses
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function include_assets() {

        wp_enqueue_script('tableon-helper', TABLEON_ASSETS_LINK . 'js/helper.js', [], TABLEON_VERSION, true);
        wp_enqueue_script('tableon-generated-tables', TABLEON_ASSETS_LINK . 'js/generated-tables.js', ['data-table-23'], TABLEON_VERSION, true);

        wp_enqueue_script('data-table-23', TABLEON_ASSETS_LINK . 'js/data-table-23/data-table-23.js', [], TABLEON_VERSION, true);
        wp_enqueue_style('data-table-23', TABLEON_ASSETS_LINK . 'js/data-table-23/data-table-23.css', [], TABLEON_VERSION);

        wp_enqueue_style('tableon-general', TABLEON_ASSETS_LINK . 'css/general.css', ['data-table-23'], TABLEON_VERSION);
        wp_enqueue_style('tableon-popup-23', TABLEON_ASSETS_LINK . 'css/popup-23.css', [], TABLEON_VERSION);
        wp_enqueue_style('tableon-growls', TABLEON_ASSETS_LINK . 'css/growls.css', [], TABLEON_VERSION);
        wp_enqueue_style('tableon-gallery', TABLEON_ASSETS_LINK . 'css/gallery.css', [], TABLEON_VERSION);
        wp_enqueue_style('tableon-filter', TABLEON_ASSETS_LINK . 'css/filter.css', [], TABLEON_VERSION);
        wp_enqueue_style('tableon-switcher-23', TABLEON_ASSETS_LINK . 'css/switcher-23.css', [], TABLEON_VERSION);

        wp_enqueue_style('calendar-23', TABLEON_ASSETS_LINK . 'css/calendar-23.css', [], TABLEON_VERSION);
        wp_enqueue_script('calendar-23', TABLEON_ASSETS_LINK . 'js/calendar-23.js', [], TABLEON_VERSION);

        wp_enqueue_style('ranger-23', TABLEON_ASSETS_LINK . 'css/ranger-23.css', [], TABLEON_VERSION);
        wp_enqueue_script('ranger-23', TABLEON_ASSETS_LINK . 'js/ranger-23.js', [], TABLEON_VERSION);

        wp_enqueue_style('selectm-23', TABLEON_ASSETS_LINK . 'css/selectm-23.css', [], TABLEON_VERSION);
        wp_enqueue_script('selectm-23', TABLEON_ASSETS_LINK . 'js/selectm-23.js', [], TABLEON_VERSION);

        wp_enqueue_script('select-23', TABLEON_ASSETS_LINK . 'js/select-23.js', [], TABLEON_VERSION);

        wp_enqueue_script('popup-23', TABLEON_ASSETS_LINK . 'js/popup-23.js', [], TABLEON_VERSION);

        wp_enqueue_script('tableon-horizontal-scrollbar-23', TABLEON_ASSETS_LINK . 'js/horizontal-scrollbar-23.js', array(), TABLEON_VERSION);
        wp_enqueue_style('tableon-horizontal-scrollbar-23', TABLEON_ASSETS_LINK . 'css/horizontal-scrollbar-23.css', array(), TABLEON_VERSION);

        wp_enqueue_script('tableon-filter', TABLEON_ASSETS_LINK . 'js/filter.js', [], TABLEON_VERSION, true);

        wp_enqueue_script('alasql', TABLEON_ASSETS_LINK . 'js/alasql.min.js', [], '0.5.5', true);
        wp_enqueue_script('tableon-general', TABLEON_ASSETS_LINK . 'js/general.js', ['data-table-23', 'tableon-horizontal-scrollbar-23'], TABLEON_VERSION, true);

        $this->wp_localize_script('tableon-general');

        do_action('tableon_include_assets');
    }

    /**
     * Main shortcode [tableon] function
     *
     * @since 1.0.0
     * @param array $args shortcode arguments
     * @return string table HTML
     */
    public function do_shortcode($args) {
        $this->include_assets();

        $args = (array) $args;

        $table_id = 0;
        if (isset($args['id'])) {
            $table_id = intval($args['id']);
        }

        if ($table_id > 0) {
            if (!$this->tables->is_exists($table_id)) {
                return '<div class="tableon-notice">' . sprintf(esc_html__('Table #%s does not exists!', 'posts-table-filterable'), $table_id) . '</div>';
            }
        }

        $table_html_id = '';

        //+++

        if ($table_id > 0 AND!isset($args['table_html_id'])) {
            $table_html_id = $this->columns->options->get($table_id, 'table_html_id', '');
        }

        if (isset($args['table_html_id'])) {
            $table_html_id = $args['table_html_id'];
        }

        if (empty($table_html_id)) {
            $table_html_id = uniqid('t');
        }

        //***

        $args['action'] = TABLEON_Default::$action; //HARDCODED FOR TABLEON
        //***
        $style = '';

        //for skin switcher
        if (isset($_GET['tableon_skin'])) {
            $args['skin'] = sanitize_text_field($_GET['tableon_skin']);
        }

        if (isset($args['skin'])) {
            $style = $this->skins->get_theme_css($args['skin'], $table_html_id);
        } else {
            $style = $this->skins->get_theme_css($table_id, $table_html_id);
        }

        if (isset($args['id'])) {
            $style .= tableon()->columns->get_colums_css($table_id, $table_html_id);
            $style .= TABLEON_Settings::get_table_custom_prepared_css($table_id, $table_html_id);
        }

        //***
        //simple shortcode [tableon]
        if (!isset($args['id']) AND!isset($args['columns'])) {
            $args['columns'] = implode(',', $this->default_columns);
        }

        //***

        $args['no_found_text'] = apply_filters('tableon_no_found_text', '', $table_id);

        $classes = apply_filters('tableon_table_classes', $args)['classes'];

        if ($table_id > 0) {
            if (tableon()->columns->options->get($table_id, 'display_cell_info', false)) {
                $classes .= ' tableon-define-display-cell-info';
            }
        }

        if (isset($args['display_cell_info']) AND $args['display_cell_info']) {
            $classes .= ' tableon-define-display-cell-info';
        }

        //+++
        $disable_filter_form = isset($args['disable_filter_form']) ? boolval($args['disable_filter_form']) : ($table_id > 0 ? boolval($this->columns->options->get($table_id, 'disable_filter_form', false)) : FALSE);

        if (isset($args['mode'])) {
            if (in_array($args['mode'], ['json', 'to_json'])) {
                $disable_filter_form = true;
            }
        }

        $filter = NULL;
        if (!$disable_filter_form) {
            $filter = $this->filter->draw_filter_form_data((isset($args['filter_form']) ? $args['filter_form'] : ''), $table_id);
        }

        $args = apply_filters('tableon_review_shortcode_args', $args);

        return TABLEON_HELPER::render_html('views/table.php', array(
                    'table_html_id' => $table_html_id,
                    'table_id' => $table_id,
                    'published' => ($table_id > 0 && $this->tables->get($table_id)) ? $this->tables->get($table_id)['status'] : true,
                    'hide_text_search' => isset($args['hide_text_search']) ? boolval($args['hide_text_search']) : ($table_id > 0 ? !boolval($this->columns->options->get($table_id, 'show_text_search', true)) : false),
                    'skin' => isset($args['skin']) ? strval($args['skin']) : ($table_id > 0 ? strval($this->skins->get($table_id)) : ''),
                    'post_type' => isset($args['post_type']) ? strval($args['post_type']) : ($table_id > 0 ? strval($this->tables->get($table_id)['post_type']) : 'post'),
                    'text_search_min_symbols' => isset($args['text_search_min_symbols']) ? intval($args['text_search_min_symbols']) : ($table_id > 0 ? $this->columns->options->get($table_id, 'text_search_min_symbols', 3) : 0),
                    'placeholder' => isset($args['text_search_placeholder']) ? TABLEON_Vocabulary::get($args['text_search_placeholder']) : ($table_id > 0 ? TABLEON_Vocabulary::get($this->columns->options->get($table_id, 'text_search_placeholder', esc_html__('search by title', 'posts-table-filterable') . ' ...')) : ''),
                    'action' => $table_id > 0 ? TABLEON::get_table_action($table_id) : (isset($args['action']) ? $args['action'] : ''),
                    'classes' => $classes,
                    'filter' => $filter,
                    'has_filter' => isset($args['filter_form']) ? boolval(!empty($args['filter_form'])) : ($table_id > 0 ? boolval(count((array) tableon()->columns->filter->get_acceptor_keys($table_id))) : FALSE),
                    'hide_filter_form' => isset($args['hide_filter_form']) ? boolval($args['hide_filter_form']) : ($table_id > 0 ? boolval($this->columns->options->get($table_id, 'hide_filter_form', false)) : FALSE),
                    'style' => $style,
                    'orderby_select' => TABLEON_Default::get_select_orderby_options(isset($args['orderby_select_fields']) ? $args['orderby_select_fields'] : (($table_id > 0 AND $this->columns->options->get($table_id, 'is_sort_droptdown_shown', 0)) ? $this->columns->options->get($table_id, 'orderby_select_fields', '') : ''), $table_id),
                    'sorting_position' => isset($args['sorting_position']) ? intval($args['sorting_position']) : (($table_id > 0 ? $this->columns->options->get($table_id, 'sorting_position', 'right') : 0)),
                    'table_view' => isset($args['table_view']) ? $args['table_view'] : ''
                )) . $this->draw_table_data($args, $table_html_id);
    }

    /**
     * Shortcode [tableon_button] function
     *
     * @since 1.0.0
     * @param array $args shortcode arguments
     * @return table HTML
     */
    public function do_shortcode_button($args) {
        $this->include_assets();

        $title = '';
        if (isset($args['title'])) {
            $title = TABLEON_Vocabulary::get($args['title']);
            unset($args['title']);
        }

        if (empty($title)) {
            $title = TABLEON_Vocabulary::get(esc_html__('click on me', 'posts-table-filterable'));
        }

        $popup_title = '';
        if (isset($args['popup_title'])) {
            $popup_title = TABLEON_Vocabulary::get($args['popup_title']);
            unset($args['popup_title']);
        }

        $help_title = '';
        if (isset($args['help_title'])) {
            $help_title = TABLEON_Vocabulary::get($args['help_title']);
            $help_title = trim($help_title, '"');
            $help_title = trim($help_title, "'");
            unset($args['help_title']);
        }

        $help_link = '';
        if (isset($args['help_link'])) {
            $help_link = $args['help_link'];
            unset($args['help_link']);
        }

        $class = '';
        if (isset($args['class'])) {
            $class = $args['class'];
            unset($args['class']);
        }

        $args_json = json_encode($args);

        return TABLEON_HELPER::draw_html_item('a', [
                    'href' => "javascript: new Popup23({title: \"{$popup_title}\",help_title: \"{$help_title}\",help_link: \"{$help_link}\",post_id: -1, what: JSON.stringify({$args_json})}); void(0);",
                    'title' => $popup_title,
                    'class' => $class
                        ], $title);
    }

    /**
     * Table action name
     *
     * @since 1.0.0
     * @param int $table_id Table ID
     * @return string Table action
     */
    public static function get_table_action($table_id) {
        $table_action = '';

        //for woocommerce version no sense, but for future ideas left
        if (tableon()->tables->get($table_id)) {
            $table_action = tableon()->tables->get($table_id)['table_action'];
        }

        if (empty($table_action)) {
            $table_action = TABLEON_Default::$action; //default profile
        }

        return $table_action;
    }

    /**
     * Get JSONed table options data and posts data
     *
     * @since 1.0.0
     * @param array $args arguments from shortcodes
     * @param string $table_html_id Table HTML ID
     * @param string $as_script how to return data, if $as_script return wrapped into <div>
     * @return string JSON data or JSON data wrapped into <div> container
     */
    public function draw_table_data($args, $table_html_id, $as_script = 'tableon-table-json-data') {

        $current_page = 0;

        if (isset($args['current_page'])) {
            $current_page = intval($args['current_page']) - 1;
        }

        if (isset($_GET['current_page'])) {
            $current_page = intval($_GET['current_page']) - 1;
        }

        if ($current_page < 0) {
            $current_page = 0;
        }

        //***

        if (isset($args['id'])) {
            if (intval($args['id']) <= 0) {
                unset($args['id']); //fix, such tables can exists
            }
        }

        //***

        if (isset($args['action'])) {

            $profile = apply_filters($args['action'], (isset($args['id']) ? intval($args['id']) : 0), $args);

            //***

            if (isset($args['columns']) AND!empty($args['columns'])) {

                if (is_string($args['columns'])) {
                    $cols = explode(',', $args['columns']);
                } else {
                    $cols = $args['columns'];
                }

                //for human fix in fields naming
                if ($args['action'] === TABLEON_Default::$action) {
                    $cols = array_map(function ($name) {
                        if (isset(TABLEON::$synonyms[$name])) {
                            return TABLEON::$synonyms[$name];
                        }

                        return $name;
                    }, $cols);
                }

                //***

                $new_profile = [];
                $new_profile[0] = $profile[0];

                if (!empty($cols)) {
                    foreach ($cols as $fk) {
                        if (isset($profile[$fk])) {
                            $new_profile[$fk] = $profile[$fk];
                        }
                    }

                    $profile = $new_profile;
                }
            }
        } else {
            if (isset($args['id'])) {
                $tid = intval($args['id']);

                if ($tid > 0) {
                    $action = TABLEON::get_table_action($tid);

                    if (!empty($action)) {
                        $profile = apply_filters($action, $tid);
                        $args['action'] = $action;

                        $cols = $this->columns->get_table_columns($tid, ['fields' => 'title,field_key'], ['is_active' => 1]);

                        if (!empty($cols)) {
                            $custom_profile = [];
                            $custom_profile[0] = $profile[0];
                            foreach ($cols as $key => $c) {
                                $profile[$c['field_key']]['title'] = $c['title'];
                                $custom_profile[$c['field_key']] = $profile[$c['field_key']];
                            }

                            $profile = $custom_profile;
                        } else {
                            return FALSE;
                        }
                    } else {
                        return FALSE;
                    }
                }
            } else {
                return FALSE;
            }
        }


        //***

        $add_data = [
            'post_type' => 'post'
        ];

        if (isset($profile[0])) {
            $add_data = $profile[0];
            unset($profile[0]);
        }

        //***

        $columns = [];
        $orders = [];
        $editable = [];
        $custom_field_keys = []; //we need to save fields with the key another than data-field

        if (!empty($profile) AND is_array($profile)) {
            foreach ($profile as $key => $c) {

                if (!$c) {
                    continue;
                }

                //$columns[$key] = $c['title'];

                if (isset($c['title'])) {
                    $columns[$key] = $c['title'];
                }

                if (isset($c['order']) AND $c['order']) {
                    $orders[$key] = $c['order'];
                }

                if (isset($c['editable']) AND $c['editable']) {
                    $editable[$key] = $c['editable'];
                }

                if (isset($c['custom_field_key']) AND $c['custom_field_key']) {
                    $custom_field_keys[$key] = $c['custom_field_key'];
                }
            }
        }


        //***

        $filter_data = '';
        if (isset($add_data['filter_data'])) {
            $filter_data = json_encode($add_data['filter_data']); //connect for filter plugins
        }

        //shortcode $args has more prioritet
        if (isset($args['filter_data'])) {
            //$filter_data = json_encode($args['filter_data']);
            $filter_data = $args['filter_data'];
        }

        $filter_provider = '';
        if (isset($add_data['filter_provider'])) {
            $filter_provider = $add_data['filter_provider'];
        }

        //$args has more prioritet
        if (isset($args['filter_provider'])) {
            $filter_provider = $args['filter_provider'];
        }

        //***

        $ajax_action = 'tableon_get_table_data';
        if (isset($add_data['ajax_action'])) {
            $ajax_action = $add_data['ajax_action'];
        }


        if (isset($args['ajax_action'])) {
            $ajax_action = $args['ajax_action'];
        }


        //for json mode
        $table_data = [];
        if (isset($args['table_data'])) {
            $table_data = $args['table_data'];
        }

        //***

        $per_page = 10;
        $per_page_position = 'tb';
        $per_page_sel_pp = range(10, 100, 10);
        $per_page_sel_position = 'tb';
        $orderby = 'id';
        $order = 'desc';
        $use_load_more = 0;
        $cells_width = [];
        $hide_on_mobile = [];
        $show_print_button = 0;
        $compact_view_width = -1;

        //constant filtr-independent choice
        $predefinition = [];

        if (isset($args['id'])) {
            $per_page = $this->columns->options->get(intval($args['id']), 'per_page_default', 10);
            $per_page_sel_pp = $this->columns->options->get_per_page_sel_pp(intval($args['id']), true);
            $per_page_position = $this->columns->options->get(intval($args['id']), 'pagination_position', 'tb');
            $per_page_sel_position = $this->columns->options->get(intval($args['id']), 'per_page_sel_position', 'tb');
            $orderby = $this->columns->options->get(intval($args['id']), 'default_orderby', 'id');
            $order = $this->columns->options->get(intval($args['id']), 'default_order', 'desc');
            $use_load_more = $this->columns->options->get(intval($args['id']), 'use_load_more_button', false);
            $show_print_button = $this->columns->options->get(intval($args['id']), 'show_print_button', 0);

            if (!isset($args['compact_view_width'])) {
                $compact_view_width = $this->columns->options->get(intval($args['id']), 'compact_view_width', -1);
            }

            if (!isset($args['use_flow_header'])) {
                $args['use_flow_header'] = $this->columns->options->get(intval($args['id']), 'use_flow_header', 1);
            }

            $predefinition['rules'] = $this->predefinition->get(intval($args['id']));

            if (!isset($args['columns'])) {//columns can be set in shortcode
                $table_columns = $this->columns->get_table_columns(intval($args['id']), [], ['is_active' => 1]);

                if (!empty($table_columns)) {
                    $cells_width = array_map(function ($f) {
                        return $f['width'];
                    }, $table_columns);

                    $columns = [];

                    foreach ($table_columns as $c) {
                        if ($c['is_active']) {
                            $columns[$c['field_key']] = $c['title'];
                        }
                    }


                    $hide_on_mobile = array_values(array_filter(array_map(function ($f) {
                                        if (!$f['show_on_mobile']) {
                                            return $f['field_key'];
                                        }

                                        return FALSE;
                                    }, $table_columns)));
                }
            }

            //***

            $disable_orders = $this->columns->options->get_order_disabled(intval($args['id']));
            if (!empty($disable_orders)) {
                foreach ($disable_orders as $k) {
                    if (isset($orders[$k])) {
                        unset($orders[$k]);
                    }
                }
            }
        }

        //***

        if (isset($args['predefinition'])) {
            $predefinition['rules'] = json_decode($args['predefinition'], true);
        }


        if (isset($args['per_page'])) {
            if (intval($args['per_page']) === -1) {
                $per_page = -1;
            } else {
                $per_page = intval($args['per_page']);
            }
        }

        //***


        if (isset($args['per_page_sel_pp'])) {
            if (intval($args['per_page_sel_pp']) === -1) {
                $per_page_sel_pp = -1;
            } else {
                $per_page_sel_pp = explode(',', $args['per_page_sel_pp']);
            }
        }

        //***

        if (isset($args['per_page_position'])) {
            $per_page_position = trim($args['per_page_position']);
        }

        //***


        if (isset($args['per_page_sel_position'])) {
            $per_page_sel_position = trim($args['per_page_sel_position']);
        }

        //***


        if (isset($args['orderby'])) {
            $orderby = trim($args['orderby']);
            if (!$orderby) {
                $orderby = 'id';
            }
        }


        if (isset($args['order'])) {
            $order = trim($args['order']);

            if (!in_array($order, ['asc', 'desc'])) {
                $order = 'desc';
            }
        }

        if (isset($args['use_load_more'])) {
            $use_load_more = intval($args['use_load_more']);
        }

        if (isset($args['cells_width']) AND!empty($args['cells_width'])) {
            $cells_width = explode(',', $args['cells_width']);
        }

        if (isset($args['hide_on_mobile']) AND!empty($args['hide_on_mobile'])) {
            $hide_on_mobile = explode(',', $args['hide_on_mobile']);
        }

        if (isset($args['author']) AND!empty($args['author'])) {
            $predefinition['author'] = intval($args['author']);
        }

        if (isset($args['show_print_btn'])) {
            $show_print_button = intval($args['show_print_btn']);
        }

        //***

        if (!empty($columns)) {
            foreach ($columns as $key => $value) {
                $columns[$key] = TABLEON_Vocabulary::get($value);
            }
        }

        //***

        if (isset($args['compact_view_width'])) {
            $compact_view_width = intval($args['compact_view_width']);
        }

        //***
        //filter non-existing keys in $columns
        array_walk($columns, function ($title, $key)use (&$profile, &$columns) {
            if (!isset($profile[$key])) {
                unset($columns[$key]);
            } else {
                //fix 16-09-2020
                $columns[$key] = TABLEON_Vocabulary::get($title);
            }
        });
        $get_data = TABLEON_HELPER::sanitize_array($_GET);
        $js_script_data = [
            'mode' => isset($args['mode']) ? $args['mode'] : 'ajax',
            'ajax_url' => admin_url('admin-ajax.php'),
            'selected_lang' => apply_filters('tableon_current_lang', get_locale()),
            'table_data' => $table_data, //for json mode
            'heads' => $columns,
            'hide_on_mobile' => $hide_on_mobile,
            'cells_width' => $cells_width,
            'orders' => $orders,
            'editable' => $editable,
            'custom_field_keys' => $custom_field_keys,
            'total_rows_count' => $per_page,
            'use_load_more' => $use_load_more,
            'css_classes' => isset($args['css_classes']) ? $args['css_classes'] : '',
            'no_found_text' => isset($args['no_found_text']) ? $args['no_found_text'] : '',
            'show_print_btn' => $show_print_button,
            'post_id' => isset($args['post_id']) ? intval($args['post_id']) : 0, //for some program cases
            'compact_view_width' => $compact_view_width,
            'stop_notice' => isset($args['stop_notice']) ? $args['stop_notice'] : '',
            'use_flow_header' => isset($args['use_flow_header']) ? intval($args['use_flow_header']) : 1,
            'style' => isset($args['style']) ? $args['style'] : '',
            'skin' => isset($args['skin']) ? $args['skin'] : '',
            'pagination' => [
                'position' => $per_page_position, //t,b,tb,none
                'next' => [
                    'class' => 'tableon-btn',
                    'content' => apply_filters('tableon_lang_load_next', '&gt;')
                ],
                'prev' => [
                    'class' => 'tableon-btn',
                    'content' => apply_filters('tableon_lang_load_prev', '&lt;')
                ],
                'input' => [
                    'class' => 'tableon-form-control'
                ],
            ],
            'lang' => [
                'loading' => TABLEON_Vocabulary::get(esc_html__('Loading ...', 'posts-table-filterable')),
                'no_data' => TABLEON_Vocabulary::get(esc_html__('No Data!', 'posts-table-filterable')),
                'error' => TABLEON_Vocabulary::get(esc_html__('Error!', 'posts-table-filterable')),
                'items' => TABLEON_Vocabulary::get(esc_html__('Posts', 'posts-table-filterable')),
                'page' => TABLEON_Vocabulary::get(esc_html__('Page', 'posts-table-filterable')),
                'pages' => TABLEON_Vocabulary::get(esc_html__('Pages', 'posts-table-filterable')),
                'load_more' => apply_filters('tableon_lang_load_more', TABLEON_Vocabulary::get(esc_html__('Load More', 'posts-table-filterable')))],
            'per_page_sel_position' => $per_page_sel_position, //t,b,tb,none
            'per_page_sel_pp' => $per_page_sel_pp,
            'print_css_url' => TABLEON_ASSETS_LINK . 'css/print.css',
            'request_data' => [
                'action' => $ajax_action,
                'fields' => array_keys($columns),
                'post_type' => isset($add_data['post_type']) ? $add_data['post_type'] : '',
                'wp_columns_actions' => isset($args['action']) ? $args['action'] : TABLEON_Default::$action,
                'table_id' => isset($args['id']) ? intval($args['id']) : 0,
                'predefinition' => serialize($predefinition),
                'filter_data' => $filter_data,
                'filter_provider' => $filter_provider,
                'orderby' => $orderby,
                'order' => $order,
                'per_page' => $per_page,
                'current_page' => $current_page,
                'shortcode_args_set' => serialize($args),
                'tableon_link_get_data' => json_encode($get_data)
            ]
        ];

        //special param, not for customers
        if (isset($args['not_load_on_init'])) {
            $js_script_data['not_load_on_init'] = 1;
        }

        if (isset($args['mode']) AND isset($args['is_front']) AND $args['mode'] === 'json') {
            $js_script_data['table_data'] = $this->get_table_data(true, array_merge($js_script_data['request_data']));
        }

        if ($as_script) {
            return "<div class='{$as_script}' data-table-id='{$table_html_id}' style='display: none;'>" . json_encode($js_script_data, JSON_HEX_QUOT | JSON_HEX_TAG) . '</div>';
        }

        return json_encode($js_script_data);
    }

    /**
     * Main query function, as by ajax so and direct call
     *
     * @since 1.0.0
     * @param boolean $return return or output
     * @param array $request_data Table requested arguments
     * @return array or string
     */
    public function get_table_data($return = false, $request_data = []) {
        $posts = [];

        if (empty($request_data)) {
            if (!empty($_REQUEST)) {//ajax
                foreach ($_REQUEST as $key => $value) {
                    $request_data[$key] = TABLEON_HELPER::sanitize_array($value);
                }
            }
        }

        $post_type = 'post';
        if (!empty($request_data['post_type'])) {
            $post_type = sanitize_key($request_data['post_type']);
        }

        $posts_per_page = 10;
        if (isset($request_data['per_page'])) {
            $posts_per_page = intval($request_data['per_page']);
        }

        if (!isset($request_data['current_page'])) {
            $request_data['current_page'] = 0;
        }

        $orderby = sanitize_key($request_data['orderby']);
        $args = array(
            'post_type' => $post_type,
            'post_status' => 'publish',
            'posts_per_page' => $posts_per_page,
            'orderby' => $orderby,
            'order' => sanitize_key($request_data['order']),
            'paged' => intval($request_data['current_page']) + 1
        );

        //***

        $table_id = 0;
        if (isset($request_data['table_id'])) {
            $table_id = intval($request_data['table_id']);
        }

        $shortcode_args_set = [];
        if (isset($request_data['shortcode_args_set'])) {
            $shortcode_args_set = unserialize((stripslashes($request_data['shortcode_args_set'])));

            //get all posts, be care with memory overflow
            if (isset($shortcode_args_set['mode']) AND ($shortcode_args_set['mode'] === 'to_json' OR $shortcode_args_set['mode'] === 'json')) {
                $args['posts_per_page'] = -1;
            }
        }


        if (!isset($request_data['wp_columns_actions'])) {
            $request_data['wp_columns_actions'] = TABLEON_Default::$action;
        }

        $profile = apply_filters($request_data['wp_columns_actions'], $table_id, $shortcode_args_set);

        if (!empty($profile)) {
            if (isset($profile[$request_data['orderby']])) {
                $orderby_field = $profile[$request_data['orderby']];
                if (isset($orderby_field['meta_key'])) {
                    $args['orderby'] = $orderby_field['orderby'];
                    $orderby = $args['meta_key'] = $orderby_field['meta_key'];
                }
            }

            //***

            if (isset($profile[0]['post_statuses'])) {
                $args['post_status'] = explode(',', $profile[0]['post_statuses']);
            } else {
                $args['post_status'] = 'publish';
            }
        }

        //***
        $filter_data = $request_data['filter_data'];
        if (!is_array($filter_data)) {
            //means transmitted by ajax
            $filter_data = json_decode(html_entity_decode(stripslashes($request_data['filter_data'])), ARRAY_N);
        }

        $filter_provider = trim($request_data['filter_provider']);

        //***

        if (!isset($args['tax_query'])) {
            $args['tax_query'] = [];
        }

        $args['tableon_text_search_by'] = [];
        if (isset($shortcode_args_set['tableon_text_search_by']) AND!empty($shortcode_args_set['tableon_text_search_by'])) {
            $tableon_text_search = explode(',', $shortcode_args_set['tableon_text_search_by']);
            $args['tableon_text_search_by'] = array_filter($tableon_text_search, function ($val) {
                return in_array($val, ['post_title', 'post_content', 'post_excerpt']);
            });
        } else {
            $args['tableon_text_search_by'][] = 'post_title'; //by default
        }

        //***
        $limit_found_posts = -1;
        if (isset($request_data['predefinition'])) {
            $predefinition = unserialize((stripslashes($request_data['predefinition'])));

            //fix 16-09-2020 [tableon author=2]
            if (isset($predefinition['author'])) {
                if (!isset($predefinition['rules'])) {
                    $predefinition['rules'] = [];
                }

                $predefinition['rules']['authors'] = strval(intval($predefinition['author']));
            }

            if (!empty($predefinition) AND isset($predefinition['rules'])) {
                foreach ($predefinition['rules'] as $key => $value) {
                    if (!empty($value)) {
                        if (intval($value) !== -1) {
                            switch ($key) {
                                case 'ids':

                                    $posts_ids = explode(',', $value);
                                    if (!empty($posts_ids) AND is_array($posts_ids)) {
                                        $args['post__in'] = array_map(function ($id) {
                                            return intval($id);
                                        }, $posts_ids);
                                    }

                                    break;

                                case 'ids_exclude':

                                    $posts_ids = explode(',', $value);
                                    if (!empty($posts_ids) AND is_array($posts_ids)) {
                                        $args['post__not_in'] = array_map(function ($id) {
                                            return intval($id);
                                        }, $posts_ids);
                                    }

                                    break;

                                case 'authors':
                                    $value = explode(',', $value);
                                    if (!empty($value) AND is_array($value)) {
                                        $args['author__in'] = $value;
                                    }
                                    break;

                                case 'by_taxonomy':
                                    $args = $this->predefinition->get_by_taxonomy($args, $value);
                                    break;
                                case 'not_by_taxonomy':
                                    $args = $this->predefinition->get_by_taxonomy($args, $value, 'NOT IN');
                                    break;
                            }
                        }
                    }
                }
            }
        }

        //+++

        $this->filter->provider($filter_data);
        if (!empty($filter_provider)) {
            //here we can include any filter plugins
            $args = apply_filters("tableon_filter_provider_{$filter_provider}", $args, $filter_data, $shortcode_args_set);
        }

        //+++
        //fix for ordering by, as not all works trought WP_Query arguments
        $posts_wp_hook = '';
        $posts_wp_hook_filter = '';
        switch ($args['orderby']) {
            case 'post_status':
                $posts_wp_hook = 'posts_orderby';

                $posts_wp_hook_filter = function () use ($args) {
                    return "post_status {$args['order']}";
                };

                add_filter($posts_wp_hook, $posts_wp_hook_filter);

                break;

            default:
                break;
        }

        //***

        if ($args['orderby'] === 'id') {
            //hot and important fix!!
            $args['orderby'] = strtoupper($args['orderby']);
        }

        //***
        //multi-lang fix
        if (isset($request_data['lang'])) {
            $args['lang'] = apply_filters('tableon_current_lang', $request_data['lang']);
        } else {
            $args['lang'] = apply_filters('tableon_current_lang', get_locale());
        }

        //***

        if (isset($args['meta_query']) AND!empty($args['meta_query'])) {
            if (!isset($args['meta_query']['relation'])) {
                $args['meta_query'] = array_merge(['relation' => 'AND'], $args['meta_query']);
            }
        }

        $query = new WP_Query(apply_filters('tableon_wp_query_args', $args, $table_id));

        if ($limit_found_posts > 0) {
            //for bestsellers and newest predefinitions
            $query->found_posts = $limit_found_posts;
        }

        if (!empty($posts_wp_hook)) {
            remove_filter($posts_wp_hook, $posts_wp_hook_filter);
        }

        //***

        if (is_array($request_data['fields'])) {
            $fields = $request_data['fields'];
        } else {
            $fields = explode(',', trim($request_data['fields']));
        }

        //***

        if (!empty($fields) AND!empty($query)) {
            foreach ($query->posts as $post) {
                $tmp = [];
                $post_id = $post->ID;
                $tmp['pid'] = $post_id; //VERY IMPORTANT AS IT POST ID IN THE TABLES CELLS ACTIONS

                foreach ($fields as $field) {
                    if (isset($profile[$field]['action'])) {
                        $tmp[$field] = $profile[$field]['action']($post_id);
                    }
                }

                $posts[] = $tmp;
            }
        }


        //***

        if ($return) {
            return ['rows' => $posts, 'count' => $query->found_posts];
        }

        die(json_encode(['rows' => $posts, 'count' => $query->found_posts]));
    }

    /**
     * Ajax actions for popup
     *
     * @since 1.0.0
     *
     * @return output the string
     */
    public function get_smth() {
        $res = '';
        $what = TABLEON_HELPER::sanitize_text(strip_tags($_REQUEST['what']));
        $post_id = 0;

        //***

        if (isset($_REQUEST['post_id']) AND intval($_REQUEST['post_id']) === -1) {
            $post_id = intval($_REQUEST['post_id']);
        }

        if ($post_id === -1) {
            //done such because shortcode can has diff arguments and no table_id
            $shortcode_button_args = $what;
            $what = 'shortcode_button';
        }

        //***

        switch ($what) {
            case 'excerpt':
                //disabled
                $res = apply_filters('tableon_filter_smth_excerpt', apply_filters('the_content', get_the_excerpt($post_id)), $what);
                break;

            case 'content':
                //disabled
                $res = apply_filters('tableon_filter_smth_content', apply_filters('the_content', get_the_content(null, false, $post_id)), $what);
                break;

            case 'shortcodes_help':
                //disabled
                $res = TABLEON_HELPER::render_html('views/shortcodes_help.php');
                break;

            case 'shortcode_button':
                $shortcode_button_args = json_decode(html_entity_decode(stripslashes($shortcode_button_args)), ARRAY_A);
                $res = $this->do_shortcode($shortcode_button_args);
                break;

            case 'shortcodes_set':
                $res = do_shortcode(TABLEON_HELPER::sanitize_array($_REQUEST['shortcodes_set']));
                break;

            case 'export':
                $data = [];
                $data['tableon_tables'] = $this->tables->gets();
                $data['tableon_tables_columns'] = $this->columns->gets();
                $data['tableon_tables_meta'] = $this->columns->meta->gets();
                $data['tableon_vocabulary'] = $this->vocabulary->gets();
                $data['tableon_settings'] = get_option('tableon_settings', []);

                if ($data['tableon_settings'] AND!is_array($data['tableon_settings'])) {
                    $data['tableon_settings'] = json_decode($data['tableon_settings'], true);
                }

                $data['tableon_mime_types_association'] = get_option('tableon_mime_types_association', []);
                if ($data['tableon_mime_types_association'] AND!is_array($data['tableon_mime_types_association'])) {
                    $data['tableon_mime_types_association'] = json_decode($data['tableon_mime_types_association'], true);
                }

                $res = TABLEON_HELPER::draw_html_item('textarea', [
                            'readonly' => 'readonly',
                            'style' => 'width: 100%; height: 500px',
                                ], json_encode($data));

                break;

            case 'import':

                $res = '<div class="tableon-notice">' . esc_html__('ATTENTION! All existed TABLEON data will be wiped!', 'posts-table-filterable') . '</div>';
                $res .= TABLEON_HELPER::draw_html_item('textarea', [
                            'autofocus' => '',
                            'id' => 'tableon-import-text',
                            'style' => 'width: 100%; height: 300px',
                                ], '') . '<br />' . TABLEON_HELPER::draw_html_item('a', [
                            'href' => 'javascript: tableon_import_options();void(0);',
                            'class' => 'button tableon-dash-btn',
                                ], '<span class="dashicons-before dashicons-arrow-up-alt"></span>&nbsp;' . esc_html__('Import', 'posts-table-filterable'));

                break;

            case 'possible_columns_keys':

                $res = '<h2>' . esc_html__('Columns keys', 'posts-table-filterable') . '</h2>';
                $res .= '<ul>';
                $profile = apply_filters(TABLEON_Default::$action, 0);

                foreach ($profile as $key => $value) {
                    if (!$key) {
                        continue;
                    }

                    $res .= "<li><b>{$key}</b>: {$value['title']}</li>";
                }

                $res .= '</ul><br />';

                $res .= '<h2>' . esc_html__('Order by keys', 'posts-table-filterable') . '</h2>';
                $res .= '<ul>';
                foreach ($this->columns->options->get_orderby_fields(0) as $key => $value) {
                    $res .= "<li><b>{$key}</b>: {$value}</li>";
                }

                $res .= '</ul><br />';

                $res .= '<h2>' . esc_html__('Filter keys', 'posts-table-filterable') . '</h2>';
                $res .= '<ul>';
                foreach ($profile as $key => $value) {

                    if (isset($value['filter']) AND $value['filter']) {
                        $res .= "<li><b>{$key}</b>: {$value['title']}</li>";
                    }
                }

                $res .= '</ul><br />';

                break;

            default:
                $what = TABLEON_HELPER::sanitize_array(json_decode(stripslashes($_REQUEST['what']), true));

                if (isset($what['call_action'])) {
                    $res = apply_filters($what['call_action'], $what['more_data'], TABLEON_HELPER::sanitize_text($_REQUEST['call_id']));
                }

                break;
        }

        die('<div class="tableon-content-in-popup">' . $res . '</div>');
    }

    /**
     * Adds to system blank page template for creating iframes for remote pages and pages in popup
     *
     * @since 1.0.0
     *
     * @return string template name
     */
    public function blank_page_init() {

        $blank_template = 'blank-template.php';

        //Add our template(s) to the dropdown in the admin
        add_filter('theme_page_templates', function (array $templates) use ($blank_template) {
            $templates[$blank_template] = esc_html__('TABLEON Blank Page', 'posts-table-filterable');
            return $templates;
        });

        //Ensure our template is loaded on the front end
        add_filter('template_include', function ($template) use ($blank_template) {
            if (is_singular()) {

                $assigned_template = trim(get_post_meta(get_the_ID(), '_wp_page_template', true));

                if ($blank_template === $assigned_template ? $assigned_template : null) {

                    if (file_exists($assigned_template)) {
                        return $assigned_template;
                    }

                    $file = wp_normalize_path(TABLEON_PATH . 'views/' . $assigned_template);

                    if (file_exists($file)) {
                        return $file;
                    }
                }
            }

            return $template;
        });
    }

    /**
     * Get a field options of table $table_id
     *
     * @since 1.0.0
     * @param int $table_id table ID
     * @param string $field_key field key
     * @return array one field(column) options
     */
    public function get_field_options($table_id, $field_key) {
        $options = $this->columns->get_by_field_key($table_id, $field_key)['options'];

        if (!empty($options)) {
            $options = json_decode($options, true);
        } else {
            $options = [];
        }

        return $options;
    }

    /**
     * For set of tables in the popup
     *
     * @since 1.0.0
     * @param array $args shortcode arguments
     * @return string generated HTML
     */
    public function do_set_button($args) {

        if (empty($args['title'])) {
            $title = TABLEON_Vocabulary::get(esc_html__('click me', 'posts-table-filterable'));
        } else {
            $title = TABLEON_Vocabulary::get($args['title']);
        }

        $popup_title = '';
        if (isset($args['popup_title'])) {
            $popup_title = TABLEON_Vocabulary::get($args['popup_title']);
        }

        $class = '';
        if (isset($args['class'])) {
            $class = $args['class'];
        }

        return TABLEON_HELPER::draw_html_item('a', [
                    'href' => "javascript: new Popup23({title: \"{$popup_title}\", shortcodes_set: \"{$args['shortcode']}\"}); void(0);",
                    'title' => $popup_title,
                    'class' => $class
                        ], $title);
    }

    /**
     * For creating set of tables
     *
     * @since 1.0.0
     * @param array $shortcodes shortcodes
     * @param string $view_path path to view
     * @return string generated HTML
     */
    public function do_set($shortcodes, $view_path = '') {
        if (!empty($shortcodes)) {
            foreach ($shortcodes as $k => $sh) {
                $shortcodes[$k]['content'] = do_shortcode($sh['content']);
            }
        }

        if (empty($view_path)) {
            return TABLEON_HELPER::render_html('views/set.php', ['shortcodes' => $shortcodes]);
        }

        return TABLEON_HELPER::render_html($view_path, ['shortcodes' => $shortcodes], FALSE);
    }

    /**
     * The plugin data import
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function import_data() {
        if (TABLEON_HELPER::can_manage_data()) {
            if (!empty($_REQUEST['data'])) {
                $data = TABLEON_HELPER::sanitize_array(json_decode(stripslashes($_REQUEST['data']), true));

                if (json_last_error() == JSON_ERROR_NONE AND is_array($data)) {
                    foreach ($data as $key => $value) {
                        switch ($key) {
                            case 'tableon_tables':
                                $this->tables->import($value);
                                break;

                            case 'tableon_tables_columns':
                                $this->columns->import($value);
                                break;

                            case 'tableon_tables_meta':
                                $this->columns->meta->import($value);
                                break;

                            case 'tableon_vocabulary':
                                $this->vocabulary->import($value);
                                break;

                            case 'tableon_settings':
                            case 'tableon_mime_types_association':
                                update_option($key, $value);
                                break;
                        }
                    }
                }
            }
        }

        die('done');
    }

}

//***

$GLOBALS['TABLEON'] = new TABLEON();

function tableon() {
    global $TABLEON;
    return $TABLEON;
}

add_action('init', array(tableon(), 'init'), 9999);
add_action('plugins_loaded', array(tableon(), 'blank_page_init'));

/**
 * Remote js action for iframe
 *
 * @since 1.0.0
 *
 * @return output generated HTML
 */
add_action('wp_loaded', function () {
    //Example: https://site.com/?tableon-remote-page=__PAGE_SLUG__&width=800&height=600&anchor=__CONTAINER_HTML_ID__
    //Example: https://site.com/tableon-remote.php?tableon-remote-page=__PAGE_SLUG__&width=800&height=600&anchor=__CONTAINER_HTML_ID__
    $get_data = TABLEON_HELPER::sanitize_array($_GET);
    if (isset($get_data['tableon-remote-page']) AND isset($get_data['anchor'])) {
        ?>
        var tableon_remote_src = "<?php esc_attr_e(home_url($get_data['tableon-remote-page'])) ?>";
        var tableon_remote_width = '<?php esc_attr_e(isset($get_data['width']) ? urldecode($get_data['width']) : '100%') ?>';
        var tableon_remote_height = <?php esc_attr_e(isset($get_data['height']) ? intval($get_data['height']) : 500) ?>;
        var tableon_remote_anchor = "#<?php esc_attr_e(strip_tags(strval($get_data['anchor']))) ?>";
        <?php
        echo 'var tableon_link_get_data=' . json_encode($get_data) . ';';
        die(TABLEON_HELPER::render_html('assets/js/remote.js'));
    }
});

//Special shortcodes

add_shortcode('tableon_popup_iframe_button', function ($args) {
    wp_enqueue_script('tableon-helper', TABLEON_ASSETS_LINK . 'js/helper.js', [], TABLEON_VERSION, true);
    wp_enqueue_script('popup-23', TABLEON_ASSETS_LINK . 'js/popup-23.js', ['tableon-helper'], TABLEON_VERSION, true);
    wp_enqueue_style('tableon-popup-23', TABLEON_ASSETS_LINK . 'css/popup-23.css', [], TABLEON_VERSION);
    wp_enqueue_style('tableon-general', TABLEON_ASSETS_LINK . 'css/general.css', [], TABLEON_VERSION);

    $popup_page_link = '';
    if (isset($args['popup_page_link'])) {
        $popup_page_link = $args['popup_page_link'];
    }

    $button_text = esc_html__('Posts in popup', 'posts-table-filterable') . ': ';
    if (isset($args['button_text'])) {
        $button_text = $args['button_text'];
    }

    $popup_title = '';
    if (isset($args['popup_title'])) {
        $popup_title = $args['popup_title'];
    }

    $css_class = '';
    if (isset($args['css_class'])) {
        $css_class = $args['css_class'];
    }

    $help_title = '';
    if (isset($args['help_title'])) {
        $help_title = TABLEON_Vocabulary::get($args['help_title']);
        $help_title = trim($help_title, '"');
        $help_title = trim($help_title, "'");
        unset($args['help_title']);
    }

    $help_link = '';
    if (isset($args['help_link'])) {
        $help_link = $args['help_link'];
        unset($args['help_link']);
    }

    return TABLEON_HELPER::draw_html_item('a', [
        'href' => "javascript: new Popup23({iframe:\"{$popup_page_link}\", title:\"{$popup_title}\", style:\"height: 100vh\",help_title:\"{$help_title}\",help_link:\"{$help_link}\"});void(0);",
        'class' => $css_class
            ], $button_text);
});

//+++

add_shortcode('tableon_gallery', function ($args) {

    $args = (array) $args;

    $post_id = 0;

    if (isset($args['id'])) {
        $post_id = intval($args['id']);
    }

    if (!$post_id) {
        global $post;
        $post_id = $post->ID;
    }

    if ($post_id > 0) {
        wp_enqueue_script('tableon-general', TABLEON_ASSETS_LINK . 'js/general.js', [], TABLEON_VERSION, true);
        wp_enqueue_style('tableon-gallery', TABLEON_ASSETS_LINK . 'css/gallery.css', [], TABLEON_VERSION);
        wp_enqueue_style('tableon-general', TABLEON_ASSETS_LINK . 'css/general.css', [], TABLEON_VERSION);

        $post = TABLEON_Default::get_post($post_id);

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
                $uniqid = uniqid();
                $data = [
                    'img_data' => $img_data,
                    'uniqid' => $uniqid,
                    'is_shortcode' => true
                ];

                return TABLEON_HELPER::render_html('views/gallery.php', apply_filters('tableon_print_plugin_options', $data));
            } else {
                return '';
            }
        }
    }
});

add_shortcode('tableon_single_btn', function ($args) {

    $args = (array) $args;

    $post_id = 0;

    if (isset($args['id'])) {
        $post_id = intval($args['id']);
    }

    if (!$post_id) {
        global $post;
        if (is_object($post)) {
            $post_id = $post->ID;
        }
    }

    if ($post_id > 0) {
        tableon()->include_assets();

        $title = esc_html__('Post', 'posts-table-filterable') . ': ';
        $title .= addslashes(get_post_field('post_title', $post_id));
        $unique_id = uniqid('gp');

        $button_text = '<i class="tableon-icon">&#xf1c6;</i>';
        if (isset($args['button_text'])) {
            $button_text = $args['button_text'];
        }

        $css_class = 'tableon-btn tableon-btn-1';
        if (isset($args['css_class'])) {
            $css_class = $args['css_class'];
        }

        $columns = '';
        if (isset($args['columns'])) {
            $columns = $args['columns'];
        }

        $help_title = '';
        if (isset($args['help_title'])) {
            $help_title = TABLEON_Vocabulary::get($args['help_title']);
            $help_title = trim($help_title, '"');
            $help_title = trim($help_title, "'");
            unset($args['help_title']);
        }

        $help_link = '';
        if (isset($args['help_link'])) {
            $help_link = $args['help_link'];
            unset($args['help_link']);
        }

        $skin = '';
        if (isset($args) AND isset($args['skin'])) {
            $skin = $args['skin'];
        }

        return TABLEON_HELPER::draw_html_item('a', array(
                    'href' => "javascript: tableon_helper.call_popup(\"tableon_get_table_single_post\",{post_id: {$post_id}, skin: \"{$skin}\", columns:\"{$columns}\", not_paste:1}, \"{$unique_id}\", \"{$title}\", {help_title: \"{$help_title}\",help_link: \"{$help_link}\"}); void(0);",
                    'title' => $title,
                    'class' => $css_class
                        ), $button_text);
    }
});

