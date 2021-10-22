CKEDITOR.dialog.add( 'abbr1Dialog', function( editor ) {
    return {
        title: 'Insert URL Facebook Post',
        minWidth: 300,
        minHeight: 200,
        contents: [
            {
                id: 'tab-basic',
                label: 'Basic Settings',
                elements: [
                    {
                        type: 'text',
                        id: 'abbr1',
                        label: 'URL',
                        validate: CKEDITOR.dialog.validate.notEmpty( "URL can´t be empty" )
                    },
                ]
            },

        ],
        onOk: function() {


            var build_query = function (obj, num_prefix, temp_key) 
            {
                var output_string = []
                Object.keys(obj).forEach(function (val) {
                  var key = val;
                  num_prefix && !isNaN(key) ? key = num_prefix + key : ''
                  var key = encodeURIComponent(key.replace(/[!'()*]/g, escape));
                  temp_key ? key = temp_key + '[' + key + ']' : ''
                  if (typeof obj[val] === 'object') 
                  {
                    var query = build_query(obj[val], null, key)
                    output_string.push(query)
                  }
                  else 
                  {
                    var value = encodeURIComponent(obj[val].replace(/[!'()*]/g, escape));
                    output_string.push(key + '=' + value)
                  }
                })
                return output_string.join('&')
            };

            var dialog = this;
            var abbr = editor.document.createElement( 'abbr1' );

            var srcURL = dialog.getValueOf( 'tab-basic', 'abbr1' );

            console.log(srcURL);

            var div = editor.document.createElement('div');
            var frame = '<p align=center><iframe src="https://www.facebook.com/plugins/post.php?href=' + srcURL +
            '&width=500&show_text=true&appId=377519652779329&height=604" width="500" height="604" ' +
            'style="border:none;overflow:hidden;max-width: 100%;min-width: 180px; width: 520px;" scrolling="no" frameborder="0" allowfullscreen="true" ' +
            'allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"></iframe></p>';

            console.log(frame);

            div.setHtml(frame);
            editor.insertElement( div );
        }
        
    };
});