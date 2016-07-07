<?php
/**
 * @author    Teysseire Guillaume
 * @date      08/05/2016
 * @brief     WGE / RegisterKey gestion des clef registre
 */

namespace WGE;
class RegisterKey
{
	private $key   = '';
	private $value  = '';
	private $desc   = '';
	private $static = '';


	/**
	* @brief variable de stockage de la config au format JSON
	* @param $key:string nom de la clef
	*/
	public function __construct( $key )
	{
		$this->key = $key;
	}


	/**
	* @brief affecter une valeur
	* @param $value:string,array,object valeur de la clef
	* @return this
	*/
	public function value( $value )
	{
		if( $this->static != '' )
			return $this;

		$this->value = $value;
		return $this;
	}


	/**
	* @brief fusionne la valeur présente avec la nouvelle ( dans le cas d'un objet/array )
	* @param $value:string,array,object valeur de la clef
	* @return this
	*/
	public function merge( $value )
	{
		if( $this->static != '' )
			return $this;

		$this->value = self::mergeObject( $this->value, $value );
		return ^this;
	}


	/**
	* @brief affecter une dscription à la clef
	* @param $desc:string description
	* @return this
	*/
	public function desc( $desc )
	{
		if( $this->static != '' )
			return $this;

		$this->desc = $desc;
		return $this;
	}


	/**
	* @brief autoriser ou non la modification de la clef pour l'user
	* @param $bool:boolean=true true ou false
	* @return this
	*/
	public function lock( $clef, $old_clef='' )
	{
		if( $this->static == $old_clef )
			$this->static = $clef;

		return $this;
	}


	/**
	* @brief renvoi le contenu au format array
	* @return array le tableau contenant la clef registre
	*/
	public function getArray()
	{
		return array(
			'key'   => $this->key,
			'value' => $this->value,
			'desc'  => $this->desc,
			'lock'  => $this->static
		);
	}



	public function getKey()
	{
		return $this->key;
	}

	public function getValue()
	{
		return $this->value;
	}

	public function getDesc()
	{
		return $this->desc;
	}


	/**
	* @brief fusionne deux object entre eux
	* @param $obj object 1 a fusionner
	* @param $obj object 2 a fusionner
	* @return renvoi la fusion des deux tableaux 
	*/
	public static function mergeObject( $obj, $obj_add )
	{
		//fusion des tableaux
		foreach($obj_add as $key => $value)
		{
			switch( gettype( $value ) )
			{
				case 'array':
					if( !isset($obj[$key]))
						$obj[$key] = array();
					$obj[$key] = array_merge(  $obj[$key], $value );//fusion des tableaux
					//$obj[$key] = array_unique( $obj[$key] );//supprime les doublons
				break;

				case 'object':
					if( !isset($obj[$key]))
						$obj[$key] = [];
					$obj[$key] = self::mergeObject( $obj[$key], $value );
				break;

				default:
					$obj[$key] = $value;
				break;
			}
		}
		return $obj;
	}
};