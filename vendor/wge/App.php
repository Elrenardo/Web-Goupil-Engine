<?php
/**
 * @author    Teysseire Guillaume
 * @version   1.0
 * @date      28/04/2016
 * @update    07/07/2016
 * @brief     WGE / App: Interface d'utilisation de l'api
 */

namespace WGE;
class App
{
	/**
	* @brief identifiant de la global utilisé pour la création du kernel 
	*/
	const IDSTART = 'wge.app.start';


	/**
	* @brief identifiant 
	*/
	const IDREST = 'rest';


	/**
	* @brief les services de App
	*/
	private static $service = NULL;


	/**
	* @brief  fonction static de création du kernel
	*/
	private static function start()
	{
		//la GLOBAL permet d'identifier la premiére instance pour créer le kernel
		if( !isset($GLOBALS[ self::IDSTART ]))
		{
			$GLOBALS[ self::IDSTART ] = true;

			//Création du gestionnaire de service
			self::$service = new Container();

			//Création service du kernel
			self::$service->add( 'kernel', Kernel::getInstance() );

			//Création du router et configuration
			self::$service->add('router', new \AltoRouter() );

			//Création du service de render
			self::$service->add('render', TemplateRender::getInstance() );

			//Création service config
			self::$service->add( 'config', Register::getInstance() );

			//Création service plugins
			self::$service->add( 'plugins', array() );

			//Création service BDD
			self::$service->add( 'bdd', array() );

			//Création service REST
			self::$service->add( 'REST', array() );

			//Création du service Translate
			self::$service->add( 'translate', new Translate() );

			//Création service schema
			self::$service->add( 'schema', array() );

			//Détecte racine App::setBasePath()
			self::autoDetectSetBasePath();

			//construction du serveur REST
			self::RESTbuild();

			//Ajout de fonctionalité au templating
			self::TemplateBuild();

			//translate build
			self::TranslateBuild();
		}
	}

	/**
	* @brief  réglage automatique du Base Path pour le router
	*/
	private static function autoDetectSetBasePath()
	{
		if( $_SERVER['PHP_SELF'] == '/index.php')
			return;

		$buffer = explode('/',$_SERVER['PHP_SELF']);
		array_pop( $buffer );//enlever le "index.php"
		$buffer = implode('/',$buffer );
		$buffer = substr($buffer,1);//enlever le premier "/"
		$buffer = $buffer.'/';
		//set base path
		App::setBasePath( $buffer );
	}

	/**
	* @brief renvoi un service
	*/
	public static function &getService( $name )
	{
		self::start();
		return self::$service->get( $name );
	}

	/**
	* @brief ajoute un nouveau service
	* @param $name:string nom du service
	* @param $obj:class
	*/
	public static function addService( $name, $obj )
	{
		self::start();
		self::$service->add( $name, $obj );
	}

	/**
	* @brief vérifie si un service existe
	* @param $name:string nom du service
	* @param bool true si existe
	*/
	public static function isService( $name )
	{
		self::start();
		return self::$service->is( $name );
	}


	/**
	* @brief fonction static de création d'une nouvelle route
	* @param $route addresse de la route
	*/
	public static function route( $route )
	{
		self::start();

		$r = new Route( $route );
		self::$service->get('kernel')->addRoute( $r );//ajouter route au kernel
		return $r;
	}


	/**
	* @brief fonction static de gestion des cods d'erreur HTTP
	* @param $code erruer http int
	*/
	public static function error( $code )
	{
		self::start();

		$r = new Route( '/'.$code );
		self::$service->get('kernel')->addRoute( $r );//ajouter route au kernel
		return $r;
	}


	/**
	* @brief renvoi le path d'une route a partir de son nom
	* @param $name:string nom de la route
	*/
	public static function getPathRoute( $name )
	{
		self::start();
		return Route::getPathToName( $name );
	}


	/**
	* @brief création d'un plugin
	* @param 
	* @return class Plugin
	*/
	public static function plugin( $name )
	{
		self::start();

		$p = new Plugin( $name );
		$l = &self::$service->get('plugins');
		$l[ $name ] = $p;

		return $p;
	}



	/**
	* @brief création d'un host ( surcharge plugin )
	* @param 
	* @return class Plugin
	*/
	public static function home( $name )
	{
		return self::plugin( $name );
	}



	/**
	* @brief Gestion des hosts
	* @param 
	* @return true si l'host est chargé
	*/
	public static function host( $host )
	{
		self::start();
		$h = new Host( $host );
		return $h;
	}

