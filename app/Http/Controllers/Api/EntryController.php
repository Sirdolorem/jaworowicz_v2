<?php

namespace App\Http\Controllers\Api;

use app\Models\Entry;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\CreateEntryRequest;
use Illuminate\Support\Facades\Auth;

class EntryController extends Controller
{
    public function createEntry(CreateEntryRequest $request){
        $request->validated();

        $user = Auth::user();
        $entry = Entry::create([
            'name' => $request->name, 
            'date_expire' => $request->date_expire,
            'to_buy' => $request->to_buy,
            'amount' => $request->amount
        ]);

        $user -> entries() -> save($entry);
        $entry -> save();

        return response()->json([
            'status' => true,
            'entry' => $entry
        ]);
    }
}
