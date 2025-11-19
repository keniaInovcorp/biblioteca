<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ReviewController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('moderateAny', Review::class);

        return view('reviews.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Review $review)
    {
       $this->authorize('moderate', $review);

        $review->load(['user', 'book', 'submission']);

        return view('reviews.show', compact('review'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
    * Approved review.
    */
    public function approve(Review $review)
    {
        $this->authorize('moderate', $review);

        $review->update(['status' => 'active']);

        // Trigger event to notify citizen.
        event(new \App\Events\ReviewStatusChanged($review));

        return redirect()->route('reviews.index')
            ->with('success', 'Review aprovada com sucesso!');
    }

     public function reject(Request $request, Review $review)
    {
        $this->authorize('moderate', $review);

        $request->validate([
            'rejection_reason' => 'required|string|min:10|max:500',
        ]);

        $review->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);


        event(new \App\Events\ReviewStatusChanged($review));

        return redirect()->route('reviews.index')
            ->with('success', 'Review rejeitada com sucesso!');
    }
}
