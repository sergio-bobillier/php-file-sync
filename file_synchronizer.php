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

	/** Class constructor. Copy settings from settings array into class
	 *  attributes if the corresponding setter function exists.
	 *
	 */

	public function __construct($settings = null)
	{
		if($setting != null && is_array($settings))
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

	public function start_sync($path_a = null, $path_b = null)
	{
		/*******************************************************************************
		 * Path check
		 *
		 * Check that the two given paths are valid, accesible and that they are both
		 * directories.
		 *
		 */

		if(!is_dir($path_a))
		{
			echo "FATAL: The path '" . $path_a . " is not accesible or is not a directory\n.";
			exit(1);
		}

		if(!is_dir($path_b))
		{
			echo "FATAL: The path '" . $path_b . " is not accesible or is not a directory\n";
			exit(2);
		}


		/*******************************************************************************
		 * Last synchronization date
		 *
		 * The program tries to load the date of the last synchronization from a file
		 * called .last-sync in the current path. The field should contain the unix
		 * timestamp of the last synchronization.
		 *
		 * If the file cannot be opened or the contents are not valid the script asumes
		 * that the two paths have never been synchronized.
		 *
		 */

		echo "Trying to retrieve the time of the last synchronization...\n";

		$last_sync_time = 0;

		$last_sync_file_name = ".last-sync";
		$last_sync_file = @fopen($last_sync_file_name, "r");

		if($last_sync_file)
		{
			$last_sync_time = fread($last_sync_file, 20);
			
			$reg_ex = "/^[0-9]+$/";
			if(!preg_match($reg_ex, $last_sync_time))
			{
				echo "Last synchronization time does not have the right format.\n";
				echo "Asuming the paths have never been synchronized before.\n";
				$last_sync_time = 0;
			}

			fclose($last_sync_file);
		}
		else
		{
			echo "Could not open file asuming the paths have never been synchronized\n";
		}

		/*******************************************************************************
		 * Start the synchronization
		 *
		 */

		// We save the time when the synchronization began so that we do not copy files
		// unnecesarily.

		$sync_start_time = time();

		if(DEBUG_MODE)
			echo "Syncrhonizng '" . $path_a . "' -> '" . $path_b . "'\n";

		$result = sync_paths($path_a, $path_b, $last_sync_time, $sync_start_time);

		if($result != 0)
		{
			echo "ERROR: The synchronization process have failed!. The exit code is: " . $result . "\n";
			exit($result);
		}

		if(DEBUG_MODE)
			echo "Syncrhonizng '" . $path_b . "' -> '" . $path_a . "'\n";

		$result = sync_paths($path_b, $path_a, $last_sync_time, $sync_start_time);

		if($result != 0)
		{
			echo "ERROR: The synchronization process have failed!. The exit code is: " . $result . "\n";
			exit($result);
		}

		// We save the current time to the file. This is the time the last
		// syncrhonization was made.

		echo "Saving the last synchronization time to disk.\n";

		$last_sync_time = time();

		if(!SIMULATE)
		{
			$last_sync_file = @fopen($last_sync_file_name, "w");
			
			if($last_sync_file)
			{
				fwrite($last_sync_file, time());
				fclose($last_sync_file);
			}
			else
			{
				echo "Could not save the last synchronization time, unable to open the file for writing.\n";
			}
		}

		// End of the script

		echo "Syncrhonization succesful!\n";
		exit(0);
	}

	/** Synchronizes two paths. After the syncrhonization is guaranteed that the
	 *  $path_b has at least the same files that $path_a.
	 *
	 *  @param string $path_a One of the paths.
	 *
	 *  @param string $path_b The other path.
	 *
	 *  @return int 0 if the synchronization process was succesful, or an error code
	 *  	if an error ocurrs.
	 *
	 */

	private function sync_paths($path_a, $path_b, $last_sync_time, $sync_start_time)
	{
		$files_a = scandir($path_a);

		foreach($files_a as $file)
		{
			// We check if the file name starts with .

			if(substr($file, 0, 1) == ".")
			{
				// If we are to skip hidden files then we junp to the next
				// iteration.

				if(SKIP_HIDDEN)
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
				// The file exists in the other path and therfore it's type should
				// match the type of the file in this path, in other words, if the
				// file in this path is a directory the file in the other path
				// should be a dirctory too.

				if($is_directory != is_dir($new_path_b))
				{
					// The type of the two files doesn't match. We abort the
					// synchornization and return an error code.

					echo "ERROR: There is a resource conflict\n";
					echo "Resource type mismatch: '" . $new_path_a . "' doesn't match '" . $new_path_b . "'\n";
					return 3;
				}
				
				if($is_directory)
				{
					if(DEBUG_MODE)
						echo "Syncrhonizng '" . $new_path_a . "' -> '" . $new_path_b . "'\n";

					// We recursively synchornize the directory with it's
					// counterpart in the other path. If the synchronization fails
					// we abort the process and return the error code.

					$result = sync_paths($new_path_a, $new_path_b, $last_sync_time, $sync_start_time);
					if($result != 0)
						return $result;
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
					$copy_file = $copy_file && ($last_modify_time_a > $last_sync_time);
					$copy_file = $copy_file && ($last_modify_time_a > $last_modify_time_b);
					$copy_file = $copy_file && ($last_modify_time_a < $sync_start_time);

					// 4. If USE_CHECKSUM is true, the checksums of the two versions
					//    of the file must differ.

					if($copy_file == true && USE_CHECKSUM == true)
					{
						// Calculate the checksums of both files and see if they are
						// different. If they do differ then the file is copied.

						$checksums_differ = false;

						if(USE_CHECKSUM == true)
						{
							$checksum_a = sha1_file($new_path_a);
							$checksum_b = sha1_file($new_path_b);
							$checksums_differ = ($checksum_a != $checksum_b);
						}

						$copy_file = $copy_file && $checksums_differ;
					}

					if($copy_file == true)
					{
						if(DEBUG_MODE)
							echo "Copying '" . $new_path_a . "' -> '" . $new_path_b . "'\n";

						$result = true;

						if(!SIMULATE)
							$result = copy($new_path_a, $new_path_b);
						
						if(!$result)
						{
							// We were unable to copy the file or directory to the
							// other path. We abort the synchronization process and
							// return an error code.

							echo "ERROR: Unable to copy the file / directory '" . $new_path_a . "' to '" . $new_path_b . "'\n";
							return 5;
						}
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

				// We check if file was last modified before the last synchronization

				if($last_modify_time <= $last_sync_time)
				{
					// Since the file was last modified before the last
					// synchronization we can asume that the file was deleted on the
					// other path after the synchronization so it should be removed
					// from this path as well.

					$result = false;
					if($is_directory)
					{
						$result = remove_directory($new_path_a);
					}
					else
					{
						if(DEBUG_MODE)
							echo "Removing '" . $new_path_a . "'\n";

						$result = true;
						if(!SIMULATE)
							$result = @unlink($new_path_a);
					}

					if(!$result)
					{
						// We were unable to remove the file. We abort the
						// synchronization process and return an error code.

						echo "ERROR: Cannot remove file / directory '" . $new_path_a . "'\n";
						return 4;
					}
				}
				else
				{
					// Since the file was last modified after the last
					// synchronization we asume that the file is new on this path
					// and thus we should copy it to the other path.

					if(DEBUG_MODE)
						echo "Copying '" . $new_path_a . "' -> '" . $new_path_b . "'\n";

					if($is_directory)
					{
						$result = copy_directory($new_path_a, $new_path_b);
					}
					else
					{
						$result = true;
						if(!SIMULATE)
							$result = copy($new_path_a, $new_path_b);
					}

					if(!$result)
					{
						// We were unable to copy the file or directory to the other
						// path. We abort the synchronization process and return
						// an error code.

						echo "ERROR: Unable to copy the file / directory '" . $new_path_a . "' to '" . $new_path_b . "'\n";
						return 5;
					}
				}
			}
		}
	}

	/** Removes an entire directory (including it's sub-folders).
	 *
	 *  @param string $path The path of the directory to be removed.
	 *
	 *  @return boolean True if the directory was succesfuly removed, false
	 *  	otherwise.
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

			// If the current file is a Directory we recusrsively delete it, if it
			// is a regular file we just have to remove it.
			
			if(is_dir($path_to_delete))
			{
				$result = remove_directory($path_to_delete);
				if(!$result)
					return false;
			}
			else
			{
				if(DEBUG_MODE)
					echo "Removing '" . $path_to_delete . "'\n";

				$result = true;
				if(!SIMULATE)
					$result = @unlink($path_to_delete);

				if(!$result)
					return false;
			}
		}

		// Finally we remove the directory itself.

		if(DEBUG_MODE)
			echo "Removing '" . $path . "'\n";

		$result = true;
		if(!SIMULATE)
			$result = rmdir($path);
		
		return $result;
	}

	/** Recursively copy a directory, all its sub-directories and files.
	 *
	 *  @param string $path The path of the directory to copy.
	 *
	 *  @param string $destination The path of the destination folder.
	 *
	 *  @return boolean True if the directory were copied succesfuly, false
	 *  	otherwise.
	 *
	 */

	private function copy_directory($path, $destination)
	{
		// First we try to create the destination directory.

		if(DEBUG_MODE)
			echo "Creating '" . $destination . "'\n";

		$result = true;
		if(!SIMULATE)
			$result = mkdir($destination);

		if(!$result)
			return false;

		// Now we scan the source directory. We try to copy each file and sub
		// folder to the destination directory.

		$files = scandir($path);

		foreach($files as $file)
		{
			// We skip the current and parent directories

			if($file == "." || $file == "..")
				continue;

			// If we have to, we skip hidden files and directories.

			if(SKIP_HIDDEN == true && substr($file, 0, 1) == ".")
				continue;

			$path_to_copy = $path . "/" . $file;
			$destination_path = $destination . "/" . $file;

			// If the current file is a directory we recursively copy it to the
			// destination path. If it is a regulr file we just copy it.

			if(DEBUG_MODE)
				echo "Copying '" . $path_to_copy . "' -> '" . $destination_path . "'\n";

			if(is_dir($path_to_copy))
			{
				$result = copy_directory($path_to_copy, $destination_path);

				if(!$result)
					return false;
			}
			else
			{
				$result = true;

				if(!SIMULATE)
					$result = copy($path_to_copy, $destination_path);
				
				if(!$result)
					return false;
			}
		}

		return true;
	}
}
?>