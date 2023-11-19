#!/bin/bash

declare FRONTEND=http://frontend:8080
declare FRONTEND_NETWORK=frontnednet
declare LND_CERT
declare LND_MACAROON
declare LND_SOCKET=polar-n1-alice:10009
declare LN_NETWORK=polar-network-1_default
declare VARS=('FRONTEND' 'FRONTEND_NETWORK' 'LND_CERT' 'LND_MACAROON' 'LND_SOCKET' 'LN_NETWORK')

# switch to this script's directory in order to relative paths work
cd "$(dirname "$0")" || exit 1

function read_variables()
{
  local default
  local val
  for varname in "${VARS[@]}"; do
    eval 'default="${'"$varname"'}"'
    while true; do
        eval 'read -p "Enter value for ${varname} (default=${default}): " val'
        if [[ -n "$val" ]]; then
          eval "$varname"'="${val}"'
        else
          eval "$varname"'='"${default}"
        fi
        eval 'if [[ -n "$'"$varname"'" ]]; then break; fi'
    done
  done
}


function print_env_docker()
{
  cat <<EOF
LN_NETWORK=${LN_NETWORK}
FRONTEND=${FRONTEND}
FRONTEND_NETWORK=${FRONTEND_NETWORK}
EOF
}

function print_env_lnserver()
{
  cat <<EOF
PORT=8080
LND_SOCKET=${LND_SOCKET}
LND_CERT=${LND_CERT}
LND_MACAROON=${LND_MACAROON}
EOF
}

function backup_envfiles()
{
  [[ -f ./.env ]]  && mv ./.env ./.env.bak
  [[ -f ./lnserver/.env ]] && mv ./lnserver/.env ./lnserver/.env.bak
}

function write_to_envfiles()
{
  local ans='n'

  printf '\nEnv for docker-compose.yml:\n\n'
  print_env_docker
  printf '\nEnv for docker-compose.yml:\n\n'
  print_env_lnserver
  printf '\n'

  read -r -p 'Write them to .env files? [y/n] ' ans
  if [[ "$ans" == 'y' ]]; then
    print_env_docker > ./.env
    print_env_lnserver > ./lnserver/.env
  fi
}

function start_docker()
{
  local ans='n'

  ans='n'
  read -r -p 'Start docker containers? [y/n] ' ans
  if [[ "$ans" == 'y' ]]; then
    docker-compose up -d
  fi

  ans='n'
  read -r -p 'Is this the first time starting the container? [y/n] ' ans
  if [[ "$ans" == 'y' ]]; then
    # copy default backend .env
    if [[ ! -f ./backend/.env ]]; then
      cp ./backend/.env.example ./backend/.env
    fi

    # generate laravel application key to encrypt sessions
    docker container exec lnpaywall-authbackend php artisan key:generate
  fi
}

function main()
{
  read_variables || exit 1
  backup_envfiles
  write_to_envfiles || exit 1
  start_docker || exit 1
}

# run main
main

