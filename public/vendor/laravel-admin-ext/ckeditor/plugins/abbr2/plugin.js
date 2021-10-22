CKEDITOR.plugins.add( 'abbr2', {
    icons: 'abbr2',
    init: function( editor ) {
        editor.addCommand( 'abbr2', new CKEDITOR.dialogCommand( 'abbr2Dialog' ) );
        editor.ui.addButton( 'abbr2', {
            label: 'Insert Youtube Link',
            command: 'abbr2',
            toolbar: 'insert'
        });

        CKEDITOR.dialog.add( 'abbr2Dialog', this.path + 'dialogs/abbr2.js' );
    }
});