<?php 
use_helper('Button', 'Ajax', 'Javascript');

$module = $sf_context->getModuleName();
$lang = $document->getCulture();
$is_connected = $sf_user->isConnected();
$is_archive = $document->isArchive();
$has_rights = $sf_user->hasCredential('moderator');
?>
<nav id="nav_tools" class="nav_box">
    <div id="nav_tools_top"></div>
    <div id="nav_tools_content">
        <ul>
            <?php if ($is_connected): ?>
                <li><?php echo button_back($module) ?></li>
            <?php endif ?>
            <li><?php echo button_search($module) ?></li>
            <li><?php echo button_mail($id); ?></li>
            <?php if ($is_connected): ?>
                <li><?php echo button_pm($id) ?></li>
                <?php if ($sf_user->hasCredential('admin')): ?>
                    <li><?php echo button_profile($id) ?></li>
                <?php endif ?>
                <?php if ($has_rights): ?>
                    <li><?php echo button_delete_culture($module, $id, $document->get('culture')) ?></li>
                <?php endif ?>
                <?php if ($has_rights && $document->get('geom_wkt')): ?>
                    <li><?php echo button_delete_geom($module, $id) ?></li>
                <?php endif ?>
                <?php if ($has_rights && !$is_archive): ?>
                    <li><?php echo button_clear_cache($module, $id) ?></li>
                    <li><?php echo button_associations_history($module, $id) ?></li>
                <?php endif ?>
            <?php endif ?>
            <li><?php echo button_report() ?></li>
            <li><?php echo button_help('help') ?></li>
        </ul>
    </div>
    <div id="nav_tools_down"></div>
</nav>
