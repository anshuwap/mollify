<?php

	/**
	 * Copyright (c) 2008- Samuli J�rvel�
	 *
	 * All rights reserved. This program and the accompanying materials
	 * are made available under the terms of the Eclipse Public License v1.0
	 * which accompanies this distribution, and is available at
	 * http://www.eclipse.org/legal/epl-v10.html. If redistributing this code,
	 * this entire header must remain intact.
	 */

	class MySQLConfigurationProvider extends ConfigurationProvider {
		private $db;
		
		public function __construct($settings) {
			global $DB_HOST, $DB_USER, $DB_PASSWORD, $DB_DATABASE, $DB_TABLE_PREFIX;
			
			if (!isset($DB_USER) or !isset($DB_PASSWORD)) throw new ServiceException("INVALID_CONFIGURATION", "No database information defined");
			
			if (isset($DB_HOST)) $host = $DB_HOST;
			else $host = "localhost";
			
			if (isset($DB_DATABASE)) $database = $DB_DATABASE;
			else $database = "mollify";

			if (isset($DB_TABLE_PREFIX)) $tablePrefix = $DB_TABLE_PREFIX;
			else $tablePrefix = "";
			
			require_once("include/mysql/MySQLDatabase.class.php");
			$this->db = new MySQLDatabase($host, $DB_USER, $DB_PASSWORD, $database, $tablePrefix);
			$this->db->connect();
		}
		
		public function isAuthenticationRequired() {
			return TRUE;
		}

		public function getSupportedFeatures() {
			$features = array('description_update', 'configuration_update');
			if ($this->isAuthenticationRequired()) $features[] = 'permission_update';
			return $features;
		}
		
		public function getInstalledVersion() {
			return $this->db->query("SELECT value FROM ".$this->db->table("parameter")." WHERE name='version'")->value(0);
		}
		
		public function checkProtocolVersion($version) {}
	
		public function findUser($username, $password) {
			$result = $this->db->query(sprintf("SELECT id, name FROM ".$this->db->table("user")." WHERE name='%s' AND password='%s'", $this->db->string($username), $this->db->string($password)));
			$matches = $result->count();
			
			if ($matches === 0) {
				Logging::logError("No user found with name [".$username."], or password was invalid");
				return NULL;
			}
			
			if ($matches > 1) {
				Logging::logError("Duplicate user found with name [".$username."] and password");
				return FALSE;
			}
			
			return $result->firstRow();
		}
		
		public function getAllUsers() {
			return $this->db->query("SELECT id, name, permission_mode FROM ".$this->db->table("user")." ORDER BY id ASC")->rows();
		}

		public function getUser($id) {
			return $this->db->query(sprintf("SELECT id, name FROM ".$this->db->table("user")." WHERE id='%s'", $this->db->string($id)))->firstRow();
		}
		
		public function addUser($name, $pw, $permission) {
			$this->db->update(sprintf("INSERT INTO ".$this->db->table("user")." (name, password, permission_mode) VALUES ('%s', '%s', '%s')", $this->db->string($name), $this->db->string($pw), $this->db->string($permission)));
			return TRUE;
		}
	
		public function updateUser($id, $name, $permission) {
			$affected = $this->db->update(sprintf("UPDATE ".$this->db->table("user")." SET name='%s', permission_mode='%s' WHERE id='%s'", $this->db->string($name), $this->db->string($permission), $this->db->string($id)));			
			if ($affected === 0)
				throw new ServiceException("INVALID_REQUEST", "Invalid update user request, user ".$id." not found");	
			return TRUE;
		}
		
		public function removeUser($userId) {
			$id = $this->db->string($id);

			$this->db->startTransaction();			
			$this->db->update(sprintf("DELETE FROM ".$this->db->table("user_folder")." WHERE user_id='%s'", $id));
			$this->db->update(sprintf("DELETE FROM ".$this->db->table("item_permission")." WHERE user_id='%s'", $id));
			$affected = $this->db->update(sprintf("DELETE FROM ".$this->db->table("user")." WHERE id='%s'", $id));
			if ($affected == 0)
				throw new ServiceException("INVALID_REQUEST", "Invalid delete user request, user ".$id." not found");	
			$this->db->commit();					
			return TRUE;
		}
			
		private function getPassword($id) {
			return $this->db->query(sprintf("SELECT password FROM ".$this->db->table("user")." WHERE id='%s'", $this->db->string($id)))->value(0);
		}
	
		public function changePassword($id, $old, $new) {
			if ($old != $this->getPassword($id)) throw new ServiceException("UNAUTHORIZED");
			
			$affected = $this->db->update(sprintf("UPDATE ".$this->db->table("user")." SET password='%s' WHERE id='%s'", $this->db->string($new, $db), $this->db->string($id)));
			if ($affected == 0)
				throw new ServiceException("INVALID_REQUEST", "Invalid change password request, user ".$id." not found");			
			return TRUE;
		}
	
		public function resetPassword($id, $pw) {
			$affected = $this->db->update(sprintf("UPDATE ".$this->db->table("user")." SET password='%s' WHERE id='%s'", $this->db->string($pw, $db), mysql_real_escape_string($id)));
			if ($affected == 0)
				throw new ServiceException("INVALID_REQUEST", "Invalid reset password request, user ".$id." not found");
			return TRUE;
		}
	
		public function getFolders() {
			return $this->db->query("SELECT id, name, path FROM ".$this->db->table("folder")." ORDER BY id ASC")->rows();
		}
	
		public function addFolder($name, $path) {
			$this->db->update(sprintf("INSERT INTO ".$this->db->table("folder")." (name, path) VALUES ('%s', '%s')", $this->db->string($name), $this->db->string($path)));
			return TRUE;
		}
	
		public function updateFolder($id, $name, $path) {
			$affected = $this->db->update(sprintf("UPDATE ".$this->db->table("folder")." SET name='%s', path='%s' WHERE id='%s'", $this->db->string($name), $this->db->string($path), $this->db->string($id)));
			if ($affected == 0)
				throw new ServiceException("INVALID_REQUEST", "Invalid update folder request, folder ".$id." not found");
			return TRUE;
		}
		
		public function removeFolder($id) {
			$plainId = $id.":";	//TODO
			
			$this->db->startTransaction();
			$this->db->update(sprintf("DELETE FROM ".$this->db->table("user_folder")." WHERE folder_id='%s'", $this->db->string($id)));
			$this->db->update(sprintf("DELETE FROM ".$this->db->table("item_description")." WHERE item_id like '%s%%'", $plainId));
			$this->db->update(sprintf("DELETE FROM ".$this->db->table("item_permission")." WHERE item_id like '%s%%'", $plainId));
			$affected = $this->db->update(sprintf("DELETE FROM ".$this->db->table("folder")." WHERE id='%s'", $this->db->string($id)));
			if ($affected == 0)
				throw new ServiceException("INVALID_REQUEST","Invalid delete folder request, folder ".$id." not found");
			$this->db->commit();
			return TRUE;
		}

		public function getUserFolders($userId) {
			$folderTable = $this->db->table("folder");
			$userFolderTable = $this->db->table("user_folder");
			
			return $this->db->query(sprintf("SELECT ".$folderTable.".id, ".$userFolderTable.".name, ".$folderTable.".name as default_name, ".$folderTable.".path FROM ".$userFolderTable.", ".$folderTable." WHERE user_id='%s' AND ".$folderTable.".id = ".$userFolderTable.".folder_id", $this->db->string($userId)))->rows();
		}
		
		public function addUserFolder($userId, $folderId, $name) {
			if ($name != NULL) {
				$this->db->update(sprintf("INSERT INTO ".$this->db->table("user_folder")." (user_id, folder_id, name) VALUES ('%s', '%s', '%s')", $this->db->string($userId), $this->db->string($folderId), $this->db->string($name)));
			} else {
				$this->db->update(sprintf("INSERT INTO ".$this->db->table("user_folder")." (user_id, folder_id, name) VALUES ('%s', '%s', NULL)", $this->db->string($userId), $this->db->string($folderId)));
			}
						
			return TRUE;
		}
	
		public function updateUserFolder($userId, $folderId, $name) {
			if ($name != NULL) {
				$affected = $this->db->update(sprintf("UPDATE ".$this->db->table("user_folder")." SET name='%s' WHERE user_id='%s' AND folder_id='%s'", $this->db->string($name), $this->db->string($userId), $this->db->string($folderId)));
			} else {
				$affected = $this->db->update(sprintf("UPDATE ".$this->db->table("user_folder")." SET name = NULL WHERE user_id='%s' AND folder_id='%s'", $this->db->string($userId), $this->db->string($folderId)));
			}
	
			if ($affected == 0)
				throw new ServiceException("INVALID_REQUEST", "Invalid update user folder request, folder ".$folder_id." not found for user ".$user_id);
			return TRUE;
		}
		
		public function removeUserFolder($userId, $folderId) {
			$this->db->update(sprintf("DELETE FROM ".$this->db->table("user_folder")." WHERE folder_id='%s' AND user_id='%s'", $this->db->string($folderId), $this->db->string($userId)));
			return TRUE;
		}
		
		public function getDefaultPermission($userId = "") {
			$mode = strtoupper($this->db->query(sprintf("SELECT permission_mode FROM ".$this->db->table("user")." WHERE id='%s'", $this->db->string($userId)))->value(0));
			$this->env->authentication()->assertPermissionValue($mode);
			return $mode;
		}
		
		function getItemDescription($item) {
			$result = $this->db->query(sprintf("SELECT description FROM ".$this->db->table("item_description")." WHERE item_id='%s'", $this->itemId($item)));
			if ($result->count() < 1) return NULL;
			return $result->value(0);
		}
				
		function setItemDescription($item, $description) {
			$id = $this->itemId($item);
			$desc = $this->db->string($description);
			
			$affected = $this->db->update(sprintf("UPDATE ".$this->db->table("item_description")." SET description='%s' WHERE item_id='%s'", $desc, $id));
			if ($affected === 0)
				$this->db->update(sprintf("INSERT INTO ".$this->db->table("item_description")." (item_id, description) VALUES ('%s','%s')", $id, $desc));
			return TRUE;
		}
	
		function removeItemDescription($item) {
			if (!$item->isFile()) {
				$this->db->update(sprintf("DELETE FROM ".$this->db->table("item_description")." WHERE item_id like '%s%%'", $this->itemId($item)));
			} else {
				$this->db->update(sprintf("DELETE FROM ".$this->db->table("item_description")." WHERE item_id='%s'", $this->itemId($item)));
			}
			return TRUE;
		}
		
		function moveItemDescription($from, $to) {
			$fromId = $this->itemId($from);
			
			if (!$from->isFile()) {
				$this->db->update(sprintf("UPDATE ".$this->db->table("item_description")." SET item_id=CONCAT('%s', SUBSTR(item_id, %d)) WHERE item_id like '%s%%'", $this->itemId($to), strlen($fromId)+1, $fromId));
			} else {
				$this->db->update(sprintf("UPDATE ".$this->db->table("item_description")." SET item_id='%s' WHERE item_id='%s'", $this->itemId($to), $fromId));
			}
					
			return TRUE;
		}
					
		function getItemPermission($item, $userId) {
			$id = $this->itemId($item);
			$userQuery = sprintf("(user_id = '%s' or user_id = '0')", $this->db->string($userId));
			$query = NULL;
	
			if ($item->isFile()) {
				$parent = $item->parent();
				
				if ($parent != NULL) {
					$parentId = $this->itemId($parent);
					$query = sprintf("SELECT permission FROM ((SELECT permission, user_id, 1 AS 'index' FROM `".$this->db->table("item_permission")."` WHERE item_id = '%s' AND %s) UNION ALL (SELECT permission, user_id, 2 AS 'index' FROM `".$this->db->table("item_permission")."` WHERE item_id = '%s' AND %s)) AS u ORDER BY u.user_id DESC, u.index ASC", $id, $userQuery, $parentId, $userQuery);
				}
			}
			if ($query === NULL) $query = sprintf("SELECT permission FROM `".$this->db->table("item_permission")."` WHERE item_id = '%s' AND %s ORDER BY user_id DESC", $id, $userQuery);
			
			$result = $this->db->query($query);			
			if ($result->count() < 1) return NULL;
			return $result->value(0);
		}
	
		function getItemPermissions($item) {
			$id = $this->itemId($item);
			$rows = $this->db->query(sprintf("SELECT user_id, permission FROM `".$this->db->table("item_permission")."` WHERE item_id = '%s'", $id))->rows();
			
			$list = array();
			foreach ($rows as $row) {
				$list[] = array("item_id" => $item->id(), "user_id" => $row["user_id"], "permission" => $row["permission"]);
			}
			return $list;
		}
			
		function updateItemPermissions($updates) {
			$new = $updates['new'];
			$modified = $updates['modified'];
			$removed = $updates['removed'];
			
			$this->db->startTransaction();
			if (count($new) > 0) $this->addItemPermissionValues($new);
			if (count($modified) > 0) $this->updateItemPermissionValues($modified);
			if (count($removed) > 0) $this->removeItemPermissionValues($removed);
			$this->db->commit();
							
			return TRUE;
		}

		private function addItemPermissionValues($list) {
			$query = "INSERT INTO `".$this->db->table("item_permission")."` (item_id, user_id, permission) VALUES ";
			$first = TRUE;
			
			foreach($list as $item) {
				$permission = $this->db->string(strtolower($item["permission"]));
				$id = $this->db->string(base64_decode($item["item_id"]));
				$user = '0';
				if ($item["user_id"] != NULL) $user = $this->db->string($item["user_id"]);
				
				if (!$first) $query .= ',';
				$query .= sprintf(" ('%s', '%s', '%s')", $id, $user, $permission);
				$first = FALSE;
			}
			
			$this->db->update($query);							
			return TRUE;
		}
	
		private function updateItemPermissionValues($list) {
			foreach($list as $item) {
				$permission = $this->db->string(strtolower($item["permission"]));
				$id = $this->db->string(base64_decode($item["item_id"]));
				$user = '0';
				if ($item["user_id"] != NULL) $user = $this->db->string($item["user_id"]);
			
				$this->db->update(sprintf("UPDATE `".$this->db->table("item_permission")."` SET permission='%s' WHERE item_id='%s' and user_id='%s'", $permission, $id, $user));
			}
							
			return TRUE;
		}
	
		private function removeItemPermissionValues($list) {
			foreach($list as $item) {
				$id = $this->db->string(base64_decode($item["item_id"]));
				$user = "user_id = '0'";
				if ($item["user_id"] != NULL) $user = sprintf("user_id = '%s'", $this->db->string($item["user_id"]));
				$this->db->update(sprintf("DELETE FROM `".$this->db->table("item_permission")."` WHERE item_id='%s' AND %s", $id, $user));
			}
							
			return TRUE;
		}

		function removeItemPermissions($item) {
			if (!$item->isFile()) {
				$this->db->update(sprintf("DELETE FROM `".$this->db->table("item_permission")."` WHERE item_id like '%s%%'", $this->itemId($item)));
			} else {
				$this->db->update(sprintf("DELETE FROM `".$this->db->table("item_permission")."` WHERE item_id='%s'", $this->itemId($item)));
			}
			return TRUE;
		}
		
		function moveItemPermissions($from, $to) {
			$fromId = $this->itemId($from);
			$toId = $this->itemId($to);
			
			if (!$from->isFile()) {
				$this->db->update(sprintf("UPDATE `".$this->db->table("item_permission")."` SET item_id=CONCAT('%s', SUBSTR(item_id, %d)) WHERE item_id like '%s%%'", $toId, strlen($fromId)+1, $fromId));
			} else {
				$this->db->update(sprintf("UPDATE `".$this->db->table("item_permission")."` SET item_id='%s' WHERE item_id='%s'", $toId, $fromId));
			}
					
			return TRUE;
		}
		
		private function itemId($item) {
			return $this->db->string(base64_decode($item->id()));
		}
	}
?>