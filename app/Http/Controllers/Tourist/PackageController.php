<?php

namespace App\Http\Controllers\Tourist;

use App\Http\Controllers\Controller;
use App\Models\TourPackage;
use App\Models\Destination;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index(Request $request)
    {
        $categoryMap = [
            'natural' => ['label' => 'Natural Attractions', 'keywords' => ['beach','waterfall','falls','cave','river','rock','island','lagoon','spring','shore']],
            'cultural' => ['label' => 'Cultural & Historical Sites', 'keywords' => ['church','heritage','museum','lighthouse','parish','histor','monument','temple']],
            'recreational' => ['label' => 'Recreational & Adventure Spots', 'keywords' => ['island','diving','snorkel','camp','hike','hiking','island hopping','adventure','tour']],
            'accommodation' => ['label' => 'Accommodation & Hospitality', 'keywords' => ['resort','hotel','inn','homestay','transient','guesthouse','lodg','villa']],
            'events' => ['label' => 'Events & Festivals', 'keywords' => ['festival','event','parade','competition','celebration']],
            'ecotourism' => ['label' => 'Ecotourism & Conservation Areas', 'keywords' => ['mangrove','park','reserve','ecolodge','protected','sanctuary']],
        ];

        $packages = TourPackage::active()
            ->when($request->search, fn($q) =>
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('location', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%")
            )
            ->when($request->category, function($q) use ($request, $categoryMap) {
                $q->where(function($sub) use ($request, $categoryMap) {
                    $sub->where('category', $request->category);
                    if ($request->category && array_key_exists($request->category, $categoryMap)) {
                        $keywords = $categoryMap[$request->category]['keywords'];
                        $sub->orWhere(function($s2) use ($keywords) {
                            foreach ($keywords as $k) {
                                $s2->orWhere('name', 'like', "%{$k}%")
                                   ->orWhere('description', 'like', "%{$k}%")
                                   ->orWhere('location', 'like', "%{$k}%");
                            }
                        })->orWhereHas('destination', function($d) use ($keywords) {
                            foreach ($keywords as $k) {
                                $d->orWhere('name', 'like', "%{$k}%");
                            }
                        });
                    }
                });
            })
            ->when($request->type, fn($q) => $q->where('type', $request->type))
            ->when($request->max_price, fn($q) => $q->where('price', '<=', $request->max_price))
            ->orderBy('rating', 'desc')
            ->paginate(9)
            ->withQueryString();

        return view('tourist.packages.index', compact('packages', 'categoryMap'));
    }

    public function show(TourPackage $tourPackage)
    {
        abort_if($tourPackage->status === 'inactive', 404);

        $tourPackage->load(['reviews.user', 'destination']);

        return view('tourist.packages.show', compact('tourPackage'));
    }
}
