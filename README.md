# Magento 2 Deployment tool

## Setup in a Server

0. Clone this repository into the server home dir:
    * `$ ssh://git@stash.staempfli.com:7999/stmagento/deployment-tool.git`
    
0. Composer install:
    * `$ cd deployment-tool && composer install`

0. Setup the local.xml template:
    * `$ cp deployment-tool/config/env.php.dist deployment-tool/config/env.php`
    * `$ vim deployment-tool/config/env.php`
    * Replace the variables between {{}}

0. Setup the specific project properties:
     * `$ cp deployment-tool/config/project.properties.dist deployment-tool/config/project.properties`
     * `$ vim deployment-tool/config/project.properties`
     * By default only the `git.project.repo` parameter is needed
     * You can change the default configuration in this file. Simple set here any of the default parameters into `deployment-tool/build/config/default.properties`. This file has a higher priority and will overwrite the default values.

0. Setup the maintenance window for this project:
    * `$ cp -r deployment-tool/config/maintenance.dist deployment-tool/config/maintenance`
    * `$ vim deployment-tool/config/maintenance/magento/pub/index.php`

0. Setup the static folder where all the static content specific from this server is contained. The content inside `magento`folder will be symlinked into the corresponding project `magento.dir`
    * `$ mkdir static`
    * Place all the Magento content inside a folder called `magento`
    * `$ mkdir static/magento`
    * You should add there the `media` and `var` folders but any other folders and files are also possible.
    * You can also call your static folder with a different name, just setup the new name into the `config/project.properties`:
        * `server.static.dir=<custom_folder_path>`

### Tips:

* Customize default properties
    * Almost everything can be customized via properties into the `config/project.properties`. If your server has a different configuration than defined into the `build/config/default.properties`, you can modify that setting up you desired configuration into `config/project.properties`. This file is loaded first, so it has a higher priority. 

* Speed up release process on dev servers:
    * For ***dev servers*** we will usually want to deploy always the most recent version of the project on the develop branch. We can skip then the question to specify a version doing the following:
        * `$ vim config/project.properties`
        * add the following parameter `release.version=snapshot`
    * On this server we do not usually need to create a database backup either. We can skip his step like that:
         * `$ vim config/project.properties`
        * add the following parameter `skipDatabaseBackup=1`

## How to use it in a project
Just go into the deployment-tool `cd deployment-tool` and use the available commands as follows:

* List available targets:
	* `$ bin/phing -l`

* New release:
	* `$ bin/phing release`
	
### TIPS:
- To release the most recent version in develop without the need to create a new tag in the repo, you can use `snapshot` as release version.

## Prerequisites

- PHP >= 5.4.*

## Recommended project structure on server:

```
  | - deployment-tool
  | - releases
  | - static
    | - magento
        | - media
        | - var
        
```