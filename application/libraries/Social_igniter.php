<?php  if  ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Social Igniter Library
*
* @package		Social Igniter
* @subpackage	Social Igniter Library
* @author		Brennan Novak
* @link			http://social-igniter.com
*
* runs all basic social connections to external sites
*/
 
class Social_igniter
{
	protected $ci;

	function __construct()
	{
		$this->ci =& get_instance();
		
		// Configs
		$this->ci->load->config('activity_stream');		

		// Models
 		$this->ci->load->model('activity_model');
 		$this->ci->load->model('content_model');		
		$this->ci->load->model('pages/pages_model');
		$this->ci->load->model('settings_model');
		$this->ci->load->model('sites_model');		
	}	
	
    // Profile Picture	
	function profile_image($user_id, $image, $email=NULL, $size='medium')
	{
		$picture 	 = '';	
		$nopicture	 = base_url().config_item('users_images_folder').$size.'_'.config_item('profile_nopicture');
		
		if ($image)
		{
			$picture = base_url().config_item('users_images_folder').$user_id.'/'.$size.'_'.$image;
		}
		elseif (config_item('site_gravatar_enabled') == 'TRUE')
		{		
			$this->ci->load->helper('gravatar');
			$picture = gravatar($email, "X", config_item('users_images_'.$size.'_width'), $nopicture);
		}
		else
		{
			$picture = $nopicture;
		}
				
		return $picture;
	}	
    
    // Generate Item
    function render_item($activity)
    {
    	$data 		= json_decode($activity->data);
    	$item 		= $this->render_item_status($activity, $data);
    	
    	if ($activity->type != 'status')
    	{
    		$item .= $this->render_item_content($activity->type, $data);
    	}
   
    	return $item;
    }
    
    // Generate Status
    function render_item_status($activity, $data)
    {
    	$has_url	= property_exists($data, 'url');
    	$has_title	= property_exists($data, 'title');    
    
    	// Status
    	if ($activity->type == 'status')
    	{
    		return $this->get_content($activity->content_id)->content;
		}
				
		// Has Status
    	$has_status = property_exists($data, 'status');

		if ($has_status)
		{
			return $object->status;
		}
		// Makes 'posted an article'
       	else
    	{
    		$verb		= item_verb($this->ci->lang->line('verbs'), $activity->verb);
    		$article	= item_type($this->ci->lang->line('object_articles'), $activity->type);
    		$type		= item_type($this->ci->lang->line('object_types'), $activity->type);
    		
    		// Has Title
    		if (($has_title) && ($data->title))
    		{	    		
	    		if ($has_url)	$title_link = $type.' <a href="'.$data->url.'">'.$data->title.'</a>';
	    		else			$title_link = $data->title; 	
    		}
    		else
    		{
	    		if ($has_url)	$title_link = ' <a href="'.$data->url.'">'.$type.'</a>';
	    		else			$title_link = $type;
    		}
    		
    		return '<span class="item_verb">'.$verb.' '.$article.' '.$title_link.'</span>';
    	}    	
    }

    // Generate Content
    function render_item_content($type, $object)
    {
        $has_thumb	= property_exists($object, 'thumb');
    
		$render_function = 'render_item_'.$type;
		$callable_method = array($this, $render_function);
		   
		// Custom Render Exists    		    		
		if (is_callable($callable_method, false, $callable_function))
		{
			$content = $this->$render_function($object, $has_thumb);
		}
		else
		{
			$content = $this->render_item_default($object, $has_thumb);
		}
    	
    	return '<span class="item_content">'.$content.'</span>';
    }
    
    /* Item Types */
    function render_item_default($object, $has_thumb)
    {
	    // Has Thumbnail
		if ($has_thumb) 
		{
			$content = '<a href="'.$object->url.'"><img src="'.$object->thumb.'" border="0"></a>'.$object->content;
		}
		else
		{
			$content = '<span class="item_content_detail">"'.$object->content.'" <a href="'.$object->url.'">read</a></span>';
		}
	    
    	return $content;
    }
    
