# Introduction #

Event logging plugin is built-in plugin that provides logging all user actions, and viewing them via admin tool.

# Configuration #

File viewer has following settings:
  * logged\_events

Logged events is optional, and limits the event types logged. If this setting is defined, all events with matching id will be logged (wildcard match is done automatically). To log all events, leave this setting out.

For example:

```
	$CONFIGURATION = array(
		...,
		"plugins" =>
			"EventLogging" => array(
				"logged_events" => array("filesystem/", "session/")
			)
		)
	);
```