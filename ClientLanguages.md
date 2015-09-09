# Languages #

By default, Mollify opens in english. To change default language, use following client option:
```
	$(document).ready(function(){	
		mollify.App.init({
			"language": {
				default: "de"
			},
			...
		});
	});
```

## User language ##

By default all users use the default language. To enable user specific language, list all available options
```
	$(document).ready(function(){	
		mollify.App.init({
			"language": {
				default: "de",
				options: ["en", "de"]
			},
			...
		});
	});
```

This will use Deutsch as default language for all users, but in user admin view it is possible to set user language to English as well.

# How to correct localization errors #

To correct localization errors, do following steps:

1) Open your copy of the localization file, Mollify localization files are located at "localization/texts\_xx.json"

2) Find the incorrect value, and correct it. Save the file

3) Reload Mollify to verify the result

4) Send the corrected localizations to me, and I will include them in the next version

If some texts are missing, use the same procedure to add new lines. Reference texts can be taken from English localization file.

If your language is not found, see next chapter on how to create a new localization.


# How to localize into new language #

To localize into new language, do following steps:

1) Copy English version "localization/texts\_en.json" into a new file with your locale identifier (from installation package, or from [version control](https://code.google.com/p/mollify/source/browse/trunk/localization/texts_en.json))

2) Edit the file, and change the locale identifier in the beginning of file

3) Localize all the values in the file

4) Edit the index.html page, and change the default language

5) Send the localization file to me, and I will include it in the next version