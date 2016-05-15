<?php
/**
 * @author    Teysseire Guillaume
 * @version   1.0
 * @date      15/05/2016
 * @brief     WGE / Bdd gestion des traductions du texte
 */

namespace WGE;
class Translate
{
	private $lang    = 'fr';
	private $key     = [];


	/**
	* @brief défini la langue a utilisé
	* @param $lang:string id lang
	* @return this
	*/
	public function setLang( $lang )
	{
		$this->lang = $this;
		return $this;
	}



	/**
	* @brief Ajoute une traduction à la clef
	* @param $key:TranslateKey 
	* @return this
	*/
	public function add( TranslateKey &$key )
	{
		$k = $key->getKey();
		$this->key[ $k ] = $key;
		return this;
	}


	/**
	* @brief renvoi une clef
	* @param $name:string nom de la clef
	* @return this
	*/
	public function get( $name )
	{
		$ret = '';
		if( isset($this->key[ $name ]))
		{
			$ret = $this->key[ $name ]->get( $this->lang );
			if( $ret == '' )
				return $name;
			return $ret;
		}
		return $name;
	}
};