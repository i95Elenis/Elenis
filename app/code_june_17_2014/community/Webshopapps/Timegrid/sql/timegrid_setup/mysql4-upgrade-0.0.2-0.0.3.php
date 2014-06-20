<?php

$installer = $this;

$installer->startSetup();

$installer->run("

ALTER IGNORE TABLE {$this->getTable('wsa_timegrid')} MODIFY `week_commencing` date NOT NULL DEFAULT '0000-00-00';

");

$installer->endSetup();


