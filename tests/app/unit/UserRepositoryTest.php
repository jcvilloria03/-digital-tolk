<?php

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Illuminate\Database\DatabaseManager;
use DTApi\Models\User;
use DTApi\Models\Type;
use DTApi\Models\Company;
use DTApi\Models\Department;
use DTApi\Models\UserMeta;
use DTApi\Models\UsersBlacklist;
use DTApi\Models\Town;
use DTApi\Models\UserTowns;
use DTApi\Models\UserLanguages;

class UserRepositoryTest extends TestCase
{
    public function tearDown(): void
    {
        m::close();
    }

    public function testCreateOrUpdate()
    {
        // Mocking necessary classes and dependencies
        $userMock = m::mock(User::class);
        $typeMock = m::mock(Type::class);
        $companyMock = m::mock(Company::class);
        $departmentMock = m::mock(Department::class);
        $userMetaMock = m::mock(UserMeta::class);
        $userBlacklistMock = m::mock(UsersBlacklist::class);
        $townMock = m::mock(Town::class);
        $userTownsMock = m::mock(UserTowns::class);
        $userLanguagesMock = m::mock(UserLanguages::class);
        $dbMock = m::mock(DatabaseManager::class);

        // Set expectations for mocked methods and make assertions
        $userMock->shouldReceive('findOrFail')->andReturn($userMock);
        $typeMock->shouldReceive('where')->andReturn($typeMock);
        $typeMock->shouldReceive('first')->andReturn($typeMock);
        $companyMock->shouldReceive('create')->andReturn($companyMock);
        $departmentMock->shouldReceive('create')->andReturn($departmentMock);
        $userMock->shouldReceive('detachAllRoles');
        $userMock->shouldReceive('save');
        $userMock->shouldReceive('attachRole');
        $userMetaMock->shouldReceive('firstOrCreate')->andReturn($userMetaMock);
        $userMetaMock->shouldReceive('toArray')->andReturn([]);
        $userMetaMock->shouldReceive('save');
        $userBlacklistMock->shouldReceive('where')->andReturn($userBlacklistMock);
        $userBlacklistMock->shouldReceive('get')->andReturn($userBlacklistMock);
        $userBlacklistMock->shouldReceive('pluck')->andReturn([]);
        $userBlacklistMock->shouldReceive('translatorExist')->andReturn(0);
        $userBlacklistMock->shouldReceive('save');
        $townMock->shouldReceive('save');
        $userTownsMock->shouldReceive('townExist')->andReturn(0);
        $userTownsMock->shouldReceive('save');
        $userLanguagesMock->shouldReceive('langExist')->andReturn(0);
        $userLanguagesMock->shouldReceive('save');
        $dbMock->shouldReceive('table')->andReturn($dbMock);
        $dbMock->shouldReceive('where')->andReturn($dbMock);
        $dbMock->shouldReceive('delete')->andReturn(true);

        $userRepository = new UserRepository();
        $userRepository->setUser($userMock);
        $userRepository->setType($typeMock);
        $userRepository->setCompany($companyMock);
        $userRepository->setDepartment($departmentMock);
        $userRepository->setUserMeta($userMetaMock);
        $userRepository->setUserBlacklist($userBlacklistMock);
        $userRepository->setTown($townMock);
        $userRepository->setUserTowns($userTownsMock);
        $userRepository->setUserLanguages($userLanguagesMock);
        $userRepository->setDB($dbMock);

        // Mock request data
        $request = [
            'role'          => 'role',
            'name'          => 'John Doe',
            'company_id'    => 1,
            'department_id' => 1,
            'email'         => 1,
            'dob_or_orgid'  => 1,
            'phone'         => '0289511111',
            'mobile'        => '09451237890',
        ];

        // Call the createOrUpdate method
        $result = $userRepository->createOrUpdate(null, $request);

        // Assertion
        $this->assertInstanceOf(User::class, $result);
    }
}