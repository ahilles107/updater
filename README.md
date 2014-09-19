updater
=======

Easy update for your php application users.

Provide a tool for easy partial updates of your php app with standarized update packages.
Use Updater services to build your own update ui in your app admin panel.

Idea of usage:

**Generate package command:**

This command allows you to create an update package in the target directory from given source
based on the differences in a git repository between the current state and a
specific git tree-ish. You can also exclude files and/or directories from update package

```
php bin/updater generate reference version description maintainer update-type [source] [target] [exclude1] ... [excludeN]
```

Example:

```
php bin/updater generate 89144ee17ce72370766e21d1a767fdbed0a9e8b7 4.3.1-RC "My test description" "Rafał Muszyński" minor /var/www/updater /var/www/updater/packages/ bin/phpunit sample_app
```

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
