<?php

namespace App\Services;
use App\Models\EventRule;
use Config;
use DateTime;

class EventRuleService
{
    public function getEventList()
    {
        $eventList = Config::get('cardevent.events');
        foreach ($eventList as &$val) {
            $eventRules = EventRule::where('event_class', $val['class'])->get();
            $val['ruleList'] = $eventRules;
        }
        return $eventList;
    }

    public function getEventRuleList()
    {
        return EventRule::get();
    }

    public function getEventRulesByEventClass($class)
    {
        return EventRule::where('event_class',$class)->get();
    }

    public function getJobList()
    {
        $jobList = [];
        $tmp = Config::get('cardevent.jobs');
        foreach ($tmp as $val) {
            $jobList[$val['class']] = $val;
        }

        return $jobList;
    }

    public function getRule($id){
        return EventRule::where('_id', $id)->first();
    }

    public function addRule($rule)
    {
        $eventRule = new EventRule;
        $eventRule->title = $rule['title'];
        $eventRule->event_class = $rule['event_class'];
        $eventRule->jobs = $rule['jobs'];
        $eventRule->conditions = $rule['conditions'];
        $eventRule->save();
        return $eventRule->_id;
    }

    public function updateRule($rule)
    {
        $eventRule = EventRule::where('_id', $rule['_id'])->first();
        $eventRule->title = $rule['title'];
        $eventRule->event_class = $rule['event_class'];
        $eventRule->jobs = $rule['jobs'];
        $eventRule->conditions = $rule['conditions'];
        $eventRule->save();
    }

    public function delRule($id){
        EventRule::destroy($id);
    }
}
