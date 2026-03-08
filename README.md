# SmartSchool Backend – Sistema de Gestión de Eventos Escolares

![Sistema de Gestión de Eventos Escolares](./evento_escolar_al_aire_libre.png)

---

## 📌 Descripción

El **Backend de SmartSchool** está desarrollado con **PHP y Laravel** y se encarga de gestionar la lógica del sistema, procesar solicitudes y administrar la base de datos del Sistema de Gestión de Eventos Escolares.

Este backend expone una **API REST** que permite al frontend interactuar con la plataforma para registrar eventos, gestionar usuarios, administrar recursos y enviar notificaciones.

---

## 🎯 Objetivo

Desarrollar un backend robusto y escalable que permita centralizar la información y automatizar los procesos relacionados con la gestión de eventos escolares.

---

## ⚙️ Funcionalidades Principales

El backend permite:

- Gestión de usuarios (registro, autenticación y roles).
- Gestión de eventos escolares.
- Asociación de recursos a eventos.
- Gestión de notificaciones.
- Generación de reportes.
- Administración de información institucional.

---

## 🛠️ Tecnologías Utilizadas

- **PHP**
- **Laravel**
- **PostgreSQL**
- **API REST**
- **JSON**

---

## 🗄️ Base de Datos

El sistema utiliza **PostgreSQL** para almacenar información relacionada con:

- Usuarios
- Eventos
- Recursos
- Notificaciones
- Reportes

---

# 🏗️ Arquitectura del Sistema

El backend sigue una arquitectura por capas para mantener una separación clara de responsabilidades dentro del sistema.
Cliente
│
▼
Controllers
│
▼
Services
│
▼
Repository
│
▼
Models
│
▼
Base de Datos (PostgreSQL)


---

## 📚 Descripción de Capas

### Controllers
Se encargan de recibir las solicitudes HTTP y enviar respuestas al cliente.  
Delegan la lógica de negocio a los **Services**.

### Requests
Gestionan la validación de datos enviados por el cliente antes de que lleguen a los controladores.

### Resources
Transforman los modelos en respuestas JSON estructuradas para la API.

### Services
Contienen la **lógica de negocio del sistema**, coordinando operaciones entre controladores y repositorios.

### Repository
Encapsula el acceso a datos y las consultas a la base de datos, manteniendo separada la lógica de persistencia.

### Models
Representan las entidades de la base de datos utilizando **Eloquent ORM**.

### Providers
Registran servicios dentro del contenedor de dependencias de Laravel.

### Database
Contiene las **migraciones y seeders** utilizados para definir y poblar la base de datos.

### Routes
Define los endpoints disponibles de la API.

---

