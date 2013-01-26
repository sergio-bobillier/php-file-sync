php-file-sync
=============

A PHP File Synchronization script to keep two locations' files synchronized.

This is a simple PHP script that keeps two paths synchronized. This means, if
you create a file in one location it will be copied to the other location. Also
if you modifiy a file or rename it those changes will also be reflected over the
other location.

## How to use

1. Copy settings-sample.php to settings.php
2. Adjust the settings in the file to your needs. (See the settings section).
3. Run the script from the command line:

	<pre>php -f sync-files.php</pre>

4. If you want to save the synchronization log to a file, use the following
	command. (Rembember to set **DEBUG_MODE** to **true** to get output).

	<pre>php -f sync-files.php > result.txt</pre>

And that is about it. Pretty easy huh?

## Settings

This sections explains the different settings you can adjust on the
**settings.php** file. A file containing basic settinga is provided in the
source code you just need to copy it with a new name to start:

<pre>cp settings-sample.php settings.php</pre>

Then adjust the settings to your needs. Here is a list of all the settings that
can be changed:

<dl>
	<dt>DEBUG_MODE</dt>
	<dd>It makes the script print every action it takes to stdout. You can set
		this setting to <b>true</b> if you want to keep a log of the
		synchronization process or just see the script output while it
		runs.</dd>
	<dt>SIMULATE</dt>
	<dd>This option will cause the script not to take any action. It will run
		as if it were doing its job but won't actually copy, delete or create
		any files or folders. You can set this setting to <b>true</b> if you
		want to see what will the script do.

		Is a good idea to run a simulation before running the script for real
		to make sure the script won't damage any files or cause data loss.</dd>
	<dt>SKIP_HIDDEN</dt>
	<dd>If set to true it will cause the script to skip all hidden files and
		folders (that start with .).</dd>
	<dt>USE_CHECKSUM</dt>
	<dd>Set this setting to <b>true</b> to make the script compare files using a
		checksum. This will cause the script not to copy files whose content
		haven't changed even if their modification dates differ.

		This feature is very helpful to avoid copying files that haven't
		actually changed but can slow down the synchronization process.</dd>
</dl>
