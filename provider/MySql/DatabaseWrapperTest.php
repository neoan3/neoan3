<?php

namespace Neoan3\Provider\MySql;

use Neoan3\Apps\DbException;
use PHPUnit\Framework\TestCase;

class DatabaseWrapperTest extends TestCase
{
    private DatabaseWrapper $db;
    protected function setUp(): void
    {
        $this->db = new DatabaseWrapper();
        $this->db->connect(['name' => 'thisdatabasenameshallneverexist']);
    }

    public function testGetNextId()
    {
        $this->expectException(DbException::class);
        $this->db->getNextId();
    }

    public function testPure()
    {
        $this->expectException(DbException::class);
        $this->db->pure('A sql string');
    }
}
