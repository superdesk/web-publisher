## Testing

For unit tests use PHPSpec tests, for functional tests use PHPUnit and Behat for integration.

How to run tests?

```bash
php bin/phpunit # PHPUnit
php bin/phpspec run # PHPSpec
```

To see current code tests coverage run:

For PHPSpec copy`phpspec.yml.dist` to `phpspec.yml` and uncomment:

```yaml
#extensions:
#    - PhpSpec\Extension\CodeCoverageExtension

#code_coverage:
#    output: build/coverage
#    format: html
```

and re-run PHPSpec.

For PHPUnit:

```
php bin/phpunit --coverage-text
```

Send code coverage raport to [codecov.io](https://codecov.io/github/superdesk/web-publisher) with:

```
bash <(curl -s https://codecov.io/bash) -t 9774e0ee-fd3e-43d3-8ba6-a25e4ef57fe5
```

**Note:** remember to enable `Xdebug` to generate the coverage.
