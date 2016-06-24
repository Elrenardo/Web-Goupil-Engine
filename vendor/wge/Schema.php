<?php
/**
 * @author    Teysseire Guillaume
 * @version   1.0
 * @date      24/06/2016
 * @brief     WGE / Gestion des schemas
 */

namespace WGE;
class Schema
{
	/**
	* @brief liste des colonnes du schea
	* @param $key:array
	*/
	private $key = [];


	/**
	* @brief ajoute une colonne de vérification au schema
	* @param $name:string nom de la colonne
	* @return SchemaColumn
	*/
	public function addColumn( $name )
	{
		$this->key[ $name ] = new SchemaColumn();
		return $this->key[ $name ];
	}



	/**
	* @brief convertit un tableau d'entrer celons le shema
	* @param $input:array
	* @return array
	*/
	public function convert( &$input )
	{
		$output = [];

		//vérification de l'input
		foreach ($this->key as $key => $value)
			$output[$key] = $value->verif( $input[$key] );

		return $output;
	}



	/**
	* @brief convertit un tableau comportant plusieurs entrer celons le shema
	* @param $input:array[ array ]
	* @return array[ array ]
	*/
	public function &multiConvet( &$multi_input )
	{
		foreach ($multi_input as $key => $value)
			$this->convert( $value );
		return $multi_input;
	}



	/**
	* @brief renvoi la liste des colonnes utilisé par le schema
	* @return array
	*/
	public function getColumn()
	{
		$tab = [];
		foreach ($this->key as $key => $value)
			$tab[ $key ] = $value->getValue();
		return $tab;
	}


};