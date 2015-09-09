# Frequently Asked Questions #

### Problems with Internet Explorer ###

When Internet Explorer cannot open Mollify, and instead shows error like "Protocol error", this is due to IE not displaying the page in standards mode.

To fix this, make sure you have following meta parameter in host page:

`<meta http-equiv="X-UA-Compatible" content="IE=Edge"/>`

### How can I increase maximum file upload size? ###

Maximum file uload size is controlled by PHP, and can be changed in "php.ini" or ".htaccess" files. Settings involved are "`upload_max_filesize`", "`post_max_size`" and "`max_execution_time`".

Php.ini controls global values, and usually are not editable in hosting services. Htaccess files are folder specific settings, where global settings can be overridden.

For example:
```
php_value upload_max_filesize 10M 
php_value post_max_size 20M 
php_value max_execution_time 10000
```

These values define that each file can be 10 M in size, whereas total upload (all files combined) can be up to 20 M in size.

Max execution time is the time server allows any script to run before it is killed, and with very large files this might need increasing. Sometimes it is also necessary to define setting "memory\_limit", which controls the amount of memory script can allocate.

For changing settings for Mollify with htaccess, create this file in the backend folder (where configuration.php is located).

### How can I change the language? ###

Change following line in the host page according to your language:
```
<head>
    ...
    <script type="text/javascript" language="javascript" src="js/localization/texts_en.js"></script>
    ...
</head>
```

For example, change script into "`texts_fr.js`" for French. See client folder "localization" for language options, and [this](HowToLocalize.md) page for instructions on how to translate into new language.

### How can I translate Mollify into new language, or make corrections to existing translations? ###

See [HowToLocalize](HowToLocalize.md)