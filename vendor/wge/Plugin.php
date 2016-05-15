<?php
/**
 * @author    Teysseire Guillaume
 * @version   1.0
 * @date      05/05/2016
 * @brief     WGE / Plugin gestion des plugin et plugin home des sites
 */

namespace WGE;
class Plugin
{
	private $name  = '';
	private $files = [];
	private $path  = '';


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
		$this->path = App::path($path);
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
		$plugin_path = Kernel::getPluginPath();
		Kernel::setPluginPath( $this->path );

		foreach ($this->files as $key => $value)
			require_once App::getRealPath( $this->path.$value );

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
};