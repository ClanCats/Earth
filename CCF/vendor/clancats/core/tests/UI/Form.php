<?php
/**
 * UI Forms tests
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2014 ClanCats GmbH
 *
 * @group UI
 * @group UI_Form
 */
class Test_UI_Form extends PHPUnit_Framework_TestCase
{
	/**
	 * prepare the configuration
	 *
	 * @return void
	 */
	public static function setUpBeforeClass() 
	{
		CCConfig::create( 'ui' )->_data = CCConfig::create( 'Core::ui' )->_data;
	}
	
	/** 
	 * Form::start tests
	 */
	public function test_start()
	{
		$generated = UI\Form::start();
		$expected = '<form role="form">';
		
		$this->assertEquals( $expected, $generated );
		
		// set a key
		$generated = UI\Form::start( 'phpunit' );
		$expected = '<form role="form" id="phpunit-form">';
		
		$this->assertEquals( $expected, $generated );
		
		// more attributes
		$generated = UI\Form::start( 'phpunit', array( 'method' => 'post' ) );
		$expected = '<form role="form" id="phpunit-form" method="post">';
		
		$this->assertEquals( $expected, $generated );
		
		// clean by ending
		UI\Form::end();
	}
	
	/** 
	 * Form::start tests
	 */
	public function test_end()
	{
		$generated = UI\Form::end();
		$expected = '</form>';
			
		$this->assertEquals( $expected, $generated );
	}
	
	/** 
	 * Form::capture tests
	 */
	public function test_capture()
	{	
		// with key
		$form = UI\Form::capture( '', 'phpunit' );
		$expected = '<form role="form" id="phpunit-form"></form>';
			
		$this->assertEquals( $expected, $form );
		
		// with key and attribute
		$form = UI\Form::capture( '', 'phpunit', array( 'method' => 'post' ) );
		$expected = '<form role="form" id="phpunit-form" method="post"></form>';
			
		$this->assertEquals( $expected, $form );
		
		// with content
		$form = UI\Form::capture( 'foo', 'phpunit', array() );
		$expected = '<form role="form" id="phpunit-form">foo</form>';
			
		$this->assertEquals( $expected, $form );
		
		// with callback
		$form = UI\Form::capture( function() { echo "bar"; }, 'phpunit', array( 'class' => 'form' ) );
		$expected = '<form role="form" id="phpunit-form" class="form">bar</form>';
			
		$this->assertEquals( $expected, $form );
		
		// reverse parameters
		$form = UI\Form::capture( 'phpunit', array(), function() { echo "bar"; });
		$expected = '<form role="form" id="phpunit-form">bar</form>';
			
		$this->assertEquals( $expected, $form );
		
		// instance function in callback
		$form = UI\Form::capture( 'phpunit', array(), function( $f ) 
		{
			echo $f->input( 'user' );
		});
		
		$expected = '<form role="form" id="phpunit-form"><input id="phpunit-form-user-input" name="user" type="text" /></form>';
			
		$this->assertEquals( $expected, $form );
		
		// without key
		$form = UI\Form::capture( null, array(), function( $f ) 
		{
			echo $f->input( 'user' );
		});
		
		$expected = '<form role="form"><input id="user-input" name="user" type="text" /></form>';
			
		$this->assertEquals( $expected, $form );
	}
	
	/** 
	 * Form::input tests
	 */
	public function test_input()
	{
		// without key
		$form = (string) \UI\Form::input( 'username' );
		
		$expected = '<input id="username-input" name="username" type="text" />';
			
		$this->assertEquals( $expected, $form );
		
		// now while a form is opend
		
		\UI\Form::start( 'foo' );
		
		$form = (string) \UI\Form::input( 'username' );
		
		$expected = '<input id="foo-form-username-input" name="username" type="text" />';
			
		$this->assertEquals( $expected, $form );
		
		// test again after closing
		\UI\Form::end();
		
		$form = (string) \UI\Form::input( 'username' )->class( 'form-control' );
		
		$expected = '<input id="username-input" name="username" type="text" class="form-control" />';
			
		$this->assertEquals( $expected, $form );
		
		// change the type
		$form = (string) \UI\Form::input( 'primary', 'email' );
		
		$expected = '<input id="primary-input" name="primary" type="email" />';
			
		$this->assertEquals( $expected, $form );
		
		// add attributes
		$form = (string) \UI\Form::input( 'count', 'number', array( 'data-min' => 5 ) );
		
		$expected = '<input id="count-input" name="count" type="number" data-min="5" />';
			
		$this->assertEquals( $expected, $form );
	}
	
	/** 
	 * Form::label tests
	 */
	public function test_label()
	{
		// simple
		$form = (string) \UI\Form::label( 'username', 'Benutzername' );
		
		$expected = '<label id="username-label" for="username-input">Benutzername</label>';
			
		$this->assertEquals( $expected, $form );
		
		// without text
		$form = (string) \UI\Form::label( 'username' );
	
		$expected = '<label id="username-label" for="username-input">username</label>';
			
		$this->assertEquals( $expected, $form );
		
		// inside of a form
		\UI\Form::start( 'foo' );
		
		$form = (string) \UI\Form::label( 'username' );
		
		$expected = '<label id="foo-form-username-label" for="foo-form-username-input">username</label>';
			
		$this->assertEquals( $expected, $form );
		
		// test again after closing
		\UI\Form::end();
	}
	
	/** 
	 * Form::checkbox tests
	 */
	public function test_checkbox()
	{
		// simple
		$form = (string) \UI\Form::checkbox( 'active', 'Active' );
		
		$expected = '<label><input id="active-check" name="active" type="checkbox" /> Active</label>';
			
		$this->assertEquals( $expected, $form );	
		
		// checked test
		$form = (string) \UI\Form::checkbox( 'active', 'Active', true );
			
		$expected = '<label><input id="active-check" name="active" type="checkbox" checked="checked" /> Active</label>';
				
		$this->assertEquals( $expected, $form );		
	}
}