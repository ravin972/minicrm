<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLeadRequest;
use App\Http\Requests\UpdateLeadRequest;
use App\Models\Lead;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Display a listing of leads.
     */
    public function index(Request $request)
    {
        $query = Lead::with(['customer', 'assignedTo']);

        // Role-based filtering
        if (auth()->user()->role === 'staff') {
            $query->where('assigned_to', auth()->id());
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($customerQuery) use ($search) {
                      $customerQuery->where('name', 'like', "%{$search}%")
                                   ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by source
        if ($request->has('source') && $request->source) {
            $query->where('source', $request->source);
        }

        // Filter by assigned user
        if ($request->has('assigned_to') && $request->assigned_to) {
            $query->where('assigned_to', $request->assigned_to);
        }

        // Filter by customer
        if ($request->has('customer_id') && $request->customer_id) {
            $query->where('customer_id', $request->customer_id);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $leads = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $leads,
            'message' => 'Leads retrieved successfully'
        ]);
    }

    /**
     * Store a newly created lead.
     */
    public function store(StoreLeadRequest $request)
    {
        $lead = Lead::create($request->validated());
        $lead->load(['customer', 'assignedTo']);

        return response()->json([
            'success' => true,
            'data' => $lead,
            'message' => 'Lead created successfully'
        ], 201);
    }

    /**
     * Display the specified lead.
     */
    public function show(Lead $lead)
    {
        $lead->load(['customer', 'assignedTo', 'tasks.assignedTo']);

        return response()->json([
            'success' => true,
            'data' => $lead,
            'message' => 'Lead retrieved successfully'
        ]);
    }

    /**
     * Update the specified lead.
     */
    public function update(UpdateLeadRequest $request, Lead $lead)
    {
        $lead->update($request->validated());
        $lead->load(['customer', 'assignedTo']);

        return response()->json([
            'success' => true,
            'data' => $lead,
            'message' => 'Lead updated successfully'
        ]);
    }

    /**
     * Remove the specified lead.
     */
    public function destroy(Lead $lead)
    {
        // Check if lead has tasks
        if ($lead->tasks()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete lead with existing tasks'
            ], 422);
        }

        $lead->delete();

        return response()->json([
            'success' => true,
            'message' => 'Lead deleted successfully'
        ]);
    }

    /**
     * Get lead statistics.
     */
    public function statistics(Request $request)
    {
        $query = Lead::query();
        
        // Role-based filtering for statistics
        if (auth()->user()->role === 'staff') {
            $query->where('assigned_to', auth()->id());
        }

        $stats = [
            'total_leads' => $query->count(),
            'new_leads' => $query->where('status', 'new')->count(),
            'contacted_leads' => $query->where('status', 'contacted')->count(),
            'qualified_leads' => $query->where('status', 'qualified')->count(),
            'lost_leads' => $query->where('status', 'lost')->count(),
            'leads_this_month' => $query->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'leads_by_status' => $query->selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray(),
            'leads_by_source' => $query->selectRaw('source, count(*) as count')
                ->groupBy('source')
                ->pluck('count', 'source')
                ->toArray(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
            'message' => 'Lead statistics retrieved successfully'
        ]);
    }

    /**
     * Get leads by customer.
     */
    public function byCustomer(Customer $customer)
    {
        $leads = $customer->leads()->with(['assignedTo', 'tasks'])->get();

        return response()->json([
            'success' => true,
            'data' => $leads,
            'message' => 'Customer leads retrieved successfully'
        ]);
    }

    /**
     * Get leads assigned to authenticated user.
     */
    public function myLeads(Request $request)
    {
        $query = Lead::with(['customer', 'tasks'])
            ->where('assigned_to', auth()->id());

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $leads = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $leads,
            'message' => 'My leads retrieved successfully'
        ]);
    }

    /**
     * Update lead status.
     */
    public function updateStatus(Request $request, Lead $lead)
    {
        $request->validate([
            'status' => 'required|in:new,contacted,qualified,lost',
        ]);

        $lead->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'data' => $lead,
            'message' => 'Lead status updated successfully'
        ]);
    }
}