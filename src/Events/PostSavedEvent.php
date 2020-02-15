<?php

namespace LaPress\Database\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use LaPress\Database\Models\AbstractPost;

/**
 * @author    Sebastian Szczepański
 * @copyright ably
 */
class PostSavedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    
    /**
     * @var AbstractPost
     */
    public $post;

    /**
     * @param AbstractPost $post
     */
    public function __construct(AbstractPost $post)
    {
        $this->post = $post;
    }
}
