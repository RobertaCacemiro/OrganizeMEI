#!/bin/bash

# Vai para o diretório raiz do Laravel dentro do container
cd /app

# Executa o Laravel Scheduler
php artisan schedule:run
