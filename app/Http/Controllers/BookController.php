<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Book;
use Illuminate\Http\Request;
use League\Csv\Writer;
use Illuminate\Support\Facades\Response;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $sortOrder = $request->input('sort', 'asc');
        $searchTerm = $request->input('search', '');
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'asc';
        }
        $books = Book::where('name', 'like', "%{$searchTerm}%")
            ->orWhere('author', 'like', "%{$searchTerm}%")
            ->orderBy('name', $sortOrder)->get();
        return response()->json($books);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $newBook = $request->validate([
            'name' => 'required|max:255|unique:books',
            'author' => 'required|max:255',
            'cover' => 'required|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('cover')) {
            $imagePath = $request->file('cover')->store('covers', 'public');
            $newBook['cover'] = $imagePath;
        }

        $book = Book::create($newBook);

        return response()->json(['message' => 'Book details stored successfully', $book], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        //
        return $book;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Book $book)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $book)
    {

        $newBook = $request->validate([
            'name' => 'required|max:255|unique:books,name,' . $book->id,
            'author' => 'required|max:255',
            'cover' => 'nullable|mimes:jpeg,png,jpg,gif|max:2048'
        ]);


        if ($request->hasFile('cover')) {

            $imagePath = $request->file('cover')->store('covers', 'public');
            $newBook['cover'] = $imagePath;
        }


        $book->update($newBook);
        $updatedBook = Book::find($book->id);
        return response()->json(['message' => 'Book updated successfully', 'book' => $updatedBook], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        //
        $book->delete();

        return response()->json(['message' => 'Book deleted successfully']);
    }

    public function exportCsv()
    {
        $books = Book::all();
        $filename = "books.csv";

        $handle = fopen('php://output', 'w');
        fputcsv($handle, ['ID', 'Name', 'Author', 'Cover']);


        foreach ($books as $book) {
            fputcsv($handle, [
                $book->id,
                $book->name,
                $book->author,
                $book->cover ? asset('storage/' . $book->cover) : 'No Cover'
            ]);
        }

        fclose($handle);

       return Response::streamDownload(function() use ($handle) {

       },$filename, [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
       ]);
    }
}
