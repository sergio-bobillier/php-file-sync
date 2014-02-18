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

require_once("synchronization_exception.php");

/** Exception to be thrown when the copy of a file from one path to the other
 *  fails.
 *
 *  @author Sergio Bobillier Ceballos
 *
 */

class File_Copy_Exception extends Synchronization_Exception
{
	/** The exit code for this exception.
	 *
	 *  @var int
	 *
	 */

	protected $exit_code = 3;
}

?>
