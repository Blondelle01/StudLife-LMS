<?php
session_start();
require_once 'config/db.php';

// Sécurité : Si l'utilisateur n'est pas connecté ou n'est pas enseignant, retour à la connexion
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'enseignant') {
    header("Location: index.php");
    exit;
}

$message = "";
$erreur = "";

// Récupération des messages après redirection
if (isset($_GET['statut'])) {
    if ($_GET['statut'] === 'cours_succes') $message = "Le cours a été créé avec succès !";
    if ($_GET['statut'] === 'lecon_succes') $message = "La leçon et son fichier ont été ajoutés avec succès !";
    if ($_GET['statut'] === 'eval_succes') $message = "L'évaluation a été rattachée à la leçon avec succès !";
}

// 1. TRAITEMENT : Création d'un cours
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['creer_cours'])) {
    $module_id = $_POST['module_id'] ?? '';
    $titre = trim(htmlentities($_POST['titre'] ?? ''));
    $description = trim(htmlentities($_POST['description'] ?? ''));
    $enseignant_id = $_SESSION['user_id'];

    if (!empty($module_id) && !empty($titre)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO cours (module_id, enseignant_id, titre, description) VALUES (?, ?, ?, ?)");
            $stmt->execute([$module_id, $enseignant_id, $titre, $description]);
            header("Location: dashboard_enseignant.php?statut=cours_succes");
            exit;
        } catch (PDOException $e) {
            $erreur = "Erreur lors de la création du cours : " . $e->getMessage();
        }
    } else {
        $erreur = "Veuillez remplir tous les champs obligatoires pour le cours.";
    }
}

// 2. TRAITEMENT : Ajout d'une leçon avec upload de fichier (CORRIGÉ)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ajouter_lecon'])) {
    $cours_id = $_POST['cours_id'] ?? '';
    $titre_lecon = trim(htmlentities($_POST['titre_lecon'] ?? ''));
    $type_contenu = $_POST['type_contenu'] ?? '';
    $ordre = intval($_POST['ordre'] ?? 1);

    if (!empty($cours_id) && !empty($titre_lecon) && isset($_FILES['fichier_cours'])) {
        $file = $_FILES['fichier_cours'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        // Vérification des extensions autorisées
        $extensions_autorisees = ($type_contenu === 'pdf') ? ['pdf'] : ['mp4', 'webm'];

        if (in_array($ext, $extensions_autorisees)) {
            if ($file['error'] === 0) {
                // Création d'un nom de fichier unique
                $nom_fichier_unique = uniqid('lms_', true) . '.' . $ext;
                $destination = 'assets/uploads/' . $nom_fichier_unique;

                // CORRECTION : move_uploaded_file (sans S)
                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    try {
                        $stmt = $pdo->prepare("INSERT INTO lecons (cours_id, titre, type_contenu, fichier_url, ordre) VALUES (?, ?, ?, ?, ?)");
                        $stmt->execute([$cours_id, $titre_lecon, $type_contenu, $destination, $ordre]);
                        header("Location: dashboard_enseignant.php?statut=lecon_succes");
                        exit;
                    } catch (PDOException $e) {
                        $erreur = "Erreur BDD : " . $e->getMessage();
                    }
                } else {
                    $erreur = "Le déplacement du fichier a échoué. Vérifie les permissions du dossier assets/uploads.";
                }
            } else {
                $erreur = "Erreur lors du téléchargement du fichier (Code erreur PHP : " . $file['error'] . ").";
            }
        } else {
            $erreur = "Extension de fichier non autorisée pour le type de support sélectionné.";
        }
    } else {
        $erreur = "Veuillez remplir tous les champs obligatoires pour la leçon.";
    }
}

// 3. TRAITEMENT : Création d'une évaluation (QCM)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['creer_evaluation'])) {
    $lecon_id = $_POST['lecon_id'] ?? '';
    $question = trim(htmlentities($_POST['question'] ?? ''));
    $choix_a = trim(htmlentities($_POST['choix_a'] ?? ''));
    $choix_b = trim(htmlentities($_POST['choix_b'] ?? ''));
    $choix_c = trim(htmlentities($_POST['choix_c'] ?? ''));
    $reponse_correcte = $_POST['reponse_correcte'] ?? '';

    if (!empty($lecon_id) && !empty($question) && !empty($choix_a) && !empty($choix_b) && !empty($choix_c) && !empty($reponse_correcte)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO evaluations (lecon_id, question, choix_a, choix_b, choix_c, reponse_correcte) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$lecon_id, $question, $choix_a, $choix_b, $choix_c, $reponse_correcte]);
            header("Location: dashboard_enseignant.php?statut=eval_succes");
            exit;
        } catch (PDOException $e) {
            $erreur = "Erreur lors de la création de l'évaluation : " . $e->getMessage();
        }
    } else {
        $erreur = "Tous les champs de l'évaluation sont obligatoires.";
    }
}

// Récupération des données pour alimenter l'interface
$modules = $pdo->query("SELECT * FROM modules ORDER BY titre ASC")->fetchAll();

$stmt_cours = $pdo->prepare("SELECT c.*, m.titre AS module_titre FROM cours c JOIN modules m ON c.module_id = m.id WHERE c.enseignant_id = ? ORDER BY c.id DESC");
$stmt_cours->execute([$_SESSION['user_id']]);
$mes_cours = $stmt_cours->fetchAll();

