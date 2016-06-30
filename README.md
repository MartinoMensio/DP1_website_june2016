# dp_web_jun16
Web programmin part of exam "Distributed programming I" first call of year 2016 (june)

published on Azure:
https://dp-web-jun16-martinomensio.azurewebsites.net/

In order to make this site work with the database on your PC, simply modify the config.php file line 76 to:
```php
$database = 'local';
```
and make sure that the localhost username/password/db_name are correct.

The requirements for the site are desribed in the [Requirements document](https://github.com/MartinoMensio/dp_web_jun16/blob/master/dp_web_jun16.pdf)

The site has been developed for the XAMPP environment (php 5.3.10, MySQL 5.5.49)
