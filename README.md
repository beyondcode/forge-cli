# Forge CLI

An opinionated Laravel Forge CLI tool.

## Usage

Use `forge login` to create a Forge API Token that will be used for future API requests.

To link an existing project with Laravel Forge, call `forge init`.
This command can also create a new site on Forge if you want.

 You can pull down the environment file that is currently used on Forge using `forge env:pull`.
 This will write a file called `.env.forge`.
 
 To push this file to Forge again, call `forge env:push`.
 
 You can also deploy the current Forge project again by running `forge deploy`.
 If you add the `--update-script` option, this will use the deployment script configured in your `forge.yml` file and update it prior to deploying your site.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email marcel@beyondco.de instead of using the issue tracker.

## Credits

- [Marcel Pociot](https://github.com/mpociot)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
