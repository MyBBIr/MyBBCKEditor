ckeditorm = opener.CKEDITOR.instances.message;
ckeditors = opener.CKEDITOR.instances.signature;
function ckinsertSmilie(code)
{
	htmlcode = '<img src="'+code+'" />';
	if (ckeditorm.mode == 'wysiwyg')
	{
		ckeditorm.insertHtml(htmlcode);
	}
	else if (ckeditors.mode == 'wysiwyg')
	{
		ckeditors.insertHtml(htmlcode);
	}
}