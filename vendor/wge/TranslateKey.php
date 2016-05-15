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
	private $key = '';
	private $tab = [];



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
		$this->tab[ $lang ] = $trad;
		return $this;
	}



	/**
	* @briefrenvoi la valeur de la langue
	* @param $lan:string langue a utilisé
	* @return string ( renvoi '' si rien )
	*/
	public function get( $lang )
	{
		if( isset($this->tab[ $lang ]))
			return $this->tab[ $lang ];
		return '';
	}




	public function getKey()
	{
		return $this->key;
	}
	public function getTrad()
	{
		return $this->trad;
	}
};