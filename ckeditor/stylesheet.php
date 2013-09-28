<?php
define("IN_MYBB", 1);
define("NO_ONLINE", 1);
define('THIS_SCRIPT', 'stylesheet.php');
require_once "../inc/init.php";
header("Content-type: text/css");
?>
.cke_top, .cke_dialog_title {
	background: #<?php echo $mybb->settings['mybbirckeditorcolor']?>;
}

.cke_bottom, .cke_dialog_footer {
	background: #f8f8f8;
}

.cke_contents {

}

.cke_path_item {
	display:none;
}

.cke_wysiwyg_div {
	/* Font */
	font-family: Tahoma,sans-serif, Arial, Verdana, "Trebuchet MS";
	font-size: 12px;

	/* Text color */
	color: #333;

	/* Remove the background color to make it transparent */
	background-color: #fff;
	margin:0;
	padding:16px;
}

.cke_wysiwyg_div.cke_editable
{
	font-size: 13px;
	line-height: 1.6em;
}

.cke_wysiwyg_div blockquote
{
	font-style: normal;
	font-family: Tahoma, Georgia, Times, "Times New Roman", serif;
	padding: 2px;
	border-style: solid;
	border-color: #ccc;
	border-width: 1px;
	width:auto;
	margin:10px;
	overflow: auto;
	height: auto;
	display: block;
	font-size: 13px;
}

.cke_wysiwyg_div blockquote:before {
	content: 'Quote:';
	border-bottom:1px solid #ccc;
	display:block;
	text-align:left;
	direction:ltr;
}

.cke_contents_ltr blockquote cite
{

}

.cke_contents_rtl blockquote cite
{
	float:left;
}

.cke_wysiwyg_div code , .cke_wysiwyg_div pre, .cke_wysiwyg_div .code
{
	font-style: normal;
	padding: 2px;
	border-style: solid;
	border-color: #ccc;
	border-width: 1px;
	width:auto;
	margin:10px;
	direction:ltr;
	text-align:left;
	overflow: auto;
	height: auto;
	display: block;
	font-family: Monaco, Consolas, Courier, monospace;
	font-size: 13px;
}


.cke_wysiwyg_div table {
	background:#000000;
	border:1px #000000 solid;
	margin:15px;
	width:95%;
}

.cke_wysiwyg_div td,.cke_wysiwyg_div th {
	background:#ffffff;
	padding:5px;
	border:none;
}


.cke_wysiwyg_div a
{
	color: #0782C1;
}

.cke_wysiwyg_div ol,.cke_wysiwyg_div ul,.cke_wysiwyg_div dl
{
	/* IE7: reset rtl list margin. (#7334) */
	*margin-right: 0px;
	/* preserved spaces for list items with text direction other than the list. (#6249,#8049)*/
	padding: 0 40px;
}

.cke_wysiwyg_div h1,.cke_wysiwyg_div h2,.cke_wysiwyg_div h3,.cke_wysiwyg_div h4,.cke_wysiwyg_div h5,.cke_wysiwyg_div h6
{
	font-weight: normal;
	line-height: 1.2em;
}

.cke_wysiwyg_div hr
{
	border: 0px;
	border-top: 1px solid #ccc;
}

.cke_wysiwyg_div img.right {
    border: 1px solid #ccc;
    float: right;
    margin-left: 15px;
    padding: 5px;
}

.cke_wysiwyg_div img.left {
    border: 1px solid #ccc;
    float: left;
    margin-right: 15px;
    padding: 5px;
}

.cke_wysiwyg_div img:hover {
	opacity: .9;
	filter: alpha(opacity = 90);
}

.cke_wysiwyg_div pre, .cke_wysiwyg_div code
{
	white-space: pre-wrap; /* CSS 2.1 */
	word-wrap: break-word; /* IE7 */
}

.cke_wysiwyg_div .marker {
    background-color: Yellow;
}

.cke_contents_ltr mybbir {
	text-align:left;
	direction:ltr;
}

.cke_contents_rtl mybbir {
	text-align:right;
	direction:rtl;
}
<?php
exit;
?>