# Task Management API

[![PHP Version](https://img.shields.io/badge/PHP-8.1-blue.svg)](https://www.php.net)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-13-blue.svg)](https://www.postgresql.org)
[![Slim Framework](https://img.shields.io/badge/Slim-4.11-orange.svg)](http://www.slimframework.com)

A simple REST API for task management built with PHP, Slim Framework, and PostgreSQL.

## Features

- CRUD operations for tasks
- Task status management (pending, in_progress, completed) 
- User association with tasks
- Input validation
- Error handling
- Docker containerized application

## Requirements

- Docker
- Docker Compose
- Port 8080 available for the web server
- Port 5433 available for PostgreSQL

## Installation & Setup

1. Clone the repository
2. Configure environment (optional)
3. Start the containers

```bash
git clone <repository-url>
cd task-management
docker-compose up -d
```

The application will be available at `http://localhost:8080`

## API Endpoints

### List all tasks

GET http://localhost:8080/api/tasks

### Create a task

POST http://localhost:8080/api/tasks
Content-Type: application/json

```json
{
    "title": "Implement REST API",
    "description": "Create a REST API using PHP and PostgreSQL",
    "due_date": "2024-01-15",
    "status": "in_progress",
    "user_id": 1
}
```

### Update a task

PUT http://localhost:8080/api/tasks/{id}
Content-Type: application/json

```json
{
    "title": "Implement REST API",
    "description": "Create a REST API using PHP and PostgreSQL - Updated",
    "due_date": "2024-01-20",
    "status": "completed",
    "user_id": 1
}
```

### Delete a task

DELETE http://localhost:8080/api/tasks/{id}

## Database Schema

The application uses two main tables:

### Users Table

- id (SERIAL PRIMARY KEY)
- name (VARCHAR)

### Tasks Table

- id (SERIAL PRIMARY KEY)
- title (VARCHAR)
- description (TEXT)
- due_date (DATE)
- status (VARCHAR) - possible values: pending, in_progress, completed
- user_id (INTEGER) - foreign key to users table
- created_at (TIMESTAMP)

## Testing

You can test the API using tools like Postman or cURL. Sample test data is automatically loaded when the containers start.

### Sample Users
- John Doe (ID: 1)
- Jane Smith (ID: 2)

### Sample cURL Commands

1. List all tasks:
```bash
curl http://localhost:8080/api/tasks
```

2. Create a new task:
```bash
curl -X POST http://localhost:8080/api/tasks \
  -H "Content-Type: application/json" \
  -d '{
    "title": "New Task",
    "description": "Task Description",
    "due_date": "2024-01-15",
    "status": "pending",
    "user_id": 1
  }'
```

## Project Structure

```
.
├── docker/
│   ├── nginx/
│   │   └── default.conf
│   ├── php/
│   │   └── Dockerfile
│   └── postgres/
│       └── init.sql
├── src/
│   ├── Controllers/
│   │   └── TaskController.php
│   ├── Models/
│   │   └── Task.php
│   └── Routes/
│       └── api.php
├── public/
│   └── index.php
├── docker-compose.yml
└── composer.json
```

## Troubleshooting

1. If you encounter database connection issues, try:
```bash
docker-compose down -v
docker-compose up -d
```

2. To check container logs:
```bash
docker-compose logs
```

3. To access the PostgreSQL container:
```bash
docker-compose exec postgres psql -U task_user -d task_db
```

## License

MIT License