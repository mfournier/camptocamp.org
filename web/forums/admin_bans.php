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


// Tell header.php to use the admin template
define('PUN_ADMIN_CONSOLE', 1);

define('PUN_ROOT', './');
require PUN_ROOT.'include/common.php';
require PUN_ROOT.'include/common_admin.php';


if ($pun_user['g_id'] > PUN_MOD || ($pun_user['g_id'] == PUN_MOD && $pun_config['p_mod_ban_users'] == '0'))
	message($lang_common['No permission']);


// Add/edit a ban (stage 1)
if (isset($_REQUEST['add_ban']) || isset($_GET['edit_ban']))
{
	if (isset($_GET['add_ban']) || isset($_POST['add_ban']))
	{
		// If the id of the user to ban was provided through GET (a link from profile.php)
		if (isset($_GET['add_ban']))
		{
			$add_ban = $_GET['add_ban'];
		}
		else	// Otherwise the user id is in POST
		{
			$add_ban = trim($_POST['new_ban_user']);
        }
        $add_user_id = intval($add_ban);
        
        if ($add_user_id > 0)
        {
			if ($add_user_id < 2)
				message($lang_common['Bad request']);

			$ban_user_id = $add_user_id;

			$result = $db->query('SELECT group_id, username, email FROM '.$db->prefix.'users WHERE id='.$ban_user_id) or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
			if ($db->num_rows($result))
				list($group_id, $ban_user, $ban_email) = $db->fetch_row($result);
			else
                message('No user by that ID registered. If you want to add a ban not tied to a specific user ID just leave the user ID blank.');
		}
        elseif (!empty($add_ban))
        {
            message('Incorrect ID format. If you want to add a ban not tied to a specific user ID just leave the user ID blank.');
        }

		// Make sure we're not banning an admin
		if (isset($group_id) && ($group_id == PUN_ADMIN || $group_id == PUN_MOD))
			message('The user '.pun_htmlspecialchars($ban_user).' is a forum administrator or moderator and can\'t be banned. If you want to ban an administrator or a moderator, you must first demote him/her to user.');

		// If we have a $ban_user_id, we can try to find the last known IP of that user
		if (isset($ban_user_id))
		{
			$result = $db->query('SELECT poster_ip FROM '.$db->prefix.'posts WHERE poster_id='.$ban_user_id.' ORDER BY posted DESC LIMIT 1') or error('Unable to fetch post info', __FILE__, __LINE__, $db->error());
			$ban_ip = ($db->num_rows($result)) ? $db->result($result) : '';
		}

		$mode = 'add';
	}
	else	// We are editing a ban
	{
		$ban_id = intval($_GET['edit_ban']);
		if ($ban_id < 1)
			message($lang_common['Bad request']);

		$result = $db->query('SELECT username, ip, email, message, expire FROM '.$db->prefix.'bans WHERE id='.$ban_id) or error('Unable to fetch ban info', __FILE__, __LINE__, $db->error());
		if ($db->num_rows($result))
			list($ban_user_id, $ban_ip, $ban_email, $ban_message, $ban_expire) = $db->fetch_row($result);
		else
			message($lang_common['Bad request']);

        if (!empty($ban_user_id))
        {
            $result = $db->query('SELECT username FROM '.$db->prefix.'users WHERE id='.$ban_user_id) or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
            if ($db->num_rows($result))
                $ban_user = $db->result($result);
            else
                message('No user by that ID registered.');
		}
        
        $ban_expire = ($ban_expire != '') ? date('Y-m-d', $ban_expire) : '';

		$mode = 'edit';
	}

	$page_title = pun_htmlspecialchars($pun_config['o_board_title']).' / Admin / Bans';
	$focus_element = array('bans2', 'ban_user_id');
	require PUN_ROOT.'header.php';

	generate_admin_menu('bans');


?>
	<div class="blockform">
		<h2><span>Ban advanced settings</span></h2>
		<div class="box">
			<form id="bans2" method="post" action="admin_bans.php">
				<div class="inform">
				<input type="hidden" name="mode" value="<?php echo $mode ?>" />
<?php if ($mode == 'edit'): ?>				<input type="hidden" name="ban_id" value="<?php echo $ban_id ?>" />
<?php endif; ?>				<fieldset>
						<legend>Supplement ban with IP</legend>
						<div class="infldset">
							<table class="aligntop">
								<tr>
									<th scope="row">User ID</th>
									<td>
										<input type="text" name="ban_user_id" size="25" maxlength="25" value="<?php if (isset($ban_user_id)) echo $ban_user_id; ?>" tabindex="1" /><?php if (isset($ban_user)) echo ' ' . pun_htmlspecialchars($ban_user); ?>
										<span>The user ID to ban.</span>
									</td>
								</tr>
								<tr>
									<th scope="row">IP-adresses</th>
									<td>
										<input type="text" name="ban_ip" size="45" maxlength="255" value="<?php if (isset($ban_ip)) echo $ban_ip; ?>" tabindex="2" />
										<span>The IP or IP-ranges you wish to ban (e.g. 150.11.110.1 or 150.11.110). Separate addresses with spaces. If an IP is entered already it is the last known IP of this user in the database.<?php if ($ban_user != '' && isset($ban_user_id)) echo ' Click <a href="admin_users.php?ip_stats='.$ban_user_id.'">here</a> to see IP statistics for this user.' ?></span>
									</td>
								</tr>
								<tr>
									<th scope="row">Ban comment</th>
									<td>
										<input type="text" name="ban_email" size="50" maxlength="200" value="<?php if (isset($ban_email)) echo strtolower($ban_email); ?>" tabindex="3" />
										<span>Comment showed only in ban list</span>
									</td>
								</tr>
							</table>
							<p class="topspace"><strong class="warntext">You should be very careful when banning an IP-range because of the possibility of multiple users matching the same partial IP.</strong></p>
						</div>
					</fieldset>
				</div>
				<div class="inform">
					<fieldset>
						<legend>Ban message and expiry</legend>
						<div class="infldset">
							<table class="aligntop">
								<tr>
									<th scope="row">Ban message</th>
									<td>
										<input type="text" name="ban_message" size="50" maxlength="255" value="<?php if (isset($ban_message)) echo pun_htmlspecialchars($ban_message); ?>" tabindex="4" />
										<span>A message that will be displayed to the banned user when he/she visits the forums.</span>
									</td>
								</tr>
								<tr>
									<th scope="row">Expire date</th>
									<td>
										<input type="text" name="ban_expire" size="17" maxlength="10" value="<?php if (isset($ban_expire)) echo $ban_expire; ?>" tabindex="5" />
										<span>The date when this ban should be automatically removed (format: YYYY-MM-DD). Leave blank to remove manually.</span>
									</td>
								</tr>
							</table>
						</div>
					</fieldset>
				</div>
				<p class="submitend"><input type="submit" name="add_edit_ban" value=" Save " tabindex="6" /></p>
			</form>
		</div>
	</div>
	<div class="clearer"></div>
</div>
<?php

	require PUN_ROOT.'footer.php';
}


