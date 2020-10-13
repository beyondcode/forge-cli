# Forge CLI

An opinionated Laravel Forge CLI tool.

## Installation

```sh
git clone https://github.com/beyondcode/forge-cli.git # clone repo to a local folder
cd forge-cli # change into the created directory
composer install # install dependencies
```

You can then use it from this folder like so:

```sh
./forge-cli login
```

Or make sure this folder is in your `$PATH`, by adding the full path of the `forge-cli` folder to the `$PATH` variable in your `.bashrc` / `.bash_profile` etc.

### Update

```sh
git pull # sync with the latest code
composer update # update dependencies
```

## Usage

Use `forge login` to create a Forge API Token that will be used for future API requests.

To link an existing project with Laravel Forge, call `forge init`.
This command can also create a new site on Forge if you want.

## env files

You can pull down the environment file that is currently used on Forge using `forge env:pull`.
This will write a file called `.env.forge`.

To push this file to Forge again, call `forge env:push`.

## nginx config

You can pull down the site nginx configuration file using `forge nginx:pull`.
This will write a file called `nginx-forge.conf`.

To push this file to Forge again, call `forge nginx:push`.

You can also deploy the current Forge project again by running `forge deploy`.
If you add the `--update-script` option, this will use the deployment script configured in your `forge.yml` file and update it prior to deploying your site.

## Syncing configuration

Once you have made changes to your `forge.yml` file, use the `forge config:push` command to synchronize your local settings to Forge.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email `marcel@beyondco.de` instead of using the issue tracker.

## Credits

- [Marcel Pociot](https://github.com/mpociot)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
