<?php

require_once("synchronization_exception.php");

/** Exception to be thrown when something gets wrong with the syncrhoniation
 *  paths
 *
 *  @author Sergio Bobillier Ceballos
 *
 */

class Invalid_Path_Supplied_Exception extends Synchronization_Exception
{
	/** The exist code for this exception.
	 *
	 *  @var int
	 *
	 */

	protected exit_code = 1;
}