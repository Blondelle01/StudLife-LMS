<?php
// 1. Gestion des sessions et des erreurs tout en haut (AVANT le HTML)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Inclusion de la configuration de la base de données
require_once 'config/db.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudLife LMS - Plateforme d'Apprentissage</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-900 font-sans antialiased text-slate-100 min-h-screen flex flex-col justify-between">

    <header class="w-full bg-slate-800/50 backdrop-blur border-b border-slate-700/50 sticky top-0 z-50 px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-600/20">
                <i class="fa-solid fa-graduation-cap text-lg"></i>
            </div>
            <span class="text-xl font-black tracking-tight text-white">StudLife <span class="text-blue-500">LMS</span></span>
        </div>
        
        <div class="flex items-center gap-4">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="dashboard_<?php echo $_SESSION['user_role']; ?>.php" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-sm transition-colors flex items-center gap-2">
                    <i class="fa-solid fa-gauge"></i> Mon Tableau de bord
                </a>
            <?php else: ?>
                <span class="text-sm text-slate-400 hidden sm:inline">Déjà inscrit ?</span>
                <a href="login.php?role=etudiant" class="px-5 py-2.5 bg-slate-700 hover:bg-slate-600 text-white font-bold rounded-xl text-sm transition-colors">
                    Connexion
                </a>
            <?php endif; ?>
        </div>
    </header>

    <main class="flex-grow flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-4xl text-center space-y-8">
            
            <div class="space-y-4">
                <span class="px-4 py-1.5 bg-blue-500/10 border border-blue-500/30 text-blue-400 rounded-full text-xs font-bold uppercase tracking-widest">
                    Université de Yaoundé 1
                </span>
                <h1 class="text-4xl sm:text-6xl font-black text-white tracking-tight leading-none">
                    Bienvenue sur <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-500">StudLife LMS</span>
                </h1>
                <p class="text-base sm:text-lg text-slate-400 max-w-2xl mx-auto">
                    Votre espace numérique d'apprentissage pour gérer vos cours, suivre vos projets et collaborer efficacement avec vos enseignants et promoteurs.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 pt-6">
                
                <a href="login.php?role=etudiant" class="group bg-slate-800/40 border border-slate-700/50 hover:border-blue-500/50 p-6 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 text-left flex flex-col justify-between space-y-4">
                    <div class="w-12 h-12 bg-blue-600/10 group-hover:bg-blue-600 text-blue-400 group-hover:text-white rounded-xl flex items-center justify-center text-xl transition-colors">
                        <i class="fa-solid fa-user-graduate"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white group-hover:text-blue-400 transition-colors">Espace Étudiant</h3>
                        <p class="text-xs text-slate-400 mt-1">Accédez à vos cours, rendus de TP et évaluations continues.</p>
                    </div>
                    <span class="text-xs font-bold text-blue-500 flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                        Entrer <i class="fa-solid fa-arrow-right"></i>
                    </span>
                </a>

                <a href="login.php?role=enseignant" class="group bg-slate-800/40 border border-slate-700/50 hover:border-indigo-500/50 p-6 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 text-left flex flex-col justify-between space-y-4">
                    <div class="w-12 h-12 bg-indigo-600/10 group-hover:bg-indigo-600 text-indigo-400 group-hover:text-white rounded-xl flex items-center justify-center text-xl transition-colors">
                        <i class="fa-solid fa-chalkboard-user"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white group-hover:text-indigo-400 transition-colors">Espace Enseignant</h3>
                        <p class="text-xs text-slate-400 mt-1">Gérez vos modules de cours, vos étudiants et publiez les notes.</p>
                    </div>
                    <span class="text-xs font-bold text-indigo-500 flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                        Entrer <i class="fa-solid fa-arrow-right"></i>
                    </span>
                </a>

                <a href="login.php?role=promoteur" class="group bg-slate-800/40 border border-slate-700/50 hover:border-emerald-500/50 p-6 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 text-left flex flex-col justify-between space-y-4">
                    <div class="w-12 h-12 bg-emerald-600/10 group-hover:bg-emerald-600 text-emerald-400 group-hover:text-white rounded-xl flex items-center justify-center text-xl transition-colors">
                        <i class="fa-solid fa-user-gear"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white group-hover:text-emerald-400 transition-colors">Espace Promoteur</h3>
                        <p class="text-xs text-slate-400 mt-1">Supervisez l'administration générale et la conformité de la plateforme.</p>
                    </div>
                    <span class="text-xs font-bold text-emerald-500 flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                        Entrer <i class="fa-solid fa-arrow-right"></i>
                    </span>
                </a>

            </div>

        </div>
    </main>

    <footer class="w-full bg-slate-950/60 border-t border-slate-800/80 py-6 text-center text-xs text-slate-500">
        <p>&copy; <?php echo date('Y'); ?> StudLife LMS. Tous droits réservés. Conçu pour le département informatique.</p>
    </footer>

</body>
</html>