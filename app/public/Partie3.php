<?php
require dirname(path: __DIR__) . '/src/vendor/autoload.php';

$client = new MongoDB\Client("mongodb://mongo-mongo-1:27017");
$collection = $client->pizzashop->produits;

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $newProduct = [
            'numero'      => (int)$_POST['numero'],
            'libelle'     => htmlspecialchars($_POST['libelle']),
            'categorie'   => $_POST['categorie'],
            'description' => htmlspecialchars($_POST['description']),
            'tarifs'      => [
                [
                    'taille' => $_POST['taille'],
                    'tarif'  => (float)$_POST['tarif']
                ]
            ],
            'recettes'    => []
        ];

        $collection->insertOne($newProduct);
        $message = "<div class='success'>Produit ajouté avec succès !</div>";
    } catch (Exception $e) {
        $message = "<div class='error'>Erreur : " . $e->getMessage() . "</div>";
    }
}



$categories = $collection->distinct('categorie');

$filter = [];
$currentCat = "Tout";

if (isset($_GET['cat']) && !empty($_GET['cat'])) {
    $filter = ['categorie' => $_GET['cat']];
    $currentCat = $_GET['cat'];
}

$cursor = $collection->find($filter, ['sort' => ['numero' => 1]]);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>PizzaShop App</title>
    <style>
        body { font-family: sans-serif; max-width: 900px; margin: 0 auto; padding: 20px; background: #f4f4f4; }
        h1, h2 { color: #333; }
        
        .menu { margin-bottom: 20px; }
        .btn { display: inline-block; padding: 8px 15px; margin-right: 5px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; }
        .btn:hover, .btn.active { background: #0056b3; }
        
        .product-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; }
        .card { background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .card h3 { margin-top: 0; color: #d32f2f; }
        .badge { background: #eee; padding: 3px 8px; border-radius: 10px; font-size: 0.8em; }
        
        .form-box { background: #fff; padding: 20px; margin-top: 40px; border-left: 5px solid #28a745; }
        .form-group { margin-bottom: 10px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input, select, textarea { width: 100%; padding: 8px; box-sizing: border-box; }
        button { background: #28a745; color: white; border: none; padding: 10px 20px; cursor: pointer; font-size: 16px; }
        
        .success { background: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 15px; }
    </style>
</head>
<body>

    <h1>🍕 PizzaShop Catalogue</h1>

    <?= $message ?>

    <div class="menu">
        <strong>Filtrer par : </strong>
        <a href="Partie3.php" class="btn <?= $currentCat == 'Tout' ? 'active' : '' ?>">Tout voir</a>
        <?php foreach ($categories as $cat): ?>
            <a href="Partie3.php?cat=<?= urlencode($cat) ?>" class="btn <?= $currentCat == $cat ? 'active' : '' ?>">
                <?= $cat ?>
            </a>
        <?php endforeach; ?>
    </div>

    <hr>

    <h2>Liste des produits : <?= htmlspecialchars($currentCat) ?></h2>
    <div class="product-list">
        <?php foreach ($cursor as $p): ?>
            <div class="card">
                <h3>#<?= $p['numero'] ?> - <?= $p['libelle'] ?></h3>
                <span class="badge"><?= $p['categorie'] ?></span>
                <p><em><?= $p['description'] ?? 'Pas de description' ?></em></p>
                
                <hr>
                <strong>Tarifs :</strong>
                <ul>
                    <?php if (isset($p['tarifs'])): ?>
                        <?php foreach ($p['tarifs'] as $t): ?>
                            <li><?= $t['taille'] ?> : <strong><?= $t['tarif'] ?> €</strong></li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li>Aucun tarif</li>
                    <?php endif; ?>
                </ul>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="form-box">
        <h2>➕ Ajouter un nouveau produit</h2>
        <form method="POST" action="Partie3.php">
            <div class="form-group">
                <label>Numéro :</label>
                <input type="number" name="numero" required placeholder="Ex: 50">
            </div>

            <div class="form-group">
                <label>Libellé :</label>
                <input type="text" name="libelle" required placeholder="Ex: Pizza 4 Fromages">
            </div>

            <div class="form-group">
                <label>Catégorie :</label>
                <select name="categorie">
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat ?>"><?= $cat ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Description :</label>
                <textarea name="description" rows="3" placeholder="Ingrédients..."></textarea>
            </div>

            <div style="background: #f9f9f9; padding: 10px; border: 1px dashed #ccc;">
                <strong>Tarif initial :</strong>
                <div class="form-group">
                    <label>Taille :</label>
                    <select name="taille">
                        <option value="normale">Normale</option>
                        <option value="grande">Grande</option>
                        <option value="xxl">XXL</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Prix (€) :</label>
                    <input type="number" step="0.01" name="tarif" required placeholder="Ex: 12.50">
                </div>
            </div>
            <br>
            <button type="submit">Enregistrer le produit</button>
        </form>
    </div>

</body>
</html>