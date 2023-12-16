#!/bin/bash

declare FRONTEND_URI
declare FRONTEND_NETWORK
declare LND_CERT
declare LND_MACAROON
declare LND_SOCKET
declare LN_NETWORK
declare BACKEND_APP_KEY
declare -r VARS=('FRONTEND_URI' 'FRONTEND_NETWORK' 'LND_CERT' 'LND_MACAROON' 'LND_SOCKET' 'LN_NETWORK')

# switch to this script's directory in order to relative paths work
cd "$(dirname "$0")" || exit 1

# read an environment variable from an .env file
function read_from_env()
{
  local file="${1}"
  local envvar="${2}"
  local default="${3}"
  local value="${default}"

  # if the .env file exists
  if [[ -f "${file}" ]]; then
    # and if it already has a value for $var
    if grep --perl-regexp "^${envvar}=" "${file}" > /dev/null 2>&1; then
      # use this value (otherwise, will use the default)
      # shellcheck disable=SC2034
      value=$(grep --perl-regexp "^${envvar}=" "${file}" | sed "s/${envvar}=//")
    fi
  fi

  # eval the expression $var="$value", which changes the global variables
  eval "${envvar}"='$value'
}

function set_defaults()
{
  local polar_lnd_cert="${HOME}/.polar/networks/1/volumes/lnd/alice/tls.cert"
  local polar_lnd_macaroon="${HOME}/.polar/networks/1/volumes/lnd/alice/data/chain/bitcoin/regtest/admin.macaroon"

  # docker envs
  read_from_env ./.env 'FRONTEND_NETWORK' 'frontendnet'
  read_from_env ./.env 'LN_NETWORK' 'polar-network-1_default'

  # lnserver envs
  [[ -f "${polar_lnd_cert}" ]] && LND_CERT=$(xxd -p -c2000 "${polar_lnd_cert}")
  [[ -f "${polar_lnd_macaroon}" ]] && LND_MACAROON=$(xxd -p -c2000 "${polar_lnd_macaroon}")
  read_from_env ./lnserver/.env 'LND_CERT' "${LND_CERT}"
  read_from_env ./lnserver/.env 'LND_MACAROON' "${LND_MACAROON}"
  read_from_env ./lnserver/.env 'LND_SOCKET' 'polar-n1-alice:10009'

  # backend envs
  read_from_env ./backend/.env 'FRONTEND_URI' 'http://frontend:8080'
  read_from_env ./backend/.env 'BACKEND_APP_KEY' "base64:$(openssl rand -base64 32)"
}

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

function print_env_backend()
{
  # generate fresh new application key
  BACKEND_APP_KEY="base64:$(openssl rand -base64 32)"

  cat <<EOF
APP_NAME=LNPAYWALL
APP_KEY=${BACKEND_APP_KEY}
APP_ENV=local
APP_DEBUG=true
FRONTEND_URI=${FRONTEND_URI}
LNSERVER_URI=http://lnpaywall-lnserver:8080
EOF
}

function print_env_docker()
{
  cat <<EOF
LN_NETWORK=${LN_NETWORK}
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
  [[ -f ./backend/.env ]]  && mv ./backend/.env ./backend/.env.bak
  [[ -f ./lnserver/.env ]] && mv ./lnserver/.env ./lnserver/.env.bak
}

function write_to_envfiles()
{
  local ans='n'

  printf '\nEnv for DOCKER:\n\n'
  print_env_docker
  printf '\nEnv for BACKEND:\n\n'
  print_env_backend
  printf '\nEnv for LNSERVER:\n\n'
  print_env_lnserver
  printf '\n'

  read -r -p 'Write them to .env files? [y/n] ' ans
  if [[ "$ans" == 'y' ]]; then
    print_env_docker > ./.env
    print_env_backend > ./backend/.env
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
}

function main()
{
  set_defaults
  read_variables || exit 1
  backup_envfiles
  write_to_envfiles || exit 1
  start_docker || exit 1
}

# run main
main
