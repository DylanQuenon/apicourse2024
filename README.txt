Pour cloner:
git clone adresse
- composer i
- php bin/console importmap:install
- php bin/console d:d:c
- php bin/console d:m:m
- composer require orm-fixtures

CREATION FAKER DANS APPFIXTURES


// installation api platform
composer require api
SECURITE
Il y'a une sécurité sur l'api donc il y'a une erreur, elle accepte qu'une adresse (voir .env => CORS_ALLOW_ORIGIN).

-dans le package bundles nelmio_cors, PUT signifie qu'il veut toutes les données.
- faire /api en mode prod pour voir l'api 
Pour affilier une api avec une entity aller dans l'entity et se placer au dessus et y mettre:
#[ApiResource()] /!\ IMPORTER LA CLASSE

//inscription postman telecharger
new collection
add request => get customer
entrez url => http://127.0.0.1:8000/api/customers
mettre ApiResource dans les entity liés
#[ApiFilter(SearchFilter::class,properties:[
    "firstName"=>"partials",
    "lastName",
    "company"
])] 
permet de faire des recherches via nom prénom et la company le partials c'est l'équivalent du like en sql
dans le package avec l'api yaml 
pagination_enabled: false => désactive pagination
        pagination_items_per_page: 30 => si il y'en avait eu y'en aurait eu 30 par pages
        pagination_client_enabled: true => peux il l'activer lui meme via l'url oui
        pagination_client_items_per_page: true => si il l'active il peut choisir combien d'items il veut par page  

http://127.0.0.1:8000/api/customers?pagination=true&itemsPerPage=10