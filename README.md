# 🏍️ MOTOCONS Backend (API REST)

Backend oficial del sistema de gestión para el almacén y taller de motocicletas **MOTOCONS**, desarrollado con **Laravel 12 / PHP 8.3+** bajo **Arquitectura Hexagonal (Puertos y Adaptadores)** y persistencia en **PostgreSQL**.

---

## 📚 Documentación Técnica para Desarrolladores

Para comprender la arquitectura y cómo aportar en las distintas capas, consulta las guías dedicadas:

* [🏛️ **Guía de Arquitectura Hexagonal y Flujo de Comunicación**](docs/ARQUITECTURA_HEXAGONAL.md)
* [🧠 **Guía de la Capa de Dominio y Sintaxis PHP 8.3+**](docs/GUIA_CAPA_DOMINIO.md)

---

## 🚀 Guía de Instalación y Puesta en Marcha (Para Compañeros)

Sigue estos pasos para clonar y ejecutar el proyecto en tu entorno local:

### 1. Clonar el repositorio
```bash
git clone https://github.com/Cadavid06/Motocons-Backend.git
cd Motocons-Backend
```

### 2. Instalar dependencias con Composer
```bash
composer install
```

### 3. Configurar variables de entorno
Copia la plantilla de entorno:
```bash
cp .env.example .env
```
Genera la clave de la aplicación:
```bash
php artisan key:generate
```

Edita tu archivo `.env` con las credenciales de tu PostgreSQL local:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=motocons_db
DB_USERNAME=postgres
DB_PASSWORD=tu_password
```

### 4. Ejecutar las migraciones
Crea la base de datos `motocons_db` en tu pgAdmin y corre las migraciones:
```bash
php artisan migrate
```
> *(Para reiniciar la base de datos limpia en cualquier momento puedes usar `php artisan migrate:fresh`).*

### 5. Iniciar el servidor de desarrollo
```bash
php artisan serve
```
La API quedará escuchando en `http://127.0.0.1:8000`.

---

## 🏛️ Estructura del Proyecto

* **`app/Domain/`**: Capa pura e independiente. Contiene las 24 entidades POPO y excepciones de negocio sin acoplamiento a ORMs.
* **`app/Application/`**: Casos de uso (`Ports/In` y `Services`), DTOs y contratos de repositorios (`Ports/Out`).
* **`app/Infrastructure/`**: Adaptadores primarios (Controladores HTTP) y secundarios (Modelos y Repositorios Eloquent para PostgreSQL).
* **`app/Shared/`**: Traits y utilidades compartidas.
* **`database/migrations/`**: Definición de las 24 tablas del sistema en PostgreSQL.

---

## 👥 Equipo de Desarrollo
**Ingeniería de Sistemas - Universidad de Córdoba (Sede Sahagún)**