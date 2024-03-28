# US_Avranches_Symfony
Penser à supprimer le fakerphp/faker


=====================================

Photo pour appel  : images pour mettre photo groupe u10 pour u10 etc...
Consignes pour photo du joueur : fond neutre / équipements du club (jogging) / bon éclairage / type de photo (d’idendité)
Appel : gérer les motifs d’abscences, savoir pour quelles raisons
3 types d’abscences : malade / blessé / “sportives” / non justifiée
Taux de présence dans la fiche du joueur
Pas matchs ni entrainement en nom —> changer de terme (ex. Rassemblement)

Terrain Alexandre :
Fiche vierge sans les positions
Quel est ton poste préféré où tu voudrais jouer ? (1 fois par an / Gérer la date du clic)
Que le joueur puisse cliquer la où il préfère jouer (afficher point cliqué pour coach)
Une fois qu’il a cliqué, joueur choisit bouton poste sur le terrain (comme ça coach peut voir la différence)
Le coach doit pouvoir changer le poste du joueur

Alignement :
Rapproché du centre les défenseurs un peu plus haut / poste gardien un peut plus haut
Milieu droit plus écarté
Milieu défensif, changer nom —> milieu
Former triangle entre les mileu et l’attaquant
Ne pas alligné les cases du milieu (les 4)
Flèche pour savoir sens de l’attaque

Bryan
VMA en kmh
Demi-cooper 6 minutes
Cooper c’est une distance en 12 minutes
0-20 pour la vma
Choisir tests par joueur
Conduite de balle (6-15 secondes)
Vitesse (aller-retour, 6-15 secondes) avec les informations au centième de secondes

Arthur
Taille graph : commence à 1m jusqu’à 1m80
Caractéristiques : à rentrer à la venue dans le club (mémorise la date), obligation à modifier au bout d’un an, bloquer l’accès au reste tant que les caractéristiques ne sont pas mises à jour.
Pareil pour son poste ça va avec.
Voir l’évolution de u10 à u13 sur la même courbe
Créer des catégories u14-u15-u16 pour l’avenir
Commencer u10 jusqu’en u17 —> permettre à l’administrateur de créer une nouvelle catégorie U.. Pour permettre l’évolution du site à long terme

===========================================================



En cours: 

Upload d'image de profile.
Extension installé : vich/uploader-bundle


Modal pour supprimer des cruds

A faire : 
 Bouton "active" dans la barre des tâches -> Fait
 Gestion A2F -> Fait
 Utilisateur -> Edit + Index -> Fait
 Parameter -> Fait

==========================================================

Tableau de bord 
    -> Si Coach 
        -> Afficher tous les users étant coach à la place de l'équipe
        -> Afficher tous les paliers (Validés d'office)

Graphique
    -> Si coach
        -> Afficher tous les paliers (Validés d'office)
        -> Voir tous les graphiques
        -> Ajouter les possibilité de voir les graphiques d'un joueur
Paliers
    -> Si coach
        -> Possibilité d'accéder au CRUD

Poste
    -> Si Coach
        -> Ne pas y avoir accès ( Valeurs restent en NULL)

PDF
    -> Si Coach
        -> Accéder à les liste des joueurs et choisir le pdf d'un joueur
        -> Déplacer le bouton dans "Administration"

ALL
    -> revoir la sécurité
    -> Installer les sécu Poid/Taille partout