    function render_item_page($object, $has_thumb)
    {
    	return '<span class="item_content_detail">"'.$object->content.'" <a href="'.$object->url.'">read</a>"</span>';
    }
    
    function render_item_image($object, $has_thumb)
    {    
    	return '<span class="item_content_detail"><a href="'.$object->url.'"><img src="'.$object->thumb.'" border="0"></a></span>';
    }
    
    function render_item_event($object, $has_thumb)
    {
    	$thumb	 = NULL;
    	$title	 = NULL;
    	$details = NULL;
			
	    // Has Thumbnail
		if ($has_thumb) 
		{
			$thumb = '<a href="'.$object->url.'"><img class="item_content_thumb" src="'.$object->thumb.'" border="0"></a>';
		}
		
		$title = '<span class="item_content_detail_sm"><a href="'.$object->url.'">'.$object->title.'</a></span>';
		
		// Location
		if (property_exists($object, 'location'))
		{
			$details = '<span class="item_content_detail_sm">Location: <span class="color_black">'.events_location($object->location, array('name','locality','region')).'</span></span>';
		}

		// Date
		if (property_exists($object, 'date_time'))
		{
			$details .= '<span class="item_content_detail_sm">Time: <span class="color_black">'.format_datetime('SIMPLE_TIME', $object->date_time).'</span></span>';
		}

		// Description
		if (property_exists($object, 'description'))
		{		
			$details .= $object->content;
    	}
    	    
    	return $thumb.$title.$details;    
    }
   
		
		
	/* Social */
	function get_social_logins($html_start, $html_end)
	{
		$social_logins 		= NULL;
		$available_logins	= config_item('social_logins');
				
		foreach ($available_logins as $login)
		{
			if (config_item($login.'_enabled') == 'TRUE')
			{
				$data['assets']	   = base_url().'application/modules/'.$login.'/assets/';
				$social_logins 	  .= $html_start.$this->ci->load->view('../modules/'.$login.'/views/partials/social_login.php', $data, true).$html_end;
			}
		}
		return $social_logins;
	}

	function get_social_post($user_id)
	{
		$post_to 			= NULL;
		$social_post		= config_item('social_post');
		
		if ($user_connections = $this->ci->session->userdata('user_connections'))
		{
			foreach ($social_post as $social)
			{
				foreach($user_connections as $exists)
				{
					if ($exists->module == $social)
					{
						$post_to .= '<li><input type="checkbox" value="1" id="post_'.$social.'" checked="checked" name="post_'.$social.'" /> '.ucwords($social).'</li>';
					}
				}		
			}
		}
		
		if ($post_to)
		{
			return '<ul id="social_post">'.$post_to.'</ul>';
		}
			
		return NULL;
	}

	// NEEDS TO BE FIXED
	function get_social_checkin($user_id)
	{
		$checkin 			= NULL;
		$social_checkin		= config_item('social_checkin');
		$user_connections	= $this->ci->session->userdata('user_connections');

		foreach ($social_checkin as $social)
		{
			foreach($user_connections as $exists)
			{
				if ($exists->module == $social)
				{
					$checkin .= '<li><input type="checkbox" value="1" id="post_'.$social.'" checked="checked" name="post_'.$social.'" /> '.ucwords($social).'</li>';
				}
			}		
		}
		
		if ($checkin)
		{
			return '<ul id="social_post"><li id="social_post_share">Check In:</li>'.$checkin.'</ul>';
		}
			
		return NULL;
	}
	
	
	/* File & Directory Scanning */
	function scan_themes()
	{
		return $themes_scan = directory_map('./application/views/', TRUE);		
	}

	function scan_modules()
	{
		return $modules_scan = directory_map('./application/modules/', TRUE);
	}

	function scan_layouts($theme)
	{		
		$layouts_scan	= directory_map('./application/views/'.$theme.'/layouts/', TRUE);
		$layouts		= array();
		
		foreach ($layouts_scan as $layout)
		{
			$layout = str_replace('.php', '', $layout);
		
			if ($layout != 'profile')
			{
				$layouts[] = $layout;
			}
		}
	
		return $layouts;
	}
	
