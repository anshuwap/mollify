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

	class DatabaseUtil {
		private $db;
		
		public function __construct($db) {
			$this->db = $db;
		}
		
		public function db() {
			return $this->db;
		}
		
		public function installedVersion() {
			$result = $this->db->query("SELECT value FROM ".$this->db->table("parameter")." WHERE name='version'");
			if ($result->count() === 0) return NULL;
			
			$ver = trim($result->value(0));
			return ($ver === "" ? NULL : $ver);
		}
		
		public function createDatabase() {
			mysqli_report(MYSQLI_REPORT_ALL);
	
			$this->db->query("CREATE DATABASE ".$this->db->database())->free();
			$this->db->selectDb();
		}
		
	}	
?>