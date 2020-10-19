---
title: Reading logs
order: 2
---

# Reading logs

Forge CLI allows you to read your servers log files without having to connect to your server via SSH.

You can see the Nginx error logs, using: `forge logs`.

There are multiple available log files that you can access:

### Nginx Access Logs
To retrieve the Nginx access logs, pass the `--file=nginx_access` option to the logs command.

```
forge logs --file=nginx_access
```

### Nginx Error Logs
To retrieve the Nginx error logs, pass the `--file=nginx_error` option to the logs command.

```
forge logs --file=nginx_error
```

### Database Logs
To retrieve the database logs, pass the `--file=database` option to the logs command.

```
forge logs --file=database
```

### PHP FPM Logs
To retrieve the PHP FPM logs, pass the `--file=php7x` option to the logs command, where `php7x` is a valid version number. For example:

```
forge logs --file=php74
```
