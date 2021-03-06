<?php

namespace App\Http\Controllers;

use App\File;
use App\FileType;
use Illuminate\Http\Request;
use App\Http\Requests\FileRequest;
use Illuminate\Support\Facades\Storage;

class FilesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $files = File::with('type')->where('active', true)->get();

        return view('admin.files.index', compact('files'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $file = new File;
        $fileTypes = FileType::where('feedId', config('instances.file_feeds.admin_uploads'))
            ->orderBy('fileTypeName')->get();
        $action = 'create';

        $params = compact('file', 'fileTypes', 'action');

        return view('admin.files.create', $params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\FileRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FileRequest $request)
    {
        $file = new File;
        $request->save($file);

        flash(__('File created.'));

        return redirect()->route('admin.files.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\File  $file
     * @return \Illuminate\Http\Response
     */
    public function show(File $file)
    {
        try {
            $privateFile = Storage::disk('s3')->get($file->path);
        } catch (\Exception $e) {
            \Log::error($e);
            flash(__('There was a problem with the file you were trying to download.'), 'error');
            return back();
        }

        $headers = [
            'Content-Type' => 'application/octet-stream',
            'Content-Description' => 'File Transfer',
            'Content-Disposition' => "attachment; filename={$file->fileName}",
            'filename'=> $file->fileName,
        ];

        return response($privateFile, 200, $headers);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\File  $file
     * @return \Illuminate\Http\Response
     */
    public function edit(File $file)
    {
        $fileTypes = FileType::where('feedId', config('instances.file_feeds.admin_uploads'))
            ->orderBy('fileTypeName')->get();
        $action = 'edit';

        $params = compact('file', 'action', 'fileTypes');

        return view('admin.files.edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\FileRequest  $request
     * @param  \App\File  $file
     * @return \Illuminate\Http\Response
     */
    public function update(FileRequest $request, File $file)
    {
        $request->save($file);

        flash(__('File updated.'));

        return redirect()->route('admin.files.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\File  $file
     * @return \Illuminate\Http\Response
     */
    public function destroy(File $file)
    {
        $file->active = false;
        $file->save();

        flash(__('File deleted.'));

        return back();
    }
}
