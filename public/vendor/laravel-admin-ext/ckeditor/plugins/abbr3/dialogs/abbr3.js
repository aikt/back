CKEDITOR.dialog.add( 'abbr3Dialog', function( editor ) {
    return {
        title: 'Insert Instagram URL post',
        minWidth: 300,
        minHeight: 200,
        contents: [
            {
                id: 'tab-basic',
                label: 'Basic Settings',
                elements: [
                    {
                        type: 'text',
                        id: 'abbr3',
                        label: 'URL',
                        validate: CKEDITOR.dialog.validate.notEmpty( "URL can´t be empty" )
                    },
                ]
            },

        ],
        onOk: function() {

            var dialog = this;
            var abbr = editor.document.createElement( 'abbr3' );

            var srcURL = dialog.getValueOf( 'tab-basic', 'abbr3' );

            real_url = srcURL.replace('?utm_source=ig_web_copy_link' , 'embed');

            var div = editor.document.createElement('div');
            var frame = `<p align=center>
            <iframe width="420" height="580" allowtransparency="true" style="border: none;max-width: 100%;min-width: 180px; width: 520px;" src="${real_url}" frameborder="0"></iframe>
                        </p>`;

            console.log(frame);

            div.setHtml(frame);
            editor.insertElement( div );
        }
        
    };
});