// Add/edit a ban (stage 2)
else if (isset($_POST['add_edit_ban']))
{
	confirm_referrer('admin_bans.php');

	$ban_user_id_tmp = trim($_POST['ban_user_id']);
	$ban_ip = trim($_POST['ban_ip']);
	$ban_email = trim($_POST['ban_email']);
	$ban_message = trim($_POST['ban_message']);
	$ban_expire = trim($_POST['ban_expire']);

	if ($ban_user_id_tmp == '' && $ban_ip == '')
		message('You must enter either a user ID or an IP address (at least).');

	// Validate user ID
    $ban_user_id = intval($ban_user_id_tmp);
    
    if ($ban_user_id > 0)
    {
        if ($ban_user_id == 1)
            message('The guest user cannot be banned.');

        $result = $db->query('SELECT group_id, username FROM '.$db->prefix.'users WHERE id='.$ban_user_id) or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
        if ($db->num_rows($result))
            list($group_id, $ban_user) = $db->fetch_row($result);
        else
            message('No user by that ID registered. If you want to add a ban not tied to a specific user ID just leave the user ID blank.');

        // Make sure we're not banning an admin
        if (isset($group_id) && ($group_id == PUN_ADMIN || $group_id == PUN_MOD))
            message('The user '.pun_htmlspecialchars($ban_user).' is a forum administrator or moderator and can\'t be banned. If you want to ban an administrator or a moderator, you must first demote him/her to user.');
    }
    elseif (!empty($ban_user_id_tmp))
    {
        message('Incorrect ID format. If you want to add a ban not tied to a specific user ID just leave the user ID blank.');
    }

	// Validate IP/IP range (it's overkill, I know)
	if ($ban_ip != '')
	{
		$ban_ip = preg_replace('/[\s]{2,}/', ' ', $ban_ip);
		$addresses = explode(' ', $ban_ip);
		$addresses = array_map('trim', $addresses);

		for ($i = 0; $i < count($addresses); ++$i)
		{
			if (strpos($addresses[$i], ':') !== false)
			{
				$octets = explode(':', $addresses[$i]);


				for ($c = 0; $c < count($octets); ++$c)
				{

					$octets[$c] = ltrim($octets[$c], "0");

					if ($c > 7 || (!empty($octets[$c]) && !ctype_xdigit($octets[$c])) || intval($octets[$c], 16) > 65535)
						message('You entered an invalid IP/IP-range.');
				}

				$cur_address = implode(':', $octets);
				$addresses[$i] = $cur_address;
			}
			else
			{
				$octets = explode('.', $addresses[$i]);

				for ($c = 0; $c < count($octets); ++$c)
				{

					$octets[$c] = (strlen($octets[$c]) > 1) ? ltrim($octets[$c], "0") : $octets[$c];

					if ($c > 3 || !ctype_digit($octets[$c]) || intval($octets[$c]) > 255)
						message('You entered an invalid IP/IP-range.');
				}

				$cur_address = implode('.', $octets);
				$addresses[$i] = $cur_address;
			}
		}

		$ban_ip = implode(' ', $addresses);
	}

	if ($ban_expire != '' && $ban_expire != 'Never')
	{
		$ban_expire = strtotime($ban_expire);

		if ($ban_expire == -1 || $ban_expire <= time())
			message('You entered an invalid expire date. The format should be YYYY-MM-DD and the date must be at least one day in the future.');
	}
	else
		$ban_expire = 'NULL';

	$ban_user_id = ($ban_user_id != '') ? '\''.$db->escape($ban_user_id).'\'' : 'NULL';
	$ban_ip = ($ban_ip != '') ? '\''.$db->escape($ban_ip).'\'' : 'NULL';
	$ban_email = ($ban_email != '') ? '\''.$db->escape($ban_email).'\'' : 'NULL';
	$ban_message = ($ban_message != '') ? '\''.$db->escape($ban_message).'\'' : 'NULL';

	if ($_POST['mode'] == 'add')
		$db->query('INSERT INTO '.$db->prefix.'bans (username, ip, email, message, expire) VALUES('.$ban_user_id.', '.$ban_ip.', '.$ban_email.', '.$ban_message.', '.$ban_expire.')') or error('Unable to add ban', __FILE__, __LINE__, $db->error());
	else
		$db->query('UPDATE '.$db->prefix.'bans SET username='.$ban_user_id.', ip='.$ban_ip.', email='.$ban_email.', message='.$ban_message.', expire='.$ban_expire.' WHERE id='.intval($_POST['ban_id'])) or error('Unable to update ban', __FILE__, __LINE__, $db->error());

	// Regenerate the bans cache
	require_once PUN_ROOT.'include/cache.php';
	generate_bans_cache();

	redirect('admin_bans.php', 'Ban '.(($_POST['mode'] == 'edit') ? 'edited' : 'added').'. Redirecting &hellip;');
}


