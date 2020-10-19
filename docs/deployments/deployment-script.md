---
title: Deployment Script
order: 2
---

# Deployment Script

After linking your project directory with Laravel Forge, your `forge.yml` file will contain the current deployment script that you have configured on Laravel Forge.

You can modify this script, by manually editing the `forge.yml` file.

```yaml
production:
  id: 1
  name: my-site
  server: 1
  quick-deploy: false
  deployment:
    - 'cd /home/forge/my-site'
    - 'git pull origin master'
    - '$FORGE_COMPOSER install --no-interaction --prefer-dist --optimize-autoloader'
    - ''
    - '( flock -w 10 9 || exit 1'
    - '    echo ''Restarting FPM...''; sudo -S service $FORGE_PHP_FPM reload ) 9>/tmp/fpmlock'
    - ''
    - 'if [ -f artisan ]; then'
    - '    $FORGE_PHP artisan migrate --force'
    - fi
  webhooks:
  daemons:
```

In order to apply the changed deployment script, you can either [push the configuration file to Forge](/docs/forge-cli/configuration/push) or [manually trigger a new deployment](/docs/forge-cli/deployments/deploy).
