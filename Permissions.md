# Overview #

Mollify has two kinds of permissions: generic permissions and filesystem related permissions.

Both can be set in user, group or system level, but filesystem permissions can also be set in filesystem items where permission inherit with the filesystem structure.


# Generic permissions #

Generic permissions:
  * change password

Generic permissions can be set in following levels:
  * user level (in admin view "User" properties)
  * user group level (in admin view "Group" properties)
  * system level (in admin view "Permissions" or via "User" properties view)

This is also the priority permissions effect.

Example 1, if user U1 belongs to group G1 and G2, and following permissions are set (change password)
  * system default: no
  * group G1: no
  * user U1: yes

Effective permission is "yes", since user permission has top priority.

Example 2, if user U1 belongs to group G1 and G2, and following permissions are set (change password)
  * system default: no
  * group G1: no
  * group G2: yes

Effective permission is "yes". Group permissions are with same priority, but in this case greater permission (the more permissive option) is chosen.

# Filesystem permissions #

Filesystem permissions:
  * filesystem item access
  * edit item description
  * comment item (Comments plugin)
  * share item (Share plugin)

## Filesystem permission rules ##

Filesystem permissions can be set in following levels:
  * user specific filesystem item (in item permission editor)
  * group specific filesystem item (in item permission editor)
  * filesystem item default (in item permission editor)
  * user default (in admin view "User" properties)
  * user group default (in admin view "Group" properties)
  * system default (in admin view "Permissions" or via "User"/"Group" properties view)

This is also the priority permissions effect.

Permission editor can be opened by admin users from item (file/folder) context menu, or popup details.

Permission editor shows all permissions for the selected item (file/folder), and also allows inspecting user effective permission.

Example 1, if user U1 belongs to group G1 and G2, and following permissions are set (filesystem item access)
  * system default: r (read only)
  * user U1 default: rw (read/write, no delete)

Effective permission for "example.txt" is "read/write, no delete", because user default overrides system default.

Example 2, if user U1 belongs to group G1 and G2, and following permissions are set (filesystem item access)
  * system default: r (read only)
  * user U1 default: rw (read/write, no delete)
  * file "example.txt" default: r (read only)

Effective permission for "example.txt" is "read only", because file default overrides user or system defaults.

Example 3, if user U1 belongs to group G1, user U2 does not, and following permissions are set (filesystem item access)
  * system default: n (no rights)
  * file "example.txt" default: r (read only)
  * file "example.txt" permission for user group G1: rwd (read&write&delete)

Effective permission for "example.txt" for user U1 is "read&write&delete", and for user U2 "read only".

Example 4, if user U1 belongs to group G1 and G2, and following permissions are set (filesystem item access)
  * system default: n (no right)
  * group G1 default: rw (read/write, no delete)
  * group G2 default: rwd (read&write&delete)

Effective permission for user U1 default permission is "read&write&delete", because group rights are with same priority and greater applies (read&write&delete, see [permission values list](https://code.google.com/p/mollify/wiki/Permissions?#Permission:_Filesystem_item_access)).

## Permission: Filesystem item access ##

Filesystem item access permission has following values (in permissiveness order):
  * no (None)
  * r (Read only)
  * rw (Read/write)
  * rwd (Read/write/delete)