#!/bin/bash

# Wait for the database to become available
echo "Waiting for the database to become available..."
until php -r "try { new PDO('mysql:host=db;dbname=$DB_DATABASE', '$DB_USER', '$DB_PASS'); echo 'Connected to the database'; } catch (PDOException \$e) { exit(1); }"; do
    sleep 1
done

# Run necessary commands
php bin/console orm:schema-tool:create

# Execute the container's original command
exec "$@"
