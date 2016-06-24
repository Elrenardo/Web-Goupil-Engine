<?php
/**
 * @author    Teysseire Guillaume
 * @version   1.0
 * @date      24/06/2016
 * @brief     WGE / Gestion des key schemas
 */

namespace WGE;
class SchemaColumn
{
	/**
	* @brief valeur par default de la colonne
	* @param variable
	*/
	private $default = '';

	/**
	* @brief type de la colonm
	* @param string
	*/
	private $type    = 'text'; 


	/**
	* @brief définir une valeur par default a la clef
	* @param $value:string
	* @return this
	*/
	public function value( $value )
	{
		if( gettype($value) == $this->type )
			$this->default = $value;
		return $this;
	}

	/**
	* @brief définir un type a la clef : http://php.net/manual/fr/function.gettype.php
	* @param $value:string
	* @return this
	*/
	public function type( $value )
	{
		$this->type = $value;

		//si c'est un number
		if( $value=='integer' || $value=='double' )
			$this->default = 0;

		return $this;
	}


	/**
	* @brief vérifie si l'entrer correspond a la table sinon renvoi la valeur par default
	* @param $type_input:??
	* @return $type_input convertit
	*/
	public function verif( &$type_input )
	{
		if( gettype( $type_input ) == $this->type )
			return $type_input;
		return $this->default;
	}





	public function getType()
	{
		return $this->type;
	}

	public function getValue()
	{
		return $this->default;
	}

};