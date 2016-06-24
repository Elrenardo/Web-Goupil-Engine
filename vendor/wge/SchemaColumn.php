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
	* @brief défini une fonction de conversion
	* @param string
	*/
	private $func    = NULL;


	/**
	* @brief définir une valeur par default a la clef
	* @param $value:string
	* @return this
	*/
	public function setDefault( $value )
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
	* @brief définir une fonction de conversion pour le type
	* @param $func:function
	* @return this
	*/
	public function type( $func )
	{
		$this->func = $func;
		return $this;
	}


	/**
	* @brief vérifie si l'entrer correspond a la table sinon renvoi la valeur par default
	* @param $type_input:??
	* @return $type_input convertit
	*/
	public function verif( &$type_input )
	{
		$type = gettype( $type_input );

		//si le type existe pas !
		if( $type == 'NULL' )
			return $this->default;

		//si le type est bon
		if( $type == $this->type )
			return $type_input;

		//si une fonction de conversion est attaché
		if( $this->func != NULL )
			return $this->func( $type_input );

		//sinon convertion du type
		return settype( $type_input, $this->type);
	}




	//Ascenseur get ...
	public function getType()
	{
		return $this->type;
	}

	public function getDefault()
	{
		return $this->default;
	}
	public function getFunc()
	{
		return $this->func;
	}

};