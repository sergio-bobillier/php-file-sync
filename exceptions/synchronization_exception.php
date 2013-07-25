<?php

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

	protected exit_code;

	/** Returns the exception's exit code.
	 *
	 *  @return int The exception's exit code.
	 *
	 */

	public function get_exit_code() { return $this->exit_code; }
}
