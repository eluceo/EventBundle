<?php

namespace Eluceo\EventBundle\Model;

use Eluceo\EventBundle\Model\Base\Event as BaseEvent;
use Doctrine\Common\Collections\ArrayCollection;

class Event extends BaseEvent
{
    public function __construct()
    {
        $this->active = false;
        $this->categories = new ArrayCollection;
        $this->image = null;
        $this->eventDates = new ArrayCollection;
    }

    public function __toString()
    {
        return $this->name ? : '';
    }

    public function addEventDate(EventDate $eventDate)
    {
        $this->eventDates[] = $eventDate;
        $eventDate->setEvent($this);
    }

    public function removeEventDate(EventDate $eventDate)
    {
        foreach ($this->eventDates as $key => $item) {
            if ($eventDate->equals($item)) {
                unset($this->eventDates[$key]);

                return true;
            }
        }

        return false;
    }

    public function addCategory(Category $category)
    {
        $this->categories[] = $category;
    }

    public function removeCategory(Category $category)
    {
        foreach ($this->categories as $key => $item) {
            if ($category->equals($item)) {
                unset ($this->categories[$key]);

                return true;
            }
        }

        return false;
    }

    public function equals(Event $other)
    {
        return $this->id == $other->getId();
    }
}
