<?php
/**
 * @author    Teysseire Guillaume
 * @version   1.0
 * @date      15/05/2016
 * @brief     WGE / Bdd gestion des traductions du texte
 */

namespace WGE;

class TranslateKey
{
	private $key   = '';
	private $value = '';



	/**
	* @brief constructeur
	* @param $key:string
	*/
	public function __construct( $key )
	{
		$this->key = $key;
	}



	/**
	* @brief Ajoute une traduction à la clef
	* @param $lan:string langue a utilisé
	* @param $trad:string valeur de la traduction
	* @return this
	*/
	public function set( $lang, $trad )
	{
		//Ajout de la langue uniquement si c'est la bonne
		if( Translate::getLang() == $lang )
			$this->value = $trad;

		return $this;
	}



	/**
	* @briefrenvoi la valeur de la langue
	* @return string
	*/
	public function get( )
	{
		return $this->value;
	}




	public function getKey()
	{
		return $this->key;
	}
};