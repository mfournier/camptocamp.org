<?php 
$culture = __('meta_language');

if (isset($banner['type']) && $banner['type'] == 'flash'): //// CUSTOM FLASH BANNER ////
    $width = $banner['width'];
    $height = $banner['height'];
    $file = sfConfig::get('app_static_url') . '/static/images/pub/' . (isset($banner['file_'.$culture]) ? $banner['file_'.$culture] : $banner['file']);
    if (isset($banner['id']))
    {
        $file .= '?clickTAG=' . $sf_request->getUriPrefix() . sfConfig::get('mod_common_counter_base_url') . $banner['id'];
    }
    ?>
    <object type="application/x-shockwave-flash" data="<?php echo $file ?>"
    width="<?php echo $width ?>" height="<?php echo $height ?>" id="banner">
        <param name="movie" value="<?php echo $file ?>" />
        <param name="quality" value="high" />
    </object>


<?php elseif (isset($banner['type']) && $banner['type'] == 'adsense'): //// GOOGLE ADSENSE //// ?>
<script type="text/javascript"><!--
google_ad_client = "pub-8662990478599655";
google_ad_slot = "5346820278";
google_ad_width = 468;
google_ad_height = 60;
//--></script>
<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>


<?php elseif (isset($banner['type']) && $banner['type'] == 'netaffiliation'): //// NETAFFILIATION //// ?>
<!--[if !IE]><!-->
<object data="http://action.metaffiliation.com/emplacement.php?emp=45475Ie8475b6313ea8b6e" type="text/html" width="468" height="60"></object>
<!--<![endif]-->
<!--[if IE]>
<iframe src="http://action.metaffiliation.com/emplacement.php?emp=45475Ie8475b6313ea8b6e" width="468" height="60" scrolling="no" frameborder="0"></iframe>
<![endif]-->


<?php else: //// CUSTOM IMAGE BANNER ////
    $id = isset($banner['id_'.$culture]) ? $banner['id_'.$culture] : $banner['id']; ?>
    <a href="<?php echo $counter_base_url . $id ?>"><?php
    $image = isset($banner['image_'.$culture]) ? $banner['image_'.$culture] : $banner['image'];
    $size = @getimagesize('static/images/pub/' . $image);
    echo image_tag(sfConfig::get('app_static_url') . '/static/images/pub/' . $image,
                   array('id' => 'banner', 'alt' => $banner['alt'], 'title' => $banner['alt'],
                         'width' => $size[0], 'height' => $size[1]));
    ?></a>
<?php endif ?>
