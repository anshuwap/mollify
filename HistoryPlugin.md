# Introduction #

History plugin allows file versioning.

When user uploads or copies file that already exists, the older file is versioned.

![http://www.mollify.org/images/screenshots/history_quota.png](http://www.mollify.org/images/screenshots/history_quota.png)

Versioned files are shown in the file listing, where they can be viewed, removed or restored.

![http://www.mollify.org/images/screenshots/history.png](http://www.mollify.org/images/screenshots/history.png)

# Installation & Configuration #

History plugin is installed with following steps:

  1. Extract plugin zip package into "backend/plugin" folder
  1. Add plugin configuration into "configuration.php" (see configuration options below)
  1. Run Mollify updater

Example minimum configuration:

```
$CONFIGURATION = array(
	...
	"plugins" => array(
		"History" => array(
			"folder" => "/data/mollify/history"
		),
		...
	)
```

History plugin has following options:
  * `folder`: Server filesystem path to the folder where versions are managed (use absolute path)
  * `max_versions`: The maximum number of versions stored for each file. When number is exceeded, older versions are removed
  * `exclude_folders`: List of root folder IDs that are excluded from version handling

NOTE! Folder used for version storage **should not** be
  * exposed via web server (ie. use location outside web root), only PHP needs read/write access to it
  * accessed/modified by other than plugin itself, as any modifications to the internal version data files may corrupt versioning. This includes a) publishing the folder in Mollify itself b) modifying the files directly with FTP  or server filesystem etc

Example full configuration:
```
$CONFIGURATION = array(
	...
	"plugins" => array(
		"History" => array(
			"folder" => "/data/mollify/history",
			"max_versions" => 3,
			"exclude_folders" => array("2")
		),
		...
	)
```

# License #

Plugin is released with Commercial Plugin license (http://www.mollify.org/license.php).

License costs 150 EUR, and download link will be provided after successful license payment.