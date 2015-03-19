<?php
namespace App\Test\TestCase\Controller;

use Cake\Auth\DefaultPasswordHasher;
use Cake\Filesystem\Folder;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;

class UsersControllerTest extends IntegrationTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = [
		'app.users',
		'app.groups',
		'app.groups_i18n',
		'app.aros',
		'app.acos',
		'app.aros_acos',
		'app.sessions',
		'app.blog_articles',
		'app.blog_articles_comments',
		'app.blog_articles_likes',
		'app.badges',
		'app.badges_users',
		'app.premium_offers',
		'app.premium_transactions',
		'app.premium_discounts'
	];

/**
 * setUp
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		$password = password_hash('password', PASSWORD_DEFAULT);
		$Users = TableRegistry::get('Users');
		$Users->updateAll(['password' => $password], []);
	}

/**
 * tearDown
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		$folder = new Folder(TEST_WWW_ROOT . 'upload');
		$folder->delete(TEST_WWW_ROOT . 'upload');
	}

/**
 * Test index method
 *
 * @return void
 */
	public function testIndex() {
		$this->get('/users/index');

		$this->assertResponseOk();
		$this->assertResponseContains('mariano');
	}

/**
 * Test login method
 *
 * @return void
 */
	public function testLogin() {
		//Login successfull.
		$data = [
			'method' => 'login',
			'username' => 'mariano',
			'password' => 'password'
		];

		$this->post('/users/login', $data);

		$this->assertResponseSuccess();
		$this->assertSession(1, 'Auth.User.id');
		$this->assertRedirect(['controller' => 'pages', 'action' => 'home']);

		//Login fail.
		$data = [
			'method' => 'login',
			'username' => 'mariano',
			'password' => 'passfail'
		];

		$this->post('/users/login', $data);

		$this->assertResponseOk();
		$this->assertSession(null, 'Auth.User.id');

		//Register successfull.
		$data = [
			'method' => 'register',
			'username' => 'mariano2',
			'email' => 'test@xeta.io',
			'password' => '12345678',
			'password_confirm' => '12345678',
		];

		$this->post('/users/login', $data);

		$this->assertResponseSuccess();
		$this->assertSession(3, 'Auth.User.id');
		$this->assertRedirect(['controller' => 'pages', 'action' => 'home']);

		//Register fail.
		$data = [
			'method' => 'register',
			'username' => 'mariano',
			'email' => 'test@xeta.io',
			'password' => '12345678',
			'password_confirm' => '12345678',
		];

		$this->post('/users/login', $data);

		$this->assertResponseSuccess();
		$this->assertSession(null, 'Auth.User.id');
		//We can't test the flash test due to the translation system.
		$this->assertResponseContains('infobox-danger');
	}

/**
 * Test logout method
 *
 * @return void
 */
	public function testLogout() {
		$data = [
			'method' => 'login',
			'username' => 'mariano',
			'password' => 'password'
		];

		$this->post('/users/login', $data);

		$this->assertResponseSuccess();
		$this->assertSession(1, 'Auth.User.id');

		$this->get('/users/logout');
		$this->assertSession(null, 'Auth.User.id');

		$this->assertRedirect(['controller' => 'pages', 'action' => 'home']);
	}

/**
 * Test account unauthorized method
 *
 * @return void
 */
	public function testAccountUnauthorized() {
		$this->get('/users/account');
		$this->assertResponseSuccess();
		$this->assertRedirect(['controller' => 'users', 'action' => 'login']);
	}

/**
 * Test account authorized method
 *
 * @return void
 */
	public function testAccountAuthorized() {
		$this->session([
			'Auth' => [
				'User' => [
					'id' => 1,
					'username' => 'mariano',
					'avatar' => '../img/avatar.png',
					'group_id' => 5,
				]
			]
		]);

		$this->get('/users/account');

		$this->assertResponseOk();
	}

