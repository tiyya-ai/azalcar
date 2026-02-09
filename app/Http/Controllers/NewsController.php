<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::where('status', 'published')
            ->where(function($query) {
                $query->where('published_at', '<=', now())
                      ->orWhereNull('published_at');
            })
            ->latest('published_at')
            ->paginate(12);
        return view('news.index', compact('news'));
    }

    public function show(News $news)
    {
        // Get related news from same category first
        $relatedNewsSameCategory = News::where('category', $news->category)
            ->where('id', '!=', $news->id)
            ->latest()
            ->take(3)
            ->get();
        
        // If not enough, fill with news from other categories
        if ($relatedNewsSameCategory->count() < 3) {
            $relatedNewsOtherCategories = News::where('id', '!=', $news->id)
                ->whereNotIn('id', $relatedNewsSameCategory->pluck('id')->toArray())
                ->latest()
                ->take(3 - $relatedNewsSameCategory->count())
                ->get();
            
            $relatedNews = $relatedNewsSameCategory->concat($relatedNewsOtherCategories);
        } else {
            $relatedNews = $relatedNewsSameCategory;
        }
            
        return view('news.show', compact('news', 'relatedNews'));
    }
}
