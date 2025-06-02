<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;
use Illuminate\Support\Facades\Validator;
use App\Helpers\Validation;
use App\Helpers\Constant;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    private $title;
    private $generalUri;
    private $arrPermissions;
    private $tableHeaders;
    private $actionButtons;

    public function __construct()
    {
        $this->title = 'Transaction';
        $this->generalUri = 'transaction';
        $this->arrPermissions = [];
        $this->actionButtons = ['btn_edit', 'btn_show', 'btn_destroy'];

        $this->tableHeaders = [
            ['name' => 'No', 'column' => '#', 'order' => true],
            ['name' => 'Transaction Code', 'column' => 'transaction_code', 'order' => true],
            ['name' => 'Product Code', 'column' => 'product_code', 'order' => true],
            ['name' => 'Product Name', 'column' => 'product_name', 'order' => true],
            ['name' => 'Unit Price', 'column' => 'unit_price', 'order' => true],
            ['name' => 'Quantity', 'column' => 'quantity', 'order' => true],
            ['name' => 'Total Price', 'column' => 'total_price', 'order' => true],
            ['name' => 'Created At', 'column' => 'created_at', 'order' => true],
            ['name' => 'Updated At', 'column' => 'updated_at', 'order' => true],
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
            $item->unit_price = 'Rp ' . number_format($item->unit_price, 0, ',', '.');
            $item->total_price = 'Rp ' . number_format($item->total_price, 0, ',', '.');
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
    $orderBy = 'transaction_harumo.id';
    $orderState = 'ASC';

    if (request('search')) {
        $orThose = request('search');
    }

    if (request('order')) {
        $orderBy = request('order');
        $orderState = request('order_state');
    }

    if (request('code')) {
        $filters[] = ['transaction_harumo.transaction_code', 'LIKE', '%' . request('transaction_code') . '%'];
    }

    $dataQueries = Transaction::where($filters)
        ->where(function ($query) use ($orThose) {
            if ($orThose) {
                $query->where('transaction_harumo.product_code', 'LIKE', '%' . $orThose . '%')
                      ->orWhere('transaction_harumo.product_name', 'LIKE', '%' . $orThose . '%')
                      ->orWhere('transaction_harumo.unit_price', 'LIKE', '%' . $orThose . '%')
                      ->orWhere('transaction_harumo.quantity', 'LIKE', '%' . $orThose . '%')
                      ->orWhere('transaction_harumo.total_price', 'LIKE', '%' . $orThose . '%');
            }
        })
        ->select(
            'transaction_harumo.id',
            'transaction_harumo.transaction_code',
            'transaction_harumo.product_code',
            'transaction_harumo.product_name',
            'transaction_harumo.unit_price',
            'transaction_harumo.quantity',
            'transaction_harumo.total_price',
            'transaction_harumo.created_at',
            'transaction_harumo.updated_at'
        )
        ->orderBy($orderBy, $orderState);

    return $dataQueries;
}

// Buat function generate kode unik
    private function generateTransactionCode()
    {
        $prefix = 'TD';
        $date = now()->format('Ymd');

        $lastTransaction = Transaction::whereDate('created_at', now()->toDateString())
            ->orderBy('created_at', 'desc')
            ->first();

        $lastNumber = 0;
        if ($lastTransaction) {
            $lastCode = substr($lastTransaction->transaction_code, -4);
            $lastNumber = (int)$lastCode;
        }

        $newNumber = $lastNumber + 1;

        return $prefix . $date . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    private function fields($mode = "create", $id = '-')
    {
        $edit = null;
        if ($id != '-') {
            $edit = Transaction::where('id', $id)->first();
        }

        $products = Product::get();
        $arrProduct = [];
        foreach ($products as $key => $product) {
            $arrProduct[] = ['value' => $product->id, 'text' => $product->name];
        }

        $field = [
            [
                'type' => 'select',
                'label' => 'Product Name',
                'name' => 'product_name',
                'class' => 'col-md-12 my-2',
                'value' => (isset($edit)) ? $edit->product_name : '',
                'options' => $arrProduct,
            ],
            [
                'type' => 'number',
                'label' => 'Quantity',
                'name' => 'quantity',
                'class' => 'col-md-12 my-2',
                'value' => (isset($edit)) ? $edit->quantity : 0
            ],
        ];
        return $field;
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

        DB::beginTransaction();

        try {
            $product = Product::where('id', $request->product_name)->first();
            if (!$product) {
                throw new Exception('Product not found');
            }

            
            $insert = new Transaction();
            $insert->transaction_code = $this->generateTransactionCode();
            $insert->product_code = $product->code;
            $insert->product_name = $product->name; 
            $insert->unit_price = $product->price;
            $insert->quantity = $request->quantity;
            $insert->total_price = $product->price * $request->quantity;

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
        DB::beginTransaction();

        try {
            $update = Transaction::find($id);
            if (!$update) {
                return response()->json([
                    'status' => false,
                    'message' => 'Transaction not found.',
                ], 404);
            }

            $product = Product::where('id', $request->product_name)->first();
            if (!$product) {
                throw new Exception('Transaction not found');
            }

            $update->product_code = $product->code;
            $update->product_name = $product->name;
            $update->unit_price = $product->price;
            $update->quantity = $request->quantity;
            $update->total_price = $product->price * $request->quantity;

            $update->save();

            DB::commit();

            return response()->json([
                'status' => true,
                'alert' => 'success',
                'message' => 'Data Was Updated Successfully',
                'data' => $update,
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
        DB::beginTransaction();

        try {
            $delete = Transaction::where('id', $id)->first();
            if (!$delete) {
                return response()->json([
                    'status' => false,
                    'message' => 'Transaction not found.',
                ], 404);
            }

            $delete->delete();

            DB::commit();

            return response()->json([
                'status' => true,
                'alert' => 'success',
                'message' => 'Data Was Deleted Successfully',
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

}
