<?php
/**
 * @author    Teysseire Guillaume
 * @version   1.0
 * @date      28/04/2016
 * @brief     WGE / Kernel gestion du noyeau et éxécution
 */

namespace WGE;
class Kernel extends Multiton
{

	private $routes = array();
	private $auth   = array();

	private $realpath = '';

	//route
	private static $basepath   = '';
	private static $pluginpath = '';

	/**
	* @brief  Constructeur du kernel
	*/
	protected function __construct()
	{
		// Indique à PHP que nous allons effectivement manipuler du texte UTF-8
		mb_internal_encoding('UTF-8');
		// indique à PHP que nous allons afficher du texte UTF-8 dans le navigateur web
		mb_http_output('UTF-8');
		//Début session
		if(session_status() == PHP_SESSION_NONE)
			session_start();

		//Récupérer le realpath
		$this->realpath = realpath('./').'/';

		//Sécurité
		$this->security();
	}



	/**
	* @brief Destruction du kernel est éxécutions fonction final
	*/
	public function __destruct()
	{
		$this->exec();
	}



	/**
	* @brief Gestion de la sécurité
	*/
	private function security()
	{
		$_GET    = array_map('htmlspecialchars', $_GET);
		$_COOKIE = array_map('htmlspecialchars', $_COOKIE);
		$_POST   = array_map('htmlspecialchars', $_POST);
	}



	/**
	* @brief Exécute le controller 
	*/
	private function exec()
	{
		//Chargement du registre
		$config  = App::getService('config');
		$router  = App::getService('router');


		$path_home = self::pathHome( $path );
		$config->load( $path_home.'register.json');

		//Ajout des routes
		foreach ($this->routes as $key => $value )
		{
			$buffer = $value->get();
			$router->map( $buffer['method'], $buffer['path'], $value);
		}

		//Exécution du router pour trouver la route
		$match = $router->match();


		//Si pas de route alors erreur 404
		if( !$match )
			$route = $this->getMatch('/404', '404: Not Found' );
		else
			$route = $match['target'];

		//vérifier le parefeu
		if( !$route->isAuth( $this->auth ))
			$route = $this->getMatch('/401', '401: Unauthorized' );

		//Activer le mode réolution path
		self::setPluginPath( $route->getTemplatePath() );

		//Exécution du controller
		$ret = $route->execController( $match['params'] );

		//Si redirection
		$route->execRedirect();

		//Executer le template
		if( !is_null($route->getTemplate()) )
		{
			switch( gettype($ret) )
			{
				case 'string':
					$buffer = ['html'=>$ret];
				break;
				case 'array':
				case 'object':
					$buffer = $ret;
				break;
				default:
				case 'NULL':
					$buffer = array();
				break;
			}
			$ret = App::getService('render')->render( $route->getTemplate(), $buffer );
		}

		//Désactiver resoution path
		self::setPluginPath('');

		//si résultat n'est pas NULL
		if( is_null($ret) )
			return '';

		//Génération de l'affichage
		switch( gettype( $ret ) )
		{
			case 'string':
				echo $ret;
			break;
			default:
				//Désactiver le cache navigateur/proxy
				header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
				header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

				//Affichage résultat en json
				header('Content-Type: application/json');
				echo json_encode($ret);
			break;
		}
	}



	/**
	* @brief extension de la fonction__destruct
	* @param $route nom de la route
	* @param $texterror affiche une erreur dans l'exit()
	*/
	private function getMatch( $route, $txterror )
	{
		if( !is_null($er = $this->issetRoute($route) ))
			return $er;
		else
			exit($txterror);
	}


	/**
	* @brief ajoute une route
	* @param $route nom de la route
	*/
	public function addRoute( Route &$route )
	{
		array_push($this->routes, $route );
	}



	/**
	* @brief retourne une route si elle existe
	* @param $path nom de la route
	*/
	public function issetRoute( $path )
	{
		foreach ($this->routes as $key => $value)
		if( $value->getPath() == $path )
			return $value;
		return NULL;
	}



	/**
	* @brief génére un chemain relatif
	* @param $path chemain
	*/
	public static function path( $path='' )
	{
		return self::$basepath.self::$pluginpath.$path;
	}


	/**
	* @brief génére un chemain relatif depuis le "home"
	* @param $path chemain
	*/
	public static function pathHome( $path='' )
	{
		//Chargement du registre
		$plugins = App::getService('plugins');

		//si pas de home
		if( Host::getHome() == '' )
			die('No "Home" declared for Host: '.App::getCurrentHost() );

		return $plugins[ Host::getHome() ]->getPath() . $path;
	}



	/**
	* @brief défini le path du plugin en cours d'utilisation
	* @param $path:string
	*/
	public static function setPluginPath( $path )
	{
		self::$pluginpath = $path;
	}



	/**
	* @brief renvoi le path du plugin en cours d'utilisation
	* @return string
	*/
	public static function getPluginPath()
	{
		return self::$pluginpath;
	}



	/**
	* @brief ajoute une authotisation pour le firewall
	* @param $auth nom de l'authorisation
	*/
	public function addAuth( $auth )
	{
		array_push( $this->auth, $auth ); 
	}



	/**
	* @brief vérifie si on posséde une authorisation
	* @param $auth nom de l'authorisation
	* @return true ou false
	*/
	public function isAuth( $auth )
	{
		//si pas de firewall
		if( empty( $this->auth ))
			return true;

		//Sinon recherche de l'id Firewall
		if( array_search( $auth, $this->auth ) === false )
			return false;
		return true;	
	}



	/**
	* @brief renvoi le chemain réelle depuis la racine
	* @param $path:string chemain a rajouter
	* @return string
	*/
	public function getRealPath( $path='' )
	{
		return $this->realpath.$path;
	}



	/**
	* @brief génére une chaine aléatoire
	* @param $length:number = 255
	* @return longeur de la chaine
	*/
	public static function randomString($length = 255)
	{
	  $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	  $charactersLength = strlen($characters);
	  $randomString = '';
	  for ($i = 0; $i < $length; $i++)
	      $randomString .= $characters[rand(0, $charactersLength - 1)];
	  return $randomString;
	}
};