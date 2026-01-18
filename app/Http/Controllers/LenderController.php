<?php

namespace App\Http\Controllers;

use App\Models\Lender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class LenderController extends Controller
{
    public function index(Request $request)
    {
        $query = Auth::user()->lenders()->orderBy('company_name');

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has('active')) {
            $query->where('is_active', $request->boolean('active'));
        }

        $lenders = $query->paginate(20);

        return view('lenders.index', compact('lenders'));
    }

    public function create()
    {
        return view('lenders.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('lenders')->where(function ($query) {
                    return $query->where('user_id', Auth::id());
                }),
            ],
            'email_2' => 'nullable|email|max:255',
            'email_3' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:2000',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['is_active'] = true;

        $lender = Lender::create($validated);

        return redirect()->route('lenders.index')
            ->with('success', "Lender '{$lender->company_name}' created successfully.");
    }

    public function edit(Lender $lender)
    {
        $this->authorize('update', $lender);

        return view('lenders.edit', compact('lender'));
    }

    public function update(Request $request, Lender $lender)
    {
        $this->authorize('update', $lender);

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('lenders')->where(function ($query) {
                    return $query->where('user_id', Auth::id());
                })->ignore($lender->id),
            ],
            'email_2' => 'nullable|email|max:255',
            'email_3' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:2000',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $lender->update($validated);

        return redirect()->route('lenders.index')
            ->with('success', "Lender '{$lender->company_name}' updated successfully.");
    }

    public function destroy(Lender $lender)
    {
        $this->authorize('delete', $lender);

        $companyName = $lender->company_name;
        $lender->delete();

        return redirect()->route('lenders.index')
            ->with('success', "Lender '{$companyName}' deleted successfully.");
    }

    public function list(Request $request)
    {
        $lenders = Auth::user()->lenders()
            ->active()
            ->orderBy('company_name')
            ->get(['id', 'company_name', 'first_name', 'last_name', 'email']);

        return response()->json($lenders);
    }
}
