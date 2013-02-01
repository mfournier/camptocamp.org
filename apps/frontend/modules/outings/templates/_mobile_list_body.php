<?php
use_helper('Date', 'General', 'Field', 'Link');

if (strlen($item['geom_wkt']))
{
    $has_gps_track = picto_tag('action_gps', __('has GPS track'));
}
else
{
    $has_gps_track = '';
}
$item_i18n = $item['OutingI18n'][0];
$activities = $item['activities'];
?>
<div class="right"><?php echo get_paginated_activities($activities) ?></div>
<div><?php
echo list_link($item_i18n, 'outings')
     . ' ' . $has_gps_track ?></div>
<div>
<?php echo _implode(' - ', array(format_date($item['date'], 'D'),
                                 link_to($item['creator'], '@document_by_id?module=users&id=' . $item['creator_id']))); ?></div>
<div>
<?php
echo _implode(' - ',
              array(displayWithSuffix($item['max_elevation'], 'meters'),
                    displayWithSuffix($item['height_diff_up'], 'meters'),
                    (isset($item['linked_routes'])) ? field_route_ratings_data($item, false, false, false, 'html', $activities) : '',
                    get_paginated_value($item['conditions_status'], 'mod_outings_conditions_statuses_list'),
                    field_frequentation_picto_if_set($item, true))); ?></div>
<div><?php include_partial('documents/regions4list', array('geoassociations' => $item['geoassociations']))?></div>
<div><?php echo picto_tag('picto_images', __('nb_linked_images')), ' ', (isset($item['nb_images'])) ?  $item['nb_images'] : '0', ' ',
                picto_tag('action_comment', __('nb_comments')), ' ', (isset($item['nb_comments'])) ?
                    link_to($item['nb_comments'], '@document_comment?module=outings&id='
                    . $item_i18n['id'] . '&lang=' . $item_i18n['culture']) : '0'; ?></div>
