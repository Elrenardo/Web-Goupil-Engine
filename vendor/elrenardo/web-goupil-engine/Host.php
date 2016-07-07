<?php
/**
 * @author    Teysseire Guillaume
 * @version   1.0
 * @date      29/04/2016
 * @update    07/07/2016
 * @brief     WGE / Host gestion ds hosts
 */

namespace WGE;
class Host extends Multiton
{
	private static $host_call = NULL;

	private static $home = '';
	private $host = '';
	private $tab_plugins = [];
	private $tab_bdds    = [];


	/**
	* @brief constructeur via l'host
	* @param $host DNS
	*/
	public function __construct( $host )
	{
		$this->host = $host;

		//verifier si c'est le home appeler par l'URL
		if( $this->host == $_SERVER['HTTP_HOST'])
			self::$host_call = $this;
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
			array_push( $this->tab_plugins, $name );
		return $this;
	}



	/**
	* @brief définie le plugin HOME
	* @param $name:string nom du pligon qui sera l'home
	*/
	public function home( $name )
	{
		if( $this->host == $_SERVER['HTTP_HOST'])
			self::$home = $name;
		return $this;
	}


	/**
	* @brief 
	* @param $name:string nom du pligon qui sera l'home
	*/
	public function bdd( $name_bdd )
	{
		if( $this->host == $_SERVER['HTTP_HOST'])
		{
			array_push( $this->tab_bdds, $name_bdd );
		}
		return $this;
	}



	/**
	* @brief charge les plugins dans le host et connecte les BDD pour le Host correpondant a l'URL
	*/
	public static function load()
	{
		//récupérer le host call
		$host = self::$host_call;
		if( is_null( $host))
			die('No Host for '.$_SERVER['HTTP_HOST'] );


		//Connexion BDD
		$bdd = App::getService('bdd');
		foreach ($host->tab_bdds as $key => $value)
		{
			if( isset($bdd[ $value ]))
				$bdd[ $value ]->connection();
			else
				die('Host: BDD lost: '.$value );
		}


		//charger les plugins
		$plugins = App::getService('plugins');
		foreach ($host->tab_plugins as $key => $value)
		{
			if( isset($plugins[ $value ]))
				$plugins[ $value ]->load();
			else
				die('Host: plugin lost: '.$value );
		}

		//charger le home
		if( isset($plugins[ $host::$home ]))
			$plugins[ $host::$home ]->load();
		else
			die('Host: home host: '.$host::$home );
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