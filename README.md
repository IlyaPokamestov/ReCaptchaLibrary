Google ReCAPTCHA ver.2 backend provider
================================================

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/cbc2c849-3910-4316-bac2-9977c4eda736/big.png)](https://insight.sensiolabs.com/projects/cbc2c849-3910-4316-bac2-9977c4eda736)
[![Latest Stable Version](https://poser.pugx.org/dario_swain/re-captcha-library/v/stable.svg)](https://packagist.org/packages/dario_swain/re-captcha-library)
[![Build Status](https://travis-ci.org/DarioSwain/ReCaptchaLibrary.svg?branch=master)](https://travis-ci.org/DarioSwain/ReCaptchaLibrary)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/DarioSwain/ReCaptchaLibrary/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/DarioSwain/ReCaptchaLibrary/?branch=master)
[![Coverage Status](https://coveralls.io/repos/DarioSwain/ReCaptchaLibrary/badge.svg)](https://coveralls.io/r/DarioSwain/ReCaptchaLibrary)
[![License](https://poser.pugx.org/dario_swain/re-captcha-library/license.svg)](https://packagist.org/packages/dario_swain/re-captcha-library)

You can find full documentation about Google reCAPTCHA API v2 [here](http://developers.google.com/recaptcha/intro).

Installation
------------

You can install this package with [Composer](http://getcomposer.org/).
Add next lines to your composer.json file:

``` json
{
    "require": {
        "dario_swain/re-captcha-library": "1.0.*"
    }
}
```

Usage Example
-------------

Use ReCaptcha class for sending request to google API:

``` php
<?php

    $privateKey = 'PRIVATE_KEY'; //You Google API private key
    $clientIp = $_SERVER['REMOTE_ADDR']; //Client IP Address
    $gReCaptchaResponse = $_POST['g-recaptcha-response']; //Google reCAPTCHA response

    $reCaptcha = new ReCaptcha($privateKey, $clientIp, $gReCaptchaResponse);
	$response = $reCaptcha->buildRequest()->send();

	if($response->isSuccess())
    {
        //Submit form ...
    }

```

Copyright
---------

Copyright (c) 2015 Ilya Pokamestov <dario_swain@yahoo.com>.
