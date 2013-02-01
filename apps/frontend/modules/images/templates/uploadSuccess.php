<?php
use_helper('Ajax', 'Form', 'Javascript', 'MyForm', 'Escaping', 'General');

$validation     = sfConfig::get('app_images_validation');
?>
<div id="image_upload">
<p class="tips">
<?php
echo __('You can add %1%, with %3% x %2% px and %4% mo', 
              array('%1%' => implode(', ', $validation['file_extensions']), 
                    '%2%' => $validation['max_size']['height'],
                    '%3%' => $validation['max_size']['width'],
                    '%4%' => $validation['weight'] / pow(1024, 2)))
    . ' ' .
    __('Minsize is %1% x %2%', array('%1%' => $validation['min_size']['height'], '%2%' => $validation['min_size']['width']));
?>
</p>
<p class="mandatory_fields_warning"><?php echo __('All fields are mandatory') ?></p>
<?php
echo global_form_errors_tag();

echo form_tag('images/upload?mod=' . $sf_params->get('mod') . '&document_id=' . $sf_params->get('document_id'),
              array('multipart' => true));
//echo input_hidden_tag('MAX_FILE_SIZE', 2 * $validation['weight']);
?>
  <div id="files_to_upload">
    <?php include_partial('file_form', array('image_number' => 0, 'default_license' => $default_license == null ? 2 : $default_license)) ?>
  </div>
  <p><?php echo picto_tag('picto_add') ?> <a href="javascript:void(0)" id="add_file_link"><?php echo __('add an other file') ?></a></p>
  <?php
  echo submit_tag(__('save'), array('id' => 'submit_files', ));

  echo ajax_feedback(true);

  echo javascript_tag("
  var next_file_id = 1;

  $('submit_files').observe('click', function() {
      $('indicator').show();
  });

  $('add_file_link').observe('click', function() {
      new_fields = '" . 
      addcslashes(get_partial('file_form', array('image_number' => 'next_file_id_var', 'default_license' => $default_license == null ? 2 : $default_license)), "\0..\37\\'\"\/") .
      "';

      new_fields = new_fields.gsub('next_file_id_var', next_file_id);
      new Insertion.Bottom('files_to_upload', new_fields);
      next_file_id++;
  });
  ");

  ?>
  </p>
</form>
