php-file-sync
=============

A PHP two-way File Synchronization script to keep two locations' files synchronized.

This is a PHP class to synchronize files, you can use it as a command line script
or within your own project. To keep two paths synchronized. This means, if
you create a file in one location it will be copied over to the other location.
Also if you modify a file or rename it those changes will also be reflected over
the other location.

## How to use

### As a command line script

1. Copy settings-sample.php to settings.php

	<pre>cp settings-sample.php settings.php</pre> 

2. Adjust the settings in the file to your needs. (See the settings section).
3. Run the script from the command line:

	<pre>php -f sync-files.php</pre>

4. If you want to save the synchronization log to a file, use the following
	command. (Remember to set **debug_mode** to **true** in the settings
	file to get output).

	<pre>php -f sync-files.php > result.txt</pre>

And that is about it. Pretty easy huh?

**IMPORTANT** The command line script is designed to synchronize only one pair of directories. If you need three way synchronization or would like to synchronize multiple locations then this script is not for you. In such case please read the **Inside your application** section ahead.

### Inside your application

1. Include the class file in your project

	<pre>require_once("file_synchronizer.php");</pre>

2. Create an array with the settings for the class:

	<pre>
	$settings = array();
	$settings["simulate"]  = false;
	$settings["skip_hidden"] = true;
	$settings["use_checksum"] = false;
	$settings["path_a"] = "/home/sergio";
	$settings["path_b"] = "/media/backup/home";
	</pre>
    
3. Instantiate the class and pass on the settings array

	<pre>$file_synchronizer = new File_Synchronizer($settings);</pre>

4. If you want the class to log it's output instantiate a Logger class
	and pass it to the class (see the **About logging** section for
	details):

	<pre>
	require_once("loggers/console_logger.php");

	$logger = new Console_Logger();
	$file_synchronizer->set_logger($logger);
	</pre>

5. Adjust any other setting as desired, for example, the last time the
	synchronization was performed

	<pre>$file_synchronizer->set_last_sync_time($last_sync_time);</pre>

Check the set_ functions in the class or the settings section for all the
settings you can adjust.

Finally fire away the synchronization process:

	try
	{
		// Start the synchronization
		$file_synchronizer->start_sync();
	}
	catch(Synchronization_Exception $sync_ex)
	{
		// Do something here to handle a synchronization exception
	}

## About logging

For the class to log it's output you must provide it with a Logger. A Logger is
a class which inherits from the Logger class in the loggers directory. A basic
console logging class (Console_Logger) is provided, which sends everything to
stdout.

### Logging elsewhere

If you want the class to send it's output somewhere else you must create your
own logging class which inherits from the Logger class and add the desired
code, for example:

```php
require_once("logger.php");

/** A basic DB logging class.
 *
 */

class DB_Logger extends Logger
{
	public function __construct()
	{
		// Initialize the database here....
	}

	public function log_message($message)
	{
		$sql = "INSERT INTO Log_Table(log_date, message)" .
			" VALUES(NOW(), '" . $message . "');";
		mysql_query($sql, $this->conn);
	}
}
```

Off course you might want to use prepared statements ;)

Then you create an instance of the Logger class and pass it to the file
synchronizer class:

```php
$db_logger = new DB_Logger();
$file_synchronizer->set_logger($db_logger);
```

Once a logger have been provided the class will log all it's output to
that class.

You can see a working example of this in the sync-files.php file.
There the script instantiate the Console_Logger class and then
pass it to the file synchronizer:

```php
$logger = new Console_Logger();
$file_synchronizer->set_logger($logger);
```

## Settings

If you are using the class as a command line script you can adjust these settings
in the **settings.php** file. If you are using the class within your own
application you can set them by passing an array to the class constructor or using
the class' **set_** functions.

When using the class as a command line script you can start by copying the sample
settings file and then editing it to your needs:

	cp settings-sample.php settings.php

Here is a a list of all settings that can be adjusted:

### debug_mode (boolean)
It makes the script print every action it takes to stdout. You can set this setting
to **true** if you want to keep a log of the synchronization process or just see the
script output while it runs.

Note that this setting can no longer be set in the File_Synchronizer class or through
the $settings array. To make the class log it's output provide it with a Logger class,
see the **About logging** section for details.

### simulate (boolean)
This option will cause the script not to take any action. It will run as if it were
doing its job but won't actually copy, delete or create any files or folders. You can
set this setting to **true** if you want to see what the script would do.

Is a good idea to run a simulation before running the script for real to make sure
the script won't damage any files or cause data loss.
		
### skip_hidden (boolean)
If set to **true** it will cause the script to skip all hidden files and folders
(that start with .).
		
### use_checksum (boolean)
Set this setting to **true** to make the script compare files using a checksum.
This will cause the script not to copy files whose content haven't changed even if
their modification dates differ.

This feature is very helpful to avoid copying files that haven't actually changed
but can slow down the synchronization process.
		
### path_a (string)
One of the paths that will be synchronized. The order in which you set the paths
shouldn't make any difference, however if you are seeing unexpected results you can
try to switch them.

### path_b (string)
One of the paths that will be synchronized. The order in which you set the paths
shouldn't make any difference, however if you are seeing unexpected results you can
try to switch them.
		
### last_sync_time (int)
This setting tells the class when the synchronization was last performed the class
uses this as a reference time to determine if a file is new in one of the paths or
it was deleted in the other or if the file is worth copying.

The time of the last synchronization is represented as a unix timestamp.

You only need to set this if you are using the class within your own project. If
you are using the class as a command line script the sync-files.php file will
automatically take care of this.

This setting was provided mainly as a way to give the user a way to store the last
synchronization time in some other location, like a database.
