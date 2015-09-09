Following settings are available in client (and their default values), used in index.html etc:

```
<script type="text/javascript">
	$(document).ready(function() {
		mollify.App.init({
			"service-path": "backend/",			// service path
			"limited-http-methods" : false,			// limited HTTP methods
			"view-url": false,				// reflect view in url
			"file-view": {					// file view options
				"drop-type": ...,			// customize drag&drop operation
				"create-empty-file-action": false,	// create empty file
				"default-view-mode": "list",		// default view mode
				"icon-view-thumbnails": false,		// show icon thumbnails in icon view mode
				"list-view-columns": {
					...				// file list column setup
				},
				...
			}},
			[
				...					// client plugins
			]
		);
	});
</script>
```

## Service path (`service-path`) ##

Location of the backend script relative to the index.html file.

## Reflect view in URL (`view-url`) ##

Mollify can reflect the current view in the browser url (if browser supports history pushState). By default this is disabled.

**More setting descriptions coming soon**


# File view options #

## Drag&drop operation (`drop-type`) ##

By default, drag&drop has following logic:
  * if dragging single item, and it is from same root folder, item is moved
  * if dragging single item, and it is from different root folder, item is copied
  * if dragging multiple items, items are copied

With this setting it is possible to customize the operation.

Setting can have single constant value that is applied always, for example:

```
	"file-view": {
                "drop-type": "copy"
	}
```

Setting can also have different values for dragging&dropping single or multiple items:

```
	"file-view": {
                "drop-type": {
			single: "move",
			multi: "copy"
		}
	}
```

Also, value can be a function where custom logic can be defined, for example:

```
	"file-view": {
		"drop-type": function(to, i) {
			// "to" is the target folder
			// "i" is dragged item/items

			if (window.isArray(i)) {
				//dragging multiple items
				return "copy";
			}
			// dragging single item
			var copy = (to.root_id != i.root_id);
			return copy ? "copy" : "move";
                }
	}
```

In this example multiple items are always copied, and single item copied when dragged from different root folder.

Values can be "copy" or "move".

## Create empty file (`create-empty-file-action`) ##

Action "create empty file" is not shown by default. With this setting, it can be enabled in folder action menu.

## Icon view thumbnails (`icon-view-thumbnails`) ##

With this option, icon view can display supported images (gif, png, jpg, jpeg) with thumbnail. Requires thumbnail feature enabled in backend https://code.google.com/p/mollify/wiki/BackendSettings#Enable_thumbnails_(_enable_thumbnails_)

## Default view mode (`default-view-mode`) ##

View mode selected when Mollify is launched, options are "`list`" (default), "`small-icon`" and "`large-icon`".

## File list column customization ##

File list columns can be customized with setting 'list-view-columns'.

The setting accepts following format:

```
<script type="text/javascript">
	mollify.App.init({
		"file-view": {
			"list-view-columns": {
				"column-1-id": { column-1-settings: "" },
				"column-2-id": { column-2-settings: "" },
				...
			}
		}
	});
</script>
```

Column ID identifies the data shown, which can be either built-in predefined column or plugin provided.

Mollify provides following columns:
  * 'name': File or folder name
  * 'type': File or folder type description
  * 'size': File size
  * 'file-modified': Last modification time
  * 'item-description': Item description

Following plugins provide more column data:
  * [Comments](http://code.google.com/p/mollify/wiki/CommentsPlugin#File_list_columns)

Column settings accepts following settings:
  * 'title': Text key (in localization file) shown in the column title. If not provided, default title used
  * 'sortable': Is column sortable
  * 'width': Column width in pixels
  * 'min-width': Column minimum width in pixels

Example configuration:

```
<script type="text/javascript">
	mollify.App.init({
		"list-view-columns": {
			"name": {width: 250},
			"type": {title:"myOwnColumnTitleKey"},
			"size": {}
		}
	});
</script>
```

## Actions customizations ##

File list actions can be customized with following syntax:

```
	mollify.App.init({
		"file-view": {	
			actions: {
				onClick: function(item, ctx) {
					return "open_menu";
				},
				onDblClick: function(item, ctx) {
					if (item.is_file) return "open_popup";
					return "go_into_folder";
				},
				onRightClick: function(item, ctx) {
					...
				}
			}
		},
		...
```

The object "actions" has can define following actions:
  * onClick (for regular click in file list)
  * onDblClick (for double click in file list)
  * onRightClick (for right click in file list)

These action functions are called with following parameters:
  * `item`: the file or folder
  * `ctx`: context with following properties
    * `viewtype`: file list type "list" or "icon"
    * `target`: action target, in list view the column
    * `element`: jQuery object for the element representing the file or folder
    * `viewport`: jQuery object for the viewport (used when displaying UI popups etc)
    * `container`: jQuery object for the container (used when displaying UI popups etc)
    * `folder`: folder that is currently displayed
    * `folder_permission`: current user permission for the item

Possible return value for the action functions:
  * `"open_popup"`: opens the popup
  * `"open_menu"`: opens the action menu
  * `"go_into_folder"`: goes into the folder (if item is folder)

  * `false` or no return value: default action
  * `true`: default action is skipped (used for custom actions)

The options with string value are shortcuts for most common options, but by returning `true` you can define your own action handler.