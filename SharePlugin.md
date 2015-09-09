# Introduction #

Share plugin is built-in plugin that provides possibility to share any file or folder.

# Configuration #

Share plugin has following settings:
  * uploader

Uploader setting is optional. If this setting is defined, the given uploader is used for folder share links. See chapter "Uploader setting" for more info.

Configure plugin by adding following into configuration.php (or merge into existing plugins array):

```
        $CONFIGURATION = array(
		...,
		"plugins" => array(
			"Share" => array(
				"uploader" => "PATH_TO_PUBLIC_UPLOADER"		// optional
			)
		)
	);
```

And following into client settings:

```
<script type="text/javascript">
	mollify.App.init({
		...
		},[
			new mollify.plugin.SharePlugin()
		]
	});
```

**NOTE** After configuration is done, share plugin requires installation via Mollify update util.

# Usage #

When share plugin has been registered in both, client and server, there will be a new option in file/folder popup "Share". This will open a list for all shares made for the file or folder, where you can also get the link for the share.

# File list columns #

Share plugin provides share count column with column id `share-info`. For example:

```
<script type="text/javascript">
	mollify.App.init({
		...
		"list-view-columns": {
			"name": {},
			"type": {},
			"size": {},
			"share-info": {},
		}, [
			new mollify.plugin.CommentPlugin()
		]
	});
```

Column will show how many shares current user has, or if someone else has shared the item. Clicking the icon also opens the share editor.

# File list columns #

Share plugin provides share count column with column id `share-info`. For example:

```
<script type="text/javascript">
	mollify.App.init({
		...
		"list-view-columns": {
			"name": {},
			"type": {},
			"size": {},
			"share-info": {},
		}, [
			new mollify.plugin.CommentPlugin()
		]
	});
```

Column will show how many shares current user has, or if someone else has shared the item. Clicking the icon also opens the share editor.

# Uploader setting #

By default, folder link will open basic HTML file uploader. With setting "uploader" the uploader can be changed.

In version 1.8, Plupload plugin (since version 1.0.6) supports public uploader. Configure it with following setting:

```
	$PLUGINS = array(
		"Share" => array(
			"uploader" => "plugin/Plupload/public"
		)
	);
```