<?php

/*
 *  PHP File Synchronization Script
 *  Copyright (C) 2014 Sergio Bobillier Ceballos <sergio.bobillier@gmail.com>
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

/*******************************************************************************
 * Script settings.
 *
 * WARNING: This settings file will only be loaded if the script is run from the
 * CLI. If you are including the class in your own application you need to
 * either load this file on your own as shown in sync-files.php or set up the
 * class manually.
 *
 */

// DEBUG_MODE
// It makes the script print every actions it performs.

$settings["debug_mode"] = true;

// SIMULATE
// It will cause the script not not take any real actions only show output.

$settings["simulate"]  = false;

// SKIP_HIDDEN
// It will cause the script to skip hidden files and directories

$settings["skip_hidden"] = true;

// USE CHECKSUM
// Use a checksums to compare files. If both files yield the same
// checksum then a file won't be copied even if it was modified after
// it's counterpart.

// WARNING: This can greatly increase the synchronization time.

$settings["use_checksum"] = false;

/*******************************************************************************
 * Synchronization paths
 *
 * These two paths will be synchronized. Once the synchronization is complete
 * both paths will have the exact same content.
 *
 */

$settings["path_a"] = "/home/sergio";
$settings["path_b"] = "/media/backup/home";

?>
