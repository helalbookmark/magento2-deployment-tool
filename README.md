# Magento 2 Deployment tool

## Installation

Global installation using composer is required.

0. Composer require:

	```
	composer global require "staempfli/magento2-deployment-tool":"dev-master"
	```

0. Check you global composer `bin-dir` configuration:

	```
	composer global config -l | grep "bin-dir"

	```

0. Add path from previous step into your `$PATH` configuration:
0. Open a new console tab and check that `mg2-deployer` tool is found

	```
	which mg2-deployer

	```

## Setup

0. File `config.php` is required to come within the project cloned.

	* You can follow the following documentation if you do not have your project configured like that yet:
		* [docs/setup/config-php.md](docs/setup/config-php.md)

0. Create Database:

	```
	 CREATE DATABASE <database_name>;
	 CREATE USER `<database_user>`@`<database_host>` IDENTIFIED BY "<user_password>";
     GRANT ALL ON <database_name>.* TO `<database_user>`@`<database_host>`;
	```

0. Run Setup: (this might take several minutes because of magento compilation)

	```
	mg2-deployer setup
	```

0. At the end of the process you should get a folder structure similar to this:

```
  | - backups
  | - deployment-settings
  | - public_html (Symlink)
  | - releases
  | - static
    | - magento
    	| - app (etc/env.php)
    	| - pub
    	| - var

```

## Usage

Tool must be executed at the path where the the project will be deployed.

* Deploy new releases:

	```
	$ mg2-deployer release
	```

* List all avaiable commands:

	```
	$ mg2-deployer -list
	```


## Custom Configuration


### Properties

You can customise all properities according to your needs:

* Properties added in `deployment-settings/project.properties` have the highest priority and will overwrite default ones
* You can check all default properties that can be customised on:
	* [build/config/default.properties](build/config/default.properties)

### Maintenance Window

You can edit the maintenance file with your own design:

```
vim deployment-settings/templates/maintenance/${magento.dir}/pub/index.php

```

### Static content & Symlinks

* Static content that is only relevant on the server will be kept into the `static` folder.
* Symlinks are created automatically on released project during every deployment.
* You can add your custom files into `static/magento` and they will be automatically symlinked.

### Scripts

You can set custom scripts to run at the end of the release process on the following file:

```
cp deployment-settings/scripts/release-after.sh.dist deployment-settings/scripts/release-after.sh
vim deployment-settings/scripts/release-after.sh
```


## Tips:

### Speed up deployment process on dev-servers:

* Release most recent version of develop branch `-Drelease.version=snapshot`
* Skip database backup step `-DskipDatabaseBackup`
* You can even set these options by default on `deployment-settings/project.properties`:

    ```
    vim deployment-settings/project.properties
    release.version=snapshot
    skipDatabaseBackup=1

    ```

## Troubleshooting

####Starting compilation

* **Error**: Something went wrong while compiling generated code. See the error log for details.

* **Solution**: Increase php `memory_limit` configuration to 728M o 1024M


## Prerequisites

- PHP >= 7.0.*
- MAGENTO >= 2.1.*