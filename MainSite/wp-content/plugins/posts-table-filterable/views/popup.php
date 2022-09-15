<?php
/**
 * Template for generating popup HTML
 * 
 * @see https://posts-table.com/codex/
 * @version 1.0.0
 */
if (!defined('ABSPATH'))
    die('No direct access allowed');
?>

<template id="tableon-popup-template">

    <div class="tableon-modal">
        <div class="tableon-modal-inner">
            <div class="tableon-modal-inner-header">
                <h3 class="tableon-modal-title">&nbsp;</h3>
                <div class="tableon-modal-title-info">&nbsp;</div>
                <a href="javascript: void(0);" class="tableon-modal-close"></a>
            </div>
            <div class="tableon-modal-inner-content">
                <div class="tableon-form-element-container"><div class="table23-place-loader"><?php echo TABLEON_Vocabulary::get(esc_html__('Loading ...', 'posts-table-filterable')) ?></div><br /></div>
            </div>
            <div class="tableon-modal-inner-footer">
                <a href="javascript: void(0);" class="<?php if (is_admin()): ?>button button-primary <?php endif; ?>tableon-btn tableon-modal-button-large-1"><?php esc_html_e('Close', 'posts-table-filterable') ?></a>
            </div>
        </div>
    </div>

    <div class="tableon-modal-backdrop"></div>

</template>

