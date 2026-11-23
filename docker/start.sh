#!/bin/sh
set -e

echo "Aguardando o MySQL e rodando o seed..."
until php src/Database/Seed.php; do
  echo " - MySQL indisponível, tentando novamente..."
  sleep 2
done

echo "Iniciando o Apache..."
exec apache2-foreground