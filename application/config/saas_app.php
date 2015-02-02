<?php
/**
 * Multitenancy Extension config
 *
 * @package		CodeIgniter SaaS Extension
 * @subpackage	Config
 * @since 		1.0
 * @author 		Laurent Chedanne
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$saas_apps = array(
	// Application par défaut (required)
	"default" => array(
		"host" => array("www", ''),
		"name" => "Default App",
		"config" => array(
			"upload_dir" => "../datas/default/uploads",
			"upload_path" => "datas/default/uploads"
		),
		"database" => 'default',
	),
	// Autre application sur le domain otherapp
	"otherapp" => array(
		"host" => array("otherapp"),
		"name" => "Autre application",
		"config" => array(
			"upload_dir" => "../datas/otherapp/uploads",
			"upload_path" => "datas/otherapp/uploads"
		),
		"database" => array(
			"hostname" => "hOstAnE",
			"username" => "UsErnAmE",
			"password" => "p@ssword",
			"database" => "otherapp_db"
		)
	)
);

$config['saas_apps'] = $saas_apps;
