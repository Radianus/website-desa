<?php

namespace App\Http\Controllers; // Perhatikan namespace ini, tidak di subfolder Admin

use App\Models\News;
use App\Models\Product;
use App\Models\Gallery;
use App\Models\Document;
use App\Models\Event;
use App\Models\Institution;
use App\Models\PengajuanSurat;
use App\Models\Comment;
use App\Models\Visit;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard.
     * Fetches various statistics and recent activities.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // --- Statistik Umum ---
        $totalNews = News::count();
        $totalProducts = Product::count();
        $totalGalleries = Gallery::count();
        $totalDocuments = Document::count();
        $totalEvents = 0;
        $totalInstitutions = Institution::count();

        // --- Statistik Layanan & Moderasi ---
        $pendingServiceRequests = PengajuanSurat::where('status', 'pending')->count();
        $totalServiceProcedures = \App\Models\ServiceProcedure::count();
        $pendingComments = Comment::where('is_approved', false)->count();

        // --- Statistik Kunjungan (BARU) ---
        $totalVisits = Visit::count();
        $uniqueVisitors = Visit::select('ip_address')->distinct()->count();
        $todayVisits = Visit::whereDate('created_at', Carbon::today())->count();
        $todayUniqueVisitors = Visit::whereDate('created_at', Carbon::today())->select('ip_address')->distinct()->count();


        // --- Aktivitas Terbaru ---
        // Eager load news for comments
        $latestNews = News::orderBy('created_at', 'desc')->take(5)->get();
        $latestServiceRequests = PengajuanSurat::orderBy('created_at', 'desc')->take(5)->get();
        $latestComments = Comment::where('is_approved', false)->orderBy('created_at', 'desc')->take(5)->with('news')->get();

        return view('admin.dashboard', compact(
            'totalNews',
            'totalProducts',
            'totalGalleries',
            'totalDocuments',
            'totalEvents',
            'totalInstitutions',
            'pendingServiceRequests',
            'totalServiceProcedures',
            'pendingComments',
            'latestNews',
            'latestServiceRequests',
            'latestComments',
            'totalVisits',
            'uniqueVisitors',
            'todayVisits',
            'todayUniqueVisitors'
        ));
    }
}