<?php

namespace Spatie\Query;

class CreateTables extends AbstractQuery
{
    public function execute()
    {
        $this->connection->exec(<<<SQL
CREATE TABLE `normal_id` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `text` TEXT NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL
        );

        $this->connection->exec(<<<SQL
CREATE TABLE `normal_uuid` (
  `uuid` BINARY(16) NOT NULL,
  `uuid_text` varchar(36) generated always as
 (insert(
    insert(
      insert(
        insert(hex(uuid),9,0,'-'),
        14,0,'-'),
      19,0,'-'),
    24,0,'-')
 ) virtual,
  `text` TEXT NOT NULL,

  PRIMARY KEY (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL
        );

        $this->connection->exec(<<<SQL
CREATE TABLE `optimised_uuid` (
  `uuid` BINARY(16) NOT NULL,
  `generated_uuid_text` varchar(36) generated always as
  (insert(
    insert(
      insert(
        insert(
          hex(
            concat(substr(uuid,5,4),substr(uuid,3,2),
              substr(uuid,1,2),substr(uuid,9,8))
            ), 9,0,'-'),
         14,0,'-'),
       19,0,'-'),
     24,0,'-')
    ) virtual,
  `uuid_text` varchar(36),
  `text` TEXT NOT NULL,

  PRIMARY KEY (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL
        );
    }
}