// Remove a ban
else if (isset($_GET['del_ban']))
{
	confirm_referrer('admin_bans.php');

	$ban_id = intval($_GET['del_ban']);
	if ($ban_id < 1)
		message($lang_common['Bad request']);

	$db->query('DELETE FROM '.$db->prefix.'bans WHERE id='.$ban_id) or error('Unable to delete ban', __FILE__, __LINE__, $db->error());

	// Regenerate the bans cache
	require_once PUN_ROOT.'include/cache.php';
	generate_bans_cache();

	redirect('admin_bans.php', 'Ban removed. Redirecting &hellip;');
}


$page_title = pun_htmlspecialchars($pun_config['o_board_title']).' / Admin / Bans';
$focus_element = array('bans', 'new_ban_user');
require PUN_ROOT.'header.php';

generate_admin_menu('bans');

?>
	<div class="blockform">
		<h2><span>New ban</span></h2>
		<div class="box">
			<form id="bans" method="post" action="admin_bans.php?action=more">
				<div class="inform">
					<fieldset>
						<legend>Add ban</legend>
						<div class="infldset">
							<table class="aligntop">
								<tr>
									<th scope="row">User ID<div><input type="submit" name="add_ban" value=" Add " tabindex="2" /></div></th>
									<td>
										<input type="text" name="new_ban_user" size="25" maxlength="25" tabindex="1" />
										<span>The user ID to ban. The next page will let you enter a custom IP. If you just want to ban a specific IP/IP-range or e-mail just leave it blank.</span>
									</td>
								</tr>
							</table>
						</div>
					</fieldset>
				</div>
			</form>
		</div>

		<h2 class="block2"><span>Existing bans</span></h2>
		<div class="box">
			<div class="fakeform">
