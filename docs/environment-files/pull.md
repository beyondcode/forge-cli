---
title: Pull environment files
order: 2
---

# Pull environment files

The `forge env:pull` command allows you to pull the current environment file of your Laravel Forge site to your local filesystem.

The naming convention is:

`.env.forge.[ENVIRONMENT]`

So by running `forge env:pull`, Forge CLI will write the current environment file to `.env.forge.production`.
Running `forge env:pull staging` would create a file called `.env.forge.staging`.
