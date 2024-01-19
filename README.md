# Pour cloner:
- git clone adresse
- composer i
- php bin/console importmap:install
- php bin/console d:d:c
- php bin/console d:m:m
- composer require orm-fixtures


## GIT COMMIT 78aa78d

- Création et migration bdd


## GIT COMMIT 66458c5

- Création du faker User,Customers,Invoices


## GIT COMMIT 16cfc6b

# Installation API Platform
- composer require api

# Sécurité CORS
- Il y a une sécurité sur l'API qui accepte qu'une adresse (voir .env => CORS_ALLOW_ORIGIN)
- Dans le bundle NelmioCorsBundle, on voit les méthodes autorisées (GET, POST, DELETE, PUT, PATCH)

# Associer une API à une entité
- Placer #[ApiResource()] au-dessus de l'entité (importer la classe)
- Une fois qu'on a placé l'api resource aller à l'adresse suivante (http://127.0.0.1:8000/api) pour voir les entity reliées à l'api et les méthodes actives (qu'on pourra déterminer par la suite voir partie sur groups pour la requete postman)
- Ajouter ApiResource dans les entités liées
- Ajouter des filtres de recherche (voir Postman) avec #[ApiFilter(SearchFilter::class, properties:[...])] ce qui permet de faire des recherches dans la requete pour récupérer les données de json à tester avec postman, la propriété partials est l'équivalent du like en sql
- #[ApiFilter(OrderFilter::class)] sert à trier sur certaines propriétés (voir entity invoice)

## GIT COMMIT 21bbfcd 

change le nom du paramètre pour la requete ainsi on écrit juste count à la place de items_per_page
      collection:
        pagination:
            items_per_page_parameter_name: 'count'
- dans l'entity 

#[ApiResource(
    paginationEnabled:true, 
    paginationItemsPerPage: 10, 
    order: ['amount'=>'asc'],
    normalizationContext: [
        'groups' => ['invoices_read']
    ]
)] on fait la pagination ressource par ressource

Il y'a 3 manière de changer la config;
-via api platform
-via ressource http c'est client qui écrit dans la navbar
-via ressource par ressource

# Groups et Normalization
Normalisation – c’est un contexte quand nous demandons à l’API de nous envoyer des informations,
c’est-à-dire la transformation des données de nos entités Doctrine (objet PHP) en tableau (Array)
normalizationContext: [
'groups' => ['customers_read']
] => a mettre dans l'entity customers

dans les autres entity mettre : #[Groups('customers_read')] préciser dans quels groupes une propriété doit être
exposée
Sérialisation – c’est la transformation des tableaux obtenus par la normalisation en un autre format
(JSON, XML, CSV)

Dans @ApiResource on a l’option normalizationContext qui permet de préciser des options pour le
contexte de normalisation.
L’option « groups » va permettre de définir les groupes de sérialisation (on peut en avoir plusieurs et
demande le nom des groupes que l’on veut créer)
    
# Postman
- Télécharger et créer une collection avec new choisir get, etc et placer l'url de la requete 
- Faire des requêtes GET, POST etc. vers l'API
