<?php

$installer = $this;

$installer->startSetup();

$installer->run("

ALTER IGNORE TABLE {$this->getTable('wsa_timegrid')} MODIFY `week_commencing` date NULL;

");

$installer->endSetup();


