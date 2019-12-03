<?php 

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Companies;
use Auth;
use App\Notifications;
use DB;

class NotificationController extends Controller {
        
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Request $request)
	{
        $columns = ["NOTIFICATION","URL"];
		if ($request->ajax()){
            $data = Notifications::select("NOTIFICATION_ID", "NOTIFICATION","URL", "created_at")
								   ->where("USER_ID", Auth::user()->id);
			$totalData = $data->count();
    		$totalFiltered = $totalData;
			if (!empty($request->search["value"])){
				foreach($columns as $key=>$col){
					if ($key == 0){
						$data->where($col, "LIKE", '%' .$request->search['value'] .'%');
					}
					else {
						$data->orWhere($col, "LIKE", '%' .$request->search['value'] .'%');
					}					
				}
				$totalFiltered = $data->count();
			}
			$data->orderBy("created_at", "DESC")
				 ->orderBy($columns[$request->order[0]['column']], $request->order[0]['dir'])
				 ->skip($request->start)
				 ->take($request->length);
			return ["draw" => intval($request->draw), "recordsTotal" => $totalData,
					"recordsFiltered" => $totalFiltered,
					"data" => $data->get() 
				   ];
		}
		else {
			return view ("notifications.index");
		}
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Request $request)
	{
		
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		
	}	

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy(Request $request)
	{		
		try {
			$data = Notifications::where("NOTIFICATION_ID", $request->id)->delete();
			return view("partials.flash", ["type" => "info", "text" => "Data berhasil dihapus"]);
		}
		catch (Exception $e){
			$response = view("partials.flash", ["type" => "error", "text" => "Penghapusan data gagal"]);
			return response($response, 500)
							->header('Content-Type', 'text/html');
		}
	}

}
