<?php

namespace App\Orchid\Layouts\Comment;

use App\Models\Comment;
use App\Models\GroupProgramme;
use App\Models\Programme;
use App\Orchid\Actions\ApprovedAction;
use Orchid\Icons\IconComponent;
use Orchid\Icons\IconFinder;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use Spatie\Tags\Tag;

class CommentListLayout extends Table
{
    /**
     * Data source.
     *
     * @var string
     */
    public $target = 'comments';

    /**
     * @return TD[]
     */
    public function columns(): array
    {
        return [
            TD::make('comments.emission', 'Emission')
                ->render(function (Comment $comment) {
                    return $comment->commentable->name;
                }),
            TD::make('comments.programme', 'Programme')
                ->render(function (Comment $comment) {
                    return $comment->commentable->programme->name;
                }),
            TD::make('comments', 'Commentaire')
                ->width('auto')
                ->render(function (Comment $comment) {
                    return $comment->comment;
                }),
            TD::make('approved', 'Approuvé')
                ->filterOptions([
                    'Oui' => 1,
                    'Non' => 2
                ])
                ->filterValue(function($value) {
                    return $value === 1 ? 'Oui' : 'Non';
                })
                ->filter(Select::make('select')
                    ->options([
                        1   => 'Oui',
                        0 => 'Non'
                    ]))
                ->render(function (Comment $comment) {
                    return Link::make('')
                        ->icon($comment->approved ? 'check' : 'close')
                        ->route('platform.comment.approved', $comment);
                }),
            TD::make('delete', 'Supprimé')
                ->render(function (Comment $comment) {
                    return Link::make('')
                        ->icon('trash')
                        ->route('platform.comment.delete', $comment);
            }),

        ];
    }
}
