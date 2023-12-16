# LNPAYWALL

LN-enabled Paywall for ANY generic webservice.

**WATCH THE DEMO VIDEO**

https://github.com/uwla/lnpaywall/assets/47862859/5e2e5b5f-9764-48a4-88cc-f5f5d553f6ea

## How it works

The user has to pay some amount of money to use the service for a chosen  period
of time:

1. User chooses for how long he wishes to access the service.
2. User pays invoice.
3. Payment is confirmed.
4. User can access the requested web service.
5. After time expires, user goes back to step 1.

![diagram](./diagram.jpg)

## Advantages

It can be integrated with **ANY** web service **WITHOUT CHANGES** to the service.

It just requires setting up the reverse-proxy to point to  the  machine  hosting
the web service.

The server providing the webservice is not aware of the proxy.

This separation of concerns frees the developer from the burden  of  setting  up
plugins, libraries and new APIs.

## Local setup

### automated setup

The script `./setup-Uizard.sh` automates the process of configuring envvars  for
`docker`, the `lnserver` and the `backend`. Just run:

```bash
./setup-wizard.sh
```

It will prompt for values of  `environment`  variables,  then  will  create  the
`.env` files and bring up docker containers.

This script DOES NOT set up Polar and the frontend container. For that, you have
to do it yourself.

### polar

Set up a local LN network using Polar with at least two LND nodes with a balaced
channel.

### frontend

Create a network:

```bash
docker network create <network_name>
```

Create a docker container to mock a web service (streaming, online gaming, video
conferecing, and so on).

```bash
docker container run \
    --name <container_name> \
    --network <network_name> \
    --rm \
    --detach \
    <container_img> <container_cmd>
```

You can also attach an existing container to the network:

```bash
docker network connect <network_name> <container_name>
```

Here is a real example of how to setup the frontend container:

```bash
docker network create frontendnet
mkdir website && echo "hello world!" > website/hello.txt
docker container run \
    --name frontend \
    --network frontendnet \
    --volume ./website:/app:ro \
    --rm \
    --detach \
    evop/static_webserver
```

### lnserver

*Skip this step if you used `setup_wizard.sh`.*

Create `env` file at `lnserver/.env`:

```bash
cd lnserver/ && cp .env.sample .env
```

You need to replace it with the proper values. The default values are  the  ones
used when you first create a Polar network.

### backend

*Skip this step if you used `setup_wizard.sh`.*

Create the `.env` file:

```bash
cd backend/ && cp .env.example .env
```

You need to adjust the `FRONTEND_URI` to match the  `<container_name>`  of  your
frontend container. You also need to generate an application key, which you  can
do so with the command:

```bash
echo "base64:$(openssl rand -base64 32)"
```

Or, if the backend container is running already:

```bash
docker container exec --user 1000 lnpaywall-backend php artisan key:generate
```

### docker

*Skip this step if you used `setup_wizard.sh`.*

Copy `.env.sample` to `.env`:

```bash
cp .env.sample .env
```

Adjust the values if needed:

```
LN_NETWORK=polar-network-1_default
FRONTEND_NETWORK=frontendnet
```

Where:

- `LN_NETWORK`: the name of the Docker network used by Polar.
- `FRONTEND_NETWORK`: a docker network attached to the frontend container

After that, bring the containers up:

```bash
docker-compose up --detach
```

## Tech stack

- typescript + nodejs (REST server for lightning queries)
- laravel (backend for handling user session and http proxy)
- docker (container technology)
- polar (simulate local LN network)

## Notes

Currently, this is a proof-of-concept only.

## LICENSE

MIT.
