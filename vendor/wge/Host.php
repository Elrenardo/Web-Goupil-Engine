<?php
/**
 * @author    Teysseire Guillaume
 * @version   1.0
 * @date      29/04/2016
 * @brief     WGE / Host gestion ds hosts
 */

namespace WGE;
class Host extends Multiton
{
	private static $home = '';
	private $host = '';


	/**
	* @brief constructeur via l'host
	* @param $host DNS
	*/
	public function __construct( $host )
	{
		$this->host = $host;
	}



	/**
	* @brief path du fichier php à charger
	* @param $path chemain
	* @return this
	*/
	public function plugin( $name )
	{
		//vérifier si c'est le bon host
		if( $this->host == $_SERVER['HTTP_HOST'])
		{
			$plugins = App::getService('plugins');
			if( isset($plugins[ $name ]))
				$plugins[ $name ]->load();
			else
				die('Host: plugin lost ! :'.$name );
		}
		return $this;
	}



	/**
	* @brief définie le plugin HOME
	* @param $name:string nom du pligon qui sera l'home
	*/
	public function home( $name )
	{
		if( $this->host == $_SERVER['HTTP_HOST'])
		{
			self::$home = $name;
			$this->plugin( $name );
		}
	}


	/**
	* @brief renvoi l'host actuel
	* @return host actuel
	*/
	public static function getCurrentHost()
	{
		return $_SERVER['HTTP_HOST'];
	}

	/**
	* @brief envoi le path du home
	* @return string
	*/
	public static function getHome()
	{
		return self::$home;
	}
};