<?php
/**
 * @author    Teysseire Guillaume
 * @version   1.0
 * @date      30/04/2016
 * @brief     WGE / RESTserver gestion des serveurs REST
 */

namespace WGE;
class RESTserver
{
	private $ctrl = NULL;
	private $auth = NULL;


	/**
	* @brief ajout de l'instance au server REST
	* @param $ctrl:class
	* @return this
	*/
	public function instance( $ctrl )
	{
		$this->ctrl = $ctrl;
		return $this;
	}


	/**
	* @brief ajoute une authorisation a l'instance REST
	* @param $auth:string ou NULL
	* @return this
	*/
	public function auth( $auth=NULL )
	{
		$this->auth = $auth;
		return $this;
	}


	/**
	* @brief éxécute une méthod d'une instance
	* @param $method:string
	* @param $param:array
	* @return résultat de la fonction
	*/
	public function exec( $method, $route, $param )
	{
		//vérifier l'auth
		if( !is_null($this->auth))
		{
			if( !App::isAuth( $this->auth ))
				return '401';
		}

		//éxécuter le controller
		if( !is_null($this->ctrl))
		if( method_exists($this->ctrl, $method ))
			return $this->ctrl->{ $method }( $route, $param );
		return '';
	}
};