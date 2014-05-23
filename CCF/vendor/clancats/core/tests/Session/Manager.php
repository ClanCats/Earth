<?php
/**
 * Session tests
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2014 ClanCats GmbH
 *
 * @group Session
 * @group Session_Manager
 */
class Test_Session_Manager extends PHPUnit_Framework_TestCase
{
	/**
	 * prepare the configuration
	 *
	 * @return void
	 */
	public static function setUpBeforeClass() 
	{
		CCConfig::create( 'session' )->_data = CCConfig::create( 'Core::phpunit/session' )->_data;
	}
	
	/**
	 * test Session\Manager::create 
	 */
	public function test_create()
	{
		$manager = Session\Manager::create( 'file' );
	
		$this->assertTrue( $manager instanceof \Session\Manager );
		
		// got a driver?
		$this->assertTrue( $manager->driver() instanceof \Session\Manager_File );
		
		// test CCSession getter
		$this->assertEquals( $manager, CCSession::manager( 'file' ) );
		
		// get another session
		$this->assertTrue( CCSession::manager() instanceof \Session\Manager );
		$this->assertTrue( CCSession::manager()->driver() instanceof \Session\Manager_Array );
	}
	
	/**
	 * test Session\Manager::read 
	 */
	public function test_read()
	{
		$manager = Session\Manager::create();
		
		$this->assertTrue( is_string( $manager->id ) );
		
		$this->assertTrue( strlen( $manager->id ) === 32 );
		
		$this->assertTrue( is_string( $manager->fingerprint ) );
		
		//$this->assertEquals( time(), $manager->last_active );
		
		$manager->last_active = "foo";
		
		$this->assertEquals( "foo", $manager->last_active );
		
		$manager->read();
		
		$this->assertEquals( time(), $manager->last_active );
	}
	
	/**
	 * test Session\Manager::write 
	 */
	public function test_write()
	{
		$manager = Session\Manager::create();
		
		$manager->foo = "bar";
		
		$this->assertEquals( "bar", $manager->foo );
		
		$manager->write();
		
		$this->assertEquals( "bar", $manager->foo );
		
		$manager->read();
		
		$this->assertEquals( "bar", $manager->foo );
		
		// multi dimension
		$manager->set( 'a.b', "c" );
		$manager->set( 'a.c', "b" );
		
		$this->assertEquals( "c", $manager->get('a.b') );
		$this->assertTrue( is_array( $manager->get('a') ) );
		$this->assertTrue( is_array( $manager->a ) );
		
		$manager->write();
		$manager->read();
		
		$this->assertEquals( "c", $manager->get('a.b') );
		$this->assertTrue( is_array( $manager->get('a') ) );
		$this->assertTrue( is_array( $manager->a ) );
	}
	
	/**
	 * test Session\Manager::regenerate 
	 */
	public function test_regenerate()
	{
		$session_id = Session\Manager::create()->id;
		$fingerprint = Session\Manager::create()->fingerprint;
		
		$this->assertEquals( $session_id, Session\Manager::create()->id );
		
		$this->assertEquals( $session_id, CCSession::manager()->id );
		
		CCSession::manager()->set( 'foo', 'bar' );
		
		CCSession::manager()->regenerate();
		
		$this->assertEquals( 'bar', CCSession::manager()->get( 'foo' ) );
		
		$this->assertTrue( $session_id != CCSession::manager()->id );
		
		$this->assertTrue( $fingerprint != CCSession::manager()->fingerprint );
	}
	
	/**
	 * test Session\Manager::destroy 
	 */
	public function test_destroy()
	{
		$session_id = Session\Manager::create()->id;
		$fingerprint = Session\Manager::create()->fingerprint;
		
		$this->assertEquals( $session_id, Session\Manager::create()->id );
		
		$this->assertEquals( $session_id, CCSession::manager()->id );
		
		CCSession::manager()->set( 'foo', 'bar' );
		
		CCSession::manager()->destroy();
		
		$this->assertFalse( CCSession::manager()->has( 'foo' ) );
		
		$this->assertTrue( $session_id != CCSession::manager()->id );
		
		$this->assertTrue( $fingerprint != CCSession::manager()->fingerprint );
	}
	
	/**
	 * test Session\Manager::gc 
	 */
	public function test_gc()
	{
		$manager = Session\Manager::create();
		
		$manager->foo = "bar";
		
		$manager->write();
		$manager->read();
		
		$this->assertEquals( "bar", $manager->foo );
		
		$manager->gc();
		$manager->read();
		
		$this->assertFalse( CCSession::manager()->has( 'bar' ) );
	}
}