# Aventones-2-Web-I
🚗 Aventones – Sistema de Compartir Viajes

Aventones es una aplicación web desarrollada como proyecto académico para la carrera de Ingeniería del Software en la Universidad Técnica Nacional (UTN), Costa Rica.
El sistema permite la gestión de viajes compartidos entre conductores y pasajeros, facilitando la organización, reserva y administración de viajes de forma estructurada y segura.

📌 Propósito del Proyecto

El propósito principal de Aventones es:

Facilitar la conexión entre conductores y pasajeros que comparten rutas similares.

Brindar una plataforma organizada para la publicación, búsqueda y reserva de viajes.

Aplicar correctamente el patrón MVC utilizando CodeIgniter 4.

Implementar buenas prácticas de arquitectura de software, separación de responsabilidades y control de flujo.

Servir como un proyecto académico completo que integre frontend, backend, base de datos y lógica de negocio.

🧠 Lógica General del Sistema

El sistema funciona bajo el siguiente flujo lógico:

Usuarios

Registro de usuarios (choferes y pasajeros).

Inicio de sesión tradicional y login sin contraseña (passwordless).

Gestión de perfil (datos personales y configuración).

Conductores

Registro y administración de vehículos.

Publicación de viajes indicando origen, destino, fecha, hora, asientos y tarifa.

Gestión de reservas recibidas (aceptar o rechazar).

Pasajeros

Búsqueda pública de viajes disponibles.

Solicitud de reservas.

Visualización de reservas activas y pasadas.

Reservas

Flujo completo de creación, aceptación, rechazo y cancelación.

Notificaciones y validaciones de estado.

Administración

Panel administrativo.

Reportes y visualización de información relevante del sistema.

Comandos CLI para tareas internas (notificaciones, seeds, etc.).

🏗️ Arquitectura del Proyecto

El proyecto está construido utilizando el patrón MVC (Model–View–Controller), implementado mediante CodeIgniter 4.

📂 Estructura principal
app/
 ├── Commands        # Comandos CLI personalizados
 ├── Config          # Configuración general del framework
 ├── Controllers     # Controladores MVC
 ├── Database        # Migrations y Seeds
 ├── Filters         # Filtros de autenticación y acceso
 ├── Helpers         # Funciones auxiliares
 ├── Language        # Archivos de idioma
 ├── Libraries       # Clases de soporte
 ├── Models          # Modelos de base de datos
 ├── Services        # Lógica de negocio
 ├── ThirdParty      # Integraciones externas
 └── Views           # Vistas (Frontend PHP)

public/
 └── assets          # CSS, JS e imágenes

🔁 Separación de Responsabilidades
🔹 Controllers

Manejan las solicitudes HTTP.

Validan datos de entrada.

Coordinan la comunicación entre vistas y servicios.

No contienen lógica de negocio pesada.

🔹 Services

Contienen la lógica de negocio principal del sistema.

Manejan reglas, validaciones complejas y procesos internos.

Permiten reutilización y escalabilidad.

🔹 Models

Representan las tablas de la base de datos.

Ejecutan consultas y operaciones CRUD.

Mantienen la integridad de los datos.

🔹 Views

Se encargan exclusivamente de la presentación.

No contienen lógica de negocio.

Muestran la información enviada por los controladores.

⚙️ Tecnologías Utilizadas
Backend

PHP 8+

CodeIgniter 4

Arquitectura MVC

PHPMailer (envío de correos)

CLI Commands (php spark)

Frontend

HTML5

CSS3

JavaScript (Vanilla JS)

Diseño responsive básico

Base de Datos

MySQL / MariaDB

Migraciones y Seeds de CodeIgniter

Control de Versiones

Git

GitHub

🔐 Seguridad y Buenas Prácticas

Separación clara de capas (MVC).

Uso de filtros para control de acceso.

Validación de datos en backend.

Protección de carpetas sensibles (writable ignorado por Git).

Manejo de sesiones y autenticación controlada.

🎯 Objetivo Académico

Este proyecto tiene como objetivo demostrar:

Comprensión del patrón MVC.

Aplicación de una arquitectura limpia y organizada.

Uso correcto de CodeIgniter 4.

Integración completa de frontend, backend y base de datos.

Buen manejo de Git y estructura de commits.

👨‍💻 Autor

Proyecto desarrollado por Juan Daniel Gómez
Carrera: Ingeniería del Software
Universidad Técnica Nacional (UTN) – Costa Rica

Si quieres, en el siguiente mensaje puedo:
