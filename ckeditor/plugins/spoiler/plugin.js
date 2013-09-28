CKEDITOR.plugins.add( 'spoiler', {
	icons: 'spoiler',
	init: function( editor ) {
		editor.addCommand( 'spoiler', new CKEDITOR.dialogCommand( 'spoilerDialog' ));
		editor.ui.addButton( 'spoiler', {
			label: 'متن پنهان',
			command: 'spoiler',
			toolbar: 'insert'
		});

		CKEDITOR.dialog.add( 'spoilerDialog', this.path + 'dialogs/spoiler.js' );
	}

});