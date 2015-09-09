## Mapping resources into different location ##

Client resource map helps customizing the app, as any client resource can be mapped into custom location. For example any template file, css file or html file can be overridden to your custom version.

```
mollify.App.init({
	"resource-map" : {
		"templates/mainview.html" : "custom/mainview.html"
	},
	...
})
```

This example overrides loading "mainview.html" template file to be loaded from folder "custom".

## Ignoring resources ##

Resources can be also marked to be ignored by setting the resource mapping to false:
```
mollify.App.init({
	"resource-map" : {
		"backend/plugin/Registration/localization/texts_en.json" : false
	},
	...
})
```

This example marks registration plugin localization file not to be loaded at all (for example if you have customized texts and added them into the main localization file).

## Overriding individual templates ##

If you don't want to override entire template file, individual templates can be overridden like this:

```
mollify.App.init({
	"resource-map" : {
		"template:mollify-tmpl-main-username" : "mollify-tmpl-custom-main-username"
	},
	...
})
```

This example says that whenever template with id "mollify-tmpl-main-username" is used, it is replaced with template id "mollify-tmpl-custom-main-username".

The custom template can be added into bottom of the page like this:

```
<html>
	<body>
	...
	</body>
	
	<!-- CUSTOM TEMPLATES -->
	<script id="mollify-tmpl-custom-main-username" type="text/x-jquery-tmpl">
		{{if authenticated}}
		<li id="mollify-username-dropdown" class="dropdown">
			<a href="#" class="dropdown-toggle">MY USER MENU: ${username}&nbsp;<b class="caret"></b></a>
		</li>
		{{/if}}
	</script>
</html>
```

Alternatively, custom templates can be in separate html file as well, but in this case the contents of the file must be loaded into the page before app starts so that the templates are available.

## Resource paths used in mappings ##

To get the resource path, open for example Chrome Dev Tools and Network Inspector which shows you all loaded resources. Copy the url starting from the mollify root level.

For example, if the loaded resource is "http://yourhost/mollify/backend/plugin/Notificator/admin/texts_en.js", use resource path "backend/plugin/Notificator/admin/texts\_en.js".