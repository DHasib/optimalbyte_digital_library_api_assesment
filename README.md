# Optimalbyte Digital library System API Assesment- Local Development Guide

A simple Laravel-based RESTful API for managing Digital library System in the optimalbyte Int. Assesment system.

 

This project ships with a **script** that hides almost every Docker / Laravel command you normally have to remember. After cloning, you can be up and running with **one line** to setup everything will be done by automation.
**Note**  This project is Docker-based, so you must have Docker installed on your machine. If you’re using Docker Desktop, please ensure the Docker Desktop application is running.



**Prerequisites**

| Tool                  | Version (‑‑minimum)                                                         | Notes                                                                                            |
| --------------------- | --------------------------------------------------------------------------- | ------------------------------------------------------------------------------------------------ |
| **Docker Desktop**    | 20.10+                                                                      | Linux users: the Docker Engine daemon & CLI.                                                     |
| **Docker‑Compose v2** | Bundled with Docker Desktop ≥ 4.13 <br>or `docker compose` plugin on Linux. |                                                                                                  |
| **GNU Make**          | 4.x                                                                         | Comes pre‑installed on macOS & most Linux distros. Windows: install via WSL or make for Windows. |

> **No local PHP, Composer, MySQL, or Node required** – everything runs inside the `digital_library_api` container.



## Table of Contents

- [Setup & Run Instructions](#setup--run-instructions)  
- [API Documentation](#api-documentation)
  - [Admin Credential](#admin-credential) 
  - [Postman Api Collections](#postman-api-collections)
  - [Project Entity Relationship Diagram (ERD)](#erd)  


## Setup & Run Instructions

 **1 · Clone the repo**

```bash
git clone https://github.com/DHasib/optimalbyte_digital_library_api_assesment.git
cd optimalbyte_digital_library_api_assesment
```



**2 · One‑shot setup ( .env → build → up → composer install → migrate & seed)**

```bash
npm run setup
```

Behind the scenes this will:

1.  Copy `.env.dev → .env` if no `.env` exists
2. `docker compose build digital_library_api & ngnix` – build the PHP/Laravel image
3. `docker compose up -d digital_library_api & ngnix` – start the app & its dependencies
4. `composer install` – inside the container
5. `php artisan migrate:fresh --seed` – wipe & seed the database

When the command finishes, the API is available at **[http://localhost:8008](http://localhost:8008)** (or the port you mapped in `docker‑compose.yml`).

---

**3 · Daily workflow cheatsheet**

| Task                                    | NPM Script Command          |  
| --------------------------------------- | --------------------------- |
| Rebuild images after Dockerfile changes |  `npm run docker-build`     |
| Start/stop containers                   |  `npm run up /npm run stop` |
| Drop DB & reseed                        |  `npm run install`          |
| Run PHPUnit test suite                  |  `npm run migrate`          |
| Clear Laravel caches                    |  `npm run test`             |
| Tail the Laravel log                    |  `npm run clear`            |
| Tear everything down                    |  `npm run log`              |

> You can override the container name on the fly:<br>`SERVICE=my_alt_service make migrate`

---

**4 · Troubleshooting**

### Containers can’t see MySQL (`getaddrinfo ENOTFOUND mysqlDB`)

* Ensure `DB_HOST` in `.env` matches the DB service in `docker‑compose.yml` (e.g. `mysqlDB`).
* The DB service **must** have a health‑check, and `digital_library_api` should have `depends_on: condition: service_healthy`.

### Composer memory errors during `npm run setup`

Use the **swapfile** setting in Docker Desktop (or allocate more memory) – Laravel’s optimized autoloader needs \~1 GB during the install.

### Changing `.env`

After editing `.env`, run `make clear` to flush cached config.

---

**5 · CI / CD hint**

Because the Makefile is deterministic, GitHub Actions / GitLab CI only need:

```yaml
steps:
  - uses: actions/checkout@v4
  - uses: docker/setup-buildx-action@v3
  - run: make setup
  - run: make test
```


## Api-Documentation

## Admin-Credential

**It will Create Automatically When You Run Make Setup Command**

```bash
'email' = admin@example.com
'password' = P@ssword
```


## Postman Api Collections

Download and import these files into Postman:

- [📥 Download Postman Api Collection JSON](https://github.com/DHasib/optimalbyte_digital_library_api_assesment/releases/download/postman_api_collection_and_env/Optimalbyte_Digital_library_System_API_Collections.postman_collection_mdHasib522@gmail.com.json)
- [📥 Download Postman Env JSON](https://github.com/DHasib/optimalbyte_digital_library_api_assesment/releases/download/postman_api_collection_and_env/library_api_env.postman_environment_mdHasib522@gmail.com.json)

**Instructions**

1. In Postman click **Import → File**, and select:
   - `Collections_mdhasib522@gmail.com.postman_collection.json`  
   - `.postman_environment_mdhasib522@gmail.com.json`
2. In the top-right of Postman, choose the **digital_library_api_env** environment.
3. Set:
   - `digital_library_api_url` → your API’s base URL (e.g. `http://localhost:8000`)
   - `token` → your JWT/Bearer token  
4. Run the requests under **Digital Library Syaytem API**.

---


## ERD
<p align="center">
  <img
    src="https://raw.githubusercontent.com/DHasib/optimalbyte_digital_library_api_assesment/main/Digital_Library_ERD.png"
    width="400"
    alt="Project ERD"
  />
</p>
