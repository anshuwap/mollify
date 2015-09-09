# Introduction #

Notificator plugin is built-in plugin that provides mail notifications when certain events happen.

# Configuration #

Notificator plugin does not have any parameters. Configure plugin by adding following into configuration.php:

```
	$CONFIGURATION = array(
		...,
		"plugins" => array(
			"Notificator" => array()
		)
	);
```

Notificator requires mail notification enabled with setting:

```
	$CONFIGURATION = array(
		...,
		"enable_mail_notification" => TRUE
	);
```



Additionally, you can set the email address where notifications are sent from:

```
	$CONFIGURATION = array(
		...,
		"mail_notification_from" => "admin@yourhost.com"
	);
```

# Installation #

Notificator plugin requires database, so run Mollify updater after adding configuration.

# Variables #

Following variables are available in the title and message for all event types:

  * %user\_id% User id
  * %user\_name% User name
  * %user\_email% User email
  * %event\_time% Time of the event
  * %event\_type% Type of the event, for example "filesystem/rename"
  * %event\_main\_type% Main type of the event, for example "filesystem"
  * %event\_sub\_type% Sub type of the event, for example "rename"

For filesystem events, following variables are available:
  * %item\_id% Id of the file or folder
  * %item\_name% Name of the file or folder
  * %item\_path% Mollify path of the file or folder
  * %item\_internal\_path% Internal path of the file or folder (for local filesystem this the actual location in the filesystem)
  * %root\_name% Name of the published folder

Following applies only to rename, copy and move events:
  * %to\_item\_id% Id of the target file or folder
  * %to\_item\_name% Name of the target file or folder
  * %to\_item\_path% Mollify path of the target file or folder
  * %to\_item\_internal\_path% Internal path of the target file or folder (for local filesystem this the actual location in the filesystem)
  * %to\_root\_name% Name of the target published folder

For session events (login, logout and failed login):
  * %ip% IP address
  * %user% (only for failed login) user name

For registration creation and registration confirmation events:
  * %registration\_id% ID of the registration
  * %registration\_approve\_link% Link for admin approval

For registration creation event also (in addition to previous):
  * %registration\_key% Registration key