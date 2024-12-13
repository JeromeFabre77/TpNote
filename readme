# Symfony Project

## Étape 1 : Installer les dépendances
```bash
composer install
```

## Étape 2 : Configurer l'URL de la base de données

Dans le fichier `.env`, configurez la variable `DATABASE_URL` avec l'URL de votre base de données :

```env
DATABASE_URL="mysql://username:password@127.0.0.1:3306/nom_de_la_base?serverVersion=5.7"
```

## Étape 3 : Créer la base de données et exécuter les migrations

Exécutez les commandes suivantes :
```bash
php bin/console doctrine:database:create
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

## Étape 4 : Lancer le serveur Symfony

Pour démarrer le serveur local :
```bash
symfony server:start
```

## Étape 5 : Tester les routes avec Postman

Un fichier JSON Postman contenant les routes à tester est inclus dans ce projet. Vous pouvez l'importer dans votre environnement Postman pour tester facilement les endpoints.

Le fichier se trouve à l'emplacement suivant :

```
TpNote.postman_collection.json
```
