// closure to avoid namespace collision
(function() {
	
    tinymce.PluginManager.add("showdown_button", function( editor, url ) {
	    
        editor.addButton( "showdown_button", {
            title: 'Showdown',
            icon: 'icon showdown-icon',
			onclick : function() {
				// triggers the thickbox
				var width = jQuery(window).width(), H = jQuery(window).height(), W = ( 720 < width ) ? 720 : width;
				W = W - 80;
				H = H - 84;
				tb_show( 'Hot Or Not Shortcode', '#TB_inline?width=' + W + '&height=' + H + '&inlineId=showdown-form' );
			}
        });
    });
		
	// executes this when the DOM is ready
	jQuery(function(){
		// creates a form to be displayed everytime the button is clicked
		// you should achieve this using AJAX instead of direct html code like this
		var form = jQuery('<div id="showdown-form"><table id="showdown-table" class="showdown-table">\
			<tr>\
				<th><label for="showdown-type">Shortcode Type</label></th>\
				<td><select name="showdown-type" id="showdown-type">\
					<option value="hotornot">Hot Or Not</option>\
					<option value="hotornotstats">Hot or Not Stats</option>\
				</select><br />\
				<small>Which shortcode are you after?</small></td>\
			</tr>\
			<tr class="show-stats">\
				<th><label for="showdown-reporttype">Report Type</label></th>\
				<td><select name="showdown-reporttype" id="showdown-reporttype">\
					<option value="top_winner">Top Winners</option>\
				</select><br />\
				<small>What report type are you after?</small></td>\
			</tr>\
			<tr class="show-stats">\
				<th><label for="showdown-number">Number</label></th>\
				<td><input type="text" value="10" name="showdown-number" id="showdown-number" /><br /><small>How many results do you want to display?</small></td>\
			</tr>\
			<tr class="show-group">\
				<th><label for="showdown-group">Group</label></th>\
				<td><input type="text" name="showdown-group" id="showdown-group" /><br /><small>Leave blank to use all competitors</small></td>\
			</tr>\
			<tr>\
				<th><label for="showdown-image">Image</label></th>\
				<td><select name="showdown-image" id="showdown-image">\
					<option value="true">Yes</option>\
					<option value="false">No</option>\
				</select><br />\
				<small>Do you want to display the competitor image?</small></td>\
			</tr>\
			<tr>\
				<th><label for="showdown-desc">Description</label></th>\
				<td><select name="showdown-desc" id="showdown-desc">\
					<option value="true">Yes</option>\
					<option value="false" selected=selected>No</option>\
				</select><br />\
				<small>Do you want to display the competitor description?</small></td>\
			</tr>\
		</table>\
		<p class="submit">\
			<input type="button" id="showdown-submit" class="showdown-primary" value="Insert Shortcode" name="submit" />\
		</p>\
		</div>');
				
		var table = form.find('table');
		form.appendTo('body').hide();
		
		// default vales
		form.find(".show-stats").hide();
		form.find(".show-group").show();
		
		// handles field show/hide toggles
		form.find('#showdown-type').change(function(){
		    var value = jQuery(this).val();
        switch(value) {
          case "hotornot":
             jQuery(".show-stats").hide();
             jQuery(".show-group").show();
             break;
          case "showdown":
             jQuery(".show-stats").hide();
             jQuery(".show-group").show();
             break;
          case "showdownresults":
             jQuery(".show-stats").hide();
             jQuery(".show-group").hide();
             break;
          default:
             jQuery(".show-stats").show();
             jQuery(".show-group").show();             
             break;
        }
		}).change();
		
		// handles the click event of the submit button
		form.find('#showdown-submit').click(function(){
			// defines the options and their default values
			// again, this is not the most elegant way to do this
			// but well, this gets the job done nonetheless
				
			
			switch ( table.find('#showdown-type').val() ) {
        case "hotornot":
          var shortcode = '[wphotornot';
          var options = { 
                'group'      : '',
                'image'      : 'true',
                'desc'       : 'false'
          };
          break;
        case "showdown":
          var shortcode = '[wpshowdown';
          var options = { 
                'group'      : '',
                'image'      : 'true',
                'desc'       : 'false'
          };
          break;
        case "showdownresults":
          var shortcode = '[wpshowdownresults';
          var options = { 
                'image'      : 'true',
                'desc'       : 'false'
          };
          break;
        case "hotornotstats":
          var shortcode = '[wphotornotstats';
          var options = { 
                'group'      : '',
                'reporttype' : 'top_winner',
                'number'     : '10',
                'image'      : 'true',
                'desc'       : 'true'
          };
          break;
        default:
          var shortcode = '[wpshowdownstats';
          var options = { 
                'group'      : '',
                'reporttype' : 'top_winner',
                'number'     : '10',
                'image'      : 'true',
                'desc'       : 'true'
          };
          break;
			
			}
			
			for( var index in options) {
				var value = table.find('#showdown-' + index).val();
				
				// attaches the attribute to the shortcode only if it's different from the default value
				if ( value !== options[index] )
					shortcode += ' ' + index + '="' + value + '"';
			}
			
			shortcode += ']';
			
			// inserts the shortcode into the active editor
			tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
			
			// closes Thickbox
			tb_remove();
		});
	});
})()