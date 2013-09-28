CKEDITOR.dialog.add( 'spoilerDialog', function ( editor ) {
	return {
		title: 'افزودن متن پنهان',
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
						html: '<p>محتویاتی که می خواهید به صورت کلیکی باشد را وارد نمائید.<br /><br /></p>'
					},					
					{
						type: 'textarea',
						id: 'texted',
						rows: 10,
						cols: 40
					},
					{
						type: 'html',
						html: '<p>By: <a href="http://My-BB.Ir" target="_blank">my-bb.ir</a></p>'
					}
				]
			}
		],
		onOk: function() {
			var dialog = this;
			editor.insertText( '[spoiler]'+dialog.getValueOf( 'tab-basic', 'texted' )+'[/spoiler]' );
		}
	};
});