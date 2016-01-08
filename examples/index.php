<?php
/*
 * This file is part of the ReCaptcha Library.
 *
 * (c) Ilya Pokamestov <dario_swain@yahoo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

error_reporting(E_ERROR | E_WARNING | E_PARSE);

require_once '../vendor/autoload.php';

use DS\Library\ReCaptcha\Client;
use DS\Library\ReCaptcha\ValidationException;

$validationError = '';
$success = null;
$name = empty($_POST["name"]) ? '' : $_POST["name"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST["g-recaptcha-response"])) {
        $gResponse = $_POST["g-recaptcha-response"];

        //Valid only for Github Pages.
        $reCaptchaClient = new Client('6Le7ihQTAAAAACmnuhffcE8jttD2uVCAp2NwkUYT');

        try {
            $success = $reCaptchaClient->validate($gResponse);
        } catch(ValidationException $e) {
            $validationError = $e->getMessage();
        }
    }
}

?>
<!DOCTYPE HTML>
    <html>
        <head>
            <script src='https://www.google.com/recaptcha/api.js'></script>
        </head>
    <body>
        <h2>PHP Form Validation Example</h2>
        <p><span class="error">* required field.</span></p>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["REQUEST_URI"]);?>">
            Name: <input type="text" name="name" value="<?php echo $name;?>">
            <br><br>
            <div class="g-recaptcha" data-sitekey="6Le7ihQTAAAAAJTtoCL0BW-hUQFfG2f8qKLojch7"></div>
            <br><br>
            <input type="submit" name="submit" value="Submit">
        </form>
        <br><br>
<?php

if ($success) {
    echo 'Validation successfully passed!';
}

if ($validationError) {
    echo $validationError;
}

?>

</body>
</html>
