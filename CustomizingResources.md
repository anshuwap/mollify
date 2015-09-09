# Introduction #

Mollify allows storing customized versions of resources outside Mollify folders, which makes updates easier.

# Suggested resource customization #

To avoid losing customizations when updating Mollify version, set up a customizations folder in the mollify root, instead of inside any of the folders in the Mollify package.

Suggested folder structure is:
  * Mollify app root/
    * custom (CREATE THIS)
    * backend/
    * js/
    * localization/
    * css/
    * templates/
    * index.html

Under folder "custom", create all custom plugins, stylesheets and localizations.

# Client resources #

Using resource map feature ([ClientResourceMap](ClientResourceMap.md)), different resources can be mapped into custom location. These include any client loaded resources:
  * plugin texts
  * plugin javascripts
  * plugin css
  * templates

Copy original file into the custom location, and add resource map entry to load your version instead.

For example, following loads custom "mainview.html" from folder "custom" :
```
	mollify.App.init({
		"resource-map" : {
			"templates/mainview.html" : "custom/templates/mainview.html"
		},
		...
	});
```

For example, following loads custom localizations file for Registration plugin:
```
	mollify.App.init({
		"resource-map" : {
			"backend/plugin/Registration/localization/texts_en.json": "custom/localization/texts_registration_en.json"
		},
		...
	});
```

# Backend texts #

When a backend feature needs text resources, these are stored in a text file. With a customizations location defined, Mollify will look for any text resource from that location first.

Customizations location is defined in configuration.php with following setting:
```
	$CONFIGURATION = array(
		"customizations_folder" => "/Applications/MAMP/htdocs/mollify/custom/backend/",
		...
	);
```

Now any text resources are first searched from this folder.

For example customizing LostPassword plugin texts:
  1. Copy "backend/plugin/LostPassword/PluginLostPasswordMessages.txt" into customizations folder defined in configuration.php
  1. Make your own modifications