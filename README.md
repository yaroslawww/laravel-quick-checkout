# Simple checkout process using session.

[![Packagist License](https://img.shields.io/packagist/l/yaroslawww/laravel-quick-checkout?color=%234dc71f)](https://github.com/yaroslawww/laravel-quick-checkout/blob/master/LICENSE.md)
[![Packagist Version](https://img.shields.io/packagist/v/yaroslawww/laravel-quick-checkout)](https://packagist.org/packages/yaroslawww/laravel-quick-checkout)
[![Build Status](https://scrutinizer-ci.com/g/yaroslawww/laravel-quick-checkout/badges/build.png?b=master)](https://scrutinizer-ci.com/g/yaroslawww/laravel-quick-checkout/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/yaroslawww/laravel-quick-checkout/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/yaroslawww/laravel-quick-checkout/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/yaroslawww/laravel-quick-checkout/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/yaroslawww/laravel-quick-checkout/?branch=master)

## Installation

Install the package via composer:

```bash
composer require yaroslawww/laravel-quick-checkout
```

You can publish the config file with:

```bash
php artisan vendor:publish --provider="QuickCheckout\ServiceProvider" --tag="config"
```

## Usage

Example usage:

```injectablephp
public function addToCart(Course $course) {
        /** @var \QuickCheckout\Cart $cart */
        $cart = \QuickCheckout\Checkout::cart()->purge()
                                       ->withLineItem($course->toCheckoutProduct(), 2)
                                       ->withLineItem(new Product('My other product', 123), 4)
                                       ->putToSession();
                                       
        // ... response or redirect
}

public function showCheckout(Course $course) {
        /** @var \QuickCheckout\Cart $cart */
        $cart = \QuickCheckout\Checkout::cart()->fromSession();
        // ... response or redirect
}
```

Model configuration:

```injectablephp
use Illuminate\Database\Eloquent\Model;
use QuickCheckout\Contracts\UsedAsCheckoutProduct;
use QuickCheckout\Eloquent\AsCheckoutProduct;

class Course extends Model implements UsedAsCheckoutProduct
{
    use AsCheckoutProduct;

    // ...
}
```

## Credits

- [![Think Studio](https://yaroslawww.github.io/images/sponsors/packages/logo-think-studio.png)](https://think.studio/)
