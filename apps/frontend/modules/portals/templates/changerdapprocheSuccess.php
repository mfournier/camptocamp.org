<?php
use_helper('Home', 'Language', 'Sections', 'Viewer', 'General', 'Field', 'AutoComplete', 'sfBBCode', 'SmartFormat', 'Button'); 

$culture = $sf_user->getCulture();
$connected = $sf_user->isConnected();
$is_moderator = $sf_user->hasCredential(sfConfig::get('app_credentials_moderator'));
$id = $sf_params->get('id');
$is_not_archive = !$document->isArchive();
$is_not_merged = !$document->get('redirects_to');
$mobile_version = c2cTools::mobileVersion();
$show_link_to_delete = ($is_not_archive && $is_not_merged && $is_moderator);
$show_link_tool = ($is_not_archive && $is_not_merged && $is_moderator);

$design_files = $document->get('design_file');
$design_files = explode(',', $design_files);
if (count($design_files))
{
    foreach ($design_files as $file)
    {
        $file = trim($file);
        if (!empty($file))
        {
            use_stylesheet('/static/css/' . $file . '.css', 'custom');
        }
    }
}

echo init_js_var(true, 'home_nav', $connected);

echo '<div id="cda_context" class="home article portals_content">';

if (!$mobile_version) // left navigation menus are only for web version
{
    echo '<div id="cda_background_left">';
    
    $lang = $document->getCulture();
    $abstract = $document->get('abstract');
    $abstract = parse_links(parse_bbcode_abstract($abstract));
    $title = __('changerdapproche');
    $know_more_link = getMetaArticleRoute('cda_know_more', false);
    include_partial('portals/welcome', array('sf_cache_key' => $id . '_' . $culture . '_' . $lang,
                                             'title' => $title,
                                             'description' => $abstract,
                                             'know_more_link' => $know_more_link,
                                             'default_open' => true));

    include_partial('portals/wizard_button', array('sf_cache_key' => ($connected ? 'connected' : 'not_connected') . '_' . $culture));

    if ($has_videos)
    {
        include_partial('portals/latest_videos', array('items' => $latest_videos, 'culture' => $culture, 'default_open' => true));
    }
    if ($has_images && $has_map)
    {
        echo '<div id="last_images">';
        $image_url_params = $sf_data->getRaw('image_url_params');
        $image_url_params = implode('&', $image_url_params);
        $custom_title_link = 'images/list';
        $custom_rss_link = 'images/rss';
        if (!empty($image_url_params))
        {
            $custom_title_link .= '?' . $image_url_params;
            $custom_rss_link .= '?' . $image_url_params;
        }
        include_partial('images/latest',
                        array('items' => $latest_images,
                              'culture' => $culture,
                              'default_open' => true,
                              'custom_title_link' => $custom_title_link,
                              'custom_rss_link' => $custom_rss_link));
        echo '</div>';
    }

    include_partial('portals/prepare', array('sf_cache_key' => $culture,
                                             'content_id' => 'cda_prepare_outing_box',
                                             'default_open' => true));

    echo '</div>';
}

echo '<div id="cda_background_right">';

if ($has_map && !$mobile_version)
{
    $map_filter = $sf_data->getRaw('map_filter');
    include_partial('documents/map_section', array('document' => $document,
                                                   'layers_list' => $map_filter['objects'],
                                                   'center' => $map_filter['center'],
                                                   'height' => $map_filter['height'],
                                                   'home_section' => true,
                                                   'section_title' => 'cda map title',
                                                   'help_text' => 'cda map help text',
                                                   'show_map' => true,
                                                   'has_geom' => $has_geom));
}

if ($has_images && !$has_map)
{
    echo '<div id="last_images">';
    $image_url_params = $sf_data->getRaw('image_url_params');
    $image_url_params = implode('&', $image_url_params);
    $custom_title_link = 'images/list';
    $custom_rss_link = 'images/rss';
    if (!empty($image_url_params))
    {
        $custom_title_link .= '?' . $image_url_params;
        $custom_rss_link .= '?' . $image_url_params;
    }
    include_partial('images/latest',
                    array('items' => $latest_images,
                          'culture' => $culture,
                          'default_open' => true,
                          'custom_title_link' => $custom_title_link,
                          'custom_rss_link' => $custom_rss_link));
    echo '</div>';
}

echo '<div id="home_left_content">';

if ($has_outings)
{
    $outing_url_params = $sf_data->getRaw('outing_url_params');
    $outing_url_params = implode('&', $outing_url_params);
    include_partial('outings/latest',
                    array('items' => $latest_outings,
                          'culture' => $culture,
                          'default_open' => true,
                          'custom_title_text' => __('Last soft mobility outings'),
                          'custom_footer_text' => __('Soft mobility outings'),
                          'custom_url_params' => $outing_url_params));
}
if ($has_articles)
{
    $article_url_params = $sf_data->getRaw('article_url_params');
    $article_url_params = implode('&', $article_url_params);
    $custom_title_link = 'articles/list';
    $custom_rss_link = 'articles/rss';
    if (!empty($article_url_params))
    {
        $custom_title_link .= '?' . $article_url_params;
        $custom_rss_link .= '?' . $article_url_params;
    }
    include_partial('articles/latest',
                    array('items' => $latest_articles,
                          'culture' => $culture,
                          'default_open' => true,
                          'custom_title_text' => __('Last soft mobility articles'),
                          'custom_footer_text' => __('Soft mobility articles'),
                          'custom_title_link' => $custom_title_link,
                          'custom_rss_link' => $custom_rss_link));
}

echo '</div>';
echo '<div id="home_right_content">';

if ($has_news)
{
    include_partial('documents/latest_mountain_news',
                    array('items' => $latest_mountain_news,
                          'culture' => $culture,
                          'default_open' => true));
}
if ($has_topics)
{
    $custom_title_link = 'viewforum.php?id=42';
    include_partial('documents/latest_threads',
                    array('items' => $latest_threads,
                          'culture' => $culture,
                          'default_open' => true,
                          'custom_title_link' => $custom_title_link));
}

echo '</div>';

if ($is_not_archive)
{
    echo '<div class="fake_clear"> &nbsp;</div>';
    include_partial('portals/inside_search_form', array('document' => $document));
}

// lang-dependent content
if ($has_description)
{
    echo '<div class="article_contenu">';
    include_partial('portals/i18n', array('document' => $document, 'languages' => $sf_data->getRaw('languages'),
                                          'needs_translation' => $needs_translation, 'images' => $associated_images));
    echo '</div>';
}

if ($is_moderator)
{
    $lang = $culture;
    
    echo '<ul class="contribs">';
    echo '<li><span class="picto action_edit"></span>' . link_to(__('Edit'), "@document_edit?module=portals&id=$id&lang=$lang") . '</li>';
    echo '<li><span class="picto action_list"></span>' . link_to(__('History'), "@document_history?module=portals&id=$id&lang=$lang") . '</li>';
    echo '</ul>';
}

if ($mobile_version) // for mobile, move prepare outing box under articles section
{
    include_partial('portals/prepare', array('sf_cache_key' => $culture,
                                             'content_id' => 'cda_prepare_outing_box',
                                             'default_open' => true));
}

echo '</div>';
echo '<div class="fake_clear"> &nbsp;</div>';
?>