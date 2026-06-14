<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_role'])) {
    echo json_encode(['erreur' => 'Non authentifié']);
    exit;
}

$module_id = intval($_GET['module_id'] ?? 0);

if ($module_id > 0) {
    try {
        // Récupérer les leçons du module à travers ses cours
        $stmt = $pdo->prepare("
            SELECT l.id AS lecon_id, l.titre AS lecon_titre, l.type_contenu, l.fichier_url, c.titre AS cours_titre 
            FROM lecons l 
            JOIN cours c ON l.cours_id = c.id 
            WHERE c.module_id = ? 
            ORDER BY l.ordre ASC
        ");
        $stmt->execute([$module_id]);
        $lecons = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Pour chaque leçon, on va chercher s'il y a une évaluation (QCM) rattachée
        foreach ($lecons as $key => $lecon) {
            $stmt_eval = $pdo->prepare("SELECT id, question, choix_a, choix_b, choix_c, reponse_correcte FROM evaluations WHERE lecon_id = ?");
            $stmt_eval->execute([$lecon['lecon_id']]);
            $eval = $stmt_eval->fetch(PDO::FETCH_ASSOC);
            
            // On attache l'évaluation à la leçon (si elle existe)
            $lecons[$key]['evaluation'] = $eval ? $eval : null;
        }

        echo json_encode(['succes' => true, 'lecons' => $lecons]);
    } catch (PDOException $e) {
        echo json_encode(['erreur' => 'Erreur BDD : ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['erreur' => 'ID de module invalide']);
}