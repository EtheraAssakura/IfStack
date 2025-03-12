-- Requête pour obtenir le nombre d'utilisateurs par rôle
SELECT r.name as role_name, COUNT(u.id) as user_count
FROM roles r
LEFT JOIN users u ON u.role_id = r.id
GROUP BY r.id, r.name;

-- Requête pour obtenir les derniers utilisateurs inscrits
SELECT u.name, u.email, u.created_at, r.name as role_name
FROM users u
LEFT JOIN roles r ON u.role_id = r.id
ORDER BY u.created_at DESC
LIMIT 10;

-- Requête pour les statistiques globales
SELECT
    (SELECT COUNT(*) FROM users) as total_users,
    (SELECT COUNT(*) FROM roles) as total_roles,
    (SELECT COUNT(*) FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)) as new_users_last_week;
