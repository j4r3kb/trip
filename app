#!/bin/sh

help="app script for local deployment
Available commands:
${LBLUE}start${NC}    run the project
${LBLUE}stop${NC}     stop all project's containers
${LBLUE}restart${NC}  restart all project's containers
${LBLUE}recreate${NC} recreate all project's containers
${LBLUE}shell${NC}    enter PHP container's shell
${LBLUE}php${NC}      run PHP commands
${LBLUE}tests${NC}    run PHP Unit bridge commands
${LBLUE}composer${NC} run Composer commands
${LBLUE}console${NC}  run Symfony console commands"

action="${1}"
os=`uname`
appDir=$(CDPATH= cd -- "$(dirname -- "${0}")" && pwd)
runDir=$appDir/docker
container_php=jb_trip_php
dir=trip

cd "${appDir}"
if [ ! -d "vendor" ]; then
  echo "Creating vendor dir before starting."
  mkdir "vendor"
fi
if [ ! -d "var" ]; then
  echo "Creating var dir before starting."
  mkdir "var"
fi

cd "${runDir}"

export UID=$(id -u)
export GID=$(id -g)

case "$action" in
  start )
    eval "docker-compose up -d" ;;
  stop )
    eval "docker-compose stop" ;;
  restart )
    eval "docker-compose stop && docker-compose up -d" ;;
  recreate )
    eval "docker-compose stop && docker-compose up -d --force-recreate" ;;
  shell )
    eval "docker exec -it -u ${UID} ${container_php} sh" ;;
  php )
    shift
    eval "docker exec -it -u ${UID} -w /var/www/${dir} ${container_php} php ${@}" ;;
  tests )
    shift
    eval "docker exec --env-file ../.env.test -it -u ${UID} -w /var/www/${dir} ${container_php} \
          php vendor/bin/simple-phpunit --testdox ${@}" ;;
  composer )
    shift
    eval "docker exec -it -u ${UID} -w /var/www/${dir} ${container_php} php bin/composer ${@}" ;;
  console )
    shift
    eval "docker exec -it -u ${UID} -w /var/www/${dir} ${container_php} bin/console ${@}" ;;
  * )
    echo "${help}" ;;
esac

return 0
