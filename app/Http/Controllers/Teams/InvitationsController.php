<?php

namespace App\Http\Controllers\Teams;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InvitationsController extends Controller
{
    protected $invitations;

    public function __construct(IInvitation $invitations)
    {
        $this->invitations = $invitations;
    }
}
