# Accredify Assessment

This is the `accredify-assessment` Laravel project, a system designed to manage and validate assessment data.

## Table of Contents

-   [Requirements](#requirements)
-   [Installation](#installation)
-   [Usage](#usage)
-   [API Endpoints](#api-endpoints)

## Requirements

-   PHP 8.1 or higher
-   Composer
-   Laravel 11
-   MySQL or any other supported database

## Installation

1. Clone the repository:

    ```bash
    git clone https://github.com/zairizartisan/accredify-assessment.git
    cd accredify-assessment

    ```

2. Run Composer Install:

    ```bash
    composer install

    ```

3. Run Migration:

    ```bash
    php artisan migrate
    ```

4. Run Seeder:

    ```bash
    php artisan db:seed
    ```

## Usage

-   Login
    -   email: `testuser@login.test`
    -   password: `123123123`

## API Endpoints

-   `/login` (POST)
-   `/verifications/store` (POST)
-   [API Documentation](https://app.swaggerhub.com/apis/ZAIRIZARTISAN/accredify-assessment-api/1)
