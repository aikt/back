CKEDITOR.plugins.add( 'abbr1', {
    icons: 'abbr1',
    init: function( editor ) {
        editor.addCommand( 'abbr1', new CKEDITOR.dialogCommand( 'abbr1Dialog' ) );
        editor.ui.addButton( 'abbr1', {
            label: 'Insert Facebook URL Post',
            command: 'abbr1',
            toolbar: 'insert'
        });

        CKEDITOR.dialog.add( 'abbr1Dialog', this.path + 'dialogs/abbr1.js' );
    }
});