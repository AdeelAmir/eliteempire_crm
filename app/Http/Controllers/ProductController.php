<?php

namespace App\Http\Controllers;
use App\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class ProductController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth');
	}

	public function AdminAllProducts()
	{
		$page = "products";
		$Role = Session::get('user_role');
		return view('admin.product.products', compact('page','Role'));
	}

	public function LoadAdminAllProducts(Request $request)
	{
		$Role = Session::get('user_role');
		$limit = $request->post('length');
		$start = $request->post('start');
		$searchTerm = $request->post('search')['value'];

        $columnIndex = $request->post('order')[0]['column']; // Column index
        $columnName = $request->post('columns')[$columnIndex]['data']; // Column name
        $columnSortOrder = $request->post('order')[0]['dir']; // asc or desc

        $fetch_data = null;
        $recordsTotal = null;
        $recordsFiltered = null;

        if ($searchTerm == '') {
        	$fetch_data = DB::table('products')
        	->where('products.deleted_at', '=', null)
        	->select('products.*')
        	->orderBy($columnName, $columnSortOrder)
        	->offset($start)
        	->limit($limit)
        	->get();

        	$recordsTotal = sizeof($fetch_data);
        	$recordsFiltered = DB::table('products')
        	->where('products.deleted_at', '=', null)
        	->select('products.*')
        	->orderBy($columnName, $columnSortOrder)
        	->count();
        } else {
        	$fetch_data = DB::table('products')
        	->where(function ($query) {
        		$query->where([
        			['products.deleted_at', '=', null]
        		]);
        	})
        	->where(function ($query) use ($searchTerm) {
        		$query->orWhere('products.id', 'LIKE', '%' . $searchTerm . '%');
        		$query->orWhere('products.name', 'LIKE', '%' . $searchTerm . '%');
        	})
        	->select('products.*')
        	->orderBy($columnName, $columnSortOrder)
        	->offset($start)
        	->limit($limit)
        	->get();
        	$recordsTotal = sizeof($fetch_data);
        	$recordsFiltered = DB::table('products')
        	->where(function ($query) {
        		$query->where([
        			['products.deleted_at', '=', null],
        		]);
        	})
        	->where(function ($query) use ($searchTerm) {
        		$query->orWhere('products.id', 'LIKE', '%' . $searchTerm . '%');
        		$query->orWhere('products.name', 'LIKE', '%' . $searchTerm . '%');
        	})
        	->select('products.*')
        	->orderBy($columnName, $columnSortOrder)
        	->count();
        }

        $data = array();
        $SrNo = $start + 1;
        $active_ban = "";
        foreach ($fetch_data as $row => $item) {
        	$sub_array = array();
        	$sub_array['id'] = $SrNo;
        	$sub_array['name'] = $item->name;
         if($Role == 1) {
          $sub_array['action'] = $active_ban . '<button class="btn btn-info mr-2" id="edit_' . $item->id . '" onclick="editProduct(this.id);"><i class="fas fa-edit"></i>';
      }
      else {
          $sub_array['action'] = $active_ban . '<button class="btn btn-info mr-2" id="edit_' . $item->id . '" onclick="editManagerUser(this.id);"><i class="fas fa-edit"></i></button>';
      }
      $SrNo++;
      $data[] = $sub_array;
  }

  $json_data = array(
     "draw" => intval($request->post('draw')),
     "iTotalRecords" => $recordsTotal,
     "iTotalDisplayRecords" => $recordsFiltered,
     "aaData" => $data
 );

  echo json_encode($json_data);
}
public function AdminAddNewProduct()
{
    $page = "products";
    $maxDate = Carbon::now()->subYears(15);
    $maxDate = $maxDate->toDateString();
    $Role = Session::get('user_role');


    return view('admin.product.add_new_product', compact('page',  'maxDate', 'Role'));
}

public function AdminProductStore(Request $request)
{
    $UserRole = Session::get('user_role');
    $ProductName = $request['name'];
    $product = Product::create([
        'name' => $ProductName,
    ]);
    return redirect(url('/admin/products'))->with('message', 'Product has been added successfully');

}
public function AdminEditProduct(Request $request)
{
    $Role = Session::get('user_role');
    $page = "products";
    $product_id = $request['id'];
    $product_details = DB::table('products')
    ->where('products.id', $product_id)
    ->where('products.deleted_at', '=', null)
    ->select('products.id AS id', 'products.name AS name')
    ->get();

    $role_details = DB::table('roles')
    ->where('deleted_at', '=', null)
    ->get();

    $maxDate = Carbon::now()->subYears(15);
    $maxDate = $maxDate->toDateString();

    return view('admin.product.edit-product', compact('page', 'product_id', 'product_details', 'role_details', 'maxDate', 'Role'));
}

public function AdminUpdateProduct(Request $request)
{
    $UserRole = Session::get('user_role');
    $product_id = $request['id'];
    $ProductName = $request['name'];
    $affected = DB::table('products')
    ->where('id', $product_id)
    ->update([
        'name' => $ProductName,
        'updated_at' => Carbon::now(),
    ]);
    return redirect(url('/admin/products'))->with('message', 'Product record has been updated successfully');
}


}
