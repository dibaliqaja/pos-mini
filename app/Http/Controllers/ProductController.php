<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

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

            return collectionResponse(
                'List Product query get success',
                $items,
                $page,
                $per_page,
                $data->total(),
                ceil($data->total() / $per_page)
            );
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 500);
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
            $product = Product::find($id);
            if(!$product) return errorResponse('No product found', 404);

            return successResponse('Product query get success',new ProductResource($product));
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Requests\ProductRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        try {
            $data = $request->validated();
            $file = $request->photo;

            $input['photo'] = 'product-'.time().'.'.$file->getClientOriginalExtension();
            $destinationPath = public_path('storage/images');
            File::exists($destinationPath) or File::makeDirectory($destinationPath);
            $file->move($destinationPath, $input['photo']);
            $data['photo']  = $input['photo'];
            
            $product = Product::create($data);

            return successResponse('Product successfully created',new ProductResource($product));
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Requests\ProductRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $request, $id)
    {
        try {
            $product = Product::find($id);
            if(!$product) return errorResponse('No product found', 404);
            
            $data = $request->validated();            
            $filePath = public_path('storage/image/'.$product->photo);
            if(File::exists($filePath)) File::delete($filePath);
            $file = $request->photo;

            $input['photo'] = 'product-'.time().'.'.$file->getClientOriginalExtension();
            $destinationPath = public_path('storage/images');
            File::exists($destinationPath) or File::makeDirectory($destinationPath);
            $file->move($destinationPath, $input['photo']);
            $data['photo']  = $input['photo'];

            $product->update($data);

            return successResponse('Product successfully updated',new ProductResource($product));
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 500);
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
            $product = Product::find($id);        
            if(!$product) return errorResponse('No product found', 404);
            $product->delete();

            return successResponse('Product successfully deleted',[]);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 500);
        }
    }
}
