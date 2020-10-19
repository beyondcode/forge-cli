---
title: Synchronize configuration with Forge
order: 1
---

# Synchronize configuration with Forge

When you made changes to your `forge.yml` file, like modifying the deployment script, adding a webhook manually, or modifying the quick-deploy setting, you need to synchronize these changes with Laravel Forge in order for them to
take  effect.

You can do this by running `forge config:push`.

This command will read your local `forge.yml` file and synchronize its settings with Laravel Forge.
