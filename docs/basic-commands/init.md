---
title: Linking projects with sites
order: 1
---

# Init

The `forge init` command allows you to link the current working directory with a site and server on Laravel Forge.

When calling `forge init`, you can choose from an interactive list, which server you want to link the site with.
![](/img/init_servers.png)

After selecting the server, you can either go and create a new site on Forge, or link the directory with an already existing site on the selected server:
![](/img/init_sites.png)

Once the site is linked/created, Forge CLI will create a file called `forge.yml` in your current working directory. 
This file contains the current site (and server) configuration that you have.

You can safely put this file into version control to later synchronize changes to Forge.
