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
	private static $lang    = 'fr';
	private $key            = [];


	/**
	* @brief défini la langue a utilisé
	* @param $lang:string id lang
	* @return this
	*/
	public static function setLang( $lang )
	{
		self::$lang = $lang;
	}

	/**
	* @brief renvoi la langue par default
	* @param $lang:string id lang
	* @return this
	*/
	public static function getLang()
	{
		return self::$lang;
	}



	/**
	* @brief Ajoute une traduction à la clef
	* @param $key:TranslateKey 
	* @return this
	*/
	public function add( TranslateKey &$obj )
	{
		$k = $obj->getKey();
		$this->key[ $k ] = $obj;
		return $this;
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


	/**
	* @brief renvoi un tableau avec les clef et traduction demandé
	* @return array
	*/
	public function getAll()
	{
		$buffer = [];
		foreach ($this->key as $key => $value)
		{
			$buffer[ $key ] = $value->get();
		}
		return $buffer;
	}
};