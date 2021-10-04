<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Create a new ProductController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $per_page = $request->per_page ? $request->per_page : 10;
            $page     = $request->page ? $request->page : 1;
            $search   = $request->search;
            $products = Product::query();
            $data     = $search
                        ? $products->where('name', 'LIKE', "%$search%")
                            ->orWhere('sku', 'LIKE', "%$search%")
                            ->orderBy('created_at', 'DESC')
                            ->paginate($per_page)
                        : $products->orderBy('created_at', 'DESC')
                            ->paginate($per_page);

            $items = collect($data->items());
            $items->transform(function($item) {
                $item->photo = asset('storage/images/' . $item->photo);
                return $item;
            });

            $response = [
                'status'     => 'success',
                'message'    => 'List Product query get success',
                'data'       => $items,
                'page'       => $page,
                'per_page'   => $per_page,
                'total_data' => $data->total(),
                'total_page' => ceil($data->total() / $per_page)
            ];

            return response()->json($response, 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $product = Product::findOrFail($id);
        
            return response()->json([
                'status' => 'success',
                'message' => 'Product query get success',
                'data' => [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'photo' => asset('storage/images/' . $product->photo)
                ]
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'sku' => 'required|string|unique:products',
                'photo' => 'required|image|mimes:jpeg,png,jpg',
            ]);

            if($validator->fails()){
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()
                ], 422);
            }

            $data = $validator->validated();
            $file = $request->photo;
            $input['photo'] = 'product-'.time().'.'.$file->getClientOriginalExtension();
            $destinationPath = public_path('storage/images');
            File::exists($destinationPath) or File::makeDirectory($destinationPath);
            $file->move($destinationPath, $input['photo']);
            $data['photo']  = $input['photo'];

            $product = Product::create($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Product successfully created',
                'data' => [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'photo' => asset('storage/images/' . $product->photo)
                ]
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'sku' => 'required|string|unique:products,sku,'.$id,
                'photo' => 'required|image|mimes:jpeg,png,jpg',
            ]);

            if($validator->fails()){
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()
                ], 422);
            }

            $data = $validator->validated();

            $product = Product::findOrFail($id);
            $filePath = public_path('storage/image/'.$product->photo);
            if(File::exists($filePath)) File::delete($filePath);

            $file = $request->photo;
            $input['photo'] = 'product-'.time().'.'.$file->getClientOriginalExtension();
            $destinationPath = public_path('storage/images');
            File::exists($destinationPath) or File::makeDirectory($destinationPath);
            $file->move($destinationPath, $input['photo']);
            $data['photo']  = $input['photo'];
            $product->update($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Product successfully updated',
                'data' => [
                    'product_id' => $id,
                    'name' => $data['name'],
                    'sku' => $data['sku'],
                    'photo' => asset('storage/images/' . $data['photo'])
                ]
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            Product::findOrFail($id)->delete();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Product deleted successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
