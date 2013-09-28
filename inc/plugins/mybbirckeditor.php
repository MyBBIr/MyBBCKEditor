<?php
/**
 * MyBB 1.6
 * Copyright 2012 My-BB.Ir Group, All Rights Reserved
 *
 * Website: http://my-bb.ir
 *
 * $Id: mybbirckeditor.php AliReza_Tofighi $
 */
 
// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}
global $mybb;
if ($mybb->settings['mybbirckeditorswitch'] == 1)
{
	if ($mybb->settings['mybbirckeditor_newthread'] == 1)
		$plugins->add_hook("newthread_end", "mybbirckeditor");
	if ($mybb->settings['mybbirckeditor_newreply'] == 1)
		$plugins->add_hook("newreply_end", "mybbirckeditor");
	$plugins->add_hook("showthread_start", "mybbirckeditor_html");
	if ($mybb->settings['mybbirckeditor_usercp'] == 1)
		$plugins->add_hook("usercp_editsig_end", "mybbirckeditor_signature");
	if ($mybb->settings['mybbirckeditor_modcp'] == 1)
		$plugins->add_hook("modcp_editprofile_end", "mybbirckeditor_signature");
	if ($mybb->settings['mybbirckeditor_private'] == 1)
		$plugins->add_hook("private_send_end", "mybbirckeditor");
	if ($mybb->settings['mybbirckeditor_editpost'] == 1)
		$plugins->add_hook("editpost_end", "mybbirckeditor");
	if ($mybb->settings['mybbirckeditor_modcp'] == 1)
		$plugins->add_hook("modcp_end", "mybbirckeditor");
	if ($mybb->settings['mybbirckeditor_modcp'] == 1)
		$plugins->add_hook("modcp_new_announcement", "mybbirckeditor");
	if ($mybb->settings['mybbirckeditor_modcp'] == 1)
		$plugins->add_hook("modcp_edit_announcement", "mybbirckeditor");
	if ($mybb->settings['mybbirckeditor_calendar'] == 1)
		$plugins->add_hook("calendar_addevent_end", "mybbirckeditor");
	if ($mybb->settings['mybbirckeditor_calendar'] == 1)
		$plugins->add_hook("calendar_editevent_end", "mybbirckeditor");
	if ($mybb->settings['mybbirckeditor_warnings'] == 1)
		$plugins->add_hook("warnings_warn_end", "mybbirckeditor_warn");
	if ($mybb->settings['mybbirckeditor_quickreply'] != 0)
	{
		$plugins->add_hook("showthread_end", "mybbirckeditorthread");
		$plugins->add_hook("showthread_start", "mybbirckeditorthread");
	}
	$plugins->add_hook("usercp_options_end", "mybbirckeditor_ucpoptions");
	$plugins->add_hook("usercp_do_options_end", "mybbirckeditor_ucpdooptions");
	$plugins->add_hook("pre_output_page", "mybbirckeditorglobaltag");
}
	$plugins->add_hook("parse_message", "mybbirckeditor_parser");

function mybbirckeditor_getthemeeditors() {
	global $setting;
	$select = '<select name="upsetting['.$setting['name'].']">';
	$options = array();
	$editor_theme_root = MYBB_ROOT."ckeditor/skins/";
	if($dh = @opendir($editor_theme_root))
	{
		while($dir = readdir($dh))
		{
			if($dir == ".svn" || $dir == "." || $dir == ".." || !is_dir($editor_theme_root.$dir))
			{
				continue;
			}
			$options[$dir] = ucfirst(str_replace('_', ' ', $dir));
			if ($setting['value'] == $dir)
			{
				$select .= '<option value="'.$dir.'" selected="selected">'.$options[$dir].'</option>';
			}
			else
			{
				$select .= '<option value="'.$dir.'">'.$options[$dir].'</option>';
			}
		}
	}
	$select .= '</select>';
	return $select;

}
function mybbirckeditor_info()
{
	global $lang;
	$lang->load('admin_mybbirckeditor');
	return array(
		"name"			=> $lang->mybbirckeditor_name,
		"description"	=> $lang->mybbirckeditor_description,
		"website"		=> "http://my-bb.ir",
		"author"		=> "AliReza_Tofighi",
		"authorsite"	=> "http://my-bb.ir",
		"version"		=> "2",
		"guid" 			=> "",
		"compatibility" => "*"
	);
}


