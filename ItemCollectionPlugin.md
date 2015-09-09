# Introduction #

Item collection plugin is built-in plugin that provides possibility to store dropbox contents and share them via public link.

# Configuration #

Configure plugin by adding following into configuration.php (or merge into existing plugins array):

```
        $CONFIGURATION = array(
		...,
		"plugins" => array(
			"ItemCollection" => array()
		)
	);
```

And following into client settings:

```
<script type="text/javascript">
	mollify.App.init({
			...
		}, [
			...,
			new mollify.plugin.ItemCollectionPlugin()
		]
	});
```

**NOTE** After configuration is done, share plugin requires installation via Mollify update util.

# Usage #

When share plugin has been registered in both, client and server, there will be a new options in dropbox: "Store collection" and "(View) Stored collections...". In the stored collections list, user can share the collection and get the public link which will download the contents in zip package.

# Requirements #

Item collection plugins requires
  * zip feature enabled
  * share plugin registered