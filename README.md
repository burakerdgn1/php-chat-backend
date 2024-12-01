# PHP Chat Application

A simple PHP chat application where users can join groups and send messages. This app includes a backend built with the Slim framework, which handles group creation, user management, and message posting. Users can join any group and send messages to it, which are periodically displayed in the console.

## Features

- Create groups
- Join groups and send messages
- View group messages periodically (no socket connections)
- Console-based client interface

## Installation

Follow these steps to set up the application:

### 1. Clone the repository

First, clone the repository to your local machine:

git clone https://github.com/burakerdgn1/php-chat-backend.git
cd php-chat-backend

2. Install PHP
macOS:
On macOS, you can install PHP using Homebrew
brew install php
Linux:
On Linux, you can install PHP using the package manager for your distribution.
For Ubuntu/Debian-based distributions:
sudo apt update
sudo apt install php php-cli php-xml php-mbstring php-sqlite3
For Fedora/CentOS-based distributions:
sudo dnf install php php-cli php-xml php-mbstring php-sqlite3
Windows:
On Windows, the easiest way to install PHP is by using XAMPP or WampServer. Both provide an installer with PHP included. You can also manually download PHP from the official website PHP Downloads.


3. Install Composer
Make sure you have Composer installed. Composer is a tool for dependency management in PHP. If you haven't installed it yet, follow the instructions on the official website.

curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

4. Install dependencies
After cloning the project, install the required libraries using Composer. Run the following command to install all dependencies defined in composer.json:

composer install

5. Install required libraries individually (if necessary)
If you need to manually install the required libraries, you can use the following commands:

slim/slim: The Slim framework for routing and HTTP handling:

composer require slim/slim:^4.14

slim/psr7: Provides PSR-7 (HTTP message interface) implementations, which are used by Slim for handling HTTP requests and responses:

composer require slim/psr7:^1.7

php-di/php-di: Dependency injection library to manage dependencies:

composer require php-di/php-di:^7.0

php-di/slim-bridge: Integrates PHP-DI with Slim, enabling dependency injection in Slim controllers:

composer require php-di/slim-bridge:^3.4

PHPUnit for testing (if required):

composer require --dev phpunit/phpunit


6. Set up the database
Make sure that the data/chat.db file exists. If it doesn't, you'll need to create it and run the necessary database migrations to create the tables (messages, groups, user_groups). You can either do this manually or automate it with the migration script.

If you need to run the migration script, execute the following command to create the necessary tables in the database:

php migrate.php

7. Install SQLite (if not installed)
SQLite is needed for the database.

macOS:
To install SQLite on macOS, you can use Homebrew:
brew install sqlite

Linux:
To install SQLite on Linux:
For Ubuntu/Debian-based distributions:
sudo apt install sqlite3

For Fedora/CentOS-based distributions:
sudo dnf install sqlite

Windows:
For Windows, you can download SQLite from the official website SQLite Download. Extract the downloaded file and add the path to the executable to your system's PATH variable.

7. Start the application
To run the application, you can use PHP's built-in web server. Run the following command from the project root:

php -S localhost:8000 -t public
This will start the application on http://localhost:8000.

8. Interact with the app
You can interact with the application using the console client, client.js, or by sending HTTP requests to the endpoints defined in the Slim app.

Full Steps to Run the client.js:
Set up your project:

If you haven't already, initialize your project by running:

npm init -y

Install the dependencies:

Run the following command to install node-fetch:

npm install node-fetch

Run the client:

Finally, run the client using the following command:

node client.js


Folder Structure
Here’s an overview of the project folder structure:

├── client.js              # Client-side JavaScript (console app)
├── composer.json          # Composer dependencies
├── composer.lock          # Composer lock file (ensures same dependencies)
├── data
│   └── chat.db            # SQLite database file
├── migrate.php            # Migration script (optional, if needed)
├── phpunit.xml            # PHPUnit configuration file
├── public
│   └── index.php          # Entry point for the Slim app (public-facing)
├── src
│   ├── Controllers        # Contains the main controllers (GroupController, MessageController)
│   ├── Database.php       # Database connection class
│   ├── Middlewares        # Middleware classes (e.g., validation)
│   ├── Repositories       # Database interaction (GroupRepository, MessageRepository)
│   ├── Routes             # Routes for groups and messages
│   ├── Services           # Business logic (GroupService, MessageService)
│   └── Validators         # Validation logic for group and message data
└── tests                  # Unit tests for controllers and services
    ├── GroupControllerTest.php
    ├── GroupServiceTest.php
    ├── MessageControllerTest.php
    └── MessageServiceTest.php

Usage
Once the app is running, users can interact with the chat system via the console client (client.js). Here's how the client works:

The user enters their ID when prompted.
The system checks if there is a matching entry in the user_groups table.
If the user is a member of any groups, those groups will be displayed.
The user can select a group and start chatting. The system will periodically display messages in that group.

Running Tests
To run the tests for the application, use PHPUnit:

php vendor/bin/phpunit --configuration phpunit.xml

This will execute all unit tests and give you feedback on the application’s functionality.

License
This project is licensed under the MIT License - see the LICENSE file for details.