/**
 * Test account authorized with put method
 *
 * @return void
 */
	public function testAccountAuthorizedWithPut() {
		$this->session([
			'Auth' => [
				'User' => [
					'id' => 1,
					'username' => 'mariano',
					'avatar' => '../img/avatar.png',
					'group_id' => 5,
				]
			]
		]);

		$data = [
			'first_name' => 'First Name',
			'last_name' => 'Last Name',
			'twitter' => 'Mariano',
			'facebook' => 'Mariano',
			'biography' => 'mariano biography',
			'signature' => 'mariano signature',
			'avatar_file' => [
				'name' => 'tmp_avatar.png',
				'tmp_name' => TEST_TMP . 'tmp_avatar.png',
				'error' => UPLOAD_ERR_OK,
				'type' => 'image/png',
				'size' => 6000
			]
		];

		$this->put('/users/account', $data);

		$this->assertResponseOk();

		$Users = TableRegistry::get('Users');
		$user = $Users->find()->where(['id' => 1])
			->select(['first_name', 'last_name', 'twitter', 'facebook', 'biography', 'signature'])
			->first()->toArray();

		unset($data['avatar_file']);

		$this->assertEquals($data, $user);
	}

/**
 * Test settings unauthorized method
 *
 * @return void
 */
	public function testSettingsUnauthorized() {
		$this->get('/users/settings');
		$this->assertResponseSuccess();
		$this->assertRedirect(['controller' => 'users', 'action' => 'login']);
	}

/**
 * Test settings authorized method
 *
 * @return void
 */
	public function testSettingsAuthorized() {
		$this->session([
			'Auth' => [
				'User' => [
					'id' => 1,
					'username' => 'mariano',
					'avatar' => '../img/avatar.png',
					'group_id' => 5,
				]
			]
		]);

		$this->get('/users/settings');

		$this->assertResponseOk();
	}

/**
 * Test account authorized with put method for Email
 *
 * @return void
 */
	public function testSettingsAuthorizedWithPutForEmail() {
		$this->session([
			'Auth' => [
				'User' => [
					'id' => 1,
					'username' => 'mariano',
					'avatar' => '../img/avatar.png',
					'group_id' => 5,
				]
			]
		]);

		$data = [
			'method' => 'email',
			'email' => 'mynew@email.io',
		];

		$this->put('/users/settings', $data);

		$this->assertResponseOk();
		$this->assertResponseContains('infobox-success');

		$Users = TableRegistry::get('Users');
		$user = $Users->find()->where(['id' => 1])
			->select(['email'])
			->first()->toArray();

		unset($data['method']);

		$this->assertEquals($data, $user);
	}

/**
 * Test account authorized with put method for Email
 *
 * @return void
 */
	public function testSettingsAuthorizedWithPutForPassword() {
		$this->session([
			'Auth' => [
				'User' => [
					'id' => 1,
					'username' => 'mariano',
					'avatar' => '../img/avatar.png',
					'group_id' => 5,
				]
			]
		]);

		$data = [
			'method' => 'password',
			'old_password' => 'password',
			'password' => '12345678',
			'password_confirm' => '12345678'
		];

		$this->put('/users/settings', $data);

		$this->assertResponseOk();
		$this->assertResponseContains('infobox-success');

		$Users = TableRegistry::get('Users');
		$user = $Users->find()->where(['id' => 1])
			->select(['password'])
			->first();

		unset($data['method'], $data['old_password'], $data['password_confirm']);

		$this->assertTrue((new DefaultPasswordHasher)->check($data['password'], $user->password));
	}

/**
 * Test profile method
 *
 * @return void
 */
	public function testProfile() {
		$this->get(['_name' => 'users-profile', 'slug' => 'mariano']);
		$this->assertResponseOk();
		$this->assertResponseContains('My awesome biography');
	}

/**
 * Test profile unauthorized method
 *
 * @return void
 */
	public function testPremiumUnauthorized() {
		$this->get(['controller' => 'users', 'action' => 'premium']);
		$this->assertResponseSuccess();
		$this->assertRedirect(['controller' => 'users', 'action' => 'login']);
	}

/**
 * Test profile authorized method
 *
 * @return void
 */
	public function testPremiumAuthorized() {
		$this->session([
			'Auth' => [
				'User' => [
					'id' => 1,
					'username' => 'mariano',
					'avatar' => '../img/avatar.png',
					'group_id' => 5,
				]
			]
		]);

		$this->get(['controller' => 'users', 'action' => 'premium']);
		$this->assertResponseSuccess();
		$this->assertResponseContains('€');
	}
}
