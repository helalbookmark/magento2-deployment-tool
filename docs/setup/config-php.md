# Setup config.php

Best practise to keep the Magento2 `config.php` file in your project repo is to create a symlink to `app/etc/` after composer install/update process:

0. Create a `symlinks` folder in your project root with the following content:

	```
	| - symlinks
		| - <magento_dir> (omit if magento installed on project root)
			| - app
				| - etc
					| - config.php
	```

0. Edit `composer.json` scripts to create symlinks on every install/update:

	```
	"scripts": {
       "replaceConfigPHP": "cd <magento_dir>/app/etc && ln -sf ../../../symlinks/<magento_dir>/app/etc/config.php"
    "post-install-cmd": [
      "@replaceConfigPHP"
    ],
    "post-update-cmd": [
      "@replaceConfigPHP"
    ]
  }
	```
