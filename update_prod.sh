#!/bin/bash

sudo ./fixperms
git pull
php app/console doctrine:migrations:migrate
php app/console cache:clear --env=prod
sudo ./fixperms