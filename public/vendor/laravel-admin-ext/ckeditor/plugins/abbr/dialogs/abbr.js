CKEDITOR.dialog.add( 'abbrDialog', function( editor ) {
    return {
        title: 'Insert URL Twitter',
        minWidth: 300,
        minHeight: 200,
        contents: [
            {
                id: 'tab-basic',
                label: 'Basic Settings',
                elements: [
                    {
                        type: 'text',
                        id: 'abbr',
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
            var abbr = editor.document.createElement( 'abbr' );

            var srcURL = dialog.getValueOf( 'tab-basic', 'abbr' );

            var input = 
            {
                url: srcURL,
            };

            var query = build_query(input);
            var src_url = "https://twitframe.com/show?" + query;

            var div = editor.document.createElement('div');
            var frame = "<p align=center><iframe scrolling=\"no\" frameborder=\"0\" allowtransparency=\"true\" "
                        + "class=\"twitter-timeline twitter-timeline-rendered\" title=\"Twitter Timeline\" height=\"600\" "
                        + "style=\"border: none; max-width: 100%; min-width: 180px; width: 520px;\" "
                        + "src=\"" + src_url + "\"></iframe></p>";
            div.setHtml(frame);
            editor.insertElement( div );
        }
        
    };
});