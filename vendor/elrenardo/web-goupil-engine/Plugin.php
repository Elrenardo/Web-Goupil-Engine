<?php
/**
 * @author    Teysseire Guillaume
 * @version   1.0
 * @date      05/05/2016
 * @update    15/07/2016
 * @brief     WGE / Plugin gestion des plugin et plugin home des sites
 */

namespace WGE;
class Plugin
{
	private $name  = '';
	private $files = [];
	private $path  = '';
	private $load  = false;

	private static $plugin_name_load = NULL;


	/**
	* @brief constructeur class
	* @param $name:string
	*/
	public function __construct( $name )
	{
		$this->name = $name;
	}


	/**
	* @brief définie le path du plugin
	* @param $path:string
	* @return this
	*/
	public function path( $path )
	{
		$this->path = $path;
		return $this;
	}


	/**
	* @brief ajoute un fichier a éxécuter
	* @param $path:string
	* @return this
	*/
	public function file( $path )
	{
		array_push( $this->files, $path );
		return $this;
	}


	/**
	* @brief carge un plgin
	*/
	public function load()
	{
		//empecher un double chargement
		if( $this->load )
			return;
		$this->load = true;

		//charger le plugin
		$plugin_path = Kernel::getPluginPath();
		Kernel::setPluginPath( $this->path );

		self::$plugin_name_load = $this->name;

		foreach ($this->files as $key => $value)
			require_once App::path( $value );

		self::$plugin_name_load = NULL;

		Kernel::setPluginPath( $plugin_path );
	}


	/**
	* @brief renvoi le path d'un plugin
	* @return string
	*/
	public function getPath()
	{
		return $this->path;
	}


	/**
	* @brief renvoi le nom d'un plgin
	* @return string
	*/
	public function getName()
	{
		return $this->name;
	}

	/**
	* @brief renvoi la listes des fichiers a charger
	* @return string
	*/
	public function getFiles()
	{
		return $this->files;
	}

	/**
	* @brief renvoi le nom du plugin en cours de chargement
	* @return string
	*/
	public function getPluginNameLoad()
	{
		return self::$plugin_name_load;
	}
};