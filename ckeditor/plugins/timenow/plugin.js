CKEDITOR.plugins.add( 'timenow', {
	lang: 'fa,en',
	icons: 'timenow,datenow',
	init: function( editor ) {
		editor.addCommand( 'timenow',{
				exec:function( editor ) {
					var str = "";

					var currentTime = new Date()
					var hours = currentTime.getHours()
					var minutes = currentTime.getMinutes()
					var seconds = currentTime.getSeconds()

					if (minutes < 10) {
						minutes = "0" + minutes
					}
					if (seconds < 10) {
						seconds = "0" + seconds
					}
					str += hours + ":" + minutes + ":" + seconds + " ";
					editor.insertText(str);
				}
			}
		);
		editor.addCommand( 'datenow',{
				exec:function( editor ) {
					var currentTime = new Date();
					var month = currentTime.getMonth() + 1;
					var day = currentTime.getDate();
					var year = currentTime.getFullYear();
					editor.insertText(month+'-'+day+'-'+year);
				}
			}
		);
		editor.ui.addButton( 'Timenow', {
			label: editor.lang.timenow.title,
			command: 'timenow',
			toolbar: 'timenow'
		});
		editor.ui.addButton( 'Datenow', {
			label: editor.lang.timenow.datenow,
			command: 'datenow',
			toolbar: 'datenow'
		});

	}
});