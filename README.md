Gplus to Rss Server
============================

Application that generates a RSS feed from a gplus profile.

## Launch with docker

First include your google API key in the code:

/* API key to connect to Google API */
$apiKey = 'YOUR API KEY';

You can generate it from:
https://console.developers.google.com

Launch the application using docker:

```bash
docker run -d -p 80:80 --name my-apache-php-app -v "$PWD":/var/www/html php:7.0-apache
```

If you want to access via localhost, you can use the following url:
http://127.0.0.1/?profile=%2BGoogle

## Other paramters

You can provide the following query parameters:

- **profile (mandatory)**: Profile ID of google+ profile
- **format (optional)**: Valid formats are
  - default: Nothing will be changed from original source
  - twitter: The title will be trimmed to have maximum 140 characters
 
Examples:
http://127.0.0.1/?profile=%2BGoogle&format=twitter
