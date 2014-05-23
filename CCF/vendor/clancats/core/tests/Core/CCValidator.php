<?php
/**
 * CCF Validator Test suite
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2014 ClanCats GmbH
 *
 * @group Core
 * @group CCValidator
 */
class CCValidator_Test extends \PHPUnit_Framework_TestCase
{
	/**
	 * CCValidator::create tests
	 */
	public function test_create()
	{
		$validator = CCValidator::create( array( 'username' => 'mario' ) );
		
		$this->assertTrue( $validator instanceof CCValidator );
		
		$this->assertEquals( 'mario', $validator->data( 'username' ) );
	}
	
	/**
	 * CCValidator::post tests
	 */
	public function test_post()
	{
		CCIn::instance( new CCIn_Instance( array(), array( 'agb' => 1 ), array(), array(), array() ) );
		
		$validator = CCValidator::post( array( 'agb' => (bool) CCIn::post( 'agb' ) ) );
		
		$this->assertTrue( $validator instanceof CCValidator );
		
		$this->assertInternalType( 'bool', $validator->data( 'agb' ) );
		
		$this->assertTrue( $validator->data( 'agb' ) );
	}
	
	/**
	 * CCValidator::required tests
	 */
	public function test_required()
	{
		$validator = new CCValidator( array( 'username' => 'mario', 'password' => '', 'field' => null ) );
		
		$this->assertTrue( $validator->required( 'username' ) );
		$this->assertTrue( $validator->not_required( 'firstname' ) );
		
		$this->assertTrue( $validator->success() );
		$this->assertFalse( $validator->failure() );
		
		$this->assertFalse( $validator->required( 'passord' ) );
		$this->assertFalse( $validator->not_required( 'username' ) );
		
		$this->assertTrue( $validator->failure() );
		$this->assertFalse( $validator->success() );
		
		$this->assertInternalType( 'array', $validator->failed() );
		
		$this->assertFalse( $validator->required( 'field' ) );
		
		// test rules syntax
		$this->assertTrue( $validator->rules( 'username', 'required' ) );
		
		// test spaces breaks etc.
		$validator = new CCValidator( array( 'name' => '     ' ) );
		
		$this->assertFalse( $validator->required( 'name' ) );
		
		// test not existing
		$this->assertFalse( $validator->required( 'notexisting' ) );
		
		// test numbers
		$validator = new CCValidator( array( 'count' => '0' ) );
		
		$this->assertTrue( $validator->required( 'count' ) );
		
		$validator = new CCValidator( array( 'count' => 0 ) );
		
		$this->assertTrue( $validator->required( 'count' ) );
	}
	
	/**
	 * CCValidator::email tests
	 */
	public function test_email()
	{
		$validator = new CCValidator( array( 
			'email1' => 'info@example.com',
			'email2' => 'info@example',
			'email3' => 'info@@example.com',
			'email4' => 'info..sdf4323fsd@ex-ample.cm',
			'email5' => '',
		));
		
		$this->assertTrue( $validator->email( 'email1' ) );
		$this->assertFalse( $validator->not_email( 'email1' ) );
		$this->assertFalse( $validator->email( 'email2' ) );
		$this->assertFalse( $validator->email( 'email3' ) );
		$this->assertTrue( $validator->email( 'email4' ) );
		$this->assertFalse( $validator->email( 'email5' ) );
		
		// test rules syntax
		$this->assertTrue( $validator->rules( 'email1', 'required', 'email' ) );
	}
	
	/**
	 * CCValidator::ip tests
	 */
	public function test_ip()
	{
		$validator = new CCValidator( array( 
			'ip1' => '127.0.0.1',
			'ip2' => '127.0000.0.1',
			'ip3' => '127.0.0.1.1',
			'ip4' => '266.0.0.2',
			'ip5' => '255.255.255.255',
		));
		
		$this->assertTrue( $validator->ip( 'ip1' ) );
		$this->assertFalse( $validator->not_ip( 'ip1' ) );
		$this->assertFalse( $validator->ip( 'ip2' ) );
		$this->assertFalse( $validator->ip( 'ip3' ) );
		$this->assertFalse( $validator->ip( 'ip4' ) );
		$this->assertTrue( $validator->ip( 'ip5' ) );
	}
	
