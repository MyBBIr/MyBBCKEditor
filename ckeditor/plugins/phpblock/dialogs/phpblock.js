CKEDITOR.dialog.add( 'phpblockDialog', function ( editor ) {
	return {
		title: 'افزودن بلوک کد پی‌اچ‌پی',
		minWidth: 500,
		minHeight: 140,

		contents: [
			{
				id: 'tab-basic',
				label: 'افزودن',
				elements: [
					// UI elements of the first tab will be defined here
					{
						type: 'html',
						html: '<p>محتویات مورد نظر خود را وارد نمائید.<br /><br /></p>'
					},					
					{
						type: 'textarea',
						id: 'texted',
						inputStyle: 'direction:ltr;text-align:left;',
						rows: 10,
						cols: 40
					}
				]
			}
		],
		onOk: function() {
			var dialog = this;
			var code = editor.document.createElement( 'pre' );
			code.setText(dialog.getValueOf( 'tab-basic', 'texted' ));
			editor.insertHtml('<br>');editor.insertElement( code );editor.insertHtml('<br>');
		}
	};
});