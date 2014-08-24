<?php
/*
 *---------------------------------------------------------------
 * Admin Index
 *---------------------------------------------------------------
 * 
 * Admin dashboard
 */
Admin\Registry::add( 'index', function( $module ) 
{
	$module->title = 'Dashboard';
	$module->controller = 'Admin::Index';
	$module->icon = "el-icon-dashboard";
});

/*
 *---------------------------------------------------------------
 * Users & Groups Bundle
 *---------------------------------------------------------------
 * 
 * Users administration bundle
 */
CCFinder::bundle( 'Earth\\Users', CCFPATH.'earth/bundles/users/' );

// register the admin module
Admin\Registry::add( 'users', function( $module ) 
{
	$module->title = __('Earth\\Users::controller/admin.topic');
	$module->controller = 'Earth\\Users::Admin';
	$module->icon = "el-icon-user";
	$module->sidebar = "Earth\\Users::sidebar/users.view";
	
	// groups submodule
	$module->add( 'groups', function( $module ) 
	{
		$module->title = __('Earth\\Users::controller/group.topic');
		$module->controller = 'Earth\\Users::Group';
		$module->sidebar = "Earth\\Users::sidebar/groups.view";
	});
});

/*
 *---------------------------------------------------------------
 * Page Bundle
 *---------------------------------------------------------------
 * 
 * Users administration bundle
 */
CCFinder::bundle( 'Earth\\Pages', CCFPATH.'earth/bundles/pages/' );

// register the earth router
CCEvent::mind( 'ccf.router.resolve.after', array( '\\Earth\\Pages\\Router', 'resolve' ) );

// register the admin module
Admin\Registry::add( 'pages', function( $module ) 
{
	$module->title = __('Earth\\Pages::controller/admin.topic');
	$module->controller = 'Earth\\Pages::Admin';
	$module->icon = "el-icon-th-list";
	$module->sidebar = "Earth\\Pages::sidebar/pages.view";
});

/*
 *---------------------------------------------------------------
 * Editor Bundle
 *---------------------------------------------------------------
 * 
 * Text editor
 */
CCFinder::bundle( 'Earth\\Editor', CCFPATH.'earth/bundles/editor/' );

// set the current editor from configuration
Earth\Editor\Manager::set_editor( 'markdown' );

/*
 *---------------------------------------------------------------
 * Prepare all admin modules
 *---------------------------------------------------------------
 * 
 * This step will finally register the routes and build the 
 * navigation tree.
 */
Admin\Registry::prepare();