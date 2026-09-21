<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Exception;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        try {
            $companies = Company::withCount('blogs')->latest()->get();

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'message' => 'Company fetched successfully',
                    'data'    => $companies,
                ], 200);
            }

            return view('admin.companies.index', compact('companies'));
        } catch (Exception $e) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch companies',
                    'error'   => $e->getMessage(),
                ], 500);
            }
            return back()->with('error', 'Failed to fetch companies: ' . $e->getMessage());
        }
    }

    public function create()
    {
        return view('admin.companies.form');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
            ]);

            $company = Company::create($validated);

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'message' => 'Company created successfully',
                    'data'    => $company,
                ], 201);
            }

            return redirect()->route('admin.companies.index')
                             ->with('success', 'Company created successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors'  => $e->errors(),
                ], 422);
            }
            throw $e; // Let Laravel handle redirect back with errors
        } catch (Exception $e) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create company',
                    'error'   => $e->getMessage(),
                ], 500);
            }
            return back()->with('error', 'Failed to create company: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Request $request, Company $company)
    {
        try {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'message' => 'Company fetched successfully',
                    'data'    => $company,
                ], 200);
            }
            return view('admin.companies.form', compact('company'));
        } catch (Exception $e) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch company',
                    'error'   => $e->getMessage(),
                ], 500);
            }
            return back()->with('error', 'Failed to fetch company: ' . $e->getMessage());
        }
    }

    public function edit(Company $company)
    {
        return view('admin.companies.form', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        try {
            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
            ]);

            $company->update($validated);

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'message' => 'Company updated successfully',
                    'data'    => $company,
                ], 200);
            }

            return redirect()->route('admin.companies.index')
                             ->with('success', 'Company updated successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors'  => $e->errors(),
                ], 422);
            }
            throw $e;
        } catch (Exception $e) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update company',
                    'error'   => $e->getMessage(),
                ], 500);
            }
            return back()->with('error', 'Failed to update company: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Request $request, Company $company)
    {
        try {
            $company->delete();
            
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'message' => 'Company deleted successfully',
                ], 200);
            }

            return redirect()->route('admin.companies.index')
                             ->with('success', 'Company deleted successfully!');
        } catch (Exception $e) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete company',
                    'error'   => $e->getMessage(),
                ], 500);
            }
            return back()->with('error', 'Failed to delete company: ' . $e->getMessage());
        }
    }
}
