
<?php
// Force l'affichage des erreurs cachées de PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Démarrage de session sécurisé
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudLife LMS - Accueil</title>
    
    <link rel="icon" href="data:,">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        .hero-gradient {
            background: radial-gradient(circle at top right, #3b82f6 0%, #1e293b 60%, #0f172a 100%);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(8px);
        }
    </style>
</head>
<body class="bg-slate-50 font-sans antialiased text-slate-900 min-h-screen flex flex-col justify-between">

    <header class="hero-gradient text-white pt-20 pb-32 px-6 text-center relative overflow-hidden">
        <div class="absolute top-0 left-0 w-96 h-96 bg-blue-500/10 rounded-full -translate-x-1/2 -translate-y-1/2 blur-3xl"></div>
        <div class="absolute bottom-0 right-0 w-80 h-80 bg-indigo-500/10 rounded-full translate-x-1/3 translate-y-1/3 blur-3xl"></div>

        <div class="max-w-4xl mx-auto relative z-10">
            <div class="inline-flex items-center gap-2 mb-5 bg-white/10 px-4 py-1.5 rounded-full border border-white/10 shadow-inner">
                <i class="fa-solid fa-graduation-cap text-blue-400 text-sm"></i>
                <span class="text-xs font-bold uppercase tracking-wider text-blue-200">L'éducation à portée de main</span>
            </div>
            <h1 class="text-4xl md:text-5xl font-black mb-5 tracking-tight">
                Bienvenue sur <span class="text-blue-400">StudLife LMS</span>
            </h1>
            <p class="text-base md:text-lg text-slate-300 max-w-xl mx-auto leading-relaxed font-medium">
                Votre portail académique connecté. Veuillez sélectionner votre espace de travail ci-dessous pour commencer.
            </p>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-6 -mt-16 mb-16 w-full relative z-30">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            
            <div class="glass-card p-8 rounded-3xl shadow-xl border border-white/60 flex flex-col items-center text-center group hover:-translate-y-2 transition-all duration-300">
                <div class="w-16 h-16 bg-slate-900 rounded-2xl flex items-center justify-center text-white mb-6 shadow-md group-hover:bg-blue-600 transition-colors">
                    <i class="fa-solid fa-user-gear text-2xl"></i>
                </div>
                <h3 class="text-xl font-extrabold mb-3 text-slate-800">Espace Promoteur</h3>
                <p class="text-slate-500 text-sm mb-8 leading-relaxed max-w-xs">
                    Gérez l'infrastructure globale de l'application et configurez les modules de formation.
                </p>
                <a href="login.php?role=promoteur" class="w-full py-3.5 bg-slate-900 text-white rounded-xl font-bold hover:bg-blue-600 transition-colors shadow-lg tracking-wide text-sm mt-auto block text-center">
                    Se connecter
                </a>
            </div>

            <div class="glass-card p-8 rounded-3xl shadow-xl border border-white/60 flex flex-col items-center text-center group hover:-translate-y-2 transition-all duration-300">
                <div class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center text-white mb-6 shadow-md group-hover:scale-105 transition-transform">
                    <i class="fa-solid fa-chalkboard-user text-2xl"></i>
                </div>
                <h3 class="text-xl font-extrabold mb-3 text-slate-800">Espace Enseignant</h3>
                <p class="text-slate-500 text-sm mb-8 leading-relaxed max-w-xs">
                    Créez vos cours, déposez vos leçons (PDF/Vidéo) et ajoutez des évaluations.
                </p>
                <a href="login.php?role=enseignant" class="w-full py-3.5 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition-colors shadow-lg tracking-wide text-sm mt-auto block text-center">
                    Se connecter
                </a>
            </div>

            <div class="glass-card p-8 rounded-3xl shadow-xl border border-white/60 flex flex-col items-center text-center group hover:-translate-y-2 transition-all duration-300">
                <div class="w-16 h-16 bg-indigo-600 rounded-2xl flex items-center justify-center text-white mb-6 shadow-md group-hover:scale-105 transition-transform">
                    <i class="fa-solid fa-user-graduate text-2xl"></i>
                </div>
                <h3 class="text-xl font-extrabold mb-3 text-slate-800">Espace Étudiant</h3>
                <p class="text-slate-500 text-sm mb-8 leading-relaxed max-w-xs">
                    Consultez vos leçons de manière fluide et validez vos compétences via les QCM interactifs.
                </p>
                <a href="login.php?role=etudiant" class="w-full py-3.5 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 transition-colors shadow-lg tracking-wide text-sm mt-auto block text-center">
                    Se connecter
                </a>
            </div>

        </div>
    </main>

    <footer class="py-8 bg-white border-t border-slate-200 text-center w-full">
        <p class="text-slate-400 text-xs font-semibold">
            &copy; 2026 StudLife LMS &middot; Développé par <span class="text-slate-600 border-b border-blue-400/50 pb-0.5">Blondelle</span>
        </p>
    </footer>

</body>
</html>