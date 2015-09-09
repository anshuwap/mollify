# Introduction #

Registration plugin is built-in plugin that provides functionality for user self registration and confirmation.

# Configuration #

Registration has following settings:
  * require\_approval (by default TRUE)
  * permission
  * groups
  * folders
  * user\_folder

If approval is required, confirmed registration will not create user yet. Instead, confirmed registrations are listed in admin view, where admin user can approve. Using notificator plugin, admins can get email notifications for confirmed registrations.

With value "permissions", user default permissions can be defined. String value is considered filesystem access permission, but with array syntax it is possible to define all permissions, see [Permissions](Permissions.md) for possible values. If not given, no default permissions are added, and system defaults are applied.

Groups is a list of user group ids for the created user. If not set, user is not assigned to any groups.

Folders is a list of folder ids for the created user. If not set, user is not assigned to any folders.

User folder is an option that creates user folders automatically after registration (leave out if no folders need to be created).

For example:

```
	$CONFIGURATION = array(
		...,
		"plugins" => array(
			"Registration" => array(
				"require_approval" => TRUE,
				"permissions" => "rwd",
				"folders" => array("1", "3"),
				"user_folder" => array("path" => "/foo/users", "folder_name" => "User Folder", "add_to_users" 
=> array("1", "2"))
			)
		)
	);
```

Registration plugin requires installation via Mollify update util.

To enable mail notifications, add following settings:
```
	$CONFIGURATION = array(
		...,
		"enable_mail_notification" => TRUE,
		"mail_notification_from" => "admin@your.host"
	);
```

The setting "mail\_notification\_from" defines the address which the mail is sent from.

Register client plugin with following client settings:

```
<script type="text/javascript">
	mollify.App.init({
		...
		}, [
			new mollify.plugin.RegistrationPlugin()
		]
	});
```

## Folders ##

It is possible to list only folder ids that are added to the new user.

```
	$CONFIGURATION = array(
		...,
		"plugins" => array(
			"Registration" => array(
				"folders" => array("1", "3"),
			)
		)
	);
```

To give folder properties, use following syntax:

```
	$CONFIGURATION = array(
		...,
		"plugins" => array(
			"Registration" => array(
				"folders" => array(
					"1" => array(
						"name" => "Folder name",
						"permissions" => "rw"
					)
				)
			)
		)
	);
```

Property "name" (optional) can set a name different than the default.

Property "permissions" can set assigned folder permissions for the new user. If only single string value given, it is assumed to be filesystem item access permission (see [Permissions](Permissions.md)).

To give other permissions (see [Permissions](Permissions.md)), use array syntax:

```
	$CONFIGURATION = array(
		...,
		"plugins" => array(
			"Registration" => array(
				"folders" => array(
					"1" => array(
						"name" => "Folder name",
						"permissions" => array(
							"filesystem_item_access" => "rw",
							"edit_description" => "1"
						)
					)
				)
			)
		)
	);
```

## User Folder ##

For user folders, the parameters are:
  * "`path`" is the place where user folders are created, use a folder that is not published.
  * "`folder_name`" is a name for the folder. Leave it out if you want to name it after the user.
  * "`add_to_users`" is a list of user ids that also see this folder. Useful for listing admins who need to control these folders.
  * "`permissions`" is the list of permissions assigned to the created folder. By default, user will get read/write/delete permission, but with this option all permissions can be defined in the same way as explained in chapter Folders.

# Registration #

Once registration plugin is installed, registration page is at "http://[PATH_TO_MOLLIFY]/?v=registration/new". Users can fill out the form presented, and they will receive a notification mail. Mail provides a link that will confirm the registration.

Optionally confirmation can be done at "http://[PATH_TO_MOLLIFY]/?v=registration/confirm&email=[EMAIL]", where [EMAIL](EMAIL.md) is the email address (in url encoded format) given in registration. This opens a form where the registration key is required.