<?php
// 1. Gestion des erreurs et sessions
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/db.php';

// 2. Détection du rôle (par défaut 'etudiant' si non spécifié)
$role = isset($_GET['role']) ? $_GET['role'] : 'etudiant';

// Traduction du rôle pour l'affichage
$role_titre = "Étudiant";
if ($role === 'promoteur') $role_titre = "Promoteur";
if ($role === 'enseignant') $role_titre = "Enseignant";

$erreur = "";

// 3. Traitement du formulaire lors du clic sur "Se connecter"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    
    if (!empty($email) && !empty($password)) {
        try {
            // Requête pour vérifier l'utilisateur et son rôle
            $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ? AND role = ?");
            $stmt->execute([$email, $role]);
            $user = $stmt->fetch();
            
            // CORRECTION ICI : Utilisation de 'mot_de_passe' avec un 'e'
            if ($user && $password === $user['mot_de_passe']) {
                // Stockage des informations en session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_nom'] = $user['nom'];
                $_SESSION['user_role'] = $user['role'];
                
                // Redirection automatique vers le bon dashboard
                header("Location: dashboard_{$role}.php");
                exit;
            } else {
                $erreur = "Identifiants incorrects pour l'espace " . $role_titre . ".";
            }
        } catch (PDOException $e) {
            $erreur = "Erreur de connexion à la base de données : " . $e->getMessage();
        }
    } else {
        $erreur = "Veuillez remplir tous les champs.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Espace <?php echo $role_titre; ?></title>
    <link rel="icon" href="data:,">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-900 font-sans antialiased text-slate-100 min-h-screen flex items-center justify-center px-4">

    <div class="w-full max-w-md bg-slate-800 border border-slate-700/50 p-8 rounded-3xl shadow-2xl relative">
        
        <a href="index.php" class="absolute top-6 left-6 text-slate-400 hover:text-white text-sm font-medium flex items-center gap-2 transition-colors">
            <i class="fa-solid fa-arrow-left"></i> Retour
        </a>

        <div class="text-center mt-4 mb-8">
            <div class="w-14 h-14 bg-blue-600 rounded-2xl flex items-center justify-center text-white mx-auto mb-4 shadow-lg shadow-blue-600/20">
                <?php if($role === 'promoteur'): ?>
                    <i class="fa-solid fa-user-gear text-xl"></i>
                <?php elseif($role === 'enseignant'): ?>
                    <i class="fa-solid fa-chalkboard-user text-xl"></i>
                <?php else: ?>
                    <i class="fa-solid fa-user-graduate text-xl"></i>
                <?php endif; ?>
            </div>
            <h2 class="text-2xl font-black text-white">Connexion</h2>
            <p class="text-sm text-slate-400 mt-1">Accès sécurisé à l'<b>Espace <?php echo $role_titre; ?></b></p>
        </div>

        <?php if (!empty($erreur)): ?>
            <div class="bg-red-500/10 border border-red-500/20 text-red-400 p-4 rounded-xl text-sm font-bold mb-6 flex items-center gap-3">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span><?php echo $erreur; ?></span>
            </div>
        <?php endif; ?>

        <form action="login.php?role=<?php echo $role; ?>" method="POST" class="space-y-5">
            <div>
                <label class="block text-xs font-bold uppercase text-slate-400 tracking-wider mb-2">Adresse Email</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-500">
                        <i class="fa-solid fa-envelope"></i>
                    </span>
                    <input type="email" name="email" required placeholder="exemple@lms.com" 
                           class="w-full bg-slate-900/50 border border-slate-700 focus:border-blue-500 text-white rounded-xl pl-11 pr-4 py-3.5 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 transition-all">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-400 tracking-wider mb-2">Mot de passe</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-500">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <input type="password" name="password" required placeholder="••••••••" 
                           class="w-full bg-slate-900/50 border border-slate-700 focus:border-blue-500 text-white rounded-xl pl-11 pr-4 py-3.5 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 transition-all">
                </div>
            </div>

            <button type="submit" class="w-full py-4 bg-blue-600 text-white font-bold rounded-xl text-sm hover:bg-blue-700 transition-colors shadow-lg shadow-blue-600/10 cursor-pointer mt-2">
                Se connecter
            </button>
        </form>

    </div>

</body>
</html>