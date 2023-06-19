<?php

namespace App\Http\Controllers;

use App\Models\JobOffer;
use App\Models\Entry;
use Illuminate\Support\Facades\Auth;

class EntryController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(JobOffer $job_offer)
    {
        $entry = new Entry([
            'job_offer_id' => $job_offer->id,
            'user_id' => Auth::user()->id,
        ]);

        try {
            // 登録
            $entry->save();
        } catch (\Exception $e) {
            return back()->withInput()
                ->withErrors('エントリーでエラーが発生しました');
        }

        return redirect()
            ->route('job_offers.show', $job_offer)
            ->with('notice', 'エントリーしました');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JobOffer $job_offer, Entry $entry)
    {
        $entry->delete();

        return redirect()->route('job_offers.show', $job_offer)
            ->with('notice', 'エントリーを取り消しました');
    }

    /**
     *
     */
    public function approval(JobOffer $job_offer, Entry $entry)
    {
        $entry->status = Entry::STATUS_APPROVAL;
        $entry->save();

        return redirect()->route('job_offers.show', $job_offer)
            ->with('notice', 'エントリーを承認しました');
    }

    /**
     *
     */
    public function reject(JobOffer $job_offer, Entry $entry)
    {
        $entry->status = Entry::STATUS_REJECT;
        $entry->save();

        return redirect()->route('job_offers.show', $job_offer)
            ->with('notice', 'エントリーを却下しました');
    }
}
