# Introduction #

Lost password plugin is built-in plugin that provides possibility to users retrieve their lost password.

# Configuration #

Lost password plugin does not have any parameters. Configure plugin by adding following into configuration.php:

```
	$CONFIGURATION = array(
		...,
		"plugins" => array(
			"LostPassword" => array()
		)
	);
```

Lost password plugin requires mail notification enabled with setting:

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