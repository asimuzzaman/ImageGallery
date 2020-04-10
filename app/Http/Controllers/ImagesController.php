<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Validator;
use App\Images;
use App\Libraries\myJSON; //custom JSON library developed by this developer (Md. Asimuzzaman)
use App\Libraries\ThumbGen; //custom library, modified by this developer

class ImagesController extends Controller
{
    private $fileName = 'database.json'; //JSON database file name

    function show() {
        //Model based data populator
    	//$all_image = Images::orderBy('created_at', 'DESC')->get();

        //JSON based data populator
        $data = new myJSON($this->fileName);
        $all_image = $data->all();

    	return view('images', [ 'all_image' => $all_image->reverse() ]); //reverse for descending order
    }

    function save(Request $request) {
    	$checker = Validator::make($request->all(), [
	      'image.*' => 'required|image|mimes:png,jpg,jpeg|max:5120', //image.* as the image field is an array
	      'title' => 'required'
	     ]);
    	
	    if($checker->passes()) {
            $image = $request->file('image')[0];
			$newImage = time() . "." . $image->getClientOriginalExtension();
			$image->move(public_path('uploads'), $newImage);

            //Thumbnail creation
            ThumbGen::create_cropped_thumbnail(public_path("/uploads/".$newImage)."", 200, 152, public_path("/uploads/thumbs"));

            //Accessing data from JSON using custom library
            $data = new myJSON($this->fileName);
            $newId = $data->insert($request->title, $newImage); //latest Inserted ID

            //Model based insertion
            /*
            $image = new Images;
            $image->title = $request->title;
            $image->address = $newImage;
            $image->save();
            */
			
            //responding with success data to front end
			return response()->json([
				'message'   => 'Image Uploaded Successfully',
				'address' => $newImage,
                'title' => $request->title,
                'id' => $newId
			]);
	    }
	    else {
			return response()->json([
				'error'   => $checker->errors()->all(),
			]);
	    }
    }

    function search(Request $request) {
    	if ($request->ajax()) {
            $data = new myJSON($this->fileName);
            $result = $data->search($request->keyword);

            //Model based search
            //$result = Images::where('title','LIKE','%'.$request->keyword.'%')->get();

            return response()->json([
                'data' => $result
            ]);
    	}
    }

    function remove(Request $request) {
    	$id = $request->id;
        
        $data = new myJSON($this->fileName);
        $image = $data->find($id);

        //Model based deletion
        /*
        $image = Images::find($id);
        $image->delete();
        */

    	$image_path = public_path()."/images/".$image['address'];

    	if(File::exists($image_path))
    		File::delete($image_path); //delete image from directory

        $data->delete($id); //delete entry from JSON file

    	return response()->json(['message' => 'Image has been deleted.']);
    }
}
