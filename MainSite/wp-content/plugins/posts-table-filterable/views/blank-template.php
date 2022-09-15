<?php
/**
 * Template for creating blank pages and further using in TABLEON iframes or/and in TABLEON popups
 * 
 * @see https://posts-table.com/how-to-create-remote-page-with-the-posts-table/
 * @version 1.0.0
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="profile" href="http://gmpg.org/xfn/11" />

        <?php if (!get_theme_support('title-tag')) : ?>
            <title><?php wp_title(); ?></title>
        <?php endif; ?>

        <?php wp_head(); ?>
    </head>

    <body <?php body_class('tableon-blank-page'); ?>>

        <?php
        while (have_posts()) {
            the_post();
            the_content();
        }
        ?>

        <?php
        tableon()->include_assets();
        wp_footer();
        ?>

        <style>
            .wpml-ls-statics-footer{
                display: none;
            }
        </style>

    </body>
</html>
