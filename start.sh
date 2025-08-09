#!/bin/bash
# Fuerza conversión a número y establece valor por defecto
PORT_NUM=$(( ${PORT:-8000} ))
exec php artisan serve --host=0.0.0.0 --port=$PORT_NUM