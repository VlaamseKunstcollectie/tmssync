# TMS Sync

TMS (The Museum System) is a collection management system for museums produced
by The Gallery Systems. This system leverages Microsoft SQL database server to
store registration data (objects, persons, thesauri,...).

This project features a set of PHP scripts which will extract relevant data from
the MS SQL server, and migrates it to a MySQL database server.

## Purpose?

The Microsoft SQL server is heavily tied to the Microsoft platform. However,
useful, open source tools created within the realm of digital humanities are
created within the Unix (Linux, OSX) framework.

Migrating data from to a more general purpose format allows for greater
interoperability.

## Why MySQL?

MySQL is a well established, general purpose database system which is widely
used to power database driven content management systems, websites and web
frameworks.

## Pre-requisites.

* a host which features
** PHP 5.6 or up.
** MySQL 5.7 or up.
** Composer installed.
* Read access from said host to the MS SQL database server which features the tms
  database.

Best practice is to create a seperate host (ubuntu, centos, debian, redhat,...)
and make sure it can connect to the MS SQL server. (Separation of concerns)

## How to install?

Clone this repository
...

Install the dependencies and make `app/console` executable
```
$ composer install
$ chmod +x app/console
```

Configure the connections to both source and destination databases by copying
`app/config/config.yml.dist` to `app/config/config.yml` and changing the
configuration variables in the copied file.

Run the installer to create the necessary MySQL tables.
```
./app/console tms:install
```

## How to use?

Just run the exporter command to sync TMS to MySQL

```
./app/console tms:export
```

Warning: all database tables will be truncated (emptied) and refilled with data!
Why? The MySQL database is considered to be a mere shallow copy of the original
data.

## Which data is synced?

The export command will make a shallow copy of the following tables. Data will
not be modified during this process.

The mapping between TMS (Source) and MySQL (destination) is determined in the
`config.yml` file. The `mapping` key contains a configurable mapping between
MS SQL tables and MySQL tables. This gives system administrators flexible
configuration options in case of database changes.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related, please email
matthias.vandermaesen@vlaamsekunstcollectie.be instead of using the issue
tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

