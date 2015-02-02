<?php

/**
 * Class to manage Saas Application
 *
 * @package		CodeIgniter SaaS Extension
 * @subpackage	Library
 * @since 		1.0
 * @author Laurent Chedanne
 *
 */
class Saas_App
{
	/**
	 * Array of apps configuration
	 *
	 * @var array
	 */
	protected $apps = array();
	
	protected $currentApp = null;

	/**
	 * __get
	 *
	 * Enables the use of CI super-global without having to define an extra variable.
	 *
	 * @access	public
	 * @param	$var
	 * @return	mixed
	 */
	public function __get($var)
	{
		return get_instance()->$var;
	}
	
	public function __construct() {
		$this->config->load("saas_app");
		$this->apps = $this->config->item("saas_apps");
		
		/*
		 * Load default db config to use as default
		*/
		if (!file_exists($file_path = APPPATH.'config/database.php')) {
			show_error('The configuration file database.php does not exist.');
		}
		include($file_path);
		
		if (!isset($db["default"]))
			show_error('The configuration file database.php must contain a default configuration');
		
		/*
		 * Overide default config with config for each app
		 */
		foreach($this->apps as $app_name => $config) {
			if (isset($config["database"]) && is_array($config["database"])) {
				$this->apps[$app_name]["database"] = array_merge($db["default"], $config["database"]);
			} elseif (isset($config["database"]) && is_string($config["database"]) && isset($db[$config["database"]])) {
				$this->apps[$app_name]["database"] = $db[$config["database"]];
			} elseif (isset($config["database"]) && is_string($config["database"]) && !isset($db[$config["database"]])) {
				show_error(sprintf('The configuration database %s not found', $config["database"]));
			}
		}
		
		$this->setDefaultApp();
	}
	
	protected function setDefaultApp()
	{
		// Get subdomain
		$subdomains = array();
		$domains = explode(".", $_SERVER["HTTP_HOST"]);
		
		/*
		 * On part du début du domaine puis on l'agrandit jusqu'à l'extension pour trouver une application
		* qui correspond
		*/
		$domain = '';
		$apps_found = array();
		foreach($domains as $part_domain)
		{
			$domain = $domain . ((empty($domain))?'':'.') . $part_domain;
			foreach($this->apps as $app_name => $app_config)
			{
				if (isset($app_config["host"])) {
					$hosts = (is_array($app_config["host"]))?$app_config["host"]:array($app_config["host"]);
					foreach($hosts as $host)
					{
						if ($host == $domain)
							$apps_found[] = $app_name;
					}
				}
			}
		}
		
		if (count($apps_found) > 1)
			show_error('Severals app found for this request');
		if (count($apps_found) <= 0 && isset($this->apps["default"]["database"]))
			$apps_found = array("default");
		elseif (count($apps_found) <= 0 )
			show_error('No app found for multitenancy');
		
		$this->currentApp = current($apps_found);
		
		// Override config
		if (isset($this->apps[$this->currentApp]["config"])) {
			if (is_array($this->apps[$this->currentApp]["config"])) {
				foreach($this->apps[$this->currentApp]["config"] as $key => $val) {
					get_instance()->config->set_item($key, $val);
				}
			} else {
				show_error(sprintf("App config %s must be an array", $this->currentApp));
			}
		}
	}
	
	/**
	 * Database Loader
	 *
	 * @param	string	the DB credentials
	 * @param	bool	whether to enable active record (this allows us to override the config setting)
	 * @return	object
	 */
	public function database($params, $active_record = NULL)
	{
		require_once(BASEPATH.'database/DB.php');
		
		if (is_null($this->currentApp))
			show_error('No current app defined for multitenancy');
		
		$app = $this->apps[$this->currentApp];
		
		/*
		 * On construit la connection
		 */
		$params = $this->createUrlConnection($app["database"]);
		return DB($params, $active_record);
	}
	
	/**
	 * Créer une chaine de connexion au serveur de base de données
	 *
	 * @param array $config
	 */
	protected function createUrlConnection(array $config)
	{
		$params_required = array("dbdriver", "username", "password", "hostname", "database");
		$params_var = array_keys($config);
		$params_missing = array_diff($params_required, $params_var);
		if (count($params_missing) > 0)
			show_error(sprintf("Connections parameters missing : %s", join(', ', $params_missing)));
		
		$url = $config["dbdriver"] . '://' . $config["username"];
		if (!empty($config["password"]))
			$url .= ':' . $config["password"];
		$url .= "@" . $config["hostname"];
		if (!empty($config["port"]))
			$url .= ':' . $config["port"];
		$url .= '/' . $config['database'];
		
		// Paramètres qui ne sont pas de la query retirés de config puis transformé en requête
		$params_not_query = array("dbdriver", "username", "password", "hostname", "database", "port");
		$params_query = $config;
		foreach($params_query as $key => $value) {
			if (in_array($key, $params_not_query))
				unset($params_query[$key]);
		}
		$url .= "?" . http_build_query($params_query);
		return $url;
	}
	
	/**
	 * Retourne le nom de l'application courante
	 *
	 */
	public function getAppName()
	{
		if (is_null($this->currentApp))
			show_error('No current app defined for multitenancy');
		
		return $this->apps[$this->currentApp]["name"];
	}
	
}