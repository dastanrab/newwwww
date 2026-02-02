<?php

namespace App\Http\Controllers\Api\Dashboard\Club;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\ClubCategory;
use App\Models\ScoreHistory;
use App\Models\User;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ClubItemController extends Controller
{
    use AuthorizesRequests;


    public function index(Request $request)
    {
        $club = Club::query();

        // Filter by category (relation)
        if ($request->filled('category')) {
            $categoryId = $request->category;

            $club->whereHas('categories', function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            });
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;

            $club->where(function ($query) use ($search) {
                $query->where('title', 'LIKE', "%$search%")
                    ->orWhere('sub_title', 'LIKE', "%$search%")
                    ->orWhere('score', $search);
            });
        }

        // Pagination
        $results = $club->latest()->paginate(20);
        return success_response($results);
    }

    public function store(Request $request)
    {
        // چک مجوز با Larvel Policy
        $this->authorize('club_create', Club::class);

        $maxImage = 1024 * 2; // 2MB
        $maxBrandIcon = 1024; // 1MB

        // اعتبارسنجی ورودی‌ها
        $validated = $request->validate([
            'title'           => 'required|min:2',
            'subTitle'        => 'required|min:2',
            'image'           => 'required|image|max:' . $maxImage,
            'brandIcon'       => 'required|image|max:' . $maxBrandIcon,
            'score'           => 'required|int',
            'status'          => 'required|in:active,inActive',
            'discount_type'   => 'required|in:1,2',
            'site'            => 'required|in:1,2',
            'discount_value'  => 'required|numeric',
            'content'         => 'nullable|string',
            'category'        => 'required|exists:club_categories,id',
            'user_id'         => 'required|exists:users,id',
        ], [
            'title.required'     => 'عنوان اجباری می باشد',
            'subTitle.required'  => 'زیرعنوان اجباری می باشد',
            'title.min'          => 'حداقل کاراکتر عنوان :min می باشد',
            'image.max'          => 'حداکثر سایز آپلود تصویر :max می باشد',
            'brandIcon.max'      => 'حداکثر سایز آپلود تصویر برند :max می باشد'
        ]);

        try {

            // -------------------------
            //  آپلود تصویر اصلی
            // -------------------------
            $imgFile = $request->file('image');
            $imagePath = now()->format('Y/m/d') . '/' . Str::random(10) . '-' . $imgFile->getClientOriginalName();
            $image = $imgFile->storeAs('', $imagePath, 'bazist');

            // -------------------------
            //  آپلود برند آیکون
            // -------------------------
            $brandFile = $request->file('brandIcon');
            $brandPath = now()->format('Y/m/d') . '/' . Str::random(10) . '-' . $brandFile->getClientOriginalName();
            $brandIcon = $brandFile->storeAs('', $brandPath, 'bazist');

            // -------------------------
            //  ثبت در دیتابیس
            // -------------------------
            $club = Club::create([
                'user_id'        => $validated['user_id'],
                'title'          => $validated['title'],
                'sub_title'      => $validated['subTitle'],
                'content'        => $validated['content'] ?? null,
                'image'          => 'uploads/' . $image,
                'brand_icon'     => 'uploads/' . $brandIcon,
                'score'          => $validated['score'],
                'status'         => $validated['status'],
                'discount_type'  => (int)$validated['discount_type'],
                'discount_value' => (int)$validated['discount_value'],
                'has_site'       => (int)$validated['site'],
            ]);

            // دسته‌بندی
            $club->categories()->sync([$validated['category']]);

            // پاسخ موفق
            return success_response($club, 'با موفقیت ایجاد شد', 201);

        } catch (Exception $e) {

            return error_response(
                'ثبت با اشکال روبرو شد',
                500,
                $e->getMessage()
            );
        }
    }
    public function update(Request $request, Club $club)
    {
        $maxImage = 1024 * 2;      // 2MB
        $maxBrandIcon = 1024;      // 1MB

        $validated = $request->validate([
            'title'        => 'required|min:2',
            'subTitle'     => 'required|min:2',
            'image'        => 'nullable|image|max:' . $maxImage,
            'brandIcon'    => 'nullable|image|max:' . $maxBrandIcon,
            'score'        => 'required|integer',
            'status'       => 'in:active,inActive',
            'category'     => 'required|exists:club_categories,id',
            'content'      => 'nullable|string',
            'userId'       => 'required|exists:users,id',
        ],[
            'title.required' => 'عنوان اجباری می باشد',
            'subTitle.required' => 'زیرعنوان اجباری می باشد',
            'title.min' => 'حداقل کاراکتر عنوان :min می باشد',
            'image.max' => 'حداکثر سایز آپلود تصویر :max می باشد',
            'brandIcon.max' => 'حداکثر سایز آپلود تصویر برند :max می باشد'
        ]);


        try {

            // Update simple fields
            $club->title      = $validated['title'];
            $club->sub_title  = $validated['subTitle'];
            $club->score      = $validated['score'];
            $club->status     = $validated['status'];
            $club->content    = $validated['content'] ?? null;
            $club->user_id    = $validated['userId'];

            // Upload main image
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $path = now()->format('Y/m/d') . '/' . str()->random(10) . '-' . $file->getClientOriginalName();
                $stored = $file->storeAs('', $path, 'bazist');
                $club->image = 'uploads/' . $stored;
            }

            // Upload brand icon
            if ($request->hasFile('brandIcon')) {
                $file = $request->file('brandIcon');
                $path = now()->format('Y/m/d') . '/' . str()->random(10) . '-' . $file->getClientOriginalName();
                $stored = $file->storeAs('', $path, 'bazist');
                $club->brand_icon = 'uploads/' . $stored;
            }

            // Save club
            $club->save();

            // Sync category
            $club->categories()->sync([$validated['category']]);

            return success_response($club, 'با موفقیت ایجاد شد', 201);


        } catch (\Throwable $e) {
            return error_response(
                'ثبت با اشکال روبرو شد',
                500,
                $e->getMessage()
            );
        }
    }


    public function scores()
    {
        $scores = ScoreHistory::query()->with('user')->orderBy('id', 'desc')->limit(50)->get();
        $users = User::query()->select(['id', 'name','lastname','score'])->orderBy('score','desc')->limit(50)->get();
         return success_response([$scores,$users]);
    }




}
