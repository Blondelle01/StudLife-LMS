<?php
// 1. Activation des erreurs pour éviter la page blanche en cas de problème
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'config/db.php';

// Sécurité : Si l'utilisateur n'est pas connecté ou n'est pas promoteur, retour à la connexion
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'promoteur') {
    header("Location: index.php");
    exit;
}

$message = "";
$erreur = "";

// Récupération du message de succès après redirection
if (isset($_GET['statut']) && $_GET['statut'] === 'succes') {
    $message = "Le module a été créé avec succès !";
}

// Traitement du formulaire d'ajout de module
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['creer_module'])) {
    $titre = trim(htmlentities($_POST['titre'] ?? ''));
    $description = trim(htmlentities($_POST['description'] ?? ''));
    $promoteur_id = $_SESSION['user_id'];

    if (!empty($titre)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO modules (titre, description, promoteur_id) VALUES (?, ?, ?)");
            $stmt->execute([$titre, $description, $promoteur_id]);
            
            // Redirection pour éviter le renvoi multiple du formulaire
            header("Location: dashboard_promoteur.php?statut=succes");
            exit;
            
        } catch (PDOException $e) {
            $erreur = "Erreur lors de la création du module : " . $e->getMessage();
        }
    } else {
        $erreur = "Le titre du module est obligatoire.";
    }
}

// Récupération de tous les modules pour les afficher
try {
    $stmt = $pdo->query("SELECT m.*, u.nom AS createur FROM modules m JOIN utilisateurs u ON m.promoteur_id = u.id ORDER BY m.id DESC");
    $modules = $stmt->fetchAll();
} catch (PDOException $e) {
    // Si la table modules n'existe pas encore ou est mal configurée
    $erreur = "Erreur de récupération des modules : " . $e->getMessage();
    $modules = [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Promoteur - LMS</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', sans-serif; }
        body { background: #f4f6f9; display: flex; min-height: 100vh; }
        
        /* Barre latérale */
        .sidebar { width: 260px; background: #1e293b; color: white; padding: 20px; flex-shrink: 0; }
        .sidebar h3 { margin-bottom: 30px; font-size: 20px; text-align: center; color: #3b82f6; }
        .sidebar a { display: block; color: #cbd5e1; padding: 12px; text-decoration: none; border-radius: 6px; margin-bottom: 10px; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background: #3b82f6; color: white; }
        .logout { background: #ef4444 !important; margin-top: 50px; text-align: center; display: block; }

        /* Contenu Principal */
        .main-content { flex-grow: 1; padding: 40px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 2px solid #e2e8f0; padding-bottom: 15px; }
        .header h1 { color: #1e293b; }
        .user-info { font-weight: bold; color: #475569; }

        /* Formulaire et Cartes */
        .grid { display: grid; grid-template-columns: 1fr 2fr; gap: 30px; }
        .card { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .card h2 { margin-bottom: 20px; color: #1e293b; font-size: 18px; }
        
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; color: #475569; font-size: 14px; }
        input, textarea { width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 15px; outline: none; }
        input:focus, textarea:focus { border-color: #3b82f6; }
        button { width: 100%; padding: 12px; background: #10b981; color: white; border: none; border-radius: 6px; font-size: 16px; cursor: pointer; font-weight: bold; transition: 0.3s; }
        button:hover { background: #059669; }

        /* Liste des modules */
        .module-item { background: #f8fafc; border: 1px solid #e2e8f0; padding: 15px; border-radius: 6px; margin-bottom: 15px; }
        .module-item h4 { color: #1e293b; font-size: 16px; margin-bottom: 5px; }
        .module-item p { color: #64748b; font-size: 14px; }

        .alert { padding: 12px; border-radius: 6px; margin-bottom: 20px; font-size: 14px; text-align: center; }
        .success { background: #d1fae5; color: #065f46; }
        .danger { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h3>LMS Administration</h3>
        <a href="#" class="active">Gestion des Modules</a>
        <a href="index.php" class="logout">Déconnexion</a>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Espace Promoteur</h1>
            <div class="user-info">Bienvenue, <?php echo $_SESSION['user_nom'] ?? 'Promoteur'; ?></div>
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert success"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if (!empty($erreur)): ?>
            <div class="alert danger"><?php echo $erreur; ?></div>
        <?php endif; ?>

        <div class="grid">
            <div class="card">
                <h2>Créer un nouveau module</h2>
                <form action="dashboard_promoteur.php" method="POST">
                    <div class="form-group">
                        <label for="titre">Titre du module</label>
                        <input type="text" id="titre" name="titre" required placeholder="Ex: Algorithmique Avancée">
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="4" placeholder="Objectifs et compétences du module..."></textarea>
                    </div>
                    <button type="submit" name="creer_module">Créer le Module</button>
                </form>
            </div>

            <div class="card">
                <h2>Modules de cours existants</h2>
                <?php if (empty($modules)): ?>
                    <p style="color: #64748b;">Aucun module n'a encore été créé ou une table est manquante.</p>
                <?php else: ?>
                    <?php foreach ($modules as $module): ?>
                        <div class="module-item">
                            <h4><?php echo $module['titre']; ?></h4>
                            <p><?php echo nl2br($module['description']); ?></p>
                            <small style="color: #94a3b8; display: block; margin-top: 5px;">Créé par : <?php echo $module['createur']; ?></small>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>
</html>