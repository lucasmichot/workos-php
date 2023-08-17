<?php

namespace WorkOS;

class UserManagementTest extends \PHPUnit\Framework\TestCase
{
    use TestHelper {
        setUp as traitSetUp;
    }

    protected function setUp(): void
    {
        $this->traitSetUp();

        $this->withApiKeyAndClientId();
        $this->userManagement = new UserManagement();
    }

    public function testAddUserToOrganization()
    {
        $userId = "user_01H7X1M4TZJN5N4HG4XXMA1234";
        $usersOrganizationsPath = "users/{$userId}/organizations";

        $result = $this->addUserToOrganizationResponseFixture();

        $params = [
            "organization_id" => "org_01EHQMYV6MBK39QC5PZXHY59C3",
        ];

        $this->mockRequest(
            Client::METHOD_POST,
            $usersOrganizationsPath,
            null,
            $params,
            true,
            $result
        );

        $user = $this->userFixture();

        $response = $this->userManagement->addUserToOrganization("user_01H7X1M4TZJN5N4HG4XXMA1234", "org_01EHQMYV6MBK39QC5PZXHY59C3");
        $this->assertSame($user, $response->toArray());
    }

    public function testCreateUser()
    {
        $usersPath = "users";

        $result = $this->createUserResponseFixture();

        $params = [
            "email" => "test@test.com",
            "password" => "x^T!V23UN1@V",
            "first_name" => "Damien",
            "last_name" => "Alabaster",
            "email_verified" => true
        ];

        $this->mockRequest(
            Client::METHOD_POST,
            $usersPath,
            null,
            $params,
            true,
            $result
        );

        $user = $this->userFixture();

        $response = $this->userManagement->createUser("test@test.com", "x^T!V23UN1@V", "Damien", "Alabaster", true);
        $this->assertSame($user, $response->toArray());
    }

    public function testCreateEmailVerificationChallenge()
    {
        $id = "user_01E4ZCR3C56J083X43JQXF3JK5";
        $createEmailVerificationPath = "users/{$id}/email_verification_challenge";

        $result = $this->createUserAndTokenResponseFixture();

        $params = [
            "verification_url" => "https://your-app.com/verify-email",
        ];

        $this->mockRequest(
            Client::METHOD_POST,
            $createEmailVerificationPath,
            null,
            $params,
            true,
            $result
        );


        $userFixture = $this->userFixture();

        $response = $this->userManagement->createEmailVerificationChallenge("user_01E4ZCR3C56J083X43JQXF3JK5", "https://your-app.com/verify-email");
        $this->assertSame("01DMEK0J53CVMC32CK5SE0KZ8Q", $response->token);
        $this->assertSame($userFixture, $response->user->toArray());
    }

    public function testCompleteEmailVerification()
    {
        $usersPath = "users/email_verification";

        $result = $this->createUserResponseFixture();

        $params = [
            "token" => "01DMEK0J53CVMC32CK5SE0KZ8Q",
        ];

        $this->mockRequest(
            Client::METHOD_POST,
            $usersPath,
            null,
            $params,
            true,
            $result
        );

        $user = $this->userFixture();

        $response = $this->userManagement->completeEmailVerification("01DMEK0J53CVMC32CK5SE0KZ8Q");
        $this->assertSame($user, $response->toArray());
    }


    public function testGetUser()
    {
        $userId = "user_01H7X1M4TZJN5N4HG4XXMA1234";
        $usersPath = "users/{$userId}";

        $result = $this->getUserResponseFixture();

        $this->mockRequest(
            Client::METHOD_GET,
            $usersPath,
            null,
            null,
            true,
            $result
        );

        $user = $this->userFixture();

        $response = $this->userManagement->getUser($userId);

        $this->assertSame($user, $response->toArray());
    }

    public function testListUsers()
    {
        $usersPath = "users";
        $params = [
            "type" => null,
            "email" => null,
            "organization" => null,
            "limit" => UserManagement::DEFAULT_PAGE_SIZE,
            "before" => null,
            "after" => null,
            "order" => null
        ];

        $result = $this->listUsersResponseFixture();

        $this->mockRequest(
            Client::METHOD_GET,
            $usersPath,
            null,
            $params,
            true,
            $result
        );

        $user = $this->userFixture();
        list($before, $after, $users) = $this->userManagement->listUsers();
        $this->assertSame($user, $users[0]->toArray());
    }

    public function testRemoveUserFromOrganization()
    {
        $userId = "user_01H7X1M4TZJN5N4HG4XXMA1234";
        $organizationId = "org_01EHQMYV6MBK39QC5PZXHY59C3";
        $usersOrganizationsDeletionPath = "users/{$userId}/organizations/{$organizationId}";

        $result = $this->removeUserFromOrganizationResponseFixture();

        $this->mockRequest(
            Client::METHOD_DELETE,
            $usersOrganizationsDeletionPath,
            null,
            null,
            true,
            $result
        );

        $user = $this->userFixture();

        $response = $this->userManagement->removeUserFromOrganization("user_01H7X1M4TZJN5N4HG4XXMA1234", "org_01EHQMYV6MBK39QC5PZXHY59C3");
        $this->assertSame($user, $response->toArray());
    }

