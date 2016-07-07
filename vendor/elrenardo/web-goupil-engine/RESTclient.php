<?php
/**
 * @author    Teysseire Guillaume
 * @date      08/05/2016
 * @brief     WGE / RESTclient éffectuer une request REST en PHP
 */

namespace WGE;

use \WGE\Route;

class RESTclient
{
	private $addr = '';
	private $post = NULL;

	/**
	* @brief Constructeur
	* @param $addr:string nom de la route
	*/
	public function __construct( $addr )
	{
		$this->addr = $addr;
	}

	/**
	* @brief Ajout de POST a la request
	* @param $array:array
	*/
	public function post(array $array )
	{
		$this->post = $array;
	}



	/**
	* @brief renvoi le contenu de la request REST
	* @return string
	*/
	public function get()
	{
		$url        = 'http://'.$_SERVER['HTTP_HOST'].'/'.App::IDREST.'/'.Route::getBasePath().$this->addr;
		$url_actuel = 'http://'.$_SERVER['HTTP_HOST'].'/'.App::IDREST.'/'.Route::getBasePath();

		if( $url_actuel == $url )
			return die('Error client REST: http loop request ! ->'.$url );

		//Envoyer la request HTTP
		return $this->getHttp( $url, $this->post );
	}



	/**
	* @brief renvoi le contenu de la request REST au format array
	* @param array
	*/
	public function getArray()
	{
		return json_decode( $this->get(), true );
	}


	/**
	* @brief Prépare l'envoi de la request en HTTP ave le cookie de session
	* @param $url:string
	* @param $post:array
	*/
	private function getHttp( $url, $post )
	{
		$cookie = '';
		if( isset($_SERVER['HTTP_COOKIE']))
			$cookie = "\r\n".'Cookie: ' . $_SERVER['HTTP_COOKIE']."\r\n";

		//Préparer les headers et session
		$opts = array('http' => array(
		        'header'  => 'Content-type: application/x-www-form-urlencoded'.$cookie,
		));

		//Si on envois des post en même temps
		if( $this->post )
		{
			$postdata = http_build_query( $post );
			$opts['http']['method']  = 'POST';
			$opts['http']['content'] =$postdata;
		}

		//Envoyer la demande
		$context  = stream_context_create($opts);
		session_write_close();//sauvegarde et fermer la session
		$buffer = file_get_contents( $url, false, $context);
		session_start();//réouverture
		return $buffer;
	}
};