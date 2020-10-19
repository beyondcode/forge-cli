---
title: Environments
order: 4
---

# Environments

Forge CLI allows you to link and manage multiple sites and servers to one working directory.

This is especially useful, when you have multiple environments for your project (for example staging and production).

All Forge CLI commands allow you to pass the environment that you want to target as an additional command line argument.
The default environment is always "production".

Examples:

```
# This will link Forge with a site for the "production" environment
forge init

# This will link Forge with a site for the "staging" environment
forge init staging
```
