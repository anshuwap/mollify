Published folders should not be accessible via regular web access. Using Mollify does not require web access to published folders as it has direct filesystem access.

But since Mollify cannot prevent the web server from serving those files, there are two options how to prevent it:

1) Place all published folders outside www root. With this option, there is no way users can access the files with browser via Apache. Only PHP has to have read and write access to the folders.

2) Prevent access with web server access rules, for example htaccess rule "deny from all" in Apache.

In all cases, published folders should not be located under Mollify folders (not client or backend).