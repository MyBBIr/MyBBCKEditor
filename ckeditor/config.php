<?php
/**
 * MyBB 1.6
 * Copyright 2010 MyBB Group, All Rights Reserved
 *
 * Website: http://mybb.com
 * License: http://mybb.com/about/license
 *
 * $Id$
 */

define("IN_MYBB", 1);
define("NO_ONLINE", 1);
define('THIS_SCRIPT', 'ckeditor.php');
if(!isset($working_dir) || !$working_dir)
{
$working_dir = dirname(dirname(__FILE__));
}
if(!$working_dir)
{
	$working_dir = '..';
}
require_once "{$working_dir}/global.php";


$lang->load('mybbirckeditor');
header("Expires: Sat, 1 Jan 2000 01:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Content-type: text/javascript");
$ctoolbar = $cache->read('cketoolbar');
if(!is_array($ctoolbar))
{
	reloade_cache_cketoolbar();
}else {
	$toolbar = implode(',',$ctoolbar);
}	
// Smilies: start
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

				$clickablesmilies = "config.smiley_images= [\n{$smilies1}\n];\n config.smiley_descriptions= [\n{$smilies2}\n];";
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
// Smilies:end;
$extraplugins = array();
$removeplugins = array();
if(!$mybb->settings['mybbirckeditor_contextmenu']) { $removeplugins[] = 'contextmenu';} else { $extraplugins[] = 'contextmenu';}
if(!$mybb->settings['mybbirckeditor_enableautosave']) { $removeplugins[] = 'autosave';}
function add_ckeextraplugin($plugin='')
{
	global $extraplugins;
	if(!$plugin)
		return false;
	$plugin = str_replace("'","\\'",$plugin);
	$extraplugins[] = $plugin;
}
$plugins->run_hooks("ckeditor_config_start");
?>
CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	 config.language = 'en';
	// config.uiColor = '#AADC6E';
	config.autosave_delay = 1;
	config.disableObjectResizing = 1;
	config.dialog_backgroundCoverColor = 'black';
	config.toolbar = [<?php echo $toolbar?>
					];
	config.smiley_columns = '<?php echo $mybb->settings['smilieinsertercols']?>';
	<?php echo $clickablesmilies?>

	config.baseHref = '<?php echo $mybb->settings['bburl']?>/';
	config.language = '<?php echo $lang->mybbirckeditor_lang?>';
	config.fontSize_sizes = "<?php echo $lang->editor_size_xx_small?>/xx-small;<?php echo $lang->editor_size_x_small?>/x-small;<?php echo $lang->editor_size_small?>/small;<?php echo $lang->editor_size_medium?>/medium;<?php echo $lang->editor_size_large?>/large;<?php echo $lang->editor_size_x_large?>/x-large;<?php echo $lang->editor_size_xx_large?>/xx-large";
	config.fontSize_defaultLabel = "<?php echo $lang->editor_size_small?>";
	<?php if($mybb->settings['mybbirckeditor_width']){echo "config.width= '{$mybb->settings['mybbirckeditor_width']}';";}
	if($mybb->settings['mybbirckeditor_height']){echo "config.height= '{$mybb->settings['mybbirckeditor_height']}';";}
	?>
	config.toolbarLocation= '<?php echo $mybb->settings['mybbirckeditor_toolbarlocation']?>';
	config.resize_maxHeight= '<?php echo $mybb->settings['mybbirckeditormaxheight']?>';
	config.image_previewText = ' ';
	config.skin = '<?php echo "{$mybb->settings['mybbirckeditorskin']}, {$mybb->settings['bburl']}/ckeditor/skins/{$mybb->settings['mybbirckeditorskin']}/"?>';
	config.extraPlugins = '<?php echo implode(',',$extraplugins)?>';
	config.removePlugins = '<?php echo implode(',',$removeplugins)?>,dialogadvtab,div,filebrowser,flash,format,forms,iframe,liststyle,pagebreak,showborders,stylescombo,tabletools,templates';
<?php
	$plugins->run_hooks("ckeditor_config");
?>
};
<?php
	$plugins->run_hooks("ckeditor_config_end");
?>
