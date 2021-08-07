<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VersionSettingModel extends Model {
	
	protected $table = 'version_setting';

	static public function get_single($id)
	{
		return self::find($id);
	}
	
}

?>