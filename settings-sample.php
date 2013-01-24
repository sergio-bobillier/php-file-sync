<?php

/*******************************************************************************
 * Constants
 *
 * Some constants
 *
 */

// DEBUG_MODE
// It makes the script print every actions it performs.

define("DEBUG_MODE", true);

// SIMULATE
// It will cause the script not not take any real actions only show output.

define("SIMULATE", false);

// SKIP_HIDDEN
// It will cause the script to skip hidden files and directories

define("SKIP_HIDDEN", true);

// USE CHECKSUM
// Use a checksums to compare files. If both files yield the same
// checksum then a file won't be copied even if it was modified after
// it's counterpart.

// WARNING: This can greatly increase the synchronization time.

define("USE_CHECKSUM", false);

/*******************************************************************************
 * Synchronization paths
 *
 * These two paths will be synchronized. Once the synchronization is complete
 * both paths will have the exact same content.
 *
 */

$path_a = "/home/sergio";
$path_b = "/media/backup/home";

?>