	/**
	* @brief renvoi le nom de domaine utilisé pour afficher le site
	* @return nom de domaine
	*/
	public static function getCurrentHost()
	{
		self::start();
		return Host::getCurrentHost();
	}

	/**
	* @brief  set base path pour le router
	*/
	public static function setBasePath( $bp )
	{
		self::start();
		//configuration du router
		self::$service->get('router')->setBasePath( $bp );
		//configuration des routes
		\WGE\Route::setBasePath( $bp );
	}

	/**
	* @brief  set base path pour le router
	*/
	public static function auth( $auth )
	{
		self::start();
		self::$service->get('kernel')->addAuth( $auth );
	}

	/**
	* @brief vérifier si on a l'authorisation
	*/
	public static function isAuth( $auth )
	{
		self::start();
		return self::$service->get('kernel')->isAuth( $auth );
	}


	/**
	* @brief ajoute une fonction dans le générateur de template
	*/
	public static function addFuncTpl( $name, $fonction=[] )
	{
		self::start();
		App::getService('render')->addFuncTpl( $name, $fonction );
	}

	/**
	* @brief ajoute un élément global dans le générateur de template
	*/
	public static function addGlobalTpl( $name, $obj )
	{
		self::start();
		App::getService('render')->addGlobalTpl( $name, $obj );
	}

	/**
	* @brief Ajouter un template
	*/
	public static function template( $name )
	{
		self::start();
		$buffer = new \WGE\Template( \WGE\Kernel::$render );
		$buffer->name( $name );

		App::getService('render')->addTemplate( $buffer );
		return $buffer;
	}

	/**
	* @brief renvoi un lien relatif complet des fichiers
	* @param $path chemain du fichier
	* @return chemain
	*/
	public static function path( $path='' )
	{
		self::start();
		return Kernel::path( $path );
	}


	/**
	* @brief renvoi un lien relatif complet des fichiers vers le home
	* @param $path chemain du fichier
	* @return chemain
	*/
	public static function pathHome( $path='' )
	{
		self::start();
		return Kernel::pathHome( $path );
	}

	/**
	* @brief renvoi l'url
	* @param $url a ajouter
	* @return string
	*/
	public static function url( $url = '' )
	{
		return 'http://'.$_SERVER['HTTP_HOST'].'/'.Route::getBasePath().$url;
	}


	/**
	* @brief création d'un serveur REST
	* @param $path nom du serveur
	* @return class REST
	*/
	public static function RESTserver( $name )
	{
		self::start();

		$rest = new RESTserver();
		self::$service->get('REST')[ $name ] = $rest;
		return $rest;
	}

	/**
	* @brief client REST
	* @param $route url de la route a contacter
	* @return string, résultat du serveur
	*/
	public static function RESTclient( $route, $post=NULL )
	{
		self::start();
		$c = new RESTclient( $route );
		if( $post )
			$c->post( $post );
		return $c->get();
	}

	/**
	* @brief client REST
	* @param $route url de la route a contacter
	* @return string, résultat du serveur
	*/
	public static function RESTArrayClient( $route, $post=NULL )
	{
		self::start();
		$c = new RESTclient( $route );
		if( $post )
			$c->post( $post );
		return $c->getArray();
	}



	/**
	* @brief construit la route pour le REST
	*/
	private static function RESTbuild()
	{
		//Création de la route REST
		self::Route('/'.self::IDREST.'/[a:class]/[a:method]/?[**:data]?')->controller(function($route,$params)
		{
			$rest = \WGE\App::$service->get('REST');

			$ctrl   = $params['class'];
			$method = $params['method'];
			$data   = array();

			//Vérifier que la class REST existe
			if( !isset( $rest[ $ctrl ]))
				return '';

			//charger les datas si il y en a
			if( isset( $params['data'] ))
				$data = $params['data'];

			//Exécution de la method de la class si elle existe
			$buffer = $rest[ $ctrl ];
			return $buffer->exec( $method, $route, $data );
		})->name('REST');
	}



	/**
	* @brief ajoute des fonctionalité au Template
	*/
	private static function TemplateBuild()
	{
		//Création des templates
		$render = App::getService('render');

		//Ajout fonction twig
		$render->addFuncTpl('path', function($path){
			return \WGE\App::path( $path );
		});
		$render->addFuncTpl('pathUrl', function($url){
			return \WGE\App::url( \WGE\App::path( $url ) );
		});
		$render->addFuncTpl('template', function($name){
			return \WGE\App::getService('render')->getTemplate( $name );
		});
		//vérifie si on posséde une authorisation
		$render->addFuncTpl('isAuth', function( $auth ){
			return \WGE\App::isAuth( $auth );
		});
		//renvoi le path celons la nom de la route
		$render->addFuncTpl('route', function( $name, $param='' ){
			if( $param != '' )
				$param = '/'.$param;
			
			return 'http://'.\WGE\Host::getCurrentHost().\WGE\Route::getPathToName( $name ).$param;
		});
		//Rest Client
		$render->addFuncTpl('RESTclient', function( $url, $tab=array() ){
			return \WGE\App::RESTclient( $url, $tab );
		});
		//Traduction
		$render->addFuncTpl('translate', function( $key ){
			return \WGE\App::getTranslate( $key );
		});
	}


