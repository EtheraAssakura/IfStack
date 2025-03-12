-- Requête pour obtenir la liste des fournitures proches de la rupture
SELECT
    f.nom as fourniture,
    f.reference_isfac,
    e.nom as etablissement,
    emp.nom as emplacement,
    s.quantite_estimee,
    COALESCE(s.seuil_alerte_local, f.seuil_alerte_global) as seuil_alerte
FROM fournitures f
JOIN stocks s ON s.fourniture_id = f.id
JOIN emplacements emp ON s.emplacement_id = emp.id
JOIN etablissements e ON emp.etablissement_id = e.id
WHERE s.quantite_estimee <= COALESCE(s.seuil_alerte_local, f.seuil_alerte_global);

-- Requête pour obtenir les livraisons effectuées dans la semaine
SELECT
    l.date_effective,
    e.nom as etablissement,
    emp.nom as emplacement,
    f.nom as fourniture,
    dl.quantite,
    u.name as livreur
FROM livraisons l
JOIN details_livraison dl ON dl.livraison_id = l.id
JOIN emplacements emp ON dl.emplacement_id = emp.id
JOIN etablissements e ON emp.etablissement_id = e.id
JOIN fournitures f ON dl.fourniture_id = f.id
JOIN users u ON l.user_id = u.id
WHERE l.date_effective BETWEEN DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY) AND CURRENT_DATE
AND l.statut = 'effectuee';

-- Requête pour obtenir les livraisons prévues pour la semaine prochaine
SELECT
    l.date_prevue,
    e.nom as etablissement,
    emp.nom as emplacement,
    f.nom as fourniture,
    dl.quantite,
    u.name as livreur
FROM livraisons l
JOIN details_livraison dl ON dl.livraison_id = l.id
JOIN emplacements emp ON dl.emplacement_id = emp.id
JOIN etablissements e ON emp.etablissement_id = e.id
JOIN fournitures f ON dl.fourniture_id = f.id
JOIN users u ON l.user_id = u.id
WHERE l.date_prevue BETWEEN CURRENT_DATE AND DATE_ADD(CURRENT_DATE, INTERVAL 7 DAY)
AND l.statut = 'planifiee';

-- Top 5 des produits les plus consommés
SELECT
    f.nom as fourniture,
    f.reference_isfac,
    COUNT(a.id) as nombre_alertes,
    ROUND(COUNT(a.id) / 4, 2) as moyenne_hebdomadaire -- Sur le dernier mois
FROM fournitures f
JOIN stocks s ON s.fourniture_id = f.id
JOIN alertes a ON a.stock_id = s.id
WHERE a.created_at >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
GROUP BY f.id, f.nom, f.reference_isfac
ORDER BY nombre_alertes DESC
LIMIT 5;

-- Statistiques globales par établissement
SELECT
    e.nom as etablissement,
    COUNT(DISTINCT s.fourniture_id) as nombre_references,
    SUM(s.quantite_estimee) as stock_total,
    COUNT(DISTINCT a.id) as alertes_en_cours
FROM etablissements e
JOIN emplacements emp ON emp.etablissement_id = e.id
JOIN stocks s ON s.emplacement_id = emp.id
LEFT JOIN alertes a ON a.stock_id = s.id AND a.traitee = false
GROUP BY e.id, e.nom;
