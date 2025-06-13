# Pixsell Backend

## Requisitos previos

Asegúrate de tener instalados los siguientes programas en tu sistema:

- [Docker](https://www.docker.com/)
- [Docker Compose](https://docs.docker.com/compose/)
- [Git](https://git-scm.com/)
- [Composer](https://getcomposer.org/)

## Pasos para clonar y configurar el proyecto

1. Clona este repositorio:

   ```bash
   git clone <URL_DEL_REPOSITORIO>
   cd pixsell-backend
   ```

2. Copia el archivo de ejemplo `.env.example` y renómbralo a `.env`:

   ```bash
   cp .env.example .env
   ```

3. Configura las variables de entorno en el archivo `.env` según tus necesidades. Aquí tienes un ejemplo básico:

   ```env
   APP_NAME=Pixsell
   APP_ENV=local
   APP_KEY=base64:GENERATE_UNA_CLAVE_AQUI
   APP_DEBUG=true
   APP_URL=http://localhost

   LOG_CHANNEL=stack

   DB_CONNECTION=mysql
   DB_HOST=mysql
   DB_PORT=3306
   DB_DATABASE=pixsell
   DB_USERNAME=root
   DB_PASSWORD=root

   BROADCAST_DRIVER=log
   CACHE_DRIVER=file
   QUEUE_CONNECTION=sync
   SESSION_DRIVER=file
   SESSION_LIFETIME=120

   REDIS_HOST=redis
   REDIS_PASSWORD=null
   REDIS_PORT=6379

   MAIL_MAILER=smtp
   MAIL_HOST=mailpit
   MAIL_PORT=1025
   MAIL_USERNAME=null
   MAIL_PASSWORD=null
   MAIL_ENCRYPTION=null
   MAIL_FROM_ADDRESS="hello@example.com"
   MAIL_FROM_NAME="${APP_NAME}"

   MEILISEARCH_HOST=http://meilisearch:7700
   ```

4. Levanta los contenedores de Docker:

   ```bash
   ./vendor/bin/sail up -d
   ```

5. Ejecuta las migraciones y los seeders para preparar la base de datos:

   ```bash
   ./vendor/bin/sail artisan migrate --seed
   ```

6. Crea el enlace simbólico para el almacenamiento:

   ```bash
   ./vendor/bin/sail artisan storage:link
   ```

## Endpoints clave

### Autenticación

- **Login**: `POST /api/login`
  ```json
  {
    "email": "usuario@example.com",
    "password": "contraseña"
  }
  ```

- **Registro**: `POST /api/register`
  ```json
  {
    "name": "Usuario",
    "email": "usuario@example.com",
    "password": "contraseña",
    "password_confirmation": "contraseña"
  }
  ```

### CRUD de Álbumes

- **Crear Álbum**: `POST /api/albumes`
  ```json
  {
    "nombre": "Vacaciones",
    "descripcion": "Fotos de las vacaciones en la playa."
  }
  ```

- **Obtener Álbum por ID**: `GET /api/albumes/{id}`

- **Actualizar Álbum**: `PUT /api/albumes/{id}`
  ```json
  {
    "nombre": "Vacaciones Actualizado",
    "descripcion": "Fotos actualizadas."
  }
  ```

- **Eliminar Álbum**: `DELETE /api/albumes/{id}`

### Selección de Imágenes

- **Seleccionar Imágenes**: `POST /api/albumes/{id}/selecciones`
  ```json
  {
    "imagenes": [1, 2, 3]
  }
  ```

- **Finalizar Álbum**: `POST /api/albumes/{id}/finalizar`

## Troubleshooting

### Errores comunes

- **Permisos de almacenamiento**: Si encuentras problemas con los permisos, asegúrate de que el directorio `storage` tiene los permisos correctos:
  ```bash
  chmod -R 775 storage bootstrap/cache
  ```

- **CORS**: Si tienes problemas de CORS, revisa la configuración en `config/cors.php`.

- **Migraciones**: Si las migraciones fallan, verifica que las variables de entorno de la base de datos estén configuradas correctamente.

## Notas adicionales

- Accede a Mailpit en [http://localhost:8025](http://localhost:8025) para ver los correos enviados.
- Accede a Meilisearch en [http://localhost:7700](http://localhost:7700) para gestionar el motor de búsqueda.

## Despliegue en producción

1. Configura las variables de entorno para producción en el archivo `.env`.
2. Ejecuta las migraciones en el servidor:

   ```bash
   php artisan migrate --force
   ```

3. Crea el enlace simbólico para el almacenamiento:

   ```bash
   php artisan storage:link
   ```

4. Configura un servidor web (como Nginx o Apache) para apuntar al directorio `public`.

---

¡El proyecto está listo para ser probado! Si tienes dudas, consulta la documentación o contacta al desarrollador.