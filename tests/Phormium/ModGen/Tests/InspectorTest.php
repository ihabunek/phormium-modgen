<?php

namespace Phormium\ModGen\Tests;

use Phormium\DB;
use Phormium\ModGen\Generator;

class InspectorTest extends \PHPUnit_Framework_TestCase
{
	private $inspector;

    public static function setUpBeforeClass()
    {
        DB::configure(PHORMIUM_CONFIG_FILE);
    }

	public function setUp()
	{
		$generator = new Generator();
		$this->inspector = $generator->getInspector('testdb');
	}

	public function testTableExists()
	{
		$this->assertTrue($this->inspector->tableExists('testdb', 'bohemian_rhapsody'));
		$this->assertTrue($this->inspector->tableExists('testdb', 'killer_queen'));
		$this->assertTrue($this->inspector->tableExists('testdb', 'slightly_mad'));

		$this->assertFalse($this->inspector->tableExists('testdb', 'bohemian_tragedy'));
		$this->assertFalse($this->inspector->tableExists('testdb', 'killer_king'));
		$this->assertFalse($this->inspector->tableExists('testdb', 'totally_bonkers'));
	}

	public function testGetTables()
	{
		$expected = array (
			'bohemian_rhapsody',
			'killer_queen',
			'slightly_mad'
		);
		$actual = $this->inspector->getTables('testdb');
		$this->assertSame($expected, $actual);
	}

	public function testGetColumns()
	{
		$expected = array (
			'killer',
			'queen',
			'gunpowder',
			'gelatine',
			'dynamite',
			'laser_beam'
		);

		$actual = $this->inspector->getColumns('testdb', 'killer_queen');
		$this->assertSame($expected, $actual);

		$expected = array (
			'is_this',
			'the_real',
			'life',
		);

		$actual = $this->inspector->getColumns('testdb', 'bohemian_rhapsody');
		$this->assertSame($expected, $actual);

		$expected = array (
			'it',
			'finally',
			'happened',
		);

		$actual = $this->inspector->getColumns('testdb', 'slightly_mad');
		$this->assertSame($expected, $actual);
	}

	public function testGetColumnsNonexistantTable()
	{
		$expected = array();
		$actual = $this->inspector->getColumns('testdb', 'nonexistant');
		$this->assertSame($expected, $actual);
	}

	public function testGetPKColumns()
	{
		$expected = array (
			'is_this',
		);

		$actual = $this->inspector->getPKColumns('testdb', 'bohemian_rhapsody');
		$this->assertSame($expected, $actual);

		$expected = array (
			'killer',
			'queen',
		);

		$actual = $this->inspector->getPKColumns('testdb', 'killer_queen');
		$this->assertSame($expected, $actual);

		$expected = array ();

		$actual = $this->inspector->getPKColumns('testdb', 'slightly_mad');
		$this->assertSame($expected, $actual);
	}
}
