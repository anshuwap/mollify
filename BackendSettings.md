Following settings are available in backend (and their default values):

```
$CONFIGURATION = array(
	"debug" => FALSE,						// debug mode
	"debug_log" => NULL,
	"timezone" => NULL,						// server timezone
	"host_public_address" => NULL,					// host public address
	"email_login" => FALSE,						// allow email login
	"session_name" => NULL,						// Mollify session name
	"session_time" => 7200,						// Mollify session time
	"enable_limited_http_methods" => FALSE,				// limited HTTP methods
	"support_output_buffer" => FALSE,				// support for output buffer
	"mime_types" => array(),					// MIME types
	"enable_change_password" => TRUE,				// enable change password
	"enable_descriptions" => FALSE,					// enable descriptions
	"enable_mail_notification" => FALSE,				// enable email notification
	"mail_notificator_class" => "mail/MailNotificator.class.php",	// mail notificator class
	"mail_notification_from" => "Admin",				// mail notification sender
	"enable_retrieve_url" => FALSE,					// enable URL retrieve
	"url_retriever_class" => "UrlRetriever.class.php",		// URL retriever implementation
	"allowed_file_upload_types" => array(),				// allowed file upload types
	"forbidden_file_upload_types" => array(),	// forbidden file upload types
	"new_folder_permission_mask" => 0755,				// new folder permission mask
	"convert_filenames" => FALSE,					// convert filesystem filenames
	"authentication_methods" => array("pw"),			// supported authentication methods
	"ldap_server" => NULL,						// LDAP server address
	"ldap_use_starttls" => FALSE,					// LDAP TLS
	"ldap_conn_string" => NULL,					// LDAP connection string
	"ldap_search" => NULL,
	"ldap_base_dn" => NULL,
	"ldap_bind_dn" => NULL,
	"ldap_bind_pw" => NULL,
	"upload_temp_dir" => NULL,					
	"enable_thumbnails" => TRUE,
	"enable_folder_protection" => FALSE,				// enable folder protection,
	"published_folders_root" => NULL				// published folders root,
	"customizations_folder" => NULL,				// customizations folder location
	"no_udev_random" => FALSE					// disable PHPPass lib /dev/urandom
);
```

## Debug mode (`debug`) ##

Enables backend debug mode. This writes debug information to PHP error log and enables debug url, used for [troubleshooting](Troubleshooting.md). Possible values are TRUE and FALSE (default).

## Server timezone (`timezone`) ##

Server timezone, needed for PHP datetime functions. Without this information, some functions may fail. For values, see http://php.net/manual/en/timezones.php

## Host public address (`host_public_address`) ##

Public address to the server where Mollify is installed. If not given, Mollify uses the PHP variable HTTP\_REFERER to determine this. Some servers don't provide this, and all requests will fail until given. The value is the URL for the site root including the protocol, for example "http://www.yoursite.com"

## Limited HTTP methods (`enable_limited_http_methods`) ##

Mollify uses REST API for client/server communication, which relies on all HTTP methods, ie. GET, PUT, POST and DELETE. Some servers don't support other methods than GET and POST, and in this case Mollify must be configured to use limited HTTP methods. Possible values are TRUE and FALSE (default).

## Support for output buffer (`support_output_buffer`) ##

If web server is configured to use output buffer, set this option to TRUE, and it will flush buffer on file downloads. See http://www.php.net/manual/en/function.ob-flush.php

## MIME types (`mime_types`) ##

File downloads may require special MIME type in order to be processed correctly. Mollify has predefined common MIME types, but this variable allows setting custom types.

Mime types are defined by the file extension, for example:

```
	$CONFIGURATION = array(
		"mime_types" => array("avi" => "video/divx", "wav" => "audio/x-wav")
	);
```

## Enable change password (`enable_change_password`) ##

By default, changing password is enabled, but setting this to FALSE you can prevent users from changing passwords.

## Enable descriptions(`enable_descriptions`) ##

Enable file and folder descriptions (by default disabled).

## Enable email notification (`enable_email_notification`) ##

Enable email notifications from any component or plugin (by default disabled). See also setting "Mail notificator class"

## Mail notificator class (`mail_notificator_class`) ##

Define the class responsible for sending the emails. This setting allows changing the default mail sender, for example, to match server requirements.

## Mail notification sender (`mail_notification_from`) ##

Define notification sender address (and optionally also name) used in mail notifications (for example "admin@yourhost.com", "Administrator <admin@yourhost.com>"). If no value is given (default), mail is sent with system default.

## Enable URL retrieve (`enable_url_retrieve`) ##

Enable url retrieve, which allows adding remote file directly to Mollify (by default disabled). See also setting "URL retriever class"

## URL retriever class (`url_retriever_class`) ##

Define the URL retriever implementation. If default implementation is not sufficient, custom retriever can be defined with this setting.

