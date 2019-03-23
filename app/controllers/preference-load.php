<?php

/** @noinspection UntrustedInclusionInspection */
/** @noinspection PhpIncludeInspection */
require_once 'app/bootstrap.php';
models\Auth::restrictAccess();

if (!isset($_POST['preference'], $_POST['area'])) {
    die('Failed save preference.');
}

$idArea = models\Area::getIdAreaByIdentifier($_POST['area']);
if (models\Area::AREA_ID_UNDEFINED === $idArea) {
    die('Failed get area.');
}

echo models\db\Preference::getPreference($_POST['preference'], $idArea);
