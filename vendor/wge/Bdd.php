<?php
/**
 * @author    Teysseire Guillaume
 * @version   1.0
 * @date      02/05/2016
 * @brief     WGE / Bdd gestion de la base de donnée pixie
 */

namespace WGE;
class Bdd
{
	private $config = [];

	/**
	* @brief constructeur
	*/
	public function __construct()
	{
		$this->config['driver']   = 'mysql';
		$this->config['host']     = 'localhost';
		$this->config['username'] = 'root';
		$this->config['password'] = '';
		$this->config['database'] = '';
		$this->config['charset']  = 'utf8';
	}


	/**
	* @brief renvoi une instance QB pour request SQL
	* @param $table string: nom de la table
	* @return instance QB;
	*/
	public static function query( $table )
	{
		return \QB::table( $table );
	}

	/**
	* @brief création table
	* @param $name nom de la table
	* @param array des champs a créer
	* @return création réussi ou pas
	*/
	static public function create( $name, array $tab)
	{
		$req = 'CREATE TABLE IF NOT EXISTS '.$name.' ( id SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT';
		foreach ($tab as $key => $value)
				$req .= ', `'.$value[0].'` '.$value[1];
		$req .= ', PRIMARY KEY (id))';

		//création de la table
		if( class_exists('\QB'))
			return \QB::query( $req );
		die('BDD not initialised !');
	}

	/**
	* @brief vérifie si le tableau d'entrer correspond au demande
	* @param $tab tableau d'entrée comme $_POST
	* @param $array_verif array() de vérification 
	* @param return false si il manque un champ ou un en trop
	*/
    static public function verifTab( $tab, $array_verif )
    {
    	//si il n'est pas vide
    	if( count($tab) < 1 )
    		return false;
    	//verifier le tableau
    	foreach ($tab as $key => $value)
    	{
    		if( !in_array($key, $array_verif))
    			return false;
    	}
    	return true;
    }


	/**
	* @brief driver a utilisé pour la connexion ex:mysql
	* @param $driver:string
	* @return this
	*/
	public function driver( $driver )
	{
		$this->config['driver'] = $driver;
		return $this;
	}


	/**
	* @brief choix de l'host ex:localhost
	* @param $host:string
	* @return this
	*/
	public function host( $host )
	{
		$this->config['host'] = $host;
		return $this;
	}


	/**
	* @brief base de donnée a utilisé
	* @param $database:string
	* @return this
	*/
	public function database( $database )
	{
		$this->config['database'] = $database;
		return $this;
	}

	/**
	* @brief user connexion
	* @param $user:string
	* @return this
	*/
	public function user( $user )
	{
		$this->config['username'] = $user;
		return $this;
	}


	/**
	* @brief password à utilisé avec le user
	* @param $password:string
	* @return this
	*/
	public function password( $password )
	{
		$this->config['password'] = $password;
		return $this;
	}

	/**
	* @brief utilisation de prefix sur les tables
	* @param $prefix:string
	* @return this
	*/
	public function prefix( $prefix )
	{
		$this->config['prefix'] = $prefix;
		return $this;
	}

	/**
	* @brief défini le schéma a utiliser pour du PostreSQL
	* @param $name:string
	* @return this
	*/
	public function schema( $name )
	{
		$this->config['schema'] = $name;
		return $this;
	}


	/**
	* @brief Défini un port a utiliser
	* @param $port:number
	* @return this
	*/
	public function port( $port )
	{
		$this->config['port'] = $port;
		return $this;
	}


	/**
	* @brief Défini un encodage
	* @param $charset:string
	* @return this
	*/
	public function charset( $charset )
	{
		$this->config['charset'] = $charset;
		return $this;
	}



	/**
	* @brief ce connecter à la BDD
	*/
	public function connexion()
	{
		return new \Pixie\Connection( $this->config['driver'], $this->config, 'QB');
	}

};