<?php
/**
 * Template for generating HTML structure of some tables in the tabs
 * 
 * @see https://posts-table.com/codex/
 * @version 1.0.0
 */

if (!defined('ABSPATH'))
    die('No direct access allowed');

$ids = [];
?>

<?php if (!empty($shortcodes)): ?>
    <div class="tableon-tables-set">
        <!------------------------ tabs -------------------------------->
        <?php foreach ($shortcodes as $c => $sh): $ids[$c] = uniqid('t') ?>
            <a onclick="tableon_show_tab(event, '<?php esc_attr_e($ids[$c]) ?>')" data-tab-id="<?php esc_attr_e($ids[$c]) ?>" class="tableon-btn tableon-tab-link <?php if ($c === 0): ?>tableon-tab-link-current<?php endif; ?>" href="javascript: void(0);"><?php esc_attr_e($sh['title']) ?></a>
        <?php endforeach; ?>

        <!------------------------ content ----------------------------->
        <?php foreach ($shortcodes as $c => $sh): ?>
            <div id="<?php esc_attr_e($ids[$c]) ?>" class="tableon-tab-content <?php if ($c === 0): ?>tableon-tab-content-current<?php else: ?>tableon-tab-content-hidden<?php endif; ?>"><?php esc_attr_e($sh['content']) ?></div>
        <?php endforeach; ?>

    </div>
<?php endif; ?>