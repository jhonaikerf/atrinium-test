### Getting Started

Navigate to docker folder
```bash
cd docker
```

Create docker .env file and fill enviroments variables (set DATABASE_USER=user for testing purpose)
DATABASE_USER=user
```bash
cp .env.example .env
```

Navigate to symfony folder
```bash
cd ../symfony
```

Create symfony .env file and fill enviroments variables
- APP_ENV=
- APP_SECRET=
- DATABASE_URL=mysql://${DOCKER_DATABASE_USER}:${DOCKER_DATABASE_PASSWORD}@database:3306/${DOCKER_DATABASE_NAME}
- MONGODB_URL=mongodb://mongodb:27017
- MONGODB_DB=${MONGO_DB_NAME}
- FIXER_ACCESS_KEY=${FIXER_ACCESS_KEY}

```bash
cp .env.example .env
```

Create symfony .env.test file and fill enviroments variables for testing
- DATABASE_URL=mysql://${DOCKER_DATABASE_USER}:${DOCKER_DATABASE_PASSWORD}@database:3306/test
- MONGODB_URL=mongodb://mongodb:27017
- MONGODB_DB=test

```bash
cp .env.test.example .env.test
```

Navigate to docker folder
```bash
cd ../docker
```

Start docker services
```bash
docker-compose up -d 
```

Entry to php-fpm sh
```bash
docker-compose exec php-fpm sh
```

Run migrations
```bash
php bin/console doctrine:migrations:migrate
```

Update schemas 
```bash
php bin/console doctrine:mongodb:schema:update
```

Install back dependencies 
```bash
composer install
```

Install front dependencies 
```bash
npm install
npm run build
```

Generate a encoded password
```bash
php bin/console security:encode-password admin
```

Create new admin user. Replace the password with the one obtained in the previous step, and remember to escape the $ characters with \$
```bash
php bin/console doctrine:query:sql "INSERT INTO user (username, roles, password) VALUES ('admin', '[\"ROLE_ADMIN\"]', '$ENCODE_PASSWORD');"
```

Getting the list of currencies from fixer api
```bash
$ php bin/console app:update-list-currencies
```

Getting the historial bank central europe rates
```bash
$ php bin/console app:sync-ecb-history
```

Run migrations in test enviroment
```bash
php bin/console doctrine:migrations:migrate --env=test
```

Update schemas in test enviroment
```bash
php bin/console doctrine:mongodb:schema:update --env=test
```

You can run tests
```bash
$ php bin/phpunit
```
