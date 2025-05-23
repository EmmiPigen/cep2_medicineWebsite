# Forglem Mig Ej

## Requirements

Before you begin, ensure you have met the following requirements:

- **PHP:** 8.1 or higher
- **Composer:** (Use scoop, if you have it, to install on Windows: `scoop install composer` or homebrew on macOS `brew install composer`)
- **Symfony CLI:** (optional but recommended) (Use scoop to install on Windows: `scoop install symfony-cli` or homebrew on macOS `brew install symfony-cli/tap/symfony-cli`)
- **A web server:** (e.g., Apache, Nginx, or the Symfony built-in server)
- **A database:** MySQL (I would recommend using MariaDB, which is a fork of MySQL and is fully compatible with it.)

## Getting Started

First you need to set up PHP to include the necessary extensions. You can do this by editing the `php.ini` file. You can find the `php.ini` file in the PHP installation directory. If you don't have a `php.ini` file, you can create one by copying the `php.ini-development` or `php.ini-production` file and renaming it to `php.ini`.

I have included a `php.ini` file in the folder called `Vigtige Filer` which you can use to replace the `php.ini` file in your PHP installation directory. Just copy the `php.ini` file from the `Vigtige Filer` folder and paste it in the PHP installation directory.

## Installation

**If you have the Symfony CLI installed, you can use the built-in server to start the application. Otherwise, you can use a web server like Apache or Nginx.** If you have the symfony CLI installed, you can use the following commands to ensure that the necessary PHP extensions are installed:

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

3. Set up environment variables: Copy `.env` to `.env.local` and configure your database and other settings:

```bash
copy .env .env.local
```

edit the `.env.local` file to configure your database connection:

```bash
# .env.local
DATABASE_URL="mysql://db_user:db_password@db_host:<port>/db_name?serverVersion=10.5.8-MariaDB"
```

change the `db_user`, `db_password`, `db_host`, `port`, and `db_name` to match your database credentials that you have created. only add a password if you have set a password for the database user.

4. Create the database: Make sure that the database server is running and you have the correct credentials in the `.env.local` file. Then run the following command to create the database:

```bash
php bin/console doctrine:database:create
```

5. Run database migrations:

```bash
php bin/console doctrine:migrations:migrate
```

6. Start the development server: If you have the Symfony CLI installed, you can use the built-in server:

```bash
symfony server:start
```

Add `--allow-all-ip` to the command to allow access from all IP addresses. This is useful if you want to access the application from a different device on the same network.

```bash
symfony server:start --allow-all-ip
```

if you add `-d` to the command, it will run the server in the background, and can use the terminal for other commands.

```bash
symfony server:start -d --allow-all-ip
```

Use `symfony server:stop` to stop the server. Or use control + c to stop the server if you are not using the `-d` option.

If you are using a web server like Apache or Nginx, you can configure the server to point to the `public` directory. For apache, you can create a virtual host configuration file, by editing the `httpd-vhosts.conf` file or the `httpd.conf` file and adding the following configuration:

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

7. Open your browser and navigate to `http://localhost:<port>` to view the application. Default port is 8000, so you can navigate to `http://localhost:8000` to view the application, if you are using the Symfony built-in server, or type `symfony open:local` to open the application in your default browser. If you are using Apache, you can navigate to `http://symfony-site.local` to view the application.

