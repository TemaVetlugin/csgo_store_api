## Application deployment instructions

### Server configuration

The article is written considering the `Ubuntu` OS for the server.

It's recommended to have the following server parameters, at least:
- 2 CPU cores
- 4 GB RAM
- 70 GB+ SSD

### Tools

The following tools are needed to set up the application:
- GIT
- Docker Compose

If Docker Compose is not installed, follow the instructions https://docs.docker.com/desktop/install/ubuntu/

### Download

Create two folders for both projects `front` and `api`.

Clone GIT repositories to corresponding folders.

```shell
git clone <FRONT_REPO>.git ./front
```

```shell
git clone <API_REPO>.git ./api
```

### Set Front-End service up
Enter the `front` directory and follow the instructions from the `README` file of the front-end project.

**NOTE:** The API service's docker infrastructure depends on the front-end project.
Ensure that the previous step is completed and the front-end docker service is up.

```shell
cd ./front
```

```shell
docker compose ps
```

### Set API service up
Enter the `api` project directory.

#### ENV variables

Create the `.env` file:
```shell
cp .env.example .env
```

Fill the `.env` file. The variables to change are described in the [README](./README.md).

**Note:** it'd be more simple to create the `.env` file based on the same file from the Dev Server.

If so, then only the following values are needed to change:
- `APP_URL`
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`
- `SESSION_DOMAIN`
- `SANCTUM_STATEFUL_DOMAINS`
- `HOME_PAGE_REDIRECT_URL`
- `MAINTENANCE_EMAIL_ADDRESSES`
- `STEAM_AUTH_API_KEYS`
- `PAYMENT_SERVICE_SECRET_KEY`
- `MARIADB_USER`
- `MARIADB_PASSWORD`
- `MARIADB_ROOT_PASSWORD`
- `MARIADB_DATABASE`

#### Run the app

To run the application simply exec:
```shell
docker compose -f compose-deploy.yml up -d
```

**At this stage, the api should be available at the `8000` port**.

To stop the application, run:
```shell
docker compose -f compose-deploy.yml down
```

To enter a service container, run:
```shell
docker compose -f compose-deploy.yml exec php bash
```

(`nginx`, `mariadb` could be used instead of `php` service name).

Docker consumes much disk space to keep its cache of built images, containers, networks, volumes.

Especially for the front-end containers.

Thus remember to clean up the disk space periodically:
```shell
docker builder prune
```

```shell
docker system prune -a
```

```shell
docker volume prune -a
```

To check the disk usage by Docker:
```shell
docker system df
```

### SSL configuration

The project does not have SSL configurations.

To set up the SSL for both front-end and back-end services, please enter the `nginx` docker container and
change both nginx configuration files under the `/etc/nginx/conf.d/` directory.

SSL for the Front-End service should be configured to the `443` port.

SSL for the Back-End service should be configured to:
- `443` if another domain is used for the API
- `8000` if the same domain as for the front-end is used (default API service port)

Restart the `nginx` docker service.

### SkinsBack

(**important**)

Configure the SkinsBack account: https://skinsback.com/merchant

Here you need to open the Settings page of the related SkinsBack project,
and add your server's IP to the list of IPs Whitelist (`Project IP Addresses`)

Ensure that there are some funds on the SkinsBack account.
This balance is used to buy skins for users who performed a payment.

### Payment Service

Log into the Payment Service account and go to the "Account" settings page (/account/settings/profile).

Fill the `Website` input with the URL of the website homepage.

Go to the "Integration" settings page (/account/settings/integration).

Here you need to fill the `Callback URL` input with the Callback API Endpoint url of the application,
it should look like: `https://<CSGOSTORE_API_DOMAIN>:<CSGOSTORE_API_PORT>/api/payment/callback`

**Warning!:** Note that the Payment Service accepts HTTPS urls only for the `Website` and `Callback`.

**NOTE:** The API is configured to the port `8000` by default.
If the SSL is configured to another port, `443` for example, the SSL related port should be specified.
