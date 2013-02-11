<?php
use_helper('Javascript');
$lang = $sf_user->getCulture();

if ($debug) {
    include_partial('documents/map_lib_include_debug');
    use_javascript('/static/js/carto/config/config.js', 'maps');
    use_javascript('/static/js/carto/config/base.js', 'maps');
} else {
    use_stylesheet('/static/js/carto/build/app.css', 'last');
    use_javascript('/static/js/carto/build/app.js', 'maps');
}
use_stylesheet('/static/js/carto/viewer.css', 'last'); // TODO: build CSS
use_javascript('/static/js/carto/build/lang-fr.js', 'maps'); // TODO: $lang
use_javascript('/static/js/carto/viewer.js', 'maps');

include_partial('documents/map_i18n');
?>

<div id="mapPort">
  <div id="mapLoading"><img src="<?php echo $app_static_url ?>/static/images/indicator.gif" alt="" /> <?php echo __('Map is loading...') ?></div>
</div>
