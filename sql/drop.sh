#!/bin/bash

SCRIPT_PATH=$(realpath $0)
SCRIPT_DIR=$(dirname "$SCRIPT_PATH")

docker exec -i solidary-food-distribution-database mariadb -u root -proot sofodi <<< \
$(docker exec -i solidary-food-distribution-database mariadb --skip-column-names -u root -proot sofodi <<EOF
    SELECT CONCAT('DROP TABLE ', table_name, ';')
    FROM information_schema.tables
    WHERE table_schema = 'sofodi';
EOF
)