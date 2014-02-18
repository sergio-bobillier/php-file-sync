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

/** Base class por all syncrhonization exceptions.
 *
 *  @author Sergio Bobillier Ceballos
 *
 */

abstract class Synchronization_Exception extends Exception
{
	/** The exception's exit code. It will be returned to the system when the
	 *  script is run from CLI.
	 *
	 *  @var int
	 *
	 */

	protected $exit_code;

	/** Returns the exception's exit code.
	 *
	 *  @return int The exception's exit code.
	 *
	 */

	public function get_exit_code() { return $this->exit_code; }
}
