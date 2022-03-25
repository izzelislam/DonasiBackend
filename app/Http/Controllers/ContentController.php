<?php

namespace App\Http\Controllers;

use App\Models\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ContentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['contents'] = Content::orderBy('created_at','desc')->get();
        return view('content.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['action'] = route('contents.store');
        return view('content.form', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'img' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'content' => 'required'
        ], [
            'title.required' => 'Title is required',
            'img.required' => 'Image is required',
            'img.image' => 'Image must be an image',
            'img.mimes' => 'Image must be a file of type: jpeg, jpg, png, gif, svg',
            'img.max' => 'Image must be less than 2048 kilobytes',
            'content.required' => 'Content is required'
        ]);

        $image = $request->file('img');
        $request['image'] =  $image->store('images/content', 'public');

        Content::create($request->all());

        return redirect()->route('contents.index')->with('success', 'Content created successfully');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data['model'] = Content::find($id);
        return view('content.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['model'] = Content::find($id);
        $data['action'] = route('contents.update', $id);
        return view('content.form', $data);
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
        $request->validate([
            'title' => 'required',
            'img' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'content' => 'required'
        ], [
            'title.required' => 'Title is required',
            'img.image' => 'Image must be an image',
            'img.mimes' => 'Image must be a file of type: jpeg, jpg, png, gif, svg',
            'img.max' => 'Image must be less than 2048 kilobytes',
            'content.required' => 'Content is required'
        ]);

        $content = Content::find($id);

        if ($request->hasFile('img')) {
            $image = $request->file('img');
            
            if (Storage::disk('public')->exists($content->image)) {
                Storage::disk('public')->delete($content->image);
            }else{
                dd('kaga ada', $content->image);
            }

            $request['image'] =  $image->store('images/content', 'public');
            
            $content->update($request->all());
        } else {
            $content->update($request->except('img'));
        }

        return redirect()->route('contents.index')->with('success', 'Content updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $content = Content::find($id);

        if (Storage::exists('images/content/' . $content->image)) {
            Storage::delete('images/content/' . $content->image);
        }

        $content->delete();

        return redirect()->route('contents.index')->with('success', 'Content deleted successfully');
    }
}