For more information, see the [Symfony documentation](https://symfony.com/doc/current/setup.html).

## Making API Calls

To give the raspberry pi access to the database, you can make a http request at this address:

```bash
http://<address>/api/{event}/{userid}
```

Replace `<address>` with the address of the server, if using the Symfony built-in server, it will be `http://localhost:8000`. If using the googlable server, it will be `https://forglemmigej.duckdns.org`.

Replace `{event}` with the event you want to call, and `{userid}` with the user id you want to call. You can make either a GET or POST request to the API.

When making a POST request, use

```python
request.post('http://<address>/api/{event}/{userid}', json=data)
```

where `data` is an base64 encoded json object. What the json object should contain depends on the event you are calling.

### Udstyr Liste Info

When making a POST request with the event: `sendUdstyrListeInfo`, the json object should contain the following data:

````python
For example, to call the `sendUdstyrListeInfo` event, the json object should contain the following data:
```python
udstyrDataList = {
    "udstyrData": [
        {'enhed': "led_Lys1", "status": "tilsluttet", "power": 76, "lokale": "Soveværelse"},
        {'enhed': "buzzer1", "status": "tilsluttet", "power": 40, "lokale": "Stue"},
        {'enhed': "movement_sensor1", "status": "Low power", "power": 50, "lokale": "Køkken"},
        {'enhed': "motion_sensor1", "status": "Online", "power": 40, "lokale": "Badeværelse"},
    ]
}
````

So the json object should contain the headers: `enhed`, `status`, `power`, and `lokale`. The values of the headers should be the values you want to send to the server. The `udstyrData` key is a list of dictionaries, where each dictionary contains the data for one device. You can add as many devices as you want to the list.

The json object should be base64 encoded before sending it in the request. You can use the following code to encode the json object:

```python
udstyrDataList = json.dumps(udstyrDataList, indent=4)
udstyrDataList_bytes = udstyrDataList.encode('ascii')
base64_bytes = base64.b64encode(udstyrDataList_bytes)
base64_string = base64_bytes.decode('ascii')
```

Then you can send the base64 encoded string in the request:

```python
x = requests.post(url, json=base64_string)
```

The server will decode the base64 string and convert it back to a json object. The server will then process the request and return a response.

### Medication Registration Log

When making a POST request with the event: `MedicationRegistrationLog`, the json object should contain the following data:

```python
medicationLogEntry = {
  "medicationLog":
    {"name": "Paracetamol", "tagetTid": tid, "status": 1, "lokale": "Stue"},
}
```

So the json object should contain the headers: `name`, `tagetTid`, `status`, and `lokale`. The values of the headers should be the values you want to send to the server. The `medicationLog` key is a dictionary, where each key contains the data for one device. You can add as many devices as you want to the list. The `tagetTid` key should contain the time in the format: `2023-10-01 12:00:00`.

The `status` key should contain the status of the medication. The status can be either 1 or 0. 1 means that the medication has been taken, and 0 means that the medication has not been taken.

Just like the `udstyrDataList`, the json object should be base64 encoded before sending it in the request. You can use the same code to encode the json object:

```python
medicationLogEntry = json.dumps(medicationLogEntry, indent=4)

medicationLogEntry_bytes = medicationLogEntry.encode('ascii')
base64_bytes = base64.b64encode(medicationLogEntry_bytes)
base64_string = base64_bytes.decode('ascii')
```

Then you can send the base64 encoded string in the request:

```python
x = requests.post(url, json=base64_string)
```

The server will decode the base64 string and convert it back to a json object. The server will then process the request and return a response.

### Medication List

There is also one GET request you can make to the server. The GET request is used to get the list of medications for a user. You can use the following code to make the GET request:

```python
url = 'http://<address>/api/getUserMedikamentListe/{userid}'
x = requests.get(url)
```

The server will respond with a json object containing the list of medications for the user. The json object will contain three headers: `status`, `message`, and `list`. The `status` header will contain the status of the request. The `message` header will contain a message about the request. The `list` header will contain the list of medications for the user. The list will contain a list of dictionaries, where each dictionary contains the data for one medication. Each dictionary will contain the following headers: `name`, `timeInterval`, and `timesToTake`. The `name` header will contain the name of the medication, the `timeInterval` header will contain the time interval for taking the medication, and the `timesToTake` header will contain a list of times to take the medication.

below is an example of the response you will get from the server:

```python
{
   "status":"success",
   "message":"Request recieved successfully",
   "list":[
      {"name":"Ibuprofen", "timeInterval":15, "timesToTake":["08:00","12:00","16:00","20:00"]},
      {"name":"Paracetamol","timeInterval":30,"timesToTake":["20:00"]},
      {"name":"Zinc","timeInterval":60,"timesToTake":["14:00","22:00"]},
      {"name":"Concerta","timeInterval":30,"timesToTake":["19:00"]},
      {"name":"D-vitamin","timeInterval":15,"timesToTake":["15:00","20:00"]},
   ]
}
```
Since the response is encoded in base64, you will need to decode the response before using it. You can use the following code to decode the response:

```python
  encoded_data = response["list"]
  decoded_json = base64.b64decode(encoded_data).decode('utf-8')
  medikamentListe = json.loads(decoded_json)
```

The `medikamentListe` variable will now contain the list of medications for the user. You can then use this list to display the medications in your application.


See the file [python.ipynb](python.ipynb) for examples of how to make the requests and how to decode the responses.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