	/**
	 * CCValidator::url tests
	 */
	public function test_url()
	{
		$validator = new CCValidator( array( 
			'1' => 'http://clancats.io',
			'2' => 'http//clancats.io',
			'3' => 'clancats.com',
			'4' => 'ftp://cc.io',
		));
		
		$this->assertTrue( $validator->url( '1' ) );
		$this->assertFalse( $validator->url( '2' ) );
		$this->assertFalse( $validator->url( '3' ) );
		$this->assertTrue( $validator->url( '4' ) );
	}
	
	/**
	 * CCValidator::regex tests
	 */
	public function test_regex()
	{
		$validator = new CCValidator( array( 
			'1' => 'foo',
			'2' => 'bbo',
		));
		
		$this->assertTrue( $validator->regex( '1', "/^foo$/" ) );
		$this->assertFalse( $validator->regex( '2', "/^foo$/" ) );
	}
	
	/**
	 * CCValidator::numeric tests
	 */
	public function test_numeric()
	{
		$validator = new CCValidator( array( 
			'1' => '123',
			'2' => 1242,
			'3' => '123.34',
			'4' => '1nope23',
			'5' => '122,0',
			'6' => '12+2'
		));
		
		$this->assertTrue( $validator->numeric( '1' ) );
		$this->assertFalse( $validator->not_numeric( '1' ) );
		$this->assertTrue( $validator->numeric( '2' ) );
		$this->assertTrue( $validator->numeric( '3' ) );
		$this->assertFalse( $validator->numeric( '4' ) );
		$this->assertFalse( $validator->numeric( '5' ) );
		$this->assertFalse( $validator->numeric( '6' ) );
	}
	
	/**
	 * CCValidator::min_num tests
	 */
	public function test_min_num()
	{
		$validator = new CCValidator( array( 
			'1' => '5',
			'2' => 15,
			'3' => '-5',
			'4' => '3x',
		));
		
		$this->assertTrue( $validator->min_num( '1', 5 ) );
		$this->assertTrue( $validator->min_num( '1', 4 ) );
		$this->assertFalse( $validator->min_num( '1', 6 ) );
		
		$this->assertTrue( $validator->min_num( '2', 14 ) );
		$this->assertFalse( $validator->min_num( '2', 16 ) );
		
		$this->assertTrue( $validator->min_num( '3', -6 ) );
		$this->assertFalse( $validator->min_num( '3', -4 ) );
		
		$this->assertFalse( $validator->min_num( '4', 1 ) );
		
		// test rules syntax
		$this->assertTrue( $validator->rules( '1', 'required', 'min_num:4' ) );
		$this->assertFalse( $validator->rules( '1', 'required', 'min_num:6' ) );
	}
	
	/**
	 * CCValidator::max_num tests
	 */
	public function test_max_num()
	{
		$validator = new CCValidator( array( 
			'1' => '5',
			'2' => 15,
			'3' => '-5',
			'4' => '3x',
		));
		
		$this->assertTrue( $validator->max_num( '1', 5 ) );
		$this->assertTrue( $validator->max_num( '1', 6 ) );
		$this->assertFalse( $validator->max_num( '1', 4 ) );
		
		$this->assertTrue( $validator->max_num( '2', 16 ) );
		$this->assertFalse( $validator->max_num( '2', 14 ) );
		
		$this->assertTrue( $validator->max_num( '3', -4 ) );
		$this->assertFalse( $validator->max_num( '3', -6 ) );
		
		$this->assertFalse( $validator->max_num( '4', 1 ) );
		
		// test rules syntax
		$this->assertTrue( $validator->rules( '1', 'required', 'max_num:6' ) );
		$this->assertFalse( $validator->rules( '1', 'required', 'max_num:4' ) );
	}
	
