updater
=======

Easy update for your php application users.

Provide a tool for easy partial updates of your php app with standarized update packages.
Use Updater services to build your own update ui in your app admin panel.

[![Build Status](https://travis-ci.org/ahilles107/updater.svg?branch=master)](https://travis-ci.org/ahilles107/updater)


Generate package command:
-------------

This command allows you to create an update package in the target directory from given source
based on the differences in a git repository between the current state and a
specific git tree-ish. You can also exclude files and/or directories from update package

```
php bin/updater generate reference version description maintainer update-type [source] [target] [exclude1] ... [excludeN]
```

**Example:**

```
php bin/updater generate 89144ee17ce72370766e21d1a767fdbed0a9e8b7 4.3.1-RC "My test description" "Rafał Muszyński" minor /var/www/updater /var/www/updater/packages/ bin/phpunit sample_app
```

`bin/phpunit` and `sample_app` will be excluded from update package.

Validate package command:
-------------

```
php bin/updater validate file
```

**Example:**

```
php bin/updater validate spec/packages/4.3.1-RC.zip
```

Update application command:
-------------

```
php bin/updater update [--rollback] target temp_dir package_dir
```

**Example:**

```
php bin/updater update /var/www/updater/spec/sample_app/ /var/www/updater/spec/sample_app/cache/ /var/www/updater/spec/packages/4.3.1-RC.zip
```

**Rollback updated changes:**

```
php bin/updater update /var/www/updater/spec/sample_app/ /var/www/updater/spec/sample_app/cache/ /var/www/updater/spec/packages/4.3.1-RC.zip --rollback
```

License
-------

This library is under the MIT license. See the complete license in the repository:

    LICENSE


TODO:
-------------

- run pre update scripts
- run composer action
- run post update sripts
