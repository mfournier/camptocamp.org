<?php
	require PUN_ROOT.'lang/'.$pun_user['language'].'/pms.php';

	if($pun_config['o_pms_enabled'] && !$pun_user['is_guest'] && $pun_user['g_pm'] == 1)
	{
		if (isset($poster_id))
        {
            $user_contacts[] = '<a href="message_send.php?id='.$poster_id.'&amp;pid='.$cur_post['id'].'" rel="nofollow">'.$lang_pms['PM'].'</a>';
        }
        else
        {
            $user_contacts[] = '<a href="message_send.php?id='.$cur_post['id'].'&amp;tid='.$id.'" rel="nofollow">'.$lang_pms['PM'].'</a>';
        }
	}
?>
