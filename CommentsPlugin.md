# Introduction #

Comments plugin is built-in plugin that provides possibility to comment any file or folder.

# Configuration #

Comments plugin does not have any parameters. Configure plugin by adding following into configuration.php (or merge into existing plugins array):

```
	$CONFIGURATION = array(
		...,
		"plugins" => array(
			"Comment" => array()
		)
	);
```

And following into client settings:

```
<script type="text/javascript">
	mollify.App.init({
		...
		}, [
			new mollify.plugin.CommentPlugin()
		]
	});
```

**NOTE** After configuration is done, comments plugin requires installation via Mollify update util.

# File list columns #

Comments plugin provides comments count column with column id `comment-count`. For example:

```
<script type="text/javascript">
	mollify.App.init({
		...
		"list-view-columns": {
			"name": {},
			"comment-count": {},
			"type": {},
			"size": {}
		}, [
			new mollify.plugin.CommentPlugin()
		]
	});
```