	function scan_media_manager()
	{
		$modules 		= $this->scan_modules();
		$media_manager	= NULL;
		
		foreach ($modules as $module)
		{
			$manager_path = '/modules/'.$module.'/views/partials/media_manager.php';
		
		    if (file_exists(APPPATH.$manager_path))
		    {
		    	$media_manager .= $this->ci->load->view('..'.$manager_path, '', true);
		    }
		}
	
		return $media_manager;
	}
	
	
	/* Site */
	function get_site()
	{
		return $this->ci->sites_model->get_site();
	}
	
	function get_themes($theme_type='site')
	{
		$theme_array		= array();
		$themes 			= $this->scan_themes();
	
		foreach ($themes as $theme)
		{
			if (strstr($theme, $theme_type))
			{
				$theme_array[] = $theme;
			}			
		}
	
		return $theme_array;
	}
	
	
	/* Pages */
	function get_index_page()
	{
		return $this->ci->pages_model->get_index_page(config_item('site_id'));
	}
	
	function get_page($title_url)
	{
		return $this->ci->pages_model->get_page(config_item('site_id'), $title_url);
	}	

	function get_page_id($page_id)
	{
		return $this->ci->pages_model->get_page_id($page_id);
	}

	function get_pages()
	{
		return $this->ci->pages_model->get_pages(config_item('site_id'));
	}
	
	function get_menu()
	{
		return $this->ci->pages_model->get_menu(config_item('site_id'));	
	}
	
	
	/* Settings */	
	function get_settings($module=NULL)
	{
		return $this->ci->settings_model->get_settings(config_item('site_id'), $module);
	}

	function get_settings_type($setting)
	{
		return $this->ci->settings_model->get_settings_type($setting);
	}	

	function get_settings_type_value($setting, $value)
	{
		return $this->ci->settings_model->get_settings_type_value($setting, $value);
	}	
	
	function get_setting_module_type($module, $setting)
	{
		return $this->ci->settings_model->get_setting_module_type($module, $setting);
	}	

	function update_settings($module, $settings_update_array)
	{
		// Get settings for module
		$settings_current = $this->get_settings($module);
	
		// Loop through all settings posted
		foreach ($settings_update_array as $setting_update)
		{
			// Form element name
			$name = key($settings_update_array);

			// Loops through all current settings
			foreach ($settings_current as $setting_current)
			{
				// If matches update it
				if ($setting_current->setting == $name)
				{					
					log_message('debug', 'settings_test '.$name.': '.$setting_update);
				
					$update_data = array('setting' => $name, 'value' => $setting_update);
				
					$this->ci->settings_model->update_setting($setting_current->settings_id, $update_data);
					break;
				}
			}
		
			next($settings_update_array);
		}
		
		return;
	}
	
	/* Activity */
	function get_timeline($limit, $module)
	{
		if ($module)	$where = array('module' => $module);
		else			$where = array();
	
		return $this->ci->activity_model->get_timeline($limit, $where);		
	}
	
	function get_activity($activity_id)
	{
		return $this->ci->activity_model->get_activity($activity_id);
	}
	
	function add_activity($activity_info, $activity_data)
	{
		if ($activity_id = $this->ci->activity_model->add_activity($activity_info, $activity_data))
		{
			return $this->ci->activity_model->get_activity($activity_id);
		}
		
		return FALSE;
	}
	
	function delete_activity($activity_id)
	{
	 	$activity = $this->get_activity($activity_id);

 		if (is_object($activity))
 		{ 		
 			if ($activity->user_id != $this->ci->session->userdata('user_id'))
 			{
 				return FALSE;
 			}
 		
 			$this->ci->activity_model->delete_activity($activity->activity_id);
 		
 			if ($activity->type == 'status')
 			{
 				$content = json_decode($activity->data);
 				
 				$this->delete_content($content->content_id);
 			}
 		
 			return TRUE;
 		}

		return FALSE;
	}
	

	/* Content */
	function check_content_comments($content_id)
	{
		$content = $this->ci->content_model->get_content($content_id);
		
		if ($content->comments_allow == 'N')
		{
			return FALSE;
		}
		elseif (($content->comments_allow == 'A') || ($content->comments_allow == 'Y'))
		{
			return $content;
		}
		
		return FALSE;
	}
	
