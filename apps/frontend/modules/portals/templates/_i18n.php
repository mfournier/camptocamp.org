<?php
use_helper('Field'); 

$is_archive = $document->isArchive();
if  (!isset($preview))
{
    $preview = false;
}
if ($is_archive || $preview)
{
    echo field_text_data_if_set($document, 'abstract', null, array('needs_translation' => $needs_translation, 'show_images' => false));
}
echo field_text_data_if_set($document, 'description', null, array('needs_translation' => $needs_translation, 'images' => $images, 'show_label' => $is_archive));
