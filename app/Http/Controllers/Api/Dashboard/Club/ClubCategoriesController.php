<?php

namespace App\Http\Controllers\Api\Dashboard\Club;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\ClubCategory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ClubCategoriesController extends Controller
{
    use AuthorizesRequests;


    public function index()
  {
      $this->authorize('club_category_create', Club::class);
     return success_response(ClubCategory::all());
  }
    public function update(Request $request, ClubCategory $clubCategory)
    {
        // چک دسترسی
        $this->authorize('club_category_edit', Club::class);

        $max = 1024 * 2; // 2MB

        // اعتبارسنجی
        $validated = $request->validate([
            'title' => 'required|min:2|unique:club_categories,title,' . $clubCategory->id,
            'icon'  => $request->hasFile('icon') ? 'image|max:'.$max : 'nullable'
        ], [
            'title.required' => 'عنوان اجباری می باشد',
            'title.min'      => 'حداقل کاراکتر عنوان :min می باشد',
            'icon.max'       => 'حداکثر سایز آپلود :max می باشد'
        ]);

        try {

            // به‌روزرسانی عنوان
            $clubCategory->title = $validated['title'];

            // آپلود فایل درصورت وجود
            if ($request->hasFile('icon')) {
                $file = $request->file('icon');
                $path = now()->format('Y/m/d').'/'.strRandom('10').'-'.$file->getClientOriginalName();

                $upload = $file->storeAs('', $path, 'bazist');

                $clubCategory->icon = 'uploads/'.$upload;
            }

            // ذخیره
            $clubCategory->save();

            return success_response($clubCategory, 'با موفقیت ویرایش شد');

        } catch (\Throwable $e) {

            return error_response(
                'ویرایش با اشکال روبرو شد',
                500,
                []
            );
        }
    }

    public function store(Request $request)
    {
        // بررسی دسترسی
        $this->authorize('club_category_create', Club::class);

        $max = 1024 * 2; // 2MB Max

        // اعتبارسنجی
        $validated = $request->validate([
            'title' => 'required|unique:club_categories,title|min:2',
            'icon'  => 'required|image|max:' . $max,
        ], [
            'title.required' => 'عنوان اجباری می باشد',
            'title.min'      => 'حداقل کاراکتر عنوان :min می باشد',
            'icon.max'       => 'حداکثر سایز آپلود :max می باشد'
        ]);

        try {

            // پوشه + نام فایل
            $file = $request->file('icon');
            $path = now()->format('Y/m/d') . '/' . Str::random(10) . '-' . $file->getClientOriginalName();

            // ذخیره‌سازی
            $upload = $file->storeAs('', $path, 'bazist');

            // ایجاد رکورد
            $create = ClubCategory::create([
                'title' => $validated['title'],
                'icon'  => 'uploads/' . $upload,
            ]);

            // خروجی JSON استاندارد
            return success_response($create, 'با موفقیت ایجاد شد', 201);

        } catch (\Throwable $e) {

            return error_response(
                'ثبت با اشکال روبرو شد',
                500,
                $e->getMessage()
            );
        }
    }


}
