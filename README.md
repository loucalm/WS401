# 🚀 Projet S4 - Architecture Découplée (Vue 3 + Symfony 7)

Cet environnement de développement est entièrement dockerisé. **Vous n'avez pas besoin d'installer PHP, Node.js ou MySQL sur vos machines physiques**, seul Docker Desktop est requis

## 🛠 Prérequis
- [Docker Desktop](https://www.docker.com/products/docker-desktop/) installé et lancé en arrière-plan.
- Git.

---

## ⚙️ Installation & Lancement (À faire lors du 1er clone)

**1. Cloner le projet et entrer dans le dossier**
```bash
git clone git@github.com:loucalm/WS401.git
cd WS401D
2. Installer les dépendances (Backend & Frontend)
Les dossiers vendor/ et node_modules/ sont ignorés par Git. Il faut les générer à l'intérieur de nos conteneurs Docker :

Bash
# Pour Symfony (Backend)
docker compose run --rm backend composer install

# Pour Vue.js (Frontend)
docker compose run --rm frontend npm install
3. Lancer toute l'infrastructure (Build)
Lors du premier lancement, Docker va télécharger les images et construire notre environnement :

Bash
docker compose up -d --build
(Attendez une dizaine de secondes que MySQL s'initialise correctement en arrière-plan).

4. Initialiser la Base de données
Puisque le dossier de base de données n'est pas versionné, il faut la recréer sur votre machine et envoyer les tables :

Bash
# Créer la base 'app_db'
docker compose exec -u 0 backend php bin/console doctrine:database:create --if-not-exists

# Exécuter les migrations (Créer les tables) - Tapez 'yes' quand demandé
docker compose exec -u 0 backend php bin/console doctrine:migrations:migrate
🌍 URLs de l'application & Accès
Une fois les conteneurs lancés (docker compose up -d), l'application est disponible sur ces adresses :

🎨 Frontend (Vue 3 / Vite) : http://localhost:5173

⚙️ Backend (API Platform) : http://localhost:8000/api

🛡️ Back-office (EasyAdmin) : http://localhost:8000/admin

🗄️ Gestion BDD (Adminer) : http://localhost:8080

Système : MySQL

Serveur : database

Utilisateur : user

Mot de passe : password

Base de données : app_db

🧰 Commandes Utiles au quotidien
Arrêter le projet proprement à la fin de la journée :

Bash
docker compose down
Relancer le projet le lendemain :

Bash
docker compose up -d
Créer une nouvelle entité / table (Backend) :

Bash
docker compose exec -u 0 backend php bin/console make:entity
docker compose exec -u 0 backend php bin/console make:migration
docker compose exec -u 0 backend php bin/console doctrine:migrations:migrate
Installer un nouveau package Node (Frontend) :

Bash
docker compose exec frontend npm install le-nom-du-package
Vider le cache de Symfony (en cas de bug d'affichage) :

Bash
docker compose exec -u 0 backend php bin/console cache:clear