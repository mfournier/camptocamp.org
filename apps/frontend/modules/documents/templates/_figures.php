<?php
if (!isset($default_open))
{
    $default_open = true;
}
?>
<div id="nav_figures" class="nav_box">
    <div class="nav_box_top"></div>
    <div class="nav_box_content">
        <?php echo nav_title('figures',  __('Camptocamp.org is about:'), 'info'); ?>
        <div class="nav_box_text" id="nav_figures_section_container">
            <ul>
            <?php foreach ($figures as $type => $nb): ?>
                <li><?php echo $nb . ' ' . __($type) ?></li>
            <?php endforeach; ?>
            </ul>
        </div>
        <?php
        $cookie_position = array_search('nav_figures', sfConfig::get('app_personalization_cookie_fold_positions'));
        echo javascript_tag('setHomeFolderStatus(\'nav_figures\', '.$cookie_position.', '.((!$default_open) ? 'false' : 'true').");");
        ?>
    </div>
    <div class="nav_box_down"></div>
</div>
