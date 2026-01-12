<?php
require dirname(__DIR__) . '/src/vendor/autoload.php';

try {
    $client = new MongoDB\Client("mongodb://mongo-mongo-1:27017");
    
    $collection = $client->pizzashop->produits;
    
    $cursor = $collection->find();

    echo "<h1>Carte du PizzaShop</h1>";
    echo "<ul>";
    
    foreach ($cursor as $document) {
        echo "<li>";
        echo "<strong>" . $document['libelle'] . "</strong> ";
        echo "<em>(" . $document['categorie'] . ")</em> : ";
        echo $document['description'];
        
        if (isset($document['tarifs'][0])) {
            echo " - Prix : " . $document['tarifs'][0]['tarif'] . "€";
        }
        echo "</li>";
    }
    echo "</ul>";

} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
?>