function mybbirckeditor_activate(){
	global $mybb, $db, $lang;
	$lang->load('admin_mybbirckeditor');
	$db->write_query('ALTER TABLE `'.TABLE_PREFIX.'users` ADD `ckeditor` INT(1) NOT NULL DEFAULT \'1\';');
	include MYBB_ROOT."/inc/adminfunctions_templates.php";
	find_replace_templatesets("usercp_options", "#".preg_quote('{$ckeditor}')."#i", '', 0);
	find_replace_templatesets("showthread_quickreply", "#".preg_quote('{$smilieinserter}')."#i", '', 0);
	find_replace_templatesets("post_attachments_attachment_postinsert", "#".preg_quote('CKEDITOR.instances.message.insertText(\'[attachment={$attachment[\'aid\']}]\');')."#i", '', 0);
	find_replace_templatesets("misc_smilies_popup", "#".preg_quote('<script type="text/javascript" src="jscripts/ckinsertsmilies.js"></script>')."#i", '', 0);
	find_replace_templatesets("usercp_options", "#".preg_quote('<tr>
<td colspan="2"><span class="smalltext">{$lang->style}</span></td>')."#i", '{$ckeditor}<tr>
<td colspan="2"><span class="smalltext">{$lang->style}</span></td>');	
	find_replace_templatesets("post_attachments_attachment_postinsert", "#".preg_quote('onclick="')."#i", 'onclick="CKEDITOR.instances.message.insertText(\'[attachment={$attachment[\'aid\']}]\');');	
	find_replace_templatesets("misc_smilies_popup_smilie", "#".preg_quote('insertSmilie')."#i", 'ckinsertSmilie');	
	find_replace_templatesets("misc_smilies_popup_smilie", "#".preg_quote('(\'{$smilie[\'insert\']}\');')."#i", '(\'{$smilie[\'image\']}\');');	
	find_replace_templatesets("misc_smilies_popup", "#".preg_quote('</head>')."#i", '<script type="text/javascript" src="jscripts/ckinsertsmilies.js"></script></head>');	
	find_replace_templatesets("showthread", "#".preg_quote('src="jscripts/thread.js')."#i", 'src="jscripts/thread{$threadckeditor}.js');	
	find_replace_templatesets("showthread_quickreply", "#".preg_quote('{$lang->message_note}<br /><br />')."#i", '{$lang->message_note}<br /><br />{$smilieinserter}');	

	$db->write_query("DELETE FROM ".TABLE_PREFIX."settings WHERE name IN(
		'mybbirckeditorswitch',
		'mybbirckeditorswitchmycode',
		'mybbirckeditorcolor',
		'mybbirckeditorskin',
		'mybbirckeditorfonts',
		'mybbirckeditormigiclinecolor',
		'mybbirckeditor_newthread',
		'mybbirckeditor_newreply',
		'mybbirckeditor_quickreply',
		'mybbirckeditor_usercp',
		'mybbirckeditor_private',
		'mybbirckeditor_editpost',
		'mybbirckeditor_modcp',
		'mybbirckeditor_calendar',
		'mybbirckeditor_warnings',
		'mybbirckeditorsmilies',
		'mybbirckeditormaxheight',
		'mybbirckeditor_enableautosave',
		'mybbirckeditor_autosave',
		'mybbirckeditor_contextmenu',
		'mybbirckeditor_width',
		'mybbirckeditor_height',
		'mybbirckeditor_minimenu',
		'mybbirckeditor_fullmenu',
		'mybbirckeditor_toolbarlocation'
	)");
	$db->delete_query("settinggroups", "name = 'mybbirckeditor'");
	$i = 0;
	$insertarray = array(
		'name' => 'mybbirckeditor',
		'title' => $lang->mybbirckeditor_settings,
		'description' => '',
		'disporder' => $rows+1,
		'isdefault' => $i
	);
	$group['gid'] = $db->insert_query("settinggroups", $insertarray);
	$i++;
	$insertarray = array(
		'name' => 'mybbirckeditorswitch',
		'title' => $lang->mybbirckeditor_enable,
		'description' => '',
		'optionscode' => 'onoff',
		'value' => 1,
		'disporder' => $i,
		'gid' => $group['gid']
	);
	$db->insert_query("settings", $insertarray);
	$i++;

	$insertarray = array(
		'name' => 'mybbirckeditorsmilies',
		'title' => $lang->mybbirckeditor_smilies,
		'description' => '',
		'optionscode' => 'onoff',
		'value' => 1,
		'disporder' => $i,
		'gid' => $group['gid']
	);
	$db->insert_query("settings", $insertarray);
	$i++;
	
	$insertarray = array(
		'name' => 'mybbirckeditorswitchmycode',
		'title' => $lang->mybbirckeditor_mycodeeditor,
		'description' => $lang->mybbirckeditor_mycodeeditor_d,
		'optionscode' => 'yesno',
		'value' => 1,
		'disporder' => $i,
		'gid' => $group['gid']
	);
	$db->insert_query("settings", $insertarray);
	$i++;
	$insertarray = array(
		'name' => 'mybbirckeditorcolor',
		'title' => $lang->mybbirckeditor_editorcolor,
		'description' => '<script type="text/javascript" src="../ckeditor/jscolor/jscolor.js"></script>',
		'optionscode' => 'php
<input class=\\\\"text_input color\\\\" type=\\\\"text\\\\" value=\\\\"".$setting[\\\'value\\\']."\\\\" name=\\\\"upsetting[{$setting[\\\'name\\\']}]\\\\" />',
		'value' => "CCCCCC",
		'disporder' => $i,
		'gid' => $group['gid']
	);
	$db->insert_query("settings", $insertarray);
	$i++;
	$insertarray = array(
		'name' => 'mybbirckeditorskin',
		'title' => $lang->mybbirckeditor_editorskin,
		'description' => '',
		'optionscode' => 'php
".mybbirckeditor_getthemeeditors()."',
		'value' => 'moonocolor',
		'disporder' => $i,
		'gid' => $group['gid']
	);
	$db->insert_query("settings", $insertarray);
	$i++;
	$insertarray = array(
		'name' => 'mybbirckeditorfonts',
		'title' => $lang->mybbirckeditor_fonts,
		'description' => $lang->mybbirckeditor_fonts_d,
		'optionscode' => 'textarea',
		'value' => "Tahoma/Tahoma;Arial/Arial;Comic Sans MS/Comic Sans MS/Courier New;Georgia/Georgia, serif;Lucida Sans Unicode/Lucida Sans Unicode;Times New Roman/Times New Roman;Trebuchet MS/Trebuchet MS;Verdana/Verdana;",
		'disporder' => $i,
		'gid' => $group['gid']
	);
	$db->insert_query("settings", $insertarray);
	$i++;
	$insertarray = array(
		'name' => 'mybbirckeditormigiclinecolor',
		'title' => $lang->mybbirckeditor_migiclinecolor,
		'description' => '',
		'optionscode' => 'php
<input class=\\\\"text_input color\\\\" type=\\\\"text\\\\" value=\\\\"".$setting[\\\'value\\\']."\\\\" name=\\\\"upsetting[{$setting[\\\'name\\\']}]\\\\" />',		'value' => "FF0000",
		'disporder' => $i,
		'gid' => $group['gid']
	);
	$db->insert_query("settings", $insertarray);
	$i++;
	
	$insertarray = array(
		'name' => 'mybbirckeditormaxheight',
		'title' => $lang->mybbirckeditor_maxheight,
		'description' => '',
		'optionscode' => 'text',
		'value' => '450',
		'disporder' => $i,
		'gid' => $group['gid']
	);
	$db->insert_query("settings", $insertarray);
	$i++;
	
	$insertarray = array(
		'name' => 'mybbirckeditor_newthread',
		'title' => $lang->mybbirckeditor_editorinnewthread,
		'description' => '',
		'optionscode' => 'onoff',
		'value' => 1,
		'disporder' => $i,
		'gid' => $group['gid']
	);
	$db->insert_query("settings", $insertarray);
	$i++;
	$insertarray = array(
		'name' => 'mybbirckeditor_newreply',
		'title' => $lang->mybbirckeditor_editorinnewreply,
		'description' => '',
		'optionscode' => 'onoff',
		'value' => 1,
		'disporder' => $i,
		'gid' => $group['gid']
	);
	$db->insert_query("settings", $insertarray);
	$i++;
	$insertarray = array(
		'name' => 'mybbirckeditor_usercp',
		'title' => $lang->mybbirckeditor_editorinusercp,
		'description' => '',
		'optionscode' => 'onoff',
		'value' => 1,
		'disporder' => $i,
		'gid' => $group['gid']
	);
	$db->insert_query("settings", $insertarray);
	$i++;
	$insertarray = array(
		'name' => 'mybbirckeditor_private',
		'title' => $lang->mybbirckeditor_editorinpm,
		'description' => '',
		'optionscode' => 'onoff',
		'value' => 1,
		'disporder' => $i,
		'gid' => $group['gid']
	);
	$db->insert_query("settings", $insertarray);
	$i++;
	$insertarray = array(
		'name' => 'mybbirckeditor_editpost',
		'title' => $lang->mybbirckeditor_editorineditpost,
		'description' => '',
		'optionscode' => 'onoff',
		'value' => 1,
		'disporder' => $i,
		'gid' => $group['gid']
	);
	$db->insert_query("settings", $insertarray);
	$i++;
	$insertarray = array(
		'name' => 'mybbirckeditor_modcp',
		'title' => $lang->mybbirckeditor_editorinmodcp,
		'description' => '',
		'optionscode' => 'onoff',
		'value' => 1,
		'disporder' => $i,
		'gid' => $group['gid']
	);
	$db->insert_query("settings", $insertarray);
	$i++;
	$insertarray = array(
		'name' => 'mybbirckeditor_calendar',
		'title' => $lang->mybbirckeditor_editorincalendar,
		'description' => '',
		'optionscode' => 'onoff',
		'value' => 1,
		'disporder' => $i,
		'gid' => $group['gid']
	);
	$db->insert_query("settings", $insertarray);
	$i++;
	$insertarray = array(
		'name' => 'mybbirckeditor_warnings',
		'title' => $lang->mybbirckeditor_editorinwarnings,
		'description' => '',
		'optionscode' => 'onoff',
		'value' => 1,
		'disporder' => $i,
		'gid' => $group['gid']
	);
	$db->insert_query("settings", $insertarray);
	$i++;
	$insertarray = array(
		'name' => 'mybbirckeditor_quickreply',
		'title' => $lang->mybbirckeditor_editorinquickreply,
		'description' => '',
		'optionscode' => 'select
0='.$lang->mybbirckeditor_quickreplys_off.'
1='.$lang->mybbirckeditor_quickreplys_full.'
2='.$lang->mybbirckeditor_quickreplys_mini.'',
		'value' => 1,
		'disporder' => $i,
		'gid' => $group['gid']
	);
	$db->insert_query("settings", $insertarray);
	$i++;
	$insertarray = array(
		'name' => 'mybbirckeditor_enableautosave',
		'title' => $lang->mybbirckeditor_enableautosave,
		'description' => '',
		'optionscode' => 'onoff',
		'value' => 1,
		'disporder' => $i,
		'gid' => $group['gid']
	);
	$db->insert_query("settings", $insertarray);
	$i++;
	$insertarray = array(
		'name' => 'mybbirckeditor_autosave',
		'title' => $lang->mybbirckeditor_autosave,
		'description' => '',
		'optionscode' => 'text',
		'value' => 25,
		'disporder' => $i,
		'gid' => $group['gid']
	);
	$db->insert_query("settings", $insertarray);
	$i++;
	$insertarray = array(
		'name' => 'mybbirckeditor_contextmenu',
		'title' => $lang->mybbirckeditor_contextmenu,
		'description' => '',
		'optionscode' => 'onoff',
		'value' => 0,
		'disporder' => $i,
		'gid' => $group['gid']
	);
	$db->insert_query("settings", $insertarray);
	$i++;
	
	$insertarray = array(
		'name' => 'mybbirckeditor_width',
		'title' => $lang->mybbirckeditor_width,
		'description' => '',
		'optionscode' => 'text',
		'value' => '',
		'disporder' => $i,
		'gid' => $group['gid']
	);
	$db->insert_query("settings", $insertarray);
	$i++;
	
	$insertarray = array(
		'name' => 'mybbirckeditor_height',
		'title' => $lang->mybbirckeditor_height,
		'description' => '',
		'optionscode' => 'text',
		'value' => '',
		'disporder' => $i,
		'gid' => $group['gid']
	);
	$db->insert_query("settings", $insertarray);
	$i++;
	$insertarray = array(
		'name' => 'mybbirckeditor_minimenu',
		'title' => $lang->mybbirckeditor_minimenu,
		'description' => '',
		'optionscode' => 'textarea',
		'value' => $db->escape_string("[ 'Source', '-', 'Save', 'NewPage', '-', 'Undo', 'Redo' ],
[ 'Find', 'Replace', '-', 'SelectAll', 'RemoveFormat' ],
[ 'Link', 'Unlink', 'Image' ],
'/',
[ 'FontSize', 'Bold', 'Italic', 'Underline' ],
[ 'NumberedList', 'BulletedList', '-', 'Blockquote' ],
[ 'TextColor', '-', 'Smiley', 'SpecialChar', '-', 'Maximize' ]"),
		'disporder' => $i,
		'gid' => $group['gid']
	);
	$db->insert_query("settings", $insertarray);
	$i++;
	$insertarray = array(
		'name' => 'mybbirckeditor_fullmenu',
		'title' => $lang->mybbirckeditor_fullmenu,
		'description' => '',
		'optionscode' => 'textarea',
		'value' => $db->escape_string("[ 'Source', '-', 'Save', 'NewPage', 'Print', '-', 'Undo', 'Redo' ],
[ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord' ],
[ 'Find', 'Replace', '-', 'SelectAll' ],
'/',
[ 'Bold', 'Italic', 'Underline', 'Strike', '-', 'MyBBSubscript', 'MyBBSuperscript', '-', 'RemoveFormat' ],
[ 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ],
[ 'Link', 'Unlink', '-', 'Image', 'Smiley'],
[ 'SpecialChar', 'Timenow', 'Datenow', '-', 'HorizontalRule', '-', 'Videos' ],
'/',
[ 'FontSize', 'Font' ],
[ 'TextColor' ],
[ 'NumberedList', 'BulletedList', '-', 'Blockquote', 'Code', 'PhpBlock', '-', 'Table' ],
[ 'Maximize', '-', 'About' ]"),
		'disporder' => $i,
		'gid' => $group['gid']
	);
	$db->insert_query("settings", $insertarray);
	$i++;
	$insertarray = array(
		'name' => 'mybbirckeditor_toolbarlocation',
		'title' => $lang->mybbirckeditor_toolbarlocation,
		'description' => '',
		'optionscode' => 'select
top='.$lang->mybbirckeditor_top.'
bottom='.$lang->mybbirckeditor_bottom,
		'value' => top,
		'disporder' => $i,
		'gid' => $group['gid']
	);
	$db->insert_query("settings", $insertarray);
	$i++;
	rebuild_settings();
}
function mybbirckeditor_deactivate(){
	global $mybb, $db;
	$db->write_query("DELETE FROM ".TABLE_PREFIX."settings WHERE name IN(
		'mybbirckeditorswitch',
		'mybbirckeditorswitchmycode',
		'mybbirckeditorcolor',
		'mybbirckeditorskin',
		'mybbirckeditorfonts',
		'mybbirckeditormigiclinecolor',
		'mybbirckeditor_newthread',
		'mybbirckeditor_newreply',
		'mybbirckeditor_usercp',
		'mybbirckeditor_private',
		'mybbirckeditor_editpost',
		'mybbirckeditor_modcp',
		'mybbirckeditor_calendar',
		'mybbirckeditor_warnings',
		'mybbirckeditor_quickreply',
		'mybbirckeditorsmilies',
		'mybbirckeditormaxheight',
		'mybbirckeditor_enableautosave',
		'mybbirckeditor_autosave',
		'mybbirckeditor_contextmenu',
		'mybbirckeditor_width',
		'mybbirckeditor_height',
		'mybbirckeditor_minimenu',
		'mybbirckeditor_fullmenu',
		'mybbirckeditor_toolbarlocation'
	)");
	rebuild_settings();
	$db->delete_query("settinggroups", "name = 'mybbirckeditor'");
	$db->query("ALTER TABLE ".TABLE_PREFIX."users DROP ckeditor");
	include MYBB_ROOT."/inc/adminfunctions_templates.php";
	find_replace_templatesets("usercp_options", "#".preg_quote('{$ckeditor}')."#i", '', 0);
	find_replace_templatesets("showthread_quickreply", "#".preg_quote('{$smilieinserter}')."#i", '', 0);
	find_replace_templatesets("post_attachments_attachment_postinsert", "#".preg_quote('CKEDITOR.instances.message.insertText(\'[attachment={$attachment[\'aid\']}]\');')."#i", '', 0);
	find_replace_templatesets("misc_smilies_popup", "#".preg_quote('<script type="text/javascript" src="jscripts/ckinsertsmilies.js"></script>')."#i", '', 0);
	find_replace_templatesets("misc_smilies_popup_smilie", "#".preg_quote('(\'{$smilie[\'image\']}\');')."#i", '(\'{$smilie[\'insert\']}\');');	
	find_replace_templatesets("misc_smilies_popup_smilie", "#".preg_quote('ckinsertSmilie')."#i", 'insertSmilie');	
	find_replace_templatesets("showthread", "#".preg_quote('src="jscripts/thread{$threadckeditor}.js')."#i", 'src="jscripts/thread.js');	
}


function mybbirckeditor_inserteditor($id, $editor=1, $mini= 0) {
	global $cache, $headerinclude, $lang, $smiliecache, $theme, $templates, $lang, $mybb, $smiliecount;
	global $forum, $insert_announcement, $announcement, $update_announcement, $parser_options;
	$lang->load('mybbirckeditor');
	if ($editor == 1)
	{
		if($mybb->settings['smilieinserter'] != 0 && $mybb->settings['smilieinsertercols'] && $mybb->settings['smilieinsertertot'])
		{
			if(!$smiliecount)
			{
				$smilie_cache = $cache->read("smilies");
				$smiliecount = count($smilie_cache);
			}

			if(!$smiliecache)
			{
				if(!is_array($smilie_cache))
				{
					$smilie_cache = $cache->read("smilies");
				}
				foreach($smilie_cache as $smilie)
				{
					if($smilie['showclickable'] != 0)
					{
						$smiliecache[$smilie['find']] = $smilie['image'];
					}
				}
			}

			unset($smilie);

			if(is_array($smiliecache))
			{
				reset($smiliecache);

				if($mybb->settings['smilieinsertertot'] >= $smiliecount)
				{
					$mybb->settings['smilieinsertertot'] = $smiliecount;
				}
				else if($mybb->settings['smilieinsertertot'] < $smiliecount)
				{
					$smiliecount = $mybb->settings['smilieinsertertot'];
					eval("\$getmore = \"".$templates->get("smilieinsert_getmore")."\";");
				}

				$smilies1 = "";
				$smilies2 = "";
				$counter = 0;
				$i = 0;

				foreach($smiliecache as $find => $image)
				{
					if ($i < $mybb->settings['smilieinsertertot'])
					{
						$find = htmlspecialchars_uni($find);
						$smilies1 .= "'".str_replace("'","\\'",$image)."', ";
						$smilies2 .= "'".str_replace("'","\\'",$find)."', ";
						++$i;
						++$counter;
					}
				}

				$clickablesmilies = "smiley_images: [\n{$smilies1}\n],\n smiley_descriptions: [\n{$smilies2}\n]";
			}
			else
			{
				$clickablesmilies = "";
			}
		}
		else
		{
			$clickablesmilies = "";
		}
	}
	else
	{
		if($mybb->settings['smilieinserter'] != 0 && $mybb->settings['smilieinsertercols'] && $mybb->settings['smilieinsertertot'])
		{
			if(!$smiliecount)
			{
				$smilie_cache = $cache->read("smilies");
				$smiliecount = count($smilie_cache);
			}

			if(!$smiliecache)
			{
				if(!is_array($smilie_cache))
				{
					$smilie_cache = $cache->read("smilies");
				}
				foreach($smilie_cache as $smilie)
				{
					if($smilie['showclickable'] != 0)
					{
						$smiliecache[$smilie['find']] = $smilie['image'];
					}
				}
			}

			unset($smilie);

			if(is_array($smiliecache))
			{
				reset($smiliecache);

				if($mybb->settings['smilieinsertertot'] >= $smiliecount)
				{
					$mybb->settings['smilieinsertertot'] = $smiliecount;
				}
				else if($mybb->settings['smilieinsertertot'] < $smiliecount)
				{
					$smiliecount = $mybb->settings['smilieinsertertot'];
				}
				$getmore = "<tr><td class=\"trow1 smalltext\" align=\"center\"><strong><a href=\"#\" onclick=\"window.open('misc.php?action=smilies&popup=true&editor=clickableEditor', 'smilies', 'resizable=yes,status=no,location=no,toolbar=no,menubar=no,fullscreen=no,scrollbars=yes,dependent=no,width=300,left=80,height=450,top=50');\" title=\"{$lang->mybbirckeditor_moresmilies}\">{$lang->mybbirckeditor_moresmilies}</a></strong></td></tr>";

				$smilies = "";
				$counter = 0;
				$i = 0;

				foreach($smiliecache as $find => $image)
				{
					if($i < $mybb->settings['smilieinsertertot'])
					{
						if($counter == 0)
						{
							$smilies .=  "<tr>\n";
						}

						$find = htmlspecialchars_uni($find);
						$smilies .= "<td style=\"text-align: center\"><img src=\"{$image}\" border=\"0\" class=\"smilie\" alt=\"{$find}\" title=\"{$find}\" onclick=\"addsmilies('{$image}');\" /></td>\n";
						++$i;
						++$counter;

						if($counter == $mybb->settings['smilieinsertercols'])
						{
							$counter = 0;
							$smilies .= "</tr>\n";
						}
					}
				}

				if($counter != 0)
				{
					$colspan = $mybb->settings['smilieinsertercols'] - $counter;
					$smilies .= "<td colspan=\"{$colspan}\">&nbsp;</td>\n</tr>\n";
				}

				eval("\$clickablesmilies = \"".$templates->get("smilieinsert")."\";");
			}
			else
			{
				$clickablesmilies = "";
			}
		}
		else
		{
			$clickablesmilies = "";
		}
	}
	if (!$mybb->settings['mybbirckeditor_contextmenu'])
	{
		$contextmenu = 'contextmenu,';
	}
	else
	{
		$contextmenu = '';
	}
	if ($mini == 1)
	{
		if (!strstr($headerinclude,"<script src=\"{$mybb->settings['bburl']}/ckeditormini/ckeditor.js?time=29-01-2013-5-57\"></script>"))
		{
			$headerinclude .= "<script src=\"{$mybb->settings['bburl']}/ckeditormini/ckeditor.js?time=29-01-2013-5-57\"></script>";
		}
			$codebuttons = "
				<script>
					function addsmilies(code)
					{
						htmlcode = '<img src=\"'+code+'\" />';
						if (CKEDITOR.instances.{$id}.mode == 'wysiwyg')
						{
							CKEDITOR.instances.{$id}.insertHtml(htmlcode);
						}
					}
					CKEDITOR.replace( '{$id}', {
						";
						if($mybb->settings['mybbirckeditorswitchmycode'] == 1)
						$codebuttons .= "
					enterMode:2,
					toolbarCanCollapse: true,
					specialChars: [' ', '!', '&quot;', '#', '$', '%', '&amp;', \"'\", '(', ')', '*', '+', '-', '.', '/', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', ':', ';', '&lt;', '=', '&gt;', '?', '@', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '[', ']', '^', '_', '`', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '{', '|', '}', '~', \"&euro;\", \"&lsquo;\", \"&rsquo;\", \"&ldquo;\", \"&rdquo;\", \"&ndash;\", \"&mdash;\", \"&iexcl;\", \"&cent;\", \"&pound;\", \"&curren;\", \"&yen;\", \"&brvbar;\", \"&sect;\", \"&uml;\", \"&copy;\", \"&ordf;\", \"&laquo;\", \"&not;\", \"&reg;\", \"&macr;\", \"&deg;\", \"&sup2;\", \"&sup3;\", \"&acute;\", \"&micro;\", \"&para;\", \"&middot;\", \"&cedil;\", \"&sup1;\", \"&ordm;\", \"&raquo;\", \"&frac14;\", \"&frac12;\", \"&frac34;\", \"&iquest;\", \"&Agrave;\", \"&Aacute;\", \"&Acirc;\", \"&Atilde;\", \"&Auml;\", \"&Aring;\", \"&AElig;\", \"&Ccedil;\", \"&Egrave;\", \"&Eacute;\", \"&Ecirc;\", \"&Euml;\", \"&Igrave;\", \"&Iacute;\", \"&Icirc;\", \"&Iuml;\", \"&ETH;\", \"&Ntilde;\", \"&Ograve;\", \"&Oacute;\", \"&Ocirc;\", \"&Otilde;\", \"&Ouml;\", \"&times;\", \"&Oslash;\", \"&Ugrave;\", \"&Uacute;\", \"&Ucirc;\", \"&Uuml;\", \"&Yacute;\", \"&THORN;\", \"&szlig;\", \"&agrave;\", \"&aacute;\", \"&acirc;\", \"&atilde;\", \"&auml;\", \"&aring;\", \"&aelig;\", \"&ccedil;\", \"&egrave;\", \"&eacute;\", \"&ecirc;\", \"&euml;\", \"&igrave;\", \"&iacute;\", \"&icirc;\", \"&iuml;\", \"&eth;\", \"&ntilde;\", \"&ograve;\", \"&oacute;\", \"&ocirc;\", \"&otilde;\", \"&ouml;\", \"&divide;\", \"&oslash;\", \"&ugrave;\", \"&uacute;\", \"&ucirc;\", \"&uuml;\", \"&yacute;\", \"&thorn;\", \"&yuml;\", \"&OElig;\", \"&oelig;\", \"&#372;\", \"&#374\", \"&#373\", \"&#375;\", \"&sbquo;\", \"&#8219;\", \"&bdquo;\", \"&hellip;\", \"&trade;\", \"&#9658;\", \"&bull;\", \"&rarr;\", \"&rArr;\", \"&hArr;\", \"&diams;\", \"&asymp;\", '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹', '۰', 'ا', 'ب', 'پ', 'ت', 'ث', 'ج', 'چ', 'ح', 'خ', 'د', 'ذ', 'ر', 'ز', 'ژ', 'س', 'ش', 'ص', 'ض', 'ط', 'ظ', 'ع', 'غ', 'ف', 'ق', 'ک', 'گ', 'ل', 'م', 'ن', 'و', 'ه', 'ی', 'ي', 'ك'],
					defaultLanguage: '{$lang->mybbirckeditor_lang}',
					baseHref: '{$mybb->settings['bburl']}/',
					language: '{$lang->mybbirckeditor_lang}',
						removePlugins: '{$contextmenu}bidi,dialogadvtab,div,filebrowser,flash,format,forms,iframe,liststyle,pagebreak,showborders,stylescombo,table,tabletools,templates',
						extraPlugins: 'magicline,bbcode',
						uiColor: '#{$mybb->settings['mybbirckeditorcolor']}',
						toolbar: [
							{$mybb->settings['mybbirckeditor_minimenu']}
						],
						";
						if($mybb->settings['mybbirckeditor_width']){$codebuttons.="width: '{$mybb->settings['mybbirckeditor_width']}',";}
						if($mybb->settings['mybbirckeditor_height']){$codebuttons.="height: '{$mybb->settings['mybbirckeditor_height']}',";}
						$codebuttons.="toolbarLocation: '{$mybb->settings['mybbirckeditor_toolbarlocation']}',
						resize_maxHeight: '{$mybb->settings['mybbirckeditormaxheight']}',
						// Define font sizes in percent values.

						skin: '{$mybb->settings['mybbirckeditorskin']}, {$mybb->settings['bburl']}/ckeditor/skins/{$mybb->settings['mybbirckeditorskin']}/',
						// Strip CKEditor smileys to those commonly used in BBCode.
					});
				</script>";
			if($mybb->settings['mybbirckeditor_enableautosave'])
			{
				$codebuttons .="<div id=\"autosave\" style=\"background:#f7f7f7;border:1px solid #cccccc;padding:3px;margin:5px 0;\" class=\"smalltext\"></div>
				<script>
					function setautosave()
					{
						CKEDITOR.instances.{$id}.setData(Cookie.get('mybbirckeditormessage'));
						Cookie.set('mybbirckeditormessage', '');
						$('autosave').style.display = 'none';
					}
					function checkautosave()
					{
						myWindow=window.open('','','width=300,height=180');
						myWindow.document.write('<!DOCTYPE html><html dir=\"rtl\"><head><title>مشاهده‌ی متن ذخیره شده</title><style>body { font-family:Tahoma;font-size:11px;text-align:center;}</style></head><body><a href=\"javascript:window.close();\">بســـــــتن پنجـــره</a><br><br><textarea style=\"width:95%;height:125px;font-family:Tahoma;text-align:right;\">'+Cookie.get('mybbirckeditormessage')+'</textarea></body></html>');
						myWindow.focus();
					}
					function autosaverefresh() {
						Cookie.set('mybbirckeditormessage',	CKEDITOR.instances.{$id}.getData());
						//alert(CKEDITOR.instances.{$id}.getData());
						$('autosave').style.display = 'none';
						$('autosave').innerHTML = '';
						setTimeout(function(){
							if(Cookie.get('mybbirckeditormessage'))
							{
								$('autosave').style.display = 'block';
								$('autosave').innerHTML = '{$lang->mybbirckeditor_autosavesaved} (<a href=\"javascript:setautosave();\">{$lang->mybbirckeditor_restoreautosave}</a> - <a href=\"javascript:checkautosave();\">{$lang->mybbirckeditor_previewautosave}</a> - <a href=\"javascript:removeautosave();autosaverefresh();\">{$lang->mybbirckeditor_removeautosave}</a>)';
							}
						},500);
						setTimeout(function() {autosaverefresh()}, {$mybb->settings['mybbirckeditor_autosave']}000);
					}
					function autosave()
					{
						setautosave();
						setTimeout(function() {autosaverefresh()}, {$mybb->settings['mybbirckeditor_autosave']}000);
						
					}
					
					function removeautosave() {
						Cookie.set('mybbirckeditormessage',	'');
						$('autosave').style.display = 'none';
						$('autosave').innerHTML = '';
					}
					if ((Cookie.get('mybbirckeditormessage')) && Cookie.get('mybbirckeditormessage') != CKEDITOR.instances.{$id}.getData(1))
					{
						$('autosave').style.display = 'block';
						$('autosave').innerHTML = '{$lang->mybbirckeditor_autosavefound} (<a href=\"javascript:setautosave();\">{$lang->mybbirckeditor_restoreautosave}</a> - <a href=\"javascript:checkautosave();\">{$lang->mybbirckeditor_previewautosave}</a> - <a href=\"javascript:removeautosave();autosaverefresh();\">{$lang->mybbirckeditor_removeautosave}</a>)';
					}
					else
					{
						autosaverefresh();
					}
				</script>";
			}
	}
	else
	{
		if (!strstr($headerinclude,"<script src=\"{$mybb->settings['bburl']}/ckeditor/ckeditor.js?time=29-01-2013-5-57\"></script>"))
		{
			$headerinclude .= "<script src=\"{$mybb->settings['bburl']}/ckeditor/ckeditor.js?time=29-01-2013-5-57\"></script>";
		}
		$codebuttons = "
			<script>
				function addsmilies(code)
				{
					htmlcode = '<img src=\"'+code+'\" />';
					if (CKEDITOR.instances.{$id}.mode == 'wysiwyg')
					{
						CKEDITOR.instances.{$id}.insertHtml(htmlcode);
					}
				}
				CKEDITOR.replace( '{$id}', {
					extraPlugins: '";
					if($mybb->settings['mybbirckeditorswitchmycode'] == 1)
						$codebuttons .= "bbcode,";
					$codebuttons .= "magicline,code,videos,phpblock,mybbbasicstyles,timenow',
					enterMode:2,
					toolbarCanCollapse: true,
					defaultLanguage: '{$lang->mybbirckeditor_lang}',
					baseHref: '{$mybb->settings['bburl']}/',
					language: '{$lang->mybbirckeditor_lang}',
					uiColor: '#{$mybb->settings['mybbirckeditorcolor']}',
					magicline_color: '#{$mybb->settings['mybbirckeditormigiclinecolor']}',
					// Remove unused plugins.
					removePlugins: '{$contextmenu}bidi,dialogadvtab,div,filebrowser,flash,format,forms,iframe,liststyle,pagebreak,showborders,stylescombo,tabletools,templates',
					// Width and height are not supported in the BBCode format, so object resizing is disabled.
					disableObjectResizing: true,
					";
					if($mybb->settings['mybbirckeditor_width']){$codebuttons.="width: '{$mybb->settings['mybbirckeditor_width']}',";}
					if($mybb->settings['mybbirckeditor_height']){$codebuttons.="height: '{$mybb->settings['mybbirckeditor_height']}',";}
					$codebuttons.="toolbarLocation: '{$mybb->settings['mybbirckeditor_toolbarlocation']}',
					resize_maxHeight: '{$mybb->settings['mybbirckeditormaxheight']}',
					// Define font sizes in percent values.
					fontSize_sizes: \"{$lang->mybbirckeditor_xxsmall}/xx-small;{$lang->mybbirckeditor_xsmall}/x-small;{$lang->mybbirckeditor_small}/small;{$lang->mybbirckeditor_medium}/medium;{$lang->mybbirckeditor_large}/large;{$lang->mybbirckeditor_xlarge}/x-large;{$lang->mybbirckeditor_xxlarge}/xx-large\",
					font_names: \"".str_replace('"','\\"',$mybb->settings['mybbirckeditorfonts'])."\",
					toolbar: [
						{$mybb->settings['mybbirckeditor_fullmenu']}
					],
					specialChars: [' ', '!', '&quot;', '#', '$', '%', '&amp;', \"'\", '(', ')', '*', '+', '-', '.', '/', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', ':', ';', '&lt;', '=', '&gt;', '?', '@', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '[', ']', '^', '_', '`', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '{', '|', '}', '~', \"&euro;\", \"&lsquo;\", \"&rsquo;\", \"&ldquo;\", \"&rdquo;\", \"&ndash;\", \"&mdash;\", \"&iexcl;\", \"&cent;\", \"&pound;\", \"&curren;\", \"&yen;\", \"&brvbar;\", \"&sect;\", \"&uml;\", \"&copy;\", \"&ordf;\", \"&laquo;\", \"&not;\", \"&reg;\", \"&macr;\", \"&deg;\", \"&sup2;\", \"&sup3;\", \"&acute;\", \"&micro;\", \"&para;\", \"&middot;\", \"&cedil;\", \"&sup1;\", \"&ordm;\", \"&raquo;\", \"&frac14;\", \"&frac12;\", \"&frac34;\", \"&iquest;\", \"&Agrave;\", \"&Aacute;\", \"&Acirc;\", \"&Atilde;\", \"&Auml;\", \"&Aring;\", \"&AElig;\", \"&Ccedil;\", \"&Egrave;\", \"&Eacute;\", \"&Ecirc;\", \"&Euml;\", \"&Igrave;\", \"&Iacute;\", \"&Icirc;\", \"&Iuml;\", \"&ETH;\", \"&Ntilde;\", \"&Ograve;\", \"&Oacute;\", \"&Ocirc;\", \"&Otilde;\", \"&Ouml;\", \"&times;\", \"&Oslash;\", \"&Ugrave;\", \"&Uacute;\", \"&Ucirc;\", \"&Uuml;\", \"&Yacute;\", \"&THORN;\", \"&szlig;\", \"&agrave;\", \"&aacute;\", \"&acirc;\", \"&atilde;\", \"&auml;\", \"&aring;\", \"&aelig;\", \"&ccedil;\", \"&egrave;\", \"&eacute;\", \"&ecirc;\", \"&euml;\", \"&igrave;\", \"&iacute;\", \"&icirc;\", \"&iuml;\", \"&eth;\", \"&ntilde;\", \"&ograve;\", \"&oacute;\", \"&ocirc;\", \"&otilde;\", \"&ouml;\", \"&divide;\", \"&oslash;\", \"&ugrave;\", \"&uacute;\", \"&ucirc;\", \"&uuml;\", \"&yacute;\", \"&thorn;\", \"&yuml;\", \"&OElig;\", \"&oelig;\", \"&#372;\", \"&#374\", \"&#373\", \"&#375;\", \"&sbquo;\", \"&#8219;\", \"&bdquo;\", \"&hellip;\", \"&trade;\", \"&#9658;\", \"&bull;\", \"&rarr;\", \"&rArr;\", \"&hArr;\", \"&diams;\", \"&asymp;\", '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹', '۰', 'ا', 'ب', 'پ', 'ت', 'ث', 'ج', 'چ', 'ح', 'خ', 'د', 'ذ', 'ر', 'ز', 'ژ', 'س', 'ش', 'ص', 'ض', 'ط', 'ظ', 'ع', 'غ', 'ف', 'ق', 'ک', 'گ', 'ل', 'م', 'ن', 'و', 'ه', 'ی', 'ي', 'ك'],
					smiley_columns: '{$mybb->settings['smilieinsertercols']}',
					skin: '{$mybb->settings['mybbirckeditorskin']}, {$mybb->settings['bburl']}/ckeditor/skins/{$mybb->settings['mybbirckeditorskin']}/',
					// Strip CKEditor smileys to those commonly used in BBCode.
					{$clickablesmilies}
					
				});
 			</script>";
			if($mybb->settings['mybbirckeditor_enableautosave'])
			{
				$codebuttons .="<div id=\"autosave\" style=\"background:#f7f7f7;border:1px solid #cccccc;padding:3px;margin:5px 0;\" class=\"smalltext\"></div>
				<script>
					function setautosave()
					{
						CKEDITOR.instances.{$id}.setData(Cookie.get('mybbirckeditormessage'));
						Cookie.set('mybbirckeditormessage', '');
						$('autosave').style.display = 'none';
					}
					function checkautosave()
					{
						myWindow=window.open('','','width=300,height=180');
						myWindow.document.write('<!DOCTYPE html><html dir=\"rtl\"><head><title>مشاهده‌ی متن ذخیره شده</title><style>body { font-family:Tahoma;font-size:11px;text-align:center;}</style></head><body><a href=\"javascript:window.close();\">بســـــــتن پنجـــره</a><br><br><textarea style=\"width:95%;height:125px;font-family:Tahoma;text-align:right;\">'+Cookie.get('mybbirckeditormessage')+'</textarea></body></html>');
						myWindow.focus();
					}
					function autosaverefresh() {
						Cookie.set('mybbirckeditormessage',	CKEDITOR.instances.{$id}.getData());
						//alert(CKEDITOR.instances.{$id}.getData());
						$('autosave').style.display = 'none';
						$('autosave').innerHTML = '';
						setTimeout(function(){
							if(Cookie.get('mybbirckeditormessage'))
							{
								$('autosave').style.display = 'block';
								$('autosave').innerHTML = '{$lang->mybbirckeditor_autosavesaved} (<a href=\"javascript:setautosave();\">{$lang->mybbirckeditor_restoreautosave}</a> - <a href=\"javascript:checkautosave();\">{$lang->mybbirckeditor_previewautosave}</a> - <a href=\"javascript:removeautosave();autosaverefresh();\">{$lang->mybbirckeditor_removeautosave}</a>)';
							}
						},500);
						setTimeout(function() {autosaverefresh()}, {$mybb->settings['mybbirckeditor_autosave']}000);
					}
					function autosave()
					{
						setautosave();
						setTimeout(function() {autosaverefresh()}, {$mybb->settings['mybbirckeditor_autosave']}000);
						
					}
					
					function removeautosave() {
						Cookie.set('mybbirckeditormessage',	'');
						$('autosave').style.display = 'none';
						$('autosave').innerHTML = '';
					}
					if ((Cookie.get('mybbirckeditormessage')) && Cookie.get('mybbirckeditormessage') != CKEDITOR.instances.{$id}.getData(1))
					{
						$('autosave').style.display = 'block';
						$('autosave').innerHTML = '{$lang->mybbirckeditor_autosavefound} (<a href=\"javascript:setautosave();\">{$lang->mybbirckeditor_restoreautosave}</a> - <a href=\"javascript:checkautosave();\">{$lang->mybbirckeditor_previewautosave}</a> - <a href=\"javascript:removeautosave();autosaverefresh();\">{$lang->mybbirckeditor_removeautosave}</a>)';
					}
					else
					{
						autosaverefresh();
					}
				</script>";
			}
	}
	mybbirckeditor_html();
	if ($editor == 1)
		return $codebuttons; 
	else
		return $clickablesmilies;
}

function mybbirckeditorglobaltag($page) {
	global $mybb;
	if (!$mybb->user['ckeditor']) { return false;}
	while (preg_match('/<mybbirckeditor>.*?<\/[\s]*mybbirckeditor>/s', $page, $function))
	{
		$myfunction = str_replace(array('<mybbirckeditor>','</mybbirckeditor>'),array('',''),$function[0]);
		//echo $function[0];
		$page = str_replace($function[0], "<script src=\"{$mybb->settings['bburl']}/ckeditor/ckeditor.js?time=29-01-2013-5-57\"></script>".mybbirckeditor_inserteditor($myfunction), $page);
		unset($myfunction);
	}
	while (preg_match('/<mybbirckeditor=smilies>.*?<\/[\s]*mybbirckeditor>/s', $page, $function))
	{
		$myfunction = str_replace(array('<mybbirckeditor=smilies>','</mybbirckeditor>'),array('',''),$function[0]);
		$page = str_replace($function[0], mybbirckeditor_inserteditor($myfunction,0), $page);
		unset($myfunction);
	}
	while (preg_match('/<mybbirckeditor=mini>.*?<\/[\s]*mybbirckeditor>/s', $page, $function))
	{
		$myfunction = str_replace(array('<mybbirckeditor=mini>','</mybbirckeditor>'),array('',''),$function[0]);
		$page = str_replace($function[0], "<script src=\"{$mybb->settings['bburl']}/ckeditormini/ckeditor.js?time=29-01-2013-5-57\"></script>".mybbirckeditor_inserteditor($myfunction,1,1), $page);
		unset($myfunction);
	}

	return $page;
}

function mybbirckeditor_warn() {
	global $cache, $smiliecache, $theme, $message, $templates, $lang, $mybb, $smiliecount, $codebuttons, $smilieinserter, $pm_notify;
	if (!$mybb->user['ckeditor']) { return false;}
	if ($mybb->settings['mybbirckeditorswitch'] == 1)
	{
		$lang->load("warnings");
		$codebuttons = mybbirckeditor_inserteditor("message");
		$message .='

 &nbsp;';
		if ($mybb->settings['mybbirckeditorsmilies'] == 1)
		{
			$smilieinserter = mybbirckeditor_inserteditor("message",0);
		}
		else
		{
			$smilieinserter = '';
		}
		eval("\$pm_notify = \"".$templates->get("warnings_warn_pm")."\";");
	}
}

function mybbirckeditor_ucpoptions(){
	global $user,$mybb,$lang,$ckeditor;
	$lang->load('mybbirckeditor');
	if($user['ckeditor'] == '1')
	{
		$ckeditorenable = "checked=\"checked\"";
	}
	else
	{
		$ckeditorenable = '';
	}
	//echo $user['ckeditor'];
	$ckeditor = '<tr>
<td valign="top" width="1"><input type="checkbox" class="checkbox" name="ckeditor" id="ckeditor" value="1" '.$ckeditorenable.' /></td>
<td><span class="smalltext"><label for="ckeditor">'.$lang->mybbirckeditor_userckeditor.'</label></span></td>
</tr>';
}

function mybbirckeditor_ucpdooptions() {
	global $mybb, $user, $userhandler;
	$user['options']['ckeditor'] = $mybb->input['ckeditor'];
	$userhandler->set_data($user);
	if(!$userhandler->validate_user())
	{
		return false;
	}
	$userhandler->update_user();
}

function mybbirckeditor() {
	global $cache, $smiliecache, $theme, $message, $templates, $lang, $mybb, $smiliecount, $codebuttons, $smilieinserter, $message;
	if (!$mybb->user['ckeditor']) { return false;}
	$codebuttons = mybbirckeditor_inserteditor("message");
		$message .='

 &nbsp;';
	if ($mybb->settings['mybbirckeditorsmilies'] == 1)
	{
		$smilieinserter = mybbirckeditor_inserteditor("message",0);
	}
	else
	{
		$smilieinserter = '';
	}
}


function mybbirckeditor_signature() {
	global $cache, $smiliecache, $theme, $templates, $lang, $mybb, $smiliecount, $codebuttons, $smilieinserter;
	if (!$mybb->user['ckeditor']) { return false;}
	if ($mybb->settings['mybbirckeditorswitch'] == 1)
	{
		$codebuttons = mybbirckeditor_inserteditor("signature");
		if ($mybb->settings['mybbirckeditorsmilies'] == 1)
		{
			$smilieinserter = mybbirckeditor_inserteditor("signature",0);
		}
		else
		{
			$smilieinserter = '';
		}
	}
}

function mybbirckeditorthread() {
	global $quickreply, $threadckeditor;
	global $cache, $smiliecache, $theme, $templates, $lang, $mybb, $smiliecount, $codebuttons, $smilieinserter;
	if (!$mybb->user['ckeditor']) { return false;}
	if ($mybb->settings['mybbirckeditorswitch'] == 1)
	{
		if ($mybb->settings['mybbirckeditor_quickreply'] == 2)
		{
			$codebuttons = mybbirckeditor_inserteditor("message", 1, 1);
		}
		else
		{
			$codebuttons = mybbirckeditor_inserteditor("message");
		}
		$threadckeditor = 'forckeditor';
		if ($mybb->settings['mybbirckeditorsmilies'] == 1)
		{
			$smilieinserter = mybbirckeditor_inserteditor("message",0);
			$smilieinserter .= '<br /><br />';
		}
		else
		{
			$smilieinserter = '';
		}
		$quickreply = str_replace('</textarea>', '</textarea>'.$codebuttons, $quickreply);
	}
}
function mybbirckeditor_parser($message) {
	global $mybb,$forum;
	if ($mybb->settings['mybbirckeditorswitch'] == 1)
	{
			$tp = new tableparser;
			$parser = new postParser;
			$pattern = array(
				"#\[quote=([\"']|&quot;|)([\"']|&quot;|)(.*?)(?:\\2)(.*?)(?:[\"']|&quot;)?(?:\\1)\](.*?)\[/quote\](\r\n?|\n?)#esi",
			);
			$replace = array(
				"\$parser->mycode_parse_post_quotes('$4','$2$3')"
			);
			do
			{
				$previous_message = $message;
				$message = preg_replace($pattern, $replace, $message, -1, $count);
			} while($count);

			if(!$message)
			{
				$message = $previous_message;
			}
			$pattern = array(
				"#\[table\](.*?)\[/table\]#esi",
			);
			$replace = array(
				"\$tp->table('$1')"
			);
			do
			{
				$previous_message = $message;
				$message = preg_replace($pattern, $replace, $message, -1, $count);
			} while($count);

			if(!$message)
			{
				$message = $previous_message;
			}
			$message = preg_replace("#\[/(b|s|u|sub|sup|i|code)\]\[\\1\]#i", "", $message);
			$message = preg_replace("#\[img\](.*?)\[/img\]#i", "<img src=\"$1\" alt=\"تصویر: $1\" title=\"تصویر: $1\" />", $message);		
			$message = preg_replace("#\[font=(.*?)\](.*?)\[/font\]#i", "<span style=\"font-family:$1;\">$2</span>", $message);
			$message = preg_replace("#\[align=(.*?)\](.*?)\[/align\]#i", "<div style=\"text-align:$1;\">$2</div>", $message);
			$message = preg_replace("#\[font\](.*?)\[/font\]#i", "$1", $message);
			$message = preg_replace("#\[align\](.*?)\[/align\]#i", "$1", $message);
			$message = preg_replace("#\[size\](.*?)\[/size\]#i", "$1", $message);
			$message = preg_replace("#\[color\](.*?)\[/color\]#i", "$1", $message);
			$message = preg_replace("#\[size\](.*?)\[/size\]#i", "$1", $message);
			$message = preg_replace("#\[sub\](.*?)\[/sub\]#i", "<sub>$1</sub>", $message);
			$message = preg_replace("#\[sup\](.*?)\[/sup\]#i", "<sup>$1</sup>", $message);
			$message = str_replace(array('[font]','[/font]','[align=center]','[align=right]','[align=left]','[align=justify]','[/align]','[sub]','[/sub]','[sup]','[/sup]','[b]','[/b]','[s]','[/s]','[u]','[u]','[i]','[/i]','[url]','[/url]','[img]','[/img]','[hr]','[size]','[/size]','[color]','[/color]','[list]','[/list]','[quote]','[/quote]','[code]','[/code]','[php]','[/php]','[table]','[/table]'),'',$message);
	}

	return $message;
}

function mybbirckeditor_html()
{
	global $mybb, $forum, $insert_announcement, $announcement, $update_announcement, $parser_options;
	if($mybb->settings['mybbirckeditorswitchmycode'] != 1)
	{
		if(isset($forum)){$forum['allowhtml'] = 1;}
		if(isset($insert_announcement)){$insert_announcement['allowhtml'] = 1;}
		if(isset($announcement)){$announcement['allowhtml'] = 1;}
		if(isset($update_announcement)){$update_announcement['allowhtml'] = 1;}
		if(isset($parser_options)){$parser_options['allowhtml'] = 1;}
	}
}

class tableparser {
	public function table($message)
	{
		$pattern = array(
			"#\[tr\](.*?)\[/tr\]#esi",
		);
		$replace = array(
			"\$this->tr('$1')"
		);
		do
		{
			$previous_message = $message;
			$message = preg_replace($pattern, $replace, $message, -1, $count);
		} while($count);
		if(!$message)
		{
			$message = $previous_message;
		}

		return "<table style=\"background:#000000;border:1px #000000 solid;margin:15px;width:95%;\">".$message."</table>";
	}
	
	public function tr($message)
	{
			$message = preg_replace("#\[td\](.*?)\[/td\]#i", "<td style=\"background:#ffffff;padding:5px;\">$1</td>", $message);
			$message = preg_replace("#\[th\](.*?)\[/th\]#i", "<th style=\"background:#ffffff;padding:5px;\">$1</th>", $message);
			return "<tr>".$message."</tr>";
	}
}

?>