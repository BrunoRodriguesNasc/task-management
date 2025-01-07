# Task Management API

A RESTful API for task management built with PHP, Slim Framework, and PostgreSQL.

## Features

- Complete CRUD operations for tasks
- Task status management (pending, in_progress, completed)
- User assignment
- Input validation
- Error handling
- Unit and Integration tests

## Requirements

- Docker
- Docker Compose

## Installation

1. Clone the repository:

```bash
git clone <repository-url>
cd task-management
docker-compose up -d
```

The application will be available at `http://localhost:8080`

## API Endpoints

### List all tasks

```bash
GET http://localhost:8080/api/tasks
```

### Get a task by ID

```bash
GET http://localhost:8080/api/tasks/{id}
```


### Create a task

```bash
POST http://localhost:8080/api/tasks
Content-Type: application/json
```

```json
{
    "title": "Implement REST API",
    "description": "Create a REST API using PHP and PostgreSQL",
    "due_date": "2024-01-15",
    "status": "pending",
    "user_id": 1
}
```

### Update a task

```bash
PUT http://localhost:8080/api/tasks/{id}
Content-Type: application/json
```

```json
{
    "title": "Implement REST API",
    "description": "Create a REST API using PHP and PostgreSQL - Updated",
    "due_date": "2024-01-20",
    "status": "pending",
    "user_id": 1
}
```

### Delete a task

```bash
DELETE http://localhost:8080/api/tasks/{id}
```

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


### Sample Users

```bash
John Doe (ID: 1)
Jane Smith (ID: 2)
```

## Running Tests

```bash

# Run unit tests only
docker-compose run --rm php-test ./vendor/bin/phpunit tests/Unit

# Run integration tests only
docker-compose run --rm php-test ./vendor/bin/phpunit tests/Integration
```

## API Documentation

The API documentation is available through Swagger UI, which provides an interactive interface to explore and test the endpoints.

### Accessing the Documentation

- **Swagger UI Interface**: [http://localhost:8080/api-docs](http://localhost:8080/api-docs)
  - Interactive documentation
  - Test endpoints directly from the browser
  - View request/response schemas
  - Example requests and responses

- **Raw OpenAPI Specification**: [http://localhost:8080/docs/openapi.yaml](http://localhost:8080/docs/openapi.yaml)
  - YAML format
  - Can be imported into other tools
  - Machine-readable API specification

### Features Documented

- Complete CRUD operations for tasks
- Request/response schemas
- Error responses
- Input validation rules
- Example payloads
- Status codes and their meanings

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

