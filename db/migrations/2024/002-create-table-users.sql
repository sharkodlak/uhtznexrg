CREATE TABLE books (
	book_id VARCHAR(8) NOT NULL
		PRIMARY KEY,
	author VARCHAR(255) NOT NULL,
	title VARCHAR(255) NOT NULL,
	genre VARCHAR(255) NOT NULL,
	price DECIMAL(10, 2) NOT NULL,
	publish_date TIMESTAMP NOT NULL,
	description TEXT NOT NULL,
	CONSTRAINT books_author_title_unique UNIQUE (author, title)
);