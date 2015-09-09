# Introduction #

Folder quota plugin allows setting maximum space for a folder.

For a user, quota is visible in the file view header. When any action (upload, copy, move etc) would exceed the quota available, it is rejected.

![http://www.mollify.org/images/screenshots/history_quota.png](http://www.mollify.org/images/screenshots/history_quota.png)

Quota is set by admin in root folder level:

![http://www.mollify.org/images/screenshots/quota_admin.png](http://www.mollify.org/images/screenshots/quota_admin.png)

# Installation & Configuration #

Quota plugin is installed with following steps:

  1. Extract plugin zip package into "backend/plugin" folder
  1. Add plugin configuration into "configuration.php" (see configuration options below)
  1. Run Mollify updater

Example minimum configuration:

```
$CONFIGURATION = array(
	...
	"plugins" => array(
		"Quota" => array(),
		...
	)
```

Quota plugin does not require any configuration, but has following options:
  * `registration_user_folder_quota`: Automatic quota set for user folders when user registers with Registration plugin (value in Mb)

Example full configuration:
```
$CONFIGURATION = array(
	...
	"plugins" => array(
		"Quota" => array(
			"registration_user_folder_quota" => 1000	
		),
		...
	)
```

# License #

Plugin is released with Commercial Plugin license (http://www.mollify.org/license.php).

License costs 150 EUR, and download link will be provided after successful license payment.