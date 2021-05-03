<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Node;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $nodes = Node::select('id','name','type','parent_id')->get()->toArray();
        $new = array();
        foreach ($nodes as $a){
            $new[$a['parent_id']][] = $a;
        }
        $tree = static::getStructure($new, array($nodes[0]));
        return response()->json([
            'result' =>  $tree,
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        $new = new Node();
        $new->name = $request->name;
        $found = Node::find($request->parent_id);
        $path = 'public';
        if($found){
            $path = $found->path;
        }
        $new->parent_id = $request->parent_id;
        if($request->is_file == "true"){
            $x = Storage::put($path, $request->file);
            $new->file = $x;
            $new->type = 'file';
            $new->path = $x;   
        }else{
            $temp = $path.'/'.$request->name;
            $response = Storage::makeDirectory($temp);
            $new->file = null;
            $new->type = 'dir';
            $new->path = $temp;
        }
        $new->save();
        return response()->json([
            'message' => 'success',
            'id'=>  $new->id,
        ], 200);
    }
    public function destroy($id)
    {
       $node = Node::find($id);
       if($node->type == 'file'){
         Storage::delete($node->path);
       }else{
         Storage::deleteDirectory($node->path);
       }
        Node::where('parent_id',$id)->orWhere('id',$id)->delete();
       return response()->json([
        'message' => 'deleted'
        ], 200);
    }
    public static function getStructure(&$list,$parent){
        $tree = array();
        foreach ($parent as $k=>$l){
            if(isset($list[$l['id']])){
                $l['children'] = static::getStructure($list, $list[$l['id']]);
            }else{
                $l['children'] = [];
            }

            $tree[] = $l;
        } 
        return $tree;
        }
}
