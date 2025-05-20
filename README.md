# IfStack - Application Web Moderne

## Description
IfStack est une application web moderne construite avec Laravel et Vue.js, offrant une expérience utilisateur fluide et réactive. Cette application combine la puissance du backend Laravel avec l'élégance de Vue.js pour créer une solution robuste et performante.

## Technologies Principales
- **Backend**: Laravel 12.x
- **Frontend**: Vue.js 3.x avec TypeScript
- **CSS**: Tailwind CSS
- **Base de données**: MySQL/PostgreSQL (Production)
- **Serveur Web**: Nginx/Apache
- **Cache**: Redis
- **Queue**: Supervisor
- **Outils de développement**: Vite, ESLint, Prettier

## Prérequis
- PHP 8.2 ou supérieur
- Node.js 18.x LTS ou supérieur
- Composer 2.x
- MySQL 8.0+ ou PostgreSQL 13+
- Redis 6.0+
- Nginx 1.18+ ou Apache 2.4+
- Supervisor

## Installation en Production

1. Cloner le repository
```bash
git clone [URL_DU_REPO]
cd IfStack
```

2. Installer les dépendances PHP
```bash
composer install --no-dev --optimize-autoloader
```

3. Installer les dépendances JavaScript et construire les assets
```bash
npm install
npm run build
```

4. Configurer l'environnement
```bash
cp .env.example .env
php artisan key:generate
```

5. Configurer les variables d'environnement de production
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://votre-domaine.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ifstack
DB_USERNAME=user
DB_PASSWORD=password

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

6. Optimiser l'application
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

7. Configurer la base de données
```bash
php artisan migrate --force
```

## Configuration de Supervisor

```ini
[program:ifstack-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /chemin/vers/ifstack/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=8
redirect_stderr=true
stdout_logfile=/chemin/vers/ifstack/storage/logs/worker.log
stopwaitsecs=3600
```

## Maintenance

### Mise à jour de l'application
```bash
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Surveillance
- Surveiller les logs dans `storage/logs/laravel.log`
- Configurer un monitoring (ex: New Relic, Datadog)
- Mettre en place des alertes sur les erreurs critiques

## Sécurité
- Activer HTTPS avec Let's Encrypt
- Configurer un pare-feu (ex: UFW)
- Mettre en place une politique de sauvegarde régulière
- Utiliser des secrets sécurisés pour les variables d'environnement
- Mettre à jour régulièrement les dépendances

## Fonctionnalités
- Interface utilisateur moderne et réactive
- Support TypeScript
- Intégration Inertia.js
- Génération de QR codes
- Export PDF
- Export Excel
- Interface drag-and-drop
- Système de cache optimisé
- Gestion des files d'attente
- Monitoring des performances

## Structure du Projet
```
├── app/            # Code PHP principal
├── resources/      # Assets frontend
├── routes/         # Définition des routes
├── database/       # Migrations et seeders
├── tests/          # Tests automatisés
├── storage/        # Logs et fichiers générés
└── public/         # Fichiers publics
```

## Tests
```bash
php artisan test
```

## Contribution
1. Fork le projet
2. Créer une branche pour votre fonctionnalité
3. Commiter vos changements
4. Pousser vers la branche
5. Ouvrir une Pull Request

## Licence
MIT

## Support
Pour toute question ou problème, veuillez ouvrir une issue dans le repository GitHub.
