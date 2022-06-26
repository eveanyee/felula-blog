<?php

namespace App\Http\Controllers;

use App\Post;
use App\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'search', 'show']);
    }
    
    public function index(Request $request)
    {
        $posts = Post::with('category', 'user')
            ->withCount('comments')
            ->published()
            ->orderBy('id', 'desc')
            ->paginate(5);

        return view('home', compact('posts'));
    }

    public function search(Request $request)
    {
        $this->validate($request, ['query' => 'required']);

        $query = $request->get('query');

        $posts = Post::where('title', 'like', "%{$query}%")
            ->orWhere('body', 'like', "%{$query}%")
            ->with('category', 'user')
            ->withCount('comments')
            ->published()
            ->paginate(5);

        return view('post.search', compact('posts'));
    }

    public function show(Post $post)
    {
        $post = $post->load(['comments.user', 'user', 'category']);

        return view('post.show', compact('post'));
    }

    public function comment(Request $request, Post $post)
    {
        $this->validate($request, ['body' => 'required']);

        $post->comments()->create([
            'user_id'   => auth()->id(),
            'body'      => $request->body           
        ]);

        session()->flash('message', 'Comment successfully created.');

        return redirect("/posts/{$post->id}");
            
    }




    public function upload()
    {
        return view('user.posts.upload', ['categories'=> Category::all()]);
    }


    public function storeupload(Request $request)
    {
        
        if ($request->hasFile('csv_file')!= false OR $request->hasFile('csv_file')!= null) 
        {
             $request->validate([
                // Only allow .csv file types not more than 2mb.
                'csv_file' => 'required|mimes:csv|max:2048',
            ]);

        }


        $file = $request->file('csv_file');
        if ($file) 
        {
            $filename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension(); //Get extension of uploaded file
            $tempPath = $file->getRealPath();
            $fileSize = $file->getSize(); //Get size of uploaded file in bytes
            
            //Where uploaded file will be stored on the server 
            $location = 'uploads'; //Created an "uploads" folder for that
            // Upload file
            $file->move($location, $filename);

            // In case the uploaded file path is to be stored in the database 
            $filepath = public_path($location . "/" . $filename);
            
            // Reading file
            $file = fopen($filepath, "r");
            $importData_arr = array(); // Read through the file and store the contents as an array
            $i = 0;

            //Read the contents of the uploaded file 
            while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                $num = count($filedata);
                
                // Skip first row (Remove below comment if you want to skip the first row)
                if ($i == 0) {
                    $i++;
                    continue;
                }

                for ($c = 0; $c < $num; $c++) {
                    $importData_arr[$i][] = $filedata[$c];
                }
                    $i++;
            }

            fclose($file); //Close after reading
            $j = 0;

            foreach ($importData_arr as $importData) 
            {
                $j++;
                try {
            DB::beginTransaction();
            Post::create([
            'title' => $importData[0],
            'body' => $importData[1],

            // The below will be populated automatically base on logged in user and the selection
            'user_id'       => auth()->id(),
            'category_id'   => $request->category,
            'is_published'  => $request->has('publish'),

            // 'user_id' => $importData[3],
            // 'category_id' => $importData[4],
            // 'is_published' => $importData[5]

            ]);

                DB::commit();
                } catch (\Exception $e) {
                    //throw $th;
                    DB::rollBack();
                }
            }

                session()->flash('message', 'Post uploaded successfully.');
                return redirect()->route('user.posts');


            } 
            else 
            {
                //no file was uploaded
                session()->flash('message', 'No file was uploaded.');
            }
        
        return view('user.posts.upload', ['categories'=> Category::all()]);
    }



    public function create()
    {
        return view('user.posts.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'title'      => 'required|max:250',
            'body'       => 'required|min:50',
            'category'   => 'required|exists:categories,id',
            'publish'    => 'accepted'
        ]);

        $post = Post::create([
            'title'         => $request->title,
            'body'          => $request->body,
            'user_id'       => auth()->id(),
            'category_id'   => $request->category,
            'is_published'  => $request->has('publish'),
        ]);

        session()->flash('message', 'Post created successfully.');

        return redirect()->route('user.posts');
    }

    public function edit(Post $post)
    {
        if($post->user_id != auth()->user()->id && auth()->user()->isNotAdmin()) {

            session()->flash('message', "You can't edit other peoples post.");

            return redirect()->route('user.posts');
        }

        return view('user.posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        if($post->user_id != auth()->user()->id && auth()->user()->isNotAdmin()) {

            session()->flash('message', "You can't update other peoples post.");

            return redirect()->route('user.posts');
        }

        $request->validate([
            'title'      => 'required|max:250',
            'body'       => 'required|min:50',
            'category'   => 'required|exists:categories,id',
            'publish'    => 'accepted'
        ]);

        $post->update([
            'title'       => $request->title,
            'body'        => $request->body,
            'category_id' => $request->category,
            'is_published'  => $request->has('publish'),
        ]);

        session()->flash('message', 'Post updated successfully.');

        return redirect()->to("/posts/$post->id");
    }

    public function destroy(Post $post)
    {
        if($post->user_id != auth()->user()->id && auth()->user()->isNotAdmin()) {

            session()->flash('message', "You can't delete other peoples post.");

            return redirect()->route('user.posts');
        }

        $post->delete();

        session()->flash('message', 'Post deleted successfully.');

        return redirect()->route('user.posts');
    }







}
