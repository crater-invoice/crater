#!/bin/bash
DOCKER_TAG=`cat /crater.docker.tag`
DIR="/var/www/html"
REPO_URL=https://github.com/crater-invoice/crater.git
TAG=${DOCKER_TAG:-master}

if [ -d "${DIR}/app" ]
then
    echo "Starting application"
else
	git clone --depth 1 --branch $TAG $REPO_URL .
fi

docker-php-entrypoint
exec "$@"