	/**
	 * CCValidator::between_num tests
	 */
	public function test_between_num()
	{
		$validator = new CCValidator( array( 
			'1' => '5',
			'2' => 15,
			'3' => '-5',
			'4' => '3x',
		));
		
		$this->assertTrue( $validator->between_num( '1', 5, 8 ) );
		$this->assertTrue( $validator->between_num( '1', 3, 10 ) );
		$this->assertFalse( $validator->between_num( '1', 6, 12 ) );
		
		$this->assertTrue( $validator->between_num( '2', 13, 20 ) );
		$this->assertFalse( $validator->between_num( '2', 16, 20 ) );
		
		$this->assertTrue( $validator->between_num( '3', -10, 5 ) );
		$this->assertFalse( $validator->between_num( '3', -20, -10 ) );
		
		$this->assertFalse( $validator->between_num( '4', 1, 5 ) );
		
		// test rules syntax
		$this->assertTrue( $validator->rules( '1', 'required', 'between_num:1,10' ) );
		$this->assertFalse( $validator->rules( '1', 'required', 'between_num:10,100' ) );
	}
	
	/**
	 * CCValidator::min, max and between tests
	 */
	public function test_string_size()
	{
		$validator = new CCValidator( array( 
			'1' => 15,
			'2' => 'foo',
			'3' => '',
		));
		
		$this->assertTrue( $validator->min( '1', 2 ) );
		$this->assertFalse( $validator->min( '1', 6 ) );
		$this->assertTrue( $validator->max( '1', 16 ) );
		$this->assertFalse( $validator->max( '1', 1 ) );
		$this->assertTrue( $validator->between( '1', 0, 2 ) );
		$this->assertFalse( $validator->between( '1', 10, 20 ) );
		
		$this->assertTrue( $validator->between( '2', 2, 6 ) );
		$this->assertFalse( $validator->between( '2', 0, 2 ) );
		
		$this->assertTrue( $validator->between( '3', 0, 16 ) );
		$this->assertFalse( $validator->between( '3', 2, 6 ) );
		
		$this->assertTrue( $validator->rules( '2', 'min:2', 'max:6' ) );
		$this->assertFalse( $validator->rules( '2', 'min:4', 'max:6' ) );
	}
	
	/**
	 * CCValidator::in
	 */
	public function test_in()
	{
		$validator = new CCValidator( array( 
			'salutation' => 'mr.',
			'salutation_2' => 'nope',
		));
		
		$salutations = array( 'mr.', 'mrs.' );
		
		$this->assertTrue( $validator->in( 'salutation', $salutations ) );
		$this->assertFalse( $validator->in( 'salutation_2', $salutations ) );
	}
	
	/**
	 * CCValidator::match
	 */
	public function test_match()
	{
		$validator = new CCValidator( array( 
			'password' => 'test',
			'password_2' => 'test',
			'password_3' => 'wrong',
		));
		
		$this->assertTrue( $validator->match( 'password', 'password_2' ) );
		$this->assertFalse( $validator->match( 'password', 'password_3' ) );
		$this->assertFalse( $validator->match( 'password', 'notexisting' ) );
	}
	
	/**
	 * CCValidator::true
	 */
	public function test_true()
	{
		$validator = new CCValidator( array( 
			'1' => 'true',
			'2' => '1',
			'3' => true,
		));
		
		$this->assertFalse( $validator->true( '1' ) );
		$this->assertTrue( $validator->not_true( '1' ) );
		$this->assertFalse( $validator->true( '2' ) );
		$this->assertTrue( $validator->true( '3' ) );
	}
	
	/**
	 * CCValidator::false
	 */
	public function test_false()
	{
		$validator = new CCValidator( array( 
			'1' => 'false',
			'2' => '0',
			'3' => false,
		));
		
		$this->assertFalse( $validator->false( '1' ) );
		$this->assertTrue( $validator->not_false( '1' ) );
		$this->assertFalse( $validator->false( '2' ) );
		$this->assertTrue( $validator->false( '3' ) );
	}
	
	/**
	 * CCValidator::negative
	 */
	public function test_negative()
	{
		$validator = new CCValidator( array( 
			'1' => '',
			'2' => 0,
			'3' => false,
			'4' => 'true',
		));
		
		$this->assertTrue( $validator->negative( '1' ) );
		$this->assertTrue( $validator->negative( '2' ) );
		$this->assertTrue( $validator->negative( '3' ) );
		$this->assertFalse( $validator->negative( '4' ) );
	}
	
