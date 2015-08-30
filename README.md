# Muse Jobs Client

[![Latest Version](https://img.shields.io/github/release/JobBrander/jobs-muse.svg?style=flat-square)](https://github.com/JobBrander/jobs-muse/releases)
[![Software License](https://img.shields.io/badge/license-APACHE%202.0-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/JobBrander/jobs-muse/master.svg?style=flat-square&1)](https://travis-ci.org/JobBrander/jobs-muse)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/JobBrander/jobs-muse.svg?style=flat-square)](https://scrutinizer-ci.com/g/JobBrander/jobs-muse/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/JobBrander/jobs-muse.svg?style=flat-square)](https://scrutinizer-ci.com/g/JobBrander/jobs-muse)
[![Total Downloads](https://img.shields.io/packagist/dt/jobbrander/jobs-muse.svg?style=flat-square)](https://packagist.org/packages/jobbrander/jobs-muse)

This package provides [Muse Jobs API](https://www.themuse.com/developers#job-listing)
support for the JobBrander's [Jobs Client](https://github.com/JobBrander/jobs-common).

## Installation

To install, use composer:

```
composer require jobbrander/jobs-muse
```

## Usage

Usage is the same as Job Branders's Jobs Client, using `\JobBrander\Jobs\Client\Provider\Muse` as the provider.

```php
$client = new JobBrander\Jobs\Client\Provider\Muse();

// Search for job listings in Chicago, IL
$jobs = $client
    ->setLocation('Chicago, IL')   // A location from the Muse's list of location strings: https://www.themuse.com/developers#job-listing
    ->setCategory('Education')     // A string that restricts search results to jobs in the specified category. A list of valid categories is in the Muse's documentation
    ->setLevel('Internship')       // A string that restricts search results to jobs in the specified experience level. A list of valid levels is in the Muse's documentation
    ->setCompany('Facebook')       // A string that restricts search results to jobs at the specified company.
    ->setDescending(true)          // The sort order of the results (ascending or descending). Valid values are: true, false; The default is true.
    ->setPage(2)                   // The requested page of result sets, numbered beginning from 1. Default is 1. If this number exceeds the value of the response property totalPages, the response will contain zero results.
    ->getJobs();
```

The `getJobs` method will return a [Collection](https://github.com/JobBrander/jobs-common/blob/master/src/Collection.php) of [Job](https://github.com/JobBrander/jobs-common/blob/master/src/Job.php) objects.

## Testing

``` bash
$ ./vendor/bin/phpunit
```

## Contributing

Please see [CONTRIBUTING](https://github.com/jobbrander/jobs-muse/blob/master/CONTRIBUTING.md) for details.

## Credits

- [Karl Hughes](https://github.com/karllhughes)
- [All Contributors](https://github.com/jobbrander/jobs-muse/contributors)

## License

The Apache 2.0. Please see [License File](https://github.com/jobbrander/jobs-muse/blob/master/LICENSE) for more information.

