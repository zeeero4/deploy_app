<?php

namespace App\Http\Controllers;

use App\Models\JobOffer;
use App\Models\JobOfferView;
use App\Models\Message;
use App\Models\Occupation;
use App\Http\Requests\JobOfferRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobOfferController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $params = $request->query();
        $job_offers = JobOffer::search($params)->published()
            ->with(['company', 'occupation'])->order($params)->paginate(5);

        $job_offers->appends($params);

        $occupations = Occupation::all();

        return view('job_offers.index', compact('job_offers', 'occupations', 'params'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $occupations = Occupation::all();
        return view('job_offers.create', compact('occupations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(JobOfferRequest $request)
    {
        $job_offer = new JobOffer($request->all());
        $job_offer->company_id = $request->user()->company->id;

        try {
            // 登録
            $job_offer->save();
        } catch (\Exception $e) {
            return back()->withInput()
                ->withErrors('求人情報登録処理でエラーが発生しました');
        }

        return redirect()
            ->route('job_offers.show', $job_offer)
            ->with('notice', '求人情報を登録しました');
    }

    /**
     * Display the specified resource.
     */
    public function show(JobOffer $job_offer)
    {
        JobOfferView::updateOrCreate([
            'job_offer_id' => $job_offer->id,
            'user_id' => Auth::user()->id,
        ]);

        $entry = !isset(Auth::user()->company)
            ? $job_offer->entries()->firstWhere('user_id', Auth::user()->id)
            : '';

        $entries = Auth::user()->id == $job_offer->company->user_id
            ? $job_offer->entries()->with('user')->get()
            : [];

        $messages = $job_offer->messages->load('user');

        return view('job_offers.show', compact('job_offer', 'entry', 'entries', 'messages'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JobOffer $job_offer)
    {
        $occupations = Occupation::all();
        return view('job_offers.edit', compact('job_offer', 'occupations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(JobOfferRequest $request, JobOffer $job_offer)
    {
        if (Auth::user()->cannot('update', $job_offer)) {
            return redirect()->route('job_offers.show', $job_offer)
                ->withErrors('自分の求人情報以外は更新できません');
        }
        $job_offer->fill($request->all());
        try {
            $job_offer->save();
        } catch (\Exception $e) {
            return back()->withInput()
                ->withErrors('求人情報更新処理でエラーが発生しました');
        }
        return redirect()->route('job_offers.show', $job_offer)
            ->with('notice', '求人情報を更新しました');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JobOffer $job_offer)
    {
        if (Auth::user()->cannot('delete', $job_offer)) {
            return redirect()->route('job_offers.show', $job_offer)
                ->withErrors('自分の求人情報以外は削除できません');
        }

        try {
            $job_offer->delete();
        } catch (\Exception $e) {
            return back()->withInput()
                ->withErrors('求人情報削除処理でエラーが発生しました');
        }

        return redirect()->route('job_offers.index')
            ->with('notice', '求人情報を削除しました');
    }
}