<?php

$result = $db->query('SELECT id, username, ip, email, message, expire FROM '.$db->prefix.'bans ORDER BY id') or error('Unable to fetch ban list', __FILE__, __LINE__, $db->error());
if ($db->num_rows($result))
{
	while ($cur_ban = $db->fetch_assoc($result))
	{
		$expire = format_time($cur_ban['expire'], true);

?>
				<div class="inform">
					<fieldset>
						<legend>Ban expires: <?php echo $expire ?></legend>
						<div class="infldset">
							<table>
<?php if ($cur_ban['username'] != ''): ?>								<tr>
									<th>User ID</th>
									<td><?php echo pun_htmlspecialchars($cur_ban['username']) ?></td>
								</tr>
<?php endif; ?><?php if ($cur_ban['ip'] != ''): ?>								<tr>
									<th>IP/IP-ranges</th>
									<td><?php echo $cur_ban['ip'] ?></td>
								</tr>
<?php endif; ?><?php if ($cur_ban['message'] != ''): ?>								<tr>
									<th>Reason</th>
									<td><?php echo pun_htmlspecialchars($cur_ban['message']) ?></td>
								</tr>
<?php endif; ?><?php if ($cur_ban['email'] != ''): ?>								<tr>
									<th>Comment</th>
									<td><?php echo $cur_ban['email'] ?></td>
								</tr>
<?php endif; ?>							</table>
							<p class="linkactions"><a href="admin_bans.php?edit_ban=<?php echo $cur_ban['id'] ?>">Edit</a> - <a href="admin_bans.php?del_ban=<?php echo $cur_ban['id'] ?>">Remove</a></p>
						</div>
					</fieldset>
				</div>
<?php

	}
}
else
	echo "\t\t\t\t".'<p>No bans in list.</p>'."\n";

?>
			</div>
		</div>
	</div>
	<div class="clearer"></div>
</div>
<?php

require PUN_ROOT.'footer.php';
