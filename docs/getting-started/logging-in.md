---
title: Logging In
order: 2
---

# Logging In

Before you can link your existing Laravel Forge sites to your local directores - or create new sites - you will need to authenticate and login with your 
Laravel Forge credentials.

You can do this via:

```shell script
forge login
```

This script will ask you for your Laravel Forge login credentials to create an API token, that will be used for additional requests.

The API token will be stored in your home directory: `~/forge/config.php`.

Now you can go and either [create a new site, or link an existing site from Forge](/docs/forge-cli/basic-commands/init).
