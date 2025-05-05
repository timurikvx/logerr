#!/bin/bash

cd /opt/logerr/docker
docker compose down -v

cd /var/www
sudo rm -fr /var/www/logerr
git clone https://github.com/timurikvx/logerr.git
sudo chmod 777 -R /var/www/logerr

cd /opt/logerr/docker
docker compose up --build -d
sleep 1s

