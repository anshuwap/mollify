(function($){$.extend(true, mollify, {
	plugin : {
		MollifyUploader : function(env) {
			var t = this;
			this.env = env;
			
			this.open = function(folder) {
				var $d = mollify.dom.template("mollify-tmpl-uploader-dlg");
				mollify.ui.views.dialogs.custom({
					element: $d,
					buttons: [
						{ id:0, "title-key": "upload" },
						{ id:1, "title-key": "cancel" }
					],
					"on-button": function(btn, dlg) {
						if (btn.id == 1)
							dlg.close();
						else t.onUpload($d, dlg,folder);
					},
					"on-show": function(dlg) { t.onOpen($d, dlg, folder); }
				});
			};
			
			this.onOpen = function($d, dlg, folder) {
				var $form = $d.find(".mollify-uploader-form").attr("action", mollify.service.url("filesystem/"+folder.id));
				$form.append(mollify.dom.template("mollify-tmpl-uploader-file"));
			};
			
			this.onUpload = function($d, dlg, folder) {
				//TODO check if there are files
				var $form = $d.find(".mollify-uploader-form");
				$form.submit();
			};
			
			return {
				open : function(folder) {
					mollify.templates.load("mollify-uploader", mollify.templates.url("uploader.html"), function() {
						t.open(folder);
					});
				}
			};
		}
	}
});})(window.jQuery);