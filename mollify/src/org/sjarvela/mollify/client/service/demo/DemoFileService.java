/**
 * Copyright (c) 2008- Samuli Järvelä
 *
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html. If redistributing this code,
 * this entire header must remain intact.
 */

package org.sjarvela.mollify.client.service.demo;

import org.sjarvela.mollify.client.filesystem.Directory;
import org.sjarvela.mollify.client.filesystem.File;
import org.sjarvela.mollify.client.filesystem.FileSystemItem;
import org.sjarvela.mollify.client.request.ResultListener;
import org.sjarvela.mollify.client.service.FileSystemService;

public class DemoFileService implements FileSystemService {
	private final DemoData data;

	public DemoFileService(DemoData data) {
		this.data = data;
	}

	public void createDirectory(Directory parentFolder, String folderName,
			ResultListener resultListener) {
		resultListener.onSuccess(true);
	}

	public void delete(FileSystemItem item, ResultListener listener) {
		listener.onSuccess(true);
	}

	public void getDirectories(Directory parent, ResultListener listener) {
		listener.onSuccess(data.getDirectories(parent.getId()));
	}

	public void getDirectoriesAndFiles(String folder, ResultListener listener) {
		listener.onSuccess(data.getDirectories(folder), data.getFiles(folder));
	}

	public String getDownloadUrl(File file) {
		return DemoEnvironment.MOLLIFY_PACKAGE_URL;
	}

	public void getRootDirectories(ResultListener listener) {
		listener.onSuccess(data.getRootDirectories());
	}

	public void rename(FileSystemItem item, String newName,
			ResultListener listener) {
		listener.onSuccess(true);
	}

	public void getFileDetails(File file, ResultListener listener) {
		listener.onSuccess(data.getFileDetails(file));
	}

	public void getDirectoryDetails(Directory directory,
			ResultListener resultListener) {
		resultListener.onSuccess(data.getDirectoryDetails(directory));
	}

}
