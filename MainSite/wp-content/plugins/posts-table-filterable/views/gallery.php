<?php
/**
 * Template for generating gallery HTML
 * 
 * @see https://posts-table.com/codex/
 * @version 1.0.0
 */
if (!defined('ABSPATH'))
    die('No direct access allowed');

if (!isset($uniqid)) {
    $uniqid = uniqid();
}

if (!isset($is_shortcode)) {
    $is_shortcode = false;
}
?>

<div <?php if ($is_shortcode): ?>class="tableon-gallery-container"<?php endif; ?> id="tableon-gallery-container-<?php esc_attr_e($uniqid) ?>" <?php if ($is_shortcode): ?>style="display: none;"<?php endif; ?>>
    <div class="tableon-gallery tableon-gallery-cell" id="tableon-gallery-<?php esc_attr_e($uniqid) ?>">
        <?php foreach ($img_data as $counter => $img) : ?>
            <div class="tableon-gallery-nav"><img src="<?php esc_attr_e($img['thumb']) ?>" loading="lazy" alt="" /><a href="#tableon-gallery-lightbox-<?php esc_attr_e($uniqid) ?>-<?php esc_attr_e($counter) ?>">&nbsp;</a></div>
        <?php endforeach; ?>
    </div>


    <?php foreach ($img_data as $counter => $img) : ?>
        <div class="tableon-gallery-lightbox" id="tableon-gallery-lightbox-<?php esc_attr_e($uniqid) ?>-<?php esc_attr_e($counter) ?>">

            <?php if (isset($img_data[$counter - 1])): ?>
                <div class="tableon-gallery-nav  tableon-gallery-nav-left"><a href="#tableon-gallery-lightbox-<?php esc_attr_e($uniqid) ?>-<?php esc_attr_e($counter - 1) ?>"><img src="<?php esc_attr_e($img_data[$counter - 1]['thumb']) ?>" loading="lazy" width="50" alt="" /></a></div>
            <?php endif; ?>

            &nbsp;<div class="tableon-gallery-content"><img src="<?php esc_attr_e($img['original']) ?>" loading="auto" alt="" />           
                <div class="tableon-gallery-title"><?php esc_attr_e($img['title']) ?></div>
                <a class="tableon-gallery-close" href="#/tableon-gallery-<?php esc_attr_e($uniqid) ?>"></a>
            </div>&nbsp;

            <?php if (isset($img_data[$counter + 1])): ?>
                <div class="tableon-gallery-nav tableon-gallery-nav-right"><a href="#tableon-gallery-lightbox-<?php esc_attr_e($uniqid) ?>-<?php esc_attr_e($counter + 1) ?>"><img src="<?php esc_attr_e($img_data[$counter + 1]['thumb']) ?>" loading="lazy" width="50" alt="" /></a></div>
                    <?php endif; ?>

        </div>
    <?php endforeach; ?>
</div>
<?php if ($is_shortcode): ?>

    <script>
        //stop image blinking while loading
        setTimeout(function () {
            document.getElementById('tableon-gallery-container-<?php esc_attr_e($uniqid) ?>').classList.add('tableon-gallery-container-active')
        }, 777);
    </script>

<?php endif; ?>
