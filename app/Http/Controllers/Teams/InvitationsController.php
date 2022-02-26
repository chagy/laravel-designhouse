<?php

namespace App\Http\Controllers\Teams;

use Mail;
use App\Models\Team;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\ITeam;
use App\Repositories\Contracts\IUser;
use App\Mail\SendInvitationToJoinTeam;
use App\Repositories\Contracts\IInvitation;

class InvitationsController extends Controller
{
    protected $invitations;
    protected $teams;
    protected $users;

    public function __construct(IInvitation $invitations,ITeam $teams,IUser $users)
    {
        $this->invitations = $invitations;
        $this->teams = $teams;
        $this->users = $users;
    }

    public function invite(Request $request,$teamId)
    {
        $team = $this->teams->find($teamId);

        $this->validate($request,[
            'email' => ['required','email']
        ]);
        $user = auth()->user();

        if(!$user->isOwnerOfTeam($team)){
            return response()->json(['emai' => 'You are not the team owner'],401);
        }

        if($team->hasPendingInvite($request->email)){
            return response()->json(['emai' => 'Email already has a pending invite'],422);
        }

        $recipient = $this->users->findByEmail($request->email);

        if(!$recipient){
            $this->createInvitation(false,$team,$request->email);

            return response()->json([
                'message' => 'Invitation sent to user'
            ],200);
        }

        if($team->hasUser($recipient)){
            return response()->json([
                'email' => 'This user seems to be a team member'
            ],422);
        }

        $this->createInvitation(true,$team,$request->email);
        return response()->json([
            'message' => 'Invitation sent to user'
        ],200);
    }

    public function resend($id)
    {

    }

    public function respond(Request $request,$id)
    {

    }

    public function destroy($id)
    {

    }

    protected function createInvitation(bool $user_exists,Team $team,string $email)
    {
        $invitation = $this->invitations->create([
            'team_id' => $team->id,
            'sender_id' => auth()->id(),
            'recipient_email' => $email,
            'token' => md5(uniqid(microtime()))
        ]);
        Mail::to($email)
                ->send(new SendInvitationToJoinTeam($invitation,$user_exists));
    }
}
