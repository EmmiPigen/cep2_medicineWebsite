# Forglem Mig Ej
## Requirements

Before you begin, ensure you have met the following requirements:

- **PHP:** 8.1 or higher
- **Composer:** (Use scoop, if you have it, to install on Windows: `scoop install composer` or homebrew on macOS `brew install composer`)
- **Symfony CLI:** (optional but recommended) (Use scoop to install on Windows: `scoop install symfony-cli` or homebrew on macOS `brew install symfony-cli/tap/symfony-cli`)
- **A web server:** (e.g., Apache, Nginx, or the Symfony built-in server) 
- **A database:** MySQL (I would recommend using MariaDB, which is a fork of MySQL and is fully compatible with it.)

## Getting Started ##
First you need to set up PHP to include the necessary extensions. You can do this by editing the `php.ini` file. You can find the `php.ini` file in the PHP installation directory.
If you don't have a `php.ini` file, you can create one by copying the `php.ini-development` or `php.ini-production` file and renaming it to `php.ini`.

I have included a `php.ini` file in the folder called `Vigtige Filer` which you can use to replace the `php.ini` file in your PHP installation directory.
Just copy the `php.ini` file from the `Vigtige Filer` folder and paste it in the PHP installation directory.

## Installation 
**If you have the Symfony CLI installed, you can use the built-in server to start the application. Otherwise, you can use a web server like Apache or Nginx.**
If you have the symfony CLI installed, you can use the following commands to ensure that the necessary PHP extensions are installed:
```bash
symfony check:requirements
```

1. Clone the repository and navigate to the project directory:
  ```bash
  git clone https://github.com/your-username/cep2_medicineWebsite.git
  cd cep2_medicineWebsite
  ```

2. Install the required dependencies using Composer:
  ```bash 
  composer install
  ```

3. Set up environment variables:
  Copy `.env` to `.env.local` and configure your database and other settings:
  ```bash
  copy .env .env.local 
  ```
edit the `.env.local` file to configure your database connection:
  ```bash
  # .env.local
  DATABASE_URL="mysql://db_user:db_password@db_host:<port>/db_name?serverVersion=10.5.8-MariaDB"
  ```
  change the `db_user`, `db_password`, `db_host`, `port`, and `db_name` to match your database credentials that you have created.
  only add a password if you have set a password for the database user.

4. Create the database:
Make sure that the database server is running and you have the correct credentials in the `.env.local` file. Then run the following command to create the database:
  ```bash
  php bin/console doctrine:database:create
  ```
 
5. Run database migrations:
  ```bash
  php bin/console doctrine:migrations:migrate
  ```

6. Start the development server:
If you have the Symfony CLI installed, you can use the built-in server:
  ```bash
  symfony server:start
  ```
Add `--allow-all-ip` to the command to allow access from all IP addresses. This is useful if you want to access the application from a different device on the same network.
  ```bash
  symfony server:start --allow-all-ip
  ```
if you add `-d` to the command, it will run the server in the background,  and can use the terminal for other commands.
  ```bash
  symfony server:start -d --allow-all-ip
  ```	

Use `symfony server:stop` to stop the server. Or use control + c to stop the server if you are not using the `-d` option.

If you are using a web server like Apache or Nginx, you can configure the server to point to the `public` directory.
For apache, you can create a virtual host configuration file, by editing the `httpd-vhosts.conf` file or the `httpd.conf` file and adding the following configuration:
  ```apache
  <VirtualHost *:80>
      ServerName symfony-site.local

  <FilesMatch /.php$>
	  SetHandler proxy:fcgi://0.0.0.0:9000> #Change the port to match your PHP-FPM configuration
  </FilesMatch>

      DocumentRoot /path/to/symfony-site/public
      <Directory /path/to/symfony-site/public>
        AllowOverride None
        Require all granted
        FallbackResource /index.php
    </Directory>

      ErrorLog /var/log/apache2/symfony-site_error.log
      CustomLog /var/log/apache2/symfony-site_access.log combined
  </VirtualHost>
  ```
  Don't forget to restart the Apache server after making changes to the configuration. 

7. Open your browser and navigate to `http://localhost:<port>` to view the application.
Default port is 8000, so you can navigate to `http://localhost:8000` to view the application, if you are using the Symfony built-in server, or type `symfony open:local` to open the application in your default browser.
If you are using Apache, you can navigate to `http://symfony-site.local` to view the application.

For more information, see the [Symfony documentation](https://symfony.com/doc/current/setup.html).

## License ##
This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
