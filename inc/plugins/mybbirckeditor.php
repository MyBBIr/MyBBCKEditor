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
	$plugins->add_hook('admin_forum_action_handler','mybbirckeditor_admin_action');
	$plugins->add_hook('admin_forum_menu','mybbirckeditor_admin_menu');
	$plugins->add_hook('admin_load','mybbirckeditor_admin');
	$plugins->add_hook("global_end", "mybbirckeditor_global");
	$plugins->add_hook("usercp_options_end", "mybbirckeditor_ucpoptions");
	$plugins->add_hook("usercp_do_options_end", "mybbirckeditor_ucpdooptions");
	$plugins->add_hook("pre_output_page", "mybbirckeditorglobaltag");
	$plugins->add_hook("admin_page_output_footer", "mybbirckeditoradmin");
	$plugins->add_hook("showthread_start","mybbirckeditorthread");
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
		"version"		=> "<span style=\"font-size:11px;\">(آزمایشی)</span> 2.9",
		"guid" 			=> "",
		"compatibility" => "*"
	);
}


function mybbirckeditor_activate(){
	global $mybb, $db, $lang;
	$lang->load('admin_mybbirckeditor');
	if($db->table_exists('ckeditor'))
	{
		$db->query("DROP TABLE ".TABLE_PREFIX."ckeditor");
	}
	$q = "CREATE TABLE IF NOT EXISTS ".TABLE_PREFIX."ckeditor (
  cid int(10) NOT NULL AUTO_INCREMENT,
  cname text NOT NULL,
  citem varchar(200) NOT NULL,
  cparent int(10) NOT NULL DEFAULT '0',
  corder int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (cid)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=119 ;";
	$db->query($q);
	$q = "INSERT INTO mybb_ckeditor (cid, cname, citem, cparent, corder) VALUES
(58, 'document', '', 0, 1),
(59, 'clipboard', '', 0, 2),
(60, 'editing', '', 0, 3),
(61, 'basicstyles', '', 0, 4),
(62, 'paragraph', '', 0, 5),
(63, 'links', '', 0, 6),
(64, 'insert', '', 0, 7),
(65, 'styles', '', 0, 8),
(66, 'colors', '', 0, 9),
(67, 'blocks', '', 0, 10),
(68, '{$lang->Source}', 'Source', 58, 1),
(69, '{$lang->dash}', '-', 58, 2),
(70, '{$lang->Save}', 'Save', 58, 3),
(71, '{$lang->NewPage}', 'NewPage', 58, 4),
(72, '{$lang->Cut}', 'Cut', 59, 1),
(73, '{$lang->Copy}', 'Copy', 59, 2),
(74, '{$lang->PasteText}', 'PasteText', 59, 3),
(75, '{$lang->PasteFromWord}', 'PasteFromWord', 59, 4),
(76, '{$lang->dash}', '-', 59, 5),
(77, '{$lang->Undo}', 'Undo', 59, 6),
(78, '{$lang->Redo}', 'Redo', 59, 7),
(79, '{$lang->Find}', 'Find', 60, 1),
(80, '{$lang->Replace}', 'Replace', 60, 2),
(81, '{$lang->SelectAll}', 'SelectAll', 60, 3),
(82, '{$lang->Bold}', 'Bold', 61, 1),
(83, '{$lang->Italic}', 'Italic', 61, 2),
(84, '{$lang->Underline}', 'Underline', 61, 3),
(85, '{$lang->Strike}', 'Strike', 61, 4),
(86, '{$lang->Subscript}', 'Subscript', 61, 5),
(87, '{$lang->Superscript}', 'Superscript', 61, 6),
(88, '{$lang->dash}', '-', 61, 7),
(89, '{$lang->RemoveFormat}', 'RemoveFormat', 61, 8),
(90, '{$lang->NumberedList}', 'NumberedList', 62, 1),
(91, '{$lang->BulletedList}', 'BulletedList', 62, 2),
(92, '{$lang->dash}', '-', 62, 3),
(93, '{$lang->Outdent}', 'Outdent', 62, 4),
(94, '{$lang->Indent}', 'Indent', 62, 5),
(95, '{$lang->Link}', 'Link', 63, 1),
(96, '{$lang->Unlink}', 'Unlink', 63, 2),
(97, '{$lang->Image}', 'Image', 64, 1),
(98, '{$lang->Smiley}', 'Smiley', 64, 2),
(99, '{$lang->dash}', '-', 64, 3),
(100, '{$lang->SpecialChar}', 'SpecialChar', 64, 4),
(101, '{$lang->HorizontalRule}', 'HorizontalRule', 64, 5),
(102, '{$lang->Font}', 'Font', 65, 1),
(103, '{$lang->FontSize}', 'FontSize', 65, 2),
(104, '{$lang->JustifyLeft}', 'JustifyLeft', 65, 3),
(105, '{$lang->JustifyCenter}', 'JustifyCenter', 65, 4),
(106, '{$lang->JustifyRight}', 'JustifyRight', 65, 5),
(107, '{$lang->JustifyBlock}', 'JustifyBlock', 65, 6),
(NULL, '{$lang->dash}', '-', 65, 7),
(NULL, '{$lang->BidiLtr}', 'BidiLtr', 65, 8),
(NULL, '{$lang->BidiRtl}', 'BidiRtl', 65, 9),
(109, '{$lang->TextColor}', 'TextColor', 66, 1),
(110, '{$lang->BGColor}', 'BGColor', 66, 2),
(111, 'other', '', 0, 11),
(112, '{$lang->Blockquote}', 'Blockquote', 67, 1),
(113, '{$lang->dash}', '-', 67, 2),
(114, '{$lang->Code}', 'Code', 67, 3),
(115, '{$lang->PhpBlock}', 'PhpBlock', 67, 4),
(116, '{$lang->Table}', 'Table', 67, 5),
(117, '{$lang->Maximize}', 'Maximize', 111, 1),
(118, '{$lang->Videos}', 'Videos', 67, 6);";
	$db->query($q);
	if($db->field_exists('ckeditor','users')) {
		$db->query("ALTER TABLE ".TABLE_PREFIX."users DROP ckeditor");
	}
	if(!$db->field_exists('ckeditor','users')) {
		$db->write_query('ALTER TABLE '.TABLE_PREFIX.'users ADD ckeditor INT(1) NOT NULL DEFAULT \'1\';');
	}
	$db->delete_query("datacache", "title = 'cketoolbar'");
	include MYBB_ROOT."/inc/adminfunctions_templates.php";
	find_replace_templatesets("usercp_options", "#".preg_quote('{$ckeditor}')."#is", '', 0);
	find_replace_templatesets("showthread_quickreply", "#".preg_quote('{$smilieinserter}')."#is", '', 0);
	find_replace_templatesets("showthread_quickreply", "#".preg_quote('{$codebuttons}')."#is", '', 0);
	find_replace_templatesets("usercp_options", "#".preg_quote('<tr>
<td colspan="2"><span class="smalltext">{$lang->style}</span></td>')."#is", '{$ckeditor}<tr>
<td colspan="2"><span class="smalltext">{$lang->style}</span></td>');	
	find_replace_templatesets("misc_smilies_popup_smilie", "#".preg_quote('(\'{$smilie[\'insert\']}\');')."#is", '(\'<img src=&quot;{$smilie[\'image\']}&quot; />\');');	
	find_replace_templatesets("showthread", "#".preg_quote('src="jscripts/thread.js')."#is", 'src="jscripts/thread{$threadckeditor}.js');	
	find_replace_templatesets("showthread_quickreply", "#".preg_quote('{$lang->message_note}<br /><br />')."#is", '{$lang->message_note}<br /><br />{$smilieinserter}');	
	find_replace_templatesets("showthread_quickreply", "#</textarea>#i", "</textarea>\n{\$codebuttons}");
	$data = array(
		'title' => 'cketoolbar',
		'cache' => ''
	);
	$db->insert_query("datacache", $data);
	reloade_cache_cketoolbar();
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
/*
	$insertarray = array(
		'name' => 'smilieinserter',
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
	$i++;*/
	$insertarray = array(
		'name' => 'mybbirckeditorcolor',
		'title' => $lang->mybbirckeditor_editorcolor,
		'description' => '<script type="text/javascript" src="../ckeditor/jscolor/jscolor.js"></script>',
		'optionscode' => 'php
<input class=\\\\"text_input color\\\\" type=\\\\"text\\\\" value=\\\\"".$setting[\\\'value\\\']."\\\\" name=\\\\"upsetting[{$setting[\\\'name\\\']}]\\\\" />',
		'value' => "E3EBF2",
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
		'value' => 'moono',
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
		'value' => 1,
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
		'value' => $db->escape_string("[ 'Source', '-', 'Bold', 'Italic', 'Underline', '-', 'RemoveFormat' ],
[ 'Link', 'Unlink', 'Image', '-', 'NumberedList', 'BulletedList', '-', 'Blockquote' ]"),
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
[ 'Find', 'Replace', '-', 'TransformTextToUppercase', 'TransformTextToLowercase', 'TransformTextCapitalize', 'TransformTextSwitcher', '-', 'SelectAll' ],
'/',
[ 'Bold', 'Italic', 'Underline', 'Strike', '-', 'Subscript', 'Superscript', '-', 'RemoveFormat' ],
[ 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl' ],
[ 'Link', 'Unlink', '-', 'Image', 'Smiley'],
[ 'SpecialChar', 'Timenow', 'Datenow', '-', 'HorizontalRule', '-', 'Videos' ],
'/',
[ 'FontSize', 'Font' ],
[ 'TextColor', 'BGColor' ],
[ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'Code', 'PhpBlock', '-', 'Table' ],
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
	if($db->table_exists('ckeditor'))
	{
		$db->query("DROP TABLE ".TABLE_PREFIX."ckeditor");
	}
	$db->delete_query("datacache", "title = 'cketoolbar'");
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
	if($db->field_exists('ckeditor','users')) {
		$db->query("ALTER TABLE ".TABLE_PREFIX."users DROP ckeditor");
	}
	include MYBB_ROOT."/inc/adminfunctions_templates.php";
	find_replace_templatesets("usercp_options", "#".preg_quote('{$ckeditor}')."#is", '', 0);
	find_replace_templatesets("showthread_quickreply", "#".preg_quote('{$smilieinserter}')."#is", '', 0);
	find_replace_templatesets("showthread_quickreply", "#".preg_quote('{$codebuttons}')."#is", '', 0);
	find_replace_templatesets("misc_smilies_popup_smilie", "#".preg_quote('(\'<img src=&quot;{$smilie[\'image\']}&quot; />\');')."#is", '(\'{$smilie[\'insert\']}\');');	
	find_replace_templatesets("showthread", "#".preg_quote('src="jscripts/thread{$threadckeditor}.js')."#is", 'src="jscripts/thread.js');	
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
				$smiliesmap = '';
				$smiliesmaps = array();
				$counter = 0;
				$i = 0;

				foreach($smiliecache as $find => $image)
				{
					if ($i < $mybb->settings['smilieinsertertot'])
					{
						$find = htmlspecialchars_uni($find);
						$smilies1 .= "'".str_replace("'","\\'",$image)."', ";
						$smilies2 .= "'".str_replace("'","\\'",$find)."', ";
						//$smiliesmap .= "'".str_replace("'","\\'",$find)."' : '".str_replace("'","\\'",$find)."', ";
						$smiliesmaps[] = $find;
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
						$smilies .= "<td style=\"text-align: center\"><img src=\"{$image}\" border=\"0\" class=\"smilie\" alt=\"{$find}\" title=\"{$find}\" onclick=\"addsmilies(this);\" /></td>\n";
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
	$autosave = '';
	$autosavediv = '';
	if($mybb->settings['mybbirckeditor_enableautosave'])
	{
		$autosavediv = "<div id=\"autosave\" style=\"background:#f7f7f7;border:1px solid #cccccc;padding:3px;margin:5px 0;display:none;\" class=\"smalltext\"></div>";
		$autosave = "
		<script type=\"text/javascript\">
			function setautosave()
			{
				CKEDITOR.instances.{$id}.setData(Cookie.get('mybbirckeditormessage'));
				Cookie.set('mybbirckeditormessage', '');
				$('autosave').style.display = 'none';
			}
			function checkautosave()
			{
				myWindow=window.open('','','width=300,height=180');
				myWindow.document.write('<!DOCTYPE html><html dir=\"rtl\"><head><title>{$lang->mybbirckeditor_previewautosave}</title><style>body { font-family:Tahoma;font-size:11px;text-align:center;}</style></head ><body class=\"body\"><a href=\"javascript:window.close();\">{$lang->mybbirckeditor_closewindow}</a><br><br><textarea style=\"width:95%;height:125px;font-family:Tahoma;text-align:right;\">'+Cookie.get('mybbirckeditormessage')+'</textarea></body></html>');
				myWindow.focus();
			}
			function autosaverefresh() {
					var currentTime = new Date()
					var hours = currentTime.getHours()
					var minutes = currentTime.getMinutes()
					var seconds = currentTime.getSeconds()

					if (minutes < 10) {
						minutes = \"0\" + minutes
					}
					if (seconds < 10) {
						seconds = \"0\" + seconds
					}
				Cookie.set('mybbirckeditormessage',	CKEDITOR.instances.{$id}.getData());
				//alert(CKEDITOR.instances.{$id}.getData());
				$('autosave').style.display = 'none';
				$('autosave').innerHTML = '';
				setTimeout(function(){
					if(Cookie.get('mybbirckeditormessage'))
					{
						$('autosave').style.display = 'block';
						$('autosave').innerHTML = '{$lang->mybbirckeditor_autosavesaved} ( {$lang->mybbirckeditor_autosavetime}: '+hours + \":\" + minutes + \":\" + seconds + \" \"+') (<a href=\"javascript:setautosave();\">{$lang->mybbirckeditor_restoreautosave}</a> - <a href=\"javascript:checkautosave();\">{$lang->mybbirckeditor_previewautosave}</a>)';
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
					var currentTime = new Date()
					var hours = currentTime.getHours()
					var minutes = currentTime.getMinutes()
					var seconds = currentTime.getSeconds()

					if (minutes < 10) {
						minutes = \"0\" + minutes
					}
					if (seconds < 10) {
						seconds = \"0\" + seconds
					}
				Cookie.set('mybbirckeditormessage',	CKEDITOR.instances.{$id}.getData());
				//alert(CKEDITOR.instances.{$id}.getData());
				$('autosave').style.display = 'none';
				$('autosave').innerHTML = '';
				setTimeout(function(){
					if(Cookie.get('mybbirckeditormessage'))
					{
						$('autosave').style.display = 'block';
						$('autosave').innerHTML = '{$lang->mybbirckeditor_autosavesaved} ( {$lang->mybbirckeditor_autosavetime}: '+hours + \":\" + minutes + \":\" + seconds + \" \"+') (<a href=\"javascript:setautosave();\">{$lang->mybbirckeditor_restoreautosave}</a> - <a href=\"javascript:checkautosave();\">{$lang->mybbirckeditor_previewautosave}</a>)';
					}
				},500);
			}
		Event.observe(document, 'dom:loaded', function() {
			if ((Cookie.get('mybbirckeditormessage')) && Cookie.get('mybbirckeditormessage') != CKEDITOR.instances.{$id}.getData(1))
			{
				$('autosave').style.display = 'block';
				$('autosave').innerHTML = '{$lang->mybbirckeditor_autosavefound} (<a href=\"javascript:setautosave();\">{$lang->mybbirckeditor_restoreautosave}</a> - <a href=\"javascript:checkautosave();\">{$lang->mybbirckeditor_previewautosave}</a>)';
			}
			setTimeout(function() {autosaverefresh()}, {$mybb->settings['mybbirckeditor_autosave']}000);
		});
		</script>";
	}
	if(is_array($smiliesmaps))
	{
		sort($smiliesmaps);
		for($i = count($smiliesmaps)-1;$i >= 0;$i--)
		{
			$smiliesmap .= "'".str_replace("'","\\'",$smiliesmaps[$i])."' : '".str_replace("'","\\'",$smiliesmaps[$i])."', ";
		}
	}
	if ($mini == 1)
	{
		if (!strstr($headerinclude,"<script src=\"{$mybb->settings['bburl']}/ckeditormini/ckeditor.js?time=29-01-2013-5-57\"></script>"))
		{
			$headerinclude .= "<script src=\"{$mybb->settings['bburl']}/ckeditormini/ckeditor.js?time=29-01-2013-5-57\"></script>";
		}
			$codebuttons = "
				<script>
					var mybbsmiliesmap = { {$smiliesmap} };
					function addsmilies(code)
					{
						htmlcode = '<img src=\"'+code.src+'\" alt=\"'+code.alt+'\" />';
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
					specialChars: [' ', '!', '&quot;', '#', '$', '%', '&amp;', \"'\", '(', ')', '*', '+', '-', '.', '/', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', ':', ';', '&lt;', '=', '&gt;', '?', '@', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '[', ']', '^', '_', '', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '{', '|', '}', '~', \"&euro;\", \"&lsquo;\", \"&rsquo;\", \"&ldquo;\", \"&rdquo;\", \"&ndash;\", \"&mdash;\", \"&iexcl;\", \"&cent;\", \"&pound;\", \"&curren;\", \"&yen;\", \"&brvbar;\", \"&sect;\", \"&uml;\", \"&copy;\", \"&ordf;\", \"&laquo;\", \"&not;\", \"&reg;\", \"&macr;\", \"&deg;\", \"&sup2;\", \"&sup3;\", \"&acute;\", \"&micro;\", \"&para;\", \"&middot;\", \"&cedil;\", \"&sup1;\", \"&ordm;\", \"&raquo;\", \"&frac14;\", \"&frac12;\", \"&frac34;\", \"&iquest;\", \"&Agrave;\", \"&Aacute;\", \"&Acirc;\", \"&Atilde;\", \"&Auml;\", \"&Aring;\", \"&AElig;\", \"&Ccedil;\", \"&Egrave;\", \"&Eacute;\", \"&Ecirc;\", \"&Euml;\", \"&Igrave;\", \"&Iacute;\", \"&Icirc;\", \"&Iuml;\", \"&ETH;\", \"&Ntilde;\", \"&Ograve;\", \"&Oacute;\", \"&Ocirc;\", \"&Otilde;\", \"&Ouml;\", \"&times;\", \"&Oslash;\", \"&Ugrave;\", \"&Uacute;\", \"&Ucirc;\", \"&Uuml;\", \"&Yacute;\", \"&THORN;\", \"&szlig;\", \"&agrave;\", \"&aacute;\", \"&acirc;\", \"&atilde;\", \"&auml;\", \"&aring;\", \"&aelig;\", \"&ccedil;\", \"&egrave;\", \"&eacute;\", \"&ecirc;\", \"&euml;\", \"&igrave;\", \"&iacute;\", \"&icirc;\", \"&iuml;\", \"&eth;\", \"&ntilde;\", \"&ograve;\", \"&oacute;\", \"&ocirc;\", \"&otilde;\", \"&ouml;\", \"&divide;\", \"&oslash;\", \"&ugrave;\", \"&uacute;\", \"&ucirc;\", \"&uuml;\", \"&yacute;\", \"&thorn;\", \"&yuml;\", \"&OElig;\", \"&oelig;\", \"&#372;\", \"&#374\", \"&#373\", \"&#375;\", \"&sbquo;\", \"&#8219;\", \"&bdquo;\", \"&hellip;\", \"&trade;\", \"&#9658;\", \"&bull;\", \"&rarr;\", \"&rArr;\", \"&hArr;\", \"&diams;\", \"&asymp;\", '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹', '۰', 'ا', 'ب', 'پ', 'ت', 'ث', 'ج', 'چ', 'ح', 'خ', 'د', 'ذ', 'ر', 'ز', 'ژ', 'س', 'ش', 'ص', 'ض', 'ط', 'ظ', 'ع', 'غ', 'ف', 'ق', 'ک', 'گ', 'ل', 'م', 'ن', 'و', 'ه', 'ی', 'ي', 'ك'],
					defaultLanguage: '{$lang->mybbirckeditor_lang}',
					baseHref: '{$mybb->settings['bburl']}/',
					language: '{$lang->mybbirckeditor_lang}',
						removePlugins: '{$contextmenu}dialogadvtab,div,filebrowser,flash,format,forms,iframe,liststyle,pagebreak,showborders,stylescombo,table,tabletools,templates',
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
	}
	else
	{
		if (!strstr($headerinclude,"<script src=\"{$mybb->settings['bburl']}/ckeditor/ckeditor.js?time=29-01-2013-5-57\"></script>"))
		{
			$headerinclude .= "<script src=\"{$mybb->settings['bburl']}/ckeditor/ckeditor.js?time=29-01-2013-5-57\"></script>";
		}
		$codebuttons = "
			<script>
				var mybbsmiliesmap = { {$smiliesmap} };
				function addsmilies(code)
				{
					htmlcode = '<img src=\"'+code.src+'\" alt=\"'+code.alt+'\" />';
					if (CKEDITOR.instances.{$id}.mode == 'wysiwyg')
					{
						CKEDITOR.instances.{$id}.insertHtml(htmlcode);
					}
				}
				CKEDITOR.replace( '{$id}', {
					extraPlugins: '";
					if($mybb->settings['mybbirckeditorswitchmycode'] == 1)
						$codebuttons .= "bbcode,";
					$codebuttons .= "magicline,code,videos,phpblock,mybbbasicstyles,timenow,texttransform',
					enterMode:2,
					toolbarCanCollapse: true,
					defaultLanguage: '{$lang->mybbirckeditor_lang}',
					baseHref: '{$mybb->settings['bburl']}/',
					language: '{$lang->mybbirckeditor_lang}',
					uiColor: '#{$mybb->settings['mybbirckeditorcolor']}',
					magicline_color: '#{$mybb->settings['mybbirckeditormigiclinecolor']}',
					// Remove unused plugins.
					removePlugins: '{$contextmenu}dialogadvtab,div,filebrowser,flash,format,forms,iframe,liststyle,pagebreak,showborders,stylescombo,tabletools,templates',
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
					specialChars: [' ', '!', '&quot;', '#', '$', '%', '&amp;', \"'\", '(', ')', '*', '+', '-', '.', '/', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', ':', ';', '&lt;', '=', '&gt;', '?', '@', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '[', ']', '^', '_', '', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '{', '|', '}', '~', \"&euro;\", \"&lsquo;\", \"&rsquo;\", \"&ldquo;\", \"&rdquo;\", \"&ndash;\", \"&mdash;\", \"&iexcl;\", \"&cent;\", \"&pound;\", \"&curren;\", \"&yen;\", \"&brvbar;\", \"&sect;\", \"&uml;\", \"&copy;\", \"&ordf;\", \"&laquo;\", \"&not;\", \"&reg;\", \"&macr;\", \"&deg;\", \"&sup2;\", \"&sup3;\", \"&acute;\", \"&micro;\", \"&para;\", \"&middot;\", \"&cedil;\", \"&sup1;\", \"&ordm;\", \"&raquo;\", \"&frac14;\", \"&frac12;\", \"&frac34;\", \"&iquest;\", \"&Agrave;\", \"&Aacute;\", \"&Acirc;\", \"&Atilde;\", \"&Auml;\", \"&Aring;\", \"&AElig;\", \"&Ccedil;\", \"&Egrave;\", \"&Eacute;\", \"&Ecirc;\", \"&Euml;\", \"&Igrave;\", \"&Iacute;\", \"&Icirc;\", \"&Iuml;\", \"&ETH;\", \"&Ntilde;\", \"&Ograve;\", \"&Oacute;\", \"&Ocirc;\", \"&Otilde;\", \"&Ouml;\", \"&times;\", \"&Oslash;\", \"&Ugrave;\", \"&Uacute;\", \"&Ucirc;\", \"&Uuml;\", \"&Yacute;\", \"&THORN;\", \"&szlig;\", \"&agrave;\", \"&aacute;\", \"&acirc;\", \"&atilde;\", \"&auml;\", \"&aring;\", \"&aelig;\", \"&ccedil;\", \"&egrave;\", \"&eacute;\", \"&ecirc;\", \"&euml;\", \"&igrave;\", \"&iacute;\", \"&icirc;\", \"&iuml;\", \"&eth;\", \"&ntilde;\", \"&ograve;\", \"&oacute;\", \"&ocirc;\", \"&otilde;\", \"&ouml;\", \"&divide;\", \"&oslash;\", \"&ugrave;\", \"&uacute;\", \"&ucirc;\", \"&uuml;\", \"&yacute;\", \"&thorn;\", \"&yuml;\", \"&OElig;\", \"&oelig;\", \"&#372;\", \"&#374\", \"&#373\", \"&#375;\", \"&sbquo;\", \"&#8219;\", \"&bdquo;\", \"&hellip;\", \"&trade;\", \"&#9658;\", \"&bull;\", \"&rarr;\", \"&rArr;\", \"&hArr;\", \"&diams;\", \"&asymp;\", '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹', '۰', 'ا', 'ب', 'پ', 'ت', 'ث', 'ج', 'چ', 'ح', 'خ', 'د', 'ذ', 'ر', 'ز', 'ژ', 'س', 'ش', 'ص', 'ض', 'ط', 'ظ', 'ع', 'غ', 'ف', 'ق', 'ک', 'گ', 'ل', 'م', 'ن', 'و', 'ه', 'ی', 'ي', 'ك'],
					smiley_columns: '{$mybb->settings['smilieinsertercols']}',
					skin: '{$mybb->settings['mybbirckeditorskin']}, {$mybb->settings['bburl']}/ckeditor/skins/{$mybb->settings['mybbirckeditorskin']}/',
					// Strip CKEditor smileys to those commonly used in BBCode.
					{$clickablesmilies}
					
				});
 			</script>";
	}
	if($autosave)
	{
		if (!strstr($headerinclude,$autosave))
		{
			$headerinclude .= $autosave;
		}
		$codebuttons .= $autosavediv;
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
		if($message)
		$message .='

 &nbsp;';
		if ($mybb->settings['smilieinserter'] == 1)
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

	if (!$mybb->user['ckeditor']) { return false;}

}


function mybbirckeditorthread() {
	global $quickreply, $threadckeditor;
	global $cache, $smiliecache, $theme, $templates, $lang, $mybb, $smiliecount, $codebuttons, $smilieinserter;
	if (!$mybb->user['ckeditor']) { return false;}
	if ($mybb->settings['mybbirckeditorswitch'] == 1)
	{
		if ($mybb->settings['mybbirckeditor_quickreply'] == 2)
		{
			$codebuttons = build_mycode_inserter();
		}
		else
		{
			$codebuttons = build_mycode_inserter();
		}
		$threadckeditor = 'forckeditor';
		if ($mybb->settings['smilieinserter'] == 1)
		{
			$smilieinserter = build_clickable_smilies();
			$smilieinserter .= '<br /><br />';
		}
		else
		{
			$smilieinserter = '';
		}
	}
}
function mybbirckeditor_parser($message) {
	global $mybb,$forum;
	if ($mybb->settings['mybbirckeditorswitch'] == 1)
	{
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
			while(preg_match("#\[table\](.*?)\[/table\]#is",$message,$match1)) {
				while(preg_match("#\[tr\](.*?)\[/tr\]#is",$match1[1],$match2)) {
					while(preg_match("#\[td\](.*?)\[/td\]#is",$match2[1],$match3)) {
						$match3[1] = str_replace("\n","<br />",$match3[1]);
						$match2[1] = str_replace($match3[0],'<td style="background:#ffffff;padding:5px;border:none;">'.$match3[1].'</td>',$match2[1]);
					}
					$match1[1] = str_replace($match2[0],'<tr>'.$match2[1].'</tr>',$match1[1]);
				}
				$match1[1] = str_replace("\n","",$match1[1]);
				$message = str_replace($match1[0],'<table style="background:#000000;border:1px #000000 solid;margin:15px;width:95%;">'.$match1[1].'</table>',$message);
			}
			$message = preg_replace("#\[/(b|s|u|sub|sup|i|code)\]\[\\1\]#is", "", $message);
			$message = preg_replace("#\[img\](.*?)\[/img\]#is", "<img src=\"$1\" alt=\"تصویر: $1\" title=\"تصویر: $1\" />", $message);		
			$message = preg_replace("#\[font=(.*?)\](.*?)\[/font\]#is", "<span style=\"font-family:$1;\">$2</span>", $message);
			$message = preg_replace("#\[color=(.*?)\](.*?)\[/color\]#is", "<span style=\"color:$1;\">$2</span>", $message);
			$message = preg_replace("#\[bgcolor=(.*?)\](.*?)\[/bgcolor\]#is", "<span style=\"background-color:$1;\">$2</span>", $message);
			$message = preg_replace("#\[align=(.*?)\](.*?)\[/align\]#is", "<div style=\"text-align:$1;\">$2</div>", $message);
			$message = preg_replace("#\[dir=ltr\](.*?)\[/dir\]#is", "<div dir=\"ltr\" style=\"direction:ltr;text-align:left;\">$1</div>", $message);
			$message = preg_replace("#\[dir=rtl\](.*?)\[/dir\]#is", "<div dir=\"rtl\" style=\"direction:rtl;text-align:right;\">$1</div>", $message);
			$message = preg_replace("#\[font\](.*?)\[/font\]#is", "$1", $message);
			$message = preg_replace("#\[align\](.*?)\[/align\]#is", "$1", $message);
			$message = preg_replace("#\[size\](.*?)\[/size\]#is", "$1", $message);
			$message = preg_replace("#\[dir\](.*?)\[/dir\]#is", "$1", $message);
			$message = preg_replace("#\[color\](.*?)\[/color\]#is", "$1", $message);
			$message = preg_replace("#\[bgcolor\](.*?)\[/bgcolor\]#is", "$1", $message);
			$message = preg_replace("#\[size\](.*?)\[/size\]#is", "$1", $message);
			$message = preg_replace("#\[sub\](.*?)\[/sub\]#is", "<sub>$1</sub>", $message);
			$message = preg_replace("#\[sup\](.*?)\[/sup\]#is", "<sup>$1</sup>", $message);
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

function mybbirckeditor_global()
{
	global $headerinclude, $mybb, $message;
	$id = 'message';
$autosave = "
		<script type=\"text/javascript\">
			function setautosave()
			{
				CKEDITOR.instances.{$id}.setData(Cookie.get('mybbirckeditormessage'));
				Cookie.set('mybbirckeditormessage', '');
				$('autosave').style.display = 'none';
			}
			function checkautosave()
			{
				myWindow=window.open('','','width=300,height=180');
				myWindow.document.write('<!DOCTYPE'+'html><ht'+'ml dir=\"{$lang->settings['dir']}\"><he'+'ad><tit'+'le>{$lang->mybbirckeditor_previewautosave}</ti'+'tle><sty'+'le>bo'+'dy { font-family:Tahoma;font-size:11px;text-align:center;}</sty'+'le></he'+'ad><bo'+'dy cla'+'ss=\"bod'+'y\"><a hr'+'ef=\"javascript:win'+'dow.clo'+'se();\">{$lang->mybbirckeditor_closewindow}</a><b'+'r><b'+'r><tex'+'tarea style=\"width:95%;height:125px;font-family:Tahoma;text-align:right;\">'+Cookie.get('mybbirckeditormessage')+'</texta'+'rea></bo'+'dy></ht'+'ml>');
				myWindow.focus();
			}
			function autosaverefresh() {
					var currentTime = new Date()
					var hours = currentTime.getHours()
					var minutes = currentTime.getMinutes()
					var seconds = currentTime.getSeconds()

					if (minutes < 10) {
						minutes = \"0\" + minutes
					}
					if (seconds < 10) {
						seconds = \"0\" + seconds
					}
				Cookie.set('mybbirckeditormessage',	CKEDITOR.instances.{$id}.getData());
				//alert(CKEDITOR.instances.{$id}.getData());
				$('autosave').style.display = 'none';
				$('autosave').innerHTML = '';
				setTimeout(function(){
					if(Cookie.get('mybbirckeditormessage'))
					{
						$('autosave').style.display = 'block';
						$('autosave').innerHTML = '{$lang->mybbirckeditor_autosavesaved} ( {$lang->mybbirckeditor_autosavetime}: '+hours + \":\" + minutes + \":\" + seconds + \" \"+') (<a href=\"javascript:setautosave();\">{$lang->mybbirckeditor_restoreautosave}</a> - <a href=\"javascript:checkautosave();\">{$lang->mybbirckeditor_previewautosave}</a>)';
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
					var currentTime = new Date()
					var hours = currentTime.getHours()
					var minutes = currentTime.getMinutes()
					var seconds = currentTime.getSeconds()

					if (minutes < 10) {
						minutes = \"0\" + minutes
					}
					if (seconds < 10) {
						seconds = \"0\" + seconds
					}
				Cookie.set('mybbirckeditormessage',	CKEDITOR.instances.{$id}.getData());
				//alert(CKEDITOR.instances.{$id}.getData());
				$('autosave').style.display = 'none';
				$('autosave').innerHTML = '';
				setTimeout(function(){
					if(Cookie.get('mybbirckeditormessage'))
					{
						$('autosave').style.display = 'block';
						$('autosave').innerHTML = '{$lang->mybbirckeditor_autosavesaved} ( {$lang->mybbirckeditor_autosavetime}: '+hours + \":\" + minutes + \":\" + seconds + \" \"+') (<a href=\"javascript:setautosave();\">{$lang->mybbirckeditor_restoreautosave}</a> - <a href=\"javascript:checkautosave();\">{$lang->mybbirckeditor_previewautosave}</a>)';
					}
				},500);
			}
		Event.observe(document, 'dom:loaded', function() {
			if ((Cookie.get('mybbirckeditormessage')) && Cookie.get('mybbirckeditormessage') != CKEDITOR.instances.{$id}.getData(1))
			{
				$('autosave').style.display = 'block';
				$('autosave').innerHTML = '{$lang->mybbirckeditor_autosavefound} (<a href=\"javascript:setautosave();\">{$lang->mybbirckeditor_restoreautosave}</a> - <a href=\"javascript:checkautosave();\">{$lang->mybbirckeditor_previewautosave}</a>)';
			}
			setTimeout(function() {autosaverefresh()}, {$mybb->settings['mybbirckeditor_autosave']}000);
		});
		</script>";
	$headerinclude .= '<script type="text/javascript" src="'.$mybb->settings['bburl'].'/ckeditor/ckeditor.js"></script>';
	$headerinclude .= $autosave;
	if($message) {
		$message .= "\n\r ";
	}
}

function mybbirckeditoradmin()
{
	global $mybb;
	echo '<script type="text/javascript" src="'.$mybb->settings['bburl'].'/ckeditor/ckeditor.js"></script>';
}


//Admin Panel:

function mybbirckeditor_admin_action(&$action)
{
	$action['ckeditor'] = array('active'=>'ckeditor');
}

function mybbirckeditor_admin_menu(&$admim_menu)
{

	end($admim_menu);

	$key = (key($admim_menu)) + 10;

	$admim_menu[$key] = array
	(
		'id' => 'ckeditor',
		'title' => 'CKEditor',
		'link' => 'index.php?module=forum/ckeditor'
	);
}

function mybbirckeditor_admin()
{
	global $mybb, $db, $page, $lang, $plugins;

	if ($page->active_action != 'ckeditor')
		return false;
	$lang->load('admin_mybbirckeditor');
	require_once MYBB_ADMIN_DIR."inc/class_form.php";
	// Create Admin Tabs
	$tabs['ckeditor'] = array
		(
			'title' => $lang->listofbuttons,
			'link' =>'index.php?module=forum/ckeditor',
			'description'=> ''
		);
	$tabs['ckeditor_add'] = array
		(
			'title' => $lang->addbutton,
			'link' =>'index.php?module=forum/ckeditor&action=add',
			'description'=> ''
		);
	$tabs['ckeditor_support'] = array
		(
			'title' => $lang->support,
			'link' => 'http://my-bb.ir',
			'description' => ''
		);
	if(is_object($plugins))
	{
		$tabs = $plugins->run_hooks("ckeditor_admin_tabs", $tabs);
	}
	if(!$mybb->input['action'])
	{
		$plugins->run_hooks("ckeditor_admin_start");
		$page->output_header($lang->buttonsmanenger);
		$page->add_breadcrumb_item($lang->listofbuttons);
		$page->output_nav_tabs($tabs,'ckeditor');
		echo '<form action="index.php?module=forum/ckeditor&action=order" method="post">';
		$table = new Table;
		$table->construct_header($lang->title);
		$table->construct_header($lang->order,array('width'=>'1'));
		$table->construct_header($lang->editordelete,array('width'=>'100px','style'=>'text-align:center'));			
		$query = $db->query("
			SELECT *
			FROM ".TABLE_PREFIX."ckeditor 
			where cparent='0' ORDER BY corder ASC
		");
		while($cke = $db->fetch_array($query))
		{
			$table->construct_cell($cke['cname'].' (<a href="index.php?module=forum/ckeditor&action=add&id='.$cke['cid'].'">'.$lang->addbuttonforthisgroup.'</a>)',array('style'=>'background:#effefe'));
			$table->construct_cell('<input type="text" name="disporder['.$cke['cid'].']" value="'.$cke['corder'].'" size="2" />',array('style'=>'background:#effefe;text-align:center'));
			$table->construct_cell('
			<a href="index.php?module=forum/ckeditor&action=edit&id=' . $cke['cid'] . '">'.$lang->edit.'</a>&nbsp;|&nbsp; <a href="index.php?module=forum/ckeditor&action=delete&id=' . $cke['cid'] . '">'.$lang->delete.'</a>
			',array('style'=>'background:#effefe;text-align:center'));
			$table->construct_row();
			
			$query2 = $db->query("
				SELECT *
				FROM ".TABLE_PREFIX."ckeditor 
				where cparent='{$cke['cid']}' ORDER BY corder ASC
			");
			while($cke = $db->fetch_array($query2))
			{
				$table->construct_cell($cke['cname'],array('style'=>'padding-left:30px;padding-right:30px'));
				$table->construct_cell('<input type="text" name="disporder['.$cke['cid'].']" value="'.$cke['corder'].'" size="2" />',array('style'=>'text-align:center'));
				$table->construct_cell('
				<a href="index.php?module=forum/ckeditor&action=edit&id=' . $cke['cid'] . '">'.$lang->edit.'</a>&nbsp;|&nbsp; <a href="index.php?module=forum/ckeditor&action=delete&id=' . $cke['cid'] . '">'.$lang->delete.'</a>
				',array('style'=>'text-align:center'));
				$table->construct_row();
			}
		}

		if($table->num_rows() == 0)
		{
			$table->construct_cell($lang->thereisnobutton, array('colspan' => 3));
			$table->construct_row();
		}

		// Show our Donation Page
		$table->construct_cell('By: <a href="http://my-bb.ir">My-BB.Ir</a>');
		$table->construct_cell('<input type="submit" name="submit" value="'.$lang->saveorders.'" />', array('colspan' => 2));
		$table->construct_row();

		$table->output($lang->listofbuttons);
		echo '</form>';
		$page->output_footer();
	}
	else if($mybb->input['action'] == 'order')
	{
		if(!empty($mybb->input['disporder']) && is_array($mybb->input['disporder']))
		{
			foreach($mybb->input['disporder'] as $key => $val)
			{
				$db->update_query("ckeditor", array('corder' => intval($val)), "cid='".intval($key)."'");
			}
		}
		$plugins->run_hooks("ckeditor_admin_order");
		reloade_cache_cketoolbar();
		admin_redirect("index.php?module=forum/ckeditor");
	}
	else if($mybb->input['action'] == 'add')
	{
		if($mybb->input['id'])
		{
			$title = $lang->addbutton;
		}
		else
		{
			$title =  $lang->addbuttongroup;
		}
		if($mybb->input['submit'])
		{
			$data = array();
			$data['cparent'] = $mybb->input['id'];
			$data['corder'] = $mybb->input['corder'];
			if($mybb->input['id'])
			{
				$data['citem'] = $mybb->input['type'];
				$data['cname'] = mybbirckeditor_getitems($mybb->input['type']);
			}
			else
			{
				$data['citem'] = '';
				$data['cname'] = $mybb->input['title'];
			}
			$db->insert_query('ckeditor',$data);
			$plugins->run_hooks("ckeditor_admin_add_do");
			reloade_cache_cketoolbar();
			admin_redirect("index.php?module=forum/ckeditor");
		}
		$page->output_header($title);
		$page->add_breadcrumb_item($title);
		$page->output_nav_tabs($tabs, 'ckeditor_add');



		$form = new Form("", "post");


		$table = new Table;		
		if($mybb->input['id']) {
			$items = mybbirckeditor_getitems();
			$item = '';
			foreach($items as $key => $val){
				$item .= '<option value="'.htmlspecialchars($key).'">'.htmlspecialchars($val).'</option>';
			}
			$table->construct_cell($lang->buttontype.':');
			$table->construct_cell('<select name="type">
				'.$item.'
			</select>');
			$table->construct_row();
		}
		else
		{
			$table->construct_cell($lang->title);
			$table->construct_cell('<input type="text" size="30" name="title" value="' . htmlspecialchars($mybb->input['title']) . '" />');
			$table->construct_row();
		}
		
		$table->construct_cell($lang->order);
		$table->construct_cell('<input type="text" size="5" name="corder" value="' . htmlspecialchars($mybb->input['order']) . '" />');
		$table->construct_row();
		$table->construct_cell('<input type="submit" value="'.htmlspecialchars($title).'" name="submit" />', array('colspan' => 2));
		$table->construct_row();

		$form->end;
		$table->output($title);

		$page->output_footer();
	}
	else if($mybb->input['action'] == 'edit')
	{
		$title = $lang->editbutton;
		if($mybb->input['submit'])
		{
			$data = array();
			$data['corder'] = $mybb->input['corder'];
			$data['citem'] = $mybb->input['type'];
			$data['cname'] = $mybb->input['title'];
			$db->update_query('ckeditor',$data,"cid='".intval($mybb->input['id'])."'");
			reloade_cache_cketoolbar();
			admin_redirect("index.php?module=forum/ckeditor");
		}
		$page->output_header($title);
		$page->add_breadcrumb_item($title);
		$page->output_nav_tabs($tabs, 'ckeditor_add');


		$query = $db->simple_select("ckeditor","*","cid='".intval($mybb->input['id'])."'");
		$button = $db->fetch_array($query);
		$form = new Form("", "post");


		$table = new Table;		
			$table->construct_cell($lang->title);
			$table->construct_cell('<input type="text" size="30" name="title" value="' . htmlspecialchars($button['cname']) . '" />');
			$table->construct_row();
			if($button['cparent'])
			{
			$items = mybbirckeditor_getitems();
			$item = '';
			foreach($items as $key => $val){
				$selected = '';
				if($key == $button['citem'])
					$selected = ' selected';
				$item .= '<option value="'.htmlspecialchars($key).'"'.$selected.'>'.htmlspecialchars($val).'</option>';
			}
			$table->construct_cell($lang->buttontype);
			$table->construct_cell('<select name="type">
				'.$item.'
			</select>');
			$table->construct_row();
			}
		
		$table->construct_cell($lang->order);
		$table->construct_cell('<input type="text" size="5" name="corder" value="' . htmlspecialchars($button['corder']) . '" />');
		$table->construct_row();
		$table->construct_cell('<input type="submit" value="'.htmlspecialchars($title).'" name="submit" />', array('colspan' => 2));
		$table->construct_row();

		$form->end;
		$table->output($title);

		$page->output_footer();
	}
	else if($mybb->input['action'] == 'delete' && $mybb->input['id'])
	{
		$id = intval($mybb->input['id']);
		$del = $db->write_query("DELETE FROM ".TABLE_PREFIX."ckeditor  WHERE cid = '{$id}'");
		if($del)
		{
			$del = $db->write_query("DELETE FROM ".TABLE_PREFIX."ckeditor  WHERE cparent = '{$id}'");
		}
		admin_redirect("index.php?module=forum/ckeditor");
	}
	else
	{
		admin_redirect("index.php");
		die();
		return false;
	}
}

function mybbirckeditor_getitems($item='')
{
	global $mybb, $lang,$plugins;
	$lang->load('admin_mybbirckeditor');
	$items = array();
	$items['-'] = $lang->dash;
	$items['Source'] = $lang->Source;
	$items['Save'] = $lang->Save;
	$items['NewPage'] = $lang->NewPage;
	$items['Cut'] = $lang->Cut;
	$items['Copy'] = $lang->Copy;
	$items['Paste'] = $lang->Paste;
	$items['PasteText'] = $lang->PasteText;
	$items['PasteFromWord'] = $lang->PasteFromWord;
	$items['Undo'] = $lang->Undo;
	$items['Redo'] = $lang->Redo;
	$items['Find'] = $lang->Find;
	$items['Replace'] = $lang->Replace;
	$items['SelectAll'] = $lang->SelectAll;
	$items['Bold'] = $lang->Bold;
	$items['Italic'] = $lang->Italic;
	$items['Underline'] = $lang->Underline;
	$items['Strike'] = $lang->Strike;
	$items['Subscript'] = $lang->Subscript;
	$items['Superscript'] = $lang->Superscript;
	$items['RemoveFormat'] = $lang->RemoveFormat;
	$items['NumberedList'] = $lang->NumberedList;
	$items['BulletedList'] = $lang->BulletedList;
	$items['Outdent'] = $lang->Outdent;
	$items['Indent'] = $lang->Indent;
	$items['Link'] = $lang->Link;
	$items['Unlink'] = $lang->Unlink;
	$items['Image'] = $lang->Image;
	$items['Smiley'] = $lang->Smiley;
	$items['SpecialChar'] = $lang->SpecialChar;
	$items['HorizontalRule'] = $lang->HorizontalRule;
	$items['FontSize'] = $lang->FontSize;
	$items['Font'] = $lang->Font;
	$items['JustifyLeft'] = $lang->JustifyLeft;
	$items['JustifyCenter'] = $lang->JustifyCenter;
	$items['JustifyRight'] = $lang->JustifyRight;
	$items['JustifyBlock'] = $lang->JustifyBlock;
	$items['BidiLtr'] = $lang->BidiLtr;
	$items['BidiRtl'] = $lang->BidiRtl;
	$items['TextColor'] = $lang->TextColor;
	$items['BGColor'] = $lang->BGColor;
	$items['Blockquote'] = $lang->Blockquote;
	$items['Code'] = $lang->Code;
	$items['PhpBlock'] = $lang->PhpBlock;
	$items['Table'] = $lang->Table;
	$items['Videos'] = $lang->Videos;
	$items['Maximize'] = $lang->Maximize;
	if(is_object($plugins))
	{
		$items = $plugins->run_hooks("ckeditor_items", $items);
	}
	if($item)
	{
		if($items[$item])
		{
			return $items[$item];
		}
		else
		{
			return false;
		}
	}
	else
	{
		return $items;
	}
}

function reloade_cache_cketoolbar() {
	global $mybb, $db, $cache, $plugins;
	$query = $db->query("
		SELECT *
		FROM ".TABLE_PREFIX."ckeditor 
		where cparent='0' ORDER BY corder ASC
	");
	$groups = array();
	$plugins->run_hooks("ckeditor_reloadetoolbar_start");
	while($cke = $db->fetch_array($query))
	{
		$query2 = $db->query("
			SELECT *
			FROM ".TABLE_PREFIX."ckeditor 
			where cparent='{$cke['cid']}' ORDER BY corder ASC
		");
		if($db->num_rows($query2))
		{
			$buttons = array();
			while($cke = $db->fetch_array($query2)) {
				if(mybbirckeditor_getitems($cke['citem']))
				{
					$buttons[] = "'".htmlspecialchars($cke['citem'])."'";
				}
			}
			$groups[] = '['.implode(',',$buttons).']';
		}
	}
	$toolbar = implode(',',$groups);
	$plugins->run_hooks("ckeditor_reloadetoolbar_end");
	$cache->update('cketoolbar',$groups);
}

?>