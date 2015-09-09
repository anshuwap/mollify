# Introduction #

Archiver plugin is built-in plugin that provides possibility to extract zip archives (later on also tar archives etc) and compress files, as well as download files/folders compressed.

# Configuration #

Archiver plugin does not have any parameters. Configure plugin by adding following into configuration.php (or merge into existing plugins array):

```
        $CONFIGURATION = array(
		...,
		"plugins" => array(
			"Archiver" => array(
				"compressor" => "[COMPRESSOR]"
				"enable_download" => TRUE/FALSE,
				"enable_compress" => TRUE/FALSE
				"enable_extract" => TRUE/FALSE
			)
		)
	);
```

Value "COMPRESSOR" is the method used for compressing files, options are:
  * `ziparchive` Default PHP zip library (this is used if setting is not defined)
  * `native` Uses OS native tools to create the package (not available on all platforms)
  * `raw` PHP implementation of zip packaging

Values "enable\_download", "enable\_compress" and "enable\_extract" control whether archiver actions are available. By default, all actions are enabled, but with this option they can be disabled (by settings FALSE).

Add following into client settings:

```
<script type="text/javascript">
	mollify.App.init({
		...
		}, [
			new mollify.plugin.ArchiverPlugin()
		]
	});
```