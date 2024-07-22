# HUG market

## Install this application:
make up
make migrate


## Run tests:
make qa


## API documentation:
API is specified in file /openapi.yaml.
You can copy file contents to https://editor.swagger.io left window
and see API documentation in the right window.


## Use Postman for API utilization:
GET,POST http://localhost/api/v1/todos
GET,PUT,DELETE http://localhost/api/v1/todos/{todoId}


## Stop application:
make down
