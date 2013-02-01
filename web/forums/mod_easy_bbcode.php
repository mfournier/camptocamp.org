<?php
/***********************************************************************

  Copyright (C) 2002-2005  Rickard Andersson (rickard@punbb.org)

  This file is part of PunBB.

  PunBB is free software; you can redistribute it and/or modify it
  under the terms of the GNU General Public License as published
  by the Free Software Foundation; either version 2 of the License,
  or (at your option) any later version.

  PunBB is distributed in the hope that it will be useful, but
  WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 59 Temple Place, Suite 330, Boston,
  MA  02111-1307  USA

************************************************************************/


if (!isset($bbcode_form))
	$bbcode_form = 'post';
if (!isset($bbcode_field))
	$bbcode_field = 'req_message';

?>
						<div style="padding-top: 4px">
							<input type="button" value=" B " name="B" onclick="insert_text('[b]','[/b]')" title="<?php echo $lang_common['B button help']; ?>"/> 
							<input type="button" value=" I " name="I" onclick="insert_text('[i]','[/i]')" title="<?php echo $lang_common['I button help']; ?>"/>
							<input type="button" value=" U " name="U" onclick="insert_text('[u]','[/u]')" title="<?php echo $lang_common['U button help']; ?>"/>
							<input type="button" value=" S " name="S" onclick="insert_text('[s]','[/s]')" title="<?php echo $lang_common['S button help']; ?>"/>
							<input type="button" value=" C " name="C" onclick="insert_text('[c]','[/c]')" title="<?php echo $lang_common['C button help']; ?>"/>
							<input type="button" value="http://" name="Url" onclick="insert_text('[url=]','[/url]')" title="<?php echo $lang_common['http button help']; ?>"/>
							<input type="button" value="@" name="Email" onclick="insert_text('[email=]','[/email]')" title="<?php echo $lang_common['@ button help']; ?>"/>
							<input type="button" value="Img" name="Img" onclick="insert_text('[img]','[/img]')" title="<?php echo $lang_common['Img button help']; ?>"/>
							<input type="button" value="Code" name="Code" onclick="insert_text('[code]','[/code]')" title="<?php echo $lang_common['Code button help']; ?>"/>
							<input type="button" value="Quote" name="Quote" onclick="insert_text('[quote]','[/quote]')" title="<?php echo $lang_common['Quote button help']; ?>"/>
                            &nbsp; &nbsp; <a href="/forums/help.php"><?php echo $lang_common['Help']; ?></a>
                            &nbsp; <span title="<?php echo $lang_common['Reduce the text box']; ?>" class="picto picto_close" onclick="changeTextareaRows('<?php echo $bbcode_field; ?>', false)"></span>
                            <span title="<?php echo $lang_common['Enlarge the text box']; ?>" class="picto picto_open" onclick="changeTextareaRows('<?php echo $bbcode_field; ?>', true)"></span>
						</div>
						<div style="padding-top: 4px">
<?php

// Display the smiley set
require_once PUN_ROOT.'include/parser.php';

$smiley_dups = array();
$num_smilies = count($smiley_text);
for ($i = 0; $i < $num_smilies; ++$i)
{
	// Is there a smiley at the current index?
	if (!isset($smiley_text[$i]))
		continue;

	if (!in_array($smiley_img[$i], $smiley_dups))
		echo "\t\t\t\t\t\t\t".'<a href="javascript:insert_text(\' '.$smiley_text[$i].' \', \'\');"><img src="'.PUN_STATIC_URL.'/static/images/forums/smilies/'.$smiley_img[$i].'" width="15" height="15" alt="'.$smiley_text[$i].'" /></a>'."\n";

	$smiley_dups[] = $smiley_img[$i];
}

?>
						</div>
