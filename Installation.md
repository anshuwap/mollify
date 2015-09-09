# 1. Installation #

  1. Unzip Mollify installation package (for example with command "`unzip mollify_[VER].zip`")
  1. Copy extracted directory "mollify" into your web server root directory (or any other place of your choice)
  1. Open installer at address "http://your.host.name/mollify/backend/install/". Installer will guide you through the configuration, for more information see chapter 3.
  1. Log in as admin and choose "Configuration" in app header to configure users and published folders

# 2. Upgrading #

> Upgrading Mollify requires following steps:

  1. Log into Mollify as admin user, and leave the session open for the upgrade process
  1. For backup, move or rename old installation directory "mollify" into different name or location
  1. Copy directory "mollify" from the latest Mollify installation package
  1. From backup, restore "configuration.php" into the new installation directory (as well as other customized files, such as "index.html")
  1. Open update utility at address "http://your.host.name/mollify/backend/update/" to check if further update actions are required.
  1. Once everything is ok, remove backup directory

# 3. Configuration #

> Mollify requires database, and supports MySQL and SQLite databases.

> Start configuration by creating "configuration.php" under directory "mollify/backend", examples can be found in folder "example" in installation package.

> Follow instructions based on configuration type (instructions for PDO is included in these pages):
    * [MySQL Database](ConfigurationMySql.md)
    * [SQLite Database](ConfigurationSQLite.md)

> For additional options, see additional settings on [client](ClientSettings.md) and [backend](BackendSettings.md).