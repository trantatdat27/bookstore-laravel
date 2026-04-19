<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Review;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatisticsService
{
    // === DASHBOARD OVERVIEW STATISTICS ===
    
    /**
     * Get total revenue for a specific period
     */
    public function getTotalRevenue($period = 'all')
    {
        $query = Order::where('status', 'completed');
        
        return $this->filterByPeriod($query, $period)->sum('total_amount');
    }

    /**
     * Get total orders count
     */
    public function getTotalOrders($period = 'all')
    {
        $query = Order::query();
        return $this->filterByPeriod($query, $period)->count();
    }

    /**
     * Get pending orders count
     */
    public function getPendingOrders()
    {
        return Order::where('status', 'pending')->count();
    }

    /**
     * Get total customers
     */
    public function getTotalCustomers()
    {
        return User::where('role', 'customer')->count();
    }

    /**
     * Get average order value
     */
    public function getAverageOrderValue($period = 'all')
    {
        $query = Order::where('status', 'completed');
        $total = $this->filterByPeriod($query, $period)->sum('total_amount');
        $count = $this->filterByPeriod($query, $period)->count();
        
        return $count > 0 ? $total / $count : 0;
    }

    /**
     * Get total books in stock
     */
    public function getTotalBooksInStock()
    {
        return Book::sum('stock');
    }

    /**
     * Get total books sold
     */
    public function getTotalBooksSold()
    {
        return Book::sum('sold');
    }

    /**
     * Get new customers this month
     */
    public function getNewCustomersThisMonth()
    {
        return User::where('role', 'customer')
            ->whereBetween('created_at', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            ])
            ->count();
    }

    // === SALES ANALYTICS ===

    /**
     * Get revenue by period (daily/monthly/yearly)
     */
    public function getRevenueByPeriod($type = 'monthly', $limit = 12)
    {
        $query = Order::where('status', 'completed')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_amount) as revenue'))
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit($limit);

        if ($type === 'monthly') {
            $query = Order::where('status', 'completed')
                ->select(
                    DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                    DB::raw('SUM(total_amount) as revenue')
                )
                ->groupBy(DB::raw('DATE_FORMAT(created_at, "%Y-%m")'))
                ->orderBy('month', 'desc')
                ->limit($limit);
        } elseif ($type === 'yearly') {
            $query = Order::where('status', 'completed')
                ->select(
                    DB::raw('YEAR(created_at) as year'),
                    DB::raw('SUM(total_amount) as revenue')
                )
                ->groupBy(DB::raw('YEAR(created_at)'))
                ->orderBy('year', 'desc');
        }

        return $query->get();
    }

    /**
     * Get top selling books
     */
    public function getTopSellingBooks($limit = 10)
    {
        return OrderItem::select('book_id', 'book_title', DB::raw('SUM(quantity) as total_sold'))
            ->groupBy('book_id', 'book_title')
            ->orderBy('total_sold', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get sales by category
     */
    public function getSalesByCategory()
    {
        return Category::withCount('books')
            ->with([
                'books' => function ($q) {
                    $q->withSum('orderItems', 'quantity');
                }
            ])
            ->get()
            ->map(function ($category) {
                return [
                    'name' => $category->name,
                    'book_count' => $category->books_count,
                    'total_sold' => $category->books->sum('order_items_sum_quantity') ?? 0,
                ];
            });
    }

    /**
     * Get order status distribution
     */
    public function getOrderStatusDistribution()
    {
        return Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }

    // === REVIEW ANALYTICS ===

    /**
     * Get average rating
     */
    public function getAverageRating()
    {
        return Review::where('status', 'approved')
            ->avg('rating') ?? 0;
    }

    /**
     * Get rating distribution
     */
    public function getRatingDistribution()
    {
        return Review::where('status', 'approved')
            ->select('rating', DB::raw('count(*) as count'))
            ->groupBy('rating')
            ->orderBy('rating', 'desc')
            ->pluck('count', 'rating')
            ->toArray();
    }

    /**
     * Get most reviewed books
     */
    public function getMostReviewedBooks($limit = 10)
    {
        return Book::withCount('reviews')
            ->orderBy('reviews_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get review approval rate
     */
    public function getReviewApprovalRate()
    {
        $total = Review::count();
        if ($total === 0) return 0;

        $approved = Review::where('status', 'approved')->count();
        return ($approved / $total) * 100;
    }

    /**
     * Get total pending reviews
     */
    public function getTotalPendingReviews()
    {
        return Review::where('status', 'pending')->count();
    }

    // === CUSTOMER ANALYTICS ===

    /**
     * Get top customers by total spend
     */
    public function getTopCustomers($limit = 10)
    {
        return Order::where('status', 'completed')
            ->select('user_id', 'customer_name', DB::raw('SUM(total_amount) as total_spent'), DB::raw('COUNT(*) as order_count'))
            ->groupBy('user_id', 'customer_name')
            ->orderBy('total_spent', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get repeat purchase rate
     */
    public function getRepeatPurchaseRate()
    {
        $total_customers = User::where('role', 'customer')->count();
        if ($total_customers === 0) return 0;

        $repeat_customers = Order::select('user_id')
            ->groupBy('user_id')
            ->havingRaw('COUNT(*) > 1')
            ->count();

        return ($repeat_customers / $total_customers) * 100;
    }

    /**
     * Get customer lifetime value
     */
    public function getAverageCustomerLifetimeValue()
    {
        $total_revenue = Order::where('status', 'completed')->sum('total_amount');
        $unique_customers = Order::distinct('user_id')->count('user_id');

        return $unique_customers > 0 ? $total_revenue / $unique_customers : 0;
    }

    // === INVENTORY ANALYTICS ===

    /**
     * Get low stock books (less than 5)
     */
    public function getLowStockBooks($threshold = 5)
    {
        return Book::where('stock', '<=', $threshold)
            ->where('stock', '>', 0)
            ->orderBy('stock')
            ->limit(10)
            ->get();
    }

    /**
     * Get out of stock books
     */
    public function getOutOfStockBooks()
    {
        return Book::where('stock', 0)->count();
    }

    /**
     * Get dead stock (zero sales)
     */
    public function getDeadStockBooks($limit = 10)
    {
        return Book::where('sold', 0)
            ->where('stock', '>', 0)
            ->limit($limit)
            ->get();
    }

    /**
     * Get stock turnover by category
     */
    public function getStockTurnoverByCategory()
    {
        return Category::with('books')
            ->get()
            ->map(function ($category) {
                $total_sold = $category->books->sum('sold');
                $total_stock = $category->books->sum('stock');
                $turnover = $total_stock > 0 ? $total_sold / $total_stock : 0;

                return [
                    'name' => $category->name,
                    'total_sold' => $total_sold,
                    'total_stock' => $total_stock,
                    'turnover_rate' => round($turnover, 2),
                ];
            });
    }

    // === TEMPORAL ANALYTICS ===

    /**
     * Get orders by day of week
     */
    public function getOrdersByDayOfWeek()
    {
        return Order::select(
            DB::raw('DAYNAME(created_at) as day'),
            DB::raw('COUNT(*) as count')
        )
            ->groupBy(DB::raw('DAYOFWEEK(created_at)'), DB::raw('DAYNAME(created_at)'))
            ->orderBy('DAYOFWEEK(created_at)')
            ->get();
    }

    /**
     * Get monthly growth rate
     */
    public function getMonthlyGrowthRate()
    {
        $current_month = Order::where('status', 'completed')
            ->whereBetween('created_at', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            ])
            ->sum('total_amount');

        $previous_month = Order::where('status', 'completed')
            ->whereBetween('created_at', [
                Carbon::now()->subMonth()->startOfMonth(),
                Carbon::now()->subMonth()->endOfMonth()
            ])
            ->sum('total_amount');

        if ($previous_month == 0) return 0;
        return (($current_month - $previous_month) / $previous_month) * 100;
    }

    // === HELPER METHODS ===

    /**
     * Filter query by period
     */
    private function filterByPeriod($query, $period)
    {
        switch ($period) {
            case 'today':
                return $query->whereDate('created_at', Carbon::today());
            case 'week':
                return $query->whereBetween('created_at', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek()
                ]);
            case 'month':
                return $query->whereBetween('created_at', [
                    Carbon::now()->startOfMonth(),
                    Carbon::now()->endOfMonth()
                ]);
            case 'year':
                return $query->whereBetween('created_at', [
                    Carbon::now()->startOfYear(),
                    Carbon::now()->endOfYear()
                ]);
            default:
                return $query;
        }
    }

    /**
     * Get dashboard overview data
     */
    public function getDashboardOverview($period = 'month')
    {
        return [
            'total_revenue' => $this->getTotalRevenue($period),
            'total_orders' => $this->getTotalOrders($period),
            'pending_orders' => $this->getPendingOrders(),
            'total_customers' => $this->getTotalCustomers(),
            'average_order_value' => $this->getAverageOrderValue($period),
            'total_books_in_stock' => $this->getTotalBooksInStock(),
            'total_books_sold' => $this->getTotalBooksSold(),
            'new_customers_this_month' => $this->getNewCustomersThisMonth(),
        ];
    }

    /**
     * Get complete statistics for dashboard
     */
    public function getCompleteStatistics()
    {
        return [
            'overview' => $this->getDashboardOverview('month'),
            'revenue_trend' => $this->getRevenueByPeriod('daily', 30),
            'top_books' => $this->getTopSellingBooks(5),
            'sales_by_category' => $this->getSalesByCategory(),
            'order_status' => $this->getOrderStatusDistribution(),
            'average_rating' => $this->getAverageRating(),
            'rating_distribution' => $this->getRatingDistribution(),
            'most_reviewed' => $this->getMostReviewedBooks(5),
            'top_customers' => $this->getTopCustomers(5),
            'low_stock' => $this->getLowStockBooks(5),
            'out_of_stock_count' => $this->getOutOfStockBooks(),
        ];
    }
}
