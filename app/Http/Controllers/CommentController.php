<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class CommentController extends Controller
{
    public function inverseApproved($comment)
    {
        $comment->approved = !$comment->approved;
        $comment->update();
        return Redirect::back();
    }
    public function delete($comment)
    {
        $comment->delete();
        return Redirect::back();
    }
}
