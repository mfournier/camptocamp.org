<?php
use_helper('FilterForm', 'General');

if (!c2cTools::mobileVersion())
{
   // put focus on the name field on dom load
   echo javascript_tag('if (!("autofocus" in document.createElement("input"))) {
   document.observe(\'dom:loaded\', function() { $(\'mnam\').focus(); })};');
}

$ranges_raw = $sf_data->getRaw('ranges');
$selected_areas_raw = $sf_data->getRaw('selected_areas');
include_partial('areas/areas_selector', array('ranges' => $ranges_raw, 'selected_areas' => $selected_areas_raw, 'use_personalization' => true));
?>
<br />
<br />
<?php
echo picto_tag('picto_maps') . __('Name:') . ' ' . input_tag('mnam', null, array('autofocus' => 'autofocus'));
echo __('Code:') . ' ' . input_tag('code');
?>
<br />
<?php
echo __('Scale:') . ' ' . field_value_selector('scal', 'mod_maps_scales_list', true);
echo __('Editor:') . ' ' . field_value_selector('edit', 'mod_maps_editors_list', true);
?>
<br />
<?php
echo __('filter language') . __('&nbsp;:') . ' ' . lang_selector('mcult');
include_partial('documents/filter_sort');
