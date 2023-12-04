# Workflow
Workflow is a platform for easily posting and applying for jobs, created in PHP for IS-115 at UiA. It utilizes PHP, MySQL, Bootstrap and FontAwesome.

## Installation

1. **Prerequisites:**
   Ensure PHP (minimum version 8.0) and MySQL (tested with 10.4.28-MariaDB) are installed on your server.

2. **Database Setup:**
   - Create a new database.
   - Execute the SQL script located in `SQL_FILES/tables.sql` to create the necessary tables.
   - Optionally, populate the database with test data using `SQL_FILES/testdata.sql`.

3. **Configure Database Connection:**
   - Open `assets/include/DBHandler.php`.
   - Update the following fields:
      - `$serverip`: The IP address to access MySQL (e.g., 192.168.0.1 or localhost).
      - `$username`: The MySQL username.
      - `$password`: The MySQL password.
      - `$dbname`: The name of the database created in step 2.

4. **Adjust Links:**
   - If some links are broken, modify the following line in `assets/include/template.php`:
     ```php
     $relativePathToRoot = str_repeat('../', (substr_count($currentURL, '/')-2));
     ```
    Make sure to change the number (currently set to '2') based on how many folders deep your files are located. If your files are in a different directory structure, you may need to adjust this number accordingly.
