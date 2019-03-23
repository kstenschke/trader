<?php

/** @noinspection UntrustedInclusionInspection */
/** @noinspection PhpIncludeInspection */
require_once 'app/bootstrap.php';
models\Auth::restrictAccess();

if (!isset($_POST['preference'], $_POST['area'], $_POST['value'])) {
    die('Failed save preference.');
}

$idArea = models\Area::getIdAreaByIdentifier($_POST['area']);
if (models\Area::AREA_ID_UNDEFINED === $idArea) {
    die('Failed get area.');
}

models\db\Preference::savePreference($_POST['preference'], $_POST['value'], $idArea);
