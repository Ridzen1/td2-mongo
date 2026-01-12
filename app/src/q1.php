<?php
require 'vendor/autoload.php';

try {

    $client = new MongoDB\Client("mongodb://mongo-mongo-1:27017");

    $collection = $client->chopizza->produits;

    $cursor = $collection->find(
        [],
        [
            'projection' => [
                'numero' => 1,
                'categorie' => 1,
                'libelle' => 1,
                '_id' => 0
            ],
            'sort' => ['numero' => 1]
        ]
    );

    echo "<h1>Liste des Produits</h1>";
    echo "<ul>";
    
    foreach ($cursor as $document) {
        echo "<li>";
        echo "<strong>N°" . $document['numero'] . "</strong> - ";
        echo "[" . $document['categorie'] . "] ";
        echo $document['libelle'];
        echo "</li>";
    }
    echo "</ul>";

} catch (Exception $e) {
    echo "Erreur de connexion : " . $e->getMessage();
}
?>