---
title: Pull configuration from Forge
order: 2
---

# Pull configuration from Forge

Forge CLI works best, if you use your `forge.yml` as the one way to configure and modify your Laravel Forge sites.

But there will be situations, where you, or someone else, have updated parts of your site configuration directly on Laravel Forge.

In this case, your local `forge.yml` file might be outdated and no longer contain the current site configuration.

To reload the configuration from Forge, you can use the `forge config:pull` command. This command will overwrite all local changes in your `forge.yml` file and replace it with the current settings from Forge.
