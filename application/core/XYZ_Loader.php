<?php
if (! defined('BASEPATH'))
	exit('No direct script access allowed');

require_once (BASEPATH . "core/Loader.php");

/**
 * Loader Class for multitenancy
 *
 * Loads views and files
 *
 * @package CodeIgniter SaaS Extension
 * @subpackage Core
 * @author Laurent Chedanne
 */
class XYZ_Loader extends CI_Loader
{

	/**
	 * Database Loader
	 *
	 * @param
	 *        	string	the DB credentials
	 * @param
	 *        	bool	whether to return the DB object
	 * @param
	 *        	bool	whether to enable active record (this allows us to override the config setting)
	 * @return object
	 */
	public function database($params = '', $return = FALSE, $active_record = NULL)
	{
		// Grab the super object
		$CI = & get_instance();
		// Do we even need to load the database class?
		if (class_exists('CI_DB') and $return == FALSE and $active_record == NULL and isset($CI->db) and is_object($CI->db)) {
			return FALSE;
		}
		
		$this->library("saas_app");
		$DB = $CI->saas_app->database($params, $active_record);
		
		if ($return === TRUE) {
			return $DB;
		}
		// Initialize the db variable. Needed to prevent
		// reference errors with some configurations
		$CI->db = '';
		
		// Load the DB class
		$CI->db = & $DB;
	}
}

/* End of file Loader.php */
/* Location: ./core/Loader.php */