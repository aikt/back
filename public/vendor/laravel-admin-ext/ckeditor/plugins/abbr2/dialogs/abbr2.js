CKEDITOR.dialog.add( 'abbr2Dialog', function( editor ) {
    return {
        title: 'Insert Youtube URL',
        minWidth: 300,
        minHeight: 200,
        contents: [
            {
                id: 'tab-basic',
                label: 'Basic Settings',
                elements: [
                    {
                        type: 'text',
                        id: 'abbr2',
                        label: 'URL',
                        validate: CKEDITOR.dialog.validate.notEmpty( "URL can´t be empty" )
                    },
                ]
            },

        ],
        onOk: function() {

            var dialog = this;
            var abbr = editor.document.createElement( 'abbr2' );

            var srcURL = dialog.getValueOf( 'tab-basic', 'abbr2' );

            console.log(srcURL);
            real_url = srcURL.replace('watch?v=' , 'embed/');
            console.log('REAL URL',srcURL);

            var div = editor.document.createElement('div');
            var frame = `<p align=center><iframe width="560" height="315" src="${real_url}" 
                        frameborder="0" allow="accelerometer; autoplay; clipboard-write; 
                        style="border:none;overflow:hidden;max-width: 100%;min-width: 180px; width: 520px;"
                        encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></p>`;

            console.log(frame);

            div.setHtml(frame);
            editor.insertElement( div );
        }
        
    };
});