## Allowed and forbidden file upload types (`allowed_file_upload_types`, `forbidden_file_upload_types`) ##

Define the file types that are allowed or forbidden to upload. By default, all types are allowed.

```
	$CONFIGURATION = array(
		"forbidden_file_upload_types" => array("gif", "png", "jpg")
	);
```

or

```
	$CONFIGURATION = array(
		"allowed_file_upload_types" => array("gif", "png", "jpg")
	);
```

Note that only one of the settings should be defined. If forbidden types are defined, every type listed will be rejected. But if allowed types are defined, any type not in the list will be rejected.

## New folder permission mask (`new_folder_permission_mask`) ##

Define the OS permission mask for the folders created. By default mask 0755 is used (read+write+execute permissions for the user owner, and read+execute for group and other), but on some server configurations other mask might be required.

## Convert filesystem filenames (`convert_filenames`) ##

Mollify uses UTF-8 internally, and filesystems that support this charset work fine without any conversions. Some servers may operate on different charset, and then conversion is required.

With value TRUE, Mollify will try to guess the conversion needed:
```
	$CONFIGURATION = array(
		"convert_filenames" => TRUE
	);
```

Otherwise the OS charset has to be defined here:
```
	$CONFIGURATION = array(
		"convert_filenames" => "CP1252"
	);
```

## Enable folder protection (`enable_folder_protection`) ##

Enable folder protection which prevents browser access to stored files. When enabled, htaccess file from "backend/include/apache" will be copied to the folder root.

## Supported authentication methods (`authentication_methods`) ##

Define supported authentication methods. By default, Mollify expects users to be authenticated only with passwords, but with this setting other methods can be enabled as well. Possible values are "pw" (password), "remote" (single sign on) and "ldap".

In admin section, you can set user authentication method. If user has "default" method selected, the first method listed in this setting will be used.

```
	$CONFIGURATION = array(
		"authentication_methods" => array("pw", "ldap")
	);
```

LDAP authentication requires configuring LDAP server, see options "ldap\_server" and "ldap\_conn\_string". This option requires that user is configured in Mollify, but authentication is done with LDAP server.

Single sign on authentication relies on Apache environment variable "REMOTE\_USER". When option "remote" is enabled in the list, Mollify automatically logs in with the user found in the "REMOTE\_USER" variable.

## LDAP authentication ##

Using LDAP authentication requires php5-ldap module installed in the server.

When LDAP authentication is enabled, following settings can be used:
  * `ldap_server`: LDAP server address
  * `ldap_use_starttls`: use TLS in LDAP session
  * `ldap_conn_string`: connection string for authenticated binding
  * `ldap_search`: search string for anonymous binding
  * `ldap_base_dn`: base DN for anonymous binding
  * `ldap_bind_dn`: bind DN for anonymous binding
  * `ldap_bind_pw`: password for anonymous binding

LDAP can be used with two ways: authenticated and anonymous binding.

### Authenticated binding ###

With authenticated binding, current user credentials are used for LDAP binding. With this option, define settings `ldap_server` and `ldap_conn_string`

By default, user name is added in the beginning of the given connection string, otherwise the place must be marked with "`[USER]`".

For example, if user "john.doe" is logging in
  * connection string "`@domain.com`" would authenticate with "`john.doe@domain.com`"
  * connection string "`uid=[USER],ou=organization,dc=domain,dc=net`" would authenticate with "`uid=john.doe,ou=organization,dc=domain,dc=net`"

```
	$CONFIGURATION = array(
		"ldap_server" => "ldap://your.ldapserver.com",
		"ldap_conn_string" => "uid=[USER],ou=organization,dc=domain,dc=net"
	);
```

### Anonymous binding ###

With anonymous binding, defined credentials are used for LDAP binding and searching the user. With this option, define settings `ldap_server` and `ldap_search`, `ldap_base_dn`, `ldap_bind_dn` and `ldap_bind_pw`.

LDAP binding is done using `ldap_base_dn`, `ldap_bind_dn` and `ldap_bind_pw`, and user search using `ldap_search`.

## Upload temporary directory (`upload_temp_dir`) ##

Define the temporary directory used for custom file uploaders. By default the OS temp dir is used.

## Enable thumbnails (`enable_thumbnails`) ##

Enable thumbnails support. This feature is used by, for example, icon view and image preview to show smaller image preview.

This feature requires GD library installed (http://php.net/manual/en/image.installation.php)

## Published folders root (`published_folders_root`) ##

With this setting, all the published folders can be restricted into specified filesystem location. For example value "/data/folders" would mean that all folders must be under this folder, and for example publishing folder with path "Test" would mean path "/data/folders/Test"

## Disable /dev/urandom access (`no_udev_random`) ##

With this setting, PHPass library can be prevented accessing /dev/urandom (used for generating random sequences). By default FALSE