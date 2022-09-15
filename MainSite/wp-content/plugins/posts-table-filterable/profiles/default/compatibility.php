<?php

/**
 * Compatibility with posts filters
 * *
 * @see https://posts-table.com/hook/tableon_filter_provider_/
 * @since   1.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

add_filter("tableon_profile_extend", function($profile, $action, $shortcode_args) {

    if ('tableon_default_tables' === $action) {

        if (isset($shortcode_args['filter_provider'])) {

            if ($shortcode_args['filter_provider'] === 'mdtf') {
                if (isset($_REQUEST['page_mdf']) AND!empty($_REQUEST['page_mdf'])) {
                    $profile[0]['filter_provider'] = 'mdtf';

                    $_GLOBALS['MDF_META_DATA_FILTER'] = TABLEON_HELPER::sanitize_array(json_decode(base64_decode($_REQUEST['page_mdf']), true));

                    do_shortcode('[meta_data_filter_results]');
                    $args = TABLEON_HELPER::sanitize_array($_REQUEST['meta_data_filter_args']);
                    $args['posts_per_page'] = -1;
                    $args['fields'] = 'ids';

                    if (isset($args['meta_query'])) {
                        if (!empty($args['meta_query'])) {
                            foreach ($args['meta_query'] as $key => $m) {
                                if (isset($m['key'])) {
                                    if ($m['key'] === 'mdf_hide_post') {
                                        unset($args['meta_query'][$key]);
                                    }
                                }
                            }
                        }
                    }


                    if (isset($args['ignore_sticky_posts'])) {
                        unset($args['ignore_sticky_posts']);
                    }

                    unset($args['orderby']);
                    unset($args['order']);

                    //***
                    $profile[0]['filter_data'] = [];
                    $profile[0]['filter_data']['ids'] = (new WP_Query($args))->posts;
                }

                //***

                add_action('tableon_filter_provider_mdtf', function ($args, $filter_data) {

                    if (!is_array($filter_data)) {
                        $filter_data = json_decode($filter_data, true);
                    }

                    if (isset($filter_data['post_title']) AND!empty($filter_data['post_title'])) {
                        tableon()->filter->provider($filter_data);
                    }

                    if (!empty($filter_data['ids'])) {
                        $args['post__in'] = $filter_data['ids'];
                    } else {
                        //$args['post__in'] = [-1];
                    }

                    return $args;
                }, 10, 2);
            }
        }
    }

    return $profile;
}, 10, 3);

