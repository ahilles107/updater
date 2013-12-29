updater
=======

Easy update for your php application users.

Provide a tool for easy partial updates of your php app with standarized update packages.
Use Updater services to build your own update ui in your app admin panel.

Idea of usage:

```
php ./updater -d /var/www/project/root update-4.3.1.zip
```

TODO:

* generate update package
* validate package
* apply update package
** Create Package class
** create temp dir for files
** run pre update scripts
** apply filmapping rules
** run composer action
** run post update sripts
