<?php
/**
 * Page of the plugin options
 * 
 * @see https://posts-table.com/tableon-documentation/
 * @version 1.0.0
 */
if (!defined('ABSPATH'))
    die('No direct access allowed');
?>

<div class="tableon-admin-preloader"> 
    <div class="cssload-loader">
        <div class="cssload-inner cssload-one"></div>
        <div class="cssload-inner cssload-two"></div>
        <div class="cssload-inner cssload-three"></div>
    </div>
</div>

<svg class="hidden">
<defs>
<path id="tabshape" d="M80,60C34,53.5,64.417,0,0,0v60H80z"/>
</defs>
</svg>

<?php tableon()->rate_alert->show_alert() ?>

<div class="wrap nosubsub tableon-options-wrapper">



    <h2 class="tableon-plugin-name"><?php printf(esc_html__('TableOn - Posts Tables Filterable v.%s', 'posts-table-filterable'), TABLEON_VERSION) ?></h2>
    <i><?php printf(esc_html__('Actualized for WordPress v.%s', 'posts-table-filterable'), get_bloginfo('version')) ?></i><br />
    <br />


    <div class="tableon-tabs tableon-tabs-style-shape">

        <nav>
            <ul>

                <li class="tab-current">
                    <a href="#tabs-main-tables">
                        <svg viewBox="0 0 80 60" preserveAspectRatio="none"><use xlink:href="#tabshape"></use></svg>
                        <span><?php esc_html_e('Tables', 'posts-table-filterable') ?></span>
                    </a>
                </li>


                <li>
                    <a href="#tabs-main-settings">
                        <svg viewBox="0 0 80 60" preserveAspectRatio="none"><use xlink:href="#tabshape"></use></svg>
                        <svg viewBox="0 0 80 60" preserveAspectRatio="none"><use xlink:href="#tabshape"></use></svg>
                        <span><?php esc_html_e('Settings', 'posts-table-filterable') ?></span>
                    </a>
                </li>

                <?php if (TABLEON_Vocabulary::is_enabled()): ?>
                    <li>
                        <a href="#tabs-main-vocabulary">
                            <svg viewBox="0 0 80 60" preserveAspectRatio="none"><use xlink:href="#tabshape"></use></svg>
                            <svg viewBox="0 0 80 60" preserveAspectRatio="none"><use xlink:href="#tabshape"></use></svg>
                            <span><?php esc_html_e('Vocabulary', 'posts-table-filterable') ?></span>
                        </a>
                    </li>
                <?php endif; ?>

                <li>
                    <a href="#tabs-main-help">
                        <svg viewBox="0 0 80 60" preserveAspectRatio="none"><use xlink:href="#tabshape"></use></svg>
                        <svg viewBox="0 0 80 60" preserveAspectRatio="none"><use xlink:href="#tabshape"></use></svg>
                        <span><?php esc_html_e('Help', 'posts-table-filterable') ?></span>
                    </a>
                </li>

            </ul>
        </nav>

        <div class="content-wrap">
            <section id="tabs-main-tables" class="content-current">

                <div style="float: left;">
                    <?php
                    echo TABLEON_HELPER::draw_html_item('a', [
                        'href' => 'javascript: tableon_main_table.create();void(0);',
                        'class' => 'button tableon-dash-btn'
                            ], '<span class="dashicons-before dashicons-plus"></span>&nbsp;' . esc_html__('Create table', 'posts-table-filterable'));
                    ?>
                </div>
                <?php if (false): ?>
                    <div style="float: right;">
                        <?php
                        echo TABLEON_HELPER::draw_html_item('a', [
                            'href' => 'https://posts-table.com/upgrading-to-premium/',
                            'target' => '_blank',
                            'class' => 'button tableon-dash-btn',
                            'style' => 'border-color: tomato; font-size: 14px !important; line-height: 33px;',
                                ], '<span class="dashicons-before dashicons-arrow-up-alt"></span>&nbsp;' . esc_html__('Upgrade to Premium', 'posts-table-filterable'));
                        ?>
                    </div>
                <?php endif; ?>

                <div class="clearfix"></div>

                <br />

                <?php
                echo wp_kses($main_table, [
                    'div' => [
                        'class' => true,
                        'id' => true,
                        'data-skin' => true,
                        'data-table-id' => true,
                        'style' => true
                    ],
                    'input' => [
                        'type' => true,
                        'data-key' => true,
                        'value' => true,
                        'minlength' => true,
                        'class' => true,
                        'placeholder' => true
                    ],
                    'table' => [
                        'class' => true,
                        'id' => true
                    ],
                    'thead' => [],
                    'tfoot' => [],
                    'tbody' => [
                        'style' => true,
                    ],
                    'th' => [
                        'data-key' => true,
                        'style' => true,
                        'class' => true
                    ],
                    'tr' => [
                        'data-pid' => true
                    ],
                    'td' => [
                        'class' => true,
                        'data-field-type' => true,
                        'data-pid' => true,
                        'data-key' => true,
                        'data-field' => true
                    ],
                ]);
                ?>

            </section>

            <section id="tabs-main-settings">

                <?php
                echo wp_kses($settings_table, [
                    'div' => [
                        'class' => true,
                        'id' => true,
                        'data-skin' => true,
                        'data-table-id' => true,
                        'style' => true
                    ],
                    'input' => [
                        'type' => true,
                        'data-key' => true,
                        'value' => true,
                        'minlength' => true,
                        'class' => true,
                        'placeholder' => true
                    ],
                    'table' => [
                        'class' => true,
                        'id' => true
                    ],
                    'thead' => [],
                    'tfoot' => [],
                    'tbody' => [
                        'style' => true,
                    ],
                    'th' => [
                        'data-key' => true,
                        'style' => true,
                        'class' => true
                    ],
                    'tr' => [
                        'data-pid' => true
                    ],
                    'td' => [
                        'class' => true,
                        'data-field-type' => true,
                        'data-pid' => true,
                        'data-key' => true,
                        'data-field' => true
                    ],
                ]);
                ?>

                <hr />

                <a href='javascript: new Popup23({title: "<?php echo esc_html__('Info data: All possible columns keys for [tableon] shortcode attributes', 'posts-table-filterable') ?>", what: "possible_columns_keys"}); void(0);' class="tableon-btn"><?php echo esc_html__('All possible columns keys', 'posts-table-filterable') ?></a>&nbsp;
                <a href='javascript: new Popup23({title: "<?php echo esc_html__('Export TABLEON Data', 'posts-table-filterable') ?>", what: "export"}); void(0);' class="tableon-btn"><?php echo esc_html__('Export TABLEON Data', 'posts-table-filterable') ?></a>&nbsp;
                <a href='javascript: new Popup23({title: "<?php echo esc_html__('Import TABLEON Data', 'posts-table-filterable') ?>", what: "import"}); void(0);' class="tableon-btn"><?php echo esc_html__('Import TABLEON Data', 'posts-table-filterable') ?></a>

            </section>

            <?php if (TABLEON_Vocabulary::is_enabled()): ?>
                <section id="tabs-main-vocabulary">

                    <div class="tableon-notice">
                        <?php
                        printf(esc_html__('This vocabulary is not for interface words, which you can translate for example by %s, but for the arbitrary words which you applied in the tables columns. Taxonomies terms also possible to translate here, to display them in the TableOn tables.', 'posts-table-filterable'), TABLEON_HELPER::draw_html_item('a', [
                                    'href' => 'https://wordpress.org/plugins/loco-translate/',
                                    'target' => '_blank'
                                        ], 'Loco Translate'))
                        ?>
                    </div>
                    <?php
                    echo TABLEON_HELPER::draw_html_item('a', [
                        'href' => 'javascript: tableon_vocabulary_table.create();void(0);',
                        'class' => 'button tableon-dash-btn'
                            ], '<span class="dashicons-before dashicons-plus"></span>&nbsp;' . esc_html__('Create', 'posts-table-filterable'));
                    ?>

                    <br /><br />
                    <?php tableon()->vocabulary->draw_table(); ?>
                    <div class="clearfix"></div>
                </section>
            <?php endif; ?>


            <section id="tabs-main-help">

                <ul>

                    <li>
                        <?php
                        echo TABLEON_HELPER::draw_html_item('a', [
                            'href' => 'https://posts-table.com/shortcode/tableon/',
                            'target' => '_blank',
                            'class' => 'tableon-btn'
                                ], '[tableon]')
                        ?>&nbsp;
                        <?php
                        echo TABLEON_HELPER::draw_html_item('a', [
                            'href' => 'https://posts-table.com/tableon-documentation/',
                            'target' => '_blank',
                            'class' => 'tableon-btn'
                                ], esc_html__('Documentation', 'posts-table-filterable'))
                        ?>&nbsp;
                        <?php
                        echo TABLEON_HELPER::draw_html_item('a', [
                            'href' => 'https://posts-table.com/category/faq/',
                            'target' => '_blank',
                            'class' => 'tableon-btn'
                                ], esc_html__('FAQ', 'posts-table-filterable'))
                        ?>&nbsp;
                        <?php
                        echo TABLEON_HELPER::draw_html_item('a', [
                            'href' => 'https://posts-table.com/codex/',
                            'target' => '_blank',
                            'class' => 'tableon-btn'
                                ], esc_html__('Codex', 'posts-table-filterable'))
                        ?>&nbsp;
                        <!-- <?php
                        echo TABLEON_HELPER::draw_html_item('a', [
                            'href' => 'https://posts-table.com/video-tutorials/',
                            'target' => '_blank',
                            'class' => 'tableon-btn'
                                ], esc_html__('Video', 'posts-table-filterable'))
                        ?>&nbsp; -->
                        <?php
                        echo TABLEON_HELPER::draw_html_item('a', [
                            'href' => 'https://posts-table.com/document/skins/',
                            'target' => '_blank',
                            'class' => 'tableon-btn'
                                ], esc_html__('Make skins', 'posts-table-filterable'))
                        ?>&nbsp;
                        <?php
                        echo TABLEON_HELPER::draw_html_item('a', [
                            'href' => 'https://demo.posts-table.com/',
                            'target' => '_blank',
                            'class' => 'tableon-btn'
                                ], esc_html__('Demo', 'posts-table-filterable'))
                        ?>&nbsp;
                        <?php
                        echo TABLEON_HELPER::draw_html_item('a', [
                            'href' => 'https://pluginus.net/support/forum/tableon-posts-table-filterable/',
                            'target' => '_blank',
                            'class' => 'tableon-btn'
                                ], esc_html__('Support', 'posts-table-filterable'))
                        ?>&nbsp;
                    </li>

                    <li>
                        <hr />
                        <h3><?php echo esc_html__('Extensions for TABLEON', 'posts-table-filterable') ?>:</h3>
                    </li>

                    <li>
                        <?php
                        echo TABLEON_HELPER::draw_html_item('a', [
                            'href' => 'https://posts-table.com/extension/favourites/',
                            'target' => '_blank',
                            'class' => 'tableon-btn'
                                ], esc_html__('Favourites', 'posts-table-filterable'))
                        ?>&nbsp;<?php
                        echo TABLEON_HELPER::draw_html_item('a', [
                            'href' => 'https://posts-table.com/extension/compare/',
                            'target' => '_blank',
                            'class' => 'tableon-btn'
                                ], esc_html__('Compare', 'posts-table-filterable'))
                        ?>&nbsp;<?php
                        echo TABLEON_HELPER::draw_html_item('a', [
                            'href' => 'https://posts-table.com/extension/attachments/',
                            'target' => '_blank',
                            'class' => 'tableon-btn'
                                ], esc_html__('Attachments', 'posts-table-filterable'))
                        ?>
                    </li>


                </ul>
                <hr />

                <!-- <iframe width="560" height="315" src="https://www.youtube.com/embed/4f1wyApG68Y" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe> -->

                <h3><?php echo esc_html__('Power your site', 'posts-table-filterable') ?>:</h3>

                <div>
                    <a href="https://bulk-editor.pro/" title="WPBE - WordPress Posts Bulk Editor Professional" target="_blank"><img src="<?php echo TABLEON_ASSETS_LINK ?>img/banners/wpbe-banner.png" width="250" alt="WPBE - WordPress Posts Bulk Editor Professional"></a>
                    &nbsp;<a href="https://wordpress.currency-switcher.com/" title="WPCS - WordPress Currency Switcher" target="_blank"><img src="<?php echo TABLEON_ASSETS_LINK ?>img/banners/wpcs-banner.jpg" width="250" alt="WPCS - WordPress Currency Switcher"></a>
                </div>

                <h3><?php echo esc_html__('Power your woocommerce store', 'posts-table-filterable') ?>:</h3>

                <div>
                    <a href="https://products-filter.com/" title="WOOF - WooCommerce Products Filter" target="_blank"><img src="<?php echo TABLEON_ASSETS_LINK ?>img/banners/woof-banner.jpg" width="250" alt="WOOF - WooCommerce Products Filter"></a>
                    &nbsp;<a href="https://currency-switcher.com/" title="WOOCS - WooCommerce Currency Switcher" target="_blank"><img src="<?php echo TABLEON_ASSETS_LINK ?>img/banners/woocs-banner.jpg" width="250" alt="WOOCS - WooCommerce Currency Switcher"></a>
                    &nbsp;<a href="https://bulk-editor.com/" title="WOOBE - WooCommerce Bulk Editor and Products Manager Professional" target="_blank"><img src="<?php echo TABLEON_ASSETS_LINK ?>img/banners/woobe-banner.jpg" width="250" alt="WOOBE - WooCommerce Bulk Editor and Products Manager Professional"></a>
                    &nbsp;<a href="https://products-tables.com/" title="WOOT - WooCommerce Products Tables Professional" target="_blank"><img src="<?php echo TABLEON_ASSETS_LINK ?>img/banners/woot-banner.png" width="250" alt="WOOT - WooCommerce Products Tables Professional"></a>
                </div>



            </section>


        </div>


    </div>

    <div id="tableon-popup-columns-template" style="display: none;">

        <div class="tableon-modal">
            <div class="tableon-modal-inner">
                <div class="tableon-modal-inner-header">
                    <h3 class="tableon-modal-title">&nbsp;</h3>

                    <div class="tableon-modal-title-info"><a href="https://posts-table.com/document/columns/" id="main-table-help-link" class="tableon-btn" target="_blank"><?php echo esc_html__('Help', 'posts-table-filterable') ?></a></div>

                    <a href="javascript: tableon_columns_table.close_popup(); void(0)" class="tableon-modal-close"></a>


                </div>
                <div class="tableon-modal-inner-content">
                    <div class="tableon-form-element-container">

                        <div class="tableon-tabs tableon-tabs-style-shape">

                            <nav>
                                <ul>

                                    <li class="tab-current">
                                        <a href="#tabs-columns">
                                            <svg viewBox="0 0 80 60" preserveAspectRatio="none"><use xlink:href="#tabshape"></use></svg>
                                            <span><?php esc_html_e('Columns', 'posts-table-filterable') ?></span>
                                        </a>
                                    </li>


                                    <li>
                                        <a href="#tabs-meta">
                                            <svg viewBox="0 0 80 60" preserveAspectRatio="none"><use xlink:href="#tabshape"></use></svg>
                                            <svg viewBox="0 0 80 60" preserveAspectRatio="none"><use xlink:href="#tabshape"></use></svg>
                                            <span><?php esc_html_e('Meta', 'posts-table-filterable') ?></span>
                                        </a>
                                    </li>



                                    <li>
                                        <a href="#tabs-filter">
                                            <svg viewBox="0 0 80 60" preserveAspectRatio="none"><use xlink:href="#tabshape"></use></svg>
                                            <svg viewBox="0 0 80 60" preserveAspectRatio="none"><use xlink:href="#tabshape"></use></svg>
                                            <span><?php esc_html_e('Filter', 'posts-table-filterable') ?></span>
                                        </a>
                                    </li>


                                    <li>
                                        <a href="#tabs-predefinition">
                                            <svg viewBox="0 0 80 60" preserveAspectRatio="none"><use xlink:href="#tabshape"></use></svg>
                                            <svg viewBox="0 0 80 60" preserveAspectRatio="none"><use xlink:href="#tabshape"></use></svg>
                                            <span><?php esc_html_e('Predefinition', 'posts-table-filterable') ?></span>
                                        </a>
                                    </li>


                                    <li>
                                        <a href="#tabs-custom-css">
                                            <svg viewBox="0 0 80 60" preserveAspectRatio="none"><use xlink:href="#tabshape"></use></svg>
                                            <svg viewBox="0 0 80 60" preserveAspectRatio="none"><use xlink:href="#tabshape"></use></svg>
                                            <span><?php esc_html_e('Custom CSS', 'posts-table-filterable') ?></span>
                                        </a>
                                    </li>


                                    <li>
                                        <a href="#tabs-options">
                                            <svg viewBox="0 0 80 60" preserveAspectRatio="none"><use xlink:href="#tabshape"></use></svg>
                                            <svg viewBox="0 0 80 60" preserveAspectRatio="none"><use xlink:href="#tabshape"></use></svg>
                                            <span><?php esc_html_e('Options', 'posts-table-filterable') ?></span>
                                        </a>
                                    </li>


                                </ul>
                            </nav>

                            <div class="content-wrap">
                                <section id="tabs-columns" class="content-current">

                                    <div>
                                        <?php
                                        echo TABLEON_HELPER::draw_html_item('a', [
                                            'href' => 'javascript: tableon_columns_table.create();void(0);',
                                            'class' => 'button tableon-dash-btn'
                                                ], '<span class="dashicons-before dashicons-welcome-add-page"></span>' . esc_html__('Prepend column', 'posts-table-filterable'));
                                        ?>
                                    </div>
                                    <br />

                                    <div class="tableon-columns-table-zone"></div>

                                    <br />

                                    <div>
                                        <?php
                                        echo TABLEON_HELPER::draw_html_item('a', [
                                            'href' => 'javascript: tableon_columns_table.create(false);void(0);',
                                            'class' => 'button tableon-dash-btn tableon-dash-btn-rotate'
                                                ], '<span class="dashicons-before dashicons-welcome-add-page"></span>' . esc_html__('Append column', 'posts-table-filterable'));
                                        ?>
                                    </div>

                                </section>

                                <section id="tabs-custom-css">

                                    <table style="width: 100%;">
                                        <tr>
                                            <td style="width: 1px; padding-left: 4px;">
                                                <a href="javascript: tableon_main_table.save_custom_css(); void(0)" class="tableon-btn tableon-btn-1"><i class="tableon-icon">&#xe801;</i></a>
                                            </td>

                                            <td>
                                                <div class="tableon-notice"><?php
                                                    printf(esc_html__('You can use custom CSS for small changes, but for quite big the table restyling its recommended to use %s. Use hotkey combination CTRL+S for CSS code saving!', 'posts-table-filterable'), TABLEON_HELPER::draw_html_item('a', [
                                                                'href' => 'https://posts-table.com/document/skins/',
                                                                'target' => '_blank'
                                                                    ], esc_html__('table skins', 'posts-table-filterable')))
                                                    ?></div>
                                            </td>

                                        </tr>
                                    </table>

                                    <div class="tableon-options-custom-css-zone"></div>                                    

                                </section>

                                <section id="tabs-options">
                                    <div class="tableon-table-options-zone"></div>
                                </section>


                                <section id="tabs-meta">

                                    <div class="tableon-notice">
                                        <?php
                                        printf(esc_html__('If to use sorting by meta keys - will be visible only posts which has any value for the selected meta key. %s', 'posts-table-filterable'), TABLEON_HELPER::draw_html_item('a', [
                                                    'href' => 'https://posts-table.com/sort-on-meta-value-but-include-posts-that-do-not-have-one/',
                                                    'target' => '_blank'
                                                        ], esc_html__('Read more here', 'posts-table-filterable')))
                                        ?>
                                    </div>

                                    <?php
                                    echo TABLEON_HELPER::draw_html_item('a', [
                                        'href' => 'javascript: tableon_meta_table.create();void(0);',
                                        'class' => 'button tableon-dash-btn'
                                            ], '<span class="dashicons-before dashicons-plus"></span>' . esc_html__('Add meta field', 'posts-table-filterable'));
                                    ?>
                                    <br /><br />

                                    <div class="tableon-meta-table-zone"></div>
                                </section>

                                <section id="tabs-filter">
                                    <p class="notice notice-success"><?php
                                        printf(esc_html__('Also for posts filtration you can use %s filter!', 'posts-table-filterable'), TABLEON_HELPER::draw_html_item('a', [
                                                    'href' => 'https://wp-filter.com',
                                                    'target' => '_blank'
                                                        ], 'MDTF'))
                                        ?></p>


                                    <div class="tabs-filter-container"></div>
                                </section>

                                <section id="tabs-predefinition">
                                    <div class="tableon-notice"><?php
                                        printf(esc_html__('Here you can set rules about what posts to display in the table. The filtration will work with the predefined posts as with basic ones. %s.', 'posts-table-filterable'), TABLEON_HELPER::draw_html_item('a', [
                                                    'href' => 'https://posts-table.com/document/predefinition/',
                                                    'target' => '_blank'
                                                        ], esc_html__('Read more here', 'posts-table-filterable')))
                                        ?></div>
                                    <div class="tableon-predefinition-table-zone"></div>
                                </section>
                            </div>

                        </div>


                    </div>
                </div>
                <div class="tableon-modal-inner-footer">
                    <a href="javascript: tableon_columns_table.close_popup(); void(0)" class="button button-primary tableon-modal-button-large-1"><?php esc_html_e('Close', 'posts-table-filterable') ?></a>
                    <!-- <a href="javascript:void(0)" class="tableon-modal-save button button-primary button-large-2"><?php esc_html_e('Apply', 'posts-table-filterable') ?></a>-->
                </div>
            </div>
        </div>

        <div class="tableon-modal-backdrop"></div>

    </div>

    <?php echo TABLEON_HELPER::render_html('views/popup.php'); ?>


</div>

