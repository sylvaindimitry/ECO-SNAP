#!/bin/bash
# Script d'initialisation de la base de données pour Render

echo "Attente de la disponibilité de la base de données..."
sleep 10

# Importer le schéma SQL si la variable est définie
if [ -n "$DATABASE_HOST" ] && [ -n "$DATABASE_USER" ] && [ -n "$DATABASE_PASSWORD" ] && [ -n "$DATABASE_NAME" ]; then
    echo "Import du schéma SQL..."
    mysql -h "$DATABASE_HOST" -P "$DATABASE_PORT" -u "$DATABASE_USER" -p"$DATABASE_PASSWORD" "$DATABASE_NAME" < /var/www/html/config/database.sql
    echo "Schéma importé avec succès!"
else
    echo "Variables de base de données non définies. Import skipped."
fi
