<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class PremiumTransactionsFixture extends TestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = [
		'id' => ['type' => 'integer'],
		'user_id' => ['type' => 'integer'],
		'premium_offer_id' => ['type' => 'integer'],
		'premium_discount_id' => ['type' => 'integer', 'null' => true],
		'price' => ['type' => 'float'],
		'tax' => ['type' => 'float'],
		'txn' => ['type' => 'string', 'length' => 255],
		'action' => ['type' => 'text', 'default' => 'new'],
		'period' => ['type' => 'integer'],
		'name' => ['type' => 'string', 'length' => 50],
		'country' => ['type' => 'string', 'length' => 50],
		'city' => ['type' => 'string', 'length' => 50],
		'address' => ['type' => 'string', 'length' => 255],
		'created' => ['type' => 'datetime'],
		'modified' => ['type' => 'datetime'],
		'_constraints' => [
			'primary' => ['type' => 'primary', 'columns' => ['id']],
		],
		'_options' => [
			'engine' => 'InnoDB', 'collation' => 'utf8_general_ci'
		],
	];

/**
 * Records
 *
 * @var array
 */
	public $records = [
		[
			'id' => 1,
			'user_id' => 1,
			'premium_offer_id' => 1,
			'premium_discount_id' => null,
			'price' => 3.17,
			'tax' => 0.17,
			'txn' => 'W13078622810RHB95',
			'action' => 'new',
			'period' => 1,
			'name' => 'Lorem ipsum',
			'country' => 'FRANCE',
			'city' => 'Paris',
			'address' => 'Lorem ipsum dolor sit amet',
			'created' => '2014-11-21 10:57:49',
			'modified' => '2014-11-21 10:57:49'
		]
	];

}
