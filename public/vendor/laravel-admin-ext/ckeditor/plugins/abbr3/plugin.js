CKEDITOR.plugins.add( 'abbr3', {
    icons: 'abbr3',
    init: function( editor ) {
        editor.addCommand( 'abbr3', new CKEDITOR.dialogCommand( 'abbr3Dialog' ) );
        editor.ui.addButton( 'abbr3', {
            label: 'Insert Instagram Post',
            command: 'abbr3',
            toolbar: 'insert'
        });

        CKEDITOR.dialog.add( 'abbr3Dialog', this.path + 'dialogs/abbr3.js' );
    }
});