<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Pack;
use App\Models\Category;
use DB;

class SearchController extends Controller
{
    /**
     * Búsqueda general de productos y packs
     * GET /api/search
     *
     * Query parameters:
     * - q: string (required, min 2 chars)
     * - category_id: integer (optional)
     * - price_min: float (optional)
     * - price_max: float (optional)
     * - only_packs: boolean (optional)
     * - limit: integer (optional, default 20)
     *
     * Returns:
     * {
     *   "products": [...],
     *   "packs": [...],
     *   "suggestions": [...],
     *   "categories": [...],
     *   "total": 42
     * }
     */
    public function search(Request $request)
    {
        $query = $request->input('q', '');
        $limit = (int) $request->input('limit', 20);
        $categoryId = $request->input('category_id');
        $priceMin = $request->input('price_min');
        $priceMax = $request->input('price_max');
        $onlyPacks = filter_var($request->input('only_packs', false), FILTER_VALIDATE_BOOLEAN);

        // Validación mínima
        if (strlen(trim($query)) < 2) {
            return response()->json([
                'products' => [],
                'packs' => [],
                'suggestions' => [],
                'categories' => [],
                'total' => 0,
            ]);
        }

        $searchTerm = '%' . trim($query) . '%';

        // 1. Búsqueda de productos
        $productsQuery = Product::with(['category', 'images', 'features.type'])
            ->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', $searchTerm)
                  ->orWhere('description', 'LIKE', $searchTerm)
                  ->orWhere('code', 'LIKE', $searchTerm);
            });

        // Filtros adicionales para productos
        if ($categoryId) {
            $productsQuery->where('category_id', $categoryId);
        }
        if ($priceMin !== null && $priceMin !== '') {
            $productsQuery->where('price', '>=', (float) $priceMin);
        }
        if ($priceMax !== null && $priceMax !== '') {
            $productsQuery->where('price', '<=', (float) $priceMax);
        }

        $products = $onlyPacks ? collect([]) : $productsQuery->limit($limit)->get();

        // 2. Búsqueda de packs
        $packsQuery = Pack::with([
            'products' => function($q) {
                $q->select('products.id', 'products.name', 'products.price')
                  ->with(['images' => function($q2) {
                      $q2->where('is_important', 1)->limit(1);
                  }]);
            },
            'images' => function($q) {
                $q->where('is_important', 1)->limit(1);
            }
        ])
        ->where(function($q) use ($searchTerm) {
            $q->where('name', 'LIKE', $searchTerm)
              ->orWhere('description', 'LIKE', $searchTerm)
              ->orWhereHas('products', function($q2) use ($searchTerm) {
                  $q2->where('name', 'LIKE', $searchTerm);
              });
        });

        // Filtros para packs
        if ($categoryId) {
            $packsQuery->whereHas('products', function($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }
        if ($priceMin !== null && $priceMin !== '') {
            $packsQuery->where('total_price', '>=', (float) $priceMin);
        }
        if ($priceMax !== null && $priceMax !== '') {
            $packsQuery->where('total_price', '<=', (float) $priceMax);
        }

        $packs = $packsQuery->limit($limit)->get();

        // 3. Categorías relacionadas (para sugerencias/filtros)
        $categories = Category::withCount('products')
            ->whereHas('products', function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', $searchTerm)
                  ->orWhere('description', 'LIKE', $searchTerm);
            })
            ->limit(5)
            ->get(['id', 'name']);

        // 4. Sugerencias de búsqueda alternativas
        $suggestions = $this->getSuggestions($query, $products, $packs);

        // 5. Calcular total (productos + packs que coinciden)
        $total = $products->count() + $packs->count();

        return response()->json([
            'products' => $products,
            'packs' => $packs,
            'suggestions' => $suggestions,
            'categories' => $categories,
            'total' => $total,
        ]);
    }

    /**
     * Búsqueda rápida para autocomplete
     * GET /api/search/quick
     *
     * Returns only minimal data for dropdown
     */
    public function quickSearch(Request $request)
    {
        $query = $request->input('q', '');
        $limit = (int) $request->input('limit', 5);

        if (strlen(trim($query)) < 2) {
            return response()->json([
                'products' => [],
                'packs' => [],
            ]);
        }

        $searchTerm = '%' . trim($query) . '%';

        // Productos mínimos con imagen importante
        $products = Product::select(['id', 'name', 'price', 'category_id', 'discount', 'stock'])
            ->with([
                'category:id,name',
                'images' => function($q) {
                    $q->where('is_important', 1)->limit(1);
                }
            ])
            ->where('name', 'LIKE', $searchTerm)
            ->orWhere('code', 'LIKE', $searchTerm) // 'code' en lugar de 'sku'
            ->limit($limit)
            ->get();

        // Packs mínimos con imagen importante y count de productos
        $packs = Pack::select(['id', 'name', 'total_price as price'])
            ->with([
                'images' => function($q) {
                    $q->where('is_important', 1)->limit(1);
                }
            ])
            ->withCount('products')
            ->where('name', 'LIKE', $searchTerm)
            ->limit(3)
            ->get();

        return response()->json([
            'products' => $products,
            'packs' => $packs,
        ]);
    }

    /**
     * Generar sugerencias basadas en el término de búsqueda
     */
    private function getSuggestions($query, $products, $packs)
    {
        $suggestions = [];

        // Extraer palabras únicas de resultados
        $words = [];
        foreach ($products as $product) {
            $words = array_merge($words, explode(' ', $product->name));
        }
        foreach ($packs as $pack) {
            $words = array_merge($words, explode(' ', $pack->name));
        }

        // Palabras únicas, ordenadas por frecuencia
        $wordCounts = array_count_values(array_map('strtolower', $words));
        arsort($wordCounts);

        // Tomar las 3 palabras más frecuentes que no sean la query original
        $originalLower = strtolower(trim($query));
        foreach ($wordCounts as $word => $count) {
            if (strlen($word) > 3 && $word !== $originalLower && !in_array($word, $suggestions)) {
                $suggestions[] = $word;
            }
            if (count($suggestions) >= 3) {
                break;
            }
        }

        return $suggestions;
    }
}
