<?php
if (!c2cTools::mobileVersion()) echo select_all_header_list_tag();
echo header_list_tag('wnam', 'name');
echo header_list_tag('act', 'activities short');
echo region_header_list_tag('region_name');
echo images_header_list_tag();
echo comments_header_list_tag();
