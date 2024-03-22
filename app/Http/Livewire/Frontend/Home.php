<?php

namespace App\Http\Livewire\Frontend;

use App\Models\Condidate;
use App\Models\User;
use App\Models\Votes;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Http\Request;

class Home extends Component
{
    public $candidates;
    public function render()
    {
        $this->candidates = Condidate::orderBy('id', 'ASC')->get();
        return view('livewire.frontend.home')->layout('layout.app');
    }

    public function addVote(Request $request,$id)
    {
        // so use can do only one ovote
        $ipAddress = $request->ip();
        $votes = new Votes();
        $addvote = Condidate::findOrFail($id);
        $addvote->points = $addvote->points + 1;
        $addvote->ip_address = $ipAddress;
        $addvote->save();
        $votes->user_id = Auth::user()->id;
        $votes->con_id = $id;
        $votes->save();
        $users = User::findOrFail(Auth::user()->id);
        $users->vote_limit = 0;
        $users->voted = true;
        $users->save();
        if ($users->vote_limit == 0) {
            return redirect('/logout');
            Auth::logout();
            return redirect(route('front.login'));
        }
    }
}
