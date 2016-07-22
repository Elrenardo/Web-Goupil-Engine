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
			array_push( $tab, $key );
		return $tab;
	}



	/**
	* @brief renvoi la liste des colonnes et type utilisé par le schema 
	* @return array
	*/
	public function getFormat()
	{
		$tab = [];
		foreach ($this->key as $key => $value)
			$tab[ $key ] = $value->getDefault();
		return $tab;
	}



	/**
	* @brief formate toute la chaines en minuscule sans toucher au number
	* @param $str:string 
	* @return string
	*/
	public static function strtolower( $str )
	{
		$type = gettype( $str);
		if( $type == 'string')
			return strtolower( $str );
		return $str;
	}
	public static function arrayStrtolower( $tab )
	{
		foreach ($tab as $key => $value)
			$tab[ $key ] = self::strtolower( $value );
		return $tab;
	}



	/**
	* @brief vérifie que la variable est un email
	* @param $str:string email
	* @return true si email sinon false
	*/
	public static function isEmail( $str )
	{
		if(filter_var( $str, FILTER_VALIDATE_EMAIL))
			return true;
		return false;
	}


	/**
	* @brief convertit un email et vérifie son format
	* @param $phone:string numéro de téléphone
	* @return string email sinon NULL
	*/
	public static function convertPhoneFR( $phone )
	{
		if( $phone[0] == '+' )
			$phone = '0'.substr( $phone, 3);

		$phone  = preg_replace('/[^0-9]/', '',$phone);

		if( strlen($phone) != 10 )
			return NULL;

		return $phone;
	}


	/**
	* @brief génére un UID
	* @param $car:number nombre de caractére générer
	* @return string aléatoire
	*/
	public static function uid($car=50)
	{
		$string = "";
		$chaine = "abcdefghijklmnpqrstuvwxy0123456789ABCDEFGHIJKLMNOPKRSTUWXYZ";
		srand((double)microtime()*time());
		for($i=0; $i<$car; $i++)
			$string .= $chaine[rand()%strlen($chaine)];
		return $string;
	}
};