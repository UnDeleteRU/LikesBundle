<?php

namespace Undelete\LikesBundle\Dispatcher;

class LikeEventDispatcher
{
    private $listeners = [];

    public function addListener($service, $onLike, $onLikeRemove)
    {
        $this->listeners[] = [
            'service' => $service,
            'onLike' => $onLike,
            'onLikeRemove' => $onLikeRemove,
        ];
    }

    public function dispatchEvent($kind, LikeEvent $event)
    {
        foreach ($this->listeners as $listener) {
            $method = false;

            if ($kind == LikeEvent::ON_LIKE) {
                $method = $listener['onLike'];
            } elseif ($kind == LikeEvent::ON_LIKE_REMOVE) {
                $method = $listener['onLikeRemove'];
            }

            if ($method) {
                $listener['service']->$method($event);
            }
        }
    }
}
