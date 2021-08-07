<?php
namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\VersionSettingModel;

	class VersionSettingController extends Controller
	{
		public function version_setting_index(Request $request){
			$data['getrecord'] = VersionSettingModel::find(1);

			$data['meta_title'] = 'Version Setting Update';
			return view('backend.version_setting.list', $data);
		}

		public function version_setting_insert(Request $request){
			// dd($request->all());
			$upversion = VersionSettingModel::find(1);
			$upversion->app_version = $request->app_version;
			$upversion->save();
			return redirect()->back()->with('success', 'Record successfully updated.');
		}

	}
?>