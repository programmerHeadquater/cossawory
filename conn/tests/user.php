<?php
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../seed/seed_users.php';

class UserSeederTest extends TestCase
{
    public function testUserSeeding()
    {
        \seed\clearUsers();
        $this->assertEquals(0, \seed\verifyUsers());

        $ids = \seed\seedUsers(5);
        $this->assertCount(5, $ids);
        $this->assertGreaterThan(0, \seed\verifyUsers());
    }
}
