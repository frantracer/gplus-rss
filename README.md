Gplus to Rss Server
============================

Application that generates a RSS feed from a gplus profile.

## Launch with docker

First include your google API key in the code:

```bash
/* API key to connect to Google API */
$apiKey = 'YOUR API KEY';
```

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
- **title_max (optional)**: Maximum number of characters for the title
- **collection (optional)**: Filter entries by collection name. If undefined all entries are returned.
- **gplus_link (optional)**: Default is set to false. If True the url will be google+ entry instead of the source url
 
Examples:

http://127.0.0.1/?profile=%2BGoogle&title_max=140

http://127.0.0.1/?profile=%2BFranTraperoCerezo&collection=Tech%20Media&gplus_link=1