	function check_content_duplicate($user_id, $title, $content)
	{
		if ($this->ci->content_model->check_content_duplicate($user_id, $title, $content))
		{
			return FALSE;
		}
		
		return TRUE;
	}
	
	function get_content($content_id)
	{
		return $this->ci->content_model->get_content($content_id);
	}
	
	function get_content_recent($type, $limit=10)
	{
		$site_id = config_item('site_id');
		
		return $this->ci->content_model->get_content_recent($site_id, $type, $limit);
	}

	function get_content_module($module, $limit=10)
	{
		$site_id = config_item('site_id');
		
		return $this->ci->content_model->get_content_module($site_id, $module, $limit);
	}
	
	function get_content_view($parameter, $value)
	{
		return $this->ci->content_model->get_content_view($parameter, $value);	
	}
	
	function get_content_view_recent($parameter, $value)
	{
		return $this->ci->content_model->get_content_view_recent($parameter, $value);	
	}
	
    function get_content_category_count($category_id)
	{
		return $this->ci->content_model->get_content_category_count($category_id);
	}
	
	// Adds Content & Activity
	function add_content($content_data, $activity_data=FALSE)
	{
		$content_id = $this->ci->content_model->add_content($content_data);

		if ($content_id)
		{
			if ($content_data['category_id']) $this->ci->social_tools->update_category_contents_count($content_data['category_id']);
		
			$activity_info = array(
				'site_id'		=> $content_data['site_id'],
				'user_id'		=> $content_data['user_id'],
				'verb'			=> 'post',
				'module'		=> $content_data['module'],
				'type'			=> $content_data['type'],
				'content_id'	=> $content_id
			);

			if (!$activity_data)
			{			
				$activity_data = array(
					'title'		=> $content_data['title'],
					'content' 	=> character_limiter(strip_tags($content_data['content'], ''), config_item('home_description_length'))			
				);
			}
				
			// Permalink
			$activity_data['url'] = base_url().$content_data['module'].'/view/'.$content_id;

			// Add Activity
			$this->add_activity($activity_info, $activity_data);			

			return $this->get_content($content_id);
		}

		return FALSE;
	}

	function update_content($content_data, $user_id, $activity_data=FALSE)
	{		
		$update = $this->ci->content_model->update_content($content_data);

		if ($update)
		{
			$activity_info = array(
				'site_id'		=> $update->site_id,
				'user_id'		=> $update->user_id,
				'verb'			=> 'update',
				'module'		=> $update->module,
				'type'			=> $update->type,
				'content_id'	=> $update->content_id
			);

			if (!$activity_data)
			{			
				$activity_data = array(
					'title'		=> $content_data['title'],
					'content' 	=> character_limiter(strip_tags($content_data['content'], ''), config_item('home_description_length'))			
				);
			}
				
			// Permalink
			$activity_data['url'] = base_url().$content_data['module'].'/view/'.$update->content_id;

			// Add Activity
			$this->add_activity($activity_info, $activity_data);		

			return $update;
		}

		return FALSE;
	}

	function update_content_comments_count($content_id)
	{
		$comments_count = $this->ci->social_tools->get_comments_content_count($content_id);
	
		return $this->ci->content_model->update_content_comments_count($content_id, $comments_count);
	}
	
	function delete_content($content_id)
	{
		return $this->ci->content_model->delete_content($content_id);
	}	
	
	/* Content Meta */
	// Feed this function a content specific query of meta_content data and return specified
	function find_meta_content_value($key, $meta_query)
	{
		foreach($meta_query as $meta)
		{
			if ($meta->key == $key)
			{
				return $meta->value;
			}
		}
		
		return FALSE;
	}
	
	function get_meta($content_meta_id)
	{
		return $this->ci->content_model->get_meta($content_meta_id);
	}
	
	function get_meta_content($content_id)
	{
		return $this->ci->content_model->get_meta_content($content_id);
	}
	
}