$stmt_lecons = $pdo->prepare("SELECT l.*, c.titre AS cours_titre FROM lecons l JOIN cours c ON l.cours_id = c.id WHERE c.enseignant_id = ? ORDER BY l.id DESC");
$stmt_lecons->execute([$_SESSION['user_id']]);
$mes_lecons = $stmt_lecons->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Enseignant - LMS</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', sans-serif; }
        body { background: #f4f6f9; display: flex; min-height: 100vh; }
        .sidebar { width: 260px; background: #0f172a; color: white; padding: 20px; flex-shrink: 0; }
        .sidebar h3 { margin-bottom: 30px; font-size: 20px; text-align: center; color: #3b82f6; }
        .sidebar a { display: block; color: #cbd5e1; padding: 12px; text-decoration: none; border-radius: 6px; margin-bottom: 10px; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background: #3b82f6; color: white; }
        .logout { background: #ef4444 !important; margin-top: 50px; text-align: center; display: block; }
        
        .main-content { flex-grow: 1; padding: 40px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 2px solid #e2e8f0; padding-bottom: 15px; }
        .header h1 { color: #1e293b; }
        
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; }
        .card { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 30px; }
        .card h2 { margin-bottom: 20px; color: #1e293b; font-size: 18px; border-left: 4px solid #3b82f6; padding-left: 10px; }
        
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; color: #475569; font-size: 14px; }
        input, select, textarea { width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 15px; }
        button { width: 100%; padding: 12px; background: #3b82f6; color: white; border: none; border-radius: 6px; font-size: 16px; cursor: pointer; font-weight: bold; transition: 0.3s; }
        button:hover { background: #2563eb; }
        
        .alert { padding: 12px; border-radius: 6px; margin-bottom: 20px; font-size: 14px; text-align: center; }
        .success { background: #d1fae5; color: #065f46; }
        .danger { background: #fee2e2; color: #991b1b; }
        
        .cours-item { background: #f8fafc; padding: 15px; border: 1px solid #e2e8f0; border-radius: 6px; margin-bottom: 10px; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h3>LMS Enseignant</h3>
        <a href="#" class="active">Mes Cours & Leçons</a>
        <a href="index.php" class="logout">Déconnexion</a>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Espace Enseignant</h1>
            <div>Bienvenue, Prof. <?php echo $_SESSION['user_nom'] ?? 'Enseignant'; ?></div>
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert success"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if (!empty($erreur)): ?>
            <div class="alert danger"><?php echo $erreur; ?></div>
        <?php endif; ?>

        <div class="grid">
            <div>
                <div class="card">
                    <h2>1. Créer un nouveau cours</h2>
                    <form action="dashboard_enseignant.php" method="POST">
                        <div class="form-group">
                            <label>Sélectionner le Module</label>
                            <select name="module_id" required>
                                <option value="">-- Choisir un module --</option>
                                <?php foreach ($modules as $m): ?>
                                    <option value="<?php echo $m['id']; ?>"><?php echo $m['titre']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Titre du cours</label>
                            <input type="text" name="titre" required placeholder="Ex: Introduction au PHP">
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" rows="2" placeholder="Résumé..."></textarea>
                        </div>
                        <button type="submit" name="creer_cours">Créer le cours</button>
                    </form>
                </div>

                <div class="card">
                    <h2>2. Ajouter une leçon</h2>
                    <form action="dashboard_enseignant.php" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label>Choisir votre cours</label>
                            <select name="cours_id" required>
                                <option value="">-- Choisir un cours --</option>
                                <?php foreach ($mes_cours as $c): ?>
                                    <option value="<?php echo $c['id']; ?>"><?php echo $c['titre']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Titre de la leçon</label>
                            <input type="text" name="titre_lecon" required placeholder="Ex: Chapitre 1 : Les Variables">
                        </div>
                        <div class="form-group">
                            <label>Type de support</label>
                            <select name="type_contenu" required>
                                <option value="pdf">Document PDF</option>
                                <option value="video">Fichier Vidéo (MP4)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Fichier (PDF ou MP4)</label>
                            <input type="file" name="fichier_cours" required accept=".pdf,.mp4">
                        </div>
                        <button type="submit" name="ajouter_lecon" style="background: #10b981;">Ajouter la leçon</button>
                    </form>
                </div>
            </div>

            <div>
                <div class="card">
                    <h2>3. Associer une évaluation à une leçon</h2>
                    <form action="dashboard_enseignant.php" method="POST">
                        <div class="form-group">
                            <label>Sélectionner la leçon concernée</label>
                            <select name="lecon_id" required>
                                <option value="">-- Choisir une leçon --</option>
                                <?php foreach ($mes_lecons as $l): ?>
                                    <option value="<?php echo $l['id']; ?>"><?php echo $l['titre']; ?> (<?php echo $l['cours_titre']; ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Question posée</label>
                            <input type="text" name="question" required placeholder="Ex: Que signifie PHP ?">
                        </div>
                        <div class="form-group">
                            <label>Option A</label>
                            <input type="text" name="choix_a" required placeholder="Option A">
                        </div>
                        <div class="form-group">
                            <label>Option B</label>
                            <input type="text" name="choix_b" required placeholder="Option B">
                        </div>
                        <div class="form-group">
                            <label>Option C</label>
                            <input type="text" name="choix_c" required placeholder="Option C">
                        </div>
                        <div class="form-group">
                            <label>Quelle est la bonne réponse ?</label>
                            <select name="reponse_correcte" required>
                                <option value="A">Option A</option>
                                <option value="B">Option B</option>
                                <option value="C">Option C</option>
                            </select>
                        </div>
                        <button type="submit" name="creer_evaluation" style="background: #f59e0b;">Ajouter l'évaluation</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>
</html>