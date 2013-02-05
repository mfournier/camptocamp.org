<?php
use_helper('Form', 'Javascript');

function show_map($container_div, $document, $lang, $layers_list = null, $height = null, $center = null, $has_geom = null)
{
    include_partial('documents/map_i18n');

    // define div identifiers
    $map_container_div_id   = $container_div . '_section_container';
    $app_static_url = sfConfig::get('app_static_url');
    
    $objects_list = array();
    if ($has_geom or is_null($has_geom))
    {
        if ($document->get('geom_wkt') != null)
        {
            // FIXME
            // When using polygons in openlayers, we have a bug preventing to pan the map when mouse cursor is above
            // a polygon object
            // As a workaround, we have replaced polygons by linestrings in javascript code. This was not working well with
            // multipolygons.
            // Eventually we directly modify this helper to replace MULTIPOLYGON by MULTINLINESTRINGS
            $geom = $document->get('geom_wkt');
            if (substr($geom, 0, 7) == 'POLYGON')
            {
                $geom = str_replace('POLYGON', 'MULTILINESTRING', $geom);
            }
            if (substr($geom, 0, 12) == 'MULTIPOLYGON')
            {
                $geom = str_replace(array('MULTIPOLYGON', '((', '))'), array('MULTILINESTRING', '(', ')'), $geom);
            }
            $objects_list[] = sprintf("{id: %d, type: '%s', wkt: '%s'}", $document->get('id'), $document->get('module'), $geom);
        }
    }

    // we display possible associated docs
    foreach(array('summits', 'parkings', 'huts') as $type)
    {
        if (!isset($document->$type)) continue;
        _addAssociatedDocsWithGeom($document->$type, $objects_list);
    }
    
    if (is_null($layers_list))
    {
        $layers_list = '[]';
    }
    else
    {
        if (!is_array($layers_list))
        {
            $layers_list = str_replace(' ', '', $layers_list);
            $layers_list = explode(',', $layers_list);
        }
        $layers_list = "['" . implode("','", $layers_list) . "']";
    }
    
    if (is_null($height))
    {
        $height = 300;
    }
    else
    {
        $height = min(800, max(300, $height));
    }
    
    if (is_null($center))
    {
        $init_center = '[]';
    }
    else
    {
        if (!is_array($center))
        {
            $init_center = str_replace(' ', '', $center);
        }
        else
        {
            $init_center = implode(', ', $center);
        }
        $init_center = '[' . $init_center . ']';
    }
    
    $html = javascript_tag("var mapLang = '$lang',
    objectsToShow = [" . implode(', ', $objects_list) . "],
    layersList = $layers_list,
    initCenter = $init_center;");
    
    $html .= '<section class="section" id="' . $map_container_div_id . '"><div class="article_contenu">';
    $html .= '<div id="map" style="height:' . $height . 'px;width:100%">';
    $html .= '<div id="mapLoading">'.image_tag($app_static_url . '/static/images/indicator.gif');
    $html .= __('Map is loading...') . '</div>';
    $html .= '</div>';
    $html .= '<div id="scale"></div>';
    $html .= '<div id="fake_clear"></div>';
    $html .= '</div></section>';

    // new CGXP code
    $html .= javascript_tag("
        var map = new c2corg.Map({
          div: 'map',
          addLayerSwitcher: true,
          layers: ['summits']
        });
    ");

    if (sfConfig::get('app_async_map', true))
    {
        use_helper('MyMinify');
        // FIXME if using ie for async load, set $debug to true, because minifying the js currently breaks ie
        $c2c_script_url = minify_get_combined_files_url(array('/static/js/carto/build/xapi.js',
                                                              '/static/js/carto/build/lang-fr.js',
                                                              //'/static/js/popup.js',
                                                              '/static/js/carto/docmap.js'),
                                                        (bool)sfConfig::get('app_minify_debug'));

        // FIXME extjs uses document.write with ie, so we cannot for the moment use async loading with ie
        $html .= javascript_tag('
if (!Prototype.Browser.IE) { var c2corgloadMapAsync = true; }
function c2c_asyncload(jsurl) { var a = document.createElement(\'script\'), h = document.getElementsByTagName(\'head\')[0]; a.async = 1; a.src = jsurl; h.appendChild(a); }
function asyncloadmap() { if (!Prototype.Browser.IE) { c2c_asyncload(\''.$c2c_script_url.'\'); }}');
    }

    return $html;
}

function _addAssociatedDocsWithGeom($docs, &$objects_list)
{
    foreach ($docs as $doc)
    {
        if (!empty($doc['pointwkt']))
        {
            $objects_list[] = sprintf("{id: %d, type: '%s', wkt: '%s'}",
                                      $doc['id'], $doc['module'], $doc['pointwkt']);
        }
    }
}

function _loadJsMapTools()
{
    $response = sfContext::getInstance()->getResponse();

    use_stylesheet('/static/js/carto/build/xapi.css', 'custom');
    use_javascript('/static/js/carto/build/xapi.js', 'maps');
    use_javascript('/static/js/carto/build/lang-fr.js', 'maps');
    use_javascript('/static/js/carto/docmap.js', 'maps');
}

_loadJsMapTools();
