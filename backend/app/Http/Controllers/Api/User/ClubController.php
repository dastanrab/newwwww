<?php
namespace App\Http\Controllers\Api\User;
use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\ClubCategory;
use App\Models\Offer;
use App\Models\ScoreHistory;
use Exception;
use Google\Api\ResourceDescriptor\History;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ClubController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $club = Club::where('status', 'active')->get();
        $categories = ClubCategory::all();
        $data = [
            'userScore' => $user->score,
            'categories' => [],
            'items' => [],
        ];
        foreach ($categories as $category) {
            $data['categories'][] = [
                'id'    => $category->id,
                'title' => $category->title,
                'icon'  => asset($category->icon),
            ];
        }
        foreach ($club as $item){
            $data['items'][] = [
                'id'         => $item->id,
                'title'      => $item->title,
                'subtitle'   => $item->sub_title,
                'content'    => $item->content,
                'image'      => asset($item->image),
                'brandIcon'  => asset($item->brand_icon),
                'score'      => $item->score,
                'categories' => $item->categories->pluck('id')
            ];
        }
        return sendJson('success','',$data);
    }

    public function scores(Request $request)
    {
        $user = auth()->user();
        $scores = $user->scores();
        if($request->type){
            $scores = $scores->where('type',$request->type);
        }
        $scores = $scores->paginate(20);
        $data = [
            'limit' => 20,
            'list' => []
        ];
        foreach ($scores as $score){
            $data['list'][] = [
                'id' => $score->id,
                'details' => $score->detail,
                'value' => $score->score,
                'date' => [
                    'day' => verta()->parse($score->created_at)->format('Y/m/d'),
                    'time' => verta()->parse($score->created_at)->format('H:i'),
                ],
                'type' => $score->type
            ];
        }
        return sendJson('success', '', $data);
    }

    public function purchase(Club $club)
    {
        $user = auth()->user();
        if ($club->status != 'active') {
            return sendJson('error', 'آیتم  در حال حاضر فعال نیست');
        }
        if($club->score > $user->score){
            return sendJson('error', 'امتیاز شما برای این آیتم کافی نمی باشد');
        }
        else{
            try {
                $code = DB::transaction(function () use ($user,$club){
                    $value = $user->score-$club->score;
                    $user->score = $value;
                    $user->save();
                    $user->scores()->create([
                        'detail' => 'کاهش امتیاز برای آیتم '.$club->title,
                        'score'  => $club->score,
                        'type'   => 'spent'
                    ]);
                    $code = Str::random(13);
                    $user->offers()->create([
                        'club_id' => $club->id,
                        'title' => $club->title,
                        'score' => $club->score,
                        'code' => $code
                    ]);
                    return $code;
                });
                return sendJson('success', '', ['code' => $code, 'message' => 'کد به شما تعلق گرفت']);
            }
            catch (Exception $e){
                return sendJson('error', 'خطایی پیش آمد لطفا بعدا تلاش کنید');
            }
        }
    }

    public function offers(Request $request)
    {
        $user = auth()->user();
        $offers = $user->offers()->paginate(10);
        $data = [
            'list' => [],
            'limit' => 20
        ];
        foreach ($offers as $offer){
            $data['list'][] = [
                'id' => $offer->id,
                'title' => $offer->title,
                'brandIcon' => asset($offer->club->brand_icon),
                'score' => $offer->score,
                'code' => $offer->code,
                'date' => [
                    'day' => verta()->parse($offer->created_at)->format('Y/m/d'),
                    'time' => verta()->parse($offer->created_at)->format('H:i'),
                ]
            ];
        }
        return sendJson('success', '', $data);
    }

}
