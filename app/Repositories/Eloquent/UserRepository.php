<?php 
namespace App\Repositories\Eloquent;

use App\Models\User;
use Illuminate\Http\Request;
use App\Repositories\Contracts\IUser;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use App\Repositories\Eloquent\BaseRepository;

class UserRepository extends BaseRepository implements IUser 
{
    
    public function model()
    {
        return User::class;
    }

    public function findByEmail($email)
    {
        return $this->model->where('email',$email)->first();
    }

    public function search(Request $request)
    {
        $query = (new $this->model)->newQuery();

        if($request->has_designs){
            $query->has('designs');
        }

        if($request->available_to_hire){
            $query->where('available_to_hire',true);
        }

        $lat = $request->latitude;
        $lng = $request->longitude;
        $dist = $request->distance;
        $unit = $request->unit;

        if($lat && $lng){
            $point = new Point($lat,$lng);
            $unit == 'km' ? $dist *= 1000 : $dist *= 1609.34;
            $query->distanceSphereExcludingSelf('location',$point,$dist);
        }

        if($request->orderBy == "closest")
        {
            $query->orderByDistanceSphere('location',$point,'asc');
        }
        else if($request->orderByLatest == "latest"){
            $query->latest();
        }else{
            $query->oldest();
        }

        return $query->get();
    }
}