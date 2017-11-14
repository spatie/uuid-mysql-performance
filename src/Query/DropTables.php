<?php

namespace Spatie\Query;

class DropTables extends AbstractQuery
{
    public function execute()
    {
        $this->connection->exec(<<<SQL
DROP TABLE IF EXISTS `normal_id`;
DROP TABLE IF EXISTS `normal_uuid`;
DROP TABLE IF EXISTS `optimised_uuid`;
SQL
        );
    }
}
