<?php
/**
* @brief WGE / Multiton Design pattern Singleton étendu !
* @version 1.0
* @date 10/03/2016
* @details Pattern Singleton qui permet d'instancier qu'une seul class de même type quand elle est appelé via "extends"
*/

namespace WGE;
class Multiton
{
 
  /**
   * @brief Tableau d'indice contenant les class instanciées différentes.
   * @var Singleton
   * @access private
   * @static
   */
   private static $_instance = [];

  /**
    * @brief Constructeur de la classe
    * @param void
    * @return void
    */
   protected function __construct(){  
   }

  /**
    * @brief empeche le clonage de l'object
    * @param void
    * @return void
    */
   private function __clone(){
   }
  
   /**
     * Private unserialize method to prevent unserializing of the *Singleton*
     * instance.
     *
     * @return void
     */
    private function __wakeup(){
    }

  /**
  * @brief Dois être appelé pour instancié une class qui sera unique via class "extends"
  * @param void
  * @return Singleton class unique
  */
   public static function getInstance()
   {
     //vérifier que la class n'est pas déja instancié
     if( !isset( self::$_instance[ get_called_class() ] ))
     {
        //ajout de la class a instancié
        $obj = get_called_class(); 
        self::$_instance[ get_called_class() ] = new $obj;
     }
     //renvoi de la class instancié
     return self::$_instance[ get_called_class() ];
   }

  /**
  * @brief renvoi un singleton disponible dans la mémoire
  * @param $class_name nom de la class a retourner
  * @return retourne la class instancié sinon die()
  */
   public static function getIssetInstance( $class_name )
   {
      if(isset(self::$_instance[ $class_name ]))
        return self::$_instance[ $class_name ];
      return die('Multiton::getIssetInstance not instance: '.$class_name);
   }



  /**
  * @brief renvoi les instances disponibles
  * @return array texte des instances disponibles
  */
   public static function getListInstance()
   {
      $ret = array();
      foreach (self::$_instance as $key =>$value)
        array_push($ret, $key);
      return $ret;
   }
}