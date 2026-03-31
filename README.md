# 🚀 Projet S4 - Architecture Découplée (Vue 3 + Symfony 7)

Cet environnement de développement est entièrement dockerisé. **Vous n'avez pas besoin d'installer PHP, Node.js ou MySQL sur votre machine physique** — seul Docker Desktop est requis.

## 🛠 Prérequis

- Docker Desktop installé et lancé en arrière-plan.
- Git.

## ⚙️ Installation & lancement (premier clone)

1. Cloner le dépôt et entrer dans le dossier :

```bash
git clone git@github.com:loucalm/WS401.git
cd WS401
```

2. Installer les dépendances (backend & frontend)

Les dossiers `vendor/` et `node_modules/` ne sont pas versionnés. Générez-les à l'intérieur des conteneurs Docker :

```bash
# Backend (Symfony)
docker compose run --rm backend composer install

# Frontend (Vue.js)
docker compose run --rm frontend npm install

# Installer les assets des bundles Symfony (API Platform / EasyAdmin)
docker compose exec -u 0 backend php bin/console assets:install public --symlink --relative
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

# Exécuter les migrations (sans prompt interactif)
docker compose exec -u 0 backend php bin/console doctrine:migrations:migrate --no-interaction
```

## 🔐 Procédure de synchronisation (Backend & Auth)

Salut l'équipe — j'ai poussé toute la logique de connexion JWT. Pour que ça fonctionne chez vous, faites ces commandes depuis la racine du projet (dans un terminal) :

1. Installer les nouvelles bibliothèques

Récupère les paquets manquants (LexikJWT, MakerBundle, etc.) :

```powershell
docker compose exec -u 0 backend composer install
```

2. Générer vos clés de sécurité locales

Les clés de cryptage ne sont pas versionnées. Générez vos propres clés locales :

```powershell
docker compose exec -u 0 backend php bin/console lexik:jwt:generate-keypair --skip-if-exists
```

3. Mettre à jour votre base de données

Créez la table `User` et synchronisez votre base MySQL locale :

```powershell
docker compose exec -u 0 backend php bin/console doctrine:migrations:migrate --no-interaction
```

4. Créer l'utilisateur de test

Injecte l'utilisateur `admin@test.com` (mot de passe : `password123`) :

```powershell
docker compose exec -u 0 backend php bin/console doctrine:fixtures:load
```

(Tapez `yes` pour confirmer le remplissage.)

✅ Comment tester ?

- Ouvrez le frontend : http://localhost:5173
- Ouvrez la console du navigateur (F12).
- Connectez-vous avec `admin@test.com` / `password123`.

Si vous voyez un long token s'afficher dans la console, vous êtes synchronisés.

Merci — dites-moi si vous voulez que j'ajoute des instructions pour Windows PowerShell natif ou pour bash spécifique.

## 📣 Ce que votre équipe doit faire

Prévenez votre équipe sur Discord/Slack. Une fois qu'ils auront fait un `git pull` pour récupérer votre code, ils devront lancer ces deux commandes depuis la racine du projet pour mettre à jour leur base de données locale — j'ai fini la BDD du projet et ajouté des données fictives, donc ces commandes sont nécessaires :

PowerShell (Windows) :

```powershell
# 1. Mettre à jour les tables MySQL
docker compose exec -u 0 backend php bin/console doctrine:migrations:migrate --no-interaction

# 2. Remplir avec les fausses données
docker compose exec -u 0 backend php bin/console doctrine:fixtures:load
```

macOS / Linux (bash / zsh) :

```bash
# 1. Mettre à jour les tables MySQL
docker compose exec -u 0 backend php bin/console doctrine:migrations:migrate --no-interaction

# 2. Remplir avec les fausses données
docker compose exec -u 0 backend php bin/console doctrine:fixtures:load
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
docker compose exec -u 0 backend php bin/console doctrine:migrations:migrate --no-interaction

# Installer un package npm (frontend)
docker compose exec frontend npm install <package-name>

# Vider le cache Symfony
docker compose exec -u 0 backend php bin/console cache:clear
```
