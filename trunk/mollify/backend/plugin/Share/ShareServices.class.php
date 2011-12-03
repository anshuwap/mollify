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

	class ShareServices extends ServicesBase {		
		protected function isValidPath($method, $path) {
			return TRUE;
		}
		
		public function isAuthenticationRequired() {
			return FALSE;
		}
		
		public function processGet() {
			if (count($this->path) == 1) {
				$this->processShareGet($this->path[0]);
				return;
			}
			if (!$this->env->authentication()->isAuthenticated()) throw new ServiceException("UNAUTHORIZED");
			if (count($this->path) != 2 or strcmp($this->path[0], 'items') != 0) throw $this->invalidRequestException();
			
			$item = $this->item($this->path[1]);
			$this->response()->success($this->handler()->getShares($item));
		}
		
		private function processShareGet($id) {
			$this->handler()->processShareGet($id);
		}
		
		public function processPost() {
			if (!$this->env->authentication()->isAuthenticated()) throw new ServiceException("UNAUTHORIZED");
			if (count($this->path) != 2 or strcmp($this->path[0], 'items') != 0) throw $this->invalidRequestException();
			
			$item = $this->item($this->path[1]);
			$data = $this->request->data;
			//if (!isset($data["type"])) throw $this->invalidRequestException("No data");
			
			$this->handler()->addShare($item);
			$this->response()->success($this->handler()->getShares($item));
		}
		
		private function handler() {
			return $this->env->plugins()->getPlugin("Share")->getHandler();
		}
	}
?>