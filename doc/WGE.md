# Web Goupil Engine:

Le Web Goupil Engine est un programme écrit en PHP, et conçu pour permettre la création de site internet ou web application moderne facilement. Il a été conçu dans l'optique de faciliter l'apprentissage et utilisation du Framework par des programmeurs de tout niveaux !


__Les principales fonctionnalités sont :__
* Gestion multi-sites.
* Architecture simple.
* Gestion de Plugin.
* Gestion de Registre.
* Serveur et client REST.
* Gestion des Routes, Firewall, ...
* Template TWIG.
* ...


# Sommaire
* [Arborescence](#arborescence)
* [Fichier racine index.php](#index.php)
* [Création de Plugin](#plugins)
* [Gestion des Hosts](#hosts)
* [Route et controller](#route)
* [Gestion des erreurs HTTP](#error)
* [Template](#template)
* [Authorisation et Firewall](#firewall)
* [Nommer une route](#name_route)
* [Client & serveur REST](#rest)
* [Gestion des Registres](#registre)
* [Gestion des Services](#service)
* [Sécurité GET, POST & COOKIE](#security)
* [Base de données](#bdd)


<br/><br/><br/>
<a name="arborescence"></a>
## Arborescence

L'arborescence du projet est composée de quatre dossiers et 3 fichiers:

- Un dossier "__doc__", qui contient la documentation du "Web Goupil Engine".

- Deux autres dossiers "__homes__" et "__plugins__", qui contiennent tous les deux des plugins. Le dossier "plugins" contient des plugins génériques, qui peuvent être utilisés dans différents sites. Tandis que le dossier "homes" contient les plugins spécifiques à chaque site, géré par le Framework.<br/>

- Un dossier "__vendor__", qui contient les dépendances du projet. Il est géré par le programme "__composer__". Ce programme gère également la configuration du projet qui se trouve dans le fichier "__composer.json__".

- Un fichier "__htaccess__" pour la gestion des routes propres.

- Et pour finir un fichier "__index.php__" qui contient la configuration des sites web géré par le Framework que nous verrons juste après.



<br/><br/><br/>
<a name="index.php"></a>
## Fichier racine index.php

Le fichier index.php est le fichier qui contient la configuration et la gestion des différents sites web géré par le Framework.

Exemple de code minimal pour le fichier __index.php__ vide:
```php
<?php
//Dépendance composer et espace de nom.
require_once './vendor/autoload.php';
use WGE\App;

//Code "Web Goupil Engine" ici
```

Voici un exemple d'index pour la gestion d'un seul site avec un plugin:
```php
<?php
//Dépendance composer et espace de nom.
require_once './vendor/autoload.php';
use WGE\App;

//Création du plugin home pour mon premier site
App::plugin('monSite')->path('homes/monSite/')->add('site.php');

//Création du Host de mon premier site
$host_current = App::getCurrentHost();
App::host( $host_current )->pluginHome('monSite');
```
Nous verrons plus en détails le fonctionnement de cette exemple par la suite mais j'attire votre attention sur la class static "__App__".

Cette class static et une interface d'utilisation entre vous et le fonctionnement de "Web Goupil Engine". Elle vous fournit tout ce dont vous avez besoin pour créer votre site rapidement et facilement et s'occupera du reste (comme l'initialisation et la configuration du kernel, des routes, services ...)





<br/><br/><br/>
<a name="plugins"></a>
## Création de plugins

Nous venons de voir le fichier index.php et nous allons parler d'un fonctionnement essentiel: les __plugins__.

Reprenons cette ligne:
```php
//Création du plugins pour mon premier site
App::plugin('monSite')->path('homes/monSite/')->add('site.php');
```
Les plugins sont l'essence même du Framework. La commande "App::plugin()" permet de déclarer un nouveau plugin. Chaque plugin doit avoir un nom différent pour éviter de potentiels conflits entre eux.

Dans "Web Goupil Engine", tous les composants ont une syntaxe identique de déclaration au format:
```php
//J'appelle l'interface App
App::plugin('le_nom_du_plugin')
//Suivie des paramettres
->path('/plugins/admin/')
->add('admin.php');
```
L’interface App::plugin() renvoi une instance de la class \WGE\Plugin.
Cette class prend deux paramètres possible:
* __path( string )__ : Le chemin vers la racine du dossier du plugin (exemple: /plugins/users/ ou /homes/web/ )
* __add( string )__ le fichier à exécuter dans le dossier identifié par le "path". Le fichier ciblé contient les instructions du plugin.

Dans l'exemple au-dessus, on peut donc voir que le fichier 'admin.php' se trouve dans le répertoire 'plugins/admin/admin.php' et que la racine du répertoire est 'plugins/admin/'.

Le contenu du plugin ne sera chargé que lors de sa déclaration. Il attendra d'être exécuté par une class "Host".





<br/><br/><br/>
<a name="hosts"></a>
## Gestion des Hosts

La gestion des "hosts" permet l'utilisation des fonctionnalisées __multi-site__ ou d'activer ou non des plugins pour certains site.

Revenons donc à notre exemple:
```php
$host_current = App::getCurrentHost();
App::host( $host_current )->pluginHome('monSite');
```
Ici, je vais demander à l'interface "App" de me donner une instance de la class "__Host__" qui me permettra d'y attacher des plugins.
```php
App::host('monsite.fr');
```
Avec cette ligne je créé un host uniquement si l'utilisateur demande l'URL: "monsite.fr", je peux ainsi créer plusieurs host pour chaque __nom de domaine__.


Créer un host c'est bien, mais s’il n'utilise pas de plugin ça ne sert à rien.
```php
//Je déclare mon host 'monsite.fr' et j'y ajoute le plugin 'monSite'
App::host('monsite.fr')->pluginHome('monSite);
```
Pour qu'un host fonctionne, l'un des plugins doit être un __plugin home__ (dans /homes). Ce plugin contient le site internet.


Si je veux rajouter un plugin générique (dans /plugins):
```php
App:plugin('admin')->path('plugins/admin/')->add('admin.php');
//Je déclare mon host 'monsite.fr' et j'y ajoute le plugin 'admin' et l'home 'monSite'
App::host('monsite.fr')->plugin('admin')->pluginHome('monSite');
```
L'ordre de chargement des plugins est très important car certains plugins ont besoin d'autres plugins pour fonctionner. __le "pluginHome" sera toujours le dernier !__



Voici un autre exemple:
```php
App::host('vador.fr')->plugin('empire')->plugin('troopers')->pluginHome('etoileNoir');
```

Mais je fais comment si mon site contient qu’un site web ou si je ne connais pas mon nom de domaine ?
```php
//Renvoi le nom de domaine utilisé par le visiteur du site
$host_current = App::getCurrentHost();
```





<br/><br/><br/>
<a name="route"></a>
## Route et controller

Maintenant que nous avons vu comment gérer les host, passons maintenant au contenu des plugins.
Nous avons vu que chaque plugin appelle via la méthode "__add( string )__" un fichier PHP qui contient la configuration du plugin:
```php
App::plugin('monSite')->path('homes/monSite/')->add('site.php');
```

Voici donc le contenu minimal de ce fichier 'site.php'
```php
<?php
use WGE\App;
//Code "Web Goupil Engine" ici
```
C’est tout !

A cette étape là, si vous allez à la racine de votre serveur vous voyez normalement une page blanche. C'est normal, notre Framework pour le moment trie uniquement les hosts.

Nous allons donc lui rajouter une route !
Reprenons notre fichier 'site.php'
```php
<?php
use WGE\App;

//Je crée une route index qui affichera 'Hellow world !' dans la page
App::route('/')->controller('Hello World !');
```
Maintenant, si vous rechargez la page vous verrez un magnifique "Hello World !".

__<b style="color:red;">/!\ Il n'est pas conseillé d'écrire du code autre que celui de "Web Goupil Engine" en dehors d'un "controller()". Cela peut nuire au bon fonctionnement du Framework !</b>__

Le fonctionnement des routes est très simple. Dans un premier temps, vous déclarez le chemin de la route (ici: / pour indiquer la racine). Mais nous pouvons aussi bien faire des routes comme ceci:
* / 
* /pages/cv
* /info/actualite/2
* /magasin/[a:id]

Le dernier cas est un cas spécifique qui vous permet de récupérer des paramètres dans la route.
```
[ filtre : nom_variable ]
```
Listes des filtres possibles pour les routes:
* __i__ : nombres
* __a__ : caractères
* __\*__ : nombre et caractères
* __\**__ : tout !
* __?__ : paramètre facultatif

Ce qui nous permet d'écrire des routes comme:
```php
//Exemple de route
App::route('/pages/[a:id]');
App::route('/info/[i:id]/[**:clef]');
App::route('/print/[*:id]?');
App::route('/promo/?[i:code]?');
```

Une fois la route créée, un controller est appelé s’il est précisé !
Le controlleur permet d'exécuter un certain nombre d'actions si la route est appelée.
Le controller peut prendre plusieurs types de variable :
```php
//Afficher du texte
->controller('texte à afficher')

//Un tableau qui sera converti en JSON pour les Web applications lors de son affichage
->controller( array('Dark'->'Vador', 'Luc'=>'Skywalker'))

//Une fonction
->controller(function( $route, $params ){
    //Mon code PHP ici
    return 'Hello World';//ici je peux retourner une chaine, un object, un array, ...
})
```
Lors de l'utilisation de fonction, les deux paramètres sont:
* la route utilisée !
* les paramètres dans un array de la route __[i:id]__ sera disponible dans __$params['id']__

Je peux aussi limiter l'appel à la route selon le type d'envoi:
```php
//uniquement GET
->method('GET')

//uniquement POST
->method('POST')

//GET et POST par default
->method('GET|POST')
```

Effectuer une redirection vers une route:
```php
//On redirige la page vers /home/user
->redirect('/home/user')
```

Exemple de route:
```php
//Afficher l'ID d'un user:
App::route('/user/[*:id]')->controller(function( $route, $params ){
    return 'Votre ID est: '.$params['id'];
})->method('GET');

//Afficher un message
App::route('/hello')->controller('Bonjour !');

//Afficher des informations au format JSON pour une Web Application
App::route('/json')->controller( array(...))->method('GET|POST');
```



<br/><br/><br/>
<a name="error"></a>
## Gestion des erreurs HTTP

Pour gérer les erreurs de routes, par exemple, l'erreur 404, vous pouvez déclarer des routes spécifiques pour chacune des erreurs HTTP disponibles:<br/>
Exemple:
```php
//Gestion erreur 404
App::error( 404 )->controller('Il n\'y a rien ici !');
```
L'interface "__App::error()__" renvoie une class Route qui peut être utilisée et paramétrée comme n'importe quelle autre route.




<br/><br/><br/>
<a name="template"></a>
## Template

Jusqu'à présent, nous avons vu comment faire des routes donnant un affichage simple ou en JSON pour les web Application.

Nous allons maintenant voir les Template.
"Web Goupil Engine" utilise le moteur de Template TWIG : http://twig.sensiolabs.org/
Ce moteur de Template gère énormément de choses tels que l'héritage de Template ou le traitement d'informations directement dedans.

Documentation pour la réalisation de Template TWIG:<br/>
http://twig.sensiolabs.org/doc/templates.html

Comment créer un nouveau Template dans un __plugin__:
```php
App::template('my_template')->path('tpl/index.twig');
```
Cette ligne va créer la Template du nom de __my_template__ dont les fichiers se retrouveront dans le dossier 'tpl/index.twig' __de mon répertoire plugin !__


Utilisation d'un Template avec une route:
```php
App::route('/')->template('my_template');
```

'Web Goupil Engine' ajoute automatiquement plusieurs fonctions au Template TWIG pour vous faciliter son utilisation:
```html
<!-- Convertit le chemin d'un plugin en chemin complet -->
{{ path('/tpl/image.jpg') }} <!-- Donnera: /homes/mySite/tpl/image.jpg -->

<!-- Convertit le chemin d'un plugin en chemin complet type url -->
{{ pathUrl('/tpl/image.jpg') }} <!-- Donnera: http://127.0.0.1/homes/mySite/tpl/image.jpg -->

```

Passage de paramètres au Template:
```php
//J'envoie l'array dans le template twig pour traitement
App::route('/')->controller( array(...) )->template('my_template');

//Mon texte en html sera disponible dans le template, dans la variable "html"
App::route('/')->controller('Hello World !')->template('my_template');

//Les paramètres de la route n'étant pas utilisés par un controller, il sont automatiquement envoyés au template
App::route('/pages/[a:page]')->template('my_template');
```

Ajouter un object global dans les templates:
```php
App::addGlobalTpl( 'nom_de_la_global', new MyClass() );

//Ce qui donnera dans le template:
{{ nom_de_la_global.maMethod() }}

//Extension de template:
{% extends template('index') %}
```
Dans le dernier cas, l'extension(extend) de Template va utiliser le plugin home "__index__" par default.
Pour changer ce fonctionnement car il est possible que votre Template home ce trouve dans un plugin autres que le "home". Il vous suffira dans la définition du Template, de précisez le paramètre __extends__ suivi du nom du plugin.
```php
App::template('my_template')->path('tpl/index.twig')->extend('myPlugin');
```


Ajouter une fonction dans les Templates:
```php
App::addFuncTpl('nom_de_la_fonction',function( ... ){
    //Votre code PHP ici
    return 'Hello World !';
});

//Ce qui donnera dans le template:
{{ nom_de_la_fonction( ... ) }}
```





<br/><br/><br/>
<a name="firewall"></a>
## Authorisation et Firewall

Si vous voulez limiter l'accès à une route pour certaines personnes possédant les droits nécessaires, il vous faudra utiliser le __Firewall__
```php
//Ajout d'un firewall à une route 
App::route('/secret')->firewall('VIP');
```
Seuls les personnes "VIP" pourront accéder à la page "/sercret" 

Ajouter une authorisation:
```php
App::auth('VIP');
```

Tester une authorisation dans les Templates:
```html
<!-- A le droit d'afficher cette page -->
{% if isFirewall('VIP') %}
    <!-- HTML -->
{% endif %}
```

<a name="name_route"></a>
## Nommer une route

Il est possible de nommer une route pour ne pas avoir à retaper son chemin à chaque fois que vous l'utilisez. De plus, si vous modifiez le chemin de votre route, toutes les routes s'actualiseront automatiquement !
```php
App::route('/info/vaucluse/data/new/[*:article]->name('pages_article');
```

Récupérer la route en PHP:
```php
//TODO
```

Récupérer la route dans un template:
```html
{{ route('pages_article') }}

<!--Exemple avec le paramètre: -->
<a href="{{ route('pages_article' }}5865a" ></a>
```






<br/><br/><br/>
<a name="rest"></a>
## Client et Serveur REST

La création d'un serveur REST est l'une des bases du 'Web Goupil Engine', elle permet d'utiliser des class directement dans le routage des routes, ce qui permet leurs utilisations sous forme d'API.

```php
class Joueur{
    //Renvoi le nom du joueur
    public function name(){
        return 'Jean-Paul';
    }
    //Renvoi les stats du joueurs
    public function stats(){
        return array(...);
    }
    //...
};

//Création su serveur REST
App::RESTserveur('nom_du_serveur_rest')->instance( new Joueur() );
```
Si je veux appeler via une URL la method "name" de la class "Joueur", il suffira de taper dans une barre d'addresse:
```
/rest/nom_du_serveur_rest/name
```
Comme toutes les routes, si le return est un array, alors le message sera traduit en JSON.

Appeler un client REST en PHP:
```php
//Appel du serveur REST
App::RESTclient('/rest/nom_du_serveur_rest/stats' );

//Appel du serveur REST et envoi d'informations au format POST
$texte = App::RESTclient('/rest/nom_du_serveur_rest/name', array(...));

//Appel du serveur REST et retour du résultat sous forme d'array
$tab = App::RESTArrayClient( '/rest/nom_du_serveur_rest/name' );
$tab = App::RESTArrayClient( '/rest/nom_du_serveur_rest/name', array(...));//POST
```

Dans les templates:
```
{{ RESTclient('/rest/nom_du_serveur_rest/name', tab ) }}
```

<br/><br/><br/>
<a name="registre"></a>
## Gestion des Registres

Le registre permet de stocker des informations sur le site et de les sauvegarder pour les recharger. Lorsque vous chargez / sauvegardez un registre, les valeurs chargées ensuite seront prioritaires sur celles définies dans le code PHP.

Les registres sont très utiles dans le plugin des configurations:
```php
//Ajouter une valeur au registre
App::register('nom_de_la_clef')->value('valeur_de_la_clef');
App::register('nom_de_la_clef')->value( array(....));

//Fusionner la valeur actuelle du registre avec une autre
App::register('nom_de_la_clef')->merge(...);

//Retourner une valeur du registre
$clef = App::getRegister('nom_de_la_clef');

//Sauvegarder le registre
App::registerSave();
```



<br/><br/><br/>
<a name="service"></a>
## Gestion des Services

Le gestionnaire de services permet de stocker une instance dans un contenaire pour pouvoir la récupérer n'importe ou.

Par dfault vous pouvez demander les services suivants:
- "kernel"
- "router"
- "render"
- "config"
- "plugins"
- "REST"

Gestion des services:
```php
//Ajouter un service
App::addService( 'nom_de_mon_service', new myClass() );

//Récupérer un service
$service = App::getService( 'nom_de_mon_service' );
```



<br/><br/><br/>
<a name="security"></a>
## Sécurité GET, POST & COOKIE

Lors de la réception de variable GET, POST et COOKIE, le Framework nettoie automatiquement ces variables avec la fonction PHP:
```php
htmlspecialchars()
```
Ce qui neutralise toute forme d'injection HTML dans ces variables. Néanmoins si on veut récupérer le contenu d'une variable au format HTML il faudra faire une inversion de "htmlspecialchars":
```php
$_GET['ma_var'] = htmlspecialchars_decode( $_GET['ma_var'] );
```




<br/><br/><br/>
<a name="bdd"></a>
## Base de données

Se connecter à une Base de données:
```php
App::bdd('localhost')
->user('root')
->password('')
->database('mybase')
->connexion();
```

Le gestionnaire de Base de données utilise "Pixie Query Builder"<br/>
Documentation:<br/>
https://github.com/usmanhalalit/pixie

Exemple de request effectuable grâce au Query Builder Pixie:
```php
//Création d'une query d'exemple:
$ret= App::query('ma_table')->select('*')->where('id','=', $id )->get();

//Compter le nombre de résultat avec un LIKE
$nb= App::query('ma_table')->where('id','LIKE','%'.$id.'%')->count();

//Premier résultat
$ret = App::query('ma_table')->first();

//Tous les résultats ( exemple jointure )
$ret= App::query('ma_table,ma_table2')
->where('ma_table.id','=','ma_table2.id')->get();

//Trouver tous les élements égales à
$ret= App::query('ma_table')->findAll('name', 'Sana');
```
