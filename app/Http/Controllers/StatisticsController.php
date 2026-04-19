<?php

namespace App\Http\Controllers;

use App\Services\StatisticsService;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    protected $statisticsService;

    public function __construct(StatisticsService $statisticsService)
    {
        $this->statisticsService = $statisticsService;
    }

    /**
     * Display the statistics dashboard
     */
    public function dashboard(Request $request)
    {
        $period = $request->query('period', 'month');
        
        $overview = $this->statisticsService->getDashboardOverview($period);
        $revenue_trend = $this->statisticsService->getRevenueByPeriod('daily', 30);
        $top_books = $this->statisticsService->getTopSellingBooks(10);
        $sales_by_category = $this->statisticsService->getSalesByCategory();
        $order_status = $this->statisticsService->getOrderStatusDistribution();
        $average_rating = $this->statisticsService->getAverageRating();
        $most_reviewed = $this->statisticsService->getMostReviewedBooks(5);
        $top_customers = $this->statisticsService->getTopCustomers(5);
        $low_stock = $this->statisticsService->getLowStockBooks(5);
        $out_of_stock_count = $this->statisticsService->getOutOfStockBooks();
        $monthly_growth = $this->statisticsService->getMonthlyGrowthRate();
        $repeat_purchase_rate = $this->statisticsService->getRepeatPurchaseRate();
        
        return view('admin.statistics.dashboard', compact(
            'overview',
            'period',
            'revenue_trend',
            'top_books',
            'sales_by_category',
            'order_status',
            'average_rating',
            'most_reviewed',
            'top_customers',
            'low_stock',
            'out_of_stock_count',
            'monthly_growth',
            'repeat_purchase_rate'
        ));
    }

    /**
     * Display sales analytics
     */
    public function sales(Request $request)
    {
        $type = $request->query('type', 'daily');
        $limit = $request->query('limit', 30);
        
        $revenue_data = $this->statisticsService->getRevenueByPeriod($type, $limit);
        $top_books = $this->statisticsService->getTopSellingBooks(10);
        $sales_by_category = $this->statisticsService->getSalesByCategory();
        $order_status = $this->statisticsService->getOrderStatusDistribution();
        
        return view('admin.statistics.sales', compact(
            'revenue_data',
            'top_books',
            'sales_by_category',
            'order_status',
            'type'
        ));
    }

    /**
     * Display review analytics
     */
    public function reviews()
    {
        $average_rating = $this->statisticsService->getAverageRating();
        $rating_distribution = $this->statisticsService->getRatingDistribution();
        $most_reviewed_books = $this->statisticsService->getMostReviewedBooks(10);
        $approval_rate = $this->statisticsService->getReviewApprovalRate();
        $pending_reviews = $this->statisticsService->getTotalPendingReviews();
        
        return view('admin.statistics.reviews', compact(
            'average_rating',
            'rating_distribution',
            'most_reviewed_books',
            'approval_rate',
            'pending_reviews'
        ));
    }

    /**
     * Display customer analytics
     */
    public function customers()
    {
        $total_customers = $this->statisticsService->getTotalCustomers();
        $top_customers = $this->statisticsService->getTopCustomers(10);
        $repeat_purchase_rate = $this->statisticsService->getRepeatPurchaseRate();
        $avg_clv = $this->statisticsService->getAverageCustomerLifetimeValue();
        $new_customers = $this->statisticsService->getNewCustomersThisMonth();
        
        return view('admin.statistics.customers', compact(
            'total_customers',
            'top_customers',
            'repeat_purchase_rate',
            'avg_clv',
            'new_customers'
        ));
    }

    /**
     * Display inventory analytics
     */
    public function inventory()
    {
        $total_stock = $this->statisticsService->getTotalBooksInStock();
        $total_sold = $this->statisticsService->getTotalBooksSold();
        $low_stock_books = $this->statisticsService->getLowStockBooks(10);
        $out_of_stock_count = $this->statisticsService->getOutOfStockBooks();
        $dead_stock = $this->statisticsService->getDeadStockBooks(10);
        $stock_turnover = $this->statisticsService->getStockTurnoverByCategory();
        
        return view('admin.statistics.inventory', compact(
            'total_stock',
            'total_sold',
            'low_stock_books',
            'out_of_stock_count',
            'dead_stock',
            'stock_turnover'
        ));
    }

    /**
     * Get JSON data for AJAX requests (for charts)
     */
    public function getChartData(Request $request)
    {
        $type = $request->query('type');
        
        switch ($type) {
            case 'revenue_trend':
                $period = $request->query('period', 'daily');
                return response()->json($this->statisticsService->getRevenueByPeriod($period, 30));
            case 'order_status':
                return response()->json($this->statisticsService->getOrderStatusDistribution());
            case 'rating_distribution':
                return response()->json($this->statisticsService->getRatingDistribution());
            case 'sales_by_category':
                return response()->json($this->statisticsService->getSalesByCategory());
            case 'top_books':
                return response()->json($this->statisticsService->getTopSellingBooks(10));
            default:
                return response()->json(['error' => 'Invalid chart type'], 400);
        }
    }
}
