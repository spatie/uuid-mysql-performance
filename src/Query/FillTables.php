<?php

namespace Spatie\Query;

use Ramsey\Uuid\Uuid;

class FillTables extends AbstractQuery
{
//    protected $amount = 100000;
    protected $amount = 10;
    protected $flush = 100;

    public function execute()
    {
        $this->fillOptimisedUuid();
        $this->fillNormalId();
        $this->fillNormalUuid();
    }

    protected function fillNormalId(): void
    {
        $queries = [];

        for ($i = 0; $i < $this->amount; $i++) {
            $text = $this->randomTexts[array_rand($this->randomTexts)];

            $queries[] = <<<SQL
INSERT INTO `normal_id` (`text`) VALUES ('$text');
SQL;

            if (count($queries) > $this->flush) {
                $this->connection->exec(implode('', $queries));
                $queries = [];
            }
        }
    }

    protected function fillNormalUuid(): void
    {
        $queries = [];

        for ($i = 0; $i < $this->amount; $i++) {
            $uuid = Uuid::uuid1()->toString();

            $text = $this->randomTexts[array_rand($this->randomTexts)];

            $queries[] = <<<SQL
INSERT INTO `normal_uuid` (`uuid`, `text`) VALUES (UNHEX(REPLACE("$uuid", "-","")), '$text');
SQL;

            if (count($queries) > $this->flush) {
                $this->connection->exec(implode('', $queries));
                $queries = [];
            }
        }
    }

    protected function fillOptimisedUuid(): void
    {
        $queries = [];

        for ($i = 0; $i < $this->amount; $i++) {
            $uuid = Uuid::uuid1()->toString();

            $uuid = str_replace('-', '', $uuid);

            $optimisedUuid = substr($uuid, 6, 2) . substr($uuid, 4, 2) . substr($uuid, 0, 4) . substr($uuid, 8, 8);

            $optimisedUuidText = Uuid::fromBytes($optimisedUuid)->toString();

            $text = $this->randomTexts[array_rand($this->randomTexts)];

            $queries[] = <<<SQL
INSERT INTO `optimised_uuid` (`uuid`, `uuid_text`, `text`) VALUES (
  '$optimisedUuid',
  '$optimisedUuidText',
  '$text'
);
SQL;

            if (count($queries) > $this->flush) {
                $this->connection->exec(implode('', $queries));
                $queries = [];
            }
        }
    }
}
