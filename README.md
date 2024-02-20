
# SoigneMoi

## Prérequis
	PHP 8.2
	Symfony 7.0.3
	Symfony CLI 5.8.6
	Composer 2.6.6
	SQLite 3.45.1
	OpenSSL

## Instructions
1.Cloner le dépôt sur la machine locale :

git clone https://github.com/KalorianKaltar/studi_web.git

2. Décommenter dans le php.ini :
	```bash
	extension=sodium

3. Se placer dans le répertoire et installer les dépendances PHP avec Composer :
    ```bash
    composer install
    ```

4. Modifier le fichier .env :
    ```bash
    DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.sqlite"
	
	###> lexik/jwt-authentication-bundle ###

	JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
	JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
	JWT_PASSPHRASE=soignemoi

	###< lexik/jwt-authentication-bundle ###
    ```

5. Générer les clefs JWT publiques et privés :
    ```bash
    mkdir config\jwt && \
    openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
	openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout
    ```

6. Mettre à jour la base de données :
	```bash
	php bin/console doctrine:schema:update --force
	```
    
7. Charger les données de la fixtures :
	```bash
	php bin/console doctrine:fixtures:load
	```
8. Lancer le serveur :
	```bash
	symfony server:start
	
9. Accéder à l'application web via l'url suivante :
 http://localhost:8000
