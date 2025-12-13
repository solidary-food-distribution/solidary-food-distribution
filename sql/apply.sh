#!/bin/bash

SCRIPT_PATH=$(realpath $0)
SCRIPT_DIR=$(dirname "$SCRIPT_PATH")

docker exec -i solidary-food-distribution-database mariadb -u root -proot sofodi < "$SCRIPT_DIR/init.sql"