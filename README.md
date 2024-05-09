# Time Tracker

Freelancer Time Tracking applications

## Table of Contents

- [Installation](#installation)
- [Usage](#usage)

## Installation

1. **Clone the repository:**
    ```bash
    git clone git@github.com:kaziharun/time-tracker.git
    ```

2. **Install dependencies:**
    ```bash
    composer install
    ```

3. **Set up environment variables:**
    - Modify the `.env` file to set up database connection.


4. **Database Setup:**
    ```bash
    php bin/console doctrine:database:create
    php bin/console doctrine:migrations:migrate
    ```
   
5. **Seeder :**
    ```bash
    php bin/console doctrine:fixtures:load
    ```
   
6. **Run the Symfony Server:**
    ```bash
    symfony server:start
    ```

7. **Access the application:**
   Open a web browser and go to `http://localhost:8000`


8. **User Access:**
   - User: `tom`
   - Password: `user123`


## Usage

Provide instructions on how to use the application or any relevant usage information.
