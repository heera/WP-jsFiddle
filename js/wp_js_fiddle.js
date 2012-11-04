(function() {
	tinymce.create('tinymce.plugins.wpjsfiddle', {
		init : function(ed, url) {
			ed.addButton('wpjsfiddle', {
				title : 'Insert Js Fiddle',
				image : url+'/images/jsfiddle-icon.png',
				onclick : function() {
					var fiddleUrl = prompt("Insert fiddle link", "");
					if (fiddleUrl != null && fiddleUrl != ''){
						ed.execCommand('mceInsertContent', false, '[wp-js-fiddle url="'+fiddleUrl+'" style="width:100%;height:400px;border:solid #4173A0 1px;"]');
                	}
				}
			});
		},
		createControl : function(n, cm) {
			return null;
		},
		getInfo : function() {
			return {
				longname : "Wp Js Fiddle",
				author : 'Sheikh Heera',
				authorurl : 'http://heera.it',
				version : "1.0"
			};
		}
	});
	tinymce.PluginManager.add('wpjsfiddle', tinymce.plugins.wpjsfiddle);
})();