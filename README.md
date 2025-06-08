# PHP Blog System

A simple blogging platform built with PHP and MySQL.

## Features

- User authentication (login/logout)
- Create, read, update, and delete (CRUD) posts
- Image upload support for posts
- Responsive design with Bootstrap
- Basic security practices (prepared statements, input validation)

## Requirements

- PHP 7.0+
- MySQL
- Web server like Apache (e.g., XAMPP)

## Installation

1. Clone the repository:

   -git clone https://github.com/JosephJonathanFernandes/Simple-Blog-PHP.git
   
2. Create a MySQL database (e.g., blogdb) and import the database.sql file (if you have one) or run the SQL commands to create tables.

3. Update database connection settings in db.php:

-$servername = "localhost";
-$username = "root";
-$password = "";
-$dbname = "blogdb";

4. Place the project files in your web server root (e.g., htdocs for XAMPP).

5. Access the project in your browser at http://localhost/blog.

## Usage

-Register or create an admin user (via create_admin.php).

-Login and start creating posts.

-Manage posts with edit and delete options.

## Folder Structure

-index.php - Homepage listing posts

-create_post.php - Form to add new posts

-edit_post.php - Edit existing posts

-delete_post.php - Delete posts

-db.php - Database connection

-uploads/ - Folder to store uploaded images

## Security Notes

-Uses prepared statements to prevent SQL injection

-Basic input validation on forms

-Avoid uploading unsafe file types

## License

-This project is open-source and free to use.


