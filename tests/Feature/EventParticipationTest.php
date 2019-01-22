<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Event;
use App\Team;
use App\User;

class EventParticipationTest extends TestCase
{
    use RefreshDatabase;
    /**
     * @test
     */
    public function user_can_participate_in_any_event_as_a_single_member_team_when_he_has_no_team()
    {
        $event = factory(Event::class)->create();
        $user = factory(User::class)->create();

        $this->withoutExceptionHandling()->be($user);
        
        $this->assertCount(0, $event->fresh()->teams);

        $this->post(route('events.participate', $event));

        $team = $user->fresh()->individualTeam;

        // assert new individualTeam is created
        $this->assertInstanceOf(Team::class, $team);
        $this->assertEquals(1, $team->id);

        //assert participation is saved
        tap($event->fresh()->teams, function($participatingTeams) use ($team) {
            $this->assertCount(1, $participatingTeams);
            $this->assertEquals($participatingTeams->first()->id, $team->id);
        });
    }
    /**
     * @test
     */
    public function user_can_participate_in_any_event_as_a_single_member_team_when_he_has_a_single_member_team()
    {
        $event = factory(Event::class)->create();
        $user = factory(User::class)->create();
        $user2 = factory(User::class)->create();

        $twoMembersTeam = $user->createTeam('Two Members Rock', $user2->id);
        $team = $user->createTeam($teamName = 'Team Name');

        $this->withoutExceptionHandling()->be($user);

        $this->assertCount(0, $event->fresh()->teams);

        $this->post(route('events.participate', $event));

        // assert participation is saved & individualTeam is selected implicitly
        tap($event->fresh()->teams, function ($participatingTeams) use ($team) {
            $this->assertCount(1, $participatingTeams);
            $this->assertEquals($participatingTeams->first()->id, $team->id);
        });
    }

    /**
     * @test
     *
     * @return void
     */
    public function user_can_participate_in_any_event_as_a_team_when_he_specifies_team_explicitly()
    {
        $event = factory(Event::class)->create();
        $user = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $user3 = factory(User::class)->create();

        $otherTeam = $user->createTeam('Team Name', $user3->id);
        $team = $user->createTeam('Two Members Rock', $user2->id);

        $this->withoutExceptionHandling()->be($user);

        $this->assertCount(0, $event->fresh()->teams);
        
        $this->post(route('events.participate', $event), [
            'team_id' => $team->id
        ]);

        // assert participation is saved & individualTeam is selected implicitly
        tap($event->fresh()->teams, function ($participatingTeams) use ($team) {
            $this->assertCount(1, $participatingTeams);
            $this->assertEquals($participatingTeams->first()->id, $team->id);
        });
    }
}