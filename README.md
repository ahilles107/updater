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
php bin/updater generate reference version description maintainer update-type [source] [target] [comparePath] [exclude1] ... [excludeN]
```

```
Arguments:

 reference             COMMIT or TAG
 version               Release version
 description           Release description
 maintainer            Package mainatainer
 update-type           Update package type (e.g. minor, critical etc.
 source                the source directory, defaults to current directory
 target                the target directory, defaults to 'packages/'
 comparePath           path in the repository from which you want to generate a package, defaults "./"
 exclude               files or directories to exclude from package
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

```
Arguments:

 file                  path to update package
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

```
Arguments:

 target                Your application directory you want to update
 temp_dir              Directory to your application temp/cache folder
 package_dir           Package real path (path to your zip package)
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
