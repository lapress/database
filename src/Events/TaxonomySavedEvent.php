<?php

namespace LaPress\Database\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use LaPress\Database\Models\Taxonomy;

/**
 * @author    Sebastian Szczepański
 * @copyright ably
 */
class TaxonomySavedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    /**
     * @var Taxonomy
     */
    private $taxonomy;

    /**
     * @param Taxonomy $taxonomy
     */
    public function __construct(Taxonomy $taxonomy)
    {
        $this->taxonomy = $taxonomy;
    }
}
