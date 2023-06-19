<?php

namespace App\Http\Controllers;

use App\Models\JobOffer;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class MessageController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(JobOffer $job_offer, Request $request)
    {
        $message = new Message($request->all());
        $message->messageable_type = 'App\Models\JobOffer';
        $message->messageable_id = $request->messageable_id;
        $message->user_id = Auth::user()->id;
        $message->message = $request->message;

        try {
            // 登録
            $message->save();
        } catch (\Exception $e) {
            return back()->withInput()
                ->withErrors('メッセージ登録処理でエラーが発生しました');
        }

        $job_offer = JobOffer::find($request->messageable_id);

        return redirect()
            ->route('job_offers.show', $job_offer)
            ->with('notice', 'メッセージを登録しました');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JobOffer $job_offer, Message $message)
    {
        $job_offer = $message->messageable;

        if (Auth::user()->id != $message->user_id) {
            return redirect()->route('job_offers.show', $job_offer)
                ->withErrors('自分のメッセージ以外は削除できません');
        }

        try {
            $message->delete();
        } catch (\Exception $e) {
            return back()->withInput()
                ->withErrors('メッセージ削除処理でエラーが発生しました');
        }

        return redirect()->route('job_offers.show', $job_offer)
            ->with('notice', ' メッセージを削除しました');
    }
}