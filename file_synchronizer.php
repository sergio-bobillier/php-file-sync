<?php

/*
 *  PHP File Synchronization Script
 *  Copyright (C) 2013 Sergio Bobillier Ceballos <sergio.bobillier@gmail.com>
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */

require_once("exceptions/invalid_path_supplied_exception.php");
require_once("exceptions/resource_missmatch_exception.php");
require_once("exceptions/file_copy_exception.php");
require_once("exceptions/file_delete_exception.php");
require_once("exceptions/directory_remove_exception.php");
require_once("exceptions/directory_create_exception.php");

/** This class synchronize two file system paths. After running the
 *  synchronization it is guaranteed that both paths will have the same files.
 *
 *  @author Sergio Bobillier Ceballos
 *
 */


class File_Synchronizer
{
	/** If set to true it makes the script print every actions it performs.
	 *
	 *  @var boolean
	 *
	 */

	private $debug_mode = true;

	/** If set to true, it will cause the script not not take any real actions
	 *  only show output.
	 *
	 *  @var boolean
	 *
	 */

	private $simulate = false;

	/** If set to true it will cause the script to skip hidden files and
	 *  directories.
	 *
	 *  @var boolean
	 *
	 */

	private $skip_hidden = true;

	/** If set to true the script will compute and use a checksums to compare
	 *  files. If both files yield the same checksum then a file won't be copied
	 *  even if it was modified after it's counterpart.
	 *
	 *  WARNING: This can greatly increase the synchronization time.
	 *
	 *  @var boolean
	 *
	 */

	private $use_checksum = false;

	/** One of the paths to synchronize.
	 *
	 *  @var string
	 *
	 */

	private $path_a = null;

	/** One of the paths to synchronize.
	 *
	 *  @var string
	 *
	 */

	private $path_b = null;

	/** An UNIX timestamp that tells the class when the two paths were last
	 *  synchronized. This timestamp is used to locate files that were changed
	 *  added or updated after the last synchronization and synchronize only
	 *  those files.
	 *
	 *  @var int
	 *
	 */

	private $last_sync_time = 0;

	/** The UNIX timestamp of the date and time when the synchronization started
	 *  this timestamp is used to avoid copying files twice (in one direction
	 *  and then in the other)
	 *
	 */

	private $sync_start_time = 0;

	/** Class constructor. Copy settings from settings array into class
	 *  attributes if the corresponding setter function exists.
	 *
	 */

	public function __construct($settings = null)
	{
		if($settings != null && is_array($settings))
		{
			foreach($settings as $setting => $value)
			{
				$method_name = "set_" . $setting;

				if(method_exists($this, $method_name))
					$this->$method_name($value);
			}
		}
	}

	/** Sets a boolean value that determines wether the script will output all
	 *  the actions it performs to stdout.
	 *
	 *  @param boolean $debug_mode True to make the script output the actions
	 *  	it takes, false to make it perform quietly.
	 *
	 */

	public function set_debug_mode($debug_mode)
	{
		$this->debug_mode = $debug_mode;
	}

	/** Returns a boolean value that tells if the script will be printing the
	 *  actions it takes to stdout or not.
	 *
	 *  @return boolean True if the script will be printing to stdout, false
	 *  	otherwise.
	 *
	 */

	public function get_debug_mode($debug_mode)
	{
		return $this->debug_mode;
	}

	/** Sets a boolean value that determines wether the script will take any
	 *  real action.
	 *
	 *  @param boolean $simulate True to keep the script from taking any real
	 *  	action, false to actually perform the synchronization.
	 *
	 */

	public function set_simulate($simulate)
	{
		$this->simulate = $simulate;
	}

	/** Returns a boolean value that determines if the script will perform the
	 *  actual syncrhonization or just pretend to do so.
	 *
	 *  @return boolean True if the script will synchronize the files, false if
	 *  	a simulation will be run.
	 *
	 */

	public function get_simulate()
	{
		return $this->simulate;
	}

	/** Sets a boolean value that determines if the script will skip hidden
	 *  files and directories during the synchronization.
	 *
	 *  @param boolean $skip_hidden True to make the script skip hidden files
	 *  	and directorios, false to make the script synchronize them too.
	 *
	 */

	public function set_skip_hidden($skip_hidden)
	{
		$this->skip_hidden = $skip_hidden;
	}

	/** Returns a value that tells if the script will be skipping hidden files
	 *  and directories.
	 *
	 *  @return boolean True if the script will skip hidden files, false
	 *  	otherwise.
	 *
	 */

	public function get_skip_hidden()
	{
		return $this->skip_hidden;
	}

