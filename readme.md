Eshop skeleton
=================

An eshop web app skeleton made with  [Nette framework](https://nette.org/en/)


Requirements
------------

This Web Project is compatible with Nette 3.2 and requires PHP 8.4.

Use MySQL as database.

Install Vite and other packages to be able to build assets (npm is required)
```
	npm install
```

Installation
------------

1. To install the App run
```
	git clone https://github.com/karel-e-nakladal/eshop-skeleton
```
~2. Migrate the database~
```
	php bin/migrate
```
3. Build assets
```
	nmp run build
```

Dont forget to copy `config/secret.neon.template` to `config/secret.neon` and set correct information.

Developement
------------

Start the PHP built-in web server
```
	php -S localhost:8000 -t www
```
Then, open `http://localhost:8000` in your browser to test the app.

To have MHR enabled run
```
	nmp run dev
```

To createt account with Administratice privilages run

```
	php bin/createAdmin.php
```

then type in an email, username and password.

Web Server Setup
----------------

For Apache or Nginx users, configure a virtual host pointing to your project's `www/` directory.

**Important Note:** Ensure `app/`, `config/`, `log/`, and `temp/` directories are not web-accessible.
Refer to [security warning](https://nette.org/security-warning) for more details.

