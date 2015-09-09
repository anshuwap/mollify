# Installing plugins #

Installation package contains many plugins, which only needs to be configured, see "Built-in plugins" below.

Additional plugins are installed simply by downloading the plugin package and unzipping them into "backend/plugin" folder.

Plugins are registered with a variable PLUGINS in configuration.php:

```
$CONFIGURATION = array(
	...,
	"plugins" => array(
		[PLUGIN_NAME] => [SETTINGS],
		...
	)
);
```

Plugin name is the same as the folder name in the installation package. Settings are plugin specific options, or empty array.

For example:

```
$CONFIGURATION = array(
	...,
	"plugins" => array(
		"PluginA" => array("some_options" => "foo"),
		"PluginB" => array()
	)
);
```

Some plugins may require client side plugin, see plugin specific instructions for each plugin.

Whenever plugins are installed or updated, open the Mollify updater util to check if database update is required.

# Commercial Plugins #
  * [Quota](QuotaPlugin.md)
  * [History](HistoryPlugin.md)

# Built-in Plugins #
  * [Event logging](EventLoggingPlugin.md)
  * [User registration](RegistrationPlugin.md)
  * [File viewer and editor](FileViewerEditorPlugin.md)
  * [Lost password](LostPasswordPlugin.md)
  * [Notificator](NotificatorPlugin.md)
  * [Archiver](ArchiverPlugin.md)
  * [Share](SharePlugin.md)
  * [Item collection](ItemCollectionPlugin.md)
  * [Comments](CommentsPlugin.md)