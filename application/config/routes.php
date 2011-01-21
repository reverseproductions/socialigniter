<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
| 	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There is one reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
*/

$route['default_controller'] 					= 'index';

/* Feeds */
$route['feed/(:any)'] 							= '$1/feed';
$route['feed/comments']							= 'feed/comments';
$route['feed']		 							= 'feed/index';

/* Categories */
$route['api/categories/view/(:any)/(:any)']		= 'api/categories/view';
$route['api/categories/view']					= 'api/categories/view';
$route['api/categories/all']					= 'api/categories/all';
$route['api/categories/create']					= 'api/categories/create';

/* Comments */
$route['api/comments/content/(:any)/(:any)']	= 'api/comments/content/$1/$2';
$route['api/comments/viewed/(:any)/(:any)']		= 'api/comments/viewed/$1/$2';
$route['api/comments/approve/(:any)/(:any)']	= 'api/comments/approve/$1/$2';
$route['api/comments/destroy/(:any)/(:any)']	= 'api/comments/destroy/$1/$2';
$route['api/comments/content']					= 'api/comments/content';
$route['api/comments/recent']					= 'api/comments/recent';
$route['api/comments/create']					= 'api/comments/create';
$route['api/comments/public']					= 'api/comments/public';
$route['api/comments/new']						= 'api/comments/new';

/* Content */
$route['api/content/view/(:any)/(:any)']		= 'api/content/view/$1/$2';
$route['api/content/viewed/(:any)/(:any)']		= 'api/content/viewed/$1/$2';
$route['api/content/modify/(:any)/(:any)']		= 'api/content/modify/$1/$2';
$route['api/content/approve/(:any)/(:any)']		= 'api/content/approve/$1/$2';
$route['api/content/save/(:any)/(:any)']		= 'api/content/save/$1/$2';
$route['api/content/publish/(:any)/(:any)']		= 'api/content/publish/$1/$2';
$route['api/content/destroy/(:any)/(:any)']		= 'api/content/destroy/$1/$2';
$route['api/content/recent']					= 'api/content/recent';
$route['api/content/create']					= 'api/content/create';

/* API Modules */
$route['api/(:any)/(:any)/(:any)/(:any)'] 		= '$1/api/$2/$3/$4';
$route['api/(:any)/(:any)/(:any)'] 				= '$1/api/$2/$3';
$route['api/(:any)/(:any)'] 					= '$1/api/$2';
$route['api/(:any)'] 							= 'api/index';

$route['api/sandbox_results']				 	= 'api/index/sandbox_results';
$route['api/sandbox'] 							= 'api/index/sandbox';
$route['api']		 							= 'api/index';

/* Connections */
$route['connections/delete/(:num)']				= 'connections/delete';
$route['connections/delete']					= 'connections/delete';
$route['connections/(:any)/(:any)/(:any)'] 		= '$1/connections/$2/$3';
$route['connections/(:any)/(:any)'] 			= '$1/connections/$2';
$route['connections/(:any)'] 					= '$1/connections/index';

/* Home */
$route['home/(:any)/(:any)/(:any)/(:any)'] 		= '$1/home/$2/$3/$4';
$route['home/(:any)/(:any)/(:any)'] 			= '$1/home/$2/$3';
$route['home/(:any)/manage'] 					= 'home/manage/$1';

$route['home/pages/manage/(:num)']				= 'pages/editor';
$route['home/pages/create']						= 'pages/editor';

$route['home/comments/(:any)'] 					= 'home/comments/$1';
$route['home/(:any)/(:any)'] 					= '$1/home/$2';
$route['home/(:any)'] 							= 'home';
$route['home/comments'] 						= 'home/comments';
$route['home/friends']							= 'home/friends';
$route['home/mentions'] 						= 'home/mentions';
$route['home/likes']							= 'home/likes';

/* Home Partials */
$route['home/item_timeline']					= 'home/item_timeline';
$route['home/item_manage']						= 'home/item_manage';
$route['home/category_editor']					= 'home/category_editor';
$route['home'] 									= 'home';

/* Profiles */
$route['profile/:any/image'] 					= 'profile/image';
$route['profile/:any'] 							= 'profile/index';

/* Settings */
$route['settings/(:any)'] 						= '$1/settings/index';
$route['settings/profile'] 						= 'settings/profile';
$route['settings/account'] 						= 'settings/account';
$route['settings/password'] 					= 'settings/password';
$route['settings/mobile'] 						= 'settings/mobile';
$route['settings/connections'] 					= 'settings/connections';
$route['settings/modules'] 						= 'settings/modules';
$route['settings/home']							= 'settings/home';
$route['settings/comments']						= 'settings/comments';
$route['settings/api']							= 'settings/api';
$route['settings/update']						= 'settings/update';
$route['settings/users']						= 'settings/users';
$route['settings/pages']						= 'settings/pages';
$route['settings'] 								= 'settings';

$route['pages/(:any)']							= 'index/index';

/* End of file routes.php */
/* Location: ./application/config/routes.php */