	/**
	* @brief ajoute des fonctionalité pour la traduction
	*/
	private static function TranslateBuild()
	{
		App::route('/translate/[a:lang]')->controller(function($route, $params)
		{
			\WGE\App::setTranslateLang( $params['lang'] );
			$translate = \WGE\App::getService('translate');
			return $translate->getAll();
		})
		->name('translate');
	}


	/**
	* @brief creer une connexion BDD
	* @param $name nom de la connexion BDD
	* @return class \WGE\bdd
	*/
	public static function bdd( $name )
	{
		self::start();

		$co = new Bdd( $name );

		$l = &self::$service->get('bdd');
		$l[ $name ] = $co;

		return $co;
	}

	/**
	* @brief renvoi une instance pour request
	* @param $nom string: nom de la connexion BDD
	* @return class QB
	*/
	public static function getBdd( $nom )
	{
		self::start();
		return self::$service->get('plugins')[ $nom ];
	}

	

	/**
	* @brief renvoi une instance pour request
	* @param $nom string: nom de la connexion BDD
	* @param $table string: nom de la table
	* @return class QB
	*/
	public static function query( $nom, $table )
	{
		self::start();
		return self::$service->get('bdd')[ $nom ]->query( $table );
	}


	/**
	* @brief créer une nouvelle entré au registre
	* @param $key:string nom de la clef
	* @return class RegisterKey
	*/
	public static function register( $key )
	{
		self::start();

		$t = new RegisterKey( $key );
		self::$service->get('config')->addRegisterKey( $t );
		return $t;
	}


	/**
	* @brief renvoi une valeur de clef
	* @param $key: nom de la clef
	* @return string valeur
	*/
	public static function &getRegister( $key )
	{
		self::start();
		return self::$service->get('config')->get($key);
	}


	/**
	* @brief enregister le registre dans un fichier json
	*/
	public static function registerSave()
	{
		self::start();
		self::$service->get('config')->save();
	}


	/**
	* @brief Ajout d'une nouvelle méthod de traduction
	* @param $key:string nom de la clef de traduction
	* @return translateKey:class
	*/
	public static function translate( $key )
	{
		self::start();
		$t = new translateKey( $key );
		self::$service->get('translate')->add( $t );
		return $t;
	}



	/**
	* @brief retourné une valeur traduite
	* @param $key:string nom de la clef de traduction
	* @return string
	*/
	public static function getTranslate( $key )
	{
		self::start();
		return self::$service->get('translate')->get( $key );
	}



	/**
	* @brief défini la langue a retourné pour la traduction
	* @param $lang:string lang
	*/
	public static function setTranslateLang( $lang )
	{
		self::start();
		Translate::setLang( $lang );
	}
	public static function getTranslateLang()
	{
		self::start();
		return Translate::getLang();
	}


	/**
	* @brief création d'un nouveau schema
	* @param $name:string nom du schema
	* @return Schema
	*/
	public static function schema( $name )
	{
		self::start();
		$t = new Schema();
		self::$service->get('schema')[ $name ] = $t;
		return $t;

	}

	/**
	* @brief return un schema
	* @param $name:string nom du schema
	* @return Schema
	*/
	public static function getSchema( $name )
	{
		self::start();
		return self::$service->get('schema')[ $name ];
	}



	/**
	* @brief génére une chaine aléatoire
	* @return longeur de la chaine
	*/
	public static function randomString($length = 255)
	{
		self::start();
		return Kernel::randomString( $length );
	}


	public static function getRealPath( $path='' )
	{
		self::start();
		return self::$service->get('kernel')->getRealPath( $path );
	}



	public static function debug( $buffer )
	{
		echo '<pre>';
		var_dump( $buffer );
		echo '</pre>';
	}


	public static function PHPdebugOn()
	{
		error_reporting(E_ALL);
		ini_set('display_errors', 1);
	}
	public static function PHPdebugOff()
	{
		error_reporting(0);
		ini_set('display_errors', 0);
	}
};