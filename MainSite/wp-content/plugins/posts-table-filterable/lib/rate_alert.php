<?php

/**
 * TABLEON Rate Alert
 *
 * Handles alert about the plugin review on the admin panel
 *
 * @since   1.0.0
 */
//delete_option('tableon_manage_rate_alert');//for tests
class TABLEON_RATE_ALERT {

    protected $notes_for_free = true;
    private $show_after_time = 86400 * 2;
    private $meta_key = 'tableon_manage_rate_alert';

    public function __construct($for_free) {
        $this->notes_for_free = $for_free;
        add_action('wp_ajax_tableon_manage_alert', array($this, 'manage_alert'));
    }

    /**
     * Get fixed time
     *
     * @since 1.0.0
     * 
     * @return int
     */
    private function get_time() {
        $time = intval(get_option($this->meta_key, -1));

        if ($time === -1) {
            add_option($this->meta_key, time());
            $time = time();
        }

        if ($time === -2) {
            $time = time(); //user already set review
        }

        return $time;
    }

    /**
     * Show review alert on the plugin admin panel
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function show_alert() {
        $show = false;

        if (($this->get_time() + $this->show_after_time) <= time()) {
            $show = true;
        }

        //***

        if ($show) {
            if (isset($_GET['page']) AND $_GET['page'] == 'tableon') {
                $support_link = 'https://pluginus.net/support/forum/tableon-posts-table-filterable/';
                ?>
                <div id="tableon-rate-alert">
                    <p>
                        <?php printf("Hi, looks like you using <b>TableOn - Posts Tables Filterable</b> for some time and I hope this software helped you with your business. If you satisfied with the plugin functionality, could you please give us BIG favor and give it a 5-star rating to help us spread the word and boost our motivation?<br /><br /><strong>~ PluginUs.NET developers team</strong>", "<a href='{$support_link}' target='_blank'>" . __('support', 'posts-table-filterable') . "</a>") ?>
                    </p>

                    <hr />

                    <?php
                    $link = 'https://wordpress.org/support/plugin/posts-table-filterable/reviews/#new-post';
              
                    ?>

                    <table>
                        <tr>
                            <td>
                                <a href="javascript: tableon_manage_alert(0);void(0);" class="button button-large dashicons-before dashicons-clock">&nbsp;<?php echo __('Nope, maybe later!', 'posts-table-filterable') ?></a>
                            </td>

                            <td>
                                <a href="<?php esc_attr_e($link) ?>" target="_blank" class="tableon-panel-button dashicons-before dashicons-star-filled">&nbsp;<?php echo __('Ok, you deserve it', 'posts-table-filterable') ?></a>
                            </td>

                            <td>
                                <a href="javascript: tableon_manage_alert(1);void(0);" class="button button-large dashicons-before dashicons-thumbs-up">&nbsp;<?php echo __('Thank you, I did it!', 'posts-table-filterable') ?></a>
                            </td>
                        </tr>
                    </table>


                </div>
                <script>
                    function tableon_manage_alert(value) {
                        //1 - did it, 0 - later
                        jQuery('#tableon-rate-alert').hide(333);
                        jQuery.post(ajaxurl, {
                            action: "tableon_manage_alert",
                            value: value
                        }, function (data) {
                            console.log(data);
                        });
                    }
                </script>

                <?php
            }
        }
    }

    /**
     * Fixing of customer action
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function manage_alert() {

        if (intval($_REQUEST['value'])) {
            update_option($this->meta_key, -2);
        } else {
            update_option($this->meta_key, time());
        }

        die('Thank you!');
    }

}
