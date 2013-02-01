<?php
if(in_array($module, array('areas', 'articles', 'books', 'huts', 'outings', 'parkings', 'routes', 'sites', 'summits'))): ?>
<br />
<br />
<hr />
<br />
<script type="text/javascript">
//<![CDATA[
Event.observe(window, 'load', function() {
C2C.GoogleSearch.i18n = new Array('<?php
$google_i18n = array('first page', 'previous page', 'next page', 'Document title', 'Extract', 'No result');
$google_i18n = array_map('__', $google_i18n);
echo implode('\', \'', $google_i18n);
?>');
<?php $cse = sfConfig::get('app_google_cse'); ?>
C2C.GoogleSearch.base_url = 'https://www.googleapis.com/customsearch/v1?key=<?php echo sfConfig::get('app_google_api_key') ?>&cx=<?php echo $cse[$module] ?>&callback=C2C.GoogleSearch.handleResponse';
C2C.GoogleSearch.alternate_url = 'http://www.google.com/cse?cx=<?php echo $cse[$module] ?>';
});
//]]>
</script>
<div id="google_search">
<?php
use_helper('Form');
$response = sfContext::getInstance()->getResponse();
$response->addJavascript('/static/js/google_search.js', 'last');
echo __('Search with google');
echo form_tag('http://www.google.com/search', array('method'=>'get', 'onsubmit' => 'C2C.GoogleSearch.q=$F(\'google_search_input\'); C2C.GoogleSearch.search(); return false;'));
$gwm = 'url(http://www.google.com/coop/intl/'.__('meta_language').'/images/google_custom_search_watermark.gif) no-repeat scroll left center #fff';
$nogwm = "none repeat scroll 0 0 #fff";
echo input_tag('q', null, array('id'=>'google_search_input',
                                'onblur' => "if (this.value == '') this.style.background = '$gwm';",
                                'onfocus' => "this.style.background = '$nogwm';",
                                'style' => 'background: '.$gwm));
echo input_hidden_tag('sitesearch', "camptocamp.org/$module"); ?>
<div id="google_search_submit" onclick="this.up().submit();"></div>
</form>
<div id="google_search_results"></div>
</div>
<?php endif;