    // Fixtures

    private function createUserAndTokenResponseFixture()
    {
        return json_encode([
            "token" => "01DMEK0J53CVMC32CK5SE0KZ8Q",
            "user" => [
                "object" => "user",
                "id" => "user_01H7X1M4TZJN5N4HG4XXMA1234",
                "user_type" => "unmanaged",
                "email" => "test@test.com",
                "first_name" => "Damien",
                "last_name" => "Alabaster",
                "email_verified_at" => "2021-07-25T19:07:33.155Z",
                "sso_profile_id" => "1AO5ZPQDE43",
                "google_oauth_profile_id" => "goog_123ABC",
                "created_at" => "2021-06-25T19:07:33.155Z",
                "updated_at" => "2021-06-25T19:07:33.155Z"
            ]
        ]);
    }

    private function addUserToOrganizationResponseFixture()
    {
        return json_encode([
            "object" => "user",
            "id" => "user_01H7X1M4TZJN5N4HG4XXMA1234",
            "user_type" => "unmanaged",
            "email" => "test@test.com",
            "first_name" => "Damien",
            "last_name" => "Alabaster",
            "email_verified_at" => "2021-07-25T19:07:33.155Z",
            "sso_profile_id" => "1AO5ZPQDE43",
            "google_oauth_profile_id" => "goog_123ABC",
            "created_at" => "2021-06-25T19:07:33.155Z",
            "updated_at" => "2021-06-25T19:07:33.155Z"
        ]);
    }

    private function createUserResponseFixture()
    {
        return json_encode([
            "object" => "user",
            "id" => "user_01H7X1M4TZJN5N4HG4XXMA1234",
            "user_type" => "unmanaged",
            "email" => "test@test.com",
            "first_name" => "Damien",
            "last_name" => "Alabaster",
            "email_verified_at" => "2021-07-25T19:07:33.155Z",
            "sso_profile_id" => "1AO5ZPQDE43",
            "google_oauth_profile_id" => "goog_123ABC",
            "created_at" => "2021-06-25T19:07:33.155Z",
            "updated_at" => "2021-06-25T19:07:33.155Z"
        ]);
    }

    private function getUserResponseFixture()
    {
        return json_encode([
            "object" => "user",
            "id" => "user_01H7X1M4TZJN5N4HG4XXMA1234",
            "user_type" => "unmanaged",
            "email" => "test@test.com",
            "first_name" => "Damien",
            "last_name" => "Alabaster",
            "email_verified_at" => "2021-07-25T19:07:33.155Z",
            "sso_profile_id" => "1AO5ZPQDE43",
            "google_oauth_profile_id" => "goog_123ABC",
            "created_at" => "2021-06-25T19:07:33.155Z",
            "updated_at" => "2021-06-25T19:07:33.155Z"
        ]);
    }

    private function listUsersResponseFixture()
    {
        return json_encode([
            "data" => [
                [
                    "object" => "user",
                    "id" => "user_01H7X1M4TZJN5N4HG4XXMA1234",
                    "user_type" => "unmanaged",
                    "email" => "test@test.com",
                    "first_name" => "Damien",
                    "last_name" => "Alabaster",
                    "email_verified_at" => "2021-07-25T19:07:33.155Z",
                    "sso_profile_id" => "1AO5ZPQDE43",
                    "google_oauth_profile_id" => "goog_123ABC",
                    "created_at" => "2021-06-25T19:07:33.155Z",
                    "updated_at" => "2021-06-25T19:07:33.155Z"
                ]
            ],
            "list_metadata" => [
                "before" => null,
                "after" => null
            ],
        ]);
    }

    private function removeUserFromOrganizationResponseFixture()
    {
        return json_encode([
            "object" => "user",
            "id" => "user_01H7X1M4TZJN5N4HG4XXMA1234",
            "user_type" => "unmanaged",
            "email" => "test@test.com",
            "first_name" => "Damien",
            "last_name" => "Alabaster",
            "email_verified_at" => "2021-07-25T19:07:33.155Z",
            "sso_profile_id" => "1AO5ZPQDE43",
            "google_oauth_profile_id" => "goog_123ABC",
            "created_at" => "2021-06-25T19:07:33.155Z",
            "updated_at" => "2021-06-25T19:07:33.155Z"
        ]);
    }

    private function userFixture()
    {
        return [
            "object" => "user",
            "id" => "user_01H7X1M4TZJN5N4HG4XXMA1234",
            "userType" => "unmanaged",
            "email" => "test@test.com",
            "firstName" => "Damien",
            "lastName" => "Alabaster",
            "emailVerifiedAt" => "2021-07-25T19:07:33.155Z",
            "googleOauthProfileId" => "goog_123ABC",
            "ssoProfileId" => "1AO5ZPQDE43",
            "createdAt" => "2021-06-25T19:07:33.155Z",
            "updatedAt" => "2021-06-25T19:07:33.155Z"
        ];
    }
}