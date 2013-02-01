<?php
use_helper('Object', 'Language', 'Validation', 'MyForm');

// Here document = hut
echo '<div>';
display_document_edit_hidden_tags($document);
echo '</div>';
echo mandatory_fields_warning();

include_partial('documents/language_field', array('document'     => $document,
                                                  'new_document' => $new_document));
echo object_group_tag($document, 'name', null, '', array('class' => 'long_input'));

echo form_section_title('Information', 'form_info', 'preview_info');

include_partial('documents/oam_coords', array('document' => $document));
echo object_group_tag($document, 'elevation', null, 'meters', array('class' => 'short_input', 'type' => 'number'));
echo object_group_dropdown_tag($document, 'shelter_type', 'mod_huts_shelter_types_list');
echo object_group_tag($document, 'is_staffed', 'object_checkbox_tag');
echo object_group_tag($document, 'phone', null, '', array('class' => 'long_input', 'type' => 'tel'));
echo object_group_tag($document, 'url', null, '', array('class' => 'long_input', 'type' => 'url'));
echo object_group_tag($document, 'staffed_capacity', null, '', array('class' => 'short_input'));
echo object_group_tag($document, 'unstaffed_capacity', null, '', array('class' => 'short_input'));
echo object_group_tag($document, 'has_unstaffed_matress', 'object_checkbox_tag');
echo object_group_tag($document, 'has_unstaffed_blanket', 'object_checkbox_tag');
echo object_group_tag($document, 'has_unstaffed_gas', 'object_checkbox_tag');
echo object_group_tag($document, 'has_unstaffed_wood', 'object_checkbox_tag');
echo object_group_dropdown_tag($document, 'activities', 'app_activities_list',
                               array('multiple' => true), false, null, null, '', '', 'picto_act act_');

echo form_section_title('Description', 'form_desc', 'preview_desc');

echo object_group_tag($document, 'staffed_period', null, '', array('class' => 'long_input'));
echo object_group_bbcode_tag($document, 'description', null, array('class' => 'mediumtext'));
echo object_group_bbcode_tag($document, 'pedestrian_access', null, array('class' => 'mediumtext'));

include_partial('documents/form_history');
?>
