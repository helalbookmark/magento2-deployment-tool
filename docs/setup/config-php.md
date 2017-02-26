# Setup config.php

Best practise to keep the Magento2 `config.php` file in your project repo is to create a symlink to `app/etc/` after composer install/update process:

0. Create a `symlinks` folder in your project root with the following content:

	```
	| - symlinks
		| - magento
			| - app
				| - etc
					| - config.php
	```

0. Edit `composer.json` scripts to create symlinks on every install/update:

	```
	"scripts": {
       "replaceMagentoFilesWithSymlinks": "cd symlinks && find . -type f -exec ln -sf `pwd`/{} ../{} \\;"
    "post-install-cmd": [
      "@replaceMagentoFilesWithSymlinks"
    ],
    "post-update-cmd": [
      "@replaceMagentoFilesWithSymlinks"
    ]
  }
	```
