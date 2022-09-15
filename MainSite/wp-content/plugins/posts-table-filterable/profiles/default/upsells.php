<?php

/**
 * Shortcode [tableon_upsells]
 *
 * Generates a post upsells posts HTML table
 *
 * @see https://posts-table.com/shortcode/tableon_upsells/
 * @since   1.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
//as an example here for future features ideas
final class TABLEON_Upsells extends TABLEON_Universal {

    public $slug = 'upsells';

    public function __construct() {
        $this->settings_columns_title = esc_html__('Upsells columns', 'posts-table-filterable');
        parent::__construct();
    }

    /**
     * Set filtration arguments for the current table
     *
     * @since 1.0.0
     * @param array $args wp_query arguments
     * @param array $filter_data current filtration data
     * @param array $shortcode_args arguments from shortcode
     * @return array wp_query arguments
     */
    public function filter_provider($args, $filter_data, $shortcode_args) {
        //[tableon_button filter_provider="tableon_upsells" post_id=49 mode="to_json"]
        if (isset($shortcode_args['post_id'])) {
            $filter_data['post_id'] = intval($shortcode_args['post_id']);
        }

        //+++

        if (is_array($filter_data) AND isset($filter_data['post_id']) AND intval($filter_data['post_id']) > 0) {
            $post = TABLEON_Default::get_post(intval($filter_data['post_id']));

            if ($post AND method_exists($post, 'get_upsell_ids')) {
                if (!$args['post__in'] = $post->get_upsell_ids()) {
                    $args['post__in'] = [-1];
                }
            } else {
                $args['post__in'] = [-1];
            }
        }

        return $args;
    }

}

new TABLEON_Upsells();
