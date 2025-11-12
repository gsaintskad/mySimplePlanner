
# mySimplePlanner

A simple task planner with a PHP API backend and a Next.js frontend, all running via Docker Compose.

## 🚀 Getting Started

This project is fully containerized. You only need Docker and Docker Compose installed to run it.

### 1. Environment Setup

Before you can run the application, you must create an `.env` file for your configuration.

1.  Copy the example file `.env.example` to a new file named `.env`:

    ```bash
    cp .env.example .env
    ```

2.  Open the new `.env` file in a text editor and customize the values.

    **Variable Explanations:**

    - `NGINX_PORT`: The port on your host machine (e.g., `8080`) where the application will run.
    - `PMA_PORT`: The port for the phpMyAdmin database tool (e.g., `8081`).
    - `MYSQL_HOST`: Leave this as `db`. It's the service name for the database inside Docker.
    - `MYSQL_DATABASE`: Your database name (e.g., `mysimpleplanner`).
    - `MYSQL_USER`: A username for the database (e.g., `planner_user`).
    - `MYSQL_PASSWORD`: A strong password for the `MYSQL_USER`.
    - `MYSQL_ROOT_PASSWORD`: A strong password for the database `root` administrator.
    - `JWT_SECRET`: **Crucial for security.** Enter a long, random string here. You can generate one in your terminal with: `openssl rand -base64 32`.

### 2. Build and Run the Application

Once your `.env` file is ready, you can launch the entire application stack with a single command:

```bash
sudo docker compose up --build -d
```


- `sudo`: Required if your user account is not in the `docker` group.
- `docker compose up`: Starts all services defined in `docker-compose.yml`.
- `--build`: Forces Docker to rebuild the images (like your Next.js app) if you've made changes.
- `-d`: Runs the containers in detached mode (in the background).

### 3\. Accessing Your Application

After the containers are up and running, your application will be available at:

- **Main Application (Frontend)**: `http://localhost:8080` (or your custom `NGINX_PORT`)
- **phpMyAdmin**: `http://localhost:8081` (or your custom `PMA_PORT`)

```

```
