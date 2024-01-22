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

['customers_read', 'invoices_read', 'users_read'] : Dans cet exemple, trois groupes de sérialisation sont définis : 'customers_read', 'invoices_read', et 'users_read'. Chaque groupe est une chaîne de caractères identifiant un ensemble spécifique de propriétés à inclure lors de la sérialisation.

Par exemple, si vous avez une entité Customer et que vous effectuez une opération de lecture (read), vous pouvez utiliser le groupe 'customers_read' pour déterminer quelles propriétés de l'entité Customer seront incluses dans la réponse.

On évite de mettre un users_read sur user pour éviter les boucles infinies sinon il s'appelle lui même


# Fonctions à ajouter dans customer

   public function getTotalAmount(): float //permet de récupérer le montant total
    {
        return round(array_reduce($this->invoices->toArray(),function($total,$invoice){
            return $total + $invoice->getAmount();
        },0),2);
    }

    $this->invoices->toArray(): Convertit la collection d'objets invoices en un tableau.
    array_reduce(...): Applique une fonction de réduction à chaque élément du tableau, réduisant le tableau à une seule valeur (dans ce cas, le total).
    La fonction de réduction prend deux paramètres:
    $total: Le résultat accumulé jusqu'à présent.
    $invoice: Chaque élément du tableau (un objet invoice dans ce cas).
    La fonction de réduction additionne le montant de chaque invoice au total.
    La valeur initiale pour la réduction est 0.
    Enfin, la fonction round(..., 2) est utilisée pour arrondir le résultat à deux chiffres après la virgule.

        public function getUnpaidAmount(): float //récupère les factures impayées, meme fonction qu'avant sauf que dans le return on a un if pour ne pas prendre en compte les factures payées ou annulées
    {
        return round(array_reduce($this->invoices->toArray(),
        function($total,$invoice)
        {
            return $total + ($invoice->getStatus() === "PAID" || $invoice->getStatus() === "CANCELLED" ? 0 : $invoice->getAmount());
        },0),2);
    }



# # GIT COMMIT 53fbdfc
 # Operations

   operations: [
        new Get(),
        new GetCollection(),
        new Post(),
        new Put(),
        new Delete,
        new Patch()
    ] ca permet de définir quel méthode et quels options on accepte sur l'api (get, post, put, delete, patch)

# Subresource
Utilisée en cas d'URI plus complexe

Dans invoice : 

#[ApiResource(
    uriTemplate: '/customers/{id}/invoices', // va aller récuperer les factures d'un customer particulier 
    uriVariables: [
        'id' => new Link(fromClass: Customer::class, fromProperty: 'invoices') c'est un lien de la classe customers venant de la propriété invoices
    ],
    operations: [ new GetCollection() ], // récupère une collection
    normalizationContext: [
        'groups' => ['invoices_subresource']
    ],
)]

- on fait une fonction getUser

# GIT COMMIT 1189ccf

Dans la config on modifie ca 

        json: ['application/json'] pour qu'il accepte le json

dans services yaml : 
    -     App\Controller\InvoiceIncrementationController:
        public: true // ca sert a permettre l'accès à la fonction depuis l'api

# INCREMENTATION CONTROLLER
- créé à la main 
-     public function __invoke(Invoice $data) la méthode magique invoke permet d'appeler un objet comme une fonction
    {
        $data->setChrono($data->getChrono() + 1); - il récupère le chrono et le modifie en rajoutant plus 1 et met à jour la bdd
        $this->manager->persist($data);
        $this->manager->flush();

        return $data;
    } 

- Dans invoice on va rajouter une une opération 

    new Post(
            controller:InvoiceIncrementationController::class, // il vient de quel controller
            uriTemplate: '/invoices/{id}/increment', // a quel uri
            name: 'Increment', // son nom
            openapiContext:[ // permet de mettre un sommaire et une description quand on fait /api
                'summary' => "Incrémente une facture",
                'description' => "Incrémente le chrono d'une facture donnée"
            ]
        ), 

## GIT COMMIT 2d58a5e

  App\Serializer\PatchedDateTimeNormalizer:
        tags: [serializer.normalizer]
Ce code indique l'utilisation d'un service appelé PatchedDateTimeNormalizer comme un normalizer dans le contexte du sérialiseur (Serializer) de Symfony.

- on fait les assert

Dans invoice quand on fait les assert 

    denormalizationContext: [
        "disable_type_enforcement" => true
    ] // on désactive le type enforcement pour qu'il ne soit plus strict sur le typage de donnéees pour que le serializer accepte les dates

- on retire les ?float et le typage des dates car si nous ne respections pas ca, on nous renvoyait une erreur en anglais sauf qu'ici on veut que l'erreur soit en français et qu'il prenne le message des asserts 


# # GIT COMMIT f82145b

# hasher le password

 event_listeners_backward_compatibility_layer: true // dans ce cas, le fait de le mettre en true sert a permettre l'utilisation des hashs dans les anciens versions de symfony

 // pour le fichier passwordencoder (voir fichier)
# Postman
- Télécharger et créer une collection avec new choisir get, etc et placer l'url de la requete 
- Faire des requêtes GET, POST etc. vers l'API

# OPEN SSL

Telecharger fichier openssl
mettre dans le disque local
C:\OpenSSL-Win64\bin dans variable d'environnement