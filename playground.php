<?php


require_once('model/PDORepository.php');
require_once('model/PDOSubasta.php');

PDOSubasta::getInstance()->getAuctions();