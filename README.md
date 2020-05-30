# F3com

This repository contains the [fatfreeframework.com](https://fatfreeframework.com) website / wiki.

## Installation

First of all install required dependencies via [Composer](https://getcomposer.org): `composer install`

The project consists of two different repositories. One with the code, another
with the data. To clone both use the following command

```
git clone --recursive git@github.com:F3Community/F3com.git
```

Use the following command to get the content from an existing "code" repository

```
git submodule update --init
```

Additionally if you wish to run this in the local php development server, do the following:

```
php -S localhost:8000 .router.php
```
