### Installation



1. Clone repo
   ```sh
   git clone https://github.com/ajmalmpamd/raify
   ```
2. Install as dependencias
   ```sh
   composer install
   ```
3. .ENV
   ```sh
   cp .env.example .env
   ```
4. Create Database and make curresponding changes in .env file
5. Make migration
   ```sh
   php artisan migrate
   ```  
     
6. Run the Project
   ```sh
   php artisan serve
   ```  