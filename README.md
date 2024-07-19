# HUG market

Install this application:
make up
make migrate

Import books:
make in
bin/console app:book:import

Run tests:
make qa

Use Postman for API utilization:
GET,POST http://localhost/api/v1/books
GET,PUT,DELETE http://localhost/api/v1/books/{book_id}

Stop application:
make down
