<?php

namespace App\Http\Controllers\Api;

use App\Models\Entry;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\CreateEntryRequest;
use App\Http\Requests\DelimerRequest;
use Illuminate\Support\Facades\Auth;

class EntryController extends Controller
{
    public function createEntry(CreateEntryRequest $request){
        $request->validated();

        $user = Auth::user();

        if($request->has('entry_id')){
            $entry = Entry::find($request->entry_id);
            $entry->name = $request->name;
            $entry->date_expire = date("yy-m-d" ,strtotime($request->date_expire));
            $entry->to_buy = $request->to_buy;
            $entry->amount = $request->amount;

            $user = $user -> entries() -> save($entry);

            return response()->json([
                'status' => true,
                'entry' => $entry
            ]);

        }
        
        $entry = Entry::create([
            'name' => $request->name, 
            'date_expire' => date("yy-m-d" ,strtotime($request->date_expire)),
            'to_buy' => $request->to_buy,
            'user_id' => $user->id,
            'amount' => $request->amount
        ]);

        $user = $user -> entries() -> save($entry);
        $entry -> save();

        return response()->json([
            'status' => true,
            'entry' => $entry
        ]);
    }

    public function delimer(DelimerRequest $request){
        $user = Auth::user();
        if($request->val_token != "a393cb768aa5a70b82f40e0015cc0586"){
            return response()->json([
                'status' => false,
                'message' => "Delimer creation failed"
            ]);
        }
        $entry = Entry::create([
            'name' => $request->name,
            'date_expire' => "1000-10-10",
            'to_buy' => "10110",
            'user_id' => $user->id,
            'amount' => "10110"
        ]);
        $user = $user -> entries() -> save($entry);
        return response()->json([
            'status' => true,
            'message' => "Created delimer succesfuly"
        ]);
    }


    public function all(){
        $user = Auth::user();
        return response()->json([
            'status' => true,
            'entries' => $user->entries
        ]);
    }
}
