<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Book;
use App\Http\Resources\BookResource;
use Validator;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $books = Book::with('category')->paginate();
        $bookResource = BookResource::collection($books);
        return $this->sendResponse($bookResource, "Successfully get books");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required|min:4',
            'description' => 'required|min:10|max:300',
            'price' => 'required|numeric',
            'image' => 'required|mimes:png,jpg,jpeg,webp|max:2048',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error', $validator->errors());
        }

        if ($request->file('image')) {
            $image = $request->file('image');
            $fileName = pathInfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = pathInfo($image->getClientOriginalName(), PATHINFO_EXTENSION);
            $fullFileName = $fileName.time().'.'.$extension;
            $path = $image->storeAs('images', $fullFileName, 'public');

            // field image = jika laravel pakai public, kalau next js gk perlu
            // $input['image'] = 'public/'.$path;
            $input['image'] = $path;
        }

        $book = Book::create($input);
        $findBook = Book::with('category')->find($book->id);

        return $this->sendResponse(new BookResource($findBook), 'Book created Succesfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $book = Book::with('category')->find($id);
        return $this->sendResponse(new BookResource($book), 'Book get Succesfully');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required|min:4',
            'description' => 'required|min:10|max:300',
            'price' => 'required|numeric',
            'image' => 'required|mimes:png,jpg,jpeg,webp|max:2048',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error', $validator->errors());
        }

        $book = Book::with('category')->find($id);
        if($request->image != null){
            $newImage = $request->file('image');
            $fileName = pathInfo($newImage->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = pathInfo($newImage->getClientOriginalName(), PATHINFO_EXTENSION);
            $fullFileName = $fileName.time().'.'.$extension;
            $path = $newImage->storeAs('images', $fullFileName, 'public');

            // field image = jika laravel pakai public, kalau next js gk perlu
            // $input['image'] = 'public/'.$path;
            $input['image'] = $path;

            if($book->image != null){
                Storage::delete($book->image);
            }
        }

        $book->update($input);
        return $this->sendResponse(new BookResource($book), 'Book updated Succesfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $book = Book::find($id);
        $book->delete();
        return $this->sendResponse([], 'Book deleted Succesfully');
    }
}
