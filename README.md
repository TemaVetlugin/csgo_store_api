## Installation & Configuration

### Environment file

Create `.env` file from the example:
```shell
cp .env.example .env
```

Then edit `.env` file with your own configurations.

Look at the [Environment Variables](#environment-variables) section for more details.

### Docker container

There are 2 `compose.yml` files prepared.
- `compose.yml` - for the local environment
- `compose-deploy.yml` - for the dev/production

Connection to the front-end Docker Containers via the Docker Network is the difference between the files.

For local environment, run the docker container:
```shell
docker compose build
```
```shell
docker compose up
```

### That's it
Check that the application is ready by requesting `http://localhost:8000`
- The database is created and migrated automatically
- All the workers are run by default
- Cron is configured and run by default

Postman Collection is available here: `docs/api/csgostore.postman_collection.json`

## Environment variables

The following env variables are matter for the application configuration:
- **Laravel environment variables**:
    - `APP_ENV` - application environment, `local` for local development, `production` for the prod environment
    - `APP_KEY` - application encryption key, copy from the `.env.example` or generate with the `php artisan key:generate`
      command inside the `php` container after the container is up (re-generate must be performed on the first run only,
      before the application is used and the database is not empty)
    - `APP_DEBUG` - is debug info displayed on errors, should be `true` for the local environment and `false` for the production
    - `APP_URL` - should be filled with the website FQDN, e.g. `https://example.com`


- **Database connection for the app** (must match be equal to the corresponding values of the `MariaDB Server Docker Configurations` section):
    - `DB_HOST` - since all app services are deployed on the same server via Docker Compose,
      should be equal to Database Docker Container name, `mariadb` is the correct value by default
    - `DB_DATABASE` - name of the application database to connect (must match the `MARIADB_DATABASE` env value)
    - `DB_USERNAME` - database username (must match the `MARIADB_USER` env value)
    - `DB_PASSWORD` - database user password (must match the `MARIADB_PASSWORD` env value)


- **MariaDB Server Docker Configurations**
    - `MARIADB_USER` - username of the database user that is used by the application
    - `MARIADB_PASSWORD` - user password of the database user that is used by the application
    - `MARIADB_ROOT_PASSWORD` - root user password
    - `MARIADB_DATABASE` - database name of the database that is used by the application


- **User authentication sessions configuration**
    - `SESSION_DOMAIN` - must be equal to the API domain without the HTTP scheme, for example `api.csgostore.com`
    - `SANCTUM_STATEFUL_DOMAINS` - must be equal to the API domain + Port without the HTTP scheme, for example `api.csgostore.com:8000`

- **SMTP configurations** (by default configured to use the GMail SMTP server)
    - `MAIL_USERNAME` - GMail account email address that is used to send mails, for example `johndoe@gmail.com`
    - `MAIL_PASSWORD` - GMail account [**APP Password**](https://support.google.com/mail/answer/185833?hl=en)
      (since 2022 Google disabled the possibility to use plain account password,
      the App Password must be generated for the account after 2FA is configured)


- **CS:GO Store Application Configurations**
    - `HOME_PAGE_REDIRECT_URL` - website url to redirect users after successful login via Steam
    - `MAINTENANCE_EMAIL_ADDRESSES` - email addresses separated by comma `,` of users who should receive admin notifications
      (like Contact Us letters, application problem notifications)
    - `STEAM_AUTH_API_KEYS` - Steam account API key, can be created for free in a Steam account https://steamcommunity.com/dev/apikey
      (note that the API key can be created only for those accounts that have at least one game bought with the price > $5)
    - `DEFAULT_CLIENT_CURRENCY` - Default currency code to convert prices for users, lowercase, `kzt` by default


- **Marketplace (SkinsBack) configurations**
    - `MARKETPLACE_CLIENT_ID` - SkinsBack account `Client ID` parameter, login to the SkinsBack account, find the related
      project, open settings, copy the `Client ID` value
    - `MARKETPLACE_SECRET_KEY` - SkinsBack account `Client Secret` parameter, could be taken from the project settings too
    - `MARKETPLACE_CURRENCY` - Default currency code of the SkinsBack, lowercase, `usd` by default


- **Payment service configurations**
    - `PAYMENT_SERVICE_SECRET_KEY` - Payment service Private Key, it can be found in the `Integration` section of the payment
      account settings
    - `PAYMENT_SERVICE_CALLBACK_IP_WHITELIST` - optional parameter, a list of payment service server IPs can be specified here
      separated by comma `,` in order to block any callback request from IPs that differ from the trusted IPs.
      The Payment Service trusted IPs could be taken from **docs/ru/integration/ips/** section of the documentation.

### Production Deploy
Please check the corresponding instruction: [DEPLOYMENT.MD](./DEPLOYMENT.MD)
