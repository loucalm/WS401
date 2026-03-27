# 🚀 Projet S4 - Architecture Découplée (Vue 3 + Symfony 7)

Cet environnement de développement est entièrement dockerisé. **Vous n'avez pas besoin d'installer PHP, Node.js ou MySQL sur votre machine physique** — seul Docker Desktop est requis.

## 🛠 Prérequis

- Docker Desktop installé et lancé en arrière-plan.
- Git.

---

## ⚙️ Installation & lancement (premier clone)

1. Cloner le dépôt et entrer dans le dossier :

```bash
git clone git@github.com:loucalm/WS401.git
cd WS401D
```

2. Installer les dépendances (backend & frontend)

Les dossiers `vendor/` et `node_modules/` ne sont pas versionnés. Générez-les à l'intérieur des conteneurs Docker :

```bash
# Backend (Symfony)
docker compose run --rm backend composer install

# Frontend (Vue.js)
docker compose run --rm frontend npm install
```

3. Lancer l'infrastructure (build)

Lors du premier lancement Docker téléchargera les images et construira l'environnement :

```bash
docker compose up -d --build
# Attendre quelques secondes que MySQL s'initialise correctement
```

4. Initialiser la base de données

Puisque le volume de la base n'est pas versionné, créez la base et exécutez les migrations :

```bash
# Créer la base 'app_db' si nécessaire
docker compose exec -u 0 backend php bin/console doctrine:database:create --if-not-exists

# Exécuter les migrations (répondre 'yes' si demandé)
docker compose exec -u 0 backend php bin/console doctrine:migrations:migrate
```

🌍 URLs utiles

- Frontend (Vue 3 / Vite) : http://localhost:5173
- Backend (API Platform) : http://localhost:8000/api
- Back-office (EasyAdmin) : http://localhost:8000/admin
- Gestion BDD (Adminer) : http://localhost:8080

Identifiants de la base

- Système : MySQL
- Serveur (service Docker) : `database`
- Utilisateur : `user`
- Mot de passe : `password`
- Base : `app_db`

🧰 Commandes utiles

```bash
# Arrêter le projet
docker compose down

# Relancer
docker compose up -d

# Créer une nouvelle entité (backend)
docker compose exec -u 0 backend php bin/console make:entity
docker compose exec -u 0 backend php bin/console make:migration
docker compose exec -u 0 backend php bin/console doctrine:migrations:migrate

# Installer un package npm (frontend)
docker compose exec frontend npm install <package-name>

# Vider le cache Symfony
docker compose exec -u 0 backend php bin/console cache:clear
```