<?php

/** @noinspection UntrustedInclusionInspection */
/** @noinspection PhpIncludeInspection */
require_once 'app/bootstrap.php';
models\Auth::restrictAccess();

if (isset($_POST['sword'])) {
    $sword  = trim($_POST['sword']);
    $result = models\db\Symbol::search($sword);

    models\db\User::saveSearch($sword);

    while ($row = \mysqli_fetch_array($result)) {
        echo '<option value="' . $row['symbol'] . '">'
                . $row['symbol'] . ' - ' . $row['security_name'] . ' (' . $row['exchange'] . ')'
           . '</option>' . "\n";
    }
}
