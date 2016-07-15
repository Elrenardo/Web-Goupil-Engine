<?php
/**
 * @author    Teysseire Guillaume
 * @version   1.0
 * @date      03/05/2016
 * @update    15/07/2016
 * @brief     WGE / Template création de nouveau template
 */
use \WGE\App;
use \WGE\Host;
use \WGE\Plugin;

namespace WGE;
class Template
{

	private $name = '';
	private $plugin = NULL;
	private $path = '';

	/**
	* @brief chosir le nom du template
	* @param $name string: nom de la template
	* @return $this
	*/
	public function name( $name )
	{
		$this->name   = $name;
		$this->plugin = Plugin::getPluginNameLoad();

		return $this;
	}


	/**
	* @brief gestion du extends des template vers un autre plugin !
	* @param $plugin:string
	* @return this
	*/
	public function extend( $plugin )
	{
		$this->plugin = $plugin;
		return $this;
	}


	/**
	* @brief chosir le nom du template
	* @param $path string chemain du templatz
	*/
	public function path( $path )
	{
		$this->path = $path;
		return $this;
	}


	/**
	* @brief renvoie le nom
	* @return string
	*/
	public function getName()
	{
		return $this->name;
	}


	/**
	* @brief renvoie le plugin
	* @return string
	*/
	public function getPlugin()
	{
		if( is_null($this->plugin))
			return Host::getHome();
		return $this->plugin;
	}


	/**
	* @brief renvoie le path
	* @return string
	*/
	public function getPath()
	{
		return $this->path;
	}
};