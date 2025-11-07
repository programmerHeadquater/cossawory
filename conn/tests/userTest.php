<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../User.php';
require_once __DIR__ . '/seedingDb.php';

use function User\user_getById;
use function User\user_getByEmail;
use function User\user_checkLogin;
use function User\user_addNewUser;
use function User\user_deleteById;
use function User\user_hasPermission;
use function User\user_canWriteReview;
use function User\user_canDeleteReview;
use function User\user_canDeleteSubmission;
use function User\user_canAddUser;
use function User\user_canDeleteUser;
use function User\user_getUsers;
use function User\user_getTotalUser;
use function User\user_updatePermission;

use function seed\seedDatabase;
use function seed\clearDatabase;

class UserTest extends TestCase
{
    private static $newUserId;

    public static function setUpBeforeClass(): void
    {
        seedDatabase();
    }

    public static function tearDownAfterClass(): void
    {
        // clearDatabase();
    }

    public function testUserGetById()
    {
        $resp = user_getById(1);
        $this->assertTrue($resp['status']);
        $this->assertEquals('admin', $resp['data']['username']);
    }

    public function testUserGetByEmail()
    {
        $resp = user_getByEmail('reviewer@example.com');
        $this->assertTrue($resp['status']);
        $this->assertEquals('reviewer@example.com', $resp['data']['email']);
    }

    public function testUserCheckLogin()
    {
        $resp = user_checkLogin('admin@example.com', 'admin123');
        $this->assertTrue($resp['status']);
        $this->assertEquals('admin', $resp['data']['username']);
    }

    public function testUserAddNewUser()
    {
        $newUserData = [
            'username' => 'tester',
            'email' => 'tester@example.com',
            'password' => 'test123',
            'can_write_review' => 'yes',
            'can_delete_review' => 'no',
            'can_delete_submission' => 'yes',
            'can_add_user' => 'no',
            'can_delete_user' => 'yes',
        ];

        $resp = user_addNewUser($newUserData);
        self::$newUserId = $resp['data']['id'] ?? null;

        $this->assertTrue($resp['status']);
        $this->assertNotNull(self::$newUserId);
    }

    public function testUserDeleteById()
    {
        if (self::$newUserId) {
            $resp = user_deleteById(self::$newUserId);
            $this->assertTrue($resp['status']);
        } else {
            $this->markTestSkipped('No user added to delete.');
        }
    }

    public function testUserPermissions()
    {
        $resp = user_canWriteReview(1);
        $this->assertTrue($resp['status']);
        $this->assertTrue($resp['data']);

        $resp = user_canDeleteReview(1);
        $this->assertTrue($resp['status']);
        $this->assertTrue($resp['data']);

        $resp = user_canDeleteSubmission(1);
        $this->assertTrue($resp['status']);
        $this->assertTrue($resp['data']);

        $resp = user_canAddUser(1);
        $this->assertTrue($resp['status']);
        $this->assertTrue($resp['data']);

        $resp = user_canDeleteUser(1);
        $this->assertTrue($resp['status']);
        $this->assertTrue($resp['data']);
    }

    public function testUserGetUsers()
    {
        $resp = user_getUsers(0);
        $this->assertTrue($resp['status']);
        $this->assertGreaterThan(0, count($resp['data']));
    }

    public function testUserGetTotalUser()
    {
        $resp = user_getTotalUser();
        $this->assertTrue($resp['status']);
        $this->assertGreaterThan(0, $resp['data']);
    }

    public function testUserUpdatePermission()
    {
        $resp = user_updatePermission(2, 1, 1, 0, 0, 0);
        $this->assertTrue($resp['status']);

        $perm = user_canWriteReview(2);
        $this->assertTrue($perm['data']);
        $perm = user_canDeleteReview(2);
        $this->assertTrue($perm['data']);
    }

    public function testInvalidPermission()
    {
        $resp = user_hasPermission(1, 'invalid_perm');
        $this->assertFalse($resp['status']);
        $this->assertStringContainsString('Invalid permission', $resp['error']);
    }
}
