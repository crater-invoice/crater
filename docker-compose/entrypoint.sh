#!/bin/bash
DOCKER_TAG=`cat /crater.docker.tag`
DIR="/var/www"
TMPDIR="/var/www/tmp"
REPO_URL=https://github.com/crater-invoice/crater.git
TAG=${DOCKER_TAG:-master}

if [ -d "${DIR}/app" ]
then
    echo "Starting application"
else
    if [[ $(stat -c %u $DIR) == 1000 ]]; then
        echo "Cloning app. Configuring $APP_URL for the first time..."
        git clone -q --depth 1 --branch $TAG $REPO_URL $TMPDIR
        cp -Rp $TMPDIR/* $DIR/
        rm -rf $TMPDIR
        # Ensure .env is present
        touch $DIR/.env
        echo "Installing requirements..."
        composer -q install
        chmod 775 storage/framework/ storage/logs/ bootstrap/cache/
    else
        echo "Make sure the root directory ${DIR} is owned by `whoami`"
        exit 1
    fi
fi

exec "$@"
