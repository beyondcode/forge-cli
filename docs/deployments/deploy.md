---
title: Triggering a deployment
order: 2
---

# Triggering a deployment

The `forge deploy` command will trigger a deployment on Laravel Forge for the currently linked site and the given environment.

You can also provide the `--update-script` option, to automatically update the deployment script on Laravel Forge with the latest deployment script that you have
configured in your forge.yml file.

After the deployment is done, you will see the latest deployment log output:
 
![](/img/deploy.png)

If you do not want to wait for the deployment to finish,you can provide the `--no-wait` option. This will only trigger the deployment on Forge, without waiting for the deployment result / deployment log.
