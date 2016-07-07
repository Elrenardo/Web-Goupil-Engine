<?php
/**
 * @author    Teysseire Guillaume
 * @version   1.0
 * @date      28/04/2016
 * @brief     WGE / Route Fabrication et gestion des routes
 */

namespace WGE;
class Route
{
	private $path     = NULL;
	private $ctrl     = array();
	private $fw       = NULL;
	private $tpl      = NULL;
	private $tpl_path = '';
	private $method   = 'GET|POST';
	private $redirect = NULL;
	private $data     = NULL;

	private static $name = array();
	private static $base_path = '';


	/**
	* @brief constructeur de Route
	* @param $ctrl:string
	*/
	public function __construct( $path )
	{
		$this->path = $path;
		$this->tpl_path = \WGE\kernel::getPluginPath();
	}

	/**
	* @brief configurer le controller
	* @param $ctrl:string,array,fonction
	* @return this
	*/
	public function controller( $ctrl )
	{
		$this->ctrl = $ctrl;
		return $this;
	}

	/**
	* @brief configurer l'autorisation
	* @param $fw:string
	* @return this
	*/
	public function auth( $fw )
	{
		$this->fw = $fw;
		return $this;
	}


	/**
	* @brief configurer le template
	* @param $name:string
	* @return this
	*/
	public function template( $name )
	{
		$this->tpl = $name;
		return $this;
	}



	/**
	* @brief configurer le controller
	* @param $method:string "GET|POST"
	* @return this
	*/
	public function method( $method )
	{
		$this->meth = $method;
		return $this;
	}


	/**
	* @brief utiliser une redirection aprés le controller
	* @param $route:string
	* @return this
	*/
	public function redirect( $route )
	{
		$this->redirect = $route;
		return $this;
	}


	/**
	* @brief définir un home repertory
	* @param $name:string
	* @return this
	*/
	public function templateRepertory( $name )
	{
		//Template
		$this->template( $name );

		$tpl = App::getService('render');
		$bd = dirname( $tpl->getTemplate( $name ) );
		//$explode = explode('/',$bd);
		//$bd = end( $explode ).'/';
		$bd = substr(strrchr($bd, '/'), 1).'/';

		//Route d'auto routage
		App::route('/[**:file]')
		->setData( $bd )
		->controller(function( $route, $param )
		{
			$bd = $route->getData();
			$path         = App::path($bd.$param['file']);
			$path_complet = App::getRealPath( $path );
			$url          = App::url( $path );

			if( !file_exists( $path_complet ))
				return '404';

			//création des headers du document a afficher
			$ret =  get_headers( $url );
			foreach ($ret as $key => $value){
				header($value);
			}
			return file_get_contents( $path_complet );
		});

		return $this;
	}


	/**
	* @brief éxécute un controler
	* @param $params:string
	* @return this
	*/
	public function execController( $params )
	{
		//Exécution du controller
		if( is_callable( $this->ctrl ))
		{
			$ctrl = $this->ctrl;
			return $ctrl( $this, $params );
		}
		return $this->ctrl;
	}

	/**
	* @brief éffectue une redirection
	*/
	public function execRedirect()
	{
		if( !is_null( $this->redirect ))
			return header('Location: '.$this->redirect );
	}


	/**
	* @brief vérifie les droits d'une authorisation
	* @param &$fw:array
	* @return true ou false
	*/
	public function isAuth( array &$fw )
	{
		//si pas de firewall
		if( empty( $this->fw ))
			return true;

		//Sinon recherche de l'id Firewall
		if( array_search( $this->fw, $fw ) === false )
			return false;
		return true;	
	}

	/**
	* @brief retourne la config de la route
	* @return array
	*/
	public function get()
	{
		$ret = array(
			'path'     => $this->path,
			'ctrl'     => $this->ctrl,
			'auth'     => $this->fw,
			'tpl'      => $this->tpl,
			'tpl_path' => $this->tpl_path,
			'redirect' => $this->redirect,
			'method'   => $this->method,
			'data'     => $this->data
		);
		return $ret;
	}

	/**
	* @brief Donne un nom à la route
	* @param $name:string nom de la route
	*/
	public function name( $name )
	{
		if( !isset(self::$name[ $name ] ))
			self::$name[ $name ] = $this->getPathNoVar();
		else
			die('Route: name route using: '.$name );
		
		return $this;
	}


	/**
	* @brief Enregistre des données sur la route pour une utilisation ultérieur
	* @param $data: array|text|class ...
	*/
	public function setData( $data )
	{
		$this->data = $data;
		return $this;
	}



	/**
	* @brief retourne la config de la route
	* @param $name:string nom de la route
	*/
	public static function getPathToName( $name )
	{
		$bp = '';
		if( self::$base_path != '' )
		{
			$bp = substr(self::$base_path, 0, -1);
			$bp = '/'.$bp;
		}

		if( isset(self::$name[ $name ] ))
			return $bp.self::$name[ $name ];
		return '#';
	}


	public static function setBasePath( $path )
	{
		self::$base_path = $path;
	}
	public static function getBasePath()
	{
		return self::$base_path;
	}



	public function getPath()
	{
		return $this->path;
	}
	public function getPathNoVar()
	{
		return explode('[',$this->path)[0];
	}
	public function getController()
	{
		return $this->ctrl;
	}
	public function getAuth()
	{
		return $this->fw;
	}
	public function getTemplate()
	{
		return $this->tpl;
	}
	public function getTemplatePath()
	{
		return $this->tpl_path;
	}
	public function getRedirect()
	{
		return $this->redirect;
	}
	public function getMethod()
	{
		return $this->method;
	}
	public function &getData()
	{
		return $this->data;
	}
};