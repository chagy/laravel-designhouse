<?php

namespace App\Http\Controllers\Designs;

use App\Models\Design;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\DesignResource;
use App\Repositories\Contracts\IDesign;
use Illuminate\Support\Facades\Storage;
use App\Repositories\Eloquent\Criteria\IsLive;
use App\Repositories\Eloquent\Criteria\LatestFirst;

class DesignController extends Controller
{
    protected $designs;

    public function __construct(IDesign $designs)
    {
        $this->designs = $designs;
    }

    public function index()
    {
        $designs = $this->designs->withCriteria([
            new LatestFirst(),
            new IsLive()
        ])->all();
        return DesignResource::collection($designs);
    }

    public function findDesign($id)
    {
        $design = $this->designs->find($id);
        return new DesignResource($design);
    }

    public function update(Request $request,$id)
    {
        $design = $this->designs->find($id);

        $this->authorize('update',$design);
        
        $this->validate($request,[
            'title' => ['required','unique:designs,title,'.$id],
            'description' => ['required','string','min:20','max:140'],
            'tags' => ['required']
        ]);

        $this->designs->update($id,[
            'title' => $request->title,
            'description' => $request->description,
            'slug' => Str::slug($request->title),
            'is_live' => !$design->upload_successful ? false : $request->is_live,
        ]);

        $this->designs->applyTags($id,$request->tags);

        return new DesignResource($design);
    }

    public function destroy($id)
    {
        $design = $this->designs->find($id);

        $this->authorize('delete',$design);

        foreach(['thumbnail','large','original'] as $size){
            if(Storage::disk($design->disk)->exists("uploads/designs/{$size}/".$design->image)){
                Storage::disk($design->disk)->delete("uploads/designs/{$size}/".$design->image);
            }
        }

        $this->designs->delete();

        return response()->json([
            'message' => 'Record deleted'
        ],200);
    }
}