	/**
	 * CCValidator::positive
	 */
	public function test_positive()
	{
		$validator = new CCValidator( array( 
			'1' => '',
			'3' => 'true',
			'4' => '1',
			'4' => false,
		));
		
		$this->assertFalse( $validator->positive( '1' ) );
		$this->assertFalse( $validator->positive( '2' ) );
		$this->assertTrue( $validator->positive( '3' ) );
		$this->assertFalse( $validator->positive( '4' ) );
	}
	
	/**
	 * CCValidator::positive
	 */
	public function test_equal()
	{
		$validator = new CCValidator( array( 
			'1' => 'foo',
			'2' => 123,
		));
		
		$this->assertTrue( $validator->equal( '1', 'foo' ) );
		$this->assertTrue( $validator->equal( '2', 123 ) );
		$this->assertFalse( $validator->equal( '2', 0 ) );
		$this->assertFalse( $validator->equal( '2', '0' ) );
	}
	
	/**
	 * CCValidator::valid_date
	 */
	public function test_valid_date()
	{
		$validator = new CCValidator( array( 
			'1' => '2014/05/12',
			'2' => '1-2-2014',
		));
		
		$this->assertTrue( $validator->date_format( '1', 'Y/m/d' ) );
		$this->assertFalse( $validator->date_format( '1', 'Y/d/m' ) );
		$this->assertTrue( $validator->date_format( '2', 'j-n-Y' ) );
	}
	
	/**
	 * CCValidator::rule
	 */
	public function test_rule()
	{
		CCValidator::rule( 'test', function( $key, $value ) 
		{
			return true;
		});
		
		CCValidator::rule( 'testfalse', function( $key, $value ) 
		{
			return false;
		});
		
		$validator = new CCValidator( array( 
			'1' => 'blabla',
		));
		
		$this->assertTrue( $validator->test( '1' ) );
		$this->assertFalse( $validator->testfalse( '1' ) );
	}
	
	/**
	 * CCValidator::data
	 */
	public function test_all_data()
	{		
		$validator = new CCValidator( array( 
			'1' => 'blabla',
		));
		
		$this->assertInternalType( 'array', $validator->data() );
	}
	
	/**
	 * CCValidator::set
	 */
	public function test_set()
	{		
		$validator = new CCValidator( array( 
			'1' => 'foo',
		));
		
		$validator->set( 'bar', 'Bar' );
		
		$this->assertEquals( 'Bar', $validator->data('bar') );
		$this->assertEquals( 2, count( $validator->data() ) );
	}
	
	/**
	 * CCValidator::label test
	 */
	public function test_label()
	{		
		$validator = new CCValidator( array( 'username' => 'Mario' ) );
		
		$validator->label( 'username', 'Benutzername' );
		
		$validator->label( array(
			'password' => 'Passwort',
			'retain' => 'Remember me',
		));
	}
	
	/**
	 * CCValidator::label invalid data test
	 *
	 * @expectedException InvalidArgumentException
	 */
	public function test_label_invalid_argument()
	{		
		$validator = new CCValidator( array( 'username' => 'Mario' ) );
		
		$validator->label( 'username' );
		$validator->label( null );
		$validator->label();
	}
	
	/**
	 * CCValidator:: bad method
	 *
	 * @expectedException        \BadMethodCallException
	 */
	public function test_bad_method()
	{		
		$validator = new CCValidator( array( 
			'1' => 'not_important',
		));
		
		$validator->doesntexists( '1' );
	}
	
	/**
	 * CCValidator::rules
	 */
	public function test_rules()
	{		
		$validator = new CCValidator( array( 
			'1' => '155',
		));
		
		$this->assertTrue( $validator->rules( '1', array( 'min_num:150', 'max_num:250' ) ) );
		$this->assertTrue( $validator->rules( '1', 'min_num:150', 'max_num:250' ) );
	}
}