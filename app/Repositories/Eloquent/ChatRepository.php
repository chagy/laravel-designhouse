<?php 
namespace App\Repositories\Eloquent;

use App\Models\Chat;
use App\Repositories\Contracts\IChat;
use App\Repositories\Eloquent\BaseRepository;

class ChatRepository extends BaseRepository implements IChat 
{
    public function model()
    {
        return Chat::class;
    }

}