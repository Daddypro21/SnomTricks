SNOWTRICKS

snowtricks est un site communautaire pour amateur de snowboard dont la seule mission est de promouvoir ce sport en partageant le maximum de tricks, pour venir en aide à ceux qui pratiquent
déjà le sport et aussi aux aspirants .

TECHNOLOGIES UTILISEES : 

*Symfony6
*PHP8
*Twig
*Bootstrap5
*HTML5/CSS3
________________________________________________________________________________________

COMMENT AVOIR CE PROJET SUR SA MACHINE ET LE FAIRE FONCTIONNER  ?

*utilisez la commande Git clone ou téléchargez tout simplement le zip du projet

*faites un Composer Install

*faites la mis à jour de la base de donnée,allez dans le fichier .env et faites la configuration : DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name
par exemple  : DATABASE_URL=mysql://root:@127.0.0.1:3306/snowtricks

*pour créer la base de donnée tapez la commande : php bin/console doctrine:database:create 

*faites les migrations avec la commande  : php bin/console make:migration

*Tapez la commande "php bin/console doctrine:migrations:migrate" pour migrer les migrations dans la base de donnée 

NB: Vous devrez avoir mysql ou un autre driver installé sur votre machine, je vous 
suggère d'installer  xampp ou wamp .

