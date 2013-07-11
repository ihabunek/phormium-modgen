<?php

namespace Phormium\ModGen\Tests;

use Phormium\ModGen\Generator;

class GenerateTest extends \PHPUnit_Framework_TestCase
{
	private $generator;

	private $namespace = 'God\\Save\\The\\Queen';

    public static function setUpBeforeClass()
    {
        DB::configure(PHORMIUM_CONFIG_FILE);
    }

	public function setUp()
	{
		$this->generator = new Generator();
	}

	/*
	 * Check generated classes look as expected.
	 */

	public function testGenerateCode1()
	{
		$actual = $this->generator->generateModelCode('testdb', 'bohemian_rhapsody', $this->namespace);
		$expected = file_get_contents(__DIR__ . '/../../../expected/BohemianRhapsody.php');
		self::assertSame($expected, $actual);

	}

	public function testGenerateCode2()
	{
		$actual = $this->generator->generateModelCode('testdb', 'killer_queen', $this->namespace);
		$expected = file_get_contents(__DIR__ . '/../../../expected/KillerQueen.php');
		self::assertSame($expected, $actual);

	}

	public function testGenerateCode3()
	{
		$actual = $this->generator->generateModelCode('testdb', 'slightly_mad', $this->namespace);
		$expected = file_get_contents(__DIR__ . '/../../../expected/SlightlyMad.php');
		self::assertSame($expected, $actual);
	}

	/*
	 * Now check they are saved to the right location.
	 */

	public function testGenerate1()
	{
		$this->generator->generateModel('testdb', 'bohemian_rhapsody', $this->namespace);
		$actual = file_get_contents(__DIR__ . '/../../../../target/God/Save/The/Queen/BohemianRhapsody.php');
		$expected = file_get_contents(__DIR__ . '/../../../expected/BohemianRhapsody.php');
		self::assertSame($expected, $actual);
	}

	public function testGenerate2()
	{
		$this->generator->generateModel('testdb', 'killer_queen', $this->namespace);
		$actual = file_get_contents(__DIR__ . '/../../../../target/God/Save/The/Queen/KillerQueen.php');
		$expected = file_get_contents(__DIR__ . '/../../../expected/KillerQueen.php');
		self::assertSame($expected, $actual);
	}

	public function testGenerate3()
	{
		$this->generator->generateModel('testdb', 'slightly_mad', $this->namespace);
		$actual = file_get_contents(__DIR__ . '/../../../../target/God/Save/The/Queen/SlightlyMad.php');
		$expected = file_get_contents(__DIR__ . '/../../../expected/SlightlyMad.php');
		self::assertSame($expected, $actual);
	}

	/*
	 * Finally check they can actually be used.
	 */

	public function testUsage1()
	{
		include_once(__DIR__ . '/../../../../target/God/Save/The/Queen/BohemianRhapsody.php');
		$data = \God\Save\The\Queen\BohemianRhapsody::objects()->fetch();

		$this->assertCount(1, $data);
		$this->assertContainsOnlyInstancesOf("\\God\\Save\\The\\Queen\\BohemianRhapsody", $data);

		$item = $data[0];
		$this->assertSame('A', $item->is_this);
		$this->assertSame('B', $item->the_real);
		$this->assertSame('C', $item->life);
	}

	public function testUsage2()
	{
		include_once(__DIR__ . '/../../../../target/God/Save/The/Queen/KillerQueen.php');
		$data = \God\Save\The\Queen\KillerQueen::objects()->fetch();

		$this->assertCount(1, $data);
		$this->assertContainsOnlyInstancesOf("\\God\\Save\\The\\Queen\\KillerQueen", $data);

		$item = $data[0];
		$this->assertSame('1', $item->killer);
		$this->assertSame('2', $item->queen);
		$this->assertSame('3', $item->gunpowder);
		$this->assertSame('X', $item->gelatine);
		$this->assertSame('Y', $item->dynamite);
		$this->assertSame('Z', $item->laser_beam);
	}

	public function testUsage3()
	{
		include_once(__DIR__ . '/../../../../target/God/Save/The/Queen/SlightlyMad.php');
		$data = \God\Save\The\Queen\SlightlyMad::objects()->fetch();

		$this->assertCount(1, $data);
		$this->assertContainsOnlyInstancesOf("\\God\\Save\\The\\Queen\\SlightlyMad", $data);

		$item = $data[0];
		$this->assertSame('6', $item->it);
		$this->assertSame('6', $item->finally);
		$this->assertSame('6', $item->happened);
	}
}