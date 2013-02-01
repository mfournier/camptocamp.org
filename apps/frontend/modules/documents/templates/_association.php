<?php
use_helper('AutoComplete', 'General', 'Field');

$has_associated_docs = count($associated_docs);
$has_extra_docs = (isset($extra_docs) && check_not_empty($extra_docs));
if (isset($document))
{
    $id = $document->get('id');
}
if (!isset($show_link_to_delete))
{
    $show_link_to_delete = false;
}
// correctly set main_id and linked_id
$module_letter = c2cTools::Module2Letter($module);
$revert_ids = isset($type) ? (substr($type,0,1) != $module_letter) : null;
if (isset($ghost_module))
{
    $ghost_module_letter = c2cTools::Module2Letter($ghost_module);
    $revert_ghost_ids = isset($ghost_type) ? (substr($ghost_type,0,1) != $ghost_module_letter) : null;
}

if ($has_associated_docs || $has_extra_docs): ?>
<div class="one_kind_association">
<div class="association_content">
<?php
echo '<div class="assoc_img picto_'.$module.'" title="'.ucfirst(__($module)).'"><span>'.ucfirst(__($module)).__('&nbsp;:').'</span></div>';

if ($has_associated_docs)
{
    $is_inline = isset($inline); //case for users list in outings
    $has_merge_inline = isset($merge_inline) && trim($merge_inline) != '';
    if ($is_inline)
    {
        echo '<div class="linked_elt">';
    }
    $is_first = true;
    $reduce_name = (isset($reduce_name) && $reduce_name);
    $is_extra = (isset($is_extra) && $is_extra);
    $has_route_list_link = (isset($route_list_module) && !empty($route_list_ids) && !c2cTools::mobileVersion());

    if ($has_route_list_link)
    {
        $base_url = 'routes/list?';
        $param2 = "$route_list_module=$route_list_ids";
        $link_text = substr(__('routes'), 0, 1);
        $title = "routes linked to $module and $route_list_module";
    }

    $doclevel = 10;
    foreach ($associated_docs as $doc)
    {
        $is_doc = (isset($doc['is_doc']) && $doc['is_doc']);
        $doc_id = $doc['id'];
        $idstring = isset($type) ? ' id="' . $type . '_' . ($revert_ids ? $id : $doc_id) . '"' : '';
        $level = 0;
        $class = 'linked_elt';

        if (isset($doc['level']))
        {
            $level = $doc['level'];
            if ($level > 1)
            {
                $class .= ' level' . $doc['level'];
            }
        }

        if ($is_doc)
        {
            $doclevel = $level;
        }

        if ((isset($doc['parent_id']) && !$is_doc) || (isset($is_extra) && $is_extra))
        {
            $class .= ' extra';
        }

        if (!$is_inline)
        {
            echo '<div class="' . $class . '"' . $idstring . '>';
        }
        else
        {
            echo '<span' . $idstring . '>';
            if (!$is_first)
            {
                echo ', ';
            }
        }
        $is_first = false;
        
        if ($module != 'users')
        {
            $name = $doc['name'];
            if ($level > 1 || $reduce_name)
            {
                if ($level > 1)
                {
                    $cut_level = 3;
                }
                else
                {
                    $cut_level = 2;
                }
                $name_list = explode(' - ', $name, $cut_level);
                $name = array_pop($name_list);
            }
            $name = ucfirst($name);
            if (!$is_doc)
            {
                $url = "@document_by_id_lang_slug?module=$module&id=$doc_id" . '&lang=' . $doc['culture'] . '&slug=' . make_slug($doc['name']);
            }
        }
        else
        {
            $name = $doc['name'];
            if (!$is_doc)
            {
                $url = "@document_by_id_lang?module=$module&id=$doc_id" . '&lang=' . $doc['culture'];
            }
        }

        if ($is_doc)
        {
            echo '<span class="current">' . $name . '</span>';
        }
        else
        {
            echo link_to($name, $url);
        }

        if (isset($doc['lowest_elevation']) && is_scalar($doc['lowest_elevation']) && $doc['lowest_elevation'] != $doc['elevation']) // for parkings
        {
            echo '&nbsp; ' . $doc['lowest_elevation'] . __('meters') . __('range separator') . $doc['elevation'] . __('meters');
        }
        else if (isset($doc['elevation']) && is_scalar($doc['elevation']))
        {
            echo '&nbsp; ' . $doc['elevation'] . __('meters');
        }

        if (isset($doc['public_transportation_types'])) // for parkings
        {
            echo field_pt_picto_if_set($doc, true, true, ' - ');
        }
        
        if ($has_route_list_link)
        {
            $param1 = "$module=$doc_id";
            if ($route_list_linked)
            {
                $url = $base_url . $param1 . '&' . $param2;
            }
            else
            {
                $url = $base_url . $param2 . '&' . $param1;
            }
            echo ' ' . link_to($link_text, $url,
                               array('title' => __($title),
                                     'class' => 'hide',
                                     'rel' => 'nofollow'));
        }

        if (!isset($doc['parent_id']) and $show_link_to_delete)
        {
            if (isset($doc['ghost_id']) && isset($ghost_module))
            {
                $tips = 'Delete the association with this ' . $module;
            }
            else
            {
                $tips = null;
            }
            
            echo c2c_link_to_delete_element($type, $revert_ids ? $id : $doc_id, $revert_ids ? $doc_id : $id, false, (int) $strict, null, 'indicator', $tips);
            
            if (isset($doc['ghost_id']) && isset($ghost_module))
            {
                $ghost_id = $doc['ghost_id'];
                $tips = 'Delete the association with this ' . $ghost_module;
                echo c2c_link_to_delete_element($ghost_type, $revert_ghost_ids ? $id : $ghost_id, $revert_ghost_ids ? $ghost_id : $id, false, (int) $strict, null, 'indicator', $tips);
            
            }

            // button for changing a relation order
            if (in_array($type, array('ss', 'tt', 'pp')))
            {
                if ($doclevel < $level)
                {
                    $mi = $id;
                    $li = $doc_id;
                }
                else
                {
                    $mi = $doc_id;
                    $li = $id;
                }
                echo link_to(image_tag(sfConfig::get('app_static_url') . '/static/images/picto/move.gif'),
                     "@default?module=documents&action=invertAssociation&type=$type&main_id=$mi&linked_id=$li");
            }
        }

        echo $is_inline ? '</span>' : '</div>';
    }
    if ($is_inline)
    {
        if ($has_merge_inline)
        {
            echo ', ' . $sf_data->getRaw('merge_inline');
        }
        echo '</div>';
    }
}

if ($has_extra_docs)
{
    $extra_docs_raw = $sf_data->getRaw('extra_docs');
    foreach ($extra_docs_raw as $doc)
    {
        if (!empty($doc))
        {
            echo '<div class="linked_elt">' . $doc . '</div>';
        }
    }
}
?>
</div>
</div>
<?php endif ?>
