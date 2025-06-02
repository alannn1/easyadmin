<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;
use App\Helpers\Validation;
// use Illuminate\Support\Facades\View;
use App\Helpers\Constant;
// use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    private $title;
    private $generalUri;
    private $arrPermissions;
    private $tableHeaders;
    private $actionButtons;

    public function __construct()
    {
        $this->title = 'Product';
        $this->generalUri = 'product';
        $this->arrPermissions = [];
        $this->actionButtons = ['btn_edit', 'btn_show', 'btn_destroy'];

        $this->tableHeaders = [
            ['name' => 'No', 'column' => '#', 'order' => true],
            ['name' => 'Code', 'column' => 'code', 'order' => true],
            ['name' => 'Name', 'column' => 'name', 'order' => true],
            ['name' => 'Description', 'column' => 'description', 'order' => true],
            ['name' => 'Price', 'column' => 'price', 'order' => true],
            ['name' => 'Created at', 'column' => 'created_at', 'order' => true],
            ['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];
    }

    public function index()
    {
        $moreActions = [
            [
                'key' => 'import-excel-default',
                'name' => 'Import Excel',
                'html_button' => "<button id='import-excel' type='button' class='btn btn-sm btn-info radius-6' href='#' data-bs-toggle='modal' data-bs-target='#modalImportDefault' title='Import Excel' ><i class='ti ti-upload'></i></button>"
            ],
            [
                'key' => 'export-excel-default',
                'name' => 'Export Excel',
                'html_button' => "<a id='export-excel' class='btn btn-sm btn-success radius-6' target='_blank' href='" . url($this->generalUri . '-export-excel-default') . "'  title='Export Excel'><i class='ti ti-cloud-download'></i></a>"
            ],
            [
                'key' => 'export-pdf-default',
                'name' => 'Export Pdf',
                'html_button' => "<a id='export-pdf' class='btn btn-sm btn-danger radius-6' target='_blank' href='" . url($this->generalUri . '-export-pdf-default') . "' title='Export PDF'><i class='ti ti-file'></i></a>"
            ],
        ];

        $permissions = (new Constant())->permissionByMenu($this->generalUri);
        $data['permissions'] = $permissions;
        $data['more_actions'] = $moreActions;
        $data['table_headers'] = $this->tableHeaders;
        $data['title'] = $this->title;
        $data['uri_key'] = $this->generalUri;
        $data['uri_list_api'] = route($this->generalUri . '.listapi');
        $data['uri_create'] = route($this->generalUri . '.create');
        $data['url_store'] = route($this->generalUri . '.store');
        $data['fields'] = $this->fields();
        $data['edit_fields'] = $this->fields();
        $data['actionButtonViews'] = [
            'backend.idev.buttons.delete', 
            'backend.idev.buttons.edit', 
            'backend.idev.buttons.show', 
            'backend.idev.buttons.import_default',
        ];
        $data['templateImportExcel'] = "#";

        $layout = (request('from_ajax') && request('from_ajax') == true) ? 'backend.idev.list_drawer_ajax' : 'backend.idev.list_drawer';

        return view($layout, $data);
    }


    public function indexApi()
    {
        $permission = (new Constant)->permissionByMenu($this->generalUri);
        $eb = [];
        $data_columns = [];

        foreach ($this->tableHeaders as $key => $col) {
            if ($key > 0) {
                $data_columns[] = $col['column'];
            }
        }
        foreach ($this->actionButtons as $key => $ab) {
            if (in_array(str_replace("btn_", "", $ab), $permission)) {
                $eb[] = $ab;
            }
        }
        $dataQueries = $this->defaultDataQuery()->paginate(10);
        $dataQueries->getCollection()->transform(function ($item) {
            $item->price = 'Rp ' . number_format($item->price, 0, ',', '.');
            return $item;
        });
        $datas['extra_buttons'] = $eb;
        $datas['data_columns'] = $data_columns;
        $datas['data_queries'] = $dataQueries;
        $datas['data_permissions'] = $permission;
        $datas['uri_key'] = $this->generalUri;

        return $datas;
    }
    private function defaultDataQuery()
    {
        $filters = [];
        $orThose = null;
        $orderBy = 'product_harumo.id';
        $orderState = 'ASC';

        if (request('search')) {
            $orThose = request('search');
        }
        if (request('order')) {
            $orderBy = request('order');
            $orderState = request('order_state');
        }
        if (request('code')) {
            $filters[] = ['product_harumo.code', 'LIKE', '%' . request('code') . '%'];
        }

        $dataQueries = Product::where($filters)
            ->where(function ($query) use ($orThose) {
                $query->where('product_harumo.name', 'LIKE', '%' . $orThose . '%')
                    ->orWhere('product_harumo.description', 'LIKE', '%' . $orThose . '%')
                    ->orWhere('product_harumo.price', 'LIKE', '%' . $orThose . '%');
            })
            ->select('product_harumo.id', 'product_harumo.code', 'product_harumo.name', 'product_harumo.description', 'product_harumo.price', 'product_harumo.created_at', 'product_harumo.updated_at')
            ->orderBy($orderBy, $orderState);

        return $dataQueries;
    }
    private function fields($mode = "create", $id = '-')
    {
        $edit = null;
        if ($id != '-') {
            $edit = Product::where('id', $id)->first();
        }

        $fields = [
            [
                'type' => 'text',
                'label' => 'Code',
                'name' => 'code',
                'class' => 'col-md-12 my-2',
                'value' => (isset($edit)) ? $edit->code : ''
            ],
            [
                'type' => 'text',
                'label' => 'Name',
                'name' => 'name',
                'class' => 'col-md-12 my-2',
                'value' => (isset($edit)) ? $edit->name : ''
            ],
            [
                'type' => 'text',
                'label' => 'Description',
                'name' => 'description',
                'class' => 'col-md-12 my-2',
                'value' => (isset($edit)) ? $edit->description : ''
            ],
            [
                'type' => 'number',
                'label' => 'Price',
                'name' => 'price',
                'class' => 'col-md-12 my-2',
                'value' => (isset($edit)) ? $edit->price : 0
            ],
        ];

        return $fields;
    }

    public function show($id)
    {
        $singleData = $this->defaultDataQuery()->where('id', $id)->first();
        unset($singleData['id']);
        
        $data['detail'] = $singleData;

        return view('backend.idev.show-default', $data);
    }
    public function store(Request $request)
    {
        // Log::info('Store request data:', $request->all()); 

        // dd($request->all()); 

        $rules = [
            'code' => 'required|unique:sqlsrv.product_harumo,code',
            'name' => 'required|string',
            'price' => 'required|integer',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messageErrors = (new Validation)->modify($validator, $rules);

            return response()->json([
                'status' => false,
                'alert' => 'danger',
                'message' => 'Required Form',
                'validation_errors' => $messageErrors,
            ], 200);
        }

        DB::beginTransaction();

        try {
            $productData = [
                'code' => $request->code,
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
            ];

            // Log::info('Data to be inserted:', $productData); 

            $insert = new Product();
            $insert->code = $productData['code'];
            $insert->name = $productData['name'];
            $insert->description = $productData['description'];
            $insert->price = $productData['price'];
            $insert->save();

            DB::commit();

            return response()->json([
                'status' => true,
                'alert' => 'success',
                'message' => 'Data Was Created Successfully',
                'data' => $insert,
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(), // tambahan trace
            ], 500);
        }
    }
    public function edit($id)
    {
        $data['fields'] = $this->fields('edit', $id);
        return $data;
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'code' => 'required|string|unique:sqlsrv.product_harumo,code,' . $id,
            'name' => 'required|string',
            'price' => 'required|integer',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messageErrors = (new Validation)->modify($validator, $rules, 'edit_');

            return response()->json([
                'status' => false,
                'alert' => 'danger',
                'message' => 'Required Form',
                'validation_errors' => $messageErrors,
            ], 200);
        }

        DB::beginTransaction();

        try {
            $product = Product::find($id);
            $product->code = $request->code;
            $product->name = $request->name;
            $product->description = $request->description;
            $product->price = $request->price;
            $product->save();

            DB::commit();

            return response()->json([
                'status' => true,
                'alert' => 'success',
                'message' => 'Data Was Updated Successfully',
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $product = Product::find($id);
            $product->delete();

            return response()->json([
                'status' => true,
                'alert' => 'success',
                'message' => 'Data Was Deleted Successfully',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
