<?php
session_start();
require_once 'config/db.php';

// Sécurité : Si l'utilisateur n'est pas connecté ou n'est pas étudiant, retour à la connexion
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'etudiant') {
    header("Location: index.php");
    exit;
}

// Récupération des modules pour la barre latérale gauche
try {
    $stmt = $pdo->query("SELECT * FROM modules ORDER BY titre ASC");
    $modules = $stmt->fetchAll();
} catch (PDOException $e) {
    $modules = [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Étudiant - LMS</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', sans-serif; }
        body { background: #f4f6f9; display: flex; min-height: 100vh; }
        
        /* Barre latérale gauche */
        .sidebar { width: 280px; background: #1e293b; color: white; padding: 20px; flex-shrink: 0; display: flex; flex-direction: column; }
        .sidebar h3 { margin-bottom: 25px; font-size: 18px; color: #3b82f6; text-transform: uppercase; letter-spacing: 1px; text-align: center; }
        .sidebar h4 { margin: 15px 0 10px 0; color: #94a3b8; font-size: 13px; text-transform: uppercase; }
        .module-link { display: block; color: #cbd5e1; padding: 12px; text-decoration: none; border-radius: 6px; margin-bottom: 8px; background: #334155; transition: 0.3s; font-size: 14px; }
        .module-link:hover, .module-link.active { background: #3b82f6; color: white; }
        .logout { background: #ef4444 !important; color: white !important; margin-top: auto; text-align: center; font-weight: bold; }

        /* Colonne centrale : Liste des leçons */
        .lessons-column { width: 320px; background: white; border-right: 1px solid #e2e8f0; padding: 20px; overflow-y: auto; flex-shrink: 0; }
        .lessons-column h3 { color: #1e293b; margin-bottom: 20px; font-size: 16px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px; }
        .lesson-item { padding: 15px; border: 1px solid #e2e8f0; border-radius: 6px; margin-bottom: 12px; cursor: pointer; transition: 0.3s; background: #f8fafc; }
        .lesson-item:hover { border-color: #3b82f6; background: #f0f7ff; }
        .lesson-item h5 { color: #1e293b; font-size: 14px; margin-bottom: 4px; }
        .lesson-item span { font-size: 12px; color: #64748b; }

        /* Colonne de droite : Lecteur de contenu de la leçon */
        .content-column { flex-grow: 1; padding: 40px; overflow-y: auto; background: #f8fafc; }
        .content-card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); min-height: 400px; display: flex; flex-direction: column; }
        .content-card h2 { color: #1e293b; margin-bottom: 10px; font-size: 22px; }
        .content-card p.context { color: #64748b; font-size: 14px; margin-bottom: 20px; }
        
        /* Lecteurs Média */
        .media-container { width: 100%; height: 500px; border-radius: 6px; overflow: hidden; background: #000; margin-bottom: 30px; border: 1px solid #e2e8f0; }
        iframe { width: 100%; height: 100%; border: none; }
        video { width: 100%; height: 100%; object-fit: contain; }

        /* Espace Évaluation / QCM */
        .eval-section { border-top: 2px dashed #e2e8f0; padding-top: 25px; margin-top: 20px; }
        .eval-section h4 { color: #1e293b; margin-bottom: 15px; font-size: 16px; display: flex; align-items: center; gap: 8px; }
        .qcm-box { background: #f1f5f9; padding: 20px; border-radius: 6px; }
        .qcm-option { display: block; background: white; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 6px; margin-bottom: 10px; cursor: pointer; font-size: 14px; transition: 0.2s; }
        .qcm-option:hover { border-color: #3b82f6; background: #f0f7ff; }
        .qcm-option input { margin-right: 10px; }
        
        .eval-btn { padding: 10px 20px; background: #3b82f6; color: white; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; margin-top: 5px; transition: 0.3s; }
        .eval-btn:hover { background: #2563eb; }
        .feedback-msg { margin-top: 15px; padding: 12px; border-radius: 6px; font-size: 14px; font-weight: bold; display: none; text-align: center; }

        .empty-state { color: #64748b; text-align: center; margin-top: 100px; font-style: italic; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h3>Espace Apprentissage</h3>
        <center><small style="color:#94a3b8;">Étudiant : <?php echo $_SESSION['user_nom']; ?></small></center>
        
        <h4>Mes Modules</h4>
        <?php if (empty($modules)): ?>
            <p style="font-size:12px; color:#64748b;">Aucun module disponible.</p>
        <?php else: ?>
            <?php foreach ($modules as $mod): ?>
                <a href="#" class="module-link" onclick="chargerModule(this, <?php echo $mod['id']; ?>)">📁 <?php echo $mod['titre']; ?></a>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <a href="index.php" class="logout">🚪 Déconnexion</a>
    </div>

    <div class="lessons-column">
        <h3>Leçons disponibles</h3>
        <div id="lessons-list">
            <p class="empty-state">Sélectionnez un module dans la barre latérale pour afficher ses leçons.</p>
        </div>
    </div>

    <div class="content-column">
        <div class="content-card" id="content-area">
            <div class="empty-state">
                <span style="font-size: 48px;">📖</span>
                <p style="margin-top:15px;">Sélectionnez une leçon pour afficher son contenu et son évaluation.</p>
            </div>
        </div>
    </div>

    <script>
        let leconsGlobales = []; // Stockage local des leçons chargées

        // FONCTION AJAX : Chargement des leçons du module cliqué
        function chargerModule(element, moduleId) {
            // Gestion visuelle de la sélection active dans la sidebar
            document.querySelectorAll('.module-link').forEach(link => link.classList.remove('active'));
            element.classList.add('active');

            const listContainer = document.getElementById('lessons-list');
            listContainer.innerHTML = '<p class="empty-state">Chargement en cours...</p>';
            document.getElementById('content-area').innerHTML = '<div class="empty-state"><span style="font-size: 48px;">📖</span><p style="margin-top:15px;">Sélectionnez une leçon pour afficher son contenu et son évaluation.</p></div>';

            // CORRECTION : fetch() au lieu de Fetchfetch()
            fetch(`api/charger_contenu.php?module_id=${moduleId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.succes) {
                        leconsGlobales = data.lecons; // Sauvegarde dans la variable globale
                        
                        if (leconsGlobales.length === 0) {
                            listContainer.innerHTML = '<p class="empty-state">Aucune leçon n\'est encore disponible dans ce module.</p>';
                            return;
                        }

                        // Construction du HTML de la liste de gauche
                        listContainer.innerHTML = '';
                        leconsGlobales.forEach((lecon, index) => {
                            const typeBadge = lecon.type_contenu === 'pdf' ? '📄 PDF' : '🎥 Vidéo';
                            listContainer.innerHTML += `
                                <div class="lesson-item" onclick="afficherLecon(${index})">
                                    <h5>${lecon.lecon_titre}</h5>
                                    <span>Cours : ${lecon.cours_titre}</span><br>
                                    <small style="color:#3b82f6; font-weight:bold;">${typeBadge}</small>
                                </div>
                            `;
                        });
                    } else {
                        listContainer.innerHTML = `<p class="empty-state" style="color:#ef4444;">Erreur : ${data.erreur}</p>`;
                    }
                })
                .catch(error => {
                    console.error("Erreur Fetch :", error);
                    listContainer.innerHTML = '<p class="empty-state" style="color:#ef4444;">Erreur réseau lors du chargement.</p>';
                });
        }

        // FONCTION : Affichage du support de cours et traitement dynamique du QCM à droite
        function afficherLecon(index) {
            const lecon = leconsGlobales[index];
            const contentArea = document.getElementById('content-area');

            // 1. Choix du bon lecteur média (Lecteur PDF embarqué ou balise Vidéo HTML5)
            let lecteurHTML = '';
            if (lecon.type_contenu === 'pdf') {
                lecteurHTML = `<div class="media-container"><iframe src="${lecon.fichier_url}"></iframe></div>`;
            } else {
                lecteurHTML = `<div class="media-container"><video src="${lecon.fichier_url}" controls></video></div>`;
            }

            // 2. Intégration du bloc évaluation si le prof a configuré un QCM
            let evalHTML = '';
            if (lecon.evaluation) {
                const q = lecon.evaluation;
                evalHTML = `
                    <div class="eval-section">
                        <h4>📝 Évaluation de la leçon</h4>
                        <div class="qcm-box">
                            <p style="font-weight:600; color:#1e293b; margin-bottom:15px;">${q.question}</p>
                            
                            <label class="qcm-option"><input type="radio" name="qcm_choice" value="A"> <b>A:</b> ${q.choix_a}</label>
                            <label class="qcm-option"><input type="radio" name="qcm_choice" value="B"> <b>B:</b> ${q.choix_b}</label>
                            <label class="qcm-option"><input type="radio" name="qcm_choice" value="C"> <b>C:</b> ${q.choix_c}</label>
                            
                            <button class="eval-btn" onclick="verifierReponse('${q.reponse_correcte}')">Soumettre ma réponse</button>
                            <div id="feedback" class="feedback-msg"></div>
                        </div>
                    </div>
                `;
            } else {
                evalHTML = `
                    <div class="eval-section">
                        <p style="color:#64748b; font-style:italic; font-size:14px;">Aucune évaluation n'est associée à cette leçon.</p>
                    </div>
                `;
            }

            // Injection complète du contenu construit dans l'interface
            contentArea.innerHTML = `
                <h2>${lecon.lecon_titre}</h2>
                <p class="context">Cours associé : <b>${lecon.cours_titre}</b></p>
                ${lecteurHTML}
                ${evalHTML}
            `;
        }

        // FONCTION INTERACTIVE : Vérification en temps réel du QCM sans recharger la page
        function verifierReponse(solutionCorrecte) {
            const options = document.getElementsByName('qcm_choice');
            let choixUtilisateur = null;

            for (let i = 0; i < options.length; i++) {
                if (options[i].checked) {
                    choixUtilisateur = options[i].value;
                    break;
                }
            }

            const feedback = document.getElementById('feedback');
            feedback.style.display = "block";

            if (!choixUtilisateur) {
                feedback.style.background = "#fef3c7";
                feedback.style.color = "#d97706";
                feedback.innerHTML = "Veuillez sélectionner une réponse avant de soumettre !";
                return;
            }

            if (choixUtilisateur === solutionCorrecte) {
                feedback.style.background = "#d1fae5";
                feedback.style.color = "#065f46";
                feedback.innerHTML = " Excellent ! C'est la bonne réponse.";
            } else {
                feedback.style.background = "#fee2e2";
                feedback.style.color = "#991b1b";
                feedback.innerHTML = `Dommage, ce n'est pas tout à fait ça. Réessaie encore !`;
            }
        }
    </script>
</body>
</html>