	/** Sets a boolean value that determines if the script will compute a
	 *  checksum if a file is found in both paths to check if they are different
	 *  before attemping to overwrite one of them with the most recent version.
	 *
	 *  @param boolean $use_checksum True to make the script use a checksum to
	 *  	check if two files are different, false to make it only use the
	 *  	modify time and date of the files.
	 *
	 */

	public function set_use_checksum($use_checksum)
	{
		$this->use_checksum = $use_checksum;
	}

	/** Returns a boolean value that tells wether the script will be using a
	 *  checksum to verify if two files are different or not.
	 *
	 *  @return boolean True if the script is set to compute and use checksums
	 *  	for file comparision, false otherwise.
	 *
	 */

	public function get_use_checksum()
	{
		return $this->use_checksum;
	}

	/** Sets a string with one of the paths that should be synchronized.
	 *
	 *  @param string $path_a A string with one of the paths.
	 *
	 */

	public function set_path_a($path_a)
	{
		$this->path_a = $path_a;
	}

	/** Returns a string with one of the paths that should be synchronized.
	 *
	 *  @return string A string with one of the paths that should be
	 *  	syncrhonized.
	 *
	 */

	public function get_path_a()
	{
		return $this->path_a;
	}

	/** Sets a string with one of the paths that should be synchronized.
	 *
	 *  @param string $path_b A string with one of the paths.
	 *
	 */

	public function set_path_b($path_b)
	{
		$this->path_b = $path_b;
	}

	/** Returns a string with one of the paths that should be synchronized.
	 *
	 *  @return string A string with one of the paths that should be
	 *  	syncrhonized.
	 *
	 */	

	public function get_path_b()
	{
		return $this->path_b;
	}

	/** Sets the UNIX timestamp of the time and date when the two paths were
	 *  last synchronized. This timestamp is used to determine if files are to
	 *  be copied from one path to the other or deleted.
	 *
	 *  @param int $last_sync_time An integer with the the UNIX timestamp of the
	 *  	last synchronization time.
	 *
	 */

	public function set_last_sync_time($last_sync_time)
	{
		$this->last_sync_time = $last_sync_time;
	}

	/** Returns a unix timestamp with the date and time when the last
	 *  syncrhonization took place. This function just returns the value that
	 *  the client set it doesn't determine it automatically.
	 *
	 */

	public function get_last_sync_time($last_sync_time)
	{
		return $this->last_sync_time;
	}

	/** Start the synchronization process. Checks both paths and then sync them.
	 *
	 *  @param string $path_a One of the paths to sync. If ommited then the
	 *  	function will use the path configured in the $path_a class
	 *  	attribute.
	 *
	 *  @param string $path_b One of the paths to sync. If ommited then the
	 *  	function will use the path configured in the $path_b class
	 *  	attribute.
	 *
	 *  @throws Synchronization_Exception If the synchronization process fails.
	 *  	(The function can throw any of the classes that inherit from
	 *  	Synchronization_Exception depending on where the error ocurred)
	 *
	 */

	public function start_sync($path_a = null, $path_b = null)
	{
		if($path_a == null)
			$path_a = $this->path_a;

		if($path_b == null)
			$path_b = $this->path_b;

		/***********************************************************************
		 * Path check
		 *
		 * Checks to make sure both paths were given and that the both paths are
		 * valid, accesible and are both directories.
		 *
		 */

		if($path_a == null || $path_b == null)
			throw new Invalid_Path_Supplied_Exception("One or both paths are missing. Both paths must be supplied");

		if(!is_dir($path_a))	
			throw new Invalid_Path_Supplied_Exception("The path '" . $path_a . "' is not accessible or is not a directory.");

		if(!is_dir($path_b))
			throw new Invalid_Path_Supplied_Exception("The path '" . $path_b . "' is not accessible or is not a directory.");

		/*******************************************************************************
		 * Start the synchronization
		 *
		 */

		// We save the time when the synchronization began so that we do not
		// copy files unnecesarily.

		$this->sync_start_time = time();

		// We synchronize the paths in both directions. We do not need to put a
		// try/catch block the calling function should catch it.

		if($this->debug_mode)
			echo "Syncrhonizng '" . $path_a . "' -> '" . $path_b . "'\n";

		$this->sync_paths($path_a, $path_b);

		if($this->debug_mode)
			echo "Syncrhonizng '" . $path_b . "' -> '" . $path_a . "'\n";

		$this->sync_paths($path_b, $path_a);
	}

	/** Synchronizes two paths. After the syncrhonization is guaranteed that the
	 *  $path_b has at least the same files that $path_a.
	 *
	 *  @param string $path_a One of the paths.
	 *
	 *  @param string $path_b The other path.
	 *
	 *  @throws Synchronization_Exception If the synchronization process fails.
	 *  	(The function can throw any of the classes that inherit from
	 *  	Synchronization_Exception depending on where the error happens.)
	 *
	 */

	private function sync_paths($path_a, $path_b)
	{
		$files_a = scandir($path_a);

		foreach($files_a as $file)
		{
			// We check if the file name starts with .

			if(substr($file, 0, 1) == ".")
			{
				// If we are to skip hidden files then we junp to the next
				// iteration.

				if($this->skip_hidden)
					continue;

				// If the file is the current directory (.) or the parent directory
				// (..) we jump to the next iteration.

				if($file == "." || $file == "..")
					continue;
			}

			$new_path_a = $path_a . "/" . $file;
			$new_path_b = $path_b . "/" . $file;
			$is_directory = is_dir($new_path_a);
			
			// We check if the same file exists in the other path

			if(file_exists($new_path_b))
			{
				// The file exists in the other path and therfore it's type
				// should match the type of the file in this path, in other
				// words, if the file in this path is a directory the file in
				// the other path should be a dirctory too.

				if($is_directory != is_dir($new_path_b))
				{
					// The type of the two files doesn't match. We abort the
					// synchornization and throw an exception.

					throw new Resource_Missmatch_Exception("Resource type mismatch: '" . $new_path_a . "' doesn't match '" . $new_path_b . "'");
				}
				
				if($is_directory)
				{
					if($this->debug_mode)
						echo "Syncrhonizng '" . $new_path_a . "' -> '" . $new_path_b . "'\n";

					// We recursively synchornize the directory with it's
					// counterpart in the other path. Note that if an error
					// occurr while doing that synchronization an exception will
					// be thrown.

					$this->sync_paths($new_path_a, $new_path_b);
				}
				else
				{
					// We get the modification time of both files.

					$last_modify_time_a = 0;
					$last_modify_time_b = 0;

					$stat = stat($new_path_a);

					if($stat != false)
					{
						$mtime = $stat["mtime"];
						$ctime = $stat["ctime"];
						$last_modify_time_a = max($mtime, $ctime);
					}

					$stat = stat($new_path_b);

					if($stat != false)
					{
						$mtime = $stat["mtime"];
						$ctime = $stat["ctime"];
						$last_modify_time_b = max($mtime, $ctime);
					}

					// For the file to be copied to the other path it must meet
					// three requirements.
					//
					// 1. It was modified after the last syncrhonzation
					// 2. It was modified after the file in the other path
					// 3. It wasn't modified after the syncrhonization started.

					$copy_file = true;
					$copy_file = $copy_file && ($last_modify_time_a > $this->last_sync_time);
					$copy_file = $copy_file && ($last_modify_time_a > $last_modify_time_b);
					$copy_file = $copy_file && ($last_modify_time_a < $this->sync_start_time);

					// 4. If USE_CHECKSUM is true, the checksums of the two
					//    versions of the file must differ.

					if($copy_file == true && $this->use_checksum == true)
					{
						// Calculate the checksums of both files and see if they
						// are different. If they do not differ then the file is
						// not copied.

						$checksum_a = sha1_file($new_path_a);
						$checksum_b = sha1_file($new_path_b);
						$copy_file = ($checksum_a != $checksum_b);
					}

					if($copy_file == true)
					{
						if($this->debug_mode)
							echo "Copying '" . $new_path_a . "' -> '" . $new_path_b . "'\n";

						$result = true;
						if(!$this->simulate)
							$result = @copy($new_path_a, $new_path_b);
						
						// If we were unable to copy the file or directory to
						// the other path. We abort the synchronization process
						// and throw an exception.

						if(!$result)
							throw new File_Copy_Exception("Unable to copy the file '" . $new_path_a . "' to '" . $new_path_b . "'");
					}
				}
			}
			else
			{
				// We get the last modification time of the file.

				$last_modify_time = 0;
				$stat = stat($new_path_a);
				
				if($stat != false)
				{
					$mtime = $stat["mtime"];
					$ctime = $stat["ctime"];
					$last_modify_time = max($mtime, $ctime);
				}

				// We check if file was last modified before the last
				// synchronization

				if($last_modify_time <= $this->last_sync_time)
				{
					// Since the file was last modified before the last
					// synchronization we can asume that the file was deleted on
					// the other path after the synchronization so it should be
					// removed from this path as well.
					
					if($is_directory)
					{
						// The file is a directory, we have to recursively
						// delete it and all its files and sub-folders.

						$this->remove_directory($new_path_a);
					}
					else
					{
						if($this->debug_mode)
							echo "Removing '" . $new_path_a . "'\n";

						$result = true;
						if(!$this->simulate)
							$result = @unlink($new_path_a);

						// If we fail to delete the file we throw an exception.

						if($result == false)
							throw new File_Delete_Exception("Couldn't delete file '" . $new_path_a . "'");
					}
				}
				else
				{
					// Since the file was last modified after the last
					// synchronization we asume that the file is new on this path
					// and thus we should copy it to the other path.

					if($this->debug_mode)
						echo "Copying '" . $new_path_a . "' -> '" . $new_path_b . "'\n";

					if($is_directory)
					{
						$this->copy_directory($new_path_a, $new_path_b);
					}
					else
					{
						$result = true;
						if(!$this->simulate)
							$result = @copy($new_path_a, $new_path_b);

						// If we were unable to copy the file to the other path
						// we throw an exception.

						if($result == false)
							throw new File_Copy_Exception("Unable to copy the file '" . $new_path_a . "' to '" . $new_path_b . "'");
					}
				}
			}
		}
	}

	/** Removes an entire directory (including it's sub-folders).
	 *
	 *  @param string $path The path of the directory to be removed.
	 *
	 *  @throws File_Delete_Exception If the function is unable to delete one of
	 *  	the files in the directory.
	 *
	 *  @throws Directory_Remove_Exception If the function is unable to remove
	 *  	the directory.
	 *
	 */

	private function remove_directory($path)
	{
		// We get a list of all files and folders in the folder we are about to
		// remove.

		$files = scandir($path);

		foreach($files as $file)
		{
			// We skip the current directory and the parent directory.

			if($file == "." || $file == "..")
				continue;

			// We get the full path of the current file.

			$path_to_delete = $path . "/" . $file;

			// If the current file is a Directory we recusrsively delete it, if
			// it is a regular file we just have to remove it.
			
			if(is_dir($path_to_delete))
			{
				$this->remove_directory($path_to_delete);
			}
			else
			{
				if($this->debug_mode)
					echo "Removing '" . $path_to_delete . "'\n";

				$result = true;

				if(!$this->simulate)
					$result = @unlink($path_to_delete);

				// If we fail to remove thw file, we throw an exception.

				if(!$result)
					throw new File_Delete_Exception("Couldn't remove file '" . $path_to_delete . "'");
			}
		}

		// Finally we remove the directory itself.

		if($this->debug_mode)
			echo "Removing '" . $path . "'\n";

		$result = true;

		if(!$this->simulate)
			$result = rmdir($path);
		
		// If we fail to remove the directory we throw an exception

		if(!$result)
			throw new Directory_Remove_Exception("Couldn't remove directory '" . $path . "'");
	}

	/** Recursively copy a directory, all its sub-directories and files.
	 *
	 *  @param string $path The path of the directory to copy.
	 *
	 *  @throws Directory_Create_Exception If the function fails to create the
	 *  	destination directory.
	 *
	 *  @throws File_Copy_Exception If the function fails to copy one of the
	 *  	files to the destination directory.
	 *
	 */

	private function copy_directory($path, $destination)
	{
		// First we try to create the destination directory.

		if($this->debug_mode)
			echo "Creating '" . $destination . "'\n";

		$result = true;
		if(!$this->simulate)
			$result = @mkdir($destination);

		if(!$result)
			throw new Directory_Create_Exception("Unable to create destination directory '" . $destination . "'");

		// Now we scan the source directory. We try to copy each file and sub
		// folder to the destination directory.

		$files = scandir($path);

		foreach($files as $file)
		{
			// We skip the current and parent directories

			if($file == "." || $file == "..")
				continue;

			// If we have to, we skip hidden files and directories.

			if($this->skip_hidden == true && substr($file, 0, 1) == ".")
				continue;

			$path_to_copy = $path . "/" . $file;
			$destination_path = $destination . "/" . $file;

			// If the current file is a directory we recursively copy it to the
			// destination path. If it is a regulr file we just copy it.

			if($this->debug_mode)
				echo "Copying '" . $path_to_copy . "' -> '" . $destination_path . "'\n";

			if(is_dir($path_to_copy))
			{
				$this->copy_directory($path_to_copy, $destination_path);
			}
			else
			{
				$result = true;
				if(!$this->simulate)
					$result = @copy($path_to_copy, $destination_path);
				
				if(!$result)
					throw new File_Copy_Exception("Unable to copy the file '" . $path_to_copy . "' to '" . $$destination_path . "'");
			}
		}
	}
}
?>
