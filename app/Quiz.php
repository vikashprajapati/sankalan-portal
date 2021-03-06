<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $guarded = [];

    public function event() {
        return $this->belongsTo(Event::class);
    }

    public function questions() {
        return $this->hasMany(Question::class);
    }

    public function teams() {
        return $this->belongsToMany(Team::class, 'quiz_participations');
    }

    public function participations() {
        return $this->hasMany(QuizParticipation::class, 'quiz_id');
    }

    public function setActive() {
        $this->event->update(['active_quiz_id' => $this->id]);
    }

    public function isActive() {
        return $this->id == $this->event->active_quiz_id;
    }

    public function isTeamAllowed(Team $team) {
        return $this->teams->pluck('id')->contains($team->id);
    }

    public function isTimeLimitExceeded($team) {
        $timeTaken = $this->participationByTeam($team)->started_at->diffInSeconds(now());
        return $timeTaken > ($this->timeLimit + 1*60); // Add 1 Minute Extra
    }

    public function allowTeam(Team $team) {
        $this->teams()->attach($team);
    }

    public function participationByTeam(Team $team) {
        return $this->participations()->where('team_id', $team->id)->first();
    }
    
    public function recordResponses(Team $team, $responses) {
        return $this->participationByTeam($team)
            ->responses()
            ->createMany($responses);
    }

    public function hasTeamResponded(Team $team) {
        return $this->participationByTeam($team)->finished_at != null;
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
