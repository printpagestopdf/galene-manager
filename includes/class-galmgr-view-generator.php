<?php
class Galmgr_View_Generator {

	public static $blocks = array();
	public static $cache_path = GALMGR_PLUGIN_PATH . 'views/cache/';
	public static $cache_enabled = true;
	public static $template_path ="";

	public static function view($file, $data = array(), $template_path="") {
		self::$template_path=$template_path;
		$cached_file = self::cache($file);
	    extract($data, EXTR_SKIP);
	   	require $cached_file;
	}

	public static function cache($file) {
		if (!file_exists(self::$cache_path)) {
		  	mkdir(self::$cache_path, 0744);
		}
		
		$templateFile=self::mk_path(self::$template_path,$file);
	    $cached_file = self::$cache_path . str_replace(array('/', '.html', '.twig'), array('_', '', ''), $file . '.php');

	    if (!self::$cache_enabled || !file_exists($cached_file) || filemtime($cached_file) < filemtime($templateFile)) {
			$code = self::include_files($file);
			$code = self::compile_code($code);
	        file_put_contents($cached_file, '<?php class_exists(\'' . __CLASS__ . '\') or exit; ?>' . PHP_EOL . $code);
	    }
		return $cached_file;
	}

	public static function clear_cache() {
		foreach(glob(self::$cache_path . '*') as $file) {
			unlink($file);
		}
	}

	public static function compile_code($code) {
		$code = self::compile_block($code);
		$code = self::compile_yield($code);
		$code = self::compile_escaped_echos($code);
		$code = self::compile_echos($code);
		$code = self::compile_php($code);
		return $code;
	}
	
	public static function mk_path($p1,$p2)
	{
		if(empty($p1))
			return $p2;
		else
			return join('/', array(rtrim($p1, '/'), trim($p2, '/')));
	}
	
	public static function include_files($file) {
		$code = file_get_contents(self::mk_path(self::$template_path,$file));
		preg_match_all('/{% ?(extends|include) ?\'?(.*?)\'? ?%}/i', $code, $matches, PREG_SET_ORDER);
		foreach ($matches as $value) {
			$code = str_replace($value[0], self::include_files($value[2]), $code);
		}
		$code = preg_replace('/{% ?(extends|include) ?\'?(.*?)\'? ?%}/i', '', $code);
		return $code;
	}

	public static function compile_php($code) {
		return preg_replace('~\{%\s*(.+?)\s*\%}~is', '<?php $1 ?>', $code);
	}

	public static function compile_echos($code) {
		return preg_replace('~\{{\s*(.+?)\s*\}}~is', '<?php echo esc_html($1) ?>', $code);
	}

	public static function compile_escaped_echos($code) {
		return preg_replace('~\{{{\s*(.+?)\s*\}}}~is', '<?php echo esc_attr($1) ?>', $code);
	}

	public static function compile_block($code) {
		preg_match_all('/{% ?block ?(.*?) ?%}(.*?){% ?endblock ?%}/is', $code, $matches, PREG_SET_ORDER);
		foreach ($matches as $value) {
			if (!array_key_exists($value[1], self::$blocks)) self::$blocks[$value[1]] = '';
			if (strpos($value[2], '@parent') === false) {
				self::$blocks[$value[1]] = $value[2];
			} else {
				self::$blocks[$value[1]] = str_replace('@parent', self::$blocks[$value[1]], $value[2]);
			}
			$code = str_replace($value[0], '', $code);
		}
		return $code;
	}

	public static function compile_yield($code) {
		foreach(self::$blocks as $block => $value) {
			$code = preg_replace('/{% ?yield ?' . $block . ' ?%}/', $value, $code);
		}
		$code = preg_replace('/{% ?yield ?(.*?) ?%}/i', '', $code);
		return $code;
	}

}
?>