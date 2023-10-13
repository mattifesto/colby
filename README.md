# Colby

Colby is a content management system.

### To develop and create a test website:

- step 1:
    - start a Docker development environment with this git repository

- step 2:
    - read `initialize_development_environment.sh`
    - follow instructions
    - run `initialize_development_environment.sh` as root

### To reset development environment:

- step 1:
    - delete all files in /var/www/html

- step 2:
    - start a terminal session in the MySQL container
    - log in to MySQL as root
    - drop the `website_database` database
    - create the `website_database` database
