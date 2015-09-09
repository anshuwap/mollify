Mollify has HTML5 uploader (based on [JQuery-File-Upload](https://github.com/blueimp/jQuery-File-Upload)) which supports all HTML5 uploading features if browser has the capabilities.

Uploader settings are defined in Mollify app init:

```
mollify.App.init({
	… ,
	"html5-uploader": {
		...
	}
}, [ … ]);
```

JQuery-File-Upload options are available at https://github.com/blueimp/jQuery-File-Upload/wiki/Options

# Avoiding server max upload size restrictions #

To avoid server side max upload size restrictions, following HTML5 options are available

## Chunked upload ##

Chunked uploading uploads smaller pieces at once, where each piece is smaller than the max size allowed. Following setting enables chunked upload:
```
...,
"html5-uploader": {
	maxChunkSize: 100000
}
```

## Stream upload ##

Stream uploading uses HTTP PUT method for sending file contents as a stream. Following client setting that enables stream upload:
```
...,
"html5-uploader": {
	multipart: false
}
```

Note that setting "maxChunkSize" should not be defined (or should be "false"), as it applies to non-multipart uploads as well.