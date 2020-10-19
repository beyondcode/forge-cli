---
title: Reboot Postgres
order: 4
---

# Reboot Postgres

Use the command `forge reboot:nginx` to reboot the Postgres server on the linked Forge site for the given environment.

**IMPORTANT:** Please remember that rebooting the server or services may cause temporary downtime! Running the command will only initiate the reboot process. It is up to you to perform whatever steps are necessary to confirm that the server or service has properly rebooted.

Every `reboot` command requires confirmation, which you can provide by adding the `--confirm` option.
