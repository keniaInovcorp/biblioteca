<?php

namespace App\Http\Controllers;

use App\Events\SubmissionCreated;
use App\Http\Requests\SubmissionRequest;
use App\Models\Book;
use App\Models\Submission;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Submission::class);
        return view('submissions.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Submission::class);
        return view('submissions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SubmissionRequest $request)
    {
        $this->authorize('create', Submission::class);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Validate limit of 3 requests
        $activeSubmissionsCount = $user->activeSubmissions()->count();
        if ($activeSubmissionsCount >= 3) {
            return back()
                ->withInput()
                ->with('error', 'Você já tem 3 requisições ativas. Devolva um livro antes de requisitar outro.');
        }

        $book = Book::findOrFail($request->book_id);

        // Please check availability again.
        if (!$book->isAvailable()) {
            return back()
                ->withInput()
                ->with('error', 'Este livro não está disponível para requisição.');
        }

        // Create request
        $submission = Submission::create([
            'request_number' => Submission::generateRequestNumber(),
            'user_id' => $user->id,
            'book_id' => $book->id,
            'request_date' => now(),
            'expected_return_date' => now()->addDays(5),
            'status' => 'created',
        ]);

        return redirect()
            ->route('submissions.index')
            ->with('success', 'Requisição criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Submission $submission)
    {

        return view('submissions.show', [
            'submission' => $submission->loadMissing(['book', 'user']),
        ]);
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
     * Confirm return of the book (Admin only)
     */
    public function confirmReturn(Submission $submission)
    {
        $this->authorize('update', $submission);

        // Only admin can confirm returns
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user->hasRole('admin')) {
            abort(403, 'Apenas administradores podem confirmar devoluções.');
        }

        // Can only return if not already returned
        if ($submission->status === 'returned') {
            return back()->with('error', 'Esta requisição já foi devolvida.');
        }

        // Calculate days elapsed from request_date to now
        /** @var Carbon $requestDate */
        $requestDate = $submission->request_date;
        $daysElapsed = $requestDate->diffInDays(now()->startOfDay());

        $submission->update([
            'status' => 'returned',
            'received_at' => now(),
            'days_elapsed' => $daysElapsed,
        ]);

        return back()->with('success', 'Devolução confirmada com sucesso!');
    }
}
