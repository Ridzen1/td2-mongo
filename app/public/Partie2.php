<?php
require dirname(path: __DIR__) . '/src/vendor/autoload.php';

try {
    $client = new MongoDB\Client("mongodb://mongo-mongo-1:27017");
    $collection = $client->pizzashop->produits;

    echo "<h2>2.1 - Liste simple (Numéro, Catégorie, Libellé)</h2>";
    
    $cursor = $collection->find([], [
        'projection' => [
            'numero' => 1, 
            'categorie' => 1, 
            'libelle' => 1, 
            '_id' => 0
        ]
    ]);

    echo "<ul>";
    foreach ($cursor as $p) {
        echo "<li>N°" . $p['numero'] . " : " . $p['libelle'] . " (" . $p['categorie'] . ")</li>";
    }
    echo "</ul>";

    echo "<h2>2.2 - Détails du produit N°6</h2>";

    $p6 = $collection->findOne(['numero' => 6]);

    if ($p6) {
        echo "<strong>Libellé :</strong> " . $p6['libelle'] . "<br>";
        echo "<strong>Catégorie :</strong> " . $p6['categorie'] . "<br>";
        echo "<strong>Description :</strong> " . $p6['description'] . "<br>";
        echo "<strong>Tarifs :</strong><ul>";
        foreach ($p6['tarifs'] as $tarif) {
            echo "<li>" . $tarif['taille'] . " : " . $tarif['tarif'] . "€</li>";
        }
        echo "</ul>";
    } else {
        echo "Produit introuvable.";
    }

    echo "<h2>2.3 - Produits pas chers (taille normale <= 3.0€)</h2>";

    $filter = [
        'tarifs' => [
            '$elemMatch' => [
                'taille' => 'normale',
                'tarif' => ['$lte' => 3.0]
            ]
        ]
    ];

    $cursor = $collection->find($filter);

    echo "<ul>";
    foreach ($cursor as $p) {
        echo "<li>" . $p['libelle'] . "</li>";
    }
    echo "</ul>";

    echo "<h2>2.4 - Produits avec exactement 4 recettes</h2>";

    $filter = ['recettes' => ['$size' => 4]];

    $cursor = $collection->find($filter);

    echo "<ul>";
    foreach ($cursor as $p) {
        echo "<li>" . $p['libelle'] . "</li>";
    }
    echo "</ul>";

    echo "<h2>2.5 - Détails du produit N°6 avec recettes complètes</h2>";

    $p6 = $collection->findOne(['numero' => 6]);

    if ($p6) {
        echo "<h3>" . $p6['libelle'] . " (" . $p6['categorie'] . ")</h3>";
        
        $idsRecettes = $p6['recettes'];

        $colRecettes = $client->pizzashop->recettes;

        $cursorRecettes = $colRecettes->find([
            '_id' => ['$in' => $idsRecettes]
        ]);

        echo "<strong>Recettes associées :</strong><ul>";
        foreach ($cursorRecettes as $recette) {
            echo "<li>" . $recette['nom'] . " (Difficulté : " . $recette['difficulte'] . ")</li>";
        }
        echo "</ul>";
    } else {
        echo "Produit N°6 introuvable.";
    }
    echo "<h2>2.6 - Fonction et retour JSON</h2>";
    function getProduitData($numero, $taille, $collection) {
        $produit = $collection->findOne(['numero' => $numero]);

        if (!$produit) {
            return ["error" => "Produit introuvable"];
        }

        $prixTrouve = null;
        foreach ($produit['tarifs'] as $t) {
            if ($t['taille'] === $taille) {
                $prixTrouve = $t['tarif'];
                break;
            }
        }

        if ($prixTrouve === null) {
            return ["error" => "Taille introuvable pour ce produit"];
        }

        return [
            "numero"    => $produit['numero'],
            "libelle"   => $produit['libelle'],
            "categorie" => $produit['categorie'],
            "taille"    => $taille,
            "tarif"     => $prixTrouve
        ];
    }

    $data = getProduitData(4, "grande", $collection);

    echo "<pre>";
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    echo "</pre>";

} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
?>