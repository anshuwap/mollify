# Troubleshooting #

In case of errors, first check most common errors below.

If none of those help with your problem, do following steps before reporting new issue:

1. Check for configuration file errors

Modify script "backend/check.php" and enable the script as instructed in the file. Open url in your browser: http://yourhost/backend/check.php. If the script tells that file is OK, continue with other steps. Otherwise correct the problem as instructed, and try again.

2. Enable server side logging by adding following settings in configuration.php:

```
	$CONFIGURATION = array(
		...,
		"debug" => TRUE,
		"debug_log" => "/mollify/debug.log", // CHANGE THIS
	);
```

The debug log file must be located in a folder where PHP has read and write permissions.

3. Recreate the error situation

4. Open url in your browser: http://yourhost/backend/r.php/debug

NOTE! Debug url requires admin user.

NOTE! Debug log is emptied every time the url is opened.

5. Copy the debug info and attach into error report

NOTE! Once the logs are reported, remember to disable server side debug logging option as it generates lots of log entries.


---



# Most Common Errors #

## Error "Localization file missing" with IIS ##

With IIS server, JSON file downloads may require configuration:
  * make sure "Application Development Features" are enabled
  * make sure JSON file type has mime type configured (application/json)

## Error "Cannot modify header information" or "Got malformed JSON response" ##

Error "Cannot modify header information, headers already sent" means PHP output is started prematurely before the Mollify session is initialized.

Error "Got malformed JSON response" means response is received to the client, but it's format is not valid.

Usually these are due to following content in `configuration.php` file:
  * there are extra characters outside php tags: spaces, line feeds, tabs etc.
  * the file has been saved in UTF-8 without BOM information

## Error "`Parse error: syntax error, unexpected '{'`" ##

Error "`Parse error: syntax error, unexpected '{'`" occurs when PHP version is lower than the minimum required.

## Timezone error in log, or file popup does not work properly ##

Mollify uses PHP date function to get file modification timestamp, but if PHP timezone has not been set, this function will cause an error.

To add timezone, either edit PHP.ini and add setting "date.timezone", or add Mollify setting "timezone" in configuration.php. Possible values for both can be found from http://php.net/manual/en/timezones.php

```
	$CONFIGURATION = array(
		...,
		"timezone" => "Europe/Helsinki"
	);
```

## Error "`Cannot resolve host`" ##

Some features, like sharing file links, requires the public URL to access the resources. If possible, Mollify will try to resolve this, but in some cases this information is not available.

In this case, you need to define this manually with setting "host public address", see http://code.google.com/p/mollify/wiki/BackendSettings#Host_public_address_(_host_public_address_)

## Some operations, like rename or editing descriptions, don't work ##

Mollify uses REST HTTP protocol for the requests, which means all HTTP methods (ie. GET, PUT, POST and DELETE) must be supported by the web server. If these are not supported, many operations will fail (error in the server log indicate protocol error or missing resource).

To fix this, Mollify can be set to operate on limited HTTP methods mode by adding following setting in configuration.php:
```
	$CONFIGURATION = array(
		...,
		"enable_limited_http_methods" => TRUE
	);
```

and following client setting into the index.html (merge into your existing settings):
```
	<script type="text/javascript">
		mollify.App.init({
			...,
			"limited-http-methods": true,
		});
	</script>
```

## Filenames have corrupted chars, or file upload/download does not work with special chars ##

Mollify operates on UTF-8, so all the data handled must be in UTF-8. First make sure your Mollify web page is set to display UTF-8 content, with following tag in head section:

`<meta http-equiv="content-type" content="text/html; charset=UTF-8">`

If this is correct, then most likely your filesystem does not operate on UTF-8 (usually Windows). In this case you need to configure charset conversion:

1) If you are using MySQL, make sure the charset used is UTF-8. If not sure, you can add following variable: `$DB_CHARSET = "utf8";`

2) Setup conversion as instructed in http://code.google.com/p/mollify/wiki/BackendSettings#Convert_filesystem_filenames_(_convert_filenames_)

## Godaddy hosting and error "No input file specified" ##

1) Enter the Godaddy Hosting Control Center

2) Choose settings-file extensions management-default extensions

3) In the table that is presented edit ".php" Runs under "PHP 5.2.x" (instead of "PHP 5.2.x FastCGI")

## Zero byte download with WebDAV ##

1. Add "include 'Sabre/DAV/UUIDUtil.php';" in Sabre.includes.php
2. Ensure write access in both "temp" and "data" folder

## Downloaded files are corrupted ##

Make sure configuration.php does not have any characters outside <?php - ?> tags, including spaces or line feeds. These characters are interpreted as output, and will corrupt file downloads.

See troubleshooting step 1 to check your configuration.php.

## Error "Failed to get response from server" ##

When response failure error occurs, it is due to either of two things:

a) **Client cannot connect to the server**

b) **Client can connect to server, but server never responds**

In both cases, it is always best to first check PHP error log, usually the reason can be found there.

If there are no PHP error log entries, it is case of client cannot connect to server (ie. it tries to access backend interface files in a wrong place). If possible, you should check your web server access logs to see what resource it tries to access. Also check your backend interface file locations (see [Installation wiki](http://code.google.com/p/mollify/wiki/Installation)), that it is set up properly.

In such error cases, it may be useful to see client logs, see [configuration wiki](http://code.google.com/p/mollify/wiki/ConfigurationAdditionalOptions) (chapter 6.2, Client Logging) for more information. [Firebug](http://getfirebug.com/) plugin for Firefox browser is also very good tool for investigating what resources a page tries to access.

If, on the other hand, PHP log reveals errors, it is a case of invalid system setup or configuration.

Common PHP errors and their reasons are:
  * `Call to undefined function:  json_encode()`: PHP version is lower than 5.2, and is not supported


## Nothing happens on zip download, or downloaded zip is unreadable ##

When a file or a folder is downloaded as zip package and nothing happens or downloaded zip cannot be read, zip package processing fails. Check your PHP error log, and see [Installation wiki](http://code.google.com/p/mollify/wiki/Installation